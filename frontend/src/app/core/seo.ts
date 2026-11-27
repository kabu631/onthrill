import { DOCUMENT } from '@angular/common';
import { inject, Injectable } from '@angular/core';
import { Meta, Title } from '@angular/platform-browser';
import { Site } from './site';

/**
 * Keeps the title and description up to date while the visitor moves between
 * pages inside the app. The first page arrives with them already set by
 * Laravel, together with the structured data search engines read.
 */
@Injectable({ providedIn: 'root' })
export class Seo {
  private readonly title = inject(Title);
  private readonly meta = inject(Meta);
  private readonly site = inject(Site);
  private readonly document = inject(DOCUMENT);

  set(page: { title?: string | null; description?: string | null; noindex?: boolean }): void {
    const name = this.site.setting('site_name');
    const title = page.title ? `${page.title} | ${name}` : this.site.setting('meta_title') || name;
    this.title.setTitle(title);
    this.meta.updateTag({ property: 'og:title', content: title });

    const description = page.description || this.site.setting('meta_description');
    if (description) {
      const text = description.replace(/<[^>]+>/g, '').replace(/\s+/g, ' ').trim().slice(0, 160);
      this.meta.updateTag({ name: 'description', content: text });
      this.meta.updateTag({ property: 'og:description', content: text });
    }

    this.meta.updateTag({ name: 'robots', content: page.noindex ? 'noindex, follow' : 'index, follow, max-image-preview:large' });

    const canonical = this.document.querySelector<HTMLLinkElement>('link[rel="canonical"]');
    if (canonical) {
      canonical.href = this.document.location.origin + this.document.location.pathname;
    }
  }
}
