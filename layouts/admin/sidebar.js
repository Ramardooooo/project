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

function toggleMaster() {
    $('masterMenu')?.classList.toggle('hidden');
    $('arrowMaster')?.classList.toggle('rotate-180');
}

function toggleKelola() {
    $('kelolaMenu')?.classList.toggle('hidden');
    $('arrowKelola')?.classList.toggle('rotate-180');
}
