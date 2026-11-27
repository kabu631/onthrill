import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, DestroyRef, effect, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { Seo } from '../../core/seo';
import { Site } from '../../core/site';
import { HomeData } from '../../core/types';
import { DestinationCard, PackageCard } from '../../shared/cards';
import { Icon } from '../../shared/icon';
import { Lightbox, LightboxItem } from '../../shared/photos';
import { Suggestion, WhereInput } from '../../shared/search';
import { FaqList, Media, Reveal, SectionHeading, Stars } from '../../shared/ui';

@Component({
  selector: 'app-home',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, FormsModule, Icon, Media, Reveal, SectionHeading, Stars, FaqList, PackageCard, DestinationCard, Lightbox, WhereInput],
  templateUrl: './home.html',
})
export class HomePage {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);

  protected readonly data = httpResource<HomeData>(() => '/api/home');
  protected readonly s = this.site.settings;

  /** Sections chosen in Admin > Page Content > Homepage, in the site's order. */
  protected readonly show = computed(() => {
    const chosen = new Set(this.s().home_sections);
    return (key: string) => chosen.has(key);
  });

  // Slideshow
  protected readonly slides = computed(() => this.s().hero_slides);
  /** Each slide's title completes the heading; without titles, the words rotate instead. */
  protected readonly lines = computed(() => {
    const slides = this.slides();
    const titled = slides.length > 0 && slides.every((s) => !!s.title);
    return titled ? slides.map((s) => s.title!) : this.s().hero_words;
  });
  protected readonly slide = signal(0);
  protected readonly line = signal(0);

  // Tour search
  protected search = { type: '', duration: '', budget: '' };
  /** What is typed in "Where to?", and the destination chosen from its suggestions. */
  protected readonly where = signal('');
  private destination = '';
  protected readonly durations = ['short', 'medium', 'long'];
  protected readonly typeSlug = (url: string) => url.split('/').pop() ?? '';

  // Packages section
  protected readonly tab = signal('all');
  protected readonly packageTypes = computed(() => {
    const seen = new Map<string, string>();
    for (const p of this.data.value()?.packages ?? []) {
      if (p.category && !seen.has(p.category.slug)) seen.set(p.category.slug, p.category.name);
    }
    return [...seen].map(([slug, name]) => ({ slug, name }));
  });
  protected readonly shownPackages = computed(() =>
    (this.data.value()?.packages ?? []).filter((p) => this.tab() === 'all' || p.category?.slug === this.tab()),
  );

  /** The "see all" tile fills the rest of the last row of destination cards. */
  protected readonly tileClass = computed(() => {
    const singles = Math.max(0, (this.data.value()?.destinations.length ?? 1) - 1);
    const lg = 4 - ((4 + singles) % 4);
    const sm = singles % 2 === 0 ? 'col-span-2' : '';
    return `${sm} ${['', 'lg:col-span-1', 'lg:col-span-2', 'lg:col-span-3', 'lg:col-span-4'][lg]}`;
  });

  protected readonly facts = computed(() => {
    const d = this.data.value();
    if (!d) return [];
    const countries = d.countries;
    const list = countries.length > 1 ? countries.slice(0, -1).join(', ') + ' and ' + countries[countries.length - 1] : countries[0] ?? '';
    return [
      d.destination_count ? { icon: 'map-pin', value: d.destination_count, label: this.site.t('fact_places') } : null,
      d.package_count ? { icon: 'map', value: d.package_count, label: this.site.t('fact_packages') } : null,
      countries.length ? { icon: 'globe', value: countries.length, label: this.site.t('fact_countries', { list }) } : null,
      { icon: 'banknotes', value: this.site.t('fact_currency_value'), label: this.site.t('fact_currency') },
    ].filter((f) => f !== null);
  });

  protected readonly searchGroups = computed(() =>
    Object.entries(this.data.value()?.search ?? {}).map(([country, options]) => ({ country, options })),
  );

  protected readonly stepCols = computed(
    () => ({ 1: 'lg:grid-cols-1', 2: 'lg:grid-cols-2', 3: 'lg:grid-cols-3' })[this.s().how_steps.length] ?? 'lg:grid-cols-4',
  );

  protected readonly whyIcons = ['banknotes', 'shield', 'users', 'phone', 'map', 'sun'];

  protected readonly galleryItems = computed<LightboxItem[]>(() =>
    (this.data.value()?.gallery ?? []).map((g) => ({ src: g.large, title: g.title, credit: g.credit, credit_url: g.credit_url })),
  );


  constructor() {
    inject(Seo).set({});

    // Next photo every 6 seconds; the words of the heading change with it.
    const timer = setInterval(() => {
      if (document.hidden) return;
      this.go(1);
    }, 6000);
    inject(DestroyRef).onDestroy(() => clearInterval(timer));

    effect(() => {
      // Keep the indexes in range if the admin removes slides.
      if (this.slide() >= Math.max(1, this.slides().length)) this.slide.set(0);
    });
  }

  protected go(step: number): void {
    const wrap = (i: number, n: number) => (n ? (i + step + n) % n : 0);
    this.slide.update((i) => wrap(i, this.slides().length));
    this.line.update((i) => wrap(i, this.lines().length));
  }

  protected pad(n: number): string {
    return String(n).padStart(2, '0');
  }

  /** A destination fills "Where to?"; a tour type sets the type list. */
  protected picked(item: Suggestion | null): void {
    this.destination = item?.kind === 'destination' ? item.slug : '';
    if (item?.kind === 'type') {
      this.search.type = item.slug;
      this.where.set('');
    }
  }

  /** To the matching packages: a chosen destination, or else the words typed, plus the other choices. */
  protected findTours(): void {
    const { type, duration, budget } = this.search;
    const words = this.destination ? '' : this.where().trim();
    const queryParams = { destination: this.destination || null, q: words || null, duration: duration || null, budget: budget || null };
    this.router.navigate(type ? ['/packages/type', type] : ['/packages'], { queryParams });
  }

  protected tabClass(active: boolean): string {
    return (
      'shrink-0 rounded-full border px-5 py-2.5 text-sm font-medium whitespace-nowrap transition-colors ' +
      (active ? 'border-saffron-600 bg-saffron-600 text-white' : 'border-navy-200 bg-white text-navy-800 hover:border-saffron-500 hover:text-saffron-700')
    );
  }
}
