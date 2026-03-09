// ═════════════════════════════════════════
// GeoActivos Sidebar Drawer Pro
// ═════════════════════════════════════════

(function(){
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.main-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggleBtn = document.querySelector('.toggle-sidebar-btn');
    const body = document.body;

    if (!sidebar || !backdrop || !toggleBtn) {
      console.warn('Sidebar elements not found');
      return;
    }

    // Función para abrir/cerrar sidebar
    function toggleSidebar() {
      const isClosed = !body.classList.contains('sidebar-collapse');
      
      if (isClosed) {
        body.classList.add('sidebar-collapse');
        sidebar.classList.add('show');
        backdrop.classList.add('show');
        body.style.overflow = 'hidden';
      } else {
        body.classList.remove('sidebar-collapse');
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        body.style.overflow = '';
      }
      
      localStorage.setItem('sidebar_open', isClosed ? 'true' : 'false');
    }

    // Click en hamburguesa
    toggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toggleSidebar();
    });

    // Click en backdrop para cerrar
    backdrop.addEventListener('click', function() {
      body.classList.remove('sidebar-collapse');
      sidebar.classList.remove('show');
      backdrop.classList.remove('show');
      body.style.overflow = '';
      localStorage.setItem('sidebar_open', 'false');
    });

    // Click en links del sidebar para cerrar en mobile
    const sidebarLinks = sidebar.querySelectorAll('a');
    sidebarLinks.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth < 992) {
          body.classList.remove('sidebar-collapse');
          sidebar.classList.remove('show');
          backdrop.classList.remove('show');
          body.style.overflow = '';
          localStorage.setItem('sidebar_open', 'false');
        }
      });
    });

    // Restaurar estado del sidebar si estaba abierto
    const wasOpen = localStorage.getItem('sidebar_open') === 'true';
    if (wasOpen && window.innerWidth >= 992) {
      body.classList.add('sidebar-collapse');
      sidebar.classList.add('show');
      backdrop.classList.add('show');
      body.style.overflow = 'hidden';
    }

    // Cerrar sidebar en resize a mobile
    window.addEventListener('resize', function() {
      if (window.innerWidth < 992) {
        body.classList.remove('sidebar-collapse');
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        body.style.overflow = '';
      }
    });

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && body.classList.contains('sidebar-collapse')) {
        body.classList.remove('sidebar-collapse');
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
        body.style.overflow = '';
        localStorage.setItem('sidebar_open', 'false');
      }
    });
  });
})();

// ═════════════════════════════════════════
// Smooth scroll helpers
// ═════════════════════════════════════════
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href === '#') {
      e.preventDefault();
      return;
    }
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

