/** Shapes of the JSON sent by Laravel (app/Support/Present.php and SiteData.php). */

export type Currency = 'INR' | 'NPR';

export interface Img {
  src: string;
  srcset: string | null;
}

export interface Link {
  name: string;
  url: string;
}

export interface MenuCountry extends Link {
  children: Link[];
}

export interface Menu {
  countries: MenuCountry[];
  tour_types: Link[];
  header_pages: Link[];
  footer_pages: Link[];
}

export interface User {
  name: string;
  email: string;
  phone: string | null;
  is_staff: boolean;
}

export interface Faq {
  question: string;
  answer: string;
}

export interface HeroSlide {
  image: Img | null;
  title: string | null;
  place: string | null;
  link: string | null;
  credit: string | null;
}

export interface SiteSettings {
  site_name: string;
  tagline: string;
  logo: string | null;
  logo_footer: string | null;
  page_banner: string | null;
  footer_background: string | null;
  share_image: string | null;
  footer_about: string;
  phone: string;
  whatsapp: string;
  email: string;
  address: string;
  business_hours: string;
  map_embed_url: string;
  map_location: string;
  facebook: string;
  instagram: string;
  youtube: string;
  twitter: string;
  default_currency: Currency;
  advance_percent: number;
  meta_title: string;
  meta_description: string;
  geo_latitude: string;
  geo_longitude: string;
  home_sections: string[];
  hero_eyebrow: string;
  hero_text: string;
  hero_primary_label: string;
  hero_primary_url: string;
  hero_secondary_label: string;
  hero_secondary_url: string;
  hero_slides: HeroSlide[];
  hero_words: string[];
  destinations_title: string;
  destinations_text: string;
  home_packages_title: string;
  why_title: string;
  why_text: string;
  why_items: { title: string; text?: string }[];
  why_image: Img | null;
  why_image_small: Img | null;
  how_title: string;
  how_text: string;
  how_steps: { title: string; text: string; icon: string | null }[];
  how_button_label: string;
  how_button_url: string;
  testimonials_title: string;
  home_gallery_title: string;
  faq_title: string;
  faq_text: string;
  faqs: Faq[];
  cta_title: string;
  cta_text: string;
  cta_image: Img | null;
  package_trust_points: string[];
  /** Price ranges for the search, with their wording in each currency. */
  budgets: ({ value: string } & Record<Currency, string>)[];
}

export interface Bootstrap {
  site: SiteSettings;
  texts: Record<string, string>;
  menu: Menu;
  currency: Currency;
  user: User | null;
  can_book: boolean;
  /** The current page's API response, sent with the page so the first screen draws at once. */
  page?: { api: string; data: unknown };
}

export interface PriceSet {
  now: number;
  was: number | null;
  off: number;
}

export interface Category {
  name: string;
  slug: string;
  url: string;
}

export interface PackageCard {
  id: number;
  slug: string;
  name: string;
  url: string;
  summary: string | null;
  duration: string;
  days: number;
  image: Img | null;
  category: Category | null;
  places: string[];
  start_point: string | null;
  group_size: string | null;
  max_altitude_m: number | null;
  prices: Record<Currency, PriceSet> | null;
}

export interface DestinationCard {
  id: number;
  slug: string;
  name: string;
  url: string;
  country: string;
  country_url: string;
  region: string;
  altitude_m: number | null;
  summary: string | null;
  image: Img | null;
  tours: number | null;
}

export interface Photo {
  image: Img | null;
  large: string | null;
  alt: string;
  credit: string | null;
  credit_url: string | null;
}

export interface PackageDetail extends PackageCard {
  end_point: string | null;
  best_season: string | null;
  difficulty: string | null;
  overview: string;
  highlights: string[];
  itinerary: { title: string; description: string | null }[];
  inclusions: string[];
  exclusions: string[];
  notes: string;
  faqs: Faq[];
  photos: Photo[];
  destinations: DestinationCard[];
  updated: string | null;
  edit_url: string | null;
}

export interface DestinationDetail extends DestinationCard {
  full_region: string | null;
  banner: Img | null;
  image_credit: string | null;
  image_credit_url: string | null;
  description: string;
  facts: { icon: string; key: string; value: string }[];
  how_to_reach: string | null;
  map_url: string;
  faqs: Faq[];
  photos: Photo[];
  updated: string | null;
}

export interface Testimonial {
  name: string;
  location: string | null;
  content: string;
  rating: number;
  photo: string | null;
}

export interface GalleryItem {
  title: string | null;
  image: Img | null;
  large: string | null;
  credit: string | null;
  credit_url: string | null;
}

export interface Paginated<T> {
  data: T[];
  meta: { current_page: number; last_page: number; per_page: number; total: number };
}

export interface HomeData {
  search: Record<string, { name: string; slug: string }[]>;
  destination_count: number;
  countries: string[];
  destinations: DestinationCard[];
  packages: PackageCard[];
  package_count: number;
  testimonials: Testimonial[];
  gallery: GalleryItem[];
}

export interface Booking {
  reference: string;
  name: string;
  first_name: string;
  email: string;
  phone: string;
  package_name: string;
  package_url: string | null;
  travel_date: string | null;
  travel_date_short: string | null;
  adults: number;
  children: number;
  travellers: number;
  nationality: string;
  travelling_from: string | null;
  currency: Currency;
  price_per_person: number | null;
  total: number | null;
  advance: number | null;
  status: string;
  status_label: string;
  payment_status: string;
  payment_label: string;
  is_paid: boolean;
  payment: {
    india: boolean;
    amount: number | null;
    qr: string | null;
    ids: { label: string; value: string }[];
    payee: string;
    bank: string;
    upi_link: string | null;
    note: string;
    screenshot_message: string;
  } | null;
}
