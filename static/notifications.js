
// Universal Notification Handler - Role Specific - FIXED 404
(function() {
    'use strict';
    
    async function loadNotifications(rolePrefix = '') {
        try {
            const res = await fetch('/api/get_notifications.php');
            const data = await res.json();
            
            const badge = document.getElementById(`notifBadge${rolePrefix}${rolePrefix ? '' : 'User'}`);
            const list = document.getElementById(`notifList${rolePrefix}${rolePrefix ? '' : 'User'}`);
            
            if (badge) badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
            if (list) list.innerHTML = data.notifications.map(notif => `
                <div class="p-4 border-b hover:bg-gray-50 ${!notif.is_read ? 'bg-amber-50 border-l-4 border-amber-400' : ''}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br flex items-center justify-center text-white text-sm font-medium shadow">
                            ${getIcon(notif.type || 'info')}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-gray-900 text-sm leading-tight mb-1">${escapeHtml(notif.title)}</h5>
                            <p class="text-xs text-gray-600 mb-2 line-clamp-2">${escapeHtml(notif.message)}</p>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400">${formatDate(notif.created_at)}</span>
                                ${!notif.is_read ? `<button onclick="markAsRead(${notif.id}, '${rolePrefix}')" class="text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition-all">Tandai</button>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('') || '<div class="p-8 text-center text-gray-500 text-sm">Belum ada pemberitahuan baru</div>';
        } catch (e) {
            console.error('Notification load error:', e);
        }
    }

    window.loadNotifications = loadNotifications;

    window.toggleNotifications = function(rolePrefix = '') {
        const dropdown = document.getElementById(`notifDropdown${rolePrefix}${rolePrefix ? '' : 'User'}`);
        if (dropdown) dropdown.classList.toggle('hidden');
        if (!dropdown || !dropdown.classList.contains('hidden')) loadNotifications(rolePrefix);
    };

    window.markAsRead = function(id, rolePrefix = '') {
        fetch('/api/mark_notification_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        }).then(() => loadNotifications(rolePrefix));
    };

    window.markAllRead = function(role) {
        fetch('/api/mark_all_notifications_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({role})
        }).then(() => loadNotifications(role === 'user' ? '' : role));
    };

    function getIcon(type) {
        const icons = {
            'approval': '<i class="fas fa-check-circle"></i>',
            'announcement': '<i class="fas fa-bullhorn"></i>',
            'activity': '<i class="fas fa-users"></i>',
            'info': '<i class="fas fa-info-circle"></i>'
        };
        return icons[type] || icons.info;
    }

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('id-ID', { 
            day: 'numeric', 
            month: 'short', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    function escapeHtml(text) {
        const map = {'&': '&amp;', '<': '<', '>': '>', '"': '"', "'": '&#039;'};
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('notifBtn')) loadNotifications('');
        if (document.getElementById('notifBtnAdmin')) loadNotifications('Admin'); 
        if (document.getElementById('notifBtnKetua')) loadNotifications('Ketua');
        
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.relative')) document.querySelectorAll('[id*="notifDropdown"]').forEach(d => d.classList.add('hidden'));
        });
        
        setInterval(() => {
            if (!document.hidden && document.visibilityState === 'visible') {
                if (document.getElementById('notifBtn')) loadNotifications('');
            }
        }, 30000);
    });
})();

