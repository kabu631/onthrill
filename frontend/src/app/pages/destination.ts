import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, effect, inject, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { errorStatus } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { DestinationCard as Destination, DestinationDetail, PackageCard as Package } from '../core/types';
import { DestinationCard, PackageCard } from '../shared/cards';
import { EnquiryForm } from '../shared/forms';
import { PhotoGrid } from '../shared/photos';
import { FaqList, Loading, PageBanner, Reveal, SectionHeading } from '../shared/ui';
import { NotFoundPage } from './not-found';

interface DestinationData {
  destination: DestinationDetail;
  packages: Package[];
  nearby: Destination[];
}

/** /destinations/:slug — key facts, description, photos, questions, and the tours that go there. */
@Component({
  selector: 'app-destination',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, PageBanner, Reveal, SectionHeading, FaqList, PhotoGrid, EnquiryForm, PackageCard, DestinationCard, Loading, NotFoundPage],
  template: `
    @if (data.value(); as v) {
      @let d = v.destination;
      <app-page-banner [tall]="true" [title]="d.name" [intro]="d.summary" [image]="d.banner" [credit]="d.image_credit" [creditUrl]="d.image_credit_url"
                       [crumbs]="[{ label: site.t('destinations_page_title'), url: '/destinations' }, { label: d.country, url: d.country_url }, { label: d.name }]">
        <p class="animate-rise mt-4 flex items-center justify-center gap-1.5 text-sm text-white/85">
          {{ d.full_region ? d.full_region + ', ' : '' }}{{ d.country }}
        </p>
      </app-page-banner>

      <!-- Key facts, overlapping the bottom of the photo -->
      @if (d.facts.length) {
        <div class="relative z-10 -mt-12">
          <div class="container-page">
            <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl bg-navy-100 shadow-lift" [class]="factCols()">
              @for (fact of d.facts; track fact.key) {
                <div class="flex items-start gap-3 bg-white p-5">
                  <div class="min-w-0">
                    <dt class="text-xs text-navy-500">{{ site.t(fact.key) }}</dt>
                    <dd class="mt-1 text-sm leading-snug font-semibold text-navy-900">{{ fact.value }}</dd>
                  </div>
                </div>
              }
            </dl>
          </div>
        </div>
      }

      <div class="container-page section pt-12 sm:pt-16">
        <div class="grid gap-12 lg:grid-cols-[1fr_23rem]">
          <article class="min-w-0">
            <div appReveal>
              <h2 class="text-2xl font-bold sm:text-3xl">{{ site.t('dest_about_title', { name: d.name }) }}</h2>
              <span class="title-rule"></span>
              <div class="rich mt-6" [innerHTML]="d.description"></div>
            </div>

            @if (d.photos.length > 1) {
              <div appReveal class="mt-12">
                <h2 class="text-2xl font-bold">{{ site.t('dest_photos_title') }}</h2>
                <div class="mt-5"><app-photo-grid [photos]="d.photos" /></div>
              </div>
            }

            @if (d.how_to_reach) {
              <div appReveal class="mt-12">
                <h2 class="text-2xl font-bold">{{ site.t('dest_reach_title', { name: d.name }) }}</h2>
                <div class="mt-4 flex gap-4 rounded-2xl border border-navy-100 bg-mist p-6">
                  <div>
                    <p class="leading-relaxed">{{ d.how_to_reach }}</p>
                    <a [href]="d.map_url" target="_blank" rel="noopener" class="more-link mt-3">{{ site.t('dest_map_link') }}</a>
                  </div>
                </div>
              </div>
            }

            @if (d.faqs.length) {
              <div appReveal class="mt-12">
                <h2 class="text-2xl font-bold">{{ site.t('dest_faq_title', { name: d.name }) }}</h2>
                <app-faq-list class="mt-5 block" [faqs]="d.faqs" />
              </div>
            }

            <p class="mt-10 text-xs text-ink/55">{{ site.t('last_updated', { date: d.updated }) }}</p>
          </article>

          <!-- Enquiry about this place, kept in view while reading -->
          <aside>
            <div class="space-y-5 lg:sticky lg:top-28">
              <div id="enquiry" class="card overflow-hidden">
                <div class="bg-navy-900 px-6 py-5 text-white">
                  <h2 class="font-display text-xl font-semibold text-white">{{ site.t('dest_enquiry_title', { name: d.name }) }}</h2>
                  <p class="mt-1 text-sm text-white/70">{{ site.t('dest_enquiry_text') }}</p>
                </div>
                <div class="px-6 pb-6">
                  <app-enquiry-form [destinationId]="d.id" [destinationName]="d.name" [compact]="true" />
                </div>
              </div>
              @if (v.packages.length) {
                <button type="button" class="btn btn-dark w-full" (click)="scrollToTours()">{{ site.t('dest_tours_button', { count: v.packages.length, name: d.name }) }}</button>
              }
            </div>
          </aside>
        </div>
      </div>

      @if (v.packages.length) {
        <section id="tours" class="section bg-sand">
          <div class="container-page">
            <app-section-heading align="left" [eyebrow]="site.t('eyebrow_packages')" [title]="site.t('dest_tours_title', { name: d.name })" />
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              @for (p of v.packages; track p.id; let i = $index) { <app-package-card appReveal [revealDelay]="(i % 3) * 80" [package]="p" /> }
            </div>
          </div>
        </section>
      }

      @if (v.nearby.length) {
        <section class="section">
          <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
              <app-section-heading align="left" [eyebrow]="site.t('eyebrow_destinations')" [title]="site.t('dest_nearby_title', { country: d.country })" />
              <a [routerLink]="d.country_url" class="more-link mb-1">{{ site.t('dest_nearby_link', { country: d.country }) }}</a>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
              @for (other of v.nearby; track other.id; let i = $index) { <app-destination-card appReveal [revealDelay]="i * 70" [destination]="other" /> }
            </div>
          </div>
        </section>
      }
    } @else if (errorStatus(data) === 404) {
      <app-not-found />
    } @else {
      <app-loading />
    }
  `,
})
export class DestinationPage {
  protected readonly site = inject(Site);
  readonly slug = input.required<string>();

  protected readonly data = httpResource<DestinationData>(() => '/api/destinations/' + this.slug());
  protected readonly errorStatus = errorStatus;

  protected readonly factCols = computed(
    () => ({ 1: 'lg:grid-cols-1', 2: 'lg:grid-cols-2', 3: 'lg:grid-cols-3' })[this.data.value()?.destination.facts.length ?? 4] ?? 'lg:grid-cols-4',
  );

  constructor() {
    const seo = inject(Seo);
    effect(() => {
      const d = this.data.value()?.destination;
      if (d) seo.set({ title: `${d.name}, ${d.country}`, description: d.summary });
    });
  }

  protected scrollToTours(): void {
    document.getElementById('tours')?.scrollIntoView({ behavior: 'smooth' });
  }
}
