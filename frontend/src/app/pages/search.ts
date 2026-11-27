import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, effect, inject, input } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { apiUrl } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { DestinationCard as Destination, PackageCard as Package } from '../core/types';
import { DestinationCard, PackageCard } from '../shared/cards';
import { Icon } from '../shared/icon';
import { PageBanner, Reveal } from '../shared/ui';

/** /search?q= — matching packages and destinations. */
@Component({
  selector: 'app-search',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon, PageBanner, Reveal, PackageCard, DestinationCard],
  template: `
    <app-page-banner [title]="site.t('search_title')" [crumbs]="[{ label: site.t('search_title') }]">
      <form role="search" class="relative mx-auto mt-8 max-w-2xl" (submit)="search($event, box.value)">
        <label for="search-page" class="sr-only">{{ site.t('search_title') }}</label>
        <input #box id="search-page" type="search" [value]="term()" [placeholder]="site.t('search_placeholder')" class="input rounded-full border-0 py-4 pr-32 pl-12 text-base shadow-lift">
        <svg icon="search" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40"></svg>
        <button type="submit" class="btn btn-primary btn-sm absolute top-1/2 right-2 -translate-y-1/2">{{ site.t('search_page_button') }}</button>
      </form>
    </app-page-banner>

    <div class="container-page section pt-10 sm:pt-14">
      @if (term().length >= 2) {
        @if (data.value(); as d) {
          @if (!d.packages.length && !d.destinations.length) {
            <div class="card mx-auto max-w-lg p-8 text-center">
              <p class="font-display text-xl font-bold text-navy-900">{{ site.t('search_none_title', { term: term() }) }}</p>
              <p class="mt-2 text-sm">{{ site.t('search_none_text') }}</p>
              <a routerLink="/contact-us" class="btn btn-primary mt-5">{{ site.t('list_empty_button') }}</a>
            </div>
          }
          @if (d.packages.length) {
            <section>
              <h2 class="text-2xl font-bold">{{ site.t('search_packages') }} <span class="font-sans text-base font-normal text-ink/50">({{ d.packages.length }})</span></h2>
              <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @for (p of d.packages; track p.id; let i = $index) { <app-package-card appReveal [revealDelay]="(i % 3) * 60" [package]="p" /> }
              </div>
            </section>
          }
          @if (d.destinations.length) {
            <section class="mt-12">
              <h2 class="text-2xl font-bold">{{ site.t('search_destinations') }} <span class="font-sans text-base font-normal text-ink/50">({{ d.destinations.length }})</span></h2>
              <div class="mt-6 grid grid-cols-2 gap-5 lg:grid-cols-4">
                @for (dest of d.destinations; track dest.id; let i = $index) { <app-destination-card appReveal [revealDelay]="(i % 4) * 60" [destination]="dest" /> }
              </div>
            </section>
          }
        } @else {
          <p class="text-center text-sm text-ink/60">{{ site.t('search_searching') }}</p>
        }
      } @else if (term()) {
        <p class="text-center text-sm">{{ site.t('search_too_short') }}</p>
      }
    </div>
  `,
})
export class SearchPage {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);
  readonly q = input<string>();

  protected readonly term = computed(() => (this.q() ?? '').trim().slice(0, 100));
  protected readonly data = httpResource<{ packages: Package[]; destinations: Destination[] }>(() =>
    this.term().length >= 2 ? apiUrl('/api/search', { q: this.term() }) : undefined,
  );

  constructor() {
    const seo = inject(Seo);
    effect(() => seo.set({ title: this.term() ? `${this.site.t('search_title')}: ${this.term()}` : this.site.t('search_title'), noindex: true }));
  }

  protected search(event: Event, q: string): void {
    event.preventDefault();
    this.router.navigate(['/search'], { queryParams: { q: q.trim() } });
  }
}
