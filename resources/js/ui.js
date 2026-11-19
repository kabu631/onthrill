/** Small interactive pieces: header, hero word rotator, sliders, lightbox. */

export function registerUi(Alpine) {
    /**
     * Sticky menu bar + mobile drawer. Over the homepage photo (overHero) the bar
     * is transparent until the visitor scrolls, then turns white ("solid").
     */
    Alpine.data('siteHeader', (overHero = false) => ({
        open: false,
        scrolled: false,
        expanded: null,
        get solid() {
            return !overHero || this.scrolled || this.open;
        },
        init() {
            const onScroll = () => { this.scrolled = window.scrollY > (overHero ? 40 : 8); };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
            this.$watch('open', (open) => document.body.classList.toggle('overflow-hidden', open));
        },
        toggle(index) {
            this.expanded = this.expanded === index ? null : index;
        },
    }));

    /** Homepage photo slideshow: fades between slides, pauses while the tab is hidden. */
    Alpine.data('heroSlider', (count = 1, interval = 6500) => ({
        count,
        index: 0,
        timer: null,
        init() {
            if (this.count < 2 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this.start();
            document.addEventListener('visibilitychange', () => (document.hidden ? this.stop() : this.start()));
        },
        start() {
            this.stop();
            this.timer = setInterval(() => this.go(this.index + 1, false), interval);
        },
        stop() {
            clearInterval(this.timer);
        },
        go(index, manual = true) {
            this.index = (index + this.count) % this.count;
            if (manual) this.start(); // a click restarts the countdown
        },
    }));

    /**
     * Packages page: the filter panel on phones, and list or grid view. The view
     * is remembered in this browser only (storage can be blocked, so it is optional).
     */
    Alpine.data('tourList', () => ({
        filters: false,
        view: 'list',
        init() {
            try {
                if (localStorage.getItem('tours-view') === 'grid') this.view = 'grid';
            } catch (error) { /* private window: keep the list */ }
        },
        setView(view) {
            this.view = view;
            try {
                localStorage.setItem('tours-view', view);
            } catch (error) { /* not remembered, still switched */ }
        },
    }));

    /** Section tabs on the package page: highlights the section being read. */
    Alpine.data('sectionNav', () => ({
        active: null,
        init() {
            const links = [...this.$el.querySelectorAll('a[href^="#"]')];
            const sections = links.map((link) => document.querySelector(link.getAttribute('href'))).filter(Boolean);
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => { if (entry.isIntersecting) this.active = entry.target.id; });
            }, { rootMargin: '-35% 0px -60% 0px' });
            sections.forEach((section) => observer.observe(section));
        },
    }));

    /** "Back to top" button, shown once the visitor has scrolled down a screen. */
    Alpine.data('backToTop', () => ({
        visible: false,
        init() {
            const onScroll = () => { this.visible = window.scrollY > window.innerHeight * 0.8; };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        },
        toTop() {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' });
        },
    }));

    /** Cycles through the hero headline words. */
    Alpine.data('rotator', (words = [], interval = 2600) => ({
        words,
        index: 0,
        init() {
            if (this.words.length < 2) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            setInterval(() => { this.index = (this.index + 1) % this.words.length; }, interval);
        },
    }));

    /** Horizontal scroll-snap carousel with prev/next buttons. */
    Alpine.data('carousel', () => ({
        atStart: true,
        atEnd: false,
        init() {
            this.$nextTick(() => this.update());
            this.$refs.track.addEventListener('scroll', () => this.update(), { passive: true });
            window.addEventListener('resize', () => this.update(), { passive: true });
        },
        update() {
            const track = this.$refs.track;
            this.atStart = track.scrollLeft <= 4;
            this.atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        },
        move(direction) {
            const track = this.$refs.track;
            const step = track.firstElementChild?.getBoundingClientRect().width ?? track.clientWidth;
            track.scrollBy({ left: direction * (step + 24), behavior: 'smooth' });
        },
    }));

    /** Image lightbox with keyboard navigation. */
    Alpine.data('lightbox', (images = []) => ({
        images,
        current: null,
        get image() { return this.current === null ? null : this.images[this.current]; },
        show(index) {
            this.current = index;
            document.body.classList.add('overflow-hidden');
        },
        close() {
            this.current = null;
            document.body.classList.remove('overflow-hidden');
        },
        next() { if (this.current !== null) this.current = (this.current + 1) % this.images.length; },
        prev() { if (this.current !== null) this.current = (this.current - 1 + this.images.length) % this.images.length; },
    }));

    /** Main photo and thumbnails on destination and package pages. */
    Alpine.data('mediaGallery', (images = []) => ({
        images,
        active: 0,
    }));

    /**
     * Booking form estimate: price per person in the traveller's currency (Indian
     * travellers INR, Nepali travellers NPR, others the currency they are viewing)
     * times the number of travellers. The final price is confirmed by phone.
     */
    Alpine.data('bookingEstimate', (config) => ({
        adults: config.adults ?? 2,
        children: config.children ?? 0,
        nationality: config.nationality ?? 'indian',
        advance: Number(config.advance) || 0,
        currency() {
            if (this.nationality === 'nepali') return 'NPR';
            if (this.nationality === 'indian') return 'INR';
            return config.other === 'NPR' ? 'NPR' : 'INR';
        },
        total() {
            const price = this.currency() === 'NPR' ? config.npr : config.inr;
            return price ? price * (Number(this.adults) + Number(this.children)) : 0;
        },
        format(amount) {
            const digits = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(amount);
            return this.currency() === 'NPR' ? `NPR ${digits}` : `₹${digits}`;
        },
    }));

    /** Search pop-up: opened from any search icon ($store.search.show()) or the "/" key. */
    Alpine.store('search', {
        open: false,
        show() { this.open = true; },
        hide() { this.open = false; },
    });

    document.addEventListener('keydown', (event) => {
        const typing = event.target.closest?.('input, textarea, select, [contenteditable="true"]');
        if (event.key === '/' && !typing && !event.ctrlKey && !event.metaKey && !event.altKey) {
            event.preventDefault();
            Alpine.store('search').show();
        }
    });

    Alpine.data('searchPanel', () => ({
        term: '',
        results: [],
        allUrl: '',
        loading: false,
        searched: false,
        request: null,
        init() {
            this.$watch('$store.search.open', (open) => {
                document.documentElement.classList.toggle('overflow-hidden', open);
                if (open) {
                    this.$nextTick(() => setTimeout(() => this.$refs.input.focus(), 50));
                }
            });
        },
        close() {
            this.$store.search.hide();
        },
        async lookup() {
            const term = this.term.trim();
            this.request?.abort();
            if (term.length < 2) {
                this.results = [];
                this.searched = false;
                return;
            }

            const request = new AbortController();
            this.request = request;
            this.loading = true;
            try {
                const url = `${this.$root.querySelector('form').getAttribute('action')}?q=${encodeURIComponent(term)}`;
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: request.signal });
                const data = await response.json();
                this.results = data.results ?? [];
                this.allUrl = data.url ?? url;
                this.searched = true;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.results = []; // Enter still opens the full results page
                }
            } finally {
                if (this.request === request) this.loading = false;
            }
        },
    }));
}
