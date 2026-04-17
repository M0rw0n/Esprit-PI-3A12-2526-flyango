/**
 * Fly&Go Share System
 * Handles sharing of Activities and Circuits to Messenger and Forum
 */

let currentShareData = {
    type: null,
    id: null,
    title: null
};

function openShareModal(type, id, title) {
    currentShareData = { type, id, title };
    const modal = document.getElementById('shareModal');
    if (modal) {
        modal.style.display = 'flex';
        document.getElementById('messengerShareSelection').style.display = 'none';
    }
}

function closeShareModal() {
    const modal = document.getElementById('shareModal');
    if (modal) modal.style.display = 'none';
}

function showMessengerShare() {
    const selection = document.getElementById('messengerShareSelection');
    if (!selection) return;
    selection.style.display = 'block';
    const searchInput = document.getElementById('searchUserInput');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
    }
    searchUsersForShare('');
}

let searchTimeout = null;

async function searchUsersForShare(query) {
    const resultsDiv = document.getElementById('searchUsersResults');
    if (!resultsDiv) {
        console.error('searchUsersResults not found');
        return;
    }
    
    resultsDiv.innerHTML = '<div style="text-align:center;padding:1rem"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>';

    const url = '/api/share/search-users' + (query ? '?q=' + encodeURIComponent(query) : '');
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success && data.users && data.users.length > 0) {
            resultsDiv.innerHTML = '';
            data.users.forEach(user => {
                const avatar = user.avatar ? 
                    '/uploads/avatars/' + user.avatar : 
                    'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name || 'U') + '&background=1B3A6B&color=fff';

                const item = document.createElement('div');
                item.style.cssText = 'display:flex;align-items:center;gap:0.75rem;padding:0.75rem;border-radius:10px;cursor:pointer;transition:background 0.2s;border:1px solid var(--border);margin-bottom:0.5rem';
                item.onclick = () => createConversationAndShare(user.id, user.name);
                item.onmouseover = () => item.style.background = '#f5f5f5';
                item.onmouseout = () => item.style.background = 'transparent';

                item.innerHTML = '<img src="' + avatar + '" style="width:40px;height:40px;border-radius:50%;object-fit:cover">' +
                    '<div style="flex:1">' +
                        '<div style="font-weight:700;font-size:0.9rem;color:var(--text)">' + (user.name || 'User') + '</div>' +
                        '<div style="font-size:0.75rem;color:var(--muted)">' + (user.email || '') + '</div>' +
                    '</div>' +
                    '<button class="btn btn-gold btn-sm" style="padding:0.4rem 1rem"><i class="fa fa-paper-plane"></i> Partager</button>';
                resultsDiv.appendChild(item);
            });
        } else {
            resultsDiv.innerHTML = '<div style="text-align:center;padding:2rem;font-size:0.85rem;color:var(--muted)"><i class="fa fa-user-times" style="font-size:1.5rem;margin-bottom:0.5rem"></i><br>Aucun utilisateur trouvé</div>';
        }
    } catch (err) {
        console.error('Search error:', err);
        resultsDiv.innerHTML = '<div style="text-align:center;padding:1rem;color:red">Erreur de connexion</div>';
    }
}

function onSearchInputChange(value) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => searchUsersForShare(value), 300);
}

async function createConversationAndShare(userId, userName) {
    const resultsDiv = document.getElementById('searchUsersResults');
    if (resultsDiv) {
        resultsDiv.innerHTML = '<div style="text-align:center;padding:2rem"><i class="fa fa-spinner fa-spin" style="font-size:1.5rem"></i><br><br>Création de la conversation...</div>';
    }

    try {
        const response = await fetch('/api/share/create-conversation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();
        if (data.success) {
            sendShareToConversation(data.conversation_id);
        } else {
            showToast('Erreur : ' + (data.error || 'Impossible de créer la conversation'), 'error');
            if (resultsDiv) resultsDiv.innerHTML = '';
        }
    } catch (err) {
        showToast('Erreur réseau', 'error');
        if (resultsDiv) resultsDiv.innerHTML = '';
    }
}

async function sendShareToConversation(conversationId) {
    try {
        const response = await fetch('/api/share/message', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: currentShareData.type,
                item_id: currentShareData.id,
                conversation_id: conversationId
            })
        });

        const data = await response.json();
        if (data.success) {
            showToast('Partagé avec succès !', 'success');
            closeShareModal();
        } else {
            showToast('Erreur : ' + (data.error || 'Impossible de partager'), 'error');
        }
    } catch (err) {
        showToast('Erreur réseau', 'error');
    }
}

async function shareToForumDirect() {
    try {
        const response = await fetch('/api/share/forum', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: currentShareData.type,
                item_id: currentShareData.id
            })
        });

        const data = await response.json();
        if (data.success) {
            showToast('Publié sur le forum !', 'success');
            setTimeout(() => {
                window.location.href = '/forum/' + data.post_id;
            }, 1000);
        } else {
            showToast('Erreur : ' + (data.error || 'Impossible de publier'), 'error');
        }
    } catch (err) {
        showToast('Erreur réseau', 'error');
    }
}

function copyShareLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Lien copié !', 'success');
        closeShareModal();
    });
}

function showToast(message, type) {
    const box = document.getElementById('toastBox');
    if (!box) return;

    const toast = document.createElement('div');
    toast.className = 'toast toast--' + (type || 'info');
    toast.textContent = message;
    box.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

window.onclick = function(event) {
    const modal = document.getElementById('shareModal');
    if (event.target == modal) {
        closeShareModal();
    }
};
