import { HttpClient } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, inject, input, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { errorMessage, fieldErrors } from '../../core/api';
import { Site } from '../../core/site';
import { Currency, PackageDetail } from '../../core/types';
import { Field } from '../../shared/forms';

/**
 * Booking request for a package. Nothing is charged: the team confirms by
 * phone. The estimate uses the traveller's currency: Indian rupees for
 * Indian travellers, Nepali rupees for Nepali travellers.
 */
@Component({
  selector: 'app-booking-form',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field],
  template: `
    @let p = package();
    <h2 class="text-2xl font-bold sm:text-3xl">{{ p.prices ? site.t('booking_title') : site.t('booking_title_request') }}</h2>
    <span class="title-rule"></span>
    <p class="mt-4 text-sm">{{ site.t('booking_note') }}</p>

    @if (problem()) {
      <div role="alert" class="alert-error mt-5">{{ problem() }}</div>
    }

    <form (ngSubmit)="send()" class="mt-6 grid gap-4 sm:grid-cols-2" novalidate>
      <app-field [label]="site.t('booking_date')" for="book-date" [error]="errors()['travel_date']" required>
        <input id="book-date" name="travel_date" type="date" class="input" [class.is-invalid]="errors()['travel_date']" required [min]="today" [(ngModel)]="form.travel_date">
      </app-field>
      <div class="grid grid-cols-2 gap-4">
        <app-field [label]="site.t('booking_adults')" for="book-adults" [error]="errors()['adults']" required>
          <select id="book-adults" name="adults" class="input" [ngModel]="adults()" (ngModelChange)="adults.set(+$event)">
            @for (n of adultOptions; track n) { <option [value]="n">{{ n }}</option> }
          </select>
        </app-field>
        <app-field [label]="site.t('booking_children')" for="book-children" [error]="errors()['children']">
          <select id="book-children" name="children" class="input" [ngModel]="children()" (ngModelChange)="children.set(+$event)">
            @for (n of childOptions; track n) { <option [value]="n">{{ n }}</option> }
          </select>
        </app-field>
      </div>

      <fieldset class="sm:col-span-2">
        <legend class="label">{{ site.t('booking_nationality') }} <span class="text-red-600" aria-hidden="true">*</span></legend>
        <div class="flex flex-wrap gap-3">
          @for (n of nationalities; track n.value) {
            <label class="flex cursor-pointer items-center gap-2 rounded-full border px-4 py-2.5 text-sm transition-colors" [class]="nationality() === n.value ? 'border-saffron-500 bg-saffron-50 font-medium' : 'border-navy-200 bg-white'">
              <input type="radio" name="nationality" [value]="n.value" [checked]="nationality() === n.value" (change)="nationality.set(n.value)" class="size-4 accent-saffron-600"> {{ n.label }}
            </label>
          }
        </div>
        <p class="mt-1.5 text-xs text-ink/55">{{ site.t('booking_nationality_note') }}</p>
        @if (errors()['nationality']) { <p class="field-error">{{ errors()['nationality'] }}</p> }
      </fieldset>

      <app-field [label]="site.t('booking_name')" for="book-name" [error]="errors()['name']" required>
        <input id="book-name" name="name" class="input" [class.is-invalid]="errors()['name']" required maxlength="120" autocomplete="name" [(ngModel)]="form.name">
      </app-field>
      <app-field [label]="site.t('booking_phone')" for="book-phone" [error]="errors()['phone']" required>
        <input id="book-phone" name="phone" type="tel" inputmode="tel" class="input" [class.is-invalid]="errors()['phone']" required maxlength="20" autocomplete="tel" [(ngModel)]="form.phone">
      </app-field>
      <app-field [label]="site.t('booking_email')" for="book-email" [error]="errors()['email']" [hint]="site.t('booking_email_hint')" required>
        <input id="book-email" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" required maxlength="160" autocomplete="email" [(ngModel)]="form.email">
      </app-field>
      <app-field [label]="site.t('booking_from')" for="book-from" [error]="errors()['travelling_from']">
        <input id="book-from" name="travelling_from" class="input" maxlength="120" [placeholder]="site.t('booking_from_placeholder')" [(ngModel)]="form.travelling_from">
      </app-field>
      <app-field [label]="site.t('booking_notes')" for="book-notes" [error]="errors()['notes']" class="sm:col-span-2">
        <textarea id="book-notes" name="notes" rows="3" maxlength="2000" class="input" [placeholder]="site.t('booking_notes_placeholder')" [(ngModel)]="form.notes"></textarea>
      </app-field>

      @if (total() > 0) {
        <!-- Live estimate; the final price is confirmed by phone. -->
        <div class="flex flex-wrap items-baseline justify-between gap-2 rounded-xl border border-saffron-200 bg-saffron-50 px-4 py-3 text-sm sm:col-span-2">
          <span>{{ site.t('booking_estimate') }} {{ travellers() }} {{ travellers() === 1 ? site.t('booking_traveller') : site.t('booking_travellers') }}</span>
          <span>
            <strong class="font-display text-lg text-navy-900">{{ site.money(total(), currency()) }}</strong>
            @if (advancePercent() > 0) {
              <span class="text-ink/60"> · {{ site.t('booking_advance') }} <strong class="text-ink">{{ site.money(advance(), currency()) }}</strong></span>
            }
          </span>
        </div>
      }

      <div class="sm:col-span-2">
        <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto" [disabled]="sending()">
          {{ sending() ? site.t('form_sending') : site.t('booking_button') }}
        </button>
        <p class="mt-3 text-xs text-ink/55">{{ site.t('booking_terms') }} <a routerLink="/terms-and-conditions" class="underline">{{ site.t('booking_terms_link') }}</a>.</p>
      </div>
    </form>
  `,
})
export class BookingForm {
  protected readonly site = inject(Site);
  private readonly http = inject(HttpClient);
  private readonly router = inject(Router);

  readonly package = input.required<PackageDetail>();

  protected readonly today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 10);
  protected readonly adultOptions = Array.from({ length: 20 }, (_, i) => i + 1);
  protected readonly childOptions = Array.from({ length: 11 }, (_, i) => i);
  protected readonly nationalities = [
    { value: 'indian', label: 'Indian' },
    { value: 'nepali', label: 'Nepali' },
    { value: 'other', label: 'Other' },
  ];

  protected readonly adults = signal(2);
  protected readonly children = signal(0);
  protected readonly nationality = signal(this.site.currency() === 'NPR' ? 'nepali' : 'indian');
  protected form = {
    travel_date: '',
    name: this.site.user()?.name ?? '',
    phone: this.site.user()?.phone ?? '',
    email: this.site.user()?.email ?? '',
    travelling_from: '',
    notes: '',
  };

  protected readonly sending = signal(false);
  protected readonly problem = signal('');
  protected readonly errors = signal<Record<string, string>>({});

  /** Indian travellers pay in INR, Nepali travellers in NPR; others in the currency shown. */
  protected readonly currency = computed<Currency>(() =>
    this.nationality() === 'nepali' ? 'NPR' : this.nationality() === 'indian' ? 'INR' : this.site.currency(),
  );
  protected readonly travellers = computed(() => this.adults() + this.children());
  protected readonly advancePercent = computed(() => this.site.setting('advance_percent'));
  protected readonly total = computed(() => (this.package().prices?.[this.currency()].now ?? 0) * this.travellers());
  protected readonly advance = computed(() => Math.round((this.total() * this.advancePercent()) / 100));

  protected send(): void {
    this.sending.set(true);
    this.problem.set('');
    const body = { ...this.form, adults: this.adults(), children: this.children(), nationality: this.nationality() };
    this.http.post<{ reference: string; url: string }>(`/api/packages/${this.package().slug}/book`, body).subscribe({
      next: (res) => this.router.navigateByUrl(res.url),
      error: (err) => {
        this.sending.set(false);
        this.errors.set(fieldErrors(err));
        this.problem.set(errorMessage(err, this.site.t('form_error')));
      },
    });
  }
}
