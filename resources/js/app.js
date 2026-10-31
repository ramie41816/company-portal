import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import Quill from 'quill';
window.Quill = Quill;

const setIcons = (theme) => {
    document.querySelectorAll('[data-theme-icon]').forEach((el) => {
        el.className = 'bi ' + (theme === 'dark' ? 'bi-sun' : 'bi-moon-stars');
    });
};

document.addEventListener('DOMContentLoaded', () => {
    setIcons(document.documentElement.dataset.bsTheme);
});

document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-theme-toggle]')) return;

    const next = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.bsTheme = next;
    try { localStorage.setItem('theme', next); } catch (err) { }
    setIcons(next);
});