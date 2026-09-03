(() => {
  'use strict';

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  // Theme (dark default, persisted locally)
  const root = document.documentElement;
  const storedTheme = localStorage.getItem('sigas-theme');
  if (storedTheme === 'light' || storedTheme === 'dark') root.dataset.theme = storedTheme;
  const themeToggle = $('[data-theme-toggle]');
  const themeIcon = $('[data-theme-icon]');
  const iconMoon = '<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg>';
  const iconSun = '<svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/></svg>';
  const syncThemeIcon = () => { if (themeIcon) themeIcon.innerHTML = root.dataset.theme === 'light' ? iconSun : iconMoon; };
  syncThemeIcon();
  themeToggle?.addEventListener('click', () => {
    root.dataset.theme = root.dataset.theme === 'light' ? 'dark' : 'light';
    localStorage.setItem('sigas-theme', root.dataset.theme);
    syncThemeIcon();
  });

  // Responsive sidebar
  const sidebar = $('[data-sidebar]');
  const overlay = $('[data-sidebar-overlay]');
  const setSidebar = (open) => {
    sidebar?.classList.toggle('open', open);
    overlay?.classList.toggle('open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  };
  $('[data-sidebar-open]')?.addEventListener('click', () => setSidebar(true));
  $('[data-sidebar-close]')?.addEventListener('click', () => setSidebar(false));
  overlay?.addEventListener('click', () => setSidebar(false));
  window.addEventListener('resize', () => { if (window.innerWidth > 960) setSidebar(false); });

  // Global keyboard search shortcut
  const globalSearchInput = $('.global-search input');
  document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k' && globalSearchInput) {
      e.preventDefault(); globalSearchInput.focus(); globalSearchInput.select();
    }
  });

  // Password visibility
  const password = $('#passwordInput');
  $('#passwordToggle')?.addEventListener('click', () => {
    if (!password) return;
    password.type = password.type === 'password' ? 'text' : 'password';
  });

  // Flash toast
  $$('[data-toast]').forEach((toast) => {
    const hide = () => { toast.classList.add('is-hiding'); setTimeout(() => toast.remove(), 260); };
    $('[data-toast-close]', toast)?.addEventListener('click', hide);
    setTimeout(hide, 6500);
  });

  // Collapsible advanced filters
  const filterPanel = $('[data-filter-panel]');
  const filterToggle = $('[data-filter-toggle]');
  if (filterPanel && filterToggle) {
    const hasValue = $$('select,input', filterPanel).some((el) => el.value && el.value !== new Date().getFullYear().toString());
    filterPanel.hidden = !hasValue;
    filterToggle.addEventListener('click', () => { filterPanel.hidden = !filterPanel.hidden; });
  }

  // Confirmation attributes
  $$('[data-confirm]').forEach((el) => {
    el.addEventListener('click', (event) => {
      const message = el.dataset.confirm || 'Lanjutkan tindakan ini?';
      if (!window.confirm(message)) event.preventDefault();
    });
  });

  // Loading states on submit
  $$('[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (event.defaultPrevented || !form.checkValidity()) return;
      form.classList.add('is-loading');
      const submit = $('button[type="submit"]', form);
      if (submit) submit.setAttribute('aria-busy', 'true');
    });
  });

  // Copy helper
  $$('[data-copy-target]').forEach((button) => {
    button.addEventListener('click', async () => {
      const target = $(button.dataset.copyTarget);
      if (!target) return;
      const value = target.textContent.trim();
      try {
        await navigator.clipboard.writeText(value);
      } catch (_) {
        const ta = document.createElement('textarea'); ta.value = value; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
      }
      const tip = document.createElement('div'); tip.className = 'copy-feedback'; tip.textContent = 'Nomor surat disalin'; document.body.appendChild(tip); setTimeout(() => tip.remove(), 1800);
    });
  });

  // Generate number form: cascading KKA + live preview
  const budget = $('#usesBudget');
  const archive = $('#archiveType');
  const parent = $('#classificationParent');
  const child = $('#classificationChild');
  const scopePreview = $('#scopePreview');
  const typeSelect = $('#letterType');
  const sensitivity = $('#sensitivity');
  const letterDate = $('#letterDate');
  const numberPreview = $('#numberPreview');

  if (budget && archive && parent && child) {
    const getScope = () => budget.value && archive.value ? budget.value + (archive.value === 'FASILITATIF' ? 'F' : 'S') : '';
    const escapeHtml = (v) => String(v).replace(/[&<>'"]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[c]));
    const fill = (select, rows, placeholder, selected = '') => {
      select.innerHTML = `<option value="">${escapeHtml(placeholder)}</option>` + rows.map(r => `<option value="${escapeHtml(r.code)}" ${r.code === selected ? 'selected' : ''}>${escapeHtml(r.code)} — ${escapeHtml(r.name)}</option>`).join('');
      select.disabled = false;
    };
    const refreshNumberPreview = () => {
      if (!numberPreview) return;
      const typeOption = typeSelect?.selectedOptions?.[0];
      const sensOption = sensitivity?.selectedOptions?.[0];
      const prefix = sensOption?.dataset?.prefix || '?';
      const unit = typeOption?.dataset?.unit || '62710';
      const kka = parent.value && child.value ? `${parent.value}.${child.value}` : 'KKA.000';
      let year = new Date().getFullYear();
      if (letterDate?.value) year = letterDate.value.slice(0,4);
      $('.np-prefix', numberPreview).textContent = `${prefix}-???`;
      $('.np-unit', numberPreview).textContent = unit;
      $('.np-kka', numberPreview).textContent = kka;
      $('.np-year', numberPreview).textContent = year;
    };
    async function loadParents(restoreChild = true) {
      const scope = getScope();
      if (scopePreview) scopePreview.textContent = scope || '—';
      parent.disabled = true; child.disabled = true;
      parent.innerHTML = '<option value="">Pilih kondisi dahulu</option>';
      child.innerHTML = '<option value="">Pilih KKA level 2 dahulu</option>';
      refreshNumberPreview();
      if (!scope) return;
      try {
        const res = await fetch(`/api/classifications?scope=${encodeURIComponent(scope)}`, {headers:{'Accept':'application/json'}});
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat klasifikasi');
        const oldParent = parent.dataset.old || '';
        fill(parent, json.data, 'Pilih KKA level 2', oldParent);
        if (oldParent && restoreChild) {
          await loadChildren(oldParent, child.dataset.old || '');
          parent.dataset.old = ''; child.dataset.old = '';
        }
      } catch (err) {
        parent.innerHTML = '<option value="">Gagal memuat data</option>';
      }
    }
    async function loadChildren(parentValue, selected = '') {
      child.disabled = true;
      child.innerHTML = '<option value="">Memuat...</option>';
      refreshNumberPreview();
      if (!parentValue) { child.innerHTML = '<option value="">Pilih KKA level 2 dahulu</option>'; return; }
      try {
        const res = await fetch(`/api/classifications?scope=${encodeURIComponent(getScope())}&parent=${encodeURIComponent(parentValue)}`, {headers:{'Accept':'application/json'}});
        const json = await res.json();
        if (!json.success) throw new Error(json.message || 'Gagal memuat klasifikasi');
        fill(child, json.data, 'Pilih KKA level 3', selected);
        refreshNumberPreview();
      } catch (err) {
        child.innerHTML = '<option value="">Gagal memuat data</option>';
      }
    }
    budget.addEventListener('change', () => loadParents(false));
    archive.addEventListener('change', () => loadParents(false));
    parent.addEventListener('change', () => loadChildren(parent.value));
    child.addEventListener('change', refreshNumberPreview);
    typeSelect?.addEventListener('change', refreshNumberPreview);
    sensitivity?.addEventListener('change', refreshNumberPreview);
    letterDate?.addEventListener('change', refreshNumberPreview);
    if (getScope()) loadParents(true); else refreshNumberPreview();
  }
})();
