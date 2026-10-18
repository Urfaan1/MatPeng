import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;
window.createIcons = createIcons;
window.lucideIcons = icons;

// Helper untuk format Rupiah di JS
window.formatRupiah = function(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(number);
};

// Render Lucide icons on DOMContentLoaded and Alpine init
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

Alpine.start();
