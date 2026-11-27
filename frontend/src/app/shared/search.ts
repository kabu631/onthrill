import { HttpClient } from '@angular/common/http';
import {
  ChangeDetectionStrategy,
  Component,
  computed,
  DestroyRef,
  ElementRef,
  inject,
  Injectable,
  input,
  model,
  output,
  Pipe,
  PipeTransform,
  signal,
} from '@angular/core';
import { Router } from '@angular/router';
import { debounceTime, distinctUntilChanged, map, Observable, of, Subject, switchMap, tap } from 'rxjs';
import { apiUrl } from '../core/api';
import { Site } from '../core/site';
import { Currency, PriceSet } from '../core/types';

export interface Suggestion {
  kind: 'destination' | 'package' | 'type';
  name: string;
  slug: string;
  url: string;
  meta: string;
  image?: string | null;
  prices?: Record<Currency, PriceSet> | null;
}

interface QuickResults {
  destinations: { name: string; slug: string; meta: string; url: string; image: string | null; tours: number }[];
  packages: { name: string; slug: string; meta: string; url: string; image: string | null; prices: Record<Currency, PriceSet> | null }[];
  types: { name: string; slug: string; url: string; tours: number }[];
}

export interface SuggestionGroup {
  key: 'destination' | 'type' | 'package';
  title: string;
  items: Suggestion[];
}

/** Suggestions while typing, shared by every search box (answers are kept for the visit). */
@Injectable({ providedIn: 'root' })
export class SearchSuggestions {
  private readonly http = inject(HttpClient);
  private readonly site = inject(Site);
  private readonly cache = new Map<string, SuggestionGroup[]>();

  find(term: string): Observable<SuggestionGroup[]> {
    const key = term.trim().toLowerCase().replace(/\s+/g, ' ');
    if (key.length < 2) return of([]);
    const cached = this.cache.get(key);
    if (cached) return of(cached);

    return this.http.get<QuickResults>(apiUrl('/api/search', { limit: 5, q: key })).pipe(
      map((r) => {
        const tours = (n: number) => this.site.t(n === 1 ? 'card_tours_one' : 'card_tours_many', { count: n });
        const groups: SuggestionGroup[] = [
          {
            key: 'destination' as const,
            title: this.site.t('search_destinations'),
            items: r.destinations.map((d) => ({ kind: 'destination' as const, name: d.name, slug: d.slug, url: d.url, image: d.image, meta: d.meta + (d.tours ? ' · ' + tours(d.tours) : '') })),
          },
          {
            key: 'type' as const,
            title: this.site.t('search_types'),
            items: r.types.map((t) => ({ kind: 'type' as const, name: t.name, slug: t.slug, url: t.url, meta: tours(t.tours) })),
          },
          {
            key: 'package' as const,
            title: this.site.t('search_packages'),
            items: r.packages.map((p) => ({ kind: 'package' as const, name: p.name, slug: p.slug, url: p.url, image: p.image, meta: p.meta, prices: p.prices })),
          },
        ].filter((g) => g.items.length);
        return groups;
      }),
      tap((groups) => this.cache.set(key, groups)),
    );
  }

  /** Before typing: a few places and the tour types from the menu. */
  popular(): SuggestionGroup[] {
    const menu = this.site.menu();
    const places = menu.countries.flatMap((c) => c.children.slice(0, 3).map((d) => ({ ...d, country: c.name }))).slice(0, 6);
    return [
      {
        key: 'destination',
        title: this.site.t('search_popular'),
        items: places.map((d) => ({ kind: 'destination', name: d.name, slug: d.url.split('/').pop() ?? '', url: d.url, meta: d.country })),
      },
      {
        key: 'type',
        title: this.site.t('search_types'),
        items: menu.tour_types.map((t) => ({ kind: 'type', name: t.name, slug: t.url.split('/').pop() ?? '', url: t.url, meta: '' })),
      },
    ].filter((g) => g.items.length) as SuggestionGroup[];
  }
}

/** The words of the search in bold within a suggestion (text escaped first). */
@Pipe({ name: 'highlight' })
export class Highlight implements PipeTransform {
  transform(text: string, term: string): string {
    const safe = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const words = term.trim().split(/\s+/).filter((w) => w.length >= 2).map((w) => w.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    if (!words.length) return safe;
    return safe.replace(new RegExp(`(${words.join('|')})`, 'gi'), '<mark class="bg-transparent font-semibold text-saffron-700">$1</mark>');
  }
}

/**
 * "Where to?" box: suggests destinations, tour types and tours while typing.
 * Choosing a destination or tour type fills the box (the search form then
 * filters by it); choosing a tour opens it. Arrow keys and Enter work too.
 */
@Component({
  selector: 'app-where-input',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Highlight],
  host: { class: 'relative block', '(document:click)': 'outside($event)' },
  template: `
    <input #box type="search" autocomplete="off" maxlength="100" role="combobox" [id]="inputId()"
           [attr.aria-expanded]="open()" [attr.aria-controls]="inputId() + '-list'" [attr.aria-activedescendant]="active() >= 0 ? inputId() + '-opt' + active() : null"
           [placeholder]="placeholder()" [value]="value()" (input)="type(box.value)" (focus)="show()" (keydown)="key($event)"
           [class]="inputClass()">

    @if (open() && groups().length) {
      <div [id]="inputId() + '-list'" role="listbox" class="animate-fade absolute inset-x-0 top-full z-50 mt-2 max-h-[26rem] min-w-72 overflow-y-auto rounded-xl border border-navy-100 bg-white py-2 text-left shadow-lift">
        @for (group of groups(); track group.key) {
          <p class="px-4 pt-2 pb-1 text-[11px] font-semibold tracking-wider text-navy-400 uppercase">{{ group.title }}</p>
          @for (item of group.items; track item.url) {
            @let i = flat().indexOf(item);
            <button type="button" role="option" [id]="inputId() + '-opt' + i" [attr.aria-selected]="active() === i"
                    class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm" [class]="active() === i ? 'bg-saffron-50' : 'hover:bg-mist'"
                    (mouseenter)="active.set(i)" (click)="choose(item)">
              @if (item.image) {
                <img [src]="item.image" alt="" class="size-9 shrink-0 rounded-md object-cover">
              }
              <span class="min-w-0 flex-1">
                <span class="block truncate text-ink" [innerHTML]="item.name | highlight: value()"></span>
                @if (item.meta) { <span class="block truncate text-xs text-ink/50">{{ item.meta }}</span> }
              </span>
              @if (item.prices) {
                <span class="shrink-0 text-xs font-semibold text-saffron-700">{{ site.money(item.prices[site.currency()].now) }}</span>
              }
            </button>
          }
        }
      </div>
    } @else if (open() && searched() && value().trim().length >= 2) {
      <div class="absolute inset-x-0 top-full z-50 mt-2 min-w-72 rounded-xl border border-navy-100 bg-white px-4 py-3 text-left text-sm text-ink/70 shadow-lift">
        {{ site.t('search_none_title', { term: value().trim() }) }}
      </div>
    }
  `,
})
export class WhereInput {
  protected readonly site = inject(Site);
  private readonly suggestions = inject(SearchSuggestions);
  private readonly router = inject(Router);
  private readonly host = inject(ElementRef<HTMLElement>);

  readonly value = model('');
  readonly placeholder = input('');
  readonly inputId = input('where');
  readonly inputClass = input('w-full bg-transparent focus:outline-none');
  /** A destination or tour type was chosen (null when the visitor types something else). */
  readonly picked = output<Suggestion | null>();
  /** Enter with nothing highlighted: search for the words. */
  readonly submitted = output<void>();

  protected readonly open = signal(false);
  protected readonly active = signal(-1);
  protected readonly searched = signal(false);
  private readonly found = signal<SuggestionGroup[]>([]);
  private readonly terms = new Subject<string>();

  protected readonly groups = computed(() => (this.value().trim().length >= 2 ? this.found() : this.suggestions.popular()));
  protected readonly flat = computed(() => this.groups().flatMap((g) => g.items));

  constructor() {
    const sub = this.terms
      .pipe(debounceTime(200), distinctUntilChanged(), switchMap((t) => this.suggestions.find(t)))
      .subscribe((groups) => {
        this.found.set(groups);
        this.searched.set(true);
        this.active.set(-1);
      });
    inject(DestroyRef).onDestroy(() => sub.unsubscribe());
  }

  protected show(): void {
    this.open.set(true);
  }

  protected type(text: string): void {
    this.value.set(text);
    this.picked.emit(null);
    this.open.set(true);
    this.active.set(-1);
    if (text.trim().length < 2) {
      this.found.set([]);
      this.searched.set(false);
    }
    this.terms.next(text.trim());
  }

  protected key(event: KeyboardEvent): void {
    const count = this.flat().length;
    if (event.key === 'ArrowDown' && count) {
      event.preventDefault();
      this.open.set(true);
      this.active.update((i) => (i + 1) % count);
    } else if (event.key === 'ArrowUp' && count) {
      event.preventDefault();
      this.active.update((i) => (i <= 0 ? count - 1 : i - 1));
    } else if (event.key === 'Enter') {
      event.preventDefault();
      const item = this.flat()[this.active()];
      if (this.open() && item) {
        this.choose(item);
      } else {
        this.open.set(false);
        this.submitted.emit();
      }
    } else if (event.key === 'Escape') {
      this.open.set(false);
    }
  }

  protected choose(item: Suggestion): void {
    this.open.set(false);
    if (item.kind === 'package') {
      this.router.navigateByUrl(item.url);
      return;
    }
    this.value.set(item.name);
    this.picked.emit(item);
  }

  protected outside(event: Event): void {
    if (!this.host.nativeElement.contains(event.target as Node)) this.open.set(false);
  }
}
