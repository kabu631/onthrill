import { HttpClient } from '@angular/common/http';
import { booleanAttribute, ChangeDetectionStrategy, Component, computed, inject, input, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { errorMessage, fieldErrors } from '../core/api';
import { Site } from '../core/site';

/** Label, the control (projected), an optional hint and the field's error. */
@Component({
  selector: 'app-field',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'block' },
  template: `
    <label [attr.for]="for()" class="label">{{ label() }} @if (required()) { <span class="text-red-600" aria-hidden="true">*</span> }</label>
    <ng-content />
    @if (error()) {
      <p class="field-error" [id]="for() + '-error'">{{ error() }}</p>
    } @else if (hint()) {
      <p class="mt-1.5 text-xs text-ink/55">{{ hint() }}</p>
    }
  `,
})
export class Field {
  readonly label = input.required<string>();
  readonly for = input.required<string>();
  readonly error = input<string | undefined>();
  readonly hint = input<string | null>(null);
  readonly required = input(false, { transform: booleanAttribute });
}

let nextId = 0;

/**
 * Enquiry form: the contact page (with a list of tours), or the side of a
 * package or destination page (about that tour or place).
 */
@Component({
  selector: 'app-enquiry-form',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, Field],
  template: `
    <form (ngSubmit)="send()" class="mt-5 grid gap-4" [class]="compact() ? '' : 'sm:grid-cols-2'" novalidate>
      @if (sent()) {
        <div role="status" class="alert-success" [class]="full()">
          <p>{{ sent() }}</p>
        </div>
      } @else if (problem()) {
        <div role="alert" class="alert-error" [class]="full()">{{ problem() }}</div>
      }

      <!-- Honeypot: hidden from people, tempting for bots -->
      <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off" [(ngModel)]="form.website"></label></div>

      @if (about(); as a) {
        <div [class]="full()">
          <p class="label">{{ a.label }}</p>
          <p class="flex items-center gap-2.5 rounded-lg border border-navy-200 bg-mist px-3.5 py-3 text-sm font-semibold text-navy-900">
            {{ a.name }}
          </p>
        </div>
      }

      <app-field [label]="site.t('enquiry_name')" [for]="id + 'name'" [error]="errors()['name']" required>
        <input [id]="id + 'name'" name="name" class="input" [class.is-invalid]="errors()['name']" required maxlength="120" autocomplete="name" [(ngModel)]="form.name">
      </app-field>
      <app-field [label]="site.t('enquiry_phone')" [for]="id + 'phone'" [error]="errors()['phone']" required>
        <input [id]="id + 'phone'" name="phone" type="tel" inputmode="tel" class="input" [class.is-invalid]="errors()['phone']" required maxlength="20" autocomplete="tel" [(ngModel)]="form.phone">
      </app-field>
      <app-field [label]="site.t('enquiry_email')" [for]="id + 'email'" [error]="errors()['email']">
        <input [id]="id + 'email'" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" maxlength="160" autocomplete="email" [(ngModel)]="form.email">
      </app-field>
      @if (!about()) {
        <app-field [label]="site.t('enquiry_topic')" [for]="id + 'topic'" [error]="errors()['package_id']">
          <select [id]="id + 'topic'" name="package_id" class="input" [(ngModel)]="form.package_id">
            <option [ngValue]="null">{{ site.t('enquiry_general') }}</option>
            @for (option of packages(); track option.id) {
              <option [ngValue]="option.id">{{ option.name }}</option>
            }
          </select>
        </app-field>
      }
      <app-field [label]="site.t('enquiry_message')" [for]="id + 'message'" [error]="errors()['message']" required [class]="full()">
        <textarea [id]="id + 'message'" name="message" [rows]="compact() ? 4 : 5" class="input" [class.is-invalid]="errors()['message']" required maxlength="3000"
                  [placeholder]="site.t('enquiry_placeholder')" [(ngModel)]="form.message"></textarea>
      </app-field>

      <div [class]="full()">
        <button type="submit" class="btn btn-primary" [class.w-full]="compact()" [disabled]="sending()">
          @if (sending()) { {{ site.t('form_sending') }} } @else { {{ site.t('enquiry_button') }} }
        </button>
      </div>
    </form>
  `,
})
export class EnquiryForm {
  protected readonly site = inject(Site);
  private readonly http = inject(HttpClient);

  readonly packageId = input<number | null>(null);
  readonly packageName = input<string | null>(null);
  readonly destinationId = input<number | null>(null);
  readonly destinationName = input<string | null>(null);
  readonly packages = input<{ id: number; name: string }[]>([]);
  readonly compact = input(false);

  protected readonly id = 'enq' + nextId++ + '-';
  protected form = { name: '', phone: '', email: '', message: '', package_id: null as number | null, website: '' };
  protected readonly sending = signal(false);
  protected readonly sent = signal('');
  protected readonly problem = signal('');
  protected readonly errors = signal<Record<string, string>>({});

  protected readonly full = computed(() => (this.compact() ? '' : 'sm:col-span-2'));
  protected readonly about = computed(() =>
    this.packageName()
      ? { label: this.site.t('enquiry_tour_label'), name: this.packageName()! }
      : this.destinationName()
        ? { label: this.site.t('enquiry_place_label'), name: this.destinationName()! }
        : null,
  );

  protected send(): void {
    this.sending.set(true);
    this.problem.set('');
    this.sent.set('');
    const body = {
      ...this.form,
      package_id: this.packageId() ?? this.form.package_id,
      destination_id: this.destinationId(),
    };
    this.http.post<{ message: string }>('/api/enquiry', body).subscribe({
      next: (res) => {
        this.sending.set(false);
        this.errors.set({});
        this.sent.set(res.message);
        this.form = { name: '', phone: '', email: '', message: '', package_id: null, website: '' };
      },
      error: (err) => {
        this.sending.set(false);
        this.errors.set(fieldErrors(err));
        this.problem.set(errorMessage(err, this.site.t('form_error')));
      },
    });
  }
}
