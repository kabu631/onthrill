import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, effect, inject, input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { errorStatus } from '../core/api';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { PackageCard as Package } from '../core/types';
import { Icon } from '../shared/icon';
import { Loading, Media, PageBanner } from '../shared/ui';
import { NotFoundPage } from './not-found';

interface PageData {
  page: { title: string; slug: string; content: string; updated: string | null };
  pages: { title: string; slug: string; url: string }[];
  tours: Package[];
}

/** Pages written in the admin: About Us, Travel Information, Privacy Policy, Terms, Photo Credits… */
@Component({
  selector: 'app-cms-page',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon, Media, PageBanner, Loading, NotFoundPage],
  template: `
    @if (data.value(); as d) {
      <app-page-banner [title]="d.page.title" [crumbs]="[{ label: d.page.title }]" />
      <div class="container-page section pt-10 sm:pt-14">
        <div class="grid gap-12 lg:grid-cols-[1fr_21rem]">
          <div class="min-w-0">
            <article class="rich" [innerHTML]="d.page.content"></article>
            <p class="mt-10 text-xs text-ink/55">{{ site.t('last_updated', { date: d.page.updated }) }}</p>
          </div>

          <aside class="space-y-6">
            @if (d.pages.length > 1) {
              <nav class="card p-5" [attr.aria-label]="site.t('page_sidebar_pages')">
                <h2 class="px-1 text-lg font-semibold">{{ site.t('page_sidebar_pages') }}</h2>
                <ul class="mt-3 space-y-0.5 text-sm">
                  @for (item of d.pages; track item.slug) {
                    <li>
                      <a [routerLink]="item.url" class="flex items-center justify-between gap-3 rounded-lg px-3 py-2.5"
                         [class]="item.slug === d.page.slug ? 'bg-saffron-50 font-semibold text-saffron-700' : 'text-ink/80 hover:bg-mist hover:text-navy-900'"
                         [attr.aria-current]="item.slug === d.page.slug ? 'page' : null">
                        {{ item.title }}
                      </a>
                    </li>
                  }
                </ul>
              </nav>
            }

            <div class="rounded-xl bg-navy-900 p-6 text-sm text-white/80">
              <p class="font-display text-xl font-semibold text-white">{{ site.t('page_sidebar_help_title') }}</p>
              <p class="mt-2 leading-relaxed">{{ site.t('page_sidebar_help_text') }}</p>
              <a routerLink="/contact-us" class="btn btn-primary mt-5 w-full">{{ site.t('page_sidebar_help_button') }}</a>
              @if (site.whatsappNumber()) {
                <a [href]="site.whatsappUrl()" target="_blank" rel="noopener" class="btn btn-whatsapp mt-3 w-full"><svg icon="whatsapp" class="size-4"></svg> WhatsApp</a>
              }
              @if (site.phone()) {
                <a [href]="'tel:' + site.digits(site.phone())" class="mt-4 block text-center font-display text-lg font-semibold text-saffron-400 hover:text-saffron-300">{{ site.phone() }}</a>
              }
            </div>

            @if (d.tours.length) {
              <div class="card p-5">
                <h2 class="px-1 text-lg font-semibold">{{ site.t('page_sidebar_tours') }}</h2>
                <ul class="mt-4 space-y-4">
                  @for (tour of d.tours; track tour.id) {
                    <li>
                      <a [routerLink]="tour.url" class="group flex gap-3">
                        <app-media [image]="tour.image" [alt]="tour.name" class="size-20 shrink-0 rounded-xl" />
                        <span class="min-w-0">
                          <span class="line-clamp-2 font-display text-[15px] leading-snug font-semibold text-navy-900 group-hover:text-saffron-700">{{ tour.name }}</span>
                          <span class="mt-1 block text-xs text-ink/60">{{ tour.duration }}</span>
                          <span class="mt-1 block text-sm font-semibold text-saffron-700">{{ site.priceLine(tour) }}</span>
                        </span>
                      </a>
                    </li>
                  }
                </ul>
                <a routerLink="/packages" class="more-link mt-5 px-1">{{ site.t('page_sidebar_all_tours') }}</a>
              </div>
            }
          </aside>
        </div>
      </div>
    } @else if (errorStatus(data) === 404) {
      <app-not-found />
    } @else {
      <app-loading />
    }
  `,
})
export class CmsPage {
  protected readonly site = inject(Site);
  readonly slug = input.required<string>();
  protected readonly data = httpResource<PageData>(() => '/api/pages/' + this.slug());
  protected readonly errorStatus = errorStatus;

  constructor() {
    const seo = inject(Seo);
    effect(() => {
      const page = this.data.value()?.page;
      if (page) seo.set({ title: page.title, description: page.content });
    });
  }
}
