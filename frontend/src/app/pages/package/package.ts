import { DecimalPipe } from '@angular/common';
import { httpResource } from '@angular/common/http';
import { ChangeDetectionStrategy, Component, computed, DestroyRef, effect, inject, input, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { errorStatus } from '../../core/api';
import { Seo } from '../../core/seo';
import { Site } from '../../core/site';
import { PackageCard as Package, PackageDetail } from '../../core/types';
import { PackageCard, Price } from '../../shared/cards';
import { EnquiryForm } from '../../shared/forms';
import { Icon } from '../../shared/icon';
import { PhotoGrid } from '../../shared/photos';
import { FaqList, Loading, Media, Reveal, SectionHeading } from '../../shared/ui';
import { NotFoundPage } from '../not-found';
import { BookingForm } from './booking-form';

interface PackageData {
  package: PackageDetail;
  related: Package[];
}

/** /packages/:slug — facts, photos, itinerary, inclusions, places, questions, price box and booking. */
@Component({
  selector: 'app-package',
  changeDetection: ChangeDetectionStrategy.OnPush,
  imports: [RouterLink, DecimalPipe, Icon, Media, Reveal, SectionHeading, FaqList, PhotoGrid, EnquiryForm, PackageCard, Price, BookingForm, Loading, NotFoundPage],
  host: { '(window:scroll)': 'track()' },
  templateUrl: './package.html',
})
export class PackagePage {
  protected readonly site = inject(Site);
  readonly slug = input.required<string>();

  protected readonly data = httpResource<PackageData>(() => '/api/packages/' + this.slug());
  protected readonly errorStatus = errorStatus;
  protected readonly p = computed(() => this.data.value()?.package);

  protected readonly price = computed(() => this.p()?.prices?.[this.site.currency()] ?? null);
  protected readonly openDays = signal<Set<number>>(new Set([0]));
  protected readonly allOpen = computed(() => this.openDays().size === (this.p()?.itinerary.length ?? 0));
  protected readonly active = signal('overview');
  protected readonly advance = computed(() => {
    const percent = this.site.setting('advance_percent');
    return String(Math.round(percent * 10) / 10);
  });

  protected readonly facts = computed(() => {
    const p = this.p();
    if (!p) return [];
    return [
      { icon: 'clock', label: this.site.t('pkg_fact_duration'), value: p.duration },
      p.start_point ? { icon: 'map-pin', label: this.site.t('pkg_fact_route'), value: p.start_point === p.end_point || !p.end_point ? p.start_point : `${p.start_point} / ${p.end_point}` } : null,
      p.best_season ? { icon: 'sun', label: this.site.t('pkg_fact_season'), value: p.best_season } : null,
      p.group_size ? { icon: 'users', label: this.site.t('pkg_fact_group'), value: p.group_size } : null,
      p.difficulty ? { icon: 'check-circle', label: this.site.t('pkg_fact_difficulty'), value: p.difficulty } : null,
      p.max_altitude_m ? { icon: 'mountain', label: this.site.t('pkg_fact_altitude'), value: p.max_altitude_m.toLocaleString('en-IN') + ' m' } : null,
    ].filter((f) => f !== null);
  });

  /** Section tabs: only the sections this package has. */
  protected readonly tabs = computed(() => {
    const p = this.p();
    if (!p) return [];
    return [
      { id: 'overview', label: this.site.t('pkg_tab_overview') },
      p.itinerary.length ? { id: 'itinerary', label: this.site.t('pkg_tab_itinerary') } : null,
      p.inclusions.length || p.exclusions.length ? { id: 'included', label: this.site.t('pkg_tab_included') } : null,
      p.notes ? { id: 'before-you-go', label: this.site.t('pkg_tab_before') } : null,
      p.destinations.length ? { id: 'places', label: this.site.t('pkg_tab_places') } : null,
      p.faqs.length ? { id: 'faqs', label: this.site.t('pkg_tab_faqs') } : null,
      this.site.canBook() ? { id: 'book', label: this.site.t('pkg_tab_book') } : null,
    ].filter((t) => t !== null);
  });

  constructor() {
    const seo = inject(Seo);
    effect(() => {
      const p = this.p();
      if (p) seo.set({ title: p.name, description: p.summary });
    });
    // Room for the price bar fixed to the bottom of the screen on phones.
    const root = document.documentElement;
    root.style.setProperty('--bottom-bar', innerWidth < 1024 ? '4.5rem' : '0px');
    inject(DestroyRef).onDestroy(() => root.style.removeProperty('--bottom-bar'));
    // A new package: its first day open again.
    effect(() => {
      this.slug();
      this.openDays.set(new Set([0]));
    });
  }

  protected toggleDay(i: number): void {
    this.openDays.update((days) => {
      const next = new Set(days);
      next.has(i) ? next.delete(i) : next.add(i);
      return next;
    });
  }

  protected toggleAll(): void {
    const count = this.p()?.itinerary.length ?? 0;
    this.openDays.set(this.allOpen() ? new Set() : new Set(Array.from({ length: count }, (_, i) => i)));
  }

  protected goTo(id: string): void {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  /** The tab of the section being read. */
  protected track(): void {
    let current = this.tabs()[0]?.id ?? 'overview';
    for (const tab of this.tabs()) {
      const el = document.getElementById(tab.id);
      if (el && el.getBoundingClientRect().top < 200) current = tab.id;
    }
    if (current !== this.active()) this.active.set(current);
  }

  protected pad(n: number): string {
    return String(n).padStart(2, '0');
  }

  protected async signOut(): Promise<void> {
    await this.site.logout();
    this.data.reload();
  }
}
