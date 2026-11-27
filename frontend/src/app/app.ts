import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { NavigationCancel, NavigationEnd, NavigationError, NavigationStart, Router, RouterOutlet } from '@angular/router';
import { Site } from './core/site';
import { Floating, Footer } from './layout/footer';
import { Header } from './layout/header';

@Component({
  selector: 'app-root',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterOutlet, Header, Footer, Floating],
  host: { class: 'flex min-h-screen flex-col' },
  template: `
    @if (navigating()) { <div class="page-progress" aria-hidden="true"></div> }
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">Skip to content</a>
    <app-header />
    <main id="main" class="flex-1">
      <router-outlet />
    </main>
    <app-footer />
    <app-floating />
  `,
})
export class App {
  protected readonly navigating = signal(false);

  constructor() {
    const site = inject(Site);
    let navigations = 0;
    inject(Router).events.subscribe((event) => {
      if (event instanceof NavigationStart) {
        // The data sent with the page belongs to the first page only.
        if (++navigations > 1) site.dropFirstPage();
        this.navigating.set(navigations > 1);
      } else if (event instanceof NavigationEnd || event instanceof NavigationCancel || event instanceof NavigationError) {
        this.navigating.set(false);
      }
    });
  }
}
