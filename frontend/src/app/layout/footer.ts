import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Site } from '../core/site';
import { Icon } from '../shared/icon';
import { Brand } from './header';

/** Four columns over a darkened Himalayan photograph, then the copyright line. */
@Component({
  selector: 'app-footer',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon, Brand],
  host: { class: 'block mt-auto' },
  template: `
    <footer class="relative isolate overflow-hidden bg-navy-950 text-sm text-white/70">
      @if (site.setting('footer_background'); as bg) {
        <img [src]="bg" alt="" loading="lazy" decoding="async" class="absolute inset-0 -z-20 size-full object-cover opacity-50">
        <div class="absolute inset-0 -z-10 bg-navy-950/85" aria-hidden="true"></div>
      }

      <!-- Contact strip -->
      <div class="border-b border-white/10">
        <div class="container-page flex flex-col items-start justify-between gap-6 py-10 lg:flex-row lg:items-center">
          <div>
            <p class="font-script text-2xl text-saffron-400">{{ site.t('eyebrow_cta') }}</p>
            <p class="mt-1 font-display text-2xl font-semibold text-white sm:text-3xl">{{ site.setting('cta_title') }}</p>
          </div>
          <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
            @if (site.phone()) {
              <a [href]="'tel:' + site.digits(site.phone())" class="block">
                <span class="block text-xs text-white/50">{{ site.t('footer_call_label') }}</span>
                <span class="font-display text-xl font-semibold text-white hover:text-saffron-300">{{ site.phone() }}</span>
              </a>
            }
            <a routerLink="/contact-us" class="btn btn-primary">{{ site.t('footer_button') }}</a>
          </div>
        </div>
      </div>

      <div class="container-page grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.3fr] lg:gap-12">
        <div>
          <a routerLink="/" class="inline-block" [attr.aria-label]="site.setting('site_name') + ' home'"><app-brand [light]="true" /></a>
          <p class="mt-6 max-w-xs leading-relaxed">{{ site.setting('footer_about') }}</p>
          @if (site.social().length) {
            <div class="mt-6 flex gap-2">
              @for (s of site.social(); track s.network) {
                <a [href]="s.url" target="_blank" rel="noopener" [attr.aria-label]="s.network" class="grid size-9 place-items-center rounded-full bg-white/10 text-white transition-colors hover:bg-saffron-600">
                  <svg [icon]="s.network" class="size-4"></svg>
                </a>
              }
            </div>
          }
        </div>

        <div>
          <h2 [class]="heading">{{ site.t('footer_destinations_title') }}</h2>
          <ul class="mt-5 space-y-2.5">
            @for (item of site.menu().countries; track item.url) {
              <li><a [routerLink]="item.url" [class]="link">{{ item.name }}</a></li>
            }
            <li><a routerLink="/destinations" [class]="link">{{ site.t('menu_all_destinations') }}</a></li>
          </ul>
          @if (site.menu().tour_types.length) {
            <h2 [class]="heading + ' mt-9'">{{ site.t('footer_packages_title') }}</h2>
            <ul class="mt-5 space-y-2.5">
              @for (item of site.menu().tour_types; track item.url) {
                <li><a [routerLink]="item.url" [class]="link">{{ item.name }}</a></li>
              }
            </ul>
          }
        </div>

        <div>
          <h2 [class]="heading">{{ site.t('footer_links_title') }}</h2>
          <ul class="mt-5 space-y-2.5">
            <li><a routerLink="/packages" [class]="link">{{ site.t('footer_all_packages') }}</a></li>
            <li><a routerLink="/gallery" [class]="link">{{ site.t('gallery_title') }}</a></li>
            @for (page of site.menu().footer_pages; track page.url) {
              <li><a [routerLink]="page.url" [class]="link">{{ page.name }}</a></li>
            }
            <li><a routerLink="/contact-us" [class]="link">{{ site.t('contact_title') }}</a></li>
          </ul>
        </div>

        <div>
          <h2 [class]="heading">{{ site.t('footer_contact_title') }}</h2>
          <dl class="mt-5 space-y-4">
            @if (site.setting('address')) {
              <div><dt class="text-xs text-white/50">{{ site.t('footer_office_label') }}</dt><dd class="mt-0.5 text-white">{{ site.setting('address') }}</dd></div>
            }
            @if (site.setting('email')) {
              <div><dt class="text-xs text-white/50">{{ site.t('footer_email_label') }}</dt><dd class="mt-0.5"><a [href]="'mailto:' + site.setting('email')" class="break-all text-white hover:text-saffron-300">{{ site.setting('email') }}</a></dd></div>
            }
            @if (site.whatsappNumber()) {
              <div><dt class="text-xs text-white/50">WhatsApp</dt><dd class="mt-0.5"><a [href]="site.whatsappUrl()" target="_blank" rel="noopener" class="text-white hover:text-saffron-300">{{ site.whatsappNumber() }}</a></dd></div>
            }
            @if (site.setting('business_hours')) {
              <div><dt class="text-xs text-white/50">{{ site.t('footer_hours_label') }}</dt><dd class="mt-0.5 text-white">{{ site.setting('business_hours') }}</dd></div>
            }
            <div><dt class="text-xs text-white/50">{{ site.t('fact_currency_value') }}</dt><dd class="mt-0.5 text-white">{{ site.t('fact_currency') }}</dd></div>
          </dl>
        </div>
      </div>

      <div class="border-t border-white/10">
        <div class="container-page flex flex-col items-start justify-between gap-1.5 py-5 pr-20 text-xs sm:flex-row sm:items-center sm:pr-6 lg:pr-8">
          <p>{{ site.t('footer_copyright', { year: year, site: site.setting('site_name') }) }}</p>
          <p class="text-white/50"><a routerLink="/photo-credits" class="underline underline-offset-2 hover:text-white">{{ site.t('footer_credits') }}</a></p>
        </div>
      </div>
    </footer>
  `,
})
export class Footer {
  protected readonly site = inject(Site);
  protected readonly year = new Date().getFullYear();
  protected readonly heading = 'font-display text-lg font-semibold text-white after:mt-3 after:block after:h-0.5 after:w-8 after:bg-saffron-500';
  protected readonly link = 'transition-colors hover:text-saffron-300';
}

/** Back to top (after scrolling) above the WhatsApp chat button. */
@Component({
  selector: 'app-floating',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  host: { '(window:scroll)': 'onScroll()' },
  template: `
    <div class="fixed right-4 z-30 flex flex-col items-center gap-3 sm:right-6" style="bottom: calc(1.25rem + var(--bottom-bar, 0px))">
      @if (visible()) {
        <button type="button" (click)="top()" class="animate-fade grid size-11 place-items-center rounded-full bg-navy-800 text-white shadow-lift transition-colors hover:bg-navy-900" aria-label="Back to top" title="Back to top">
          <svg icon="arrow-up" class="size-5"></svg>
        </button>
      }
      @if (site.whatsappNumber()) {
        <a [href]="site.whatsappUrl()" target="_blank" rel="noopener" class="relative grid size-14 place-items-center rounded-full bg-whatsapp text-white shadow-lift transition-transform hover:scale-105" aria-label="Chat with us on WhatsApp" title="Chat on WhatsApp">
          <svg icon="whatsapp" class="size-8"></svg>
        </a>
      }
    </div>
  `,
})
export class Floating {
  protected readonly site = inject(Site);
  protected readonly visible = signal(false);

  protected onScroll(): void {
    this.visible.set(window.scrollY > 600);
  }

  protected top(): void {
    scrollTo({ top: 0, behavior: 'smooth' });
  }
}
