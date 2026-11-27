import { HttpClient, httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, effect, inject, input, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { apiUrl, errorMessage, errorStatus, fieldErrors } from '../../core/api';
import { Seo } from '../../core/seo';
import { Site } from '../../core/site';
import { Currency, Paginated, User } from '../../core/types';
import { Field } from '../../shared/forms';
import { Icon } from '../../shared/icon';
import { Loading, PageBanner, Pagination } from '../../shared/ui';

interface AccountBooking {
  reference: string;
  package_name: string;
  travel_date: string | null;
  travellers: number;
  status_label: string;
  payment_label: string;
  is_paid: boolean;
  currency: Currency;
  total: number | null;
}

/** /my-account — the traveller's bookings and details. */
@Component({
  selector: 'app-account',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field, Icon, PageBanner, Pagination, Loading],
  template: `
    <app-page-banner [title]="site.t('account_title')" [crumbs]="[{ label: site.t('account_title') }]" />

    @if (data.value(); as d) {
      <div class="container-page section pt-10 sm:pt-12">
        @if (message()) {
          <div role="status" class="alert-success mb-8"><p>{{ message() }}</p></div>
        }
        <div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
          <section>
            <h2 class="text-2xl font-bold">{{ site.t('account_bookings') }}</h2>
            @if (!d.bookings.data.length) {
              <div class="card mt-5 p-10 text-center">
                <p>{{ site.t('account_empty') }}</p>
                <a routerLink="/packages" class="btn btn-primary mt-5">{{ site.t('account_browse') }}</a>
              </div>
            } @else {
              <ul class="mt-5 space-y-3">
                @for (b of d.bookings.data; track b.reference) {
                  <li>
                    <a [routerLink]="'/my-account/bookings/' + b.reference" class="card flex flex-wrap items-center justify-between gap-3 p-5 transition-all hover:border-saffron-300 hover:shadow-lift">
                      <span>
                        <span class="block font-semibold text-ink">{{ b.package_name }}</span>
                        <span class="text-xs text-ink/55">{{ b.reference }} · {{ b.travel_date }} · {{ b.travellers }} {{ b.travellers === 1 ? site.t('booking_traveller') : site.t('booking_travellers') }}</span>
                      </span>
                      <span class="flex items-center gap-3">
                        <span class="badge" [class]="b.is_paid ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'">{{ b.payment_label }}</span>
                        <span class="badge bg-navy-100 text-navy-800">{{ b.status_label }}</span>
                        @if (b.total !== null) { <span class="font-bold text-navy-800">{{ site.money(b.total, b.currency) }}</span> }
                        <svg icon="chevron-right" class="size-4 text-ink/40"></svg>
                      </span>
                    </a>
                  </li>
                }
              </ul>
              <app-pagination [current]="d.bookings.meta.current_page" [last]="d.bookings.meta.last_page" (go)="go($event)" />
            }
          </section>

          <aside class="space-y-5">
            <div class="card p-6">
              <h2 class="text-xl font-bold">{{ site.t('account_details') }}</h2>
              @if (problem()) { <div role="alert" class="alert-error mt-4">{{ problem() }}</div> }
              <form (ngSubmit)="save()" class="mt-5 space-y-4" novalidate>
                <app-field [label]="site.t('field_full_name')" for="acc-name" [error]="errors()['name']" required>
                  <input id="acc-name" name="name" class="input" required maxlength="120" [(ngModel)]="form.name">
                </app-field>
                <app-field [label]="site.t('field_email')" for="acc-email" [error]="errors()['email']" required>
                  <input id="acc-email" name="email" type="email" class="input" required maxlength="160" [(ngModel)]="form.email">
                </app-field>
                <app-field [label]="site.t('field_phone')" for="acc-phone" [error]="errors()['phone']">
                  <input id="acc-phone" name="phone" type="tel" class="input" maxlength="20" [(ngModel)]="form.phone">
                </app-field>
                <details class="rounded-xl border border-navy-100 p-4 text-sm" [open]="!!(errors()['password'] || errors()['current_password'])">
                  <summary class="cursor-pointer font-medium text-ink">{{ site.t('account_change_password') }}</summary>
                  <div class="mt-4 space-y-4">
                    <app-field [label]="site.t('field_current_password')" for="acc-current" [error]="errors()['current_password']">
                      <input id="acc-current" name="current_password" type="password" class="input" autocomplete="current-password" [(ngModel)]="form.current_password">
                    </app-field>
                    <app-field [label]="site.t('field_new_password')" for="acc-password" [error]="errors()['password']">
                      <input id="acc-password" name="password" type="password" class="input" autocomplete="new-password" [(ngModel)]="form.password">
                    </app-field>
                    <app-field [label]="site.t('field_confirm_password')" for="acc-confirm">
                      <input id="acc-confirm" name="password_confirmation" type="password" class="input" autocomplete="new-password" [(ngModel)]="form.password_confirmation">
                    </app-field>
                  </div>
                </details>
                <button type="submit" class="btn btn-primary w-full" [disabled]="sending()">{{ sending() ? site.t('form_sending') : site.t('account_save') }}</button>
              </form>
            </div>
            @if (site.user()?.is_staff) {
              <a href="/admin" class="btn btn-dark w-full">Open Admin Panel</a>
            }
            <button type="button" class="btn btn-outline w-full" (click)="signOut()">{{ site.t('account_sign_out') }}</button>
          </aside>
        </div>
      </div>
    } @else if (errorStatus(data) === 401) {
      <div class="container-page section text-center">
        <p>{{ site.t('login_intro') }}</p>
        <a routerLink="/login" [queryParams]="{ next: '/my-account' }" class="btn btn-primary mt-5">{{ site.t('login_title') }}</a>
      </div>
    } @else {
      <app-loading />
    }
  `,
})
export class AccountPage {
  protected readonly site = inject(Site);
  private readonly http = inject(HttpClient);
  private readonly router = inject(Router);
  readonly page = input<string>();

  protected readonly data = httpResource<{ user: User; bookings: Paginated<AccountBooking> }>(() =>
    apiUrl('/api/account', { page: Number(this.page()) > 1 ? this.page() : null }),
  );
  protected readonly errorStatus = errorStatus;

  protected form = { name: '', email: '', phone: '', current_password: '', password: '', password_confirmation: '' };
  protected readonly sending = signal(false);
  protected readonly problem = signal('');
  protected readonly errors = signal<Record<string, string>>({});
  protected readonly message = signal<string>((history.state?.message as string) ?? '');

  constructor() {
    inject(Seo).set({ title: this.site.t('account_title'), noindex: true });
    effect(() => {
      const user = this.data.value()?.user;
      if (user) this.form = { ...this.form, name: user.name, email: user.email, phone: user.phone ?? '' };
    });
    // Signed out in another tab, or the session ended: sign in again.
    effect(() => {
      if (errorStatus(this.data) === 401) this.router.navigate(['/login'], { queryParams: { next: '/my-account' } });
    });
  }

  protected save(): void {
    this.sending.set(true);
    this.problem.set('');
    this.http.put<{ user: User; message: string }>('/api/account', this.form).subscribe({
      next: (res) => {
        this.sending.set(false);
        this.errors.set({});
        this.message.set(res.message);
        this.site.signedIn(res.user, this.site.canBook());
        this.form = { ...this.form, current_password: '', password: '', password_confirmation: '' };
        scrollTo({ top: 0, behavior: 'smooth' });
      },
      error: (err) => {
        this.sending.set(false);
        this.errors.set(fieldErrors(err));
        this.problem.set(errorMessage(err, this.site.t('form_error')));
      },
    });
  }

  protected go(page: number): void {
    this.router.navigate([], { queryParams: { page: page > 1 ? page : null } });
  }

  protected async signOut(): Promise<void> {
    await this.site.logout();
    this.router.navigateByUrl('/');
  }
}
