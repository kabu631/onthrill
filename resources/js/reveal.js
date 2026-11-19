/**
 * Page animation: one effect everywhere.
 *
 * - Elements marked data-reveal (or every child of a data-reveal-group) fade in
 *   and rise a little when they scroll into view; children of a group follow
 *   one another. Page titles do the same on load (.anim-rise in app.css).
 * - A thin bar at the top of the window shows that the next page is loading.
 *
 * The <head> of the layout adds .reveal-on to <html> before the page is painted,
 * unless the visitor's device asks for reduced motion. Without it nothing here
 * hides or animates anything.
 */
const STAGGER_MS = 90;
const MAX_STAGGER_STEPS = 7;

export function initReveal() {
    const root = document.documentElement;
    if (!root.classList.contains('reveal-on')) {
        return;
    }

    const items = [...document.querySelectorAll('[data-reveal]')];
    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        [...group.children].forEach((child, index) => {
            if (!child.hasAttribute('data-reveal')) {
                child.setAttribute('data-reveal', '');
                items.push(child);
            }
            child.style.setProperty('--reveal-delay', `${Math.min(index, MAX_STAGGER_STEPS) * STAGGER_MS}ms`);
        });
    });

    // From here on the script is in charge (this switches off the CSS safety net).
    root.classList.add('reveal-js');

    if (!('IntersectionObserver' in window)) {
        items.forEach(show);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                show(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

    items.forEach((item) => observer.observe(item));
}

function show(element) {
    element.classList.add('is-visible');
}

/* Top loading bar: starts when a link to another page is followed or a form is sent. */
export function initPageProgress() {
    const bar = document.getElementById('page-progress');
    if (!bar) {
        return;
    }

    const start = () => {
        bar.classList.remove('is-loading');
        void bar.offsetWidth; // restart the animation
        bar.classList.add('is-loading');
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0
            || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
            || link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }
        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin) {
            return; // tel:, mailto:, WhatsApp and other sites
        }
        if (url.pathname === location.pathname && url.search === location.search && url.hash) {
            return; // jump within the same page
        }
        start();
    });

    // Forms sent in the background cancel the normal submit, so they are skipped.
    document.addEventListener('submit', (event) => {
        if (!event.defaultPrevented && event.target.target !== '_blank') {
            start();
        }
    });

    // Coming back with the browser's Back button can restore the old page as it was.
    window.addEventListener('pageshow', () => bar.classList.remove('is-loading'));
}
