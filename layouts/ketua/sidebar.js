let sidebarCollapsed = false;
const $ = id => document.getElementById(id);

function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;

    $('sidebar').style.width = sidebarCollapsed ? '64px' : '256px';
    $('sidebarToggleIcon').className = sidebarCollapsed ? 'fas fa-arrow-right' : 'fas fa-bars';

    ['sidebarTitle','sidebarSubtitle','sidebarMenu','sidebarFooter']
        .forEach(id => $(id).style.display = sidebarCollapsed ? 'none' : '');

    const main = $('mainContent');
    main.classList.remove('ml-64','ml-16');
    main.classList.add(sidebarCollapsed ? 'ml-16' : 'ml-64');
}

function togglePosition() {
    // Placeholder for position toggle if needed
}

function toggleKelola() {
    const kelolaMenu = $('kelolaMenu');
    const arrow = $('arrowKelola');
    const isHidden = kelolaMenu.classList.contains('hidden');
    
    if (isHidden) {
        kelolaMenu.classList.remove('hidden');
        arrow.classList.add('rotate-180');
    } else {
        kelolaMenu.classList.add('hidden');
        arrow.classList.remove('rotate-180');
    }
}
