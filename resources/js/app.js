import './bootstrap';
import Swal from 'sweetalert2';
window.Swal = Swal;
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';

import.meta.glob('../images/*', {
  eager: true,
  import: 'default'
});

window.Alpine = Alpine;

Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.start();
