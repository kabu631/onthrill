import { ChangeDetectionStrategy, Component, computed, DestroyRef, effect, ElementRef, inject, input, output, signal, viewChild } from '@angular/core';
import { NavigationEnd, Router, RouterLink, RouterLinkActive } from '@angular/router';
import { debounceTime, distinctUntilChanged, filter, Subject, switchMap } from 'rxjs';
import { Site } from '../core/site';
import { Currency } from '../core/types';
import { Icon } from '../shared/icon';
import { Highlight, SearchSuggestions, SuggestionGroup } from '../shared/search';

/** The logo uploaded in Site Settings, or the site name as a wordmark. */
@Component({
  selector: 'app-brand',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'block' },
  template: `
    @if (logo()) {
      <img [src]="logo()" [alt]="site.setting('site_name')" class="h-10 w-auto lg:h-12">
    } @else {
      <span class="flex items-center gap-2.5">
        <svg class="size-10 shrink-0 lg:size-11" viewBox="0 0 64 64" aria-hidden="true">
          <rect width="64" height="64" rx="14" [attr.fill]="light() ? '#ff8335' : '#13293a'" />
          <path d="M14 46 28 22l7 11 5-7 10 20z" [attr.fill]="light() ? '#13293a' : '#ff8335'" />
          <circle cx="44" cy="18" r="4" [attr.fill]="light() ? '#fff6ef' : '#ffd2b5'" />
        </svg>
        <span class="leading-none">
          <span class="block font-display text-2xl font-bold tracking-tight lg:text-[1.7rem]" [class]="light() ? 'text-white' : 'text-navy-900'">{{ site.setting('site_name') }}</span>
          @if (site.setting('tagline')) {
            <span class="mt-1 block text-[11px] font-medium tracking-wide" [class]="light() ? 'text-saffron-300' : 'text-saffron-700'">{{ site.setting('tagline') }}</span>
          }
        </span>
      </span>
    }
  `,
})
export class Brand {
  protected readonly site = inject(Site);
  readonly light = input(false);
  protected readonly logo = computed(() => this.site.setting(this.light() ? 'logo_footer' : 'logo'));
}

/** INR / NPR switch. */
@Component({
  selector: 'app-currency-switch',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'flex items-center gap-2', role: 'group', '[attr.aria-label]': 'site.t("top_prices_in")' },
  template: `
    <span [class]="dark() ? 'text-white/60' : 'text-ink/60'">{{ site.t('top_prices_in') }}</span>
    <span class="flex rounded-full p-0.5" [class]="dark() ? 'bg-white/10' : 'bg-mist'">
      @for (c of codes; track c.code) {
        <button type="button" class="rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors"
                [class]="site.currency() === c.code ? (dark() ? 'bg-white text-navy-900' : 'bg-navy-900 text-white') : (dark() ? 'text-white/75 hover:text-white' : 'text-navy-800')"
                [attr.aria-pressed]="site.currency() === c.code" (click)="site.setCurrency(c.code)">{{ c.label }}</button>
      }
    </span>
  `,
})
export class CurrencySwitch {
  protected readonly site = inject(Site);
  readonly dark = input(true);
  protected readonly codes: { code: Currency; label: string }[] = [
    { code: 'INR', label: '₹ INR' },
    { code: 'NPR', label: 'NPR' },
  ];
}

/**
 * Search pop-up (the search button in the menu, or the "/" key). Popular
 * places before typing; then destinations, tour types and tours as the visitor
 * types, with the matching words in bold. Arrow keys and Enter choose; Enter
 * on its own opens the full results.
 */
@Component({
  selector: 'app-search-panel',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon, Highlight],
  host: { '(document:keydown.escape)': 'close.emit()' },
  template: `
    <div class="animate-fade fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Search">
      <div class="absolute inset-0 bg-navy-950/75" (click)="close.emit()"></div>
      <div class="relative mx-auto w-full max-w-2xl px-3 pt-3 sm:px-4 sm:pt-24">
        <div class="overflow-hidden rounded-xl bg-white shadow-lift">
          <form role="search" class="flex items-center gap-3 border-b border-navy-100 px-4 sm:px-5" (submit)="submit($event)">
            <svg icon="search" class="size-5 text-ink/40"></svg>
            <label for="search-panel-input" class="sr-only">{{ site.t('search_title') }}</label>
            <input #box id="search-panel-input" type="search" autocomplete="off" maxlength="100" [value]="term()" (input)="type(box.value)" (keydown)="key($event)"
                   role="combobox" aria-controls="search-panel-list" [attr.aria-activedescendant]="active() >= 0 ? 'search-opt' + active() : null"
                   [placeholder]="site.t('search_placeholder')"
                   class="h-14 min-w-0 flex-1 border-0 bg-transparent text-base text-ink placeholder:text-ink/40 focus:outline-none sm:h-16 sm:text-lg">
            <button type="button" (click)="close.emit()" class="rounded px-2 py-1 text-xs font-medium text-ink/55 hover:bg-navy-50">Esc</button>
          </form>

          <div id="search-panel-list" role="listbox" class="max-h-[65vh] overflow-y-auto py-2">
            @if (loading() && !groups().length) {
              <p class="px-5 py-6 text-sm text-ink/60">{{ site.t('search_searching') }}</p>
            }
            @for (group of groups(); track group.key) {
              <p class="px-5 pt-3 pb-1 text-[11px] font-semibold tracking-wider text-navy-400 uppercase">{{ group.title }}</p>
              @if (group.key === 'type' && !typed()) {
                <div class="flex flex-wrap gap-2 px-5 pt-1 pb-2">
                  @for (item of group.items; track item.url) {
                    <a [routerLink]="item.url" (click)="close.emit()" class="rounded-full border border-navy-200 px-3 py-1.5 text-sm text-ink/80 hover:border-saffron-500 hover:text-saffron-700">{{ item.name }}</a>
                  }
                </div>
              } @else {
                @for (item of group.items; track item.url) {
                  @let i = flat().indexOf(item);
                  <a [routerLink]="item.url" (click)="close.emit()" [id]="'search-opt' + i" role="option" [attr.aria-selected]="active() === i" (mouseenter)="active.set(i)"
                     class="flex items-center gap-4 px-5 py-2.5" [class]="active() === i ? 'bg-saffron-50' : ''">
                    @if (item.image) {
                      <img [src]="item.image" alt="" class="size-11 shrink-0 rounded-md object-cover">
                    }
                    <span class="min-w-0 flex-1">
                      <span class="block truncate text-ink" [innerHTML]="item.name | highlight: term()"></span>
                      @if (item.meta) { <span class="block truncate text-xs text-ink/50">{{ item.meta }}</span> }
                    </span>
                    @if (item.kind === 'package') {
                      <span class="shrink-0 text-sm font-semibold text-saffron-700">{{ item.prices ? site.money(item.prices[site.currency()].now) : site.t('price_on_request') }}</span>
                    }
                  </a>
                }
              }
            }

            @if (typed() && searched() && !loading() && !groups().length) {
              <div class="px-5 py-6 text-sm">
                <p class="font-medium text-ink">{{ site.t('search_none_title', { term: term().trim() }) }}</p>
                <p class="mt-1 text-ink/70">{{ site.t('search_none_text') }} <a routerLink="/contact-us" (click)="close.emit()" class="font-medium text-saffron-700 underline underline-offset-2">{{ site.t('list_empty_button') }}</a></p>
              </div>
            }
          </div>

          @if (typed() && groups().length) {
            <div class="flex items-center justify-between gap-4 border-t border-navy-100 bg-mist px-5 py-3 text-sm">
              <a [routerLink]="'/search'" [queryParams]="{ q: term().trim() }" (click)="close.emit()" class="font-semibold text-saffron-700 hover:text-saffron-800">
                {{ site.t('search_all_results', { term: term().trim() }) }}
              </a>
              <span class="hidden text-xs text-ink/45 sm:inline">{{ site.t('search_keys') }}</span>
            </div>
          }
        </div>
      </div>
    </div>
  `,
})
export class SearchPanel {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);
  private readonly suggestions = inject(SearchSuggestions);
  readonly close = output();

  private readonly box = viewChild<ElementRef<HTMLInputElement>>('box');
  protected readonly term = signal('');
  protected readonly typed = computed(() => this.term().trim().length >= 2);
  private readonly found = signal<SuggestionGroup[]>([]);
  protected readonly groups = computed(() => (this.typed() ? this.found() : this.suggestions.popular()));
  protected readonly flat = computed(() => this.groups().flatMap((g) => (g.key === 'type' && !this.typed() ? [] : g.items)));
  protected readonly active = signal(-1);
  protected readonly loading = signal(false);
  protected readonly searched = signal(false);
  private readonly terms = new Subject<string>();

  constructor() {
    effect(() => this.box()?.nativeElement.focus());
    const sub = this.terms
      .pipe(
        debounceTime(200),
        distinctUntilChanged(),
        filter((t) => t.length >= 2),
        switchMap((t) => this.suggestions.find(t)),
      )
      .subscribe({
        next: (groups) => {
          this.found.set(groups);
          this.loading.set(false);
          this.searched.set(true);
          this.active.set(-1);
        },
        error: () => this.loading.set(false),
      });
    inject(DestroyRef).onDestroy(() => sub.unsubscribe());
  }

  protected type(value: string): void {
    this.term.set(value);
    this.active.set(-1);
    if (value.trim().length >= 2) {
      this.loading.set(true);
    } else {
      this.found.set([]);
      this.searched.set(false);
    }
    this.terms.next(value.trim());
  }

  protected key(event: KeyboardEvent): void {
    const count = this.flat().length;
    if (!count) return;
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      this.active.update((i) => (i + 1) % count);
    } else if (event.key === 'ArrowUp') {
      event.preventDefault();
      this.active.update((i) => (i <= 0 ? count - 1 : i - 1));
    }
  }

  protected submit(event: Event): void {
    event.preventDefault();
    const chosen = this.flat()[this.active()];
    if (chosen) {
      this.router.navigateByUrl(chosen.url);
    } else if (this.typed()) {
      this.router.navigate(['/search'], { queryParams: { q: this.term().trim() } });
    } else {
      return;
    }
    this.close.emit();
  }
}

/**
 * Top bar (contact, social, currency, account), then the main bar kept at the
 * top while scrolling, with the menu built from the content in the admin panel.
 */
@Component({
  selector: 'app-header',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, RouterLinkActive, Icon, Brand, CurrencySwitch, SearchPanel],
  host: {
    // No box of its own: the menu bar stays stuck to the top of the whole page while scrolling.
    class: 'contents',
    '(window:scroll)': 'onScroll()',
    '(document:keydown)': 'shortcut($event)',
  },
  template: `
    <!-- Top bar (large screens) -->
    <div class="hidden bg-navy-950 text-[13px] text-white/75 lg:block">
      <div class="container-page flex h-11 items-center justify-between gap-6">
        <ul class="flex min-w-0 items-center gap-6">
          @if (site.phone()) {
            <li><a [href]="'tel:' + site.digits(site.phone())" class="flex items-center gap-2 hover:text-white"><svg icon="phone" class="size-4 text-saffron-400"></svg> {{ site.phone() }}</a></li>
          }
          @if (site.whatsappNumber()) {
            <li><a [href]="site.whatsappUrl()" target="_blank" rel="noopener" class="flex items-center gap-2 hover:text-white"><svg icon="whatsapp" class="size-4 text-whatsapp"></svg> {{ site.whatsappNumber() }}</a></li>
          }
          @if (site.setting('email')) {
            <li><a [href]="'mailto:' + site.setting('email')" class="flex items-center gap-2 hover:text-white"><svg icon="mail" class="size-4 text-saffron-400"></svg> {{ site.setting('email') }}</a></li>
          }
          @if (site.setting('business_hours')) {
            <li class="hidden 2xl:block">{{ site.setting('business_hours') }}</li>
          }
        </ul>
        <div class="flex shrink-0 items-center gap-5">
          @if (site.social().length) {
            <ul class="flex items-center gap-3">
              @for (s of site.social(); track s.network) {
                <li><a [href]="s.url" target="_blank" rel="noopener" [attr.aria-label]="s.network" class="hover:text-white"><svg [icon]="s.network" class="size-4"></svg></a></li>
              }
            </ul>
          }
          <app-currency-switch class="border-l border-white/20 pl-5" />
          <div class="flex items-center gap-4 border-l border-white/20 pl-5">
            @if (site.user()?.is_staff) {
              <a href="/admin" class="font-semibold text-white hover:text-saffron-300">Admin Panel</a>
            }
            <a [routerLink]="site.user() ? '/my-account' : '/login'" class="flex items-center gap-1.5 hover:text-white">
              <svg icon="user" class="size-4"></svg> {{ site.user() ? site.t('top_my_account') : site.t('top_sign_in') }}
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Main bar -->
    <header class="sticky top-0 z-40 border-b bg-white/95 text-navy-900 backdrop-blur transition-shadow" [class]="scrolled() ? 'border-navy-100 shadow-card' : 'border-transparent'">
      <div class="container-page flex h-[4.5rem] items-center gap-4 lg:h-[5.25rem]">
        <a routerLink="/" class="shrink-0" [attr.aria-label]="site.setting('site_name') + ' home'"><app-brand /></a>

        <nav class="ml-auto hidden h-full xl:block" aria-label="Main">
          <ul class="flex h-full items-stretch">
            <li class="group">
              <a routerLink="/destinations" routerLinkActive="text-saffron-600! after:scale-x-100" [class]="navLink">
                {{ site.t('menu_destinations') }} <svg icon="chevron-down" class="size-3.5 opacity-60 transition-transform group-hover:rotate-180"></svg>
              </a>
              <div [class]="dropdown + ' inset-x-0'">
                <div class="container-page">
                  <div class="grid grid-cols-4 gap-8 rounded-b-2xl border-t-[3px] border-saffron-500 bg-white p-8 text-ink shadow-lift">
                    @for (item of site.menu().countries; track item.url) {
                      <div [class.col-span-2]="item.children.length > 7">
                        <a [routerLink]="item.url" class="flex items-baseline justify-between border-b border-navy-100 pb-3 font-display text-lg font-semibold text-navy-900 hover:text-saffron-600">
                          {{ item.name }}
                          <span class="text-xs font-normal text-ink/50">{{ places(item.children.length) }}</span>
                        </a>
                        <ul class="mt-3 grid gap-x-6 text-sm" [class.grid-cols-2]="item.children.length > 7">
                          @for (child of item.children; track child.url) {
                            <li><a [routerLink]="child.url" class="block py-1.5 text-ink/75 transition-colors hover:text-saffron-600">{{ child.name }}</a></li>
                          }
                        </ul>
                      </div>
                    }
                    <div class="col-span-full flex items-center justify-between border-t border-navy-100 pt-5 text-sm">
                      <span class="text-ink/60">{{ site.t('menu_destinations_note') }}</span>
                      <a routerLink="/destinations" class="more-link">{{ site.t('menu_all_destinations') }}</a>
                    </div>
                  </div>
                </div>
              </div>
            </li>

            <li class="group relative">
              <a routerLink="/packages" routerLinkActive="text-saffron-600! after:scale-x-100" [class]="navLink">
                {{ site.t('menu_packages') }}
                @if (site.menu().tour_types.length) { <svg icon="chevron-down" class="size-3.5 opacity-60 transition-transform group-hover:rotate-180"></svg> }
              </a>
              @if (site.menu().tour_types.length) {
                <div [class]="dropdown + ' left-0'">
                  <ul class="w-72 rounded-b-2xl border-t-[3px] border-saffron-500 bg-white p-2 text-sm text-ink shadow-lift">
                    @for (child of site.menu().tour_types; track child.url) {
                      <li><a [routerLink]="child.url" class="block rounded-lg px-4 py-2.5 text-ink/80 hover:bg-saffron-50 hover:text-saffron-700">{{ child.name }}</a></li>
                    }
                    <li class="mt-1 border-t border-navy-100 pt-1"><a routerLink="/packages" class="block rounded-lg px-4 py-2.5 font-semibold text-saffron-700 hover:bg-saffron-50">{{ site.t('menu_all_packages') }}</a></li>
                  </ul>
                </div>
              }
            </li>

            @for (link of staticLinks(); track link.url) {
              <li><a [routerLink]="link.url" routerLinkActive="text-saffron-600! after:scale-x-100" [class]="navLink">{{ link.name }}</a></li>
            }
          </ul>
        </nav>

        <div class="ml-auto flex items-center gap-1 xl:ml-3">
          <button type="button" class="grid size-11 place-items-center rounded-full transition-colors hover:bg-mist hover:text-saffron-600" (click)="searchOpen.set(true)" [attr.aria-label]="site.t('search_title')" [title]="site.t('search_title') + ' (/)'">
            <svg icon="search" class="size-5"></svg>
          </button>
          @if (site.canBook()) {
            <a [routerLink]="site.t('menu_book_link')" class="btn btn-primary ml-2 hidden sm:inline-flex">{{ site.t('menu_book_label') }}</a>
          }
          <button type="button" class="-mr-2 ml-1 grid size-11 place-items-center rounded-full hover:bg-mist xl:hidden" (click)="menuOpen.set(true)" aria-label="Open menu">
            <svg icon="menu" class="size-6"></svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Mobile drawer -->
    @if (menuOpen()) {
      <div class="fixed inset-0 z-50 text-ink xl:hidden" role="dialog" aria-modal="true" aria-label="Menu" (document:keydown.escape)="menuOpen.set(false)">
        <div class="animate-fade absolute inset-0 bg-navy-950/60" (click)="menuOpen.set(false)"></div>
        <div class="absolute inset-y-0 right-0 flex w-[88%] max-w-sm animate-[slide_0.22s_ease-out] flex-col bg-white shadow-lift">
          <div class="flex items-center justify-between border-b border-navy-100 px-4 py-3">
            <a routerLink="/" [attr.aria-label]="site.setting('site_name') + ' home'"><app-brand /></a>
            <button type="button" class="grid size-10 place-items-center rounded-full hover:bg-mist" (click)="menuOpen.set(false)" aria-label="Close menu"><svg icon="x" class="size-6"></svg></button>
          </div>

          <nav class="flex-1 overflow-y-auto px-2 py-3 text-[15px] font-medium text-navy-900" aria-label="Mobile">
            <a routerLink="/destinations" class="block rounded-lg px-3 py-3 hover:bg-mist">{{ site.t('menu_all_destinations') }}</a>
            @for (item of site.menu().countries; track item.url; let i = $index) {
              <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left hover:bg-mist" (click)="toggle('c' + i)" [attr.aria-expanded]="expanded() === 'c' + i">
                {{ item.name }} <svg icon="chevron-down" class="size-4 transition-transform" [class.rotate-180]="expanded() === 'c' + i"></svg>
              </button>
              @if (expanded() === 'c' + i) {
                <ul class="mb-1 ml-3 border-l-2 border-saffron-200 pl-2 text-sm font-normal text-ink/80">
                  <li><a [routerLink]="item.url" class="block rounded-lg px-3 py-2.5 font-medium text-saffron-700">{{ item.name }}: {{ places(item.children.length) }}</a></li>
                  @for (child of item.children; track child.url) {
                    <li><a [routerLink]="child.url" class="block rounded-lg px-3 py-2.5 hover:bg-mist">{{ child.name }}</a></li>
                  }
                </ul>
              }
            }
            <button type="button" class="flex w-full items-center justify-between rounded-lg px-3 py-3 text-left hover:bg-mist" (click)="toggle('packages')" [attr.aria-expanded]="expanded() === 'packages'">
              {{ site.t('menu_packages') }} <svg icon="chevron-down" class="size-4 transition-transform" [class.rotate-180]="expanded() === 'packages'"></svg>
            </button>
            @if (expanded() === 'packages') {
              <ul class="mb-1 ml-3 border-l-2 border-saffron-200 pl-2 text-sm font-normal text-ink/80">
                <li><a routerLink="/packages" class="block rounded-lg px-3 py-2.5 font-medium text-saffron-700">{{ site.t('menu_all_packages') }}</a></li>
                @for (child of site.menu().tour_types; track child.url) {
                  <li><a [routerLink]="child.url" class="block rounded-lg px-3 py-2.5 hover:bg-mist">{{ child.name }}</a></li>
                }
              </ul>
            }
            <div class="my-2 border-t border-navy-100"></div>
            @for (link of staticLinks(); track link.url) {
              <a [routerLink]="link.url" class="block rounded-lg px-3 py-3 hover:bg-mist">{{ link.name }}</a>
            }
            <a [routerLink]="site.user() ? '/my-account' : '/login'" class="block rounded-lg px-3 py-3 hover:bg-mist">{{ site.user() ? site.t('top_my_account') : site.t('top_sign_in') }}</a>
            @if (site.user()?.is_staff) {
              <a href="/admin" class="block rounded-lg px-3 py-3 font-semibold text-saffron-700 hover:bg-mist">Admin Panel</a>
            }
            <app-currency-switch [dark]="false" class="mt-3 px-3 text-sm" />
          </nav>

          <div class="space-y-2 border-t border-navy-100 p-3 text-sm">
            @if (site.phone()) {
              <a [href]="'tel:' + site.digits(site.phone())" class="flex items-center gap-2 px-1 py-1 font-medium text-navy-900"><svg icon="phone" class="size-4 text-saffron-600"></svg> {{ site.phone() }}</a>
            }
            @if (site.setting('email')) {
              <a [href]="'mailto:' + site.setting('email')" class="flex items-center gap-2 px-1 py-1 font-medium text-navy-900"><svg icon="mail" class="size-4 text-saffron-600"></svg> {{ site.setting('email') }}</a>
            }
            @if (site.canBook()) {
              <a [routerLink]="site.t('menu_book_link')" class="btn btn-primary w-full">{{ site.t('menu_book_label') }}</a>
            }
            @if (site.whatsappNumber()) {
              <a [href]="site.whatsappUrl()" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><svg icon="whatsapp" class="size-4"></svg> WhatsApp {{ site.whatsappNumber() }}</a>
            }
          </div>
        </div>
      </div>
    }

    @if (searchOpen()) {
      <app-search-panel (close)="searchOpen.set(false)" />
    }
  `,
})
export class Header {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);

  protected readonly scrolled = signal(false);
  protected readonly menuOpen = signal(false);
  protected readonly searchOpen = signal(false);
  protected readonly expanded = signal<string | null>(null);

  protected readonly navLink =
    'relative flex h-full items-center gap-1 px-3.5 text-[15px] font-medium text-navy-900 transition-colors hover:text-saffron-600 ' +
    'after:absolute after:inset-x-3.5 after:bottom-5 after:h-0.5 after:origin-left after:scale-x-0 after:rounded-full after:bg-saffron-500 after:transition-transform hover:after:scale-x-100';
  protected readonly dropdown =
    'invisible absolute top-full z-50 translate-y-2 opacity-0 transition-all duration-200 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100';

  /** Gallery, then pages ticked "Show in header menu" (Website > Pages), then Contact. */
  protected readonly staticLinks = computed(() => [
    { name: this.site.t('gallery_title'), url: '/gallery' },
    ...this.site.menu().header_pages,
    { name: this.site.t('menu_contact'), url: '/contact-us' },
  ]);

  constructor() {
    // Close the drawer (and any open dropdown) after following a link.
    this.router.events.pipe(filter((e) => e instanceof NavigationEnd)).subscribe(() => {
      this.menuOpen.set(false);
      (document.activeElement as HTMLElement | null)?.blur?.();
    });
    effect(() => document.documentElement.classList.toggle('overflow-hidden', this.menuOpen()));
  }

  protected onScroll(): void {
    this.scrolled.set(window.scrollY > 10);
  }

  protected places(count: number): string {
    return this.site.t(count === 1 ? 'menu_places_one' : 'menu_places_many', { count });
  }

  protected toggle(key: string): void {
    this.expanded.update((current) => (current === key ? null : key));
  }

  /** "/" opens the search, unless the visitor is typing somewhere. */
  protected shortcut(event: KeyboardEvent): void {
    const target = event.target as HTMLElement;
    if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName) && !target.isContentEditable) {
      event.preventDefault();
      this.searchOpen.set(true);
    }
  }
}
