/* AbidjanGym — Sidebar component (injected in all authenticated pages) */
(function () {
  const page = location.pathname.split('/').pop() || 'dashboard.html';

  const ic = {
    grid: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
    users: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    card: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
    money: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="12" y1="2" x2="12" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
    qr: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="4" height="4"/><rect x="13" y="7" width="4" height="4"/><rect x="7" y="13" width="4" height="4"/><circle cx="15" cy="15" r="0.5" fill="currentColor"/><circle cx="17" cy="15" r="0.5" fill="currentColor"/><circle cx="15" cy="17" r="0.5" fill="currentColor"/><circle cx="17" cy="17" r="0.5" fill="currentColor"/></svg>',
    bell: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
    chart: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><polyline points="3 20 21 20"/></svg>',
    gear: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>',
    logout: '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
  };

  const nav = [
    { href: 'dashboard.html', label: 'Dashboard', icon: ic.grid },
    { href: 'members.html', label: 'Abonnés', icon: ic.users, count: '247' },
    { href: 'plans.html', label: 'Plans', icon: ic.card },
    { href: 'payments.html', label: 'Paiements', icon: ic.money },
    { href: 'scan.html', label: 'Scan QR', icon: ic.qr },
    { href: 'notifications.html', label: 'Notifications', icon: ic.bell },
    { href: 'reports.html', label: 'Rapports', icon: ic.chart },
    { href: 'settings.html', label: 'Paramètres', icon: ic.gear },
  ];

  const html = `
    <div class="sb-hd">
      <div class="sb-logo">FZ</div>
      <div>
        <div class="sb-gym-name">FitZone Abidjan</div>
        <div class="sb-gym-sub">Espace Admin</div>
      </div>
    </div>
    <nav class="sb-nav">
      ${nav.map(n => `
        <a href="${n.href}" class="sb-link${page === n.href ? ' active' : ''}">
          ${n.icon}
          <span>${n.label}</span>
          ${n.count ? `<span class="sb-cnt">${n.count}</span>` : ''}
        </a>
      `).join('')}
    </nav>
    <div class="sb-ft">
      <div class="sb-user">
        <div class="sb-av">JM</div>
        <div>
          <span class="sb-uname">Jean-Marc K.</span>
          <span class="sb-urole">Administrateur</span>
        </div>
      </div>
    </div>
  `;

  const el = document.getElementById('sidebar-root');
  if (el) el.innerHTML = html;

  // Mobile menu
  const btn = document.getElementById('menu-btn');
  const ov = document.getElementById('sb-ov');
  const sb = el;
  if (btn && sb) {
    btn.addEventListener('click', () => { sb.classList.toggle('open'); ov && ov.classList.toggle('show') });
    ov && ov.addEventListener('click', () => { sb.classList.remove('open'); ov.classList.remove('show') });
  }
})();
