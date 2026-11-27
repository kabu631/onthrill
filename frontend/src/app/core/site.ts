import { HttpClient } from '@angular/common/http';
import { computed, inject, Injectable, signal } from '@angular/core';
import { firstValueFrom } from 'rxjs';
import { Bootstrap, Currency, PackageCard, SiteSettings, User } from './types';

declare global {
  interface Window {
    __ONTHRILL__?: Bootstrap;
  }
}

/** Digit grouping used in India and Nepal: 125000 becomes 1,25,000. */
export function lakh(amount: number | null | undefined): string {
  const value = Math.round((amount ?? 0) * 100) / 100;
  const [whole, fraction] = Math.abs(value).toFixed(2).split('.');
  let grouped = whole;
  if (whole.length > 3) {
    grouped = whole.slice(0, -3).replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + whole.slice(-3);
  }
  return (value < 0 ? '-' : '') + grouped + (fraction === '00' ? '' : '.' + fraction);
}

/** ₹1,25,000 or NPR 2,00,000 ("NPR" so it is never mistaken for Indian rupees). */
export function money(amount: number | null | undefined, currency: Currency): string {
  return currency === 'NPR' ? 'NPR ' + lakh(amount) : '₹' + lakh(amount);
}

/**
 * Site-wide state: settings and wording from the admin panel, the menu, the
 * visitor's currency and account. Comes with the page (window.__ONTHRILL__),
 * or from /api/site when the app is served on its own (ng serve).
 */
@Injectable({ providedIn: 'root' })
export class Site {
  private readonly http = inject(HttpClient);

  private readonly boot = signal<Bootstrap | null>(null);

  readonly settings = computed(() => this.boot()!.site);
  readonly menu = computed(() => this.boot()!.menu);
  readonly user = signal<User | null>(null);
  readonly canBook = signal(true);
  readonly currency = signal<Currency>('INR');

  readonly phone = computed(() => this.settings().phone);
  readonly whatsappNumber = computed(() => this.settings().whatsapp || this.settings().phone);
  readonly social = computed(() => {
    const s = this.settings();
    return (['facebook', 'instagram', 'youtube', 'twitter'] as const)
      .filter((network) => !!s[network])
      .map((network) => ({ network, url: s[network] }));
  });

  /** Page data sent with the first page; used once, for the request it answers. */
  private firstPage: { api: string; data: unknown } | null = null;

  async load(): Promise<void> {
    const boot = window.__ONTHRILL__ ?? (await firstValueFrom(this.http.get<Bootstrap>('/api/site')));
    this.firstPage = boot.page ?? null;
    this.boot.set(boot);
    this.user.set(boot.user);
    this.canBook.set(boot.can_book);
    this.currency.set(boot.currency);
  }

  /** The data embedded for `url`, if the page was sent with it (once only). */
  takeFirstPage(url: string): unknown | undefined {
    const page = this.firstPage;
    if (page && sameUrl(page.api, url)) {
      this.firstPage = null;
      return page.data;
    }
    return undefined;
  }

  /** Once the visitor moves on, the embedded data is out of date. */
  dropFirstPage(): void {
    this.firstPage = null;
  }

  setting<K extends keyof SiteSettings>(key: K): SiteSettings[K] {
    return this.settings()[key];
  }

  /** Wording from Admin > Page Content, with :placeholders filled in. */
  t(key: string, replace: Record<string, string | number | null | undefined> = {}): string {
    let text = this.boot()?.texts[key] ?? '';
    // Longest names first, so ":countries" is never mistaken for ":count".
    for (const name of Object.keys(replace).sort((a, b) => b.length - a.length)) {
      text = text.split(':' + name).join(String(replace[name] ?? ''));
    }
    return text;
  }

  /** After signing in, registering or out: who is visiting, and whether they may book. */
  signedIn(user: User | null, canBook: boolean): void {
    this.user.set(user);
    this.canBook.set(canBook);
  }

  logout(): Promise<void> {
    return firstValueFrom(this.http.post<{ user: null; can_book: boolean }>('/api/logout', {})).then((res) =>
      this.signedIn(null, res.can_book),
    );
  }

  setCurrency(code: Currency): void {
    this.currency.set(code);
    document.cookie = `currency=${code}; path=/; max-age=${60 * 60 * 24 * 365}; samesite=lax`;
  }

  currencyName(code: Currency = this.currency()): string {
    return this.t(code === 'NPR' ? 'currency_npr_name' : 'currency_inr_name');
  }

  money(amount: number | null | undefined, currency: Currency = this.currency()): string {
    return money(amount, currency);
  }

  /** "From ₹12,000" or "Price on request", in the visitor's currency. */
  priceLine(p: Pick<PackageCard, 'prices'>): string {
    const price = p.prices?.[this.currency()];
    return price ? this.t('price_from') + ' ' + this.money(price.now) : this.t('price_on_request');
  }

  digits(phone: string | null | undefined): string {
    return (phone ?? '').replace(/\D+/g, '');
  }

  whatsappUrl(message: string = this.t('whatsapp_greeting')): string {
    const url = 'https://wa.me/' + this.digits(this.whatsappNumber());
    return message ? url + '?text=' + encodeURIComponent(message) : url;
  }

  plural(count: number, one: string, many: string): string {
    return count === 1 ? one : many;
  }
}

function sameUrl(a: string, b: string): boolean {
  const normal = (url: string) => {
    const [path, query = ''] = url.split('?');
    const params = new URLSearchParams(query);
    params.sort();
    const q = params.toString();
    return path + (q ? '?' + q : '');
  };
  return normal(a) === normal(b);
}
