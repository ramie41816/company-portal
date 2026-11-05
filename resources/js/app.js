// Import Bootstrap framework core
import * as bootstrap from 'bootstrap';

// Import SweetAlert2 for notification modals
import Swal from 'sweetalert2';

// Import Bootstrap Icons stylesheet bundle
import 'bootstrap-icons/font/bootstrap-icons.css';

// Import Quill editor and make it globally available
import Quill from 'quill';
window.Quill = Quill;

// Import FullCalendar and plugins
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Make FullCalendar globally available
window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin
};

// Function to update theme toggle icons based on current mode
const setIcons = (theme) => {
    document.querySelectorAll('[data-theme-icon]').forEach((el) => {
        el.className = 'bi ' + (theme === 'dark' ? 'bi-sun' : 'bi-moon-stars');
    });
};

// Set correct toggle icon on initial page load
document.addEventListener('DOMContentLoaded', () => {
    setIcons(document.documentElement.dataset.bsTheme);
});

// Handle click events for toggling light and dark themes
document.addEventListener('click', (e) => {
    if (!e.target.closest('[data-theme-toggle]')) return;

    const next = document.documentElement.dataset.bsTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.bsTheme = next;
    try { localStorage.setItem('theme', next); } catch (err) { }
    setIcons(next);
});

// Make SweetAlert2 globally available across your scripts
window.Swal = Swal;