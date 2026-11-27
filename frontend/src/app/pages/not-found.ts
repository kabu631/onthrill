import { ChangeDetectionStrategy, Component, inject, OnInit } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { Seo } from '../core/seo';
import { Site } from '../core/site';
import { Icon } from '../shared/icon';

/** "Page not found", as its own page and inside pages whose content does not exist. */
@Component({
  selector: 'app-not-found',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, Icon],
  template: `
    <div class="container-page section text-center">
      <p class="font-display text-8xl font-bold text-saffron-500">404</p>
      <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ site.t('e404_title') }}</h1>
      <p class="mx-auto mt-3 max-w-md">{{ site.t('e404_text') }}</p>

      <form role="search" class="relative mx-auto mt-8 max-w-md" (submit)="search($event, q.value)">
        <label for="search-404" class="sr-only">{{ site.t('search_title') }}</label>
        <input #q id="search-404" type="search" [placeholder]="site.t('search_placeholder')" class="input rounded-full py-3.5 pl-12">
        <svg icon="search" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40"></svg>
      </form>

      <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a routerLink="/" class="btn btn-primary">{{ site.t('e404_home') }}</a>
        <a routerLink="/destinations" class="btn btn-outline">{{ site.t('menu_destinations') }}</a>
        <a routerLink="/packages" class="btn btn-outline">{{ site.t('menu_packages') }}</a>
      </div>
    </div>
  `,
})
export class NotFoundPage implements OnInit {
  protected readonly site = inject(Site);
  private readonly seo = inject(Seo);
  private readonly router = inject(Router);

  ngOnInit(): void {
    this.seo.set({ title: this.site.t('e404_title'), noindex: true });
  }

  protected search(event: Event, q: string): void {
    event.preventDefault();
    this.router.navigate(['/search'], { queryParams: { q } });
  }
}
