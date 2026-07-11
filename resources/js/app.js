import './bootstrap';
import Swal from 'sweetalert2';
window.Swal = Swal;
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

import.meta.glob('../images/*', {
  eager: true,
  import: 'default'
});

const initAlpinePlugins = (alpineInstance) => {
    alpineInstance.plugin(intersect);
    alpineInstance.plugin(collapse);
};

// Check if Livewire is active/present on the page to prevent duplicate Alpine instances
const hasLivewire = document.querySelector('[wire\\:id], [wire\\:key]') || 
                    document.querySelector('script[src*="livewire"]') || 
                    window.Livewire;

if (hasLivewire) {
    document.addEventListener('livewire:init', () => {
        initAlpinePlugins(window.Alpine);
    });
} else {
    import('alpinejs').then((module) => {
        const Alpine = module.default;
        window.Alpine = Alpine;
        initAlpinePlugins(Alpine);
        Alpine.start();
    });
}
