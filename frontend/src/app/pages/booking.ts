import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, effect, inject, input, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { errorStatus } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { Booking } from '../core/types';
import { Icon } from '../shared/icon';
import { Loading, PageBanner } from '../shared/ui';
import { NotFoundPage } from './not-found';

/** How to pay the advance: UPI or bank for Indian travellers; eSewa, Khalti or bank for Nepali travellers. */
@Component({
  selector: 'app-payment-box',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  template: `
    @let b = booking();
    @let pay = b.payment!;
    <div class="card border-saffron-300 bg-saffron-50 p-6 sm:p-8">
      <h2 class="text-xl font-bold">{{ site.t('pay_title') }}</h2>
      <p class="mt-2 text-sm" [innerHTML]="intro()"></p>

      @if (pay.qr || pay.ids.length) {
        <div class="mt-5 grid items-start gap-6" [class]="pay.qr ? 'sm:grid-cols-[auto_1fr]' : ''">
          @if (pay.qr) {
            <figure class="mx-auto w-52 rounded-2xl border border-navy-200 bg-white p-3 text-center sm:mx-0">
              <img [src]="pay.qr" [alt]="(pay.india ? 'UPI' : 'eSewa / Khalti') + ' QR code'" width="184" height="184" class="mx-auto size-46 object-contain">
              <figcaption class="mt-2 text-xs text-ink/60">{{ pay.india ? site.t('pay_scan_upi') : site.t('pay_scan_nepal') }}</figcaption>
            </figure>
          }
          <div>
            <dl class="divide-y divide-navy-200 rounded-2xl border border-navy-200 bg-white text-sm">
              @for (id of pay.ids; track id.label) {
                <div class="flex items-center justify-between gap-3 px-4 py-3">
                  <div class="min-w-0">
                    <dt class="text-navy-500">{{ id.label }}</dt>
                    <dd class="font-semibold break-all text-ink">{{ id.value }}</dd>
                  </div>
                  <button type="button" class="btn btn-outline btn-sm shrink-0" (click)="copy(id.value)">{{ copied() === id.value ? site.t('pay_copied') : site.t('pay_copy') }}</button>
                </div>
              }
              <div class="px-4 py-3">
                <dt class="text-navy-500">{{ site.t('pay_name') }}</dt>
                <dd class="font-semibold text-ink">{{ pay.payee }}</dd>
              </div>
            </dl>
            @if (pay.upi_link && pay.amount) {
              <!-- Phones only: a computer has no UPI app to open. -->
              <a [href]="pay.upi_link" class="btn btn-primary mt-4 w-full sm:hidden">{{ site.t('pay_upi_button', { amount: site.money(pay.amount, b.currency) }) }}</a>
            }
          </div>
        </div>
      }

      @if (pay.bank) {
        <h3 class="mt-5 text-sm font-semibold text-ink">{{ site.t('pay_bank') }}</h3>
        <p class="mt-1 text-sm whitespace-pre-line">{{ pay.bank }}</p>
      }

      @if (!pay.qr && !pay.ids.length && !pay.bank) {
        <p class="mt-4 text-sm text-ink">{{ site.t('pay_none') }}</p>
      } @else if (pay.note) {
        <p class="mt-5 text-sm whitespace-pre-line text-ink">{{ pay.note }}</p>
      }

      @if (site.whatsappNumber()) {
        <a [href]="site.whatsappUrl(pay.screenshot_message)" target="_blank" rel="noopener" class="btn btn-whatsapp mt-5"><svg icon="whatsapp" class="size-5"></svg> {{ site.t('pay_screenshot') }}</a>
      }
    </div>
  `,
})
export class PaymentBox {
  protected readonly site = inject(Site);
  readonly booking = input.required<Booking>();
  protected readonly copied = signal('');

  /** The amount and reference in bold (escaped first: they are shown as HTML). */
  protected readonly intro = computed(() => {
    const b = this.booking();
    const amount = b.payment?.amount;
    const bold = (text: string) => `<strong class="text-ink">${escape(text)}</strong>`;
    const text = amount ? this.site.t('pay_text') : this.site.t('pay_text_request');
    return escape(text)
      .split(':amount').join(amount ? bold(this.site.money(amount, b.currency)) : '')
      .split(':reference').join(bold(b.reference));
  });

  protected copy(value: string): void {
    navigator.clipboard?.writeText(value).then(() => {
      this.copied.set(value);
      setTimeout(() => this.copied.set(''), 2000);
    });
  }
}

function escape(text: string): string {
  return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/** /booking/:reference (right after booking) and /my-account/bookings/:reference. */
@Component({
  selector: 'app-booking',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon, PageBanner, PaymentBox, Loading, NotFoundPage],
  template: `
    @if (data.value()?.booking; as b) {
      @if (inAccount()) {
        <app-page-banner [title]="title()" [crumbs]="[{ label: site.t('account_title'), url: '/my-account' }, { label: b.reference }]" />
      }
      <div class="container-page section" [class.pt-10]="inAccount()">
        <div class="mx-auto max-w-3xl">
          @if (!inAccount()) {
            <div class="animate-rise text-center">
              <span class="mx-auto grid size-20 place-items-center rounded-full bg-green-100 text-green-700 ring-8 ring-green-50"><svg icon="check" class="size-10"></svg></span>
              <h1 class="mt-6 text-3xl font-bold sm:text-4xl">{{ site.t('bookingpage_thanks', { name: b.first_name }) }}</h1>
              <p class="mx-auto mt-3 max-w-xl" [innerHTML]="received()"></p>
            </div>
          }

          <div class="card mt-8 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-navy-100 bg-mist px-6 py-4">
              <h2 class="text-xl font-bold">{{ site.t('bookingpage_details') }}</h2>
              <span class="badge bg-navy-900 text-white">{{ b.reference }}</span>
            </div>
            <dl class="divide-y divide-navy-100 px-6 text-sm">
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_tour') }}</dt>
                <dd class="text-ink">@if (b.package_url) { <a [routerLink]="b.package_url" class="font-medium text-saffron-700 underline underline-offset-2">{{ b.package_name }}</a> } @else { {{ b.package_name }} }</dd></div>
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_date') }}</dt><dd class="text-ink">{{ b.travel_date }}</dd></div>
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_travellers') }}</dt>
                <dd class="text-ink">{{ b.adults }} {{ b.adults === 1 ? 'adult' : 'adults' }}@if (b.children) { , {{ b.children }} {{ b.children === 1 ? 'child' : 'children' }} }</dd></div>
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_nationality') }}</dt><dd class="text-ink">{{ b.nationality }}</dd></div>
              @if (b.travelling_from) {
                <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_from') }}</dt><dd class="text-ink">{{ b.travelling_from }}</dd></div>
              }
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_total') }}</dt>
                <dd class="text-ink">
                  @if (b.total !== null) {
                    <strong class="font-display text-lg text-navy-900">{{ site.money(b.total, b.currency) }}</strong>
                    <span class="text-ink/55"> ({{ site.money(b.price_per_person, b.currency) }} {{ site.t('price_per_person') }})</span>
                  } @else { {{ site.t('price_on_request') }} }
                </dd></div>
              <div class="grid gap-1 py-3.5 sm:grid-cols-[11rem_1fr]"><dt class="text-navy-500">{{ site.t('bookingpage_row_status') }}</dt>
                <dd class="flex flex-wrap gap-2"><span class="badge bg-navy-100 text-navy-800">{{ b.status_label }}</span><span class="badge" [class]="b.is_paid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'">{{ b.payment_label }}</span></dd></div>
            </dl>
          </div>

          @if (b.payment) {
            <div class="mt-6"><app-payment-box [booking]="b" /></div>
          }

          <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a routerLink="/" class="btn btn-outline">{{ site.t('bookingpage_home') }}</a>
            @if (site.user()) { <a routerLink="/my-account" class="btn btn-dark">{{ site.t('bookingpage_account') }}</a> }
          </div>
        </div>
      </div>
    } @else if (errorStatus(data) === 404 || errorStatus(data) === 401) {
      <app-not-found />
    } @else {
      <app-loading />
    }
  `,
})
export class BookingPage {
  protected readonly site = inject(Site);
  readonly reference = input.required<string>();
  readonly inAccount = input(false);

  protected readonly data = httpResource<{ booking: Booking }>(() =>
    (this.inAccount() ? '/api/account/bookings/' : '/api/bookings/') + this.reference(),
  );
  protected readonly errorStatus = errorStatus;
  protected readonly title = computed(() => this.site.t('bookingpage_title', { reference: this.reference() }));

  protected readonly received = computed(() => {
    const b = this.data.value()?.booking;
    if (!b) return '';
    let html = escape(this.site.t('bookingpage_received'));
    for (const [key, value] of [['reference', b.reference], ['email', b.email], ['phone', b.phone]]) {
      html = html.split(':' + key).join(`<strong class="text-ink">${escape(value)}</strong>`);
    }
    return html;
  });

  constructor() {
    const seo = inject(Seo);
    effect(() => seo.set({ title: this.title(), noindex: true }));
  }
}
