import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Dark mode initialization
if (localStorage.getItem('darkMode') === 'true' || 
    (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
}

// Auto-hide flash messages
setTimeout(() => {
    document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]').forEach(el => {
        el.style.opacity = '0';
        el.style.transition = 'opacity 0.5s';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);

// Confirm delete actions
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
        if (!confirm(el.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
