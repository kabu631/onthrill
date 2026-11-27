import { DecimalPipe } from '@angular/common';
import { ChangeDetectionStrategy, Component, computed, inject, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Site } from '../core/site';
import { DestinationCard as Destination, PackageCard as Package } from '../core/types';
import { Media } from './ui';

/** Per-person price in the visitor's currency: offer, regular price, or "on request". */
@Component({
  selector: 'app-price',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'block' },
  template: `
    @if (price(); as p) {
      <p class="text-xs text-ink/55">{{ site.t('price_from') }}</p>
      <p class="flex flex-wrap items-baseline gap-x-2">
        <span class="font-display font-bold text-saffron-700" [class]="large() ? 'text-3xl' : 'text-xl'">{{ site.money(p.now) }}</span>
        @if (p.was) {
          <span class="text-sm text-ink/45 line-through">{{ site.money(p.was) }}</span>
        }
      </p>
      <p class="text-xs text-ink/55">{{ site.t('price_per_person') }}</p>
    } @else {
      <p class="font-display font-semibold text-navy-800" [class]="large() ? 'text-2xl' : 'text-base'">{{ site.t('price_on_request') }}</p>
    }
  `,
})
export class Price {
  protected readonly site = inject(Site);
  readonly package = input.required<Pick<Package, 'prices'>>();
  readonly large = input(false);
  protected readonly price = computed(() => this.package().prices?.[this.site.currency()] ?? null);
}

/** A tour package: photo, the places it visits, its length and the price. */
@Component({
  selector: 'app-package-card',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Media, Price],
  host: { class: 'block h-full' },
  template: `
    @let p = package();
    <article class="group flex h-full overflow-hidden rounded-xl border border-navy-100 bg-white transition-shadow duration-300 hover:shadow-lift"
             [class]="list() ? 'flex-col sm:flex-row' : 'flex-col'">
      <a [routerLink]="p.url" class="relative block shrink-0 overflow-hidden" tabindex="-1" aria-hidden="true"
         [class]="list() ? 'aspect-[4/3] sm:aspect-auto sm:w-[38%] sm:min-h-60' : 'aspect-[4/3]'">
        <app-media [image]="p.image" [alt]="p.name" class="absolute inset-0" imgClass="group-hover:scale-105" />
        @if (offer(); as off) {
          <span class="absolute top-4 left-0 rounded-r-md bg-saffron-600 px-3 py-1 text-xs font-semibold text-white">{{ site.t('price_off', { percent: off }) }}</span>
        }
      </a>

      <div class="flex flex-1 flex-col p-5">
        @if (placeLine()) {
          <p class="line-clamp-1 text-xs font-medium tracking-wide text-saffron-700 uppercase">{{ placeLine() }}</p>
        }
        <h3 class="mt-1.5 line-clamp-2 text-lg leading-snug font-semibold">
          <a [routerLink]="p.url" class="transition-colors hover:text-saffron-600">{{ p.name }}</a>
        </h3>
        @if (list() && p.summary) {
          <p class="mt-2 line-clamp-3 text-sm leading-relaxed">{{ p.summary }}</p>
        }
        <p class="mt-2 mb-5 text-sm text-ink/60">{{ facts() }}</p>

        <div class="mt-auto flex items-end justify-between gap-3 border-t border-navy-100 pt-4">
          <app-price [package]="p" />
          <a [routerLink]="p.url" class="btn btn-outline-primary btn-sm shrink-0">
            {{ !p.prices && site.canBook() ? site.t('card_enquire') : site.t('card_view') }}
          </a>
        </div>
      </div>
    </article>
  `,
})
export class PackageCard {
  protected readonly site = inject(Site);
  readonly package = input.required<Package>();
  readonly list = input(false);

  protected readonly placeLine = computed(() => {
    const p = this.package();
    const places = p.places;
    return places.length ? places.slice(0, 3).join(', ') + (places.length > 3 ? ' +' + (places.length - 3) : '') : p.start_point;
  });
  /** "5 Nights / 6 Days · Chardham Yatra · 3,710 m" */
  protected readonly facts = computed(() => {
    const p = this.package();
    return [p.duration, p.category?.name, p.max_altitude_m ? p.max_altitude_m.toLocaleString('en-IN') + ' m' : null]
      .filter(Boolean)
      .join(' · ');
  });
  protected readonly offer = computed(() => {
    const price = this.package().prices?.[this.site.currency()];
    return price && price.off > 0 ? price.off : 0;
  });
}

/** A destination as a photo with its name over the bottom of the photo. */
@Component({
  selector: 'app-destination-card',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Media, DecimalPipe],
  host: { class: 'block h-full' },
  template: `
    @let d = destination();
    <a [routerLink]="d.url" class="group relative isolate block overflow-hidden rounded-xl bg-navy-900" [class]="fill() ? 'size-full' : 'aspect-[4/5] min-h-64'">
      <app-media [image]="d.image" [alt]="d.name" class="absolute inset-0 -z-20" imgClass="group-hover:scale-105" />
      <span class="absolute inset-0 -z-10 bg-linear-to-t from-navy-950/85 via-navy-950/10 to-transparent" aria-hidden="true"></span>

      <span class="absolute inset-x-0 bottom-0 block p-5 sm:p-6">
        <span class="block text-xs font-medium tracking-wide text-white/75 uppercase">{{ d.country }}@if (d.altitude_m) { · {{ d.altitude_m | number }} m }</span>
        <span class="mt-1 block font-display leading-snug font-semibold text-white" [class]="tall() ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl'">{{ d.name }}</span>
        @if (d.tours) {
          <span class="mt-2 inline-block rounded bg-white/15 px-2 py-0.5 text-xs font-medium text-white backdrop-blur">{{ site.t(d.tours === 1 ? 'card_tours_one' : 'card_tours_many', { count: d.tours }) }}</span>
        }
      </span>
    </a>
  `,
})
export class DestinationCard {
  protected readonly site = inject(Site);
  readonly destination = input.required<Destination>();
  readonly tall = input(false);
  /** Fill a grid cell of fixed height (homepage) instead of keeping the 4:5 shape. */
  readonly fill = input(false);
}
