import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, inject, input } from '@angular/core';
import { Router } from '@angular/router';
import { apiUrl } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { GalleryItem, Paginated } from '../core/types';
import { Lightbox, LightboxItem } from '../shared/photos';
import { Media, PageBanner, Pagination, Reveal } from '../shared/ui';

/** /gallery — photos that open in the viewer, each with its credit. */
@Component({
  selector: 'app-gallery',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [Media, PageBanner, Pagination, Reveal, Lightbox],
  template: `
    <app-page-banner [title]="site.t('gallery_title')" [intro]="site.t('gallery_intro')" [crumbs]="[{ label: site.t('gallery_title') }]" />
    <div class="container-page section pt-10 sm:pt-14">
      @if (data.value(); as d) {
        @if (d.data.length) {
          <ul class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
            @for (g of d.data; track $index; let i = $index) {
              <li appReveal [revealDelay]="(i % 4) * 60">
                <button type="button" (click)="viewer.open(i)" class="group relative block aspect-square w-full overflow-hidden rounded-2xl bg-mist" [attr.aria-label]="'Open photo ' + (i + 1)">
                  <app-media [image]="g.image" [alt]="g.title || site.setting('site_name')" class="absolute inset-0" imgClass="group-hover:scale-110" />
                </button>
              </li>
            }
          </ul>
          <app-pagination [current]="d.meta.current_page" [last]="d.meta.last_page" (go)="go($event)" />
          <app-lightbox #viewer [items]="items()" />
        } @else {
          <p class="card p-10 text-center">{{ site.t('gallery_empty') }}</p>
        }
      } @else {
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4" aria-busy="true">
          @for (i of [1, 2, 3, 4, 5, 6, 7, 8]; track i) { <div class="skeleton aspect-square"></div> }
        </div>
      }
    </div>
  `,
})
export class GalleryPage {
  protected readonly site = inject(Site);
  private readonly router = inject(Router);
  readonly page = input<string>();

  protected readonly data = httpResource<Paginated<GalleryItem>>(() => apiUrl('/api/gallery', { page: Number(this.page()) > 1 ? this.page() : null }));
  protected readonly items = computed<LightboxItem[]>(() =>
    (this.data.value()?.data ?? []).map((g) => ({ src: g.large, title: g.title, credit: g.credit, credit_url: g.credit_url })),
  );

  constructor() {
    inject(Seo).set({ title: this.site.t('gallery_title'), description: this.site.t('gallery_intro') });
  }

  protected go(page: number): void {
    this.router.navigate([], { queryParams: { page: page > 1 ? page : null } });
    scrollTo({ top: 0, behavior: 'smooth' });
  }
}
