<?php

/*
| Destinations seeded on install. Edit them in Admin > Tours > Destinations.
| Coordinates are left empty where they were not certain; add them in the admin
| (they are used for the map links and for search engines).
| `image` refers to files in database/seeders/assets/destinations.
*/

return [
    // ------------------------------------------------------------------ Nepal
    [
        'name' => 'Pashupatinath Temple',
        'slug' => 'pashupatinath-temple',
        'image' => 'pashupatinath',
        'country' => 'Nepal',
        'region' => 'Kathmandu, Bagmati Province',
        'summary' => 'The most important Shiva temple in Nepal, on the banks of the Bagmati River in Kathmandu.',
        'description' => <<<'HTML'
<p>Pashupatinath is a temple of Lord Shiva as Pashupati, the lord of all living beings. It stands on the west bank of the Bagmati River in the east of Kathmandu, a short drive from Tribhuvan International Airport, and is part of the Kathmandu Valley UNESCO World Heritage Site.</p>
<p>The main temple is a two-tiered pagoda with a gilded roof and silver doors. Only Hindus may enter the courtyard of the main temple; other visitors can see it from the terraces on the east bank of the river. Along the river are the cremation ghats, smaller shrines and the rows of stone Shiva lingams that pilgrims walk past on the way to the temple.</p>
<p>The Bagmati aarti is held on the ghats every evening and is the best time to visit after the morning darshan. Maha Shivaratri, in February or March, brings very large crowds, including sadhus who come from all over Nepal and India.</p>
HTML,
        'best_time' => 'October to April; Maha Shivaratri (February or March) for the festival',
        'opening_hours' => 'Early morning to evening; evening aarti on the ghats every day',
        'nearest_airport' => 'Tribhuvan International Airport, Kathmandu (about 2 km)',
        'how_to_reach' => 'The temple is in the city. It is about 15 minutes by car from Thamel and the airport.',
        'latitude' => 27.7104, 'longitude' => 85.3488,
        'faqs' => [
            ['question' => 'Can non-Hindus enter Pashupatinath Temple?', 'answer' => 'Only Hindus may enter the courtyard of the main temple. Everyone can visit the riverside, the ghats and the terraces on the east bank, from where the temple can be seen.'],
            ['question' => 'When is the evening aarti at Pashupatinath?', 'answer' => 'The aarti is held on the ghats of the Bagmati every evening around sunset. Arrive half an hour early to find a place to sit.'],
        ],
        'featured' => true,
    ],
    [
        'name' => 'Doleshwor Mahadev',
        'slug' => 'doleshwor-mahadev',
        'image' => 'doleshwor-mahadev',
        'country' => 'Nepal',
        'region' => 'Bhaktapur, Bagmati Province',
        'summary' => 'A Shiva temple in Bhaktapur district that devotees believe is the head of Kedarnath.',
        'description' => <<<'HTML'
<p>Doleshwor Mahadev is a Shiva temple at Sipadol in Bhaktapur district, south-east of Kathmandu. According to the story of the Pandavas, Lord Shiva took the form of a bull to avoid them; the bull's hump remained at Kedarnath in the Himalaya and its head appeared here. For this reason many devotees regard Doleshwor as the head of Kedarnath, and pilgrims who have been to Kedarnath often come here to complete their darshan.</p>
<p>The temple is small and quiet compared with the big temples of the valley, with a golden Nandi facing the shrine. It is usually visited on the same day as Pashupatinath or Bhaktapur Durbar Square.</p>
HTML,
        'best_time' => 'All year; Shravan month and Maha Shivaratri are the busiest times',
        'opening_hours' => 'Morning to evening',
        'nearest_airport' => 'Tribhuvan International Airport, Kathmandu',
        'how_to_reach' => 'About an hour by car from central Kathmandu, through Bhaktapur.',
        'latitude' => null, 'longitude' => null,
        'faqs' => [
            ['question' => 'Why is Doleshwor Mahadev linked with Kedarnath?', 'answer' => 'In the Pandava legend, Shiva hid from the Pandavas as a bull. The hump stayed at Kedarnath and the head appeared at Doleshwor, so devotees see the two temples as parts of one darshan.'],
        ],
        'featured' => false,
    ],
    [
        'name' => 'Muktinath Temple',
        'slug' => 'muktinath-temple',
        'image' => 'muktinath',
        'country' => 'Nepal',
        'region' => 'Mustang, Gandaki Province',
        'summary' => 'A temple of Lord Vishnu at 3,710 m in Mustang, sacred to Hindus and Buddhists, known for its 108 water spouts.',
        'description' => <<<'HTML'
<p>Muktinath stands at 3,710 m in Mustang district, below the Thorong La pass in the Annapurna region. For Hindus it is a temple of Lord Vishnu and one of the 108 Divya Desams; for Buddhists it is Chumig Gyatsa, a sacred place of Guru Rinpoche. The name means place of liberation.</p>
<p>Behind the pagoda temple are 108 water spouts shaped like cow heads, called Muktidhara. Pilgrims bathe under each of them, and in the two kunds in front of the temple, before darshan. A short walk away is the Jwala Mai temple, where a small natural gas flame burns beside running water.</p>
<p>Most pilgrims fly from Pokhara to Jomsom and drive up to Ranipauwa, the village below the temple, then walk or ride a horse for the last stretch. The road from Pokhara through Beni and the Kali Gandaki valley is open too, but it is long and rough. Because of the altitude, take it slowly on arrival and drink plenty of water.</p>
HTML,
        'altitude_m' => 3710,
        'best_time' => 'March to June and September to November',
        'opening_hours' => 'Morning to late afternoon',
        'nearest_airport' => 'Jomsom (JMO), with flights from Pokhara (PKR)',
        'how_to_reach' => 'Fly Pokhara to Jomsom (about 20 minutes), then drive about 1½ hours to Ranipauwa and walk 15 to 20 minutes up to the temple. By road, Pokhara to Muktinath takes a full day each way.',
        'latitude' => 28.8166, 'longitude' => 83.8716,
        'faqs' => [
            ['question' => 'Do I need a permit to visit Muktinath?', 'answer' => 'Muktinath is inside the Annapurna Conservation Area. Indian and other foreign visitors need the conservation area entry permit; Nepali citizens do not. We arrange it with the booking.'],
            ['question' => 'Is Muktinath suitable for elderly pilgrims?', 'answer' => 'Many elderly pilgrims go every year. Flying to Jomsom avoids the long road, horses are available for the walk to the temple, and we plan an easy first day at altitude. Check with your doctor if you have heart or breathing problems.'],
        ],
        'featured' => true,
    ],
    [
        'name' => 'Manakamana Temple',
        'slug' => 'manakamana-temple',
        'image' => 'manakamana',
        'country' => 'Nepal',
        'region' => 'Gorkha, Gandaki Province',
        'summary' => 'A hilltop temple of the goddess Bhagawati in Gorkha, reached by cable car from the Prithvi Highway.',
        'description' => <<<'HTML'
<p>Manakamana is a temple of the goddess Bhagawati, a form of Parvati, on a ridge in Gorkha district. Her name means the goddess who fulfils the wishes of the heart, and devotees come to ask for her blessing before weddings, new ventures and journeys.</p>
<p>The pagoda temple stands in a paved square at about 1,300 m, with views across the Trishuli and Marsyangdi valleys. Most visitors take the cable car from Kurintar on the Prithvi Highway, a ride of about ten minutes over the forest. Saturdays and the Dashain and Nag Panchami festivals bring long queues for darshan; weekday mornings are quieter.</p>
HTML,
        'altitude_m' => 1302,
        'best_time' => 'All year; weekday mornings are the least crowded',
        'opening_hours' => 'Cable car runs from morning until late afternoon',
        'nearest_airport' => 'Tribhuvan International Airport, Kathmandu, or Pokhara',
        'how_to_reach' => 'Drive 3 to 4 hours from Kathmandu or Pokhara to Kurintar on the Prithvi Highway, then take the cable car up to the temple.',
        'latitude' => 27.9045, 'longitude' => 84.5835,
        'faqs' => [
            ['question' => 'How long is the Manakamana cable car ride?', 'answer' => 'About ten minutes each way from the station at Kurintar. Allow two to three hours for the ride, the queue for darshan and the return.'],
        ],
        'featured' => true,
    ],
    [
        'name' => 'Galeshwor Dham',
        'slug' => 'galeshwor-dham',
        'image' => 'galeshwor-dham',
        'country' => 'Nepal',
        'region' => 'Myagdi, Gandaki Province',
        'summary' => 'A Shiva temple built on a large rock beside the Kali Gandaki, on the road from Beni to Muktinath.',
        'description' => <<<'HTML'
<p>Galeshwor Dham is a temple of Lord Shiva in Myagdi district, on the bank of the Kali Gandaki river near Beni. The temple is built on top of a large rock, and the place is named after the sage Galav, who is said to have meditated here.</p>
<p>Pilgrims travelling to Muktinath by road usually stop here on the way, and many take a holy dip in the Kali Gandaki below the temple. The valley is warm at this height, so it is a pleasant halt before the road climbs towards Jomsom.</p>
HTML,
        'altitude_m' => null,
        'best_time' => 'October to May; landslides can block the road in the monsoon',
        'opening_hours' => 'Morning to evening',
        'nearest_airport' => 'Pokhara (PKR)',
        'how_to_reach' => 'About 3 hours by road from Pokhara, on the Beni to Jomsom road.',
        'latitude' => null, 'longitude' => null,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Lumbini',
        'slug' => 'lumbini',
        'image' => 'lumbini',
        'country' => 'Nepal',
        'region' => 'Rupandehi, Lumbini Province',
        'summary' => 'The birthplace of Lord Buddha, a UNESCO World Heritage Site in the plains of southern Nepal.',
        'description' => <<<'HTML'
<p>Lumbini is where Siddhartha Gautama, who became the Buddha, was born. It lies in the Terai plains of southern Nepal, close to the Indian border, and is a UNESCO World Heritage Site.</p>
<p>The Maya Devi Temple stands over the spot of the birth and shelters the marker stone found during excavations. Beside it are the sacred pond, Puskarini, and the pillar raised by Emperor Ashoka, whose inscription records his visit. To the north is the Monastic Zone, where Buddhist countries have built monasteries in their own styles, linked by a long canal to the Eternal Peace Flame.</p>
<p>The area is large and flat, so plan a full day and use a cycle rickshaw or electric cart between the sites. Summer is very hot; the winter months are the most comfortable.</p>
HTML,
        'best_time' => 'October to March; Buddha Jayanti in April or May',
        'opening_hours' => 'Sacred Garden from early morning to evening',
        'nearest_airport' => 'Gautam Buddha International Airport, Bhairahawa (about 20 km)',
        'how_to_reach' => 'From the Sunauli–Belahiya border crossing on the India–Nepal border, Lumbini is about an hour by road. Flights from Kathmandu land at Bhairahawa.',
        'latitude' => 27.4697, 'longitude' => 83.2759,
        'faqs' => [
            ['question' => 'How far is Lumbini from the Indian border?', 'answer' => 'About 30 km from the Sunauli–Belahiya border, roughly an hour by road. Many Indian travellers start their Nepal tour here.'],
        ],
        'featured' => true,
    ],
    [
        'name' => 'Swayambhunath Stupa',
        'slug' => 'swayambhunath-stupa',
        'image' => 'swayambhunath',
        'country' => 'Nepal',
        'region' => 'Kathmandu, Bagmati Province',
        'summary' => 'The ancient stupa on a hill west of Kathmandu, sacred to Buddhists and Hindus.',
        'description' => <<<'HTML'
<p>Swayambhunath is one of the oldest religious sites in the Kathmandu Valley, on a wooded hill on the western side of the city. The white dome of the stupa is crowned by a gilded tower painted with the eyes of the Buddha, and prayer flags run from it in every direction. It is part of the Kathmandu Valley UNESCO World Heritage Site.</p>
<p>The site is holy to both Buddhists and Hindus. Around the stupa are smaller shrines, including the temple of Harati, the goddess who protects children. A long stone stairway leads up from the east, and the terrace at the top looks out over the whole valley. The many monkeys on the hill give the place its other name, the Monkey Temple.</p>
HTML,
        'best_time' => 'October to April; early morning for the views',
        'opening_hours' => 'Open all day',
        'nearest_airport' => 'Tribhuvan International Airport, Kathmandu',
        'how_to_reach' => 'About 20 minutes by car from Thamel. A road also reaches the western side of the hill for those who prefer not to climb the stairs.',
        'latitude' => 27.7149, 'longitude' => 85.2904,
        'faqs' => [
            ['question' => 'Do I have to climb the stairs at Swayambhunath?', 'answer' => 'No. The eastern stairway is steep, but vehicles can reach the parking area near the top on the western side, from where it is a short walk.'],
        ],
        'featured' => false,
    ],
    [
        'name' => 'Dakshinkali Temple',
        'slug' => 'dakshinkali-temple',
        'image' => 'dakshinkali',
        'country' => 'Nepal',
        'region' => 'Pharping, Kathmandu, Bagmati Province',
        'summary' => 'A temple of the goddess Kali in a forested gorge near Pharping, south of Kathmandu.',
        'description' => <<<'HTML'
<p>Dakshinkali is a temple of the goddess Kali about 22 km south of Kathmandu, below the village of Pharping. The shrine is open to the sky and sits at the bottom of a forested gorge where two streams meet.</p>
<p>Tuesdays and Saturdays are the main days of worship, when the path down to the shrine is full of devotees carrying offerings; animal offerings are part of the tradition here. The temple is busiest during Dashain. Other days are much quieter.</p>
<p>Pharping, on the way, has Buddhist monasteries and caves linked with Guru Rinpoche, and the Shesh Narayan temple, so the trip makes a good half-day outing from the city.</p>
HTML,
        'best_time' => 'All year; Tuesdays, Saturdays and Dashain are the busiest days',
        'opening_hours' => 'Morning to evening',
        'nearest_airport' => 'Tribhuvan International Airport, Kathmandu',
        'how_to_reach' => 'About 1 to 1½ hours by car from central Kathmandu, through Pharping.',
        'latitude' => 27.6047, 'longitude' => 85.2622,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Bindhyabasini Temple',
        'slug' => 'bindhyabasini-temple',
        'image' => 'bindhyabasini',
        'country' => 'Nepal',
        'region' => 'Pokhara, Gandaki Province',
        'summary' => 'The best-known temple of old Pokhara, dedicated to the goddess Bhagawati, on a hillock with Himalayan views.',
        'description' => <<<'HTML'
<p>Bindhyabasini is a temple of the goddess Bhagawati, worshipped here as Bindhyabasini, in the old part of Pokhara. It stands on a small hill above the bazaar, in a courtyard with smaller temples of Shiva, Ganesh and Hanuman.</p>
<p>On a clear morning the Annapurna range and Machhapuchhre can be seen from the temple grounds. Saturdays and festival days are crowded, and the temple is a popular place for weddings and other ceremonies.</p>
HTML,
        'best_time' => 'October to April; mornings for the mountain views',
        'opening_hours' => 'Early morning to evening',
        'nearest_airport' => 'Pokhara (PKR)',
        'how_to_reach' => 'About 15 minutes by car from Lakeside in Pokhara.',
        'latitude' => 28.2387, 'longitude' => 83.9900,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Tal Barahi Temple',
        'slug' => 'tal-barahi-temple',
        'image' => 'tal-barahi',
        'country' => 'Nepal',
        'region' => 'Pokhara, Gandaki Province',
        'summary' => 'A two-tiered pagoda temple on a small island in Phewa Lake, reached by boat.',
        'description' => <<<'HTML'
<p>Tal Barahi is a temple of the goddess Barahi on a small island in Phewa Lake, Pokhara. The two-tiered pagoda is surrounded by trees, and the short boat ride from the Lakeside shore is part of the visit.</p>
<p>Devotees bring offerings on Saturdays, when the boats are busiest. On the way back many visitors row on the lake, which on clear days reflects the Annapurna range.</p>
HTML,
        'best_time' => 'October to April',
        'opening_hours' => 'Boats run from morning until early evening',
        'nearest_airport' => 'Pokhara (PKR)',
        'how_to_reach' => 'Take a boat from the Barahi ghat on Lakeside; the crossing takes 5 to 10 minutes.',
        'latitude' => 28.2083, 'longitude' => 83.9455,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Pumdikot Shiva Temple',
        'slug' => 'pumdikot-shiva-temple',
        'image' => 'pumdikot',
        'country' => 'Nepal',
        'region' => 'Pokhara, Gandaki Province',
        'summary' => 'A large statue of Lord Shiva on a hilltop above Phewa Lake, with views over Pokhara and the Annapurna range.',
        'description' => <<<'HTML'
<p>Pumdikot is a hilltop in Pumdi Bhumdi, on the southern side of Phewa Lake in Pokhara. A large seated statue of Lord Shiva holding his trident stands above a temple hall at the top, and it can be seen from much of the city.</p>
<p>The terrace around the statue looks over the lake, Pokhara and, on clear days, the full Annapurna range. Sunrise and sunset are the best times to go. The road climbs most of the way and is followed by a short walk.</p>
HTML,
        'best_time' => 'October to April; sunrise or sunset',
        'opening_hours' => 'Morning to evening',
        'nearest_airport' => 'Pokhara (PKR)',
        'how_to_reach' => 'About 45 minutes by car from Lakeside, Pokhara, then a short walk.',
        'latitude' => null, 'longitude' => null,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Gupteshwor Mahadev Cave',
        'slug' => 'gupteshwor-mahadev-cave',
        'image' => 'gupteshwor-mahadev',
        'country' => 'Nepal',
        'region' => 'Pokhara, Gandaki Province',
        'summary' => 'A cave shrine of Lord Shiva in Pokhara, opposite Devi’s Falls, where the cave leads to the falling water.',
        'description' => <<<'HTML'
<p>Gupteshwor Mahadev is a limestone cave in Pokhara with a shrine of Lord Shiva inside. The entrance is across the road from Devi’s Falls, down a spiral staircase.</p>
<p>The first chamber holds the Shiva lingam, where devotees offer worship. Beyond it, a passage continues through the cave to a point where the water of Devi’s Falls can be seen pouring in. The floor is wet and uneven, so wear shoes with a good grip. Photography is not allowed at the shrine.</p>
HTML,
        'best_time' => 'All year; after the monsoon the falls are at their strongest',
        'opening_hours' => 'Morning to evening',
        'nearest_airport' => 'Pokhara (PKR)',
        'how_to_reach' => 'About 15 minutes by car from Lakeside, Pokhara, beside Devi’s Falls.',
        'latitude' => 28.1898, 'longitude' => 83.9585,
        'faqs' => [],
        'featured' => false,
    ],

    // ---------------------------------------------------------- India (Chardham)
    [
        'name' => 'Yamunotri',
        'slug' => 'yamunotri',
        'image' => 'yamunotri',
        'country' => 'India',
        'region' => 'Uttarkashi, Uttarakhand',
        'summary' => 'The source of the Yamuna and the first temple of the Chardham Yatra, reached by a 6 km walk.',
        'description' => <<<'HTML'
<p>Yamunotri is the temple of the goddess Yamuna, near the source of the river in the Garhwal Himalaya. It is traditionally the first stop of the Chardham Yatra, which continues to Gangotri, Kedarnath and Badrinath.</p>
<p>The temple is reached by a walk of about 6 km from Janki Chatti, the last point on the road. Ponies and palkis are available for those who prefer not to walk. Next to the temple is Surya Kund, a hot spring where pilgrims cook rice and potatoes in cloth to take home as prasad.</p>
<p>Yamunotri opens each year on Akshaya Tritiya in April or May and closes on Bhai Dooj after Diwali.</p>
HTML,
        'altitude_m' => 3293,
        'best_time' => 'May to June and September to October',
        'opening_hours' => 'Open from Akshaya Tritiya (April or May) to Bhai Dooj (October or November)',
        'nearest_airport' => 'Jolly Grant Airport, Dehradun',
        'how_to_reach' => 'Drive from Haridwar or Dehradun to Barkot, continue to Janki Chatti, then walk or ride about 6 km to the temple.',
        'latitude' => 31.0136, 'longitude' => 78.4599,
        'faqs' => [
            ['question' => 'How long is the walk to Yamunotri?', 'answer' => 'About 6 km each way from Janki Chatti. Most people take 3 to 4 hours to go up. Ponies and palkis can be hired at the start.'],
        ],
        'featured' => false,
    ],
    [
        'name' => 'Gangotri',
        'slug' => 'gangotri',
        'image' => 'gangotri',
        'country' => 'India',
        'region' => 'Uttarkashi, Uttarakhand',
        'summary' => 'The temple of the goddess Ganga on the Bhagirathi river, the second temple of the Chardham Yatra.',
        'description' => <<<'HTML'
<p>Gangotri is the temple of the goddess Ganga, on the bank of the Bhagirathi river in Uttarkashi district. It is the second temple of the Chardham Yatra. The river's actual source, the Gaumukh glacier, lies about 19 km further up the valley.</p>
<p>Unlike Yamunotri and Kedarnath, Gangotri can be reached by road, so it is the easiest of the four temples to visit. The temple opens on Akshaya Tritiya and closes around Diwali, when the image of the goddess is taken down to Mukhba village for the winter.</p>
HTML,
        'altitude_m' => 3100,
        'best_time' => 'May to June and September to October',
        'opening_hours' => 'Open from Akshaya Tritiya (April or May) until around Diwali',
        'nearest_airport' => 'Jolly Grant Airport, Dehradun',
        'how_to_reach' => 'By road from Uttarkashi, about 100 km. There is no walk; vehicles reach the temple town.',
        'latitude' => 30.9946, 'longitude' => 78.9398,
        'faqs' => [],
        'featured' => false,
    ],
    [
        'name' => 'Kedarnath',
        'slug' => 'kedarnath',
        'image' => 'kedarnath',
        'country' => 'India',
        'region' => 'Rudraprayag, Uttarakhand',
        'summary' => 'One of the twelve Jyotirlingas of Lord Shiva, at 3,583 m below the Kedarnath peaks.',
        'description' => <<<'HTML'
<p>Kedarnath is one of the twelve Jyotirlingas of Lord Shiva and the most important of the Panch Kedar temples. The stone temple stands at 3,583 m at the head of the Mandakini valley, with snow peaks rising directly behind it.</p>
<p>The temple is reached by a walk of about 16 km from Gaurikund. Ponies, palkis and pithus can be hired, and helicopters fly from Phata, Sersi and Guptkashi. The morning puja and the evening aarti are both worth staying for, so most pilgrims spend a night at Kedarnath.</p>
<p>The opening date is announced each year on Maha Shivaratri and usually falls in late April or May. The temple closes on Bhai Dooj after Diwali.</p>
HTML,
        'altitude_m' => 3583,
        'best_time' => 'May to June and September to October',
        'opening_hours' => 'Open from late April or May until Bhai Dooj (October or November)',
        'nearest_airport' => 'Jolly Grant Airport, Dehradun',
        'how_to_reach' => 'Drive to Sonprayag and take the shuttle to Gaurikund, then walk or ride about 16 km. Helicopters fly from Phata, Sersi and Guptkashi.',
        'latitude' => 30.7352, 'longitude' => 79.0669,
        'faqs' => [
            ['question' => 'How long is the Kedarnath trek?', 'answer' => 'About 16 km each way from Gaurikund. Walking up takes 6 to 8 hours for most people. Ponies, palkis and helicopter tickets can be arranged.'],
            ['question' => 'Is a Doleshwor Mahadev visit connected with Kedarnath?', 'answer' => 'Yes. Devotees believe Doleshwor Mahadev in Bhaktapur, Nepal, is the head of the Kedarnath Shiva, and many pilgrims visit both.'],
        ],
        'featured' => true,
    ],
    [
        'name' => 'Badrinath',
        'slug' => 'badrinath',
        'image' => 'badrinath',
        'country' => 'India',
        'region' => 'Chamoli, Uttarakhand',
        'summary' => 'The temple of Lord Vishnu on the Alaknanda river, one of the four dhams and the last temple of the Chardham Yatra.',
        'description' => <<<'HTML'
<p>Badrinath is the temple of Lord Vishnu as Badrinarayan, on the bank of the Alaknanda river between the Nar and Narayan ranges. It is one of the four dhams of India and one of the 108 Divya Desams, and the last temple of the Uttarakhand Chardham Yatra.</p>
<p>The brightly painted temple front faces the river. Pilgrims bathe in Tapt Kund, a hot spring below the temple, before darshan. A few kilometres beyond is Mana village, near the border, with the caves linked to Vyas and Ganesh and the Bhim Pul rock bridge.</p>
<p>Badrinath can be reached by road. The opening date is announced each year on Vasant Panchami and usually falls in late April or May; the temple closes in November.</p>
HTML,
        'altitude_m' => 3300,
        'best_time' => 'May to June and September to October',
        'opening_hours' => 'Open from late April or May until November',
        'nearest_airport' => 'Jolly Grant Airport, Dehradun',
        'how_to_reach' => 'By road through Rishikesh, Rudraprayag and Joshimath. Badrinath is about 300 km from Haridwar.',
        'latitude' => 30.7433, 'longitude' => 79.4938,
        'faqs' => [
            ['question' => 'Can you drive to Badrinath?', 'answer' => 'Yes. Badrinath is on the road and needs no walk, so it is suitable for elderly pilgrims. The drive from Haridwar takes most of two days.'],
        ],
        'featured' => true,
    ],

    // ------------------------------------------------------------- Tibet (China)
    [
        'name' => 'Kailash Mansarovar',
        'slug' => 'kailash-mansarovar',
        'image' => 'kailash-mansarovar',
        'country' => 'Tibet (China)',
        'region' => 'Ngari, Tibet Autonomous Region',
        'summary' => 'Mount Kailash and Lake Mansarovar in western Tibet, sacred to Hindus, Buddhists, Jains and followers of Bon.',
        'description' => <<<'HTML'
<p>Mount Kailash, 6,638 m, is held to be the home of Lord Shiva, and Lake Mansarovar below it is one of the most sacred lakes in Hinduism. The mountain is also sacred to Buddhists, Jains and followers of Bon, and pilgrims of all four faiths walk around it.</p>
<p>Lake Mansarovar lies at 4,590 m. Pilgrims take a holy dip or collect its water, perform puja on the shore and drive around the lake. The parikrama of Kailash is about 52 km and usually takes three days on foot, crossing the Dolma La pass at about 5,630 m. Ponies and porters can be hired for the parikrama.</p>
<p>From Nepal the yatra goes overland through the Kerung border north of Kathmandu, or by flights through Nepalgunj and Simikot to Hilsa. Every route needs Chinese visas and Tibet permits arranged by an authorised operator, a good level of fitness and time to get used to the altitude. The rules and fees for each nationality are set by the authorities each season.</p>
HTML,
        'altitude_m' => 4590,
        'best_time' => 'May to September',
        'opening_hours' => 'Yatra season from May to September; permits are issued for set dates',
        'nearest_airport' => 'Kathmandu (KTM) for the overland route; Nepalgunj and Simikot for the Hilsa route',
        'how_to_reach' => 'Overland from Kathmandu through the Kerung border, or fly Kathmandu–Nepalgunj–Simikot and continue to Hilsa and Purang. Both routes take about two weeks in all.',
        'latitude' => 30.6667, 'longitude' => 81.4833,
        'faqs' => [
            ['question' => 'How difficult is the Kailash parikrama?', 'answer' => 'It is a high-altitude walk of about 52 km over three days, with the Dolma La pass at about 5,630 m on the second day. Good fitness and a medical check before travel are strongly advised. Those who do not walk can complete the Mansarovar parikrama by vehicle and see Kailash from Darchen.'],
            ['question' => 'What documents are needed for Kailash Mansarovar?', 'answer' => 'A passport valid for at least six months, a Chinese group visa and Tibet permits arranged by an authorised operator, and a medical fitness certificate. Requirements differ for Indian and Nepali passport holders and can change each season, so we confirm them before you book.'],
        ],
        'featured' => true,
    ],
];
