import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, effect, inject, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { apiUrl, errorStatus } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { DestinationCard as Destination } from '../core/types';
import { DestinationCard } from '../shared/cards';
import { Crumb, PageBanner, Reveal } from '../shared/ui';
import { NotFoundPage } from './not-found';

interface DestinationsData {
  country: { name: string; slug: string } | null;
  countries: { name: string; slug: string }[];
  groups: { name: string; slug: string; destinations: Destination[] }[];
}

/** /destinations and /destinations/country/:country */
@Component({
  selector: 'app-destinations',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, PageBanner, Reveal, DestinationCard, NotFoundPage],
  template: `
    @if (errorStatus(data) === 404) {
      <app-not-found />
    } @else {
      <app-page-banner [title]="title()" [intro]="intro()" [crumbs]="crumbs()" />

      <div class="container-page section pt-10 sm:pt-14">
        <nav aria-label="Countries" class="no-scrollbar -mx-4 flex justify-start gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:justify-center sm:px-0">
          <a routerLink="/destinations" [class]="tab(!country())">{{ site.t('dest_tab_all') }}</a>
          @for (c of data.value()?.countries ?? []; track c.slug) {
            <a [routerLink]="'/destinations/country/' + c.slug" [class]="tab(country() === c.slug)">{{ c.name }}</a>
          }
        </nav>

        @if (data.value(); as d) {
          @for (group of d.groups; track group.slug) {
            <section class="mt-12">
              @if (!country()) {
                <div class="flex flex-wrap items-end justify-between gap-3">
                  <div>
                    <h2 class="text-2xl font-bold sm:text-3xl">{{ group.name }}</h2>
                    <span class="title-rule"></span>
                  </div>
                  <a [routerLink]="'/destinations/country/' + group.slug" class="more-link">{{ places(group.destinations.length) }}</a>
                </div>
              }
              <div class="mt-7 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @for (dest of group.destinations; track dest.id; let i = $index) {
                  <app-destination-card appReveal [revealDelay]="(i % 4) * 70" [destination]="dest" />
                }
              </div>
            </section>
          } @empty {
            <p class="card mt-8 p-10 text-center">{{ site.t('dest_empty') }}</p>
          }
        } @else if (data.isLoading()) {
          <div class="mt-12 grid grid-cols-2 gap-5 lg:grid-cols-4">
            @for (i of [1, 2, 3, 4]; track i) { <div class="skeleton aspect-[4/5]"></div> }
          </div>
        }
      </div>
    }
  `,
})
export class DestinationsPage {
  protected readonly site = inject(Site);
  readonly country = input<string>();

  protected readonly data = httpResource<DestinationsData>(() => apiUrl('/api/destinations', { country: this.country() }));
  protected readonly errorStatus = errorStatus;

  protected readonly countryName = computed(() => this.data.value()?.country?.name ?? null);
  protected readonly title = computed(() =>
    this.countryName() ? this.site.t('country_page_title', { country: this.countryName() }) : this.site.t('destinations_page_title'),
  );
  protected readonly intro = computed(() =>
    this.country() ? this.site.t('country_intro_' + this.country()) : this.site.t('destinations_page_intro'),
  );
  protected readonly crumbs = computed<Crumb[]>(() =>
    this.country()
      ? [{ label: this.site.t('destinations_page_title'), url: '/destinations' }, { label: this.countryName() ?? '' }]
      : [{ label: this.site.t('destinations_page_title') }],
  );

  constructor() {
    const seo = inject(Seo);
    effect(() => seo.set({ title: this.title(), description: this.intro() }));
  }

  protected places(count: number): string {
    return this.site.t(count === 1 ? 'menu_places_one' : 'menu_places_many', { count });
  }

  protected tab(active: boolean): string {
    return (
      'rounded-full border px-5 py-2.5 text-sm font-medium whitespace-nowrap transition-colors ' +
      (active ? 'border-saffron-600 bg-saffron-600 text-white' : 'border-navy-200 bg-white text-navy-800 hover:border-saffron-500 hover:text-saffron-700')
    );
  }
}
