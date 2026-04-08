// Unified Modern Sidebar JS v2.0
// Handles toggle, active states, smooth animations for all layouts

let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
const $ = selector => document.querySelector(selector);

function initSidebar() {
    applyCollapsedState();
    setActiveMenu();
    setupEventListeners();
}

function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
    
    const sidebar = $('#sidebar');
    if (!sidebar) return;
    
    // Animate width
    sidebar.style.width = sidebarCollapsed ? '64px' : '256px';
    
    // Toggle visibility of text elements
    toggleTextElements(sidebarCollapsed);
    
    // Adjust layout margins
    updateLayoutMargins(sidebarCollapsed);
}

function toggleTextElements(hide) {
    ['#sidebarTitle', '#sidebarSubtitle', '#sidebarMenu .sidebar-text', '#sidebarFooter'].forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(el => {
            el.style.display = hide ? 'none' : '';
            el.style.opacity = hide ? '0' : '1';
        });
    });
}

function updateLayoutMargins(isCollapsed) {
    const header = $('#mainHeader');
    const mainContent = $('#mainContent');
    const marginClass = isCollapsed ? 'ml-16' : 'ml-64';
    
    if (header) {
        header.className = header.className.replace(/ml-(16|64)/g, '') + ' ' + marginClass;
    }
    if (mainContent) {
        mainContent.className = mainContent.className.replace(/ml-(16|64)/g, '') + ' ' + marginClass;
    }
}

function setActiveMenu() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('#sidebarMenu a, #sidebarMenu button');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href') || '';
        if (currentPath.includes(href) || href.includes(currentPath.split('/').pop())) {
            item.classList.add('sidebar-active', 'sidebar-item');
            item.closest('li')?.classList.add('active');
        }
        item.classList.add('sidebar-item');
    });
}

function toggleSubmenu(btn) {
    const submenu = btn.nextElementSibling;
    const arrow = btn.querySelector('i:last-child');
    submenu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function setupEventListeners() {
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', (e) => {
        if (window.innerWidth < 768 && !e.target.closest('#sidebar') && !e.target.closest('#hamburgerBtn')) {
            if (!sidebarCollapsed) toggleSidebar();
        }
    });
}

// Smooth transitions
const style = document.createElement('style');
style.textContent = `
    #sidebar { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    #mainHeader, #mainContent { transition: margin-left 0.3s ease; }
    .rotate-180 { transform: rotate(180deg); transition: transform 0.2s ease; }
`;
document.head.appendChild(style);

// Auto-init on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
} else {
    initSidebar();
}

