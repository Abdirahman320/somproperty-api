/* ============================================================
   SOM Property Management — Main JavaScript
   Version 2.0.0
   ============================================================ */

'use strict';

/* ── Inline SVG icons used by JS-rendered UI (toasts) ── */
const ICONS = {
  success: '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
  danger:  '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
  warning: '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
  info:    '<svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
};

/* ── CSRF helper ── */
function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

/* ── API client ── */
const API = {
  baseUrl: '/api/v1',

  async request(method, endpoint, data = null) {
    const opts = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
    };
    if (data) opts.body = JSON.stringify(data);

    const res = await fetch(this.baseUrl + endpoint, opts);
    const json = await res.json();

    if (!res.ok) {
      Toast.show(json.message ?? 'Something went wrong. Please try again.', 'danger');
      throw new Error(json.message);
    }
    return json;
  },

  get:    (ep)       => API.request('GET',    ep),
  post:   (ep, data) => API.request('POST',   ep, data),
  put:    (ep, data) => API.request('PUT',    ep, data),
  delete: (ep)       => API.request('DELETE', ep),
};

/* ── Focus trap helper (shared by Modal + Drawer) ── */
const FocusTrap = {
  selector: 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',

  create(container) {
    const handler = (e) => {
      if (e.key !== 'Tab') return;
      const items = [...container.querySelectorAll(this.selector)]
        .filter(el => el.offsetParent !== null);
      if (!items.length) return;
      const first = items[0];
      const last  = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault(); last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault(); first.focus();
      }
    };
    container.addEventListener('keydown', handler);
    return () => container.removeEventListener('keydown', handler);
  },
};

/* ── Toast notifications ── */
const Toast = {
  container: null,

  init() {
    this.container = document.getElementById('toast-container');
    if (!this.container) {
      this.container = document.createElement('div');
      this.container.id = 'toast-container';
      this.container.setAttribute('role', 'status');
      this.container.setAttribute('aria-live', 'polite');
      document.body.appendChild(this.container);
    }
  },

  show(msg, type = 'info', duration = 3800) {
    if (!this.container) this.init();
    const el = document.createElement('div');
    el.className = `toast is-${type}`;
    el.innerHTML = `${ICONS[type] ?? ICONS.info}<span>${msg}</span>`;
    this.container.appendChild(el);
    setTimeout(() => {
      el.classList.add('leaving');
      setTimeout(() => el.remove(), 320);
    }, duration);
  },
};

/* ── Modal ── */
const Modal = {
  backdrop: null,
  releaseTrap: null,
  lastFocused: null,

  init() {
    this.backdrop = document.getElementById('modal-backdrop');
    this.backdrop?.addEventListener('click', e => {
      if (e.target === this.backdrop) this.close();
    });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && this.backdrop?.classList.contains('open')) this.close();
    });
  },

  open(title, bodyHTML) {
    const t = document.getElementById('modal-title');
    const b = document.getElementById('modal-body');
    if (t) t.textContent = title;
    if (b) b.innerHTML = bodyHTML;
    this.lastFocused = document.activeElement;
    this.backdrop?.classList.add('open');
    document.body.classList.add('no-scroll');
    if (this.backdrop) {
      this.releaseTrap = FocusTrap.create(this.backdrop);
      const box = document.getElementById('modal-box');
      (box?.querySelector(FocusTrap.selector) || box)?.focus();
    }
  },

  close() {
    this.backdrop?.classList.remove('open');
    document.body.classList.remove('no-scroll');
    this.releaseTrap?.(); this.releaseTrap = null;
    this.lastFocused?.focus?.();
  },
};

/* ── Off-canvas navigation drawer (mobile) ── */
const Drawer = {
  sidebar: null,
  overlay: null,
  toggleBtn: null,
  releaseTrap: null,
  lastFocused: null,

  init() {
    this.sidebar   = document.querySelector('.sidebar');
    this.overlay   = document.querySelector('.sidebar-overlay');
    this.toggleBtn = document.querySelector('.hamburger');
    if (!this.sidebar || !this.toggleBtn) return;

    this.toggleBtn.addEventListener('click', () => this.open());
    this.overlay?.addEventListener('click', () => this.close());
    this.sidebar.querySelector('.sidebar-close')?.addEventListener('click', () => this.close());
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && this.isOpen()) this.close();
    });
    // Close after navigating on mobile
    this.sidebar.querySelectorAll('.nav-item').forEach(item =>
      item.addEventListener('click', () => { if (this.isOpen()) this.close(); }));
    // Reset state if resized up to desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768 && this.isOpen()) this.close();
    });
  },

  isOpen() { return this.sidebar?.classList.contains('open'); },

  open() {
    this.lastFocused = document.activeElement;
    this.sidebar.classList.add('open');
    this.overlay?.classList.add('open');
    this.toggleBtn.setAttribute('aria-expanded', 'true');
    document.body.classList.add('no-scroll');
    this.releaseTrap = FocusTrap.create(this.sidebar);
    this.sidebar.querySelector('.sidebar-close, .nav-item')?.focus();
  },

  close() {
    this.sidebar.classList.remove('open');
    this.overlay?.classList.remove('open');
    this.toggleBtn.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('no-scroll');
    this.releaseTrap?.(); this.releaseTrap = null;
    this.lastFocused?.focus?.();
  },
};

/* ── Confirm dialog ── */
function confirmAction(msg, onConfirm) {
  if (window.confirm(msg)) onConfirm();
}

/* ── Billing calculator ── */
function calcBillTotal() {
  const rent     = parseFloat(document.getElementById('rent_amount')?.value)     || 0;
  const water    = parseFloat(document.getElementById('water_amount')?.value)    || 0;
  const electric = parseFloat(document.getElementById('electric_amount')?.value) || 0;
  const other    = parseFloat(document.getElementById('other_charges')?.value)   || 0;
  const total    = rent + water + electric + other;
  const el = document.getElementById('bill-total');
  if (el) el.textContent = '$' + total.toFixed(2);
}

/* ── Utility reading calculator ── */
function calcUtility(type) {
  const prev = parseFloat(document.getElementById(type + '_prev')?.value) || 0;
  const curr = parseFloat(document.getElementById(type + '_curr')?.value) || 0;
  const rate = parseFloat(document.getElementById(type + '_rate')?.value) || 0;
  const consumption = Math.max(0, curr - prev);
  const amount      = consumption * rate;
  const consEl = document.getElementById(type + '_consumption');
  const amtEl  = document.getElementById(type + '_amount_display');
  if (consEl) consEl.textContent = consumption.toFixed(3);
  if (amtEl)  amtEl.textContent  = '$' + amount.toFixed(2);
  const hiddenEl = document.getElementById(type + '_calculated');
  if (hiddenEl) hiddenEl.value = amount.toFixed(2);
  calcBillTotal();
}

/* ── DataTable simple sort ── */
function initSortableTable(tableId) {
  const table = document.getElementById(tableId);
  if (!table) return;
  table.querySelectorAll('th[data-sort]').forEach(th => {
    th.setAttribute('role', 'button');
    th.setAttribute('tabindex', '0');
    const run = () => {
      const col = Array.from(th.parentNode.children).indexOf(th);
      const asc = th.dataset.dir !== 'asc';
      table.querySelectorAll('th[data-sort]').forEach(o => { if (o !== th) delete o.dataset.dir; });
      th.dataset.dir = asc ? 'asc' : 'desc';
      const rows = Array.from(table.querySelectorAll('tbody tr'));
      rows.sort((a, b) => {
        const av = a.children[col]?.textContent.trim() ?? '';
        const bv = b.children[col]?.textContent.trim() ?? '';
        return asc ? av.localeCompare(bv, undefined, {numeric:true})
                   : bv.localeCompare(av, undefined, {numeric:true});
      });
      const tbody = table.querySelector('tbody');
      rows.forEach(r => tbody.appendChild(r));
    };
    th.addEventListener('click', run);
    th.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); run(); } });
  });
}

/* ── Search filter ── */
function filterTable(inputId, tableId) {
  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);
  if (!input || !table) return;
  input.addEventListener('input', () => {
    const q = input.value.toLowerCase();
    table.querySelectorAll('tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}

/* ── Sparkline renderer — draws an inline SVG trend from data ── */
function renderSparklines() {
  document.querySelectorAll('[data-sparkline]').forEach(svg => {
    let pts;
    try { pts = JSON.parse(svg.dataset.sparkline); } catch { return; }
    if (!Array.isArray(pts) || pts.length < 2) return;
    const w = 100, h = 32, pad = 2;
    const max = Math.max(...pts), min = Math.min(...pts);
    const range = (max - min) || 1;
    const step = (w - pad * 2) / (pts.length - 1);
    const coords = pts.map((v, i) => {
      const x = pad + i * step;
      const y = pad + (h - pad * 2) * (1 - (v - min) / range);
      return [x, y];
    });
    const line = coords.map((c, i) => (i ? 'L' : 'M') + c[0].toFixed(1) + ' ' + c[1].toFixed(1)).join(' ');
    const area = line + ` L${(w - pad).toFixed(1)} ${h - pad} L${pad} ${h - pad} Z`;
    svg.setAttribute('viewBox', `0 0 ${w} ${h}`);
    svg.setAttribute('preserveAspectRatio', 'none');
    svg.innerHTML = `<path class="area" d="${area}"/><path class="line" d="${line}"/>`;
  });
}

/* ── Chart helper (uses Chart.js if loaded) ── */
function renderBarChart(canvasId, labels, data, label = 'Revenue', color = '#2563eb') {
  if (typeof Chart === 'undefined') return;
  const ctx = document.getElementById(canvasId)?.getContext('2d');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{ label, data, backgroundColor: color, borderRadius: 6, borderSkipped: false, maxBarThickness: 44 }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { padding: 10, cornerRadius: 8 } },
      scales: {
        y: { beginAtZero: true, grid: { color: '#eef2f6' }, ticks: { color: '#5b6675', font: { size: 11 } }, border: { display: false } },
        x: { grid: { display: false }, ticks: { color: '#5b6675', font: { size: 11 } }, border: { display: false } },
      },
    },
  });
}

function renderDoughnutChart(canvasId, labels, data, colors) {
  if (typeof Chart === 'undefined') return;
  const ctx = document.getElementById(canvasId)?.getContext('2d');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0, hoverOffset: 6 }] },
    options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 14, font: { size: 12 } } } } },
  });
}

/* ── Notification sender ── */
async function sendNotification(formId) {
  const form     = document.getElementById(formId);
  const formData = new FormData(form);
  const data     = Object.fromEntries(formData.entries());
  try {
    await API.post('/owner/notifications/send', data);
    Toast.show('Notifications sent via app and email', 'success');
    Modal.close();
  } catch (_) {}
}

/* ── Print bill ── */
function printBill(billId) {
  window.open(`/owner/billing/bills/${billId}/print`, '_blank');
}

/* ── Format currency ── */
function formatCurrency(amount, symbol = '$') {
  return symbol + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/* ── DOM ready ── */
/* ---------------------------------------------------------------
   Unobtrusive show/hide toggles
   [data-toggle="#id"] flips .d-none on target (and focuses first field)
   [data-hide="#id"]   hides target
--------------------------------------------------------------- */
function initToggles() {
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-toggle]');
    if (t) {
      const el = document.querySelector(t.getAttribute('data-toggle'));
      if (el) {
        el.classList.toggle('d-none');
        if (!el.classList.contains('d-none')) {
          const f = el.querySelector('input, select, textarea');
          if (f) f.focus();
        }
      }
      return;
    }
    const h = e.target.closest('[data-hide]');
    if (h) {
      const el = document.querySelector(h.getAttribute('data-hide'));
      if (el) el.classList.add('d-none');
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  Toast.init();
  Modal.init();
  Drawer.init();
  renderSparklines();
  initToggles();
});
