// Dashboard behaviour
import '@fontsource-variable/inter';
import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

const body = document.body;

// ---------------------------------------------------------------- sidebar
const isDesktop = () => window.matchMedia('(min-width: 1200px)').matches;
try {
    if (isDesktop() && localStorage.getItem('sb-collapsed') === '1') body.classList.add('sb-collapsed');
} catch { /* storage unavailable */ }

document.querySelectorAll('[data-sb-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        if (isDesktop()) {
            body.classList.toggle('sb-collapsed');
            try { localStorage.setItem('sb-collapsed', body.classList.contains('sb-collapsed') ? '1' : '0'); } catch { /* ignore */ }
        } else {
            body.classList.toggle('sb-open');
        }
    });
});
document.querySelector('.sb-backdrop')?.addEventListener('click', () => body.classList.remove('sb-open'));
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') body.classList.remove('sb-open'); });

// ---------------------------------------------------------------- confirm dialogs
const confirmModalEl = document.getElementById('confirmModal');
if (confirmModalEl) {
    const modal = new bootstrap.Modal(confirmModalEl);
    let pendingForm = null;
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!form.matches('[data-confirm]') || form.dataset.confirmed) return;
        e.preventDefault();
        pendingForm = form;
        confirmModalEl.querySelector('.confirm-text').textContent = form.dataset.confirm;
        modal.show();
    });
    confirmModalEl.querySelector('.confirm-yes').addEventListener('click', () => {
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = '1';
        modal.hide();
        pendingForm.requestSubmit ? pendingForm.requestSubmit() : pendingForm.submit();
    });
}

// Auto-submit filters
document.querySelectorAll('[data-autosubmit]').forEach((el) => {
    el.addEventListener('change', () => el.form.submit());
});

// Auto-dismiss success flashes
document.querySelectorAll('.alert-success[data-autohide]').forEach((el) => {
    setTimeout(() => bootstrap.Alert.getOrCreateInstance(el).close(), 5000);
});

// ---------------------------------------------------------------- form helpers
// Image preview
document.querySelectorAll('[data-image-input]').forEach((wrap) => {
    const input = wrap.querySelector('input[type=file]');
    const preview = wrap.querySelector('.preview');
    input?.addEventListener('change', () => {
        const file = input.files?.[0];
        if (!file || !file.type.startsWith('image/')) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('This image is larger than 5 MB. Please choose a smaller file.');
            input.value = '';
            return;
        }
        const url = URL.createObjectURL(file);
        preview.innerHTML = '';
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'Preview';
        preview.appendChild(img);
    });
});

// Character counters
document.querySelectorAll('[data-counter]').forEach((input) => {
    const limit = parseInt(input.dataset.counter, 10);
    const label = document.querySelector(`label[for="${input.id}"]`);
    if (!label) return;
    const span = document.createElement('span');
    span.className = 'char-count';
    label.appendChild(span);
    const update = () => {
        span.textContent = `${input.value.length}/${limit}`;
        span.classList.toggle('over', input.value.length > limit);
    };
    input.addEventListener('input', update);
    update();
});

// Slug preview from title
const titleInput = document.querySelector('#field-title, #field-name');
const slugInput = document.querySelector('#field-slug');
if (titleInput && slugInput && !slugInput.value) {
    titleInput.addEventListener('input', () => {
        if (slugInput.dataset.touched) return;
        slugInput.placeholder = titleInput.value.toLowerCase().normalize('NFKD').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-').slice(0, 80);
    });
    slugInput.addEventListener('input', () => { slugInput.dataset.touched = '1'; });
}

// Warn about unsaved changes
document.querySelectorAll('form[data-dirty-check]').forEach((form) => {
    let dirty = false;
    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });
    window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
});

// ---------------------------------------------------------------- rich text editor (lazy-loaded)
const editors = document.querySelectorAll('[data-editor]');
if (editors.length) {
    Promise.all([import('quill'), import('quill/dist/quill.snow.css')]).then(([{ default: Quill }]) => {
        editors.forEach((holder) => {
            const input = document.getElementById(holder.dataset.editor);
            const quill = new Quill(holder, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'link', 'video'],
                        ['clean'],
                    ],
                },
                placeholder: holder.dataset.placeholder || 'Start writing…',
            });
            quill.clipboard.dangerouslyPasteHTML(input.value || '');
            const sync = () => { input.value = quill.root.innerHTML === '<p><br></p>' ? '' : quill.root.innerHTML; };
            quill.on('text-change', () => { sync(); input.dispatchEvent(new Event('input', { bubbles: true })); });
            sync();
        });
    });
}

// ---------------------------------------------------------------- charts (lazy-loaded)
const charts = document.querySelectorAll('[data-chart]');
if (charts.length) {
    import('chart.js/auto').then(({ default: Chart }) => {
        Chart.defaults.font.family = "'Inter Variable', system-ui, sans-serif";
        Chart.defaults.color = '#64716a';
        const brand = getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#1f7a4d';
        charts.forEach((canvas) => {
            const cfg = JSON.parse(canvas.dataset.chart);
            const isMoney = cfg.money;
            const fmt = (v) => (isMoney ? '₹' : '') + Number(v).toLocaleString('en-IN');
            if (cfg.type === 'doughnut') {
                new Chart(canvas, {
                    type: 'doughnut',
                    data: { labels: cfg.labels, datasets: [{ data: cfg.values, backgroundColor: [brand, '#e2a93b', '#2f7fb8', '#c8473b', '#7a8b83'], borderWidth: 0 }] },
                    options: { maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }, tooltip: { callbacks: { label: (c) => `${c.label}: ${fmt(c.parsed)}` } } } },
                });
                return;
            }
            const ctx = canvas.getContext('2d');
            const grad = ctx.createLinearGradient(0, 0, 0, canvas.clientHeight || 300);
            grad.addColorStop(0, brand + '55');
            grad.addColorStop(1, brand + '00');
            new Chart(canvas, {
                type: cfg.type || 'line',
                data: {
                    labels: cfg.labels,
                    datasets: [{
                        label: cfg.label || '',
                        data: cfg.values,
                        borderColor: brand,
                        backgroundColor: cfg.type === 'bar' ? brand : grad,
                        fill: cfg.type !== 'bar',
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        borderRadius: 6,
                        maxBarThickness: 36,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (c) => fmt(c.parsed.y) } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                        y: { beginAtZero: true, grid: { color: '#eef1ee' }, ticks: { callback: (v) => fmt(v), maxTicksLimit: 6, precision: 0 } },
                    },
                },
            });
        });
    });
}
