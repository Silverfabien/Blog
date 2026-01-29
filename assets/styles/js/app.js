import $ from 'jquery';
import '@hotwired/turbo';

import '@fortawesome/fontawesome-free/css/all.css'

import '../css/app.css';
import '../css/article/show.css'

window.$ = window.jQuery = $;

import { handleApiFlash, consumeFlashFromPreviousPage } from "./components/apiFlash.js";

document.addEventListener('turbo:before-fetch-response', async (event) => {
    const response = event.detail.fetchResponse?.response;
    if (!response) return;

    const contentType = response.headers.get('Content-Type') || '';
    if (!contentType.includes('application/json')) return;

    try {
        const data = await response.clone().json();
        handleApiFlash(data);
    } catch (e) {

    }
});

function hydrateServerFlashes() {
    const container = document.getElementById('flash-container');
    if (!container) return;

    const flashes = container.querySelectorAll('[data-flash]');
    flashes.forEach((el) => {
        // animation d'entrée
        el.classList.add('opacity-0', 'translate-x-6', 'transition-all', 'duration-200', 'ease-out');
        el.getBoundingClientRect();
        el.classList.remove('opacity-0', 'translate-x-6');
        el.classList.add('opacity-100', 'translate-x-0');

        const removeWithAnimation = () => {
            el.classList.add('opacity-0', 'translate-x-6');
            el.classList.remove('opacity-100', 'translate-x-0');
            window.setTimeout(() => el.remove(), 200);
        };

        const btn = el.querySelector('button');
        if (btn) btn.addEventListener('click', removeWithAnimation);

        const timeout = Number(el.getAttribute('data-timeout') || 5000);
        if (timeout > 0) {
            window.setTimeout(removeWithAnimation, timeout);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    hydrateServerFlashes();
});

document.addEventListener('turbo:load', () => {
    consumeFlashFromPreviousPage();
})
