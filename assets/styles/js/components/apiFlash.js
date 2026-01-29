const TYPE_CLASS_MAP = {
    info: 'alert-info',
    success: 'alert-success',
    warn: 'alert-warning',
    error: 'alert-error'
};

const STORAGE_KEY = 'pending_flash';

function ensureFlashContainer() {
    let container = document.getElementById('flash-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'flash-container';
        document.body.appendChild(container);
    }

    container.className = 'toast toast-bottom toast-end z-50';

    return container;
}

export function addFlash(type, message, { timeout = 5000 } = {}) {
    if (!message) return;

    const container = ensureFlashContainer();
    const el = document.createElement('div');
    const mappedClass = TYPE_CLASS_MAP[String(type || '').toLowerCase()] || 'alert-info';

    el.className = `alert ${mappedClass} shadow-lg w-[min(420px,calc(100vw-2rem))]
        opacity-0 translate-x-6 transition-all duration-200 ease-out`;

    el.innerHTML = `
        <div class="flex items-start justify-between gap-3 w-full">
            <span class="text-sm leading-snug">${escapeHtml(String(message))}</span>
            <button type="button" class="btn btn-ghost btn-xs" aria-label="Fermer">✕</button>
        </div>
    `;

    const closeBtn = el.querySelector('button');

    const removeWithAnimation = () => {
        el.classList.add('opacity-0', 'translate-x-6');
        el.classList.remove('opacity-100', 'translate-x-0');
        window.setTimeout(() => el.remove(), 200);
    }

    closeBtn.addEventListener('click', removeWithAnimation);

    container.appendChild(el);

    el.getBoundingClientRect();
    el.classList.remove('opacity-0', 'translate-x-6');
    el.classList.add('opacity-100', 'translate-x-0');

    if (timeout && timeout > 0) {
        window.setTimeout(removeWithAnimation, timeout);
    }
}

export function handleApiFlash(payload) {
    if (!payload || typeof payload !== 'object') return false;

    if (payload.type && payload.message) {
        addFlash(payload.type, payload.message);
        return true;
    }

    return false;
}

export function saveFlashForNextPage(type, message) {
    if (!message) return;
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify({ type, message }));
}

export function consumeFlashFromPreviousPage() {
    const raw = sessionStorage.getItem(STORAGE_KEY);
    if (!raw) return;

    sessionStorage.removeItem(STORAGE_KEY);

    try {
        const data = JSON.parse(raw);
        if (data && data.type && data.message) {
            addFlash(data.type, data.message);
            return true;
        }
    } catch (e) {

    }

    return false;
}

function escapeHtml(str) {
    return str
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
