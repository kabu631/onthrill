<?php

/*
|--------------------------------------------------------------------------
| Site setting defaults
|--------------------------------------------------------------------------
| Every value here can be changed from Admin > Site Settings or Admin >
| Page Content, which store overrides in the `settings` table. These are the
| fallbacks used until then (and what the seeder starts from). Image paths
| are relative to the public storage disk.
|
| Contact details are deliberately empty: add the real phone, WhatsApp and
| office address in Admin > Site Settings. Until then the website simply
| leaves those lines out.
*/

return [
    'defaults' => [
        // General
        'site_name' => 'OnThrill',
        'tagline' => 'Pilgrimage & Cultural Tours',
        'logo' => 'site/logo.png',               // empty: the site name is shown as a wordmark
        'logo_footer' => 'site/logo-footer.png', // light version: footer and the header over the homepage photo
        'footer_about' => 'Pilgrimage and cultural tours to the temples of Nepal, the Chardham of Uttarakhand and Kailash Mansarovar in Tibet, for travellers from India and Nepal.',
        'page_banner' => 'site/page-banner.webp',
        'footer_background' => 'site/footer-bg.webp',

        // Contact
        'phone' => '',
        'whatsapp' => '',
        'email' => 'info@onthrill.com',
        'address' => '',
        'business_hours' => '',
        'map_embed_url' => '',
        // Shown on the contact page map when there is no embed link: the address, or this place.
        'map_location' => 'Kathmandu, Nepal',

        // Social (leave blank to hide the icon)
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'twitter' => '',

        // ------------------------------------------------------------------
        // Prices and payments (Admin > Site Settings > Prices & Payments)
        // ------------------------------------------------------------------
        'default_currency' => 'INR',   // shown until the visitor picks INR or NPR
        'npr_per_inr' => 1.6,          // Nepali rupees for one Indian rupee
        'advance_percent' => 25,       // advance asked for to confirm a booking
        'booking_notify_email' => 'info@onthrill.com',

        // Indian travellers (INR)
        'upi_id' => '',
        'upi_number' => '',
        'upi_name' => 'OnThrill',
        'upi_qr' => '',
        'bank_details_india' => '',

        // Nepali travellers (NPR)
        'esewa_id' => '',
        'khalti_id' => '',
        'nepal_qr' => '',
        'bank_details_nepal' => '',

        'payment_note' => 'After paying, send the payment screenshot on WhatsApp or by email with your booking reference.',

        // ------------------------------------------------------------------
        // Homepage (Admin > Page Content > Homepage)
        // ------------------------------------------------------------------
        'home_sections' => ['hero', 'facts', 'destinations', 'packages', 'why', 'how', 'testimonials', 'gallery', 'faq', 'cta'],

        'hero_eyebrow' => 'Book Your',
        // Photo slideshow behind the headline. Each slide's title completes the
        // heading ("Book Your" + title); the place and credit show in the corner.
        'hero_slides' => [
            ['image' => 'site/hero-pashupatinath.webp', 'title' => 'Pashupatinath Darshan', 'place' => 'Pashupatinath Temple, Kathmandu', 'link' => '/destinations/pashupatinath-temple', 'credit' => 'Photo: Nabin K. Sapkota / Wikimedia Commons, CC BY-SA 4.0'],
            ['image' => 'site/hero-kedarnath.webp', 'title' => 'Chardham Yatra', 'place' => 'Kedarnath, Uttarakhand', 'link' => '/destinations/kedarnath', 'credit' => 'Photo: Nehasnaps0306 / Wikimedia Commons, CC BY-SA 4.0'],
            ['image' => 'site/hero-swayambhunath.webp', 'title' => 'Kathmandu Temple Tour', 'place' => 'Swayambhunath Stupa, Kathmandu', 'link' => '/destinations/swayambhunath-stupa', 'credit' => 'Photo: Nirmal Dulal / Wikimedia Commons, CC BY-SA 4.0'],
            ['image' => 'site/hero-kailash.webp', 'title' => 'Kailash Mansarovar Yatra', 'place' => 'Mount Kailash, Tibet', 'link' => '/destinations/kailash-mansarovar', 'credit' => 'Photo: Ondřej Žváček / Wikimedia Commons, CC BY 2.5'],
        ],
        // Used when no slides are set: one photo, with these words rotating in the heading.
        'hero_words' => ['Pashupatinath Darshan', 'Muktinath Yatra', 'Chardham Yatra', 'Kailash Mansarovar Yatra'],
        'hero_background' => 'site/hero-bg.webp',
        'hero_text' => 'Temple tours of Nepal, the Chardham of Uttarakhand and Kailash Mansarovar in Tibet, planned for travellers from India and Nepal. Prices in Indian and Nepali rupees.',
        'hero_primary_label' => 'View Tour Packages',
        'hero_primary_url' => '/packages',
        'hero_secondary_label' => 'Explore Destinations',
        'hero_secondary_url' => '/destinations',

        'destinations_title' => 'Sacred Destinations',
        'destinations_text' => 'Temples and holy places in Nepal, India and Tibet. Each page has the facts you need before you go: altitude, best season and how to get there.',

        'home_packages_title' => 'Popular Tour Packages',

        'why_title' => 'Why Travel With Us',
        'why_text' => 'We plan temple tours for families and groups from India and Nepal: hotels close to the temples, transport, permits and darshan timings, priced in your own currency.',
        'why_image' => 'destinations/manakamana.webp',
        'why_image_small' => 'destinations/muktinath-2.webp',
        'why_items' => [
            ['title' => 'Prices in your currency', 'text' => 'Every package is priced in Indian and Nepali rupees, so there are no surprises when you pay.'],
            ['title' => 'Darshan and permits arranged', 'text' => 'Temple timings, Char Dham registration, conservation area permits and Tibet permits are handled before you travel.'],
            ['title' => 'A pace that suits families', 'text' => 'Itineraries leave time for puja and rest, with extra days where the altitude is high.'],
            ['title' => 'One team from start to finish', 'text' => 'The same team plans your trip and stays in touch while you travel.'],
        ],

        'how_title' => 'How Booking Works',
        'how_text' => 'Send a booking request in a few minutes. Nothing is charged until we have confirmed the details with you.',
        'how_steps' => [
            ['title' => 'Choose a package', 'text' => 'Pick a tour, or ask us to plan one around your dates.'],
            ['title' => 'Send a booking request', 'text' => 'Tell us the travel date and who is travelling.'],
            ['title' => 'We confirm by phone', 'text' => 'We check hotels, flights and permits, then confirm the final price.'],
            ['title' => 'Pay the advance', 'text' => 'Pay by UPI from India, or by eSewa, Khalti or bank transfer from Nepal.'],
        ],
        'how_button_label' => 'Browse Packages',
        'how_button_url' => '/packages',

        'testimonials_title' => 'What Travellers Say',
        'home_gallery_title' => 'Photo Gallery',

        'faq_title' => 'Questions Travellers Ask',
        'faq_text' => 'Visas, permits, seasons and payments. If your question is not answered here, send it to us and we will reply.',
        'faqs' => [
            ['question' => 'Do Indian citizens need a visa to visit Nepal?', 'answer' => 'No. Indian citizens do not need a visa for Nepal. Carry a valid passport or a Voter ID card issued by the Election Commission of India. Children should carry an identity document as well.'],
            ['question' => 'Do Nepali citizens need a visa for the Chardham Yatra?', 'answer' => 'No visa is needed. Carry your citizenship certificate or passport. Every Char Dham pilgrim must also be registered with the Uttarakhand government, which we do for you.'],
            ['question' => 'When are the Chardham temples open?', 'answer' => 'Usually from late April or May until October or November. The opening dates are announced each year on Hindu festival days, and we plan departures around them.'],
            ['question' => 'What is the best time to visit Muktinath?', 'answer' => 'March to June and September to November. The temple is at 3,710 m, so winter brings snow and very cold mornings, and flights to Jomsom are often delayed in the monsoon.'],
            ['question' => 'Can Indian passport holders do the Kailash Mansarovar Yatra through Nepal?', 'answer' => 'The rules for Indian passport holders are set by the Chinese authorities and can change from one season to the next. Send us an enquiry and we will tell you the current requirements before you book.'],
            ['question' => 'How do I pay for a booking?', 'answer' => 'Indian travellers pay in Indian rupees by UPI or bank transfer. Nepali travellers pay in Nepali rupees by eSewa, Khalti or bank transfer. An advance confirms the booking and the rest is paid before travel.'],
        ],

        'cta_title' => 'Planning a yatra for your family?',
        'cta_text' => 'Tell us your dates and who is travelling. We will suggest a route and send you a price in rupees.',
        'cta_image' => 'destinations/kailash-mansarovar.webp',

        // ------------------------------------------------------------------
        // Every other piece of wording on the website (headings, buttons, form
        // labels, page titles) is listed with its starting text in
        // app/Support/SiteTexts.php and edited in Admin > Page Content.
        // ------------------------------------------------------------------
        'package_trust_points' => ['Price confirmed before you pay', 'Hotels close to the temples', 'Permits and registration arranged', 'Help on call while you travel'],
        'inquiry_autoreply' => true,

        // ------------------------------------------------------------------
        // Email sending (Admin > Site Settings > Email). When no SMTP server is
        // set, the MAIL_* values from .env are used.
        // ------------------------------------------------------------------
        'mail_host' => '',
        'mail_port' => 587,
        'mail_encryption' => 'tls',
        'mail_username' => '',
        'mail_password' => '', // stored encrypted
        'mail_from_address' => '',

        // ------------------------------------------------------------------
        // Search engines (Admin > Site Settings > SEO)
        // ------------------------------------------------------------------
        'meta_title' => 'OnThrill – Pilgrimage Tours of Nepal, Chardham and Kailash Mansarovar',
        'meta_description' => 'Pilgrimage and cultural tour packages to Pashupatinath, Muktinath, Manakamana, Lumbini and other temples of Nepal, the Chardham Yatra and Kailash Mansarovar, for travellers from India and Nepal. Prices in INR and NPR.',
        'share_image' => 'site/hero-bg.webp',
        'organization_name' => 'OnThrill',
        'geo_region' => '',        // e.g. NP-P3 (Bagmati Province)
        'geo_placename' => '',     // e.g. Kathmandu
        'geo_latitude' => '',
        'geo_longitude' => '',
        'google_site_verification' => '',
        'bing_site_verification' => '',
        'google_analytics_id' => '',
    ],
];
