/* ============================================
   DASHBOARD.JS - Admin Dashboard
   Dynamic search, filters & sort via AJAX
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── User tab elements ── */
    const searchInput  = document.getElementById('user-search');
    const statusFilter = document.getElementById('status-filter');
    const roleFilter   = document.getElementById('role-filter');
    const sortFilter   = document.getElementById('sort-filter');
    const usersGrid    = document.getElementById('users-grid');
    const resultsCount = document.getElementById('results-count');
    const spinner      = document.getElementById('search-spinner');

    /* ── Attach listeners ── */
    if (searchInput)  searchInput .addEventListener('input',  debounce(fetchUsers, 300));
    if (statusFilter) statusFilter.addEventListener('change', fetchUsers);
    if (roleFilter)   roleFilter  .addEventListener('change', fetchUsers);
    if (sortFilter)   sortFilter  .addEventListener('change', fetchUsers);

    /* ── Profile tab filters ── */
    const typeVoyageButtons = document.querySelectorAll('.type-filter');
    typeVoyageButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            typeVoyageButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyProfileFilters();
        });
    });

    const destinationFilter = document.getElementById('destination-filter');
    const budgetMin         = document.getElementById('budget-min');
    const budgetMax         = document.getElementById('budget-max');

    if (destinationFilter) destinationFilter.addEventListener('input', debounce(applyProfileFilters, 300));
    if (budgetMin)         budgetMin        .addEventListener('input', debounce(applyProfileFilters, 400));
    if (budgetMax)         budgetMax        .addEventListener('input', debounce(applyProfileFilters, 400));

    /* ══════════════════════════════════════════
       AJAX user search & filter
       ══════════════════════════════════════════ */
    function fetchUsers() {
        if (!usersGrid) return;

        const search = searchInput?.value.trim()  || '';
        const status = statusFilter?.value        || '';
        const role   = roleFilter?.value          || '';
        const sort   = sortFilter?.value          || 'date_desc';

        /* Build query params */
        const params = new URLSearchParams();
        if (search) params.set('q',      search);
        if (status) params.set('status', status);
        if (role)   params.set('role',   role);
        if (sort)   params.set('sort',   sort);

        /* Show spinner */
        if (spinner) spinner.classList.remove('d-none');

        fetch('/admin/users/search?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) throw new Error('Erreur réseau');
            return res.json();
        })
        .then(users => {
            renderUsers(users, sort);
        })
        .catch(() => {
            /* Fallback: full page reload if AJAX fails */
            applyFiltersPageReload();
        })
        .finally(() => {
            if (spinner) spinner.classList.add('d-none');
        });
    }

    /* ── Render user cards from JSON ── */
    function renderUsers(users, sort) {
        if (!usersGrid) return;

        /* Client-side sort (server already sorts, but just in case) */
        users = sortUsers(users, sort);

        if (users.length === 0) {
            usersGrid.innerHTML = `
                <div class="alert alert-info w-100">
                    Aucun utilisateur trouvé
                </div>`;
            if (resultsCount) resultsCount.textContent = '0 résultat(s)';
            return;
        }

        usersGrid.innerHTML = users.map(u => buildUserCard(u)).join('');
        if (resultsCount) resultsCount.textContent = users.length + ' résultat(s)';

        /* Re-bind delete confirmations on new cards */
        bindDeleteForms();
    }

    /* ── Build a user card HTML from JSON data ── */
    function buildUserCard(u) {
        const initials     = getInitials(u.nom_complet);
        const avatarColor  = pickColor(initials);
        const isAdmin      = u.role === 'ADMIN';
        const isActive     = u.actif;

        const avatarHtml = `
            <div class="user-avatar-initials"
                 style="background: linear-gradient(135deg, ${avatarColor}, ${darken(avatarColor)});">
                ${escHtml(initials)}
            </div>`;

        return `
        <div class="user-card">
            <div class="user-card-avatar">${avatarHtml}</div>

            <div class="user-card-info">
                <h5 class="user-card-name">${escHtml(u.nom_complet)}</h5>
                <p class="user-card-email">${escHtml(u.email)}</p>
                <p class="user-card-phone">${escHtml(u.phone || '-')}</p>

                <div class="user-card-badges">
                    <span class="badge badge-role ${isAdmin ? 'badge-admin' : 'badge-voyageur'}">
                        ${escHtml(u.role)}
                    </span>
                    <span class="badge ${isActive ? 'badge-success' : 'badge-danger'}">
                        ${isActive ? '✅ Actif' : '❌ Inactif'}
                    </span>
                </div>

                <p class="user-card-date">
                    Créé: ${formatDate(u.created_at)}
                </p>
            </div>

            <div class="user-card-actions">
                <a href="/admin/users/${u.id}/edit"
                   class="btn btn-sm btn-outline-primary">✏️ Éditer</a>

                <form method="POST" action="/admin/users/${u.id}/delete"
                      style="display:inline;" class="delete-form">
                    <input type="hidden" name="_token" value="${escHtml(u.csrf_delete || '')}">
                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Supprimer</button>
                </form>

                <form method="POST" action="/admin/users/${u.id}/toggle-active"
                      style="display:inline;">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        ${isActive ? '📴 Désactiver' : '✅ Activer'}
                    </button>
                </form>
            </div>
        </div>`;
    }

    /* ── Sort users array client-side ── */
    function sortUsers(users, sort) {
        return [...users].sort((a, b) => {
            switch (sort) {
                case 'name_asc':
                    return a.nom_complet.localeCompare(b.nom_complet);
                case 'name_desc':
                    return b.nom_complet.localeCompare(a.nom_complet);
                case 'date_asc':
                    return new Date(a.created_at || 0) - new Date(b.created_at || 0);
                case 'date_desc':
                default:
                    return new Date(b.created_at || 0) - new Date(a.created_at || 0);
            }
        });
    }

    /* ── Bind delete confirmation to dynamic cards ── */
    function bindDeleteForms() {
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                    e.preventDefault();
                }
            });
        });
    }

    /* Initial bind for server-rendered cards */
    bindDeleteForms();

    /* ══════════════════════════════════════════
       Profile filters (page reload — no AJAX
       endpoint for profiles yet)
       ══════════════════════════════════════════ */
    function applyProfileFilters() {
        const typeVoyageBtn  = document.querySelector('.type-filter.active');
        const typeVoyage     = typeVoyageBtn?.dataset.type      || '';
        const destination    = destinationFilter?.value.trim()  || '';
        const budgetMinValue = budgetMin?.value                  || '';
        const budgetMaxValue = budgetMax?.value                  || '';

        const url = new URL('/admin/profiles', window.location.origin);
        if (typeVoyage)     url.searchParams.set('type_voyage',  typeVoyage);
        if (destination)    url.searchParams.set('destination',  destination);
        if (budgetMinValue) url.searchParams.set('budget_min',   budgetMinValue);
        if (budgetMaxValue) url.searchParams.set('budget_max',   budgetMaxValue);

        window.location.href = url.toString();
    }

    /* ── Fallback full-page reload for user filters ── */
    function applyFiltersPageReload() {
        const search = searchInput?.value.trim()  || '';
        const status = statusFilter?.value        || '';
        const role   = roleFilter?.value          || '';
        const sort   = sortFilter?.value          || 'date_desc';

        const url = new URL('/admin/users', window.location.origin);
        if (search) url.searchParams.set('search', search);
        if (status) url.searchParams.set('status', status);
        if (role)   url.searchParams.set('role',   role);
        if (sort)   url.searchParams.set('sort',   sort);

        window.location.href = url.toString();
    }

    /* ══════════════════════════════════════════
       Helpers
       ══════════════════════════════════════════ */

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function getInitials(fullName) {
        const parts = (fullName || '').trim().split(/\s+/);
        if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
        return (parts[0] || '?')[0].toUpperCase();
    }

    function formatDate(iso) {
        if (!iso) return '-';
        try {
            return new Date(iso).toLocaleDateString('fr-FR', {
                day: '2-digit', month: '2-digit', year: 'numeric'
            });
        } catch { return iso; }
    }

    const PALETTE = [
        '#16a085','#1abc9c','#27ae60','#3498db',
        '#2980b9','#9b59b6','#e67e22','#e74c3c',
    ];

    function pickColor(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash);
        return PALETTE[Math.abs(hash) % PALETTE.length];
    }

    function darken(hex) {
        const num = parseInt(hex.replace('#', ''), 16);
        const amt = -30;
        const r = Math.min(255, Math.max(0, (num >> 16) + amt));
        const g = Math.min(255, Math.max(0, ((num >> 8) & 0xff) + amt));
        const b = Math.min(255, Math.max(0, (num & 0xff) + amt));
        return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
    }

});

console.log('Dashboard JS Loaded ✓');
