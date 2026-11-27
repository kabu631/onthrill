import { HttpClient } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, inject, input, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { errorMessage, fieldErrors } from '../../core/api';
import { Seo } from '../../core/seo';
import { Site } from '../../core/site';
import { User } from '../../core/types';
import { Field } from '../../shared/forms';

interface SignedIn {
  user: User;
  can_book: boolean;
  redirect: string;
  message: string | null;
}

/** Shared by the sign-in forms: sending, errors, and where to go afterwards. */
abstract class AuthForm {
  protected readonly site = inject(Site);
  protected readonly http = inject(HttpClient);
  protected readonly router = inject(Router);
  private readonly route = inject(ActivatedRoute);

  protected readonly sending = signal(false);
  protected readonly problem = signal('');
  protected readonly notice = signal('');
  protected readonly errors = signal<Record<string, string>>({});

  constructor(titleKey: string) {
    inject(Seo).set({ title: this.site.t(titleKey), noindex: true });
  }

  protected post<T>(url: string, body: object, done: (res: T) => void): void {
    this.sending.set(true);
    this.problem.set('');
    this.http.post<T>(url, body).subscribe({
      next: (res) => {
        this.sending.set(false);
        this.errors.set({});
        done(res);
      },
      error: (err) => {
        this.sending.set(false);
        this.errors.set(fieldErrors(err));
        this.problem.set(errorMessage(err, this.site.t('form_error')));
      },
    });
  }

  /** Staff go to the admin panel (a separate page); customers back to where they were, or their account. */
  protected signedIn(res: SignedIn): void {
    this.site.signedIn(res.user, res.can_book);
    const next = this.route.snapshot.queryParamMap.get('next');
    const target = next && next.startsWith('/') && !next.startsWith('//') ? next : res.redirect;
    if (target.startsWith('/admin')) {
      location.assign(target);
    } else {
      this.router.navigateByUrl(target, { state: { message: res.message } });
    }
  }
}

const CARD = 'card mx-auto max-w-md p-6 sm:p-10';

@Component({
  selector: 'app-login',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field],
  template: `
    <div class="bg-sand">
      <div class="container-page section">
        <div [class]="card + ' animate-rise'">
          <h1 class="text-center text-3xl font-bold">{{ site.t('login_title') }}</h1>
          <p class="mt-2 text-center text-sm">{{ site.t('login_intro') }}</p>
          @if (problem() && !errors()['email']) { <div role="alert" class="alert-error mt-6">{{ problem() }}</div> }
          <form (ngSubmit)="send()" class="mt-8 space-y-4" novalidate>
            <app-field [label]="site.t('field_email')" for="login-email" [error]="errors()['email']" required>
              <input id="login-email" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" required autocomplete="email" [(ngModel)]="form.email">
            </app-field>
            <app-field [label]="site.t('field_password')" for="login-password" [error]="errors()['password']" required>
              <input id="login-password" name="password" type="password" class="input" required autocomplete="current-password" [(ngModel)]="form.password">
            </app-field>
            <div class="text-right text-sm"><a routerLink="/forgot-password" class="font-medium text-saffron-700 hover:underline">{{ site.t('login_forgot') }}</a></div>
            <button type="submit" class="btn btn-primary w-full" [disabled]="sending()">{{ sending() ? site.t('form_sending') : site.t('login_title') }}</button>
          </form>
          <p class="mt-6 text-center text-sm">{{ site.t('login_new') }} <a routerLink="/register" class="font-semibold text-saffron-700 hover:underline">{{ site.t('login_create') }}</a></p>
        </div>
      </div>
    </div>
  `,
})
export class LoginPage extends AuthForm {
  protected readonly card = CARD;
  protected form = { email: '', password: '' };

  constructor() {
    super('login_title');
  }

  protected send(): void {
    this.post<SignedIn>('/api/login', this.form, (res) => this.signedIn(res));
  }
}

@Component({
  selector: 'app-register',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field],
  template: `
    <div class="bg-sand">
      <div class="container-page section">
        <div [class]="card + ' animate-rise'">
          <h1 class="text-center text-3xl font-bold">{{ site.t('register_title') }}</h1>
          <p class="mt-2 text-center text-sm">{{ site.t('register_intro') }}</p>
          @if (problem()) { <div role="alert" class="alert-error mt-6">{{ problem() }}</div> }
          <form (ngSubmit)="send()" class="mt-8 space-y-4" novalidate>
            <app-field [label]="site.t('field_full_name')" for="reg-name" [error]="errors()['name']" required>
              <input id="reg-name" name="name" class="input" [class.is-invalid]="errors()['name']" required maxlength="120" autocomplete="name" [(ngModel)]="form.name">
            </app-field>
            <app-field [label]="site.t('field_email')" for="reg-email" [error]="errors()['email']" required>
              <input id="reg-email" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" required maxlength="160" autocomplete="email" [(ngModel)]="form.email">
            </app-field>
            <app-field [label]="site.t('field_phone')" for="reg-phone" [error]="errors()['phone']">
              <input id="reg-phone" name="phone" type="tel" inputmode="tel" class="input" [class.is-invalid]="errors()['phone']" maxlength="20" autocomplete="tel" [(ngModel)]="form.phone">
            </app-field>
            <app-field [label]="site.t('field_password')" for="reg-password" [error]="errors()['password']" [hint]="site.t('field_password_hint')" required>
              <input id="reg-password" name="password" type="password" class="input" [class.is-invalid]="errors()['password']" required autocomplete="new-password" [(ngModel)]="form.password">
            </app-field>
            <app-field [label]="site.t('field_confirm_password')" for="reg-confirm" required>
              <input id="reg-confirm" name="password_confirmation" type="password" class="input" required autocomplete="new-password" [(ngModel)]="form.password_confirmation">
            </app-field>
            <button type="submit" class="btn btn-primary w-full" [disabled]="sending()">{{ sending() ? site.t('form_sending') : site.t('register_button') }}</button>
          </form>
          <p class="mt-6 text-center text-sm">{{ site.t('register_have') }} <a routerLink="/login" class="font-semibold text-saffron-700 hover:underline">{{ site.t('login_title') }}</a></p>
        </div>
      </div>
    </div>
  `,
})
export class RegisterPage extends AuthForm {
  protected readonly card = CARD;
  protected form = { name: '', email: '', phone: '', password: '', password_confirmation: '' };

  constructor() {
    super('register_title');
  }

  protected send(): void {
    this.post<SignedIn>('/api/register', this.form, (res) => this.signedIn(res));
  }
}

@Component({
  selector: 'app-forgot-password',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field],
  template: `
    <div class="bg-sand">
      <div class="container-page section">
        <div [class]="card + ' animate-rise'">
          <h1 class="text-center text-3xl font-bold">{{ site.t('forgot_title') }}</h1>
          <p class="mt-2 text-center text-sm">{{ site.t('forgot_intro') }}</p>
          @if (notice()) { <div role="status" class="alert-success mt-6"><p>{{ notice() }}</p></div> }
          @if (problem() && !errors()['email']) { <div role="alert" class="alert-error mt-6">{{ problem() }}</div> }
          <form (ngSubmit)="send()" class="mt-6 space-y-4" novalidate>
            <app-field [label]="site.t('field_email')" for="forgot-email" [error]="errors()['email']" required>
              <input id="forgot-email" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" required maxlength="160" autocomplete="email" [(ngModel)]="form.email">
            </app-field>
            <button type="submit" class="btn btn-primary w-full" [disabled]="sending()">{{ sending() ? site.t('form_sending') : site.t('forgot_button') }}</button>
          </form>
          <p class="mt-6 text-center text-sm">{{ site.t('forgot_back') }} <a routerLink="/login" class="font-semibold text-saffron-700 hover:underline">{{ site.t('login_title') }}</a></p>
          @if (site.phone()) { <p class="mt-2 text-center text-xs text-ink/60">{{ site.t('email_call_us', { phone: site.phone() }) }}</p> }
        </div>
      </div>
    </div>
  `,
})
export class ForgotPasswordPage extends AuthForm {
  protected readonly card = CARD;
  protected form = { email: '' };

  constructor() {
    super('forgot_title');
  }

  protected send(): void {
    this.notice.set('');
    this.post<{ message: string }>('/api/forgot-password', this.form, (res) => this.notice.set(res.message));
  }
}

@Component({
  selector: 'app-reset-password',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [FormsModule, RouterLink, Field],
  template: `
    <div class="bg-sand">
      <div class="container-page section">
        <div [class]="card + ' animate-rise'">
          <h1 class="text-center text-3xl font-bold">{{ site.t('reset_title') }}</h1>
          <p class="mt-2 text-center text-sm">{{ site.t('reset_intro') }}</p>
          @if (problem() && !errors()['email']) { <div role="alert" class="alert-error mt-6">{{ problem() }}</div> }
          <form (ngSubmit)="send()" class="mt-8 space-y-4" novalidate>
            <app-field [label]="site.t('field_email')" for="reset-email" [error]="errors()['email']" required>
              <input id="reset-email" name="email" type="email" class="input" [class.is-invalid]="errors()['email']" required maxlength="160" autocomplete="email" [(ngModel)]="form.email">
            </app-field>
            @if (errors()['email']) {
              <p class="text-sm"><a routerLink="/forgot-password" class="font-medium text-saffron-700 underline">{{ site.t('forgot_button') }}</a></p>
            }
            <app-field [label]="site.t('field_new_password')" for="reset-password" [error]="errors()['password']" required>
              <input id="reset-password" name="password" type="password" class="input" [class.is-invalid]="errors()['password']" required minlength="8" autocomplete="new-password" [(ngModel)]="form.password">
            </app-field>
            <app-field [label]="site.t('field_confirm_password')" for="reset-confirm" required>
              <input id="reset-confirm" name="password_confirmation" type="password" class="input" required minlength="8" autocomplete="new-password" [(ngModel)]="form.password_confirmation">
            </app-field>
            <button type="submit" class="btn btn-primary w-full" [disabled]="sending()">{{ sending() ? site.t('form_sending') : site.t('reset_button') }}</button>
          </form>
        </div>
      </div>
    </div>
  `,
})
export class ResetPasswordPage extends AuthForm implements OnInit {
  protected readonly card = CARD;
  readonly token = input.required<string>();
  readonly email = input<string>();
  protected form = { email: '', password: '', password_confirmation: '' };

  constructor() {
    super('reset_title');
  }

  ngOnInit(): void {
    this.form.email = this.email() ?? '';
  }

  protected send(): void {
    this.post<SignedIn>('/api/reset-password', { ...this.form, token: this.token() }, (res) => this.signedIn(res));
  }
}
