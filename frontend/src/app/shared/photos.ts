import { ChangeDetectionStrategy, Component, computed, effect, inject, input, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Site } from '../core/site';
import { Photo } from '../core/types';
import { Icon } from './icon';
import { Media } from './ui';

export interface LightboxItem {
  src: string | null;
  title: string | null;
  credit: string | null;
  credit_url: string | null;
}

/** Full-screen photo viewer: arrow keys, swipe, Escape to close. */
@Component({
  selector: 'app-lightbox',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Icon, RouterLink],
  host: { '(document:keydown)': 'key($event)' },
  template: `
    @if (index() !== null) {
      @let item = items()[index()!];
      <div class="animate-fade fixed inset-0 z-[80] flex flex-col bg-navy-950/95 text-white" role="dialog" aria-modal="true" aria-label="Photo viewer"
           (touchstart)="touchStart($event)" (touchend)="touchEnd($event)">
        <div class="flex items-center justify-between px-4 py-3 text-sm">
          <span class="tabular-nums text-white/70">{{ index()! + 1 }} / {{ items().length }}</span>
          <button type="button" class="grid size-11 place-items-center rounded-full hover:bg-white/10" (click)="close()" aria-label="Close"><svg icon="x" class="size-6"></svg></button>
        </div>
        <div class="relative flex min-h-0 flex-1 items-center justify-center px-4 sm:px-16">
          @if (item.src) {
            <img [src]="item.src" [alt]="item.title ?? ''" class="max-h-full max-w-full rounded-lg object-contain">
          }
          @if (items().length > 1) {
            <button type="button" class="absolute left-2 grid size-12 place-items-center rounded-full bg-white/10 hover:bg-white/20 sm:left-4" (click)="move(-1)" aria-label="Previous photo"><svg icon="chevron-left" class="size-6"></svg></button>
            <button type="button" class="absolute right-2 grid size-12 place-items-center rounded-full bg-white/10 hover:bg-white/20 sm:right-4" (click)="move(1)" aria-label="Next photo"><svg icon="chevron-right" class="size-6"></svg></button>
          }
        </div>
        <div class="px-4 py-4 text-center text-sm">
          @if (item.title) { <p class="font-medium">{{ item.title }}</p> }
          @if (item.credit) {
            <p class="mt-1 text-xs text-white/55">
              @if (item.credit_url) { <a [href]="item.credit_url" target="_blank" rel="noopener" class="hover:text-white">{{ item.credit }}</a> } @else { {{ item.credit }} }
              · <a routerLink="/photo-credits" (click)="close()" class="underline underline-offset-2 hover:text-white">{{ site.t('photo_credits_link') }}</a>
            </p>
          }
        </div>
      </div>
    }
  `,
})
export class Lightbox {
  protected readonly site = inject(Site);
  readonly items = input.required<LightboxItem[]>();
  readonly index = signal<number | null>(null);
  private startX = 0;

  constructor() {
    // No page scrolling behind the viewer.
    effect(() => document.documentElement.classList.toggle('overflow-hidden', this.index() !== null));
  }

  open(i: number): void {
    this.index.set(i);
  }

  close(): void {
    this.index.set(null);
  }

  move(step: number): void {
    const count = this.items().length;
    this.index.update((i) => (i === null ? null : (i + step + count) % count));
  }

  protected key(event: KeyboardEvent): void {
    if (this.index() === null) return;
    if (event.key === 'Escape') this.close();
    if (event.key === 'ArrowLeft') this.move(-1);
    if (event.key === 'ArrowRight') this.move(1);
  }

  protected touchStart(event: TouchEvent): void {
    this.startX = event.changedTouches[0].clientX;
  }

  protected touchEnd(event: TouchEvent): void {
    const dx = event.changedTouches[0].clientX - this.startX;
    if (Math.abs(dx) > 50) this.move(dx < 0 ? 1 : -1);
  }
}

/** One large photo and up to four smaller ones, opening in the viewer. */
@Component({
  selector: 'app-photo-grid',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Media, Lightbox, RouterLink],
  template: `
    @if (photos().length) {
      <div class="grid gap-2 sm:h-[24rem] sm:grid-cols-4 sm:grid-rows-2 lg:h-[30rem]">
        @for (photo of shown(); track $index; let i = $index) {
          <button type="button" (click)="viewer.open(i)" [attr.aria-label]="'Open photo ' + (i + 1) + ' of ' + photos().length"
                  class="group relative block overflow-hidden rounded-xl bg-mist" [class]="cellClass(i)">
            <app-media [image]="photo.image" [alt]="photo.alt" [eager]="i === 0" class="absolute inset-0" imgClass="group-hover:scale-105" />
            @if (i === 4 && photos().length > 5) {
              <span class="absolute inset-0 grid place-items-center bg-navy-950/60 font-display text-xl font-semibold text-white">{{ site.t('photos_more', { count: photos().length - 5 }) }}</span>
            }
            @if (i === 0 && photos().length > 1) {
              <span class="absolute right-3 bottom-3 flex items-center gap-1.5 rounded-full bg-white/95 px-3 py-1.5 text-xs font-semibold text-navy-900 sm:hidden">
                {{ site.t('photos_count', { count: photos().length }) }}
              </span>
            }
          </button>
        }
      </div>
      @if (photos()[0].credit) {
        <p class="mt-2 text-right text-[11px] text-ink/50">
          @if (photos()[0].credit_url) { <a [href]="photos()[0].credit_url" target="_blank" rel="noopener" class="hover:text-ink">{{ photos()[0].credit }}</a> } @else { {{ photos()[0].credit }} }
          · <a routerLink="/photo-credits" class="underline underline-offset-2 hover:text-ink">{{ site.t('photo_credits_link') }}</a>
        </p>
      }
      <app-lightbox #viewer [items]="items()" />
    }
  `,
})
export class PhotoGrid {
  protected readonly site = inject(Site);
  readonly photos = input.required<Photo[]>();

  protected readonly shown = computed(() => this.photos().slice(0, 5));
  protected readonly items = computed<LightboxItem[]>(() =>
    this.photos().map((p) => ({ src: p.large, title: p.alt, credit: p.credit, credit_url: p.credit_url })),
  );
  protected cellClass(i: number): string {
    return (this.layout()[i] ?? '') + (i === 0 ? ' aspect-[4/3] sm:aspect-auto' : ' hidden sm:block');
  }

  /** Grid placement by number of photos (large screens). */
  protected readonly layout = computed(() => {
    switch (Math.min(this.photos().length, 5)) {
      case 1: return ['sm:col-span-4 sm:row-span-2'];
      case 2: return ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2 sm:row-span-2'];
      case 3: return ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2', 'sm:col-span-2'];
      case 4: return ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2', '', ''];
      default: return ['sm:col-span-2 sm:row-span-2', '', '', '', ''];
    }
  });
}
