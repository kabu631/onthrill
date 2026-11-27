import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, inject } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { EnquiryForm } from '../shared/forms';
import { PageBanner, Reveal } from '../shared/ui';

/** /contact-us — four ways to reach the team, the enquiry form and the office on a map. */
@Component({
  selector: 'app-contact',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [PageBanner, Reveal, EnquiryForm],
  template: `
    <app-page-banner [title]="site.t('contact_title')" [intro]="site.t('contact_intro')" [crumbs]="[{ label: site.t('contact_title') }]" />

    <div class="container-page section pt-10 sm:pt-14">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @for (card of cards(); track card.label; let i = $index) {
          <div appReveal [revealDelay]="i * 70" class="flex flex-col rounded-xl border border-navy-100 border-t-4 border-t-saffron-500 bg-white p-6">
            <p class="text-xs font-semibold tracking-wider text-navy-400 uppercase">{{ card.label }}</p>
            @if (card.href) {
              <a [href]="card.href" [attr.target]="card.external ? '_blank' : null" rel="noopener" class="mt-1 font-display text-lg font-semibold break-words text-navy-900 hover:text-saffron-600">{{ card.value }}</a>
            } @else {
              <p class="mt-1 font-display text-lg font-semibold text-navy-900">{{ card.value }}</p>
            }
            @if (card.note) { <p class="mt-2 text-xs text-ink/60">{{ card.note }}</p> }
          </div>
        }
      </div>

      <div class="mt-10 grid items-stretch gap-8 lg:mt-12 lg:grid-cols-12">
        <div appReveal class="card flex flex-col justify-between p-6 sm:p-8 lg:col-span-7">
          <div>
            <h2 class="text-2xl font-bold sm:text-3xl">{{ site.t('contact_form_title') }}</h2>
            <p class="mt-1 text-sm text-ink/75">{{ site.t('contact_required') }}</p>
            <app-enquiry-form [packages]="data.value()?.packages ?? []" />
          </div>
          <p class="mt-6 border-t border-navy-100 pt-5 text-xs text-navy-500">{{ site.t('contact_privacy') }}</p>
        </div>

        <div appReveal class="card flex flex-col overflow-hidden lg:col-span-5">
          <div class="flex items-start justify-between gap-3 border-b border-navy-100 p-6">
            <div>
              <p class="text-xs font-semibold tracking-wider text-saffron-700 uppercase">{{ site.t('contact_office_badge') }}</p>
              <h3 class="mt-2 text-xl font-bold">{{ site.setting('site_name') }}</h3>
              <p class="mt-0.5 text-xs text-navy-600">{{ place() }}</p>
            </div>
            <a [href]="directions()" target="_blank" rel="noopener" class="btn btn-outline btn-sm shrink-0">{{ site.t('contact_directions') }}</a>
          </div>
          @if (map()) {
            <div class="relative min-h-[340px] flex-1 bg-navy-50 sm:min-h-[380px]">
              <iframe [src]="map()" [title]="site.t('contact_map_title')" loading="lazy" class="absolute inset-0 size-full border-0" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            </div>
          }
          @if (site.setting('business_hours')) {
            <p class="border-t border-navy-100 bg-mist px-6 py-3.5 text-xs text-navy-700">{{ site.t('contact_label_hours') }}: {{ site.setting('business_hours') }}</p>
          }
        </div>
      </div>
    </div>
  `,
})
export class ContactPage {
  protected readonly site = inject(Site);
  private readonly sanitizer = inject(DomSanitizer);
  protected readonly data = httpResource<{ packages: { id: number; name: string }[] }>(() => '/api/contact');

  /** The office address, its coordinates, or the "map location" (Site Settings > Contact & Social). */
  protected readonly place = computed(() => {
    const s = this.site.settings();
    return s.address || (s.geo_latitude && s.geo_longitude ? `${s.geo_latitude},${s.geo_longitude}` : s.map_location);
  });
  protected readonly map = computed(() => {
    const embed = this.site.setting('map_embed_url');
    const url = embed && /^https:\/\//.test(embed)
      ? embed
      : this.place() ? `https://maps.google.com/maps?q=${encodeURIComponent(this.place())}&z=14&output=embed` : '';
    // The embed link is set by staff in Site Settings; only https addresses are framed.
    return url ? this.sanitizer.bypassSecurityTrustResourceUrl(url) : null;
  });
  protected readonly directions = computed(
    () => 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(this.place() || this.site.setting('site_name')),
  );

  protected readonly cards = computed(() => {
    const s = this.site.settings();
    const wa = this.site.whatsappNumber();
    return [
      { label: this.site.t('contact_label_phone'), value: s.phone || '—', href: s.phone ? 'tel:' + this.site.digits(s.phone) : null, note: s.business_hours, external: false },
      { label: this.site.t('contact_label_whatsapp'), value: wa || '—', href: wa ? this.site.whatsappUrl() : null, note: wa ? this.site.t('contact_whatsapp_button') : '', external: true },
      { label: this.site.t('contact_label_email'), value: s.email || '—', href: s.email ? 'mailto:' + s.email : null, note: this.site.t('contact_email_note'), external: false },
      { label: this.site.t('contact_label_address'), value: s.address || s.map_location, href: null, note: '', external: false },
    ];
  });

  constructor() {
    inject(Seo).set({ title: this.site.t('contact_title'), description: this.site.t('contact_intro') });
  }
}
