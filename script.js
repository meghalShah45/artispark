// ArtiSpark — front-end behaviour (no dependencies)

// ---------- Mobile menu ----------
const menuToggle = document.getElementById('mobileMenuToggle');
const navMenu = document.getElementById('navMenu');

menuToggle?.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
});

navMenu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
        menuToggle?.classList.remove('active');
        navMenu.classList.remove('active');
    });
});

// ---------- Active nav link while scrolling ----------
const sections = [...document.querySelectorAll('section[id]')];
const navLinks = [...document.querySelectorAll('.nav-link[href^="#"]')];

function updateActiveNav() {
    const y = window.scrollY + 140;
    let current = '';
    sections.forEach((s) => { if (s.offsetTop <= y) current = s.id; });
    navLinks.forEach((l) => l.classList.toggle('active', l.getAttribute('href') === '#' + current));
}
window.addEventListener('scroll', updateActiveNav, { passive: true });
updateActiveNav();

// ---------- Reveal on scroll ----------
const revealEls = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    revealEls.forEach((el) => io.observe(el));
} else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
}

// ---------- Service tiles: tap to open on touch devices ----------
document.querySelectorAll('.service-tile').forEach((tile) => {
    tile.addEventListener('click', () => {
        const open = tile.classList.contains('is-open');
        document.querySelectorAll('.service-tile.is-open').forEach((t) => t.classList.remove('is-open'));
        if (!open) tile.classList.add('is-open');
    });
});

// ---------- Contact form ----------
const contactForm = document.getElementById('contactForm');
const contactSubmit = document.getElementById('contactSubmit');
const contactStatus = document.getElementById('contactStatus');

contactForm?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(contactForm);
    contactSubmit.disabled = true;
    contactSubmit.textContent = 'Sending...';
    contactStatus.textContent = '';
    contactStatus.className = 'form-status';

    try {
        const response = await fetch(contactForm.action, {
            method: 'POST',
            body: formData,
            headers: { Accept: 'application/json' }
        });
        const responseText = await response.text();
        let result = {};
        try {
            result = responseText ? JSON.parse(responseText) : {};
        } catch (parseError) {
            throw new Error('The server returned an unexpected response. Please check the contact form setup.');
        }
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Something went wrong. Please try again.');
        }
        contactStatus.textContent = result.message || 'Thank you. Your inquiry has been sent successfully.';
        contactStatus.classList.add('is-success');
        contactForm.reset();
    } catch (error) {
        contactStatus.textContent = error.message || 'Sorry, your message could not be sent right now.';
        contactStatus.classList.add('is-error');
    } finally {
        contactSubmit.disabled = false;
        contactSubmit.textContent = 'Send Message';
    }
});

// ---------- Contact map (Leaflet + OpenStreetMap) ----------
const mapEl = document.getElementById('contactMap');
if (mapEl && window.L) {
    let markers = [];
    try { markers = JSON.parse(mapEl.dataset.markers || '[]'); } catch (e) { markers = []; }
    if (markers.length) {
        const map = L.map(mapEl, { scrollWheelZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        const group = L.featureGroup(markers.map((m) =>
            L.marker([Number(m.lat), Number(m.lng)]).bindPopup(m.label || '')
        )).addTo(map);
        map.fitBounds(group.getBounds().pad(0.35));
    }
}
