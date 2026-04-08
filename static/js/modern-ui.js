// Modern UI Controller - Unified for all layouts (user/admin/ketua)
// Features: Collapsible sidebar, dark mode, mobile menu, search, recent activity

class ModernUI {
  constructor() {
    this.isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    this.isDark = localStorage.getItem('darkMode') === 'true';
    this.mobileOpen = false;
    this.init();
  }

  init() {
    this.applyTheme();
    this.toggleSidebar(this.isCollapsed);
    this.bindEvents();
    this.loadRecentActivity();
    document.addEventListener('DOMContentLoaded', () => this.loadNotifications());
  }

  applyTheme() {
    if (this.isDark) {
      document.documentElement.classList.add('dark');
    }
  }

  bindEvents() {
    // Sidebar toggle
    const toggleBtns = document.querySelectorAll('[data-toggle-sidebar]');
    toggleBtns.forEach(btn => btn.addEventListener('click', () => this.toggleSidebar()));

    // Dark mode toggle
    const darkToggles = document.querySelectorAll('[data-toggle-dark]');
    darkToggles.forEach(btn => btn.addEventListener('click', () => this.toggleDarkMode()));

    // Mobile menu
    const mobileToggles = document.querySelectorAll('[data-toggle-mobile]');
    mobileToggles.forEach(btn => btn.addEventListener('click', (e) => {
      e.stopPropagation();
      this.toggleMobile();
    }));

    // Close on outside click
    document.addEventListener('click', () => this.isMobile() && this.mobileOpen && this.closeMobile());

    // Dropdowns
    document.querySelectorAll('[data-dropdown]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const target = document.getElementById(btn.dataset.dropdown);
        target.classList.toggle('hidden');
        btn.querySelector('i').classList.toggle('rotate-180');
      });
    });

    // Search
    const searchInput = document.getElementById('sidebarSearch');
    if (searchInput) {
      searchInput.addEventListener('input', (e) => this.filterMenu(e.target.value));
    }

    // Keyboard nav
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.closeAll();
    });
  }

  toggleSidebar(collapsed = null) {
    this.isCollapsed = collapsed ?? !this.isCollapsed;
    localStorage.setItem('sidebarCollapsed', this.isCollapsed);

    const sidebar = document.getElementById('sidebar');
    const header = document.getElementById('mainHeader') || document.querySelector('header');
    const main = document.getElementById('mainContent');
    const texts = document.querySelectorAll('.sidebar-text');
    const title = document.getElementById('sidebarTitle');
    const subtitle = document.getElementById('sidebarSubtitle');
    const footer = document.getElementById('sidebarFooter');

    sidebar.classList.toggle('w-[280px]', !this.isCollapsed);
    sidebar.classList.toggle('w-[72px]', this.isCollapsed);

    [header, main].forEach(el => {
      if (el) {
        el.classList.toggle('ml-[280px]', !this.isCollapsed);
        el.classList.toggle('ml-[72px]', this.isCollapsed);
      }
    });

    texts.forEach(t => t.style.display = this.isCollapsed ? 'none' : '');
    [title, subtitle, footer].forEach(el => el && (el.style.display = this.isCollapsed ? 'none' : ''));

    // Update icons
    document.querySelectorAll('#sidebarToggleIcon').forEach(icon => {
      icon.className = this.isCollapsed ? 'fas fa-bars' : 'fas fa-th-large';
    });
  }

  toggleDarkMode() {
    this.isDark = !this.isDark;
    localStorage.setItem('darkMode', this.isDark);
    document.documentElement.classList.toggle('dark', this.isDark);
  }

  toggleMobile() {
    this.mobileOpen = !this.mobileOpen;
    const overlay = document.getElementById('mobileOverlay');
    const sidebar = document.getElementById('sidebar');
    if (this.mobileOpen) {
      sidebar.classList.add('translate-x-0');
      overlay.classList.remove('hidden');
    } else {
      sidebar.classList.remove('translate-x-0');
      overlay.classList.add('hidden');
    }
  }

  closeMobile() {
    this.mobileOpen = false;
    document.getElementById('sidebar').classList.remove('translate-x-0');
    document.getElementById('mobileOverlay').classList.add('hidden');
  }

  isMobile() {
    return window.innerWidth < 768;
  }

  filterMenu(query) {
    document.querySelectorAll('#sidebarMenu a, #sidebarMenu button').forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
  }

  async loadRecentActivity() {
    const footer = document.getElementById('sidebarFooter');
    if (!footer) return;
    try {
      // Mock recent activity - replace with real API
      const activities = [
        { time: '2min ago', text: 'New notification received' },
        { time: '1hr ago', text: 'User updated profile' },
        { time: 'Today', text: 'System maintenance completed' }
      ];
      footer.innerHTML = activities.map(a => 
        `<div class="text-xs py-1 border-b border-gray-100 last:border-b-0 dark:border-gray-700">${a.text}<span class="float-right opacity-75">${a.time}</span></div>`
      ).join('') + '<div class="mt-2 pt-2 text-xs text-gray-400 dark:text-gray-500">Version 1.3</div>';
    } catch (e) { console.error('Activity load failed'); }
  }

  closeAll() {
    this.closeMobile();
    document.querySelectorAll('[data-dropdown]').forEach(btn => {
      document.getElementById(btn.dataset.dropdown)?.classList.add('hidden');
      btn.querySelector('i')?.classList.remove('rotate-180');
    });
  }

  loadNotifications() {
    if (typeof window.loadNotifications === 'function') {
      window.loadNotifications('');
    }
  }
}

// Global init
let modernUI;
document.addEventListener('DOMContentLoaded', () => {
  modernUI = new ModernUI();
  console.log('ModernUI initialized');
});

// Export for legacy calls
window.toggleSidebar = () => modernUI?.toggleSidebar();
window.toggleDarkMode = () => modernUI?.toggleDarkMode();
