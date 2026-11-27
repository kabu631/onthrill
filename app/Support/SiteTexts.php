<?php

namespace App\Support;

/**
 * Every piece of wording on the public website that is not part of a tour,
 * destination or page record: headings, button labels, form labels, notes.
 * Edited in Admin > Website > Page Content; these are the starting values.
 *
 * Layout: admin tab => section => key => [label in the admin, default text, long text?]
 * Words starting with a colon (":name", ":count") are filled in by the website.
 */
class SiteTexts
{
    public const GROUPS = [
        'Header & footer' => [
            'Top bar' => [
                'top_prices_in' => ['Before the INR / NPR switch', 'Prices in'],
                'top_sign_in' => ['Sign in link', 'Sign in'],
                'top_my_account' => ['Link when signed in', 'My Account'],
            ],
            'Main menu' => [
                'menu_destinations' => ['Destinations menu', 'Destinations'],
                'menu_packages' => ['Tour packages menu', 'Tour Packages'],
                'menu_contact' => ['Contact menu', 'Contact'],
                'menu_all_destinations' => ['Link to all destinations', 'All destinations'],
                'menu_all_packages' => ['Link to all packages', 'All packages'],
                'menu_places_one' => ['Number of places (one)', ':count place'],
                'menu_places_many' => ['Number of places (several)', ':count places'],
                'menu_destinations_note' => ['Note at the bottom of the destinations menu', 'Altitude, best season and how to get there, for every place we visit.'],
                'menu_book_label' => ['Button on the right', 'Book a Tour'],
                'menu_book_link' => ['Button link', '/packages'],
                'search_placeholder' => ['Search box placeholder', 'Search Muktinath, Chardham, Kailash…'],
            ],
            'Footer' => [
                'footer_destinations_title' => ['Destinations column heading', 'Destinations'],
                'footer_packages_title' => ['Tour packages column heading', 'Tour Packages'],
                'footer_links_title' => ['Links column heading', 'Useful Links'],
                'footer_contact_title' => ['Contact column heading', 'Get in Touch'],
                'footer_all_packages' => ['Link to all packages', 'All tour packages'],
                'footer_call_label' => ['Label above the phone number', 'Call or WhatsApp'],
                'footer_email_label' => ['Label above the email', 'Email'],
                'footer_office_label' => ['Label above the address', 'Office'],
                'footer_hours_label' => ['Label above the office hours', 'Office hours'],
                'footer_button' => ['Button', 'Send an enquiry'],
                'footer_copyright' => ['Copyright line', 'Copyright © :year :site. All rights reserved.'],
                'footer_credits' => ['Photo credits link', 'Photographs from Wikimedia Commons'],
            ],
            'WhatsApp' => [
                'whatsapp_greeting' => ['Message that opens in WhatsApp', 'Namaste, I would like to plan a tour.'],
            ],
        ],

        'Homepage' => [
            'Tour search bar' => [
                'search_where' => ['Where to: label', 'Where to?'],
                'search_where_placeholder' => ['Where to: placeholder', 'Temple, place or tour name'],
                'search_destination' => ['Destination label', 'Destination'],
                'search_anywhere' => ['Any destination', 'Anywhere'],
                'search_type' => ['Tour type label', 'Type of tour'],
                'search_all_types' => ['Any tour type', 'All tours'],
                'search_duration' => ['Length label', 'Duration'],
                'search_any_length' => ['Any length', 'Any length'],
                'search_budget' => ['Budget label', 'Budget'],
                'search_any_budget' => ['Any budget', 'Any budget'],
                'search_button' => ['Button', 'Search tours'],
                'search_price_note' => ['Note under the bar', 'Prices are shown in :currency.'],
                'search_price_switch' => ['Link to switch currency', 'Show in :currency'],
            ],
            'Numbers under the search bar' => [
                'fact_places' => ['Under the number of places', 'Temples and sacred places'],
                'fact_packages' => ['Under the number of packages', 'Tour packages, from day trips to two-week yatras'],
                'fact_countries' => ['Under the number of countries', 'Countries: :list'],
                'fact_currency_value' => ['Currencies', 'INR & NPR'],
                'fact_currency' => ['Under the currencies', 'Pay by UPI, eSewa, Khalti or bank'],
            ],
            'Buttons and links' => [
                'home_destinations_link' => ['Destinations: link beside the heading', 'All destinations'],
                'home_destinations_tile' => ['Destinations: last tile text', 'sacred places in :countries'],
                'home_destinations_tile_link' => ['Destinations: last tile link', 'See all destinations'],
                'home_packages_all_tab' => ['Packages: first tab', 'All tours'],
                'home_packages_button' => ['Packages: button under the cards', 'View all :count tour packages'],
                'home_why_button' => ['Why us: first button', 'About us'],
                'home_why_button_link' => ['Why us: first button link', '/about-us'],
                'home_why_button2' => ['Why us: second button (contact page)', 'Talk to us'],
                'home_gallery_link' => ['Gallery: link beside the heading', 'View all photos'],
                'home_faq_box_title' => ['Questions: box heading', 'Still have a question?'],
                'home_faq_box_text' => ['Questions: box text', 'We usually reply the same day.'],
                'home_faq_box_button' => ['Questions: box button', 'Send an enquiry'],
                'home_cta_button' => ['Call-to-action: button', 'Send an enquiry'],
            ],
            'Small labels above the section titles' => [
                'eyebrow_destinations' => ['Destinations', 'Explore'],
                'eyebrow_packages' => ['Tour packages', 'Featured tours'],
                'eyebrow_why' => ['Why us', 'Why choose us'],
                'eyebrow_how' => ['How booking works', 'Easy booking'],
                'eyebrow_testimonials' => ['Testimonials', 'Reviews'],
                'eyebrow_gallery' => ['Gallery', 'Memories'],
                'eyebrow_faq' => ['Questions', 'Helpful answers'],
                'eyebrow_cta' => ['Call-to-action', 'Plan your yatra'],
            ],
        ],

        'Destinations' => [
            'Destinations page' => [
                'destinations_page_title' => ['Page title', 'Destinations'],
                'destinations_page_intro' => ['Intro text', 'Temples and sacred places in Nepal, the Chardham of Uttarakhand in India, and Kailash Mansarovar in Tibet.', true],
                'dest_tab_all' => ['First tab', 'All destinations'],
                'dest_empty' => ['When there are none', 'Destinations will be added soon.'],
            ],
            'Country pages' => [
                'country_page_title' => ['Page title', 'Temples and Sacred Places in :country'],
                'country_intro_nepal' => ['Nepal intro', 'Hindu and Buddhist pilgrimage places in Nepal, from the temples of Kathmandu to Muktinath in the Himalaya and Lumbini in the plains.', true],
                'country_intro_india' => ['India intro', 'The four temples of the Chardham Yatra in Uttarakhand: Yamunotri, Gangotri, Kedarnath and Badrinath.', true],
                'country_intro_tibet' => ['Tibet intro', 'Mount Kailash and Lake Mansarovar in western Tibet, reached from Nepal.', true],
            ],
            'Destination cards' => [
                'card_tours_one' => ['Number of tours (one)', ':count tour'],
                'card_tours_many' => ['Number of tours (several)', ':count tours'],
                'card_about' => ['When no tour visits it yet', 'About this place'],
            ],
            'Each destination page' => [
                'dest_about_title' => ['Description heading', 'About :name'],
                'dest_photos_title' => ['Photos heading', 'Photos'],
                'dest_reach_title' => ['How to reach heading', 'How to reach :name'],
                'dest_faq_title' => ['Questions heading', 'Questions about :name'],
                'dest_fact_altitude' => ['Fact: altitude', 'Altitude'],
                'dest_fact_best_time' => ['Fact: best time', 'Best time to visit'],
                'dest_fact_hours' => ['Fact: opening hours', 'Opening hours'],
                'dest_fact_airport' => ['Fact: nearest airport', 'Nearest airport'],
                'dest_tours_title' => ['Tours section heading', 'Tours that visit :name'],
                'dest_nearby_title' => ['More places heading', 'More sacred places in :country'],
                'dest_nearby_link' => ['Link beside it', 'All of :country'],
                'dest_map_link' => ['Map link', 'Open in Google Maps'],
            ],
            'Enquiry box on destination pages' => [
                'dest_enquiry_title' => ['Heading', 'Plan a visit to :name'],
                'dest_enquiry_text' => ['Text', 'Tell us your dates and who is travelling. We will reply with a plan and a price.', true],
                'dest_tours_button' => ['Button to the tours (when there are tours)', 'Tours to :name (:count)'],
            ],
        ],

        'Tour packages' => [
            'Packages page' => [
                'packages_title' => ['Page title', 'Tour Packages'],
                'packages_intro' => ['Intro text', 'Pilgrimage and cultural tours with hotels, transport and darshan arranged. Prices are per person.', true],
                'list_destination_title' => ['Title when showing one destination', 'Tours to :name'],
                'list_destination_intro' => ['Intro when showing one destination', 'Tour packages that include :name.'],
            ],
            'Filters and sorting' => [
                'list_filter_button' => ['Filter button (phones)', 'Filter tours'],
                'list_type_title' => ['Tour type heading', 'Tour type'],
                'list_all_types' => ['All tour types', 'All tours'],
                'list_destination_heading' => ['Destination heading', 'Destination'],
                'list_anywhere' => ['Any destination', 'Anywhere'],
                'list_search_title' => ['Search heading', 'Search tours'],
                'list_search_placeholder' => ['Search placeholder', 'Place, temple or tour'],
                'list_budget_title' => ['Budget heading', 'Budget per person'],
                'list_any_budget' => ['Any budget', 'Any budget'],
                'budget_under' => ['Budget: lowest range', 'Under :amount'],
                'budget_between' => ['Budget: middle ranges', ':from to :to'],
                'budget_over' => ['Budget: highest range', 'Over :amount'],
                'list_length_title' => ['Length heading', 'Length of tour'],
                'list_any_length' => ['Any length', 'Any length'],
                'duration_short' => ['Short tours', 'Up to 5 days'],
                'duration_medium' => ['Medium tours', '6 to 10 days'],
                'duration_long' => ['Long tours', '11 days or more'],
                'list_clear' => ['Clear filters link', 'Clear filters'],
                'list_found_one' => ['Number found (one)', ':count tour found'],
                'list_found_many' => ['Number found (several)', ':count tours found'],
                'list_sort' => ['Sort label', 'Sort by'],
                'list_view_label' => ['List or grid buttons (screen readers)', 'Show tours as'],
                'list_view_list' => ['List button', 'List'],
                'list_view_grid' => ['Grid button', 'Grid'],
                'sort_recommended' => ['Sort: recommended', 'Recommended'],
                'sort_duration' => ['Sort: length', 'Shortest first'],
                'sort_price_low' => ['Sort: cheapest', 'Price: low to high'],
                'sort_price_high' => ['Sort: dearest', 'Price: high to low'],
            ],
            'Help box and empty results' => [
                'list_help_title' => ['Help box heading', 'Need a different plan?'],
                'list_help_text' => ['Help box text', 'We can change any tour or plan one around your dates.'],
                'list_help_button' => ['Help box button', 'Ask us'],
                'list_empty_title' => ['No results heading', 'No tours match these filters'],
                'list_empty_text' => ['No results text', 'Try another destination or length, or tell us where you would like to go and we will plan it.', true],
                'list_empty_button' => ['No results button', 'Send an enquiry'],
            ],
            'Package cards and prices' => [
                'price_from' => ['Before the price', 'From'],
                'price_per_person' => ['After the price', 'per person'],
                'price_on_request' => ['Instead of a price', 'Price on request'],
                'price_off' => ['Offer badge', ':percent% off'],
                'card_view' => ['Card button', 'View Tour'],
                'card_enquire' => ['Card button (price on request)', 'Enquire'],
            ],
            'Each package page' => [
                'pkg_tab_overview' => ['Tab: overview', 'Overview'],
                'pkg_tab_itinerary' => ['Tab: itinerary', 'Itinerary'],
                'pkg_tab_included' => ['Tab: included', 'Included'],
                'pkg_tab_before' => ['Tab: before you go', 'Before you go'],
                'pkg_tab_places' => ['Tab: places', 'Places'],
                'pkg_tab_faqs' => ['Tab: questions', 'FAQs'],
                'pkg_tab_book' => ['Tab: booking', 'Book'],
                'pkg_overview_title' => ['Overview heading', 'Overview'],
                'pkg_fact_duration' => ['Fact: duration', 'Duration'],
                'pkg_fact_route' => ['Fact: start and end', 'Starts / ends'],
                'pkg_fact_season' => ['Fact: season', 'Best season'],
                'pkg_fact_group' => ['Fact: group size', 'Group size'],
                'pkg_fact_difficulty' => ['Fact: difficulty', 'Difficulty'],
                'pkg_fact_altitude' => ['Fact: highest point', 'Highest point'],
                'pkg_highlights_title' => ['Highlights heading', 'Highlights'],
                'pkg_itinerary_title' => ['Itinerary heading', 'Day-by-day itinerary'],
                'pkg_open_all' => ['Open all days', 'Open all days'],
                'pkg_close_all' => ['Close all days', 'Close all days'],
                'pkg_day' => ['Word above the day number', 'Day'],
                'pkg_included_title' => ['Inclusions heading', 'What is included'],
                'pkg_included' => ['Included list heading', 'Included'],
                'pkg_excluded' => ['Not included list heading', 'Not included'],
                'pkg_before_title' => ['Before you go heading', 'Before you go'],
                'pkg_places_title' => ['Places heading', 'Places on this tour'],
                'pkg_faq_title' => ['Questions heading', 'Questions about this tour'],
                'pkg_similar_title' => ['Similar tours heading', 'Similar tours'],
            ],
            'Price box' => [
                'price_shown_in' => ['Currency note', 'Shown in :currency'],
                'price_switch' => ['Switch currency link', 'show in :currency'],
                'price_advance' => ['Advance note', 'An advance of :percent% confirms the booking. The rest is paid before you travel.', true],
                'price_request_note' => ['Under "Price on request"', 'It depends on the season and your nationality.'],
                'price_request_text' => ['Price on request text', 'Send a request and we will send you the exact price.'],
                'price_book_button' => ['Book button', 'Book this tour'],
                'price_request_button' => ['Button when the price is on request', 'Request a price'],
                'price_whatsapp' => ['WhatsApp link', 'Ask on WhatsApp'],
                'mobile_book_button' => ['Book button in the bar at the bottom of phones', 'Book now'],
            ],
            'Booking form' => [
                'booking_title' => ['Heading', 'Book this tour'],
                'booking_title_request' => ['Heading when the price is on request', 'Request a price for this tour'],
                'booking_note' => ['Note under the heading', 'We confirm every booking by phone before you pay anything.'],
                'booking_date' => ['Travel date', 'Travel date'],
                'booking_adults' => ['Adults', 'Adults'],
                'booking_children' => ['Children', 'Children'],
                'booking_nationality' => ['Nationality', 'Nationality'],
                'booking_nationality_note' => ['Note under nationality', 'Indian travellers pay in Indian rupees (UPI or bank); Nepali travellers in Nepali rupees (eSewa, Khalti or bank).', true],
                'booking_name' => ['Full name', 'Full name'],
                'booking_phone' => ['Phone', 'Phone / WhatsApp'],
                'booking_email' => ['Email', 'Email'],
                'booking_email_hint' => ['Note under email', 'Your booking details are sent here.'],
                'booking_from' => ['Travelling from', 'Travelling from'],
                'booking_from_placeholder' => ['Travelling from placeholder', 'City'],
                'booking_notes' => ['Message', 'Anything we should know?'],
                'booking_notes_placeholder' => ['Message placeholder', 'Elderly travellers, hotel preferences, special puja… (optional)'],
                'booking_estimate' => ['Price estimate label', 'Estimated total for'],
                'booking_traveller' => ['Traveller (one)', 'traveller'],
                'booking_travellers' => ['Travellers (several)', 'travellers'],
                'booking_advance' => ['Advance label in the estimate', 'advance'],
                'booking_button' => ['Button', 'Send booking request'],
                'booking_terms' => ['Note under the button', 'Nothing is charged now. By sending this request you agree to our'],
                'booking_terms_link' => ['Terms link text', 'Terms'],
            ],
        ],

        'Enquiries & contact' => [
            'Contact page' => [
                'contact_title' => ['Page title', 'Contact Us'],
                'contact_intro' => ['Intro text', 'Tell us where you want to go and when. We usually reply the same day.', true],
                'contact_form_title' => ['Form heading', 'Your Enquiry'],
                'contact_required' => ['Required fields note', 'Fields marked * are required.'],
                'contact_label_phone' => ['Label: phone', 'Phone'],
                'contact_label_whatsapp' => ['Label: WhatsApp', 'WhatsApp'],
                'contact_label_email' => ['Label: email', 'Email'],
                'contact_label_address' => ['Label: address', 'Address'],
                'contact_label_hours' => ['Label: hours', 'Hours'],
                'contact_whatsapp_button' => ['WhatsApp button', 'Chat on WhatsApp'],
                'contact_map_title' => ['Map heading', 'Find us on the map'],
                'contact_directions' => ['Directions button', 'Get directions'],
                'contact_email_note' => ['Under the email', 'We usually reply the same day.'],
                'contact_office_badge' => ['Label above the office name', 'Head office'],
                'contact_privacy' => ['Note under the form', 'Your contact details are only used to plan your tour.'],
            ],
            'Enquiry form (contact page and the side of tour pages)' => [
                'enquiry_name' => ['Name', 'Your name'],
                'enquiry_phone' => ['Phone', 'Phone / WhatsApp'],
                'enquiry_email' => ['Email', 'Email'],
                'enquiry_topic' => ['Topic list (contact page)', 'Interested in'],
                'enquiry_general' => ['General enquiry option', 'General enquiry'],
                'enquiry_tour_label' => ['Label of the tour it is about', 'Tour'],
                'enquiry_place_label' => ['Label of the place it is about', 'Destination'],
                'enquiry_message' => ['Message', 'Message'],
                'enquiry_placeholder' => ['Message placeholder', 'Where would you like to go, when, and how many people are travelling?'],
                'enquiry_button' => ['Button', 'Send Enquiry'],
                'form_sending' => ['While sending (all forms)', 'Sending…'],
                'form_error' => ['When a field is wrong (all forms)', 'Please check the highlighted fields and try again.'],
                'inquiry_success' => ['After sending', 'Thank you. We have received your enquiry and will contact you soon.', true],
            ],
            'Enquiry box on package pages' => [
                'pkg_enquiry_title' => ['Heading', 'Enquire about this tour'],
                'pkg_enquiry_text' => ['Text', 'Ask about dates, hotels or a change to the itinerary. We usually reply the same day.', true],
            ],
            'Emails to travellers' => [
                'email_greeting' => ['Greeting', 'Namaste :name,'],
                'email_booking_intro' => ['Booking email: first paragraph', 'Thank you for your booking request. Your reference is :reference. We will call you on :phone to confirm the dates, hotels and final price. Nothing needs to be paid until then.', true],
                'email_pay_title' => ['Booking email: payment heading', 'Paying the advance'],
                'email_row_advance' => ['Booking email: advance row', 'Advance to confirm'],
                'email_view_booking' => ['Booking email: button (signed-in travellers)', 'View your booking'],
                'email_enquiry_intro' => ['Enquiry reply: first paragraph', 'Thank you for contacting :site. We have received your enquiry and will get back to you on :phone soon.', true],
                'email_call_us' => ['Line with the phone number', 'Questions? Call or WhatsApp us on :phone.'],
                'email_website_button' => ['Website button', 'Visit our website'],
                'email_signoff' => ['Sign-off', 'Shubh yatra,'],
            ],
        ],

        'Other pages' => [
            'Gallery page' => [
                'gallery_title' => ['Page title', 'Gallery'],
                'gallery_intro' => ['Intro text', 'Photos from the temples and sacred places our tours visit.', true],
                'gallery_empty' => ['When there are no photos', 'Photos will be added soon.'],
            ],
            'Information pages: sidebar (About, Travel Information, Privacy…)' => [
                'page_sidebar_pages' => ['List of pages: heading', 'More information'],
                'page_sidebar_help_title' => ['Help box: heading', 'Planning a pilgrimage?'],
                'page_sidebar_help_text' => ['Help box: text', 'Tell us where you would like to go and when. We will suggest a route and send you a price.', true],
                'page_sidebar_help_button' => ['Help box: button', 'Send an enquiry'],
                'page_sidebar_tours' => ['Popular tours: heading', 'Popular tours'],
                'page_sidebar_all_tours' => ['Popular tours: link', 'All tour packages'],
            ],
            'On several pages' => [
                'currency_inr_name' => ['Name of Indian currency in sentences', 'Indian rupees'],
                'currency_npr_name' => ['Name of Nepali currency in sentences', 'Nepali rupees'],
                'breadcrumb_home' => ['First link of the breadcrumbs', 'Home'],
                'last_updated' => ['Date line at the end of pages', 'Last updated :date'],
                'photos_count' => ['Photo count button (phones)', ':count photos'],
                'photos_more' => ['On the last photo when there are more', '+:count photos'],
                'photo_credits_link' => ['Link after a photo credit', 'all photo credits'],
            ],
            'Booking confirmation page' => [
                'bookingpage_title' => ['Page title', 'Booking :reference'],
                'bookingpage_thanks' => ['Heading', 'Thank you, :name'],
                'bookingpage_received' => ['Text', 'We have received your booking request :reference. A copy has been sent to :email, and we will call you on :phone to confirm the details and the final price.', true],
                'bookingpage_details' => ['Details heading', 'Your booking'],
                'bookingpage_row_tour' => ['Row: tour', 'Tour'],
                'bookingpage_row_date' => ['Row: travel date', 'Travel date'],
                'bookingpage_row_travellers' => ['Row: travellers', 'Travellers'],
                'bookingpage_row_nationality' => ['Row: nationality', 'Nationality'],
                'bookingpage_row_from' => ['Row: travelling from', 'Travelling from'],
                'bookingpage_row_total' => ['Row: total', 'Estimated total'],
                'bookingpage_row_status' => ['Row: status', 'Status'],
                'bookingpage_home' => ['Button: home', 'Back to Home'],
                'bookingpage_account' => ['Button: account', 'My Bookings'],
            ],
            'Payment details (booking page)' => [
                'pay_title' => ['Heading', 'Paying the advance'],
                'pay_text' => ['Text', 'After we confirm your booking by phone, pay :amount and mention booking reference :reference.', true],
                'pay_text_request' => ['Text when the price is on request', 'We will send you the price and the advance to pay. Mention booking reference :reference when you pay.', true],
                'pay_scan_upi' => ['Under the UPI QR code', 'Scan with any UPI app'],
                'pay_scan_nepal' => ['Under the Nepal QR code', 'Scan with eSewa or Khalti'],
                'pay_copy' => ['Copy button', 'Copy'],
                'pay_copied' => ['After copying', 'Copied'],
                'pay_name' => ['Name label', 'Name'],
                'pay_upi_button' => ['UPI app button (phones)', 'Pay :amount with a UPI app'],
                'pay_bank' => ['Bank heading', 'Bank transfer'],
                'pay_none' => ['When no payment details are set', 'Our team will share the payment details with you when we call.'],
                'pay_screenshot' => ['WhatsApp button', 'Send payment screenshot'],
            ],
            'Search' => [
                'search_title' => ['Search page title', 'Search'],
                'search_page_button' => ['Search button', 'Search'],
                'search_none_title' => ['No results heading', 'No results for “:term”'],
                'search_none_text' => ['No results text', 'Try a different word, or ask us. We can plan a tour to places that are not listed here.', true],
                'search_packages' => ['Packages heading', 'Tour packages'],
                'search_destinations' => ['Destinations heading', 'Destinations'],
                'search_types' => ['Tour types heading', 'Tour types'],
                'search_popular' => ['Before typing: heading', 'Popular searches'],
                'search_keys' => ['Keyboard hint', '↑ ↓ to choose, Enter to open'],
                'search_too_short' => ['When fewer than 2 letters', 'Please type at least 2 characters.'],
                'search_searching' => ['While searching', 'Searching…'],
                'search_all_results' => ['Link to all results', 'See all results for “:term”'],
            ],
            'Page not found' => [
                'e404_title' => ['Heading', 'This page could not be found'],
                'e404_text' => ['Text', 'The link may be old or mistyped. Try searching, or start from our destinations and tour packages.', true],
                'e404_home' => ['Home button', 'Go to Home'],
            ],
            'Sign in and account' => [
                'login_title' => ['Sign in: heading', 'Sign in'],
                'login_intro' => ['Sign in: text', 'View your tour bookings.'],
                'login_forgot' => ['Sign in: forgot link', 'Forgot password?'],
                'login_new' => ['Sign in: new visitor text', 'New here?'],
                'login_create' => ['Sign in: register link', 'Create an account'],
                'register_title' => ['Register: heading', 'Create an account'],
                'register_intro' => ['Register: text', 'See all your booking requests in one place.'],
                'register_button' => ['Register: button', 'Create account'],
                'register_have' => ['Register: already registered text', 'Already registered?'],
                'forgot_title' => ['Forgot password: heading', 'Forgot your password?'],
                'forgot_intro' => ['Forgot password: text', 'Enter the email you signed up with and we will send you a link to choose a new password.', true],
                'forgot_button' => ['Forgot password: button', 'Email me a link'],
                'forgot_back' => ['Forgot password: back text', 'Remembered it?'],
                'reset_title' => ['New password: heading', 'Choose a new password'],
                'reset_intro' => ['New password: text', 'At least 8 characters. You will be signed in straight away.'],
                'reset_button' => ['New password: button', 'Save new password'],
                'account_title' => ['Account: page title', 'My Account'],
                'account_bookings' => ['Account: bookings heading', 'My Bookings'],
                'account_empty' => ['Account: no bookings yet', 'You have not made any bookings yet.'],
                'account_browse' => ['Account: button when empty', 'Browse Tour Packages'],
                'account_details' => ['Account: details heading', 'My Details'],
                'account_save' => ['Account: save button', 'Save changes'],
                'account_sign_out' => ['Account: sign out button', 'Sign out'],
                'account_change_password' => ['Account: change password link', 'Change password'],
                'field_full_name' => ['Field: full name', 'Full name'],
                'field_email' => ['Field: email', 'Email'],
                'field_phone' => ['Field: phone', 'Phone / WhatsApp'],
                'field_password' => ['Field: password', 'Password'],
                'field_password_hint' => ['Note under a new password', 'At least 8 characters.'],
                'field_confirm_password' => ['Field: confirm password', 'Confirm password'],
                'field_current_password' => ['Field: current password', 'Current password'],
                'field_new_password' => ['Field: new password', 'New password'],
            ],
        ],
    ];

    private static ?array $defaults = null;

    /** @return array<string, string> key => starting text */
    public static function defaults(): array
    {
        if (self::$defaults === null) {
            self::$defaults = [];
            foreach (self::GROUPS as $sections) {
                foreach ($sections as $fields) {
                    foreach ($fields as $key => $field) {
                        self::$defaults[$key] = $field[1];
                    }
                }
            }
        }

        return self::$defaults;
    }

    /** @return array<string, string> every key with its current wording (sent to the website app) */
    public static function all(): array
    {
        return collect(array_keys(self::defaults()))->mapWithKeys(fn ($key) => [$key => self::get($key)])->all();
    }

    /** The saved wording, or the starting text when the field was left empty. */
    public static function get(string $key): string
    {
        $text = trim((string) Settings::get($key, ''));

        return $text !== '' ? $text : (self::defaults()[$key] ?? '');
    }
}
