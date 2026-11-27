import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, effect, inject, input, linkedSignal, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { apiUrl, errorStatus } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { Category, PackageCard as Package, Paginated } from '../core/types';
import { PackageCard } from '../shared/cards';
import { Suggestion, WhereInput } from '../shared/search';
import { Icon } from '../shared/icon';
import { Crumb, PageBanner, Pagination, Reveal } from '../shared/ui';
import { NotFoundPage } from './not-found';

interface PackagesData {
  category: (Category & { description: string | null }) | null;
  destination: { name: string; slug: string } | null;
  duration: string | null;
  budget: string | null;
  q: string;
  sort: string;
  categories: (Category & { count: number })[];
  destinations: { country: string; options: { name: string; slug: string }[] }[];
  packages: Paginated<Package>;
}

const VIEW_KEY = 'onthrill.packages.view';

/** /packages and /packages/type/:category, filtered by ?destination=, ?duration= and sorted by ?sort=. */
@Component({
  selector: 'app-packages',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, FormsModule, Icon, PageBanner, Reveal, PackageCard, Pagination, NotFoundPage, WhereInput],
  template: `
    @if (errorStatus(data) === 404) {
      <app-not-found />
    } @else {
      <app-page-banner [title]="title()" [intro]="intro()" [crumbs]="crumbs()" />

      <div class="container-page section pt-10 sm:pt-14">
        <div class="grid gap-8 lg:grid-cols-[18rem_1fr] lg:gap-10">
          <!-- Filters: a panel that opens on phones, a sidebar on large screens -->
          <aside [attr.aria-label]="site.t('list_filter_button')" class="min-w-0">
            <button type="button" class="btn btn-outline w-full justify-between lg:hidden" (click)="filtersOpen.set(!filtersOpen())" [attr.aria-expanded]="filtersOpen()">
              <span class="flex items-center gap-2"><svg icon="filters" class="size-4"></svg> {{ site.t('list_filter_button') }}</span>
              <svg icon="chevron-down" class="size-4 transition-transform" [class.rotate-180]="filtersOpen()"></svg>
            </button>

            <div class="mt-3 space-y-5 lg:sticky lg:top-28 lg:mt-0 lg:block" [class.hidden]="!filtersOpen()">
              <div class="card p-5">
                <h2 class="text-lg font-semibold">{{ site.t('list_type_title') }}</h2>
                <ul class="mt-3 space-y-0.5 text-sm">
                  <li>
                    <a routerLink="/packages" [queryParams]="keep()" [class]="typeLink(!category())">
                      {{ site.t('list_all_types') }} <span class="text-xs font-normal opacity-60">{{ totalCount() }}</span>
                    </a>
                  </li>
                  @for (c of current()?.categories ?? []; track c.slug) {
                    <li>
                      <a [routerLink]="c.url" [queryParams]="keep()" [class]="typeLink(category() === c.slug)">
                        {{ c.name }} <span class="text-xs font-normal opacity-60">{{ c.count }}</span>
                      </a>
                    </li>
                  }
                </ul>
              </div>

              <div class="card space-y-6 p-5">
                <form role="search" (submit)="$event.preventDefault(); filter({ q: words.trim() || null })">
                  <label for="filter-words" class="text-lg font-semibold text-navy-900">{{ site.t('list_search_title') }}</label>
                  <div class="relative mt-3">
                    <app-where-input inputId="filter-words" [(value)]="wordsModel" [placeholder]="site.t('list_search_placeholder')" (picked)="picked($event)" (submitted)="filter({ q: words.trim() || null })"
                                     inputClass="input pr-11" />
                    <button type="submit" class="absolute top-1/2 right-1.5 grid size-8 -translate-y-1/2 place-items-center rounded-md text-ink/50 hover:bg-mist hover:text-saffron-700" [attr.aria-label]="site.t('search_page_button')">
                      <svg icon="search" class="size-4"></svg>
                    </button>
                  </div>
                </form>
                <div>
                  <label for="filter-destination" class="text-lg font-semibold text-navy-900">{{ site.t('list_destination_heading') }}</label>
                  <select id="filter-destination" class="input mt-3" [ngModel]="destination() ?? ''" (ngModelChange)="filter({ destination: $event || null })">
                    <option value="">{{ site.t('list_anywhere') }}</option>
                    @for (group of current()?.destinations ?? []; track group.country) {
                      <optgroup [label]="group.country">
                        @for (o of group.options; track o.slug) { <option [value]="o.slug">{{ o.name }}</option> }
                      </optgroup>
                    }
                  </select>
                </div>
                <fieldset>
                  <legend class="text-lg font-semibold text-navy-900">{{ site.t('list_length_title') }}</legend>
                  <div class="mt-3 space-y-1 text-sm">
                    @for (d of durationOptions(); track d.value) {
                      <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-mist hover:text-saffron-700">
                        <input type="radio" name="duration" [value]="d.value" [checked]="(duration() ?? '') === d.value" (change)="filter({ duration: d.value || null })" class="size-4 accent-saffron-600">
                        {{ d.label }}
                      </label>
                    }
                  </div>
                </fieldset>
                <fieldset>
                  <legend class="text-lg font-semibold text-navy-900">{{ site.t('list_budget_title') }}</legend>
                  <div class="mt-3 space-y-1 text-sm">
                    @for (b of budgetOptions(); track b.value) {
                      <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-mist hover:text-saffron-700">
                        <input type="radio" name="budget" [value]="b.value" [checked]="(budget() ?? '') === b.value" (change)="filter({ budget: b.value || null })" class="size-4 accent-saffron-600">
                        {{ b.label }}
                      </label>
                    }
                  </div>
                </fieldset>
                @if (filtered()) {
                  <button type="button" (click)="clearAll()" class="block w-full text-center text-sm font-medium text-saffron-700 underline underline-offset-2">{{ site.t('list_clear') }}</button>
                }
              </div>

              <!-- Help for anyone who has not found the right tour -->
              <div class="rounded-xl bg-navy-900 p-6 text-sm text-white/80">
                <p class="font-display text-lg font-semibold text-white">{{ site.t('list_help_title') }}</p>
                <p class="mt-1.5">{{ site.t('list_help_text') }}</p>
                <a routerLink="/contact-us" class="btn btn-primary btn-sm mt-4">{{ site.t('list_help_button') }}</a>
              </div>
            </div>
          </aside>

          <div class="min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-navy-100 bg-white px-4 py-3">
              <div class="flex flex-wrap items-center gap-2 text-sm">
                @if (current(); as d) {
                  <span>{{ site.t(d.packages.meta.total === 1 ? 'list_found_one' : 'list_found_many', { count: d.packages.meta.total }) }}</span>
                  @if (d.destination) {
                    <button type="button" (click)="filter({ destination: null })" class="inline-flex items-center gap-1 rounded-full bg-saffron-50 px-3 py-1 text-xs font-medium text-saffron-800 hover:bg-saffron-100">
                      {{ d.destination.name }} <svg icon="x" class="size-3.5"></svg><span class="sr-only">{{ site.t('list_clear') }}</span>
                    </button>
                  }
                  @if (d.q) {
                    <button type="button" (click)="filter({ q: null })" class="inline-flex items-center gap-1 rounded-full bg-saffron-50 px-3 py-1 text-xs font-medium text-saffron-800 hover:bg-saffron-100">
                      “{{ d.q }}” <svg icon="x" class="size-3.5"></svg><span class="sr-only">{{ site.t('list_clear') }}</span>
                    </button>
                  }
                  @if (d.budget && budgetLabel(d.budget); as label) {
                    <button type="button" (click)="filter({ budget: null })" class="inline-flex items-center gap-1 rounded-full bg-saffron-50 px-3 py-1 text-xs font-medium text-saffron-800 hover:bg-saffron-100">
                      {{ label }} <svg icon="x" class="size-3.5"></svg><span class="sr-only">{{ site.t('list_clear') }}</span>
                    </button>
                  }
                  @if (d.duration) {
                    <button type="button" (click)="filter({ duration: null })" class="inline-flex items-center gap-1 rounded-full bg-saffron-50 px-3 py-1 text-xs font-medium text-saffron-800 hover:bg-saffron-100">
                      {{ site.t('duration_' + d.duration) }} <svg icon="x" class="size-3.5"></svg><span class="sr-only">{{ site.t('list_clear') }}</span>
                    </button>
                  }
                }
              </div>
              <div class="flex items-center gap-4">
                <div class="hidden items-center gap-1 sm:flex" role="group" [attr.aria-label]="site.t('list_view_label')">
                  @for (v of views; track v.value) {
                    <button type="button" class="grid size-10 place-items-center rounded-full border transition-colors" [class]="view() === v.value ? 'border-navy-900 bg-navy-900 text-white' : 'border-navy-200 text-navy-800 hover:border-navy-900'"
                            (click)="setView(v.value)" [attr.aria-pressed]="view() === v.value" [title]="site.t(v.label)">
                      <svg [icon]="v.icon" class="size-5"></svg><span class="sr-only">{{ site.t(v.label) }}</span>
                    </button>
                  }
                </div>
                <label class="flex items-center gap-2">
                  <span class="text-sm whitespace-nowrap">{{ site.t('list_sort') }}</span>
                  <select class="input w-auto rounded-full py-2" [ngModel]="sort() ?? 'recommended'" (ngModelChange)="filter({ sort: $event === 'recommended' ? null : $event })">
                    @for (s of sorts; track s) { <option [value]="s">{{ site.t('sort_' + s) }}</option> }
                  </select>
                </label>
              </div>
            </div>

            @if (current(); as d) {
              @if (d.packages.data.length) {
                <div class="mt-6 grid gap-6" [class]="view() === 'grid' ? 'sm:grid-cols-2 xl:grid-cols-3' : ''" [class.opacity-60]="data.isLoading()">
                  @for (p of d.packages.data; track p.id; let i = $index) {
                    <app-package-card appReveal [revealDelay]="(i % 3) * 60" [package]="p" [list]="view() === 'list'" />
                  }
                </div>
                <app-pagination [current]="d.packages.meta.current_page" [last]="d.packages.meta.last_page" (go)="page($event)" />
              } @else {
                <div class="card mt-6 p-10 text-center">
                  <p class="font-display text-xl font-semibold text-navy-900">{{ site.t('list_empty_title') }}</p>
                  <p class="mt-2 text-sm">{{ site.t('list_empty_text') }}</p>
                  <div class="mt-6 flex flex-wrap justify-center gap-3">
                    @if (filtered()) { <button type="button" (click)="clearAll()" class="btn btn-outline">{{ site.t('list_clear') }}</button> }
                    <a routerLink="/contact-us" class="btn btn-primary">{{ site.t('list_empty_button') }}</a>
                  </div>
                </div>
              }
            } @else {
              <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3" aria-busy="true">
                @for (i of [1, 2, 3, 4, 5, 6]; track i) { <div class="skeleton h-96"></div> }
              </div>
            }
          </div>
        </div>
      </div>
    }
  `,
})
export class PackagesPage {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);

  // From the address: /packages/type/:category?destination=&duration=&sort=&page=
  readonly category = input<string>();
  readonly destination = input<string>();
  readonly duration = input<string>();
  readonly budget = input<string>();
  readonly q = input<string>();
  readonly sort = input<string>();
  readonly page_ = input<string | undefined>(undefined, { alias: 'page' });

  protected readonly data = httpResource<PackagesData>(() =>
    apiUrl('/api/packages', {
      destination: this.destination(),
      budget: this.budget(),
      duration: this.duration(),
      q: this.q(),
      page: Number(this.page_()) > 1 ? this.page_() : null,
      sort: this.sort(),
      type: this.category(),
    }),
  );
  /** The last results stay on screen (dimmed) while the next ones load. */
  protected readonly current = linkedSignal<PackagesData | undefined, PackagesData | undefined>({
    source: () => this.data.value(),
    computation: (value, previous) => value ?? previous?.value,
  });
  protected readonly errorStatus = errorStatus;

  protected readonly sorts = ['recommended', 'duration', 'price_low', 'price_high'];
  protected readonly views = [
    { value: 'list', icon: 'view-list', label: 'list_view_list' },
    { value: 'grid', icon: 'view-grid', label: 'list_view_grid' },
  ] as const;
  protected readonly view = signal<'list' | 'grid'>(readView());
  protected readonly filtersOpen = signal(false);

  protected readonly filtered = computed(() => !!(this.destination() || this.duration() || this.budget() || this.q()));
  /** Filters other than the tour type, kept when switching tour type. */
  protected readonly keep = computed(() => ({
    destination: this.destination() || null,
    duration: this.duration() || null,
    budget: this.budget() || null,
    q: this.q() || null,
    sort: this.sort() || null,
  }));
  /** The words in the search box, kept in step with the address. */
  protected readonly wordsModel = linkedSignal(() => this.q() ?? '');
  protected get words(): string {
    return this.wordsModel();
  }
  protected readonly budgetOptions = computed(() => [
    { value: '', label: this.site.t('list_any_budget') },
    ...this.site.setting('budgets').map((b) => ({ value: b.value, label: b[this.site.currency()] })),
  ]);
  protected readonly totalCount = computed(() => (this.current()?.categories ?? []).reduce((sum, c) => sum + c.count, 0));
  protected readonly durationOptions = computed(() => [
    { value: '', label: this.site.t('list_any_length') },
    ...['short', 'medium', 'long'].map((value) => ({ value, label: this.site.t('duration_' + value) })),
  ]);

  protected readonly title = computed(() => {
    const d = this.current();
    return d?.category?.name ?? (d?.destination ? this.site.t('list_destination_title', { name: d.destination.name }) : this.site.t('packages_title'));
  });
  protected readonly intro = computed(() => {
    const d = this.current();
    return d?.category?.description || (d?.destination ? this.site.t('list_destination_intro', { name: d.destination.name }) : this.site.t('packages_intro'));
  });
  protected readonly crumbs = computed<Crumb[]>(() => {
    const d = this.current();
    const all = this.site.t('packages_title');
    if (d?.category) return [{ label: all, url: '/packages' }, { label: d.category.name }];
    if (d?.destination) return [{ label: all, url: '/packages' }, { label: d.destination.name }];
    return [{ label: all }];
  });

  constructor() {
    const seo = inject(Seo);
    effect(() => seo.set({ title: this.title(), description: this.intro(), noindex: this.filtered() }));
  }

  protected budgetLabel(value: string): string {
    return this.budgetOptions().find((b) => b.value === value)?.label ?? '';
  }

  /** A destination from the suggestions filters by it; a tour type opens its page. */
  protected picked(item: Suggestion | null): void {
    if (item?.kind === 'destination') {
      this.wordsModel.set('');
      this.filter({ destination: item.slug, q: null });
    } else if (item?.kind === 'type') {
      this.wordsModel.set('');
      this.router.navigate([item.url], { queryParams: { ...this.keep(), q: null } });
    }
  }

  protected clearAll(): void {
    this.wordsModel.set('');
    this.filter({ destination: null, duration: null, budget: null, q: null });
  }

  protected filter(change: Record<string, string | null>): void {
    this.router.navigate([], { queryParams: { ...change, page: null }, queryParamsHandling: 'merge' });
  }

  protected page(n: number): void {
    this.router.navigate([], { queryParams: { page: n > 1 ? n : null }, queryParamsHandling: 'merge' });
    scrollTo({ top: 0, behavior: 'smooth' });
  }

  protected setView(view: 'list' | 'grid'): void {
    this.view.set(view);
    try {
      localStorage.setItem(VIEW_KEY, view);
    } catch {
      // Private windows may refuse; the choice simply is not remembered.
    }
  }

  protected typeLink(active: boolean): string {
    return (
      'flex items-center justify-between gap-3 rounded-lg px-3 py-2 ' +
      (active ? 'bg-saffron-50 font-semibold text-saffron-700' : 'hover:bg-mist')
    );
  }
}

function readView(): 'list' | 'grid' {
  try {
    return localStorage.getItem(VIEW_KEY) === 'list' ? 'list' : 'grid';
  } catch {
    return 'grid';
  }
}
