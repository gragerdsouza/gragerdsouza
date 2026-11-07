/**
 * Highlights the menu item for the section currently in view (home page only).
 *
 * Rule: the active section is the last one whose top has scrolled above the line just
 * under the sticky header. At the very bottom of the page the last section wins, and a
 * clicked item stays active until the visitor scrolls by hand.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
const links = [...document.querySelectorAll('[data-nav-link]')];
const hashOf = (link) => new URL(link.href).hash.slice(1);

// Only sections that exist on this page, in document order.
const sections = [...new Set(links.map(hashOf))]
    .map((id) => document.getElementById(id))
    .filter(Boolean)
    .sort((a, b) => (a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1));

/** Px below the viewport top a section must pass to become active (header + scroll-mt-20 + slack). */
const ACTIVATION_LINE = 120;

/** Section chosen by a click; honoured until the visitor scrolls manually. */
let pinnedId = null;

/**
 * Mark the link for the given section as current and clear the others.
 *
 * @param {string|null} id Section id in view, or null when none is.
 * @returns {void}
 */
function setActive(id) {
    links.forEach((link) => {
        if (id !== null && hashOf(link) === id) {
            link.setAttribute('aria-current', 'true');
        } else {
            link.removeAttribute('aria-current');
        }
    });
}

/**
 * Work out which section is in view from the current scroll position.
 *
 * @returns {string|null} The active section id, or null above the first section.
 */
function currentSectionId() {
    const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2;
    if (atBottom) return sections.at(-1).id;

    let active = null;
    for (const section of sections) {
        if (section.getBoundingClientRect().top <= ACTIVATION_LINE) active = section.id;
    }

    return active;
}

/**
 * Recalculate the highlight (throttled to one run per animation frame).
 *
 * @returns {void}
 */
let ticking = false;
function update() {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
        ticking = false;
        setActive(pinnedId ?? currentSectionId());
    });
}

if (sections.length > 0) {
    links.forEach((link) =>
        link.addEventListener('click', () => {
            pinnedId = hashOf(link);
            setActive(pinnedId);
        }),
    );

    // Any manual scrolling releases the pin set by a click.
    ['wheel', 'touchmove', 'keydown'].forEach((type) =>
        window.addEventListener(type, () => (pinnedId = null), { passive: true }),
    );

    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });

    // Respect a hash in the URL on first load (e.g. after the contact form redirects to /#contact).
    pinnedId = location.hash.slice(1) || null;
    update();
}
