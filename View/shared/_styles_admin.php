<?php
/**
 * View/shared/_styles_admin.php
 * Feuille de style partagée pour toutes les vues admin.
 */
?>
<style>
/* ═══════════════════════════════════════════════
   RESET & TOKENS
═══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --primary:       #6c63ff;
  --primary-dark:  #574fd6;
  --primary-light: #ede9fe;
  --accent:        #ff6584;
  --success:       #22c55e;
  --success-light: #dcfce7;
  --warning:       #f59e0b;
  --warning-light: #fef3c7;
  --danger:        #ef4444;
  --danger-light:  #fee2e2;
  --info:          #3b82f6;
  --info-light:    #dbeafe;
  --bg:            #f4f3ff;
  --surface:       #ffffff;
  --surface2:      #f8f7ff;
  --border:        #e5e7eb;
  --text:          #1e1b4b;
  --muted:         #6b7280;
  --radius:        12px;
  --radius-sm:     8px;
  --shadow:        0 4px 24px rgba(108,99,255,.08);
  --shadow-md:     0 8px 32px rgba(108,99,255,.14);
  --sidebar-w:     248px;
  --topbar-h:      64px;
}

body {
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  font-size: 15px;
}

/* ═══════════════════════════════════════════════
   LAYOUT
═══════════════════════════════════════════════ */
.layout   { display: flex; min-height: 100vh; }
.sidebar  {
  width: var(--sidebar-w);
  background: linear-gradient(175deg, #2e2a6e 0%, var(--primary) 100%);
  color: #fff;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  z-index: 100;
  transition: .3s;
}
.main { margin-left: var(--sidebar-w); flex: 1; min-height: 100vh; }

/* ═══════════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════════ */
.sidebar-brand {
  padding: 24px 20px 20px;
  font-size: 1.45rem;
  font-weight: 800;
  letter-spacing: .5px;
  border-bottom: 1px solid rgba(255,255,255,.1);
}
.sidebar-brand span { color: #c7c3ff; }
.sidebar-role {
  font-size: .7rem;
  font-weight: 500;
  color: rgba(255,255,255,.5);
  letter-spacing: 1.5px;
  text-transform: uppercase;
  margin-top: 4px;
}
.sidebar-section-title {
  font-size: .68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  color: rgba(255,255,255,.4);
  padding: 18px 20px 6px;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 20px;
  color: rgba(255,255,255,.72);
  text-decoration: none;
  font-size: .88rem;
  font-weight: 500;
  transition: .18s;
  border-left: 3px solid transparent;
}
.nav-item:hover  { background: rgba(255,255,255,.1); color: #fff; }
.nav-item.active { background: rgba(255,255,255,.15); color: #fff; border-left-color: #fff; font-weight: 700; }
.sidebar-footer {
  margin-top: auto;
  padding: 16px 20px;
  font-size: .72rem;
  color: rgba(255,255,255,.35);
  border-top: 1px solid rgba(255,255,255,.1);
}

/* ═══════════════════════════════════════════════
   TOPBAR
═══════════════════════════════════════════════ */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 28px;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  z-index: 50;
  box-shadow: 0 1px 8px rgba(108,99,255,.06);
}
.page-title       { font-size: 1.35rem; font-weight: 700; }
.page-title span  { color: var(--primary); }
.topbar-actions   { display: flex; gap: 10px; align-items: center; }

/* ═══════════════════════════════════════════════
   PAGE CONTENT
═══════════════════════════════════════════════ */
.page-content { padding: 24px 28px; }

/* ═══════════════════════════════════════════════
   BUTTONS
═══════════════════════════════════════════════ */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  border-radius: var(--radius-sm);
  font-size: .85rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  border: none;
  transition: .18s;
  white-space: nowrap;
}
.btn-primary   { background: var(--primary);       color: #fff; }
.btn-primary:hover  { background: var(--primary-dark); }
.btn-success   { background: var(--success);       color: #fff; }
.btn-success:hover  { opacity: .88; }
.btn-danger    { background: var(--danger);        color: #fff; }
.btn-danger:hover   { opacity: .88; }
.btn-warning   { background: var(--warning);       color: #fff; }
.btn-secondary { background: #f1f0ff;              color: var(--primary); border: 1.5px solid #ddd8ff; }
.btn-secondary:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-ghost     { background: transparent;          color: var(--muted); border: 1.5px solid var(--border); }
.btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
.btn-sm { padding: 5px 12px; font-size: .78rem; }
.btn-pdf { background: #dc2626; color: #fff; }
.btn-pdf:hover { background: #b91c1c; }
.btn-excel { background: #16a34a; color: #fff; }
.btn-excel:hover { background: #15803d; }

/* ═══════════════════════════════════════════════
   STAT CARDS
═══════════════════════════════════════════════ */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}
.stat-card {
  background: var(--surface);
  border-radius: var(--radius);
  padding: 20px;
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  gap: 6px;
  border-left: 4px solid transparent;
  transition: .2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.stat-icon  { font-size: 1.6rem; margin-bottom: 4px; }
.stat-label { font-size: .73rem; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; font-weight: 600; }
.stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
.stat-sub   { font-size: .75rem; color: var(--muted); }

.stat-card.total     { border-left-color: var(--primary); }
.stat-card.total     .stat-value { color: var(--primary); }
.stat-card.upcoming  { border-left-color: var(--info); }
.stat-card.upcoming  .stat-value { color: var(--info); }
.stat-card.ongoing   { border-left-color: var(--success); }
.stat-card.ongoing   .stat-value { color: var(--success); }
.stat-card.completed { border-left-color: var(--muted); }
.stat-card.completed .stat-value { color: var(--muted); }
.stat-card.cancelled { border-left-color: var(--danger); }
.stat-card.cancelled .stat-value { color: var(--danger); }
.stat-card.online    { border-left-color: var(--accent); }
.stat-card.online    .stat-value { color: var(--accent); }

/* ═══════════════════════════════════════════════
   TOOLBAR (Search + Filters)
═══════════════════════════════════════════════ */
.toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 18px;
  flex-wrap: wrap;
  align-items: center;
}
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-wrap input {
  width: 100%;
  padding: 9px 14px 9px 38px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: .88rem;
  outline: none;
  transition: .2s;
  background: var(--surface);
}
.search-wrap input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(108,99,255,.08); }
.search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: .9rem; }

.filter-group { display: flex; gap: 6px; flex-wrap: wrap; }
.filter-btn {
  padding: 7px 16px;
  border-radius: 50px;
  font-size: .8rem;
  font-weight: 600;
  border: 1.5px solid var(--border);
  background: var(--surface);
  cursor: pointer;
  text-decoration: none;
  color: var(--text);
  transition: .18s;
}
.filter-btn:hover, .filter-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

/* Category filter */
.filter-select {
  padding: 7px 14px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-sm);
  font-size: .85rem;
  outline: none;
  cursor: pointer;
  background: var(--surface);
  color: var(--text);
}
.filter-select:focus { border-color: var(--primary); }

/* ═══════════════════════════════════════════════
   TABLE
═══════════════════════════════════════════════ */
.card {
  background: var(--surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}
.card-header {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-weight: 600;
  font-size: .9rem;
}

table { width: 100%; border-collapse: collapse; }
thead th {
  background: var(--surface2);
  padding: 12px 16px;
  text-align: left;
  font-size: .73rem;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: var(--muted);
  white-space: nowrap;
  user-select: none;
}
.sortable-th {
  cursor: pointer;
  transition: .15s;
}
.sortable-th:hover { color: var(--primary); background: #efedff; }
.sort-indicator { margin-left: 4px; opacity: .5; }
.sort-indicator.active { opacity: 1; color: var(--primary); }

tbody td {
  padding: 13px 16px;
  border-top: 1px solid var(--border);
  vertical-align: middle;
}
tbody tr:hover { background: #fafafe; }

.event-title { font-weight: 600; font-size: .92rem; }
.event-meta  { font-size: .77rem; color: var(--muted); margin-top: 2px; }

/* ═══════════════════════════════════════════════
   BADGES
═══════════════════════════════════════════════ */
.badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 50px;
  font-size: .73rem;
  font-weight: 700;
  white-space: nowrap;
}
.badge-upcoming  { background: var(--info-light);    color: #1d4ed8; }
.badge-ongoing   { background: var(--success-light); color: #15803d; }
.badge-completed { background: #f3f4f6;              color: #6b7280; }
.badge-cancelled { background: var(--danger-light);  color: #b91c1c; }
.badge-online    { background: var(--primary-light);  color: #5b21b6; }
.badge-onsite    { background: var(--warning-light);  color: #b45309; }

/* ═══════════════════════════════════════════════
   ACTIONS
═══════════════════════════════════════════════ */
.actions { display: flex; gap: 5px; }

/* ═══════════════════════════════════════════════
   ALERTS
═══════════════════════════════════════════════ */
.alert {
  padding: 12px 18px;
  border-radius: var(--radius-sm);
  margin-bottom: 18px;
  font-size: .87rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 8px;
}
.alert-success { background: var(--success-light); color: #166534; border-left: 4px solid var(--success); }
.alert-error   { background: var(--danger-light);  color: #991b1b; border-left: 4px solid var(--danger); }
.alert-info    { background: var(--info-light);    color: #1e40af; border-left: 4px solid var(--info); }

/* ═══════════════════════════════════════════════
   EMPTY STATE
═══════════════════════════════════════════════ */
.empty-state {
  text-align: center;
  padding: 56px 20px;
  color: var(--muted);
}
.empty-state .empty-icon { font-size: 3rem; margin-bottom: 12px; opacity: .5; }
.empty-state p { font-size: .92rem; }

/* ═══════════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════════ */
.pagination {
  display: flex;
  gap: 4px;
  align-items: center;
  padding: 14px 20px;
  border-top: 1px solid var(--border);
  justify-content: center;
}
.page-btn {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: .82rem;
  font-weight: 600;
  border: 1.5px solid var(--border);
  background: var(--surface);
  color: var(--text);
  cursor: pointer;
  text-decoration: none;
  transition: .15s;
}
.page-btn:hover, .page-btn.current { background: var(--primary); color: #fff; border-color: var(--primary); }

/* ═══════════════════════════════════════════════
   CHARTS SECTION
═══════════════════════════════════════════════ */
.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}
.chart-card {
  background: var(--surface);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 20px;
}
.chart-title {
  font-size: .88rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* ═══════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════ */
@media(max-width: 900px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.open { transform: translateX(0); }
  .main { margin-left: 0; }
  .page-content { padding: 16px; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .charts-grid { grid-template-columns: 1fr; }
}
@media(max-width: 600px) {
  table { font-size: .8rem; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>
