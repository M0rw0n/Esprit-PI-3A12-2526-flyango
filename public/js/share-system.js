/**
 * Complete Share System - Fly&Go
 * Features: Copy link, WhatsApp, Email, Messenger (internal), Preview
 */

function openShareModal(postId, postTitle, postContent) {
    var existingModal = document.getElementById('shareModal');
    if (existingModal) existingModal.remove();

    var postUrl = window.location.origin + '/forum/' + postId;
    var shareUrl = encodeURIComponent(postUrl);
    
    var modal = document.createElement('div');
    modal.id = 'shareModal';
    modal.style.position = 'fixed';
    modal.style.inset = '0';
    modal.style.background = 'rgba(0,0,0,0.5)';
    modal.style.zIndex = '2000';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.animation = 'fadeIn 0.2s ease';
    
    modal.onclick = function(e) {
        if (e.target === modal) closeShareModal();
    };

    modal.innerHTML = '<div style="background: white; border-radius: 16px; max-width: 500px; width: 95%; max-height: 90vh; overflow: hidden; box-shadow: 0 10px 50px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;">' +
        '<div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #1B3A6B, #00B4D8); color: white;">' +
            '<h3 style="margin:0;font-size:1.15rem;font-weight:700"><i class="fa fa-share-alt"></i> Partager</h3>' +
            '<button onclick="closeShareModal()" style="background: rgba(255,255,255,0.2); border: none; font-size: 1.5rem; cursor: pointer; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">×</button>' +
        '</div>' +
        '<div style="padding: 1rem 1.5rem; border-bottom: 1px solid #eee; background: #f8f9fa;">' +
            '<div style="display: flex; gap: 0.75rem; align-items: flex-start;">' +
                '<div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #1B3A6B, #00B4D8); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; flex-shrink: 0;">' +
                    '<i class="fa fa-file-text"></i>' +
                '</div>' +
                '<div style="flex:1;min-width:0">' +
                    '<div style="font-weight:600;font-size:.9rem;color:#1C1C1E;margin-bottom:.25rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escapeHtml(postTitle || 'Post du forum') + '</div>' +
                    '<div style="font-size:.8rem;color:#65676B;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + escapeHtml(postContent || 'Cliquez pour voir le post complet...') + '</div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        '<div style="padding: 1rem 1.5rem">' +
            '<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1rem;">' +
                '<div onclick="copyPostLink(\'' + escapeHtml(postUrl) + '\')" style="display: flex; flex-direction: column; align-items: center; padding: 1rem 0.5rem; border-radius: 12px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;" onmouseover="this.style.borderColor=\'#1B3A6B\';this.style.background=\'#f0f7ff\'" onmouseout="this.style.borderColor=\'transparent\';this.style.background=\'transparent\'">' +
                    '<div style="width: 50px; height: 50px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">' +
                        '<i class="fa fa-link" style="font-size:1.3rem;color:#1B3A6B"></i>' +
                    '</div>' +
                    '<span style="font-size:.75rem;font-weight:600;color:#1C1C1E">Copier</span>' +
                '</div>' +
                '<div onclick="shareWhatsApp(\'' + shareUrl + '\', \'' + escapeHtml(postTitle || 'Post') + '\')" style="display: flex; flex-direction: column; align-items: center; padding: 1rem 0.5rem; border-radius: 12px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;" onmouseover="this.style.borderColor=\'#25D366\';this.style.background=\'#f0fff4\'" onmouseout="this.style.borderColor=\'transparent\';this.style.background=\'transparent\'">' +
                    '<div style="width: 50px; height: 50px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">' +
                        '<i class="fa fa-whatsapp" style="font-size:1.5rem;color:#25D366"></i>' +
                    '</div>' +
                    '<span style="font-size:.75rem;font-weight:600;color:#1C1C1E">WhatsApp</span>' +
                '</div>' +
                '<div onclick="shareEmail(\'' + shareUrl + '\', \'' + escapeHtml(postTitle || 'Post') + '\')" style="display: flex; flex-direction: column; align-items: center; padding: 1rem 0.5rem; border-radius: 12px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;" onmouseover="this.style.borderColor=\'#EA4335\';this.style.background=\'#fff8f8\'" onmouseout="this.style.borderColor=\'transparent\';this.style.background=\'transparent\'">' +
                    '<div style="width: 50px; height: 50px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">' +
                        '<i class="fa fa-envelope" style="font-size:1.3rem;color:#EA4335"></i>' +
                    '</div>' +
                    '<span style="font-size:.75rem;font-weight:600;color:#1C1C1E">Email</span>' +
                '</div>' +
                '<div onclick="openMessengerShare(' + postId + ')" style="display: flex; flex-direction: column; align-items: center; padding: 1rem 0.5rem; border-radius: 12px; cursor: pointer; transition: all 0.2s; border: 2px solid transparent;" onmouseover="this.style.borderColor=\'#00B4D8\';this.style.background=\'#f0fdff\'" onmouseout="this.style.borderColor=\'transparent\';this.style.background=\'transparent\'">' +
                    '<div style="width: 50px; height: 50px; border-radius: 50%; background: #f0f2f5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.5rem;">' +
                        '<i class="fa fa-comments" style="font-size:1.3rem;color:#00B4D8"></i>' +
                    '</div>' +
                    '<span style="font-size:.75rem;font-weight:600;color:#1C1C1E">Message</span>' +
                '</div>' +
            '</div>' +
            '<div id="messengerSharePanel" style="display:none">' +
                '<div style="font-size:.8rem;font-weight:600;color:#1C1C1E;margin-bottom:.5rem;display:flex;align-items:center;gap:.5rem">' +
                    '<i class="fa fa-comment" style="color:#00B4D8"></i> Envoyer via Fly&Go Messages' +
                '</div>' +
                '<textarea id="shareMessageText" placeholder="Écrivez un message..." rows="2" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; font-size: .9rem; resize: none; font-family: inherit; margin-bottom: 0.75rem;"></textarea>' +
                '<div style="margin-bottom:.75rem">' +
                    '<input type="text" id="shareConvSearch" placeholder="Rechercher une conversation..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #ddd; border-radius: 8px; font-size: .9rem; background: #f8f9fa;" oninput="filterShareConversations(this.value)">' +
                '</div>' +
                '<div id="shareConversationsList" style="max-height: 200px; overflow-y: auto; border-radius: 8px; border: 1px solid #eee;">' +
                    '<div style="padding:2rem;text-align:center;color:#65676B"><i class="fa fa-spinner fa-spin"></i><div style="margin-top:.5rem">Chargement...</div></div>' +
                '</div>' +
                '<div id="shareNewChatBtn" style="margin-top: 0.75rem; display: flex; gap: 0.5rem;">' +
                    '<input type="text" id="shareNewUserSearch" placeholder="Créer nouvelle conversation..." style="flex: 1; padding: 0.6rem 1rem; border: 1px solid #ddd; border-radius: 20px; font-size: .85rem;">' +
                    '<button onclick="startNewShareConversation(document.getElementById(\'shareNewUserSearch\').value, ' + postId + ')" style="padding: 0.6rem 1rem; background: linear-gradient(135deg, #1B3A6B, #00B4D8); color: white; border: none; border-radius: 20px; font-size: .85rem; font-weight: 600; cursor: pointer;"><i class="fa fa-plus"></i></button>' +
                '</div>' +
            '</div>' +
            '<div id="copySuccessAlert" style="display: none; padding: 0.75rem; background: #d4edda; border-radius: 8px; color: #155724; font-size: .85rem; text-align: center; margin-top: 0.75rem;">' +
                '<i class="fa fa-check-circle"></i> Lien copié dans le presse-papiers !' +
            '</div>' +
        '</div>' +
        '<input type="hidden" id="sharePostId" value="' + postId + '">' +
        '<input type="hidden" id="sharePostUrl" value="' + postUrl + '">' +
    '</div>';

    document.body.appendChild(modal);
}

function closeShareModal() {
    var modal = document.getElementById('shareModal');
    if (modal) modal.remove();
}

function copyPostLink(url) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() { showCopyAlert(); }).catch(function() { fallbackCopy(url); });
    } else {
        fallbackCopy(url);
    }
}

function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    ta.style.pointerEvents = 'none';
    document.body.appendChild(ta);
    ta.select();
    try {
        document.execCommand('copy');
        showCopyAlert();
    } catch(e) {
        showNotification('Erreur', 'Impossible de copier');
    }
    document.body.removeChild(ta);
}

function showCopyAlert() {
    var alert = document.getElementById('copySuccessAlert');
    if (alert) {
        alert.style.display = 'block';
        setTimeout(function() { alert.style.display = 'none'; }, 3000);
    }
}

function shareWhatsApp(url, title) {
    var text = encodeURIComponent(title + '\n\n' + url);
    window.open('https://wa.me/?text=' + text, '_blank', 'width=600,height=400');
}

function shareEmail(url, title) {
    var subject = encodeURIComponent('Regarde ce post : ' + title);
    var body = encodeURIComponent('Je wanted to share this with you:\n\n' + url);
    window.location.href = 'mailto:?subject=' + subject + '&body=' + body;
}

function openMessengerShare(postId) {
    var panel = document.getElementById('messengerSharePanel');
    if (panel.style.display === 'block') {
        panel.style.display = 'none';
        return;
    }
    panel.style.display = 'block';
    loadShareConversations();
}

function loadShareConversations() {
    var list = document.getElementById('shareConversationsList');
    if (!list) return;
    
    list.innerHTML = '<div style="padding:2rem;text-align:center;color:#65676B"><i class="fa fa-spinner fa-spin"></i><div style="margin-top:.5rem">Chargement...</div></div>';
    
    fetch('/api/share/get-conversations', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success || !data.conversations || data.conversations.length === 0) {
            list.innerHTML = '<div style="padding:2rem;text-align:center;color:#65676B"><i class="fa fa-comments" style="font-size:2rem;opacity:.3"></i><div style="margin-top:.75rem;font-weight:600">Aucune conversation</div><div style="font-size:.8rem;margin-top:.25rem">Créez-en une nouvelle ci-dessous</div></div>';
            return;
        }
        
        var html = '';
        data.conversations.forEach(function(c) {
            var name = c.otherUser ? c.otherUser.name : (c.name || 'Conversation');
            var avatar = c.otherUser && c.otherUser.avatar ? c.otherUser.avatar : '';
            var initial = name.charAt(0).toUpperCase();
            
            html += '<div class="share-conv-item" data-name="' + name.toLowerCase() + '" onclick="sendToConversation(' + c.id + ', ' + (c.otherUser ? c.otherUser.id : 0) + ')" style="display:flex;align-items:center;gap:.75rem;padding:1rem;border-bottom:1px solid #eee;cursor:pointer;transition:background .2s" onmouseover="this.style.background=\'#f0f2f5\'" onmouseout="this.style.background=\'transparent\'">' +
                '<div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#1B3A6B,#00B4D8);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0">' +
                (avatar ? '<img src="' + avatar + '" style="width:100%;height:100%;border-radius:50%;object-fit:cover">' : initial) +
                '</div>' +
                '<div style="flex:1;min-width:0"><div style="font-weight:600;font-size:.95rem;color:#1C1C1E">' + escapeHtml(name) + '</div></div>' +
                '<i class="fa fa-chevron-right" style="color:#ccc"></i>' +
            '</div>';
        });
        list.innerHTML = html;
    })
    .catch(function(err) {
        list.innerHTML = '<div style="padding:2rem;text-align:center;color:#dc3545"><i class="fa fa-exclamation-triangle"></i><div style="margin-top:.5rem">Erreur de chargement</div><button onclick="loadShareConversations()" style="margin-top:.75rem;padding:.5rem 1rem;background:#1B3A6B;color:white;border:none;border-radius:20px;cursor:pointer">Réessayer</button></div>';
    });
}

function filterShareConversations(query) {
    var items = document.querySelectorAll('.share-conv-item');
    var q = query.toLowerCase();
    items.forEach(function(item) {
        var name = item.dataset.name || '';
        item.style.display = name.indexOf(q) !== -1 ? 'flex' : 'none';
    });
}

function sendToConversation(conversationId, recipientId) {
    var postId = document.getElementById('sharePostId').value;
    var message = document.getElementById('shareMessageText').value.trim();
    var postUrl = document.getElementById('sharePostUrl').value;
    
    var btn = event.currentTarget;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    btn.style.pointerEvents = 'none';
    
    fetch('/api/share/to-conversation', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            post_id: postId,
            message: message || ('Je partage ce post avec vous : ' + postUrl)
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Post partagé !', 'Le post a été envoyé dans la conversation.');
            setTimeout(closeShareModal, 800);
        } else {
            btn.innerHTML = '<i class="fa fa-chevron-right"></i>';
            btn.style.pointerEvents = 'auto';
            showNotification('Erreur', data.error || 'Impossible de partager');
        }
    })
    .catch(function() {
        btn.innerHTML = '<i class="fa fa-chevron-right"></i>';
        btn.style.pointerEvents = 'auto';
        showNotification('Erreur', 'Problème de connexion');
    });
}

function startNewShareConversation(searchQuery, postId) {
    if (!searchQuery || searchQuery.length < 2) {
        showNotification('Info', 'Tapez au moins 2 caractères');
        return;
    }
    
    fetch('/api/messages/users/search?q=' + encodeURIComponent(searchQuery), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.users || data.users.length === 0) {
            showNotification('Non trouvé', 'Aucun utilisateur trouvé');
            return;
        }
        
        var user = data.users[0];
        createShareConversation(user.id, postId);
    })
    .catch(function() {
        showNotification('Erreur', 'Problème de recherche');
    });
}

function createShareConversation(userId, postId) {
    var message = document.getElementById('shareMessageText').value.trim();
    var postUrl = document.getElementById('sharePostUrl').value;
    
    fetch('/api/share/create-conversation', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success && data.conversation_id) {
            sendToConversationNew(data.conversation_id, postId, message || ('Je partage ce post : ' + postUrl));
        } else {
            showNotification('Erreur', data.error || 'Impossible de créer la conversation');
        }
    })
    .catch(function() {
        showNotification('Erreur', 'Problème de connexion');
    });
}

function sendToConversationNew(conversationId, postId, message) {
    fetch('/api/share/to-conversation', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            post_id: postId,
            message: message
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Post partagé !', 'Conversation créée et message envoyé.');
            setTimeout(closeShareModal, 800);
        } else {
            showNotification('Erreur', data.error || 'Impossible de partager');
        }
    })
    .catch(function() {
        showNotification('Erreur', 'Problème de connexion');
    });
}

function showNotification(title, text) {
    var notif = document.createElement('div');
    notif.style.position = 'fixed';
    notif.style.bottom = '20px';
    notif.style.right = '20px';
    notif.style.background = 'linear-gradient(135deg,#1B3A6B,#00B4D8)';
    notif.style.color = 'white';
    notif.style.padding = '1rem 1.5rem';
    notif.style.borderRadius = '12px';
    notif.style.boxShadow = '0 4px 20px rgba(0,0,0,0.3)';
    notif.style.zIndex = '3001';
    notif.style.fontSize = '.9rem';
    notif.style.maxWidth = '300px';
    notif.style.animation = 'slideUp .3s ease';
    notif.innerHTML = '<div style="font-weight:700;margin-bottom:.25rem">' + title + '</div><div style="opacity:.9">' + text + '</div>';
    document.body.appendChild(notif);
    setTimeout(function() { 
        notif.style.animation = 'fadeOut .3s ease forwards'; 
        setTimeout(function() { notif.remove(); }, 300); 
    }, 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load conversations for sharing
function loadShareConversations() {
    var list = document.getElementById('shareConversationsList');
    if (!list) return;
    
    list.innerHTML = '<div style="padding:2rem;text-align:center;color:#65676B"><i class="fa fa-spinner fa-spin"></i><div style="margin-top:.5rem">Chargement...</div></div>';
    
    fetch('/api/share/get-conversations', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (!data.success || !data.conversations || data.conversations.length === 0) {
            list.innerHTML = '<div style="padding:2rem;text-align:center;color:#65676B"><i class="fa fa-comments" style="font-size:2rem;opacity:.3"></i><div style="margin-top:.75rem;font-weight:600">Aucune conversation</div><div style="font-size:.8rem;margin-top:.25rem">Créez-en une nouvelle dans la messagerie</div></div>';
            return;
        }
        
        var html = '';
        data.conversations.forEach(function(c) {
            var name = c.otherUser ? c.otherUser.name : (c.name || 'Conversation');
            var avatar = c.otherUser && c.otherUser.avatar ? c.otherUser.avatar : '';
            var initial = name.charAt(0).toUpperCase();
            
            html += '<div class="share-conv-item" data-name="' + name.toLowerCase() + '" onclick="sendToConversation(' + c.id + ', ' + (c.otherUser ? c.otherUser.id : 0) + ')" style="display:flex;align-items:center;gap:.75rem;padding:1rem;border-bottom:1px solid #eee;cursor:pointer;transition:background .2s" onmouseover="this.style.background=\'#f0f2f5\'" onmouseout="this.style.background=\'transparent\'">' +
                '<div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#1B3A6B,#00B4D8);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;flex-shrink:0">' +
                (avatar ? '<img src="' + avatar + '" style="width:100%;height:100%;border-radius:50%;object-fit:cover">' : initial) +
                '</div>' +
                '<div style="flex:1;min-width:0"><div style="font-weight:600;font-size:.95rem;color:#1C1C1E">' + escapeHtml(name) + '</div></div>' +
                '<i class="fa fa-chevron-right" style="color:#ccc"></i>' +
            '</div>';
        });
        list.innerHTML = html;
    })
    .catch(function(err) {
        list.innerHTML = '<div style="padding:2rem;text-align:center;color:#dc3545"><i class="fa fa-exclamation-triangle"></i><div style="margin-top:.5rem">Erreur de chargement</div></div>';
    });
}

function sendToConversation(conversationId, recipientId) {
    var postId = document.getElementById('sharePostId').value;
    var message = document.getElementById('shareMessageText').value.trim();
    var postUrl = document.getElementById('sharePostUrl').value;
    
    fetch('/api/share/to-conversation', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            post_id: postId,
            message: message || ('Je partage ce post avec vous : ' + postUrl)
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Post partagé !', 'Le post a été envoyé dans la conversation.');
            setTimeout(closeShareModal, 800);
        } else {
            showNotification('Erreur', data.error || 'Impossible de partager');
        }
    })
    .catch(function() {
        showNotification('Erreur', 'Problème de connexion');
    });
}

function filterShareConversations(query) {
    var items = document.querySelectorAll('.share-conv-item');
    var q = query.toLowerCase();
    items.forEach(function(item) {
        var name = item.dataset.name || '';
        item.style.display = name.indexOf(q) !== -1 ? 'flex' : 'none';
    });
}