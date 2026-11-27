import {
  afterNextRender,
  ChangeDetectionStrategy,
  Component,
  computed,
  DestroyRef,
  Directive,
  ElementRef,
  inject,
  input,
  linkedSignal,
  output,
  signal,
} from '@angular/core';
import { RouterLink } from '@angular/router';
import { Site } from '../core/site';
import { Faq, Img } from '../core/types';
import { Icon } from './icon';

/**
 * Fades an element in and lifts it a little when it scrolls into view.
 * `appReveal` on a list item, with `[revealDelay]` to stagger a row.
 */
@Directive({ selector: '[appReveal]', host: { class: 'reveal', '[style.--reveal-delay.ms]': 'revealDelay()' } })
export class Reveal {
  readonly revealDelay = input(0);

  constructor() {
    const el = inject(ElementRef<HTMLElement>).nativeElement as HTMLElement;
    const destroy = inject(DestroyRef);
    afterNextRender(() => {
      if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.classList.add('is-visible');
        return;
      }
      const observer = new IntersectionObserver(
        (entries) => {
          if (entries.some((e) => e.isIntersecting)) {
            el.classList.add('is-visible');
            observer.disconnect();
          }
        },
        { rootMargin: '0px 0px -8% 0px' },
      );
      observer.observe(el);
      destroy.onDestroy(() => observer.disconnect());
    });
  }
}

/** A photograph at the size it is shown, or the site's mountain mark when there is none yet. */
@Component({
  selector: 'app-media',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'block overflow-hidden bg-mist' },
  template: `
    @if (image(); as img) {
      <img [src]="img.src" [attr.srcset]="img.srcset" [attr.sizes]="sizes()" [alt]="alt()"
           [attr.loading]="eager() ? null : 'lazy'" [attr.fetchpriority]="eager() ? 'high' : null" decoding="async"
           class="size-full object-cover transition-transform duration-700" [class]="imgClass()">
    } @else {
      <div class="grid size-full place-items-center" role="img" [attr.aria-label]="alt()">
        <img src="/favicon.svg" alt="" class="w-1/4 max-w-20 min-w-8 opacity-30">
      </div>
    }
  `,
})
export class Media {
  readonly image = input<Img | null | undefined>(null);
  readonly alt = input('');
  readonly eager = input(false);
  readonly sizes = input<string | null>(null);
  readonly imgClass = input('');
}

/** Centred (or left-aligned) section title with the small saffron label above it. */
@Component({
  selector: 'app-section-heading',
  changeDetection: ChangeDetectionStrategy.OnPush,
  host: { class: 'block' },
  template: `
    <div appReveal [class]="align() === 'center' ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl'">
      @if (eyebrow()) {
        <p class="eyebrow" [class]="light() ? 'text-saffron-400!' : ''">{{ eyebrow() }}</p>
      }
      <h2 class="mt-1 text-3xl leading-tight font-bold sm:text-4xl" [class.text-white]="light()">{{ title() }}</h2>
      @if (text()) {
        <p class="mt-4 leading-relaxed" [class]="light() ? 'text-white/75' : ''">{{ text() }}</p>
      }
    </div>
  `,
  imports: [Reveal],
})
export class SectionHeading {
  readonly title = input.required<string>();
  readonly eyebrow = input<string | null>(null);
  readonly text = input<string | null>(null);
  readonly align = input<'center' | 'left'>('center');
  readonly light = input(false);
}

export interface Crumb {
  label: string;
  url?: string | null;
}

/** Page title over a photograph, with breadcrumbs. */
@Component({
  selector: 'app-page-banner',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon],
  template: `
    <section class="relative isolate flex items-center overflow-hidden bg-navy-950 text-center"
             [class]="tall() ? 'min-h-[22rem] sm:min-h-[30rem]' : 'min-h-[17rem] sm:min-h-[21rem]'">
      @if (background(); as bg) {
        <img [src]="bg.src" [attr.srcset]="bg.srcset" sizes="100vw" alt="" fetchpriority="high" class="absolute inset-0 -z-20 size-full object-cover">
      }
      <div class="absolute inset-0 -z-10 bg-linear-to-b from-navy-950/70 via-navy-950/50 to-navy-950/75" aria-hidden="true"></div>

      <div class="container-page py-14 sm:py-20">
        <h1 class="animate-rise mx-auto max-w-4xl text-3xl leading-tight font-bold text-white sm:text-5xl">{{ title() }}</h1>
        @if (intro()) {
          <p class="animate-rise mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/85 [animation-delay:100ms] sm:text-base">{{ intro() }}</p>
        }
        <ng-content />
        <nav aria-label="Breadcrumb" class="animate-rise mt-7 inline-flex flex-wrap items-center justify-center gap-x-2 gap-y-1 rounded-full bg-white/10 px-5 py-2 text-xs text-white/80 backdrop-blur [animation-delay:180ms] sm:text-sm">
          <a routerLink="/" class="hover:text-white">{{ site.t('breadcrumb_home') }}</a>
          @for (crumb of crumbs(); track crumb.label) {
            <svg icon="chevron-right" class="size-3 text-white/50"></svg>
            @if (crumb.url) {
              <a [routerLink]="crumb.url" class="hover:text-white">{{ crumb.label }}</a>
            } @else {
              <span class="font-medium text-saffron-300" aria-current="page">{{ crumb.label }}</span>
            }
          }
        </nav>
      </div>

      @if (credit()) {
        <p class="absolute top-2 right-3 max-w-[60%] truncate text-[10px] text-white/55">
          @if (creditUrl()) { <a [href]="creditUrl()" target="_blank" rel="noopener" class="hover:text-white">{{ credit() }}</a> } @else { {{ credit() }} }
        </p>
      }
    </section>
  `,
})
export class PageBanner {
  protected readonly site = inject(Site);
  readonly title = input.required<string>();
  readonly intro = input<string | null>(null);
  readonly crumbs = input<Crumb[]>([]);
  readonly image = input<Img | null>(null);
  readonly credit = input<string | null>(null);
  readonly creditUrl = input<string | null>(null);
  readonly tall = input(false);

  protected readonly background = computed<Img | null>(() => {
    const own = this.image();
    if (own) {
      return own;
    }
    const banner = this.site.setting('page_banner');
    return banner ? { src: banner, srcset: null } : null;
  });
}

/** Question-and-answer pairs that open one at a time. */
@Component({
  selector: 'app-faq-list',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  template: `
    <div class="space-y-3">
      @for (faq of faqs(); track faq.question; let i = $index) {
        <div class="rounded-xl border bg-white transition-all" [class]="open() === i ? 'border-saffron-200 shadow-card' : 'border-navy-100'">
          <h3 class="text-base">
            <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-display text-[17px] font-semibold text-navy-900"
                    [attr.aria-expanded]="open() === i" (click)="open.set(open() === i ? -1 : i)">
              {{ faq.question }}
              <span class="grid size-8 shrink-0 place-items-center rounded-full transition-colors" [class]="open() === i ? 'bg-saffron-600 text-white' : 'bg-mist text-saffron-600'">
                <svg [icon]="open() === i ? 'minus' : 'plus'" class="size-4"></svg>
              </span>
            </button>
          </h3>
          @if (open() === i) {
            <p class="animate-fade px-5 pb-5 leading-relaxed">{{ faq.answer }}</p>
          }
        </div>
      }
    </div>
  `,
})
export class FaqList {
  readonly faqs = input.required<Faq[]>();
  readonly firstOpen = input(true);
  protected readonly open = linkedSignal<number>(() => (this.firstOpen() ? 0 : -1));
}

/** Previous / next and page numbers. */
@Component({
  selector: 'app-pagination',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  template: `
    @if (last() > 1) {
      <nav class="mt-12 flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
        <button type="button" class="grid size-11 place-items-center rounded-full border border-navy-200 bg-white text-navy-800 hover:border-navy-900 disabled:opacity-40"
                [disabled]="current() <= 1" (click)="go.emit(current() - 1)" aria-label="Previous page"><svg icon="chevron-left" class="size-4"></svg></button>
        @for (page of pages(); track $index) {
          @if (page === 0) {
            <span class="px-1 text-ink/40">…</span>
          } @else {
            <button type="button" class="grid size-11 place-items-center rounded-full border text-sm font-semibold"
                    [class]="page === current() ? 'border-navy-900 bg-navy-900 text-white' : 'border-navy-200 bg-white text-navy-800 hover:border-navy-900'"
                    [attr.aria-current]="page === current() ? 'page' : null" (click)="go.emit(page)">{{ page }}</button>
          }
        }
        <button type="button" class="grid size-11 place-items-center rounded-full border border-navy-200 bg-white text-navy-800 hover:border-navy-900 disabled:opacity-40"
                [disabled]="current() >= last()" (click)="go.emit(current() + 1)" aria-label="Next page"><svg icon="chevron-right" class="size-4"></svg></button>
      </nav>
    }
  `,
})
export class Pagination {
  readonly current = input.required<number>();
  readonly last = input.required<number>();
  readonly go = output<number>();

  /** 1 … 4 5 6 … 12 (0 stands for the gap). */
  protected readonly pages = computed(() => {
    const out: number[] = [];
    const cur = this.current();
    const last = this.last();
    for (let p = 1; p <= last; p++) {
      if (p === 1 || p === last || Math.abs(p - cur) <= 1) {
        out.push(p);
      } else if (out[out.length - 1] !== 0) {
        out.push(0);
      }
    }
    return out;
  });
}

/** Stars for a review: the rating given, out of five. */
@Component({
  selector: 'app-stars',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon],
  host: { class: 'flex gap-0.5 text-saffron-500', role: 'img', '[attr.aria-label]': 'rating() + " out of 5 stars"' },
  template: `@for (i of [1, 2, 3, 4, 5]; track i) { <svg icon="star" [class]="size() + (i > rating() ? ' text-ink/15' : '')"></svg> }`,
})
export class Stars {
  readonly rating = input.required<number>();
  readonly size = input('size-4');
}

/** A loading placeholder in the shape of the coming content. */
@Component({
  selector: 'app-loading',
  changeDetection: ChangeDetectionStrategy.OnPush,
  template: `
    <div class="container-page section" aria-busy="true" aria-label="Loading">
      <div class="skeleton h-9 w-2/3 max-w-md"></div>
      <div class="skeleton mt-4 h-4 w-full max-w-xl"></div>
      <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @for (i of [1, 2, 3]; track i) { <div class="skeleton aspect-[4/3]"></div> }
      </div>
    </div>
  `,
})
export class Loading {}
