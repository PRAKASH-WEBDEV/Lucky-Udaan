// Public website behaviour — kept small: only the Bootstrap plugins the site uses.
import '@fontsource-variable/inter';
import '@fontsource-variable/plus-jakarta-sans';
import 'bootstrap-icons/font/bootstrap-icons.css';
import Collapse from 'bootstrap/js/dist/collapse';
import Dropdown from 'bootstrap/js/dist/dropdown';
import Offcanvas from 'bootstrap/js/dist/offcanvas';
import Carousel from 'bootstrap/js/dist/carousel';
import Modal from 'bootstrap/js/dist/modal';
import Alert from 'bootstrap/js/dist/alert';

window.bootstrap = { Collapse, Dropdown, Offcanvas, Carousel, Modal, Alert };
document.documentElement.classList.remove('no-js');

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Sticky header shadow + back-to-top visibility
const header = document.querySelector('.site-header');
const toTop = document.querySelector('.fab-top');
const onScroll = () => {
    const y = window.scrollY;
    header?.classList.toggle('is-scrolled', y > 8);
    toTop?.classList.toggle('show', y > 600);
};
window.addEventListener('scroll', onScroll, { passive: true });
onScroll();
toTop?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' }));

// Close the off-canvas menu when the viewport grows to desktop
const offcanvasEl = document.getElementById('mobileNav');
window.matchMedia('(min-width: 1200px)').addEventListener('change', (e) => {
    if (e.matches && offcanvasEl) Offcanvas.getInstance(offcanvasEl)?.hide();
});

// Reveal-on-scroll and animated counters
const counters = document.querySelectorAll('[data-count]');
const animateCount = (el) => {
    const target = parseInt(el.dataset.count, 10) || 0;
    if (reduceMotion || target === 0) { el.textContent = target.toLocaleString('en-IN'); return; }
    const start = performance.now();
    const dur = 1600;
    const tick = (now) => {
        const p = Math.min(1, (now - start) / dur);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString('en-IN');
        if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
};

if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            if (entry.target.dataset.count !== undefined) animateCount(entry.target);
            io.unobserve(entry.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    document.querySelectorAll('.reveal, [data-count]').forEach((el) => io.observe(el));
} else {
    document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
    counters.forEach(animateCount);
}

// Gallery lightbox (photos and YouTube videos)
const lightbox = document.getElementById('lightbox');
if (lightbox) {
    const modal = new Modal(lightbox);
    const body = lightbox.querySelector('.modal-body');
    document.querySelectorAll('[data-lightbox]').forEach((item) => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const { src, video, caption } = item.dataset;
            body.innerHTML = '';
            if (video) {
                const wrap = document.createElement('div');
                wrap.className = 'ratio ratio-16x9';
                const iframe = document.createElement('iframe');
                iframe.src = video + '?autoplay=1&rel=0';
                iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
                iframe.allowFullscreen = true;
                iframe.title = caption || 'Video';
                wrap.appendChild(iframe);
                body.appendChild(wrap);
            } else {
                const img = document.createElement('img');
                img.src = src;
                img.alt = caption || '';
                body.appendChild(img);
            }
            if (caption) {
                const cap = document.createElement('p');
                cap.className = 'lightbox-cap';
                cap.textContent = caption;
                body.appendChild(cap);
            }
            modal.show();
        });
    });
    lightbox.addEventListener('hidden.bs.modal', () => { body.innerHTML = ''; });
}

// Prevent double submits and show a busy state
document.querySelectorAll('form[data-busy]').forEach((form) => {
    form.addEventListener('submit', () => {
        const btn = form.querySelector('[type="submit"]');
        if (!btn || !form.checkValidity()) return;
        btn.disabled = true;
        btn.dataset.label = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Sending…';
    });
});

// Donation: custom amount + 80G fields
const donateForm = document.getElementById('donateForm');
if (donateForm) {
    const amountInput = donateForm.querySelector('#amount');
    donateForm.querySelectorAll('input[name="preset"]').forEach((r) => {
        r.addEventListener('change', () => { amountInput.value = r.value; });
    });
    amountInput?.addEventListener('input', () => {
        donateForm.querySelectorAll('input[name="preset"]').forEach((r) => { r.checked = r.value === amountInput.value; });
    });
    const toggle80g = donateForm.querySelector('#wants_80g');
    const extra = document.getElementById('taxFields');
    const sync80g = () => {
        if (!toggle80g || !extra) return;
        extra.hidden = !toggle80g.checked;
        extra.querySelectorAll('input, textarea').forEach((i) => { i.required = toggle80g.checked && i.dataset.req80g !== undefined; });
    };
    toggle80g?.addEventListener('change', sync80g);
    sync80g();
}

// Copy-to-clipboard buttons (bank/UPI details)
document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(btn.dataset.copy);
            const old = btn.textContent;
            btn.textContent = 'Copied';
            setTimeout(() => { btn.textContent = old; }, 1500);
        } catch { /* clipboard unavailable */ }
    });
});

// Share buttons use the native share sheet when available
document.querySelectorAll('[data-share]').forEach((btn) => {
    if (!navigator.share) return;
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        navigator.share({ title: document.title, url: location.href }).catch(() => {});
    });
});
