// ==================== FLY&GO MESSENGER ====================
// Like Facebook Messenger - Full Featured with Real-Time

// Global State
var messengerState = {
    conversations: [],
    currentConversation: null,
    messages: [],
    friends: [],
    pendingInvitations: [],
    currentUser: null,
    currentUserId: null,
    loading: false,
    sending: false,
    searchQuery: '',
    typingTimeout: null,
    typingInterval: null,
    pollingInterval: null,
    scrollPosition: 0,
    isAtBottom: true,
    lastMessageId: 0,
    typingUsers: [],
    onlineUsers: [],
    searchedMessages: []
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Fly&Go Messenger initializing...');
    initMessenger();
});

function initMessenger() {
    loadCurrentUser();
    loadConversations();
    setupEventListeners();
    
    connectMercure();
    startOnlineStatusPing();
}

function startOnlineStatusPing() {
    // Ping every 30 seconds
    setInterval(function() {
        fetch('/api/messages/ping', { method: 'POST' }).catch(function() {});
    }, 30000);
}

var mercureEventSource = null;

function connectMercure() {
    if (mercureEventSource) {
        mercureEventSource.close();
    }
    
    // In dev, Mercure hub is often on port 3000
    // We try to detect if we're on localhost to use port 3000
    var hubUrl = '/.well-known/mercure';
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        hubUrl = 'http://localhost:3000/.well-known/mercure';
    }
    
    console.log('📡 Connecting to Mercure Hub at:', hubUrl);
    
    // Listen to all conversation topics
    mercureEventSource = new EventSource(hubUrl + '?topic=conversation/*');
    
    mercureEventSource.onmessage = function(event) {
        try {
            var data = JSON.parse(event.data);
            console.log('🔔 Mercure update received:', data);
            
            if (data.type === 'new_message') {
                handleNewMessage(data);
            } else if (data.type === 'typing') {
                updateTypingIndicator(data.users);
            } else if (data.type === 'message_read') {
                updateReadStatus(data.messageId);
            } else if (data.type === 'user_online') {
                handleUserOnlineStatus(data);
            } else if (data.type === 'message_deleted') {
                handleMessageDeleted(data);
            } else if (data.type === 'call_signaling') {
                if (window.CallManager) {
                    window.CallManager.handleSignaling(data);
                }
            }
        } catch (e) {
            console.error('❌ Mercure parse error:', e);
        }
    };
    
    mercureEventSource.onerror = function(err) {
        console.warn('⚠️ Mercure connection lost. Retrying in 5s...');
        if (mercureEventSource) {
            mercureEventSource.close();
            mercureEventSource = null;
        }
        setTimeout(connectMercure, 5000);
    };
    
    mercureEventSource.onopen = function() {
        console.log('✅ Mercure connected!');
    };
}

function loadCurrentUser() {
    fetch('/api/user/me', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        messengerState.currentUser = data;
        console.log('Current user:', data);
    })
    .catch(function() {
        console.log('Could not load current user');
    });
}

// ==================== CONVERSATIONS ====================
function loadConversations() {
    if (messengerState.loading) return;
    messengerState.loading = true;
    
    var list = document.getElementById('conversationList');
    if (list) list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement des conversations...</p></div>';
    
    fetch('/api/messages/conversations?t=' + Date.now(), {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('API error: ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
        console.log('Conversations loaded:', data);
        messengerState.conversations = data.conversations || [];
        messengerState.currentUserId = data.userId;
        renderConversations();
        messengerState.loading = false;
    })
    .catch(function(err) {
        console.error('Error loading conversations:', err);
        messengerState.loading = false;
        if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p><small>Vérifiez votre connexion</small></div>';
    });
}

function renderConversations() {
    var list = document.getElementById('conversationList');
    if (!list) return;
    
    if (messengerState.conversations.length === 0) {
        list.innerHTML = '<div class="messenger-empty"><i class="fa fa-comments"></i><p>Aucune conversation</p><small>Commencez une nouvelle conversation avec un ami</small></div>';
        return;
    }
    
    list.innerHTML = messengerState.conversations.map(function(conv) {
        var name = getConversationName(conv);
        var avatar = getConversationAvatar(conv);
        var lastMsg = getLastMessagePreview(conv);
        var time = conv.lastMessage && conv.lastMessage.createdAt ? formatMessageTime(conv.lastMessage.createdAt) : '';
        var unread = conv.unreadCount > 0 ? '<span class="unread-badge">' + conv.unreadCount + '</span>' : '';
        
        return '<div class="conversation-item" data-id="' + conv.id + '" onclick="openConversation(' + conv.id + ')">' +
            '<div class="conv-avatar">' + avatar + '</div>' +
            '<div class="conv-content">' +
                '<div class="conv-header"><span class="conv-name">' + name + '</span><span class="conv-time">' + time + '</span></div>' +
                '<div class="conv-preview">' + lastMsg + unread + '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function getConversationName(conv) {
    if (conv.type === 'group') return conv.name || 'Groupe';
    if (conv.otherUser && conv.otherUser.name) return conv.otherUser.name;
    return 'Conversation';
}

function getConversationAvatar(conv) {
    if (conv.type === 'group') {
        if (conv.image) return '<img src="' + conv.image + '" alt="">';
        return conv.name ? conv.name.charAt(0).toUpperCase() : 'G';
    }
    if (conv.otherUser && conv.otherUser.avatar) return '<img src="' + conv.otherUser.avatar + '" alt="">';
    return getConversationName(conv).charAt(0).toUpperCase();
}

function getLastMessagePreview(conv) {
    if (!conv.lastMessage || !conv.lastMessage.content) return 'Aucun message';
    return conv.lastMessage.content;
}

function formatMessageTime(dateStr) {
    if (!dateStr) return '';
    var date = new Date(dateStr);
    var now = new Date();
    var diff = now - date;
    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
    
    if (days === 0) {
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    } else if (days === 1) {
        return 'Hier';
    } else if (days < 7) {
        var daysFr = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        return daysFr[date.getDay()];
    } else {
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
    }
}

// ==================== MESSAGES ====================
function openConversation(convId) {
    console.log('Opening conversation:', convId);
    var conv = messengerState.conversations.find(function(c) { return c.id === convId; });
    if (!conv) return;
    
    messengerState.currentConversation = conv;
    
    // Update UI
    document.getElementById('emptyChatState').style.display = 'none';
    document.getElementById('chatHeader').classList.add('active');
    document.getElementById('chatInputArea').classList.add('active');
    document.getElementById('chatName').textContent = getConversationName(conv);
    document.getElementById('chatAvatar').innerHTML = getConversationAvatar(conv);
    
    // Load messages
    loadMessages(convId);
}

function loadMessages(convId) {
    var container = document.getElementById('messagesContainer');
    if (!convId) return;
    
    messengerState.currentConversation = messengerState.currentConversation || {};
    messengerState.currentConversation.id = convId;
    
    if (container) {
        container.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement...</p></div>';
    }
    
    fetch('/api/messages/conversation/' + convId + '?limit=50', {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('API error: ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
        messengerState.messages = data.messages || [];
        
        if (data.conversation) {
            messengerState.currentConversation = data.conversation;
            var existingUser = messengerState.currentConversation.otherUser;
            if (data.conversation.otherUser && !existingUser) {
                messengerState.currentConversation.otherUser = data.conversation.otherUser;
            }
        }
        
        var maxId = 0;
        messengerState.messages.forEach(function(m) { if (m.id > maxId) maxId = m.id; });
        if (maxId > 0) messengerState.lastMessageId = maxId;
        
        renderMessages();
        markConversationAsRead(convId);
    })
    .catch(function(err) {
        console.error('Error loading messages:', err);
        if (container) container.innerHTML = '<div class="messenger-error"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p><small>Cliquez pour réessayer</small></div>';
        container.onclick = function() { loadMessages(convId); };
    });
}

function renderMessages() {
    var container = document.getElementById('messagesContainer');
    if (!container) return;
    
    if (messengerState.messages.length === 0) {
        container.innerHTML = '<div class="messenger-empty"><i class="fa fa-commenting-o"></i><p>Aucun message</p><small>Envoyez le premier message!</small></div>';
        return;
    }
    
    var currentUserId = messengerState.currentUserId;
    var lastDate = '';
    var html = '';
    
    messengerState.messages.forEach(function(msg) {
        var msgDate = new Date(msg.createdAt).toLocaleDateString('fr-FR');
        if (msgDate !== lastDate) {
            html += '<div class="date-separator"><span>' + formatDateSeparator(msg.createdAt) + '</span></div>';
            lastDate = msgDate;
        }
        
        var isMe = (msg.sender && msg.sender.id == currentUserId) || (msg.senderId == currentUserId);
        var senderName = msg.sender ? msg.sender.name : (msg.senderName || 'U');
        var avatar = senderName.charAt(0).toUpperCase();
        var time = new Date(msg.createdAt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        var status = isMe ? getMessageStatusIcon(msg.status) : '';
        
// Message content
        var content = '';
        
        // Image/GIF message
        if (msg.image || msg.gifUrl) {
            var imageUrl = msg.image || msg.gifUrl;
            if (imageUrl.startsWith('http')) {
                // ok
            } else {
                imageUrl = imageUrl;
            }
            content = '<div class="message-image" onclick="window.open(\'' + imageUrl + '\', \'_blank\')">' +
                '<img src="' + imageUrl + '" style="max-width:280px;border-radius:12px;cursor:zoom-in;" loading="lazy" onerror="this.style.display=\'none\';this.parentElement.innerHTML=\'<div class=message-error>Média non disponible</div>\';">' +
            '</div>';
        }
        
        // Video message
        if (msg.video) {
            content += '<div class="message-video">' +
                '<video src="' + msg.video + '" controls style="max-width:280px;border-radius:12px;"></video>' +
            '</div>';
        }
        
        // Audio message
        if (msg.audio) {
            var audioUrl = msg.audio.startsWith('http') ? msg.audio : msg.audio;
            content += '<div class="voice-message" data-audio="' + audioUrl + '">' +
                '<button class="play-voice-btn" onclick="playVoiceMessage(this, \'' + audioUrl + '\')">' +
                    '<i class="fa fa-play"></i>' +
                '</button>' +
                '<div class="voice-progress"><div class="voice-progress-bar"></div></div>' +
                '<span class="voice-duration">0:00</span>' +
                '<audio src="' + audioUrl + '" preload="metadata" onerror="this.parentElement.innerHTML=\'<div class=message-error>Audio non disponible</div>\';"></audio>' +
            '</div>';
        }
        
        if (msg.forumPostId) {
            content += '<div class="shared-post-preview" onclick="window.location.href=\'/forum/post/' + msg.forumPostId + '\'">' +
                '<i class="fa fa-link"></i> Post partagé</div>';
        }
        
        // Add content text if there's actual text (not just for voice messages)
        if (msg.content && msg.content !== '🎤 Message vocal') {
            content += '<div class="message-text">' + escapeHtml(msg.content) + '</div>';
        }
        
        // Reactions
        var reactions = '';
        if (msg.reactions && msg.reactions.length > 0) {
            reactions = '<div class="message-reactions">';
            msg.reactions.forEach(function(r) {
                reactions += '<span class="reaction-badge" onclick="toggleReactionPicker(' + msg.id + ')">' + r.emoji + ' ' + r.count + '</span>';
            });
            reactions += '</div>';
        }
        
        html += '<div class="message-row ' + (isMe ? 'sent' : 'received') + '" data-id="' + msg.id + '">' +
            '<div class="message-avatar">' + avatar + '</div>' +
            '<div class="message-content">' +
                '<div class="message-bubble">' + content + reactions + '</div>' +
                '<div class="message-meta"><span class="message-time">' + time + '</span>' + status + '</div>' +
            '</div>' +
            '<div class="message-actions">' +
                '<button onclick="showReplyForm(' + msg.id + ')"><i class="fa fa-reply"></i></button>' +
                '<button onclick="showMessageMenu(' + msg.id + ')"><i class="fa fa-ellipsis-h"></i></button>' +
            '</div>' +
        '</div>';
    });
    
    container.innerHTML = html;
    scrollToBottom(true);
}

// Voice message player
var currentPlayingAudio = null;
var currentPlayBtn = null;

function playVoiceMessage(btn, audioUrl) {
    console.log('Playing audio:', audioUrl);
    
    if (currentPlayingAudio) {
        currentPlayingAudio.pause();
        currentPlayingAudio.currentTime = 0;
        if (currentPlayBtn) {
            currentPlayBtn.innerHTML = '<i class="fa fa-play"></i>';
            currentPlayBtn.classList.remove('playing');
        }
    }
    
    if (currentPlayingAudio && currentPlayBtn === btn) {
        currentPlayingAudio = null;
        currentPlayBtn = null;
        return;
    }
    
    function tryPlayAudio(url) {
        var audio = new Audio(url);
        audio.preload = 'auto';
        audio.crossOrigin = 'anonymous';
        
        var canPlayWebM = audio.canPlayType('audio/webm;codecs=opus');
        var canPlayMp3 = audio.canPlayType('audio/mpeg');
        console.log('Can play webm:', canPlayWebM, 'mp3:', canPlayMp3);
        
        audio.onloadedmetadata = function() {
            var duration = audio.duration;
            if (!isNaN(duration)) {
                var min = Math.floor(duration / 60);
                var sec = Math.floor(duration % 60);
                var timeStr = ('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2);
                var durationSpan = btn.parentElement.querySelector('.voice-duration');
                if (durationSpan) durationSpan.textContent = timeStr;
            }
        };
        
        audio.ontimeupdate = function() {
            if (audio.duration) {
                var progress = (audio.currentTime / audio.duration) * 100;
                var progressBar = btn.parentElement.querySelector('.voice-progress-bar');
                if (progressBar) progressBar.style.width = progress + '%';
            }
        };
        
        audio.onended = function() {
            btn.innerHTML = '<i class="fa fa-play"></i>';
            btn.classList.remove('playing');
            var progressBar = btn.parentElement.querySelector('.voice-progress-bar');
            if (progressBar) progressBar.style.width = '0%';
        };
        
        audio.onerror = function(e) {
            console.error('Audio error:', audio.error, 'code:', audio.error ? audio.error.code : 'N/A');
            if (audio.error && audio.error.code === MediaError.MEDIA_ERR_SRC_NOT_SUPPORTED) {
                showToast('Format non supporté - Utilisez Chrome ou Edge', 'error');
            } else if (audio.error && audio.error.code === MediaError.MEDIA_ERR_NETWORK) {
                showToast('Erreur réseau - Vérifiez connexion', 'error');
            } else {
                showToast('Erreur lecture audio', 'error');
            }
        };
        
        audio.play().then(function() {
            btn.innerHTML = '<i class="fa fa-pause"></i>';
            btn.classList.add('playing');
            currentPlayingAudio = audio;
            currentPlayBtn = btn;
        }).catch(function(err) {
            console.error('Play promise error:', err.name, err.message);
            if (err.name === 'NotSupportedError') {
                var ua = navigator.userAgent;
                if (ua.indexOf('Firefox') > -1) {
                    showToast('Firefox ne supporte pas webm - Veuillez utiliser Chrome ou Edge', 'error');
                } else if (ua.indexOf('Safari') > -1 && ua.indexOf('Chrome') === -1) {
                    showToast('Safari ne supporte pas webm - Veuillez utiliser Chrome', 'error');
                } else {
                    showToast('Format non supporté', 'error');
                }
            } else if (err.name === 'AbortError') {
                console.log('Audio aborted');
            } else {
                showToast('Erreur lecture audio', 'error');
            }
        });
    }
    
    tryPlayAudio(audioUrl);
}

// Auto-scroll to bottom function
function scrollToBottom(force) {
    var chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function formatDateSeparator(dateStr) {
    var date = new Date(dateStr);
    var now = new Date();
    var diff = now - date;
    var days = Math.floor(diff / (1000 * 60 * 60 * 24));
    
    if (days === 0) return 'Aujourd\'hui';
    if (days === 1) return 'Hier';
    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
}

function getMessageStatusIcon(status) {
    if (status === 'read') return '<span class="status-read">✓✓</span>';
    if (status === 'delivered') return '<span class="status-delivered">✓✓</span>';
    return '<span class="status-sent">✓</span>';
}

function scrollToBottom() {
    var chatMessages = document.getElementById('chatMessages');
    if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
}

function markConversationAsRead(convId) {
    fetch('/api/messages/conversation/' + convId + '/read', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).catch(function() {});
}

// ==================== SEND MESSAGE ====================
function sendMessage(content) {
    if (!content || !messengerState.currentConversation || messengerState.sending) return;
    messengerState.sending = true;
    
    var input = document.getElementById('messageInput');
    if (input) input.value = '';
    
    console.log('Sending message:', content, 'to conversation:', messengerState.currentConversation.id);
    
    fetch('/api/messages/conversation/' + messengerState.currentConversation.id + '/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ content: content })
    })
    .then(function(r) {
        console.log('Response status:', r.status);
        return r.json();
    })
    .then(function(data) {
        console.log('Response data:', data);
        if (data.success) {
            loadMessages(messengerState.currentConversation.id);
        } else {
            console.error('Send failed:', data.error || data.message);
        }
        messengerState.sending = false;
    })
    .catch(function(err) {
        console.error('Error sending message:', err);
        messengerState.sending = false;
    });
}

function handleEnter(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        var input = document.getElementById('messageInput');
        if (input && input.value.trim()) {
            sendMessage(input.value.trim());
        }
    }
}

function handleSendButton() {
    var input = document.getElementById('messageInput');
    if (input && input.value.trim()) {
        sendMessage(input.value.trim());
    }
}

// ==================== TABS ====================
function switchMessengerTab(tab) {
    document.querySelectorAll('.messenger-tab').forEach(function(t) {
        t.classList.remove('active');
    });
    document.querySelector('.messenger-tab[data-tab="' + tab + '"]').classList.add('active');
    
    document.getElementById('conversationList').style.display = tab === 'conversations' ? 'block' : 'none';
    document.getElementById('invitationsPanel').classList.toggle('active', tab === 'invitations');
    document.getElementById('friendsListPanel').classList.toggle('active', tab === 'friends');
    document.getElementById('newChatPanel').style.display = 'none';
    
    messengerState.currentMessengerTab = tab;
    
    if (tab === 'invitations') {
        loadReceivedInvitations();
    } else if (tab === 'friends') {
        loadFriendsList();
    } else if (tab === 'conversations' && messengerState.conversations.length === 0) {
        loadConversations();
    }
}

// ==================== FRIENDS & INVITATIONS ====================
function loadReceivedInvitations() {
    var list = document.getElementById('receivedInvitationsList');
    if (list) list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement...</p></div>';
    
    fetch('/api/friend/received', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        messengerState.pendingInvitations = data.requests || [];
        renderReceivedInvitations();
    })
    .catch(function(err) {
        console.error('Error loading invitations:', err);
        if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p></div>';
    });
}

function renderReceivedInvitations() {
    var list = document.getElementById('receivedInvitationsList');
    if (!list) return;
    
    if (messengerState.pendingInvitations.length === 0) {
        list.innerHTML = '<div class="messenger-empty"><i class="fa fa-user-plus"></i><p>Aucune demande d\'ami</p><small>Les demandes apparaîtront ici</small></div>';
        return;
    }
    
    list.innerHTML = messengerState.pendingInvitations.map(function(inv) {
        var name = (inv.sender && inv.sender.name) ? inv.sender.name : 
                   (inv.otherUser && inv.otherUser.name) ? inv.otherUser.name : 'Utilisateur';
        var avatar = name.charAt(0).toUpperCase();
        return '<div class="invite-item">' +
            '<div class="conv-avatar">' + avatar + '</div>' +
            '<div class="invite-info"><div class="invite-name">' + name + '</div></div>' +
            '<div class="invite-actions">' +
            '<button class="invite-btn accept" onclick="acceptFriend(' + inv.id + ')"><i class="fa fa-check"></i></button>' +
            '<button class="invite-btn reject" onclick="rejectFriend(' + inv.id + ')"><i class="fa fa-times"></i></button>' +
            '</div></div>';
    }).join('');
}

function acceptFriend(id) {
    fetch('/api/friend/accept/' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function() { loadReceivedInvitations(); loadConversations(); })
    .catch(function() {});
}

function rejectFriend(id) {
    fetch('/api/friend/reject/' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function() { loadReceivedInvitations(); })
    .catch(function() {});
}

function loadFriendsList() {
    var list = document.getElementById('friendsList');
    if (list) list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement des amis...</p></div>';
    
    fetch('/api/friend/list', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        messengerState.friends = data.friends || [];
        renderFriendsList();
    })
    .catch(function(err) {
        console.error('Error loading friends list:', err);
        if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p><small>Réessayez plus tard</small></div>';
    });
}

function renderFriendsList() {
    var list = document.getElementById('friendsList');
    if (!list) return;
    
    if (messengerState.friends.length === 0) {
        list.innerHTML = '<div class="messenger-empty"><i class="fa fa-users"></i><p>Aucun ami</p><small>Ajoutez des amis via l\'onglet Invitations</small></div>';
        return;
    }
    
    list.innerHTML = messengerState.friends.map(function(friend) {
        var name = friend.name || 'Ami';
        var avatar = name.charAt(0).toUpperCase();
        return '<div class="friend-item" onclick="startConversationWithFriend(' + friend.id + ', \'' + name.replace(/'/g, "\\'") + '\')">' +
            '<div class="conv-avatar">' + avatar + '</div>' +
            '<div class="friend-info"><div class="friend-name">' + name + '</div></div>' +
            '<div class="friend-actions"><button class="chat-btn" title="Envoyer un message"><i class="fa fa-comment"></i></button></div>' +
        '</div>';
    }).join('');
}

function startConversationWithFriend(userId, name) {
    fetch('/api/messages/start/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var convId = data.conversationId || (data.conversation && data.conversation.id);
            if (convId) {
                loadConversations();
                openConversation(convId);
                switchMessengerTab('conversations');
            }
        }
    })
    .catch(function(err) {
        console.error('Error starting conversation:', err);
    });
}

// ==================== NEW CHAT ====================
function showNewChatModal() {
    var panel = document.getElementById('newChatPanel');
    if (panel) {
        panel.style.display = 'block';
        loadFriendsForNewChat();
    }
}

function closeNewChatModal() {
    var panel = document.getElementById('newChatPanel');
    if (panel) panel.style.display = 'none';
}

function loadFriendsForNewChat() {
    var list = document.getElementById('newChatFriendsList');
    if (list) list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>';
    
    fetch('/api/friend/list', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var friends = data.friends || [];
        messengerState.friends = friends;
        
        if (friends.length === 0) {
            if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-user-plus"></i><p>Aucun ami pour le moment</p><small>Ajoutez des amis pour commencer une conversation</small></div>';
        } else {
            renderFriendsForNewChat(friends);
        }
    })
    .catch(function(err) {
        console.error('Error loading friends:', err);
        if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p></div>';
    });
}

function renderFriendsForNewChat(friends) {
    var list = document.getElementById('newChatFriendsList');
    if (!list) return;
    
    if (friends.length === 0) {
        list.innerHTML = '<div class="messenger-empty"><i class="fa fa-search"></i><p>Aucun ami trouvé</p></div>';
        return;
    }
    
    list.innerHTML = friends.map(function(f) {
        var avatar = f.avatar ? '<img src="' + f.avatar + '" alt="">' : (f.name || 'A').charAt(0).toUpperCase();
        return '<div class="friend-item" onclick="startConversationWithFriend(' + f.id + ', \'' + (f.name || 'Ami').replace(/'/g, "\\'") + '\')">' +
            '<div class="conv-avatar">' + avatar + '</div>' +
            '<div class="friend-info"><div class="friend-name">' + (f.name || 'Ami') + '</div></div>' +
        '</div>';
    }).join('');
}

function searchNewChat(query) {
    messengerState.searchQuery = query.toLowerCase().trim();
    var list = document.getElementById('newChatFriendsList');
    if (!list) return;
    
    if (query.length < 2) {
        loadFriendsForNewChat();
        return;
    }
    
    list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i> Recherche...</div>';
    
    fetch('/api/messages/users/search?q=' + encodeURIComponent(query), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var users = data.users || [];
        if (users.length === 0) {
            list.innerHTML = '<div class="messenger-empty"><i class="fa fa-search"></i><p>Aucun utilisateur trouvé pour "' + escapeHtml(query) + '"</p></div>';
        } else {
            list.innerHTML = users.map(function(u) {
                var avatar = u.avatar ? '<img src="' + u.avatar + '" alt="">' : (u.name || 'U').charAt(0).toUpperCase();
                var name = u.firstName && u.lastName ? u.firstName + ' ' + u.lastName : (u.name || 'Utilisateur');
                return '<div class="friend-item" onclick="startConversationWithFriend(' + u.id + ', \'' + name.replace(/'/g, "\\'") + '\')">' +
                    '<div class="conv-avatar">' + avatar + '</div>' +
                    '<div class="friend-info"><div class="friend-name">' + name + '</div><div class="friend-email">' + (u.email || '') + '</div></div>' +
                '</div>';
            }).join('');
        }
    })
    .catch(function(err) {
        console.error('Search error:', err);
        loadFriendsForNewChat();
    });
}

// ==================== MESSAGE ACTIONS ====================
function showReplyForm(msgId) {
    var msg = messengerState.messages.find(function(m) { return m.id === msgId; });
    if (!msg) return;
    
    messengerState.replyTo = msg;
    var input = document.getElementById('messageInput');
    if (input) {
        input.placeholder = 'Répondre à ' + (msg.senderName || msg.sender.name) + '...';
        input.focus();
    }
}

function showMessageMenu(msgId) {
    var msg = messengerState.messages.find(function(m) { return m.id === msgId; });
    if (!msg) return;
    
    showContextMenu(msgId, [
        { label: 'Répondre', action: function() { showReplyForm(msgId); } },
        { label: 'Transférer', action: function() { forwardMessage(msgId); } },
        { label: 'Copier', action: function() { navigator.clipboard.writeText(msg.content); showToast('Copié', 'success'); } },
        { label: 'Supprimer', action: function() { deleteMessage(msgId); } }
    ]);
}

function showContextMenu(msgId, items) {
    var existing = document.querySelector('.message-context-menu');
    if (existing) existing.remove();
    
    var menu = document.createElement('div');
    menu.className = 'message-context-menu';
    menu.innerHTML = items.map(function(item, i) {
        return '<div class="context-menu-item" onclick="this.closest(\'.message-context-menu\').remove(); item.action();">' + item.label + '</div>';
    }).join('');
    
    document.body.appendChild(menu);
    
    document.addEventListener('click', function close() {
        menu.remove();
        document.removeEventListener('click', close);
    });
}

function deleteMessage(msgId) {
    var forAll = confirm('Supprimer pour tout le monde ? (Annuler pour supprimer seulement pour moi)');
    
    fetch('/api/messages/delete/' + msgId + '?forAll=' + forAll, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            loadMessages(messengerState.currentConversation.id);
            showToast('Message supprimé', 'success');
        }
    })
    .catch(function() {
        showToast('Erreur lors de la suppression', 'error');
    });
}

function clearReplyTo() {
    messengerState.replyTo = null;
    var input = document.getElementById('messageInput');
    if (input) input.placeholder = 'Écrivez un message...';
}

// Mercure connection is handled at the beginning of the file

function handleMessageDeleted(data) {
    if (messengerState.currentConversation && data.conversationId === messengerState.currentConversation.id) {
        var msg = messengerState.messages.find(function(m) { return m.id === data.messageId; });
        if (msg) {
            msg.content = '🚫 Ce message a été supprimé';
            msg.image = null;
            msg.audio = null;
            msg.video = null;
            renderMessages();
        }
    }
}

function handleNewMessage(data) {
    if (messengerState.currentConversation && data.conversationId === messengerState.currentConversation.id) {
        messengerState.messages.push(data.message);
        renderMessages();
        markConversationAsRead(messengerState.currentConversation.id);
    } else {
        loadConversations();
        showToast('Nouveau message', 'info');
    }
}

function updateReadStatus(messageId) {
    var msg = messengerState.messages.find(function(m) { return m.id === messageId; });
    if (msg) {
        msg.status = 'read';
        renderMessages();
    }
}

function handleUserOnlineStatus(data) {
    if (!data.userId) return;
    
    if (data.online) {
        if (!messengerState.onlineUsers.includes(data.userId)) {
            messengerState.onlineUsers.push(data.userId);
        }
    } else {
        messengerState.onlineUsers = messengerState.onlineUsers.filter(function(id) { return id !== data.userId; });
    }
    
    updateOnlineIndicators();
}

function publishToMercure(topic, data) {
    var formData = new FormData();
    formData.append('topic', topic);
    formData.append('data', JSON.stringify(data));
    
    fetch('/api/hub', { method: 'POST', body: formData }).catch(function() {});
}

// ==================== REAL TIME POLLING ====================
function startRealTimePolling() {
    if (messengerState.pollingInterval) clearInterval(messengerState.pollingInterval);
    
    messengerState.pollingInterval = setInterval(function() {
        checkNewMessages();
        checkTypingIndicators();
        checkOnlineStatus();
    }, 1000);
}

function checkNewMessages() {
    if (!messengerState.currentConversation) return;
    
    var convId = messengerState.currentConversation.id;
    fetch('/api/messages/conversation/' + convId + '/last', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.message && data.message.id > messengerState.lastMessageId) {
            messengerState.lastMessageId = data.message.id;
            if (!data.isMe) {
                loadMessages(convId);
                showNewMessageToast(data.message);
            }
        }
        if (data.lastId > messengerState.lastMessageId) {
            messengerState.lastMessageId = data.lastId;
        }
    })
    .catch(function() {});
}

function showNewMessageToast(msg) {
    var name = messengerState.currentConversation ? getConversationName(messengerState.currentConversation) : 'Nouveau message';
    showToast('Nouveau message de ' + name, 'info');
}

function checkTypingIndicators() {
    if (!messengerState.currentConversation) return;
    
    var convId = messengerState.currentConversation.id;
    fetch('/api/messages/conversation/' + convId + '/typing', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        updateTypingIndicator(data.typing);
    })
    .catch(function() {});
}

function checkOnlineStatus() {
    fetch('/api/user/online', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        messengerState.onlineUsers = data.online || [];
        updateOnlineIndicators();
    })
    .catch(function() {});
}

function updateOnlineIndicators() {
    var statusEl = document.getElementById('chatStatus');
    if (!statusEl || !messengerState.currentConversation) return;
    
    var conv = messengerState.currentConversation;
    var otherUserId = conv.otherUser ? conv.otherUser.id : null;
    var isOnline = otherUserId && messengerState.onlineUsers.includes(otherUserId);
    
    if (isOnline) {
        statusEl.innerHTML = '<span class="online-dot"></span>En ligne';
    } else {
        statusEl.innerHTML = '<span class="offline-dot"></span>Hors ligne';
    }
}

// ==================== TYPING INDICATOR ====================
function sendTypingIndicator() {
    if (!messengerState.currentConversation) return;
    
    var convId = messengerState.currentConversation.id;
    fetch('/api/messages/conversation/' + convId + '/typing', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {})
    .catch(function() {});
}

function onMessageInput() {
    sendTypingIndicator();
    
    if (messengerState.typingTimeout) clearTimeout(messengerState.typingTimeout);
    messengerState.typingTimeout = setTimeout(function() {
        clearTypingIndicator();
    }, 2000);
}

function updateTypingIndicator(data) {
    var statusEl = document.getElementById('chatStatus');
    if (!statusEl || !data || !data.user) return;
    
    // Don't show typing for self
    if (data.user.id === messengerState.currentUserId) return;
    
    if (messengerState.typingTimeout) clearTimeout(messengerState.typingTimeout);
    
    var name = data.user.name || 'Quelqu\'un';
    statusEl.innerHTML = '<span class="typing-indicator"><i class="fa fa-circle"></i><i class="fa fa-circle"></i><i class="fa fa-circle"></i></span> ' + name + ' est en train d\'écrire...';
    
    messengerState.typingTimeout = setTimeout(function() {
        updateOnlineIndicators();
    }, 3000);
}

function clearTypingIndicator() {}

// ==================== SCROLL INTELLIGENT ====================
function setupScrollListener() {
    var chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    chatMessages.addEventListener('scroll', function() {
        var scrollTop = chatMessages.scrollTop;
        var scrollHeight = chatMessages.scrollHeight;
        var clientHeight = chatMessages.clientHeight;
        
        messengerState.isAtBottom = scrollHeight - scrollTop - clientHeight < 50;
        messengerState.scrollPosition = scrollTop;
    });
}

function scrollToBottom(force) {
    var chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    if (force || messengerState.isAtBottom) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// ==================== MESSAGE SEARCH ====================
function searchMessages(query) {
    if (!query || query.length < 2) {
        messengerState.searchedMessages = [];
        hideMessageSearchResults();
        return;
    }
    
    var convId = messengerState.currentConversation ? messengerState.currentConversation.id : null;
    if (!convId) return;
    
    fetch('/api/messages/conversation/' + convId + '/search?q=' + encodeURIComponent(query), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        messengerState.searchedMessages = data.messages || [];
        showMessageSearchResults(messengerState.searchedMessages);
    })
    .catch(function() {});
}

function showMessageSearchResults(messages) {
    var container = document.getElementById('messageSearchResults');
    if (!container) return;
    
    if (messages.length === 0) {
        container.innerHTML = '<div class="messenger-empty">Aucun résultat</div>';
        return;
    }
    
    container.innerHTML = messages.map(function(msg) {
        var time = new Date(msg.createdAt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        return '<div class="search-result-item" onclick="jumpToMessage(' + msg.id + ')">' +
            '<span class="search-result-time">' + time + '</span>' +
            '<span class="search-result-sender">' + (msg.sender ? msg.sender.name : '') + '</span>' +
            '<span class="search-result-content">' + escapeHtml(msg.content.substring(0, 50)) + '</span>' +
        '</div>';
    }).join('');
}

function hideMessageSearchResults() {}

function jumpToMessage(msgId) {
    var msgEl = document.querySelector('.message[data-id="' + msgId + '"]');
    if (msgEl) {
        msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        msgEl.classList.add('highlight');
        setTimeout(function() { msgEl.classList.remove('highlight'); }, 2000);
    }
}

// ==================== FORWARD MESSAGE ====================
function forwardMessage(msgId) {
    var msg = messengerState.messages.find(function(m) { return m.id === msgId; });
    if (!msg) return;
    
    messengerState.forwardingMessage = msg;
    showForwardModal();
}

function showForwardModal() {
    var modal = document.createElement('div');
    modal.className = 'forward-modal';
    modal.innerHTML = '<div class="forward-modal-content">' +
        '<h3>Transférer à</h3>' +
        '<div class="forward-friends-list"></div>' +
        '<button onclick="closeForwardModal()">Annuler</button>' +
    '</div>';
    document.body.appendChild(modal);
}

function closeForwardModal() {
    var modal = document.querySelector('.forward-modal');
    if (modal) modal.remove();
    messengerState.forwardingMessage = null;
}

function forwardToFriend(friendId) {
    if (!messengerState.forwardingMessage) return;
    
    fetch('/api/messages/start/' + friendId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success && data.conversation) {
            return fetch('/api/messages/conversation/' + data.conversation.id + '/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ content: '[Transféré] ' + messengerState.forwardingMessage.content })
            });
        }
    })
    .then(function() {
        closeForwardModal();
        showToast('Message Transferé', 'success');
    })
    .catch(function() {});
}

// ==================== UTILITIES ====================
function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function setupEventListeners() {
    setupScrollListener();
    
    var searchInput = document.getElementById('newChatSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchNewChat(this.value);
        });
    }
    
    var msgInput = document.getElementById('messageInput');
    if (msgInput) {
        msgInput.addEventListener('keypress', handleEnter);
        msgInput.addEventListener('input', onMessageInput);
    }
}

// ==================== SHARED POST ====================
function sharePostToConversation(convId, postId, content) {
    fetch('/api/messages/conversation/' + convId + '/messages', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ 
            content: content || '📌 Post partagé',
            postId: postId
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Post partagé avec succès', 'success');
        }
    })
    .catch(function() {
        showToast('Erreur lors du partage', 'error');
    });
}

// ==================== TOAST NOTIFICATIONS ====================
function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'messenger-toast ' + (type || 'info');
    toast.innerHTML = '<i class="fa fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + '"></i> ' + message;
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

// Make functions globally available
window.openConversation = openConversation;
window.switchMessengerTab = switchMessengerTab;
window.handleEnter = handleEnter;
window.sendMessage = sendMessage;
window.handleSendButton = handleSendButton;
window.showNewChatModal = showNewChatModal;
window.closeNewChatModal = closeNewChatModal;
window.acceptFriend = acceptFriend;
window.rejectFriend = rejectFriend;
window.startConversationWithFriend = startConversationWithFriend;
window.sharePostToConversation = sharePostToConversation;
window.searchNewChat = searchNewChat;
window.showReplyForm = showReplyForm;
window.showMessageMenu = showMessageMenu;
window.clearReplyTo = clearReplyTo;
window.deleteMessage = deleteMessage;
window.forwardMessage = forwardMessage;
window.searchMessages = searchMessages;
window.jumpToMessage = jumpToMessage;

// These are called from HTML buttons (without parameters)
function startAudioCall() {
    console.log('startAudioCall - currentConversation:', messengerState.currentConversation);
    
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    console.log('Conversation object:', messengerState.currentConversation);
    
    var otherUserId = null;
    if (messengerState.currentConversation.otherUser) {
        otherUserId = messengerState.currentConversation.otherUser.id;
    } else if (messengerState.currentConversation.userId) {
        otherUserId = messengerState.currentConversation.userId;
    }
    
    console.log('Other user ID:', otherUserId);
    
    if (otherUserId) {
        console.log('📞 Starting audio call to user:', otherUserId);
        if (typeof window.CallManager !== 'undefined') {
            window.CallManager.startCall(otherUserId, 'audio');
        }
    } else {
        showToast('Impossible de démarrer l\'appel', 'error');
    }
}

function startVideoCall() {
    console.log('startVideoCall - currentConversation:', messengerState.currentConversation);
    
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    var otherUserId = null;
    if (messengerState.currentConversation.otherUser) {
        otherUserId = messengerState.currentConversation.otherUser.id;
    } else if (messengerState.currentConversation.userId) {
        otherUserId = messengerState.currentConversation.userId;
    }
    
    console.log('Other user ID:', otherUserId);
    
    if (otherUserId) {
        console.log('📹 Starting video call to user:', otherUserId);
        if (typeof window.CallManager !== 'undefined') {
            window.CallManager.startCall(otherUserId, 'video');
        }
    } else {
        showToast('Impossible de démarrer l\'appel', 'error');
    }
}

window.startAudioCall = function(userId) {
    if (typeof CallManager !== 'undefined') {
        CallManager.startCall(userId, 'audio');
    } else {
        startAudioCall();
    }
};

window.startVideoCall = function(userId) {
    if (typeof CallManager !== 'undefined') {
        CallManager.startCall(userId, 'video');
    } else {
        startVideoCall();
    }
};

console.log('window.startAudioCall set:', typeof window.startAudioCall);
console.log('window.startVideoCall set:', typeof window.startVideoCall);

// ==================== USER PROFILE ====================
function showUserProfileInfo() {
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    var otherUser = messengerState.currentConversation.otherUser;
    if (!otherUser) {
        showToast('Utilisateur non trouvé', 'error');
        return;
    }
    
    var profileHtml = '<div style="max-width:320px;padding:20px;">' +
        '<div style="text-align:center;">' +
            '<div style="width:80px;height:80px;border-radius:50%;background:#3b82f6;color:white;font-size:32px;display:flex;align-items:center;justify-content:center;margin:0 auto 15px;">' +
                (otherUser.name ? otherUser.name.charAt(0).toUpperCase() : '?') +
            '</div>' +
            '<h3 style="margin:0 0 5px;">' + (otherUser.name || 'Utilisateur') + '</h3>' +
            '<p style="color:#666;margin:0 0 15px;">' + (otherUser.email || '') + '</p>' +
        '</div>' +
        '<div style="border-top:1px solid #eee;padding-top:15px;margin-top:15px;">' +
            '<button onclick="startAudioCall()" style="width:100%;padding:10px;margin-bottom:8px;background:#10b981;color:white;border:none;border-radius:8px;cursor:pointer;">' +
                '<i class="fa fa-phone"></i> Appel audio' +
            '</button>' +
            '<button onclick="startVideoCall()" style="width:100%;padding:10px;margin-bottom:8px;background:#3b82f6;color:white;border:none;border-radius:8px;cursor:pointer;">' +
                '<i class="fa fa-video"></i> Appel vidéo' +
            '</button>' +
            '<button onclick="toggleMuteConversation()" style="width:100%;padding:10px;margin-bottom:8px;background:#f59e0b;color:white;border:none;border-radius:8px;cursor:pointer;">' +
                '<i class="fa fa-bell-slash"></i> Sourdine' +
            '</button>' +
            '<button onclick="toggleArchiveConversation()" style="width:100%;padding:10px;margin-bottom:8px;background:#6b7280;color:white;border:none;border-radius:8px;cursor:pointer;">' +
                '<i class="fa fa-archive"></i> Archiver' +
            '</button>' +
            '<button onclick="blockUser(' + otherUser.id + ')" style="width:100%;padding:10px;margin-bottom:8px;background:#ef4444;color:white;border:none;border-radius:8px;cursor:pointer;">' +
                '<i class="fa fa-ban"></i> Bloquer' +
            '</button>' +
        '</div>' +
    '</div>';
    
    var modal = document.createElement('div');
    modal.id = 'userProfileModal';
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
    modal.innerHTML = '<div style="background:white;border-radius:12px;max-width:90%;">' + 
        profileHtml + 
        '</div>';
    
    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    };
    
    document.body.appendChild(modal);
}

window.showUserProfileInfo = showUserProfileInfo;

function toggleMuteConversation() {
    if (!messengerState.currentConversation) return;
    var convId = messengerState.currentConversation.id;
    
    fetch('/api/messages/conversation/' + convId + '/mute', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ mute: true })
    })
    .then(function() { showToast('Conversation en sourdine', 'success'); })
    .catch(function() {});
}

function toggleArchiveConversation() {
    if (!messengerState.currentConversation) return;
    var convId = messengerState.currentConversation.id;
    
    fetch('/api/messages/conversation/' + convId + '/archive', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ archive: true })
    })
    .then(function() { 
        showToast('Conversation archivée', 'success');
        loadConversations();
        document.getElementById('emptyChatState').style.display = 'flex';
        document.getElementById('chatHeader').classList.remove('active');
        document.getElementById('chatInputArea').classList.remove('active');
    })
    .catch(function() {});
}

function blockUser(userId) {
    if (!confirm('Voulez-vous vraiment bloquer cet utilisateur ?')) return;
    
    fetch('/api/friend/block/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function() { 
        showToast('Utilisateur bloqué', 'success');
        loadConversations();
        document.getElementById('userProfileModal').remove();
    })
    .catch(function() {});
}

window.toggleMuteConversation = toggleMuteConversation;
window.toggleArchiveConversation = toggleArchiveConversation;
window.blockUser = blockUser;

// ==================== VOICE MESSAGES ====================
var voiceMediaRecorder = null;
var voiceChunks = [];
var voiceStartTime = null;
var voiceInterval = null;

// Voice recording state
var voiceMediaRecorder = null;
var voiceChunks = [];
var voiceStartTime = null;
var voiceInterval = null;
var isRecording = false;

function toggleVoiceRecording() {
    if (isRecording) {
        stopVoiceMessage();
    } else {
        startVoiceMessage();
    }
}

function startVoiceMessage() {
    console.log('🎤 Starting voice recording...');
    isRecording = true;
    showToast('Enregistrement...', 'info');
    
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            var mimeType = 'audio/webm;codecs=opus';
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'audio/webm';
            }
            if (!MediaRecorder.isTypeSupported(mimeType)) {
                mimeType = 'audio/ogg;codecs=opus';
            }
            voiceMediaRecorder = new MediaRecorder(stream, { mimeType: mimeType });
            voiceChunks = [];
            
            voiceMediaRecorder.ondataavailable = function(e) {
                voiceChunks.push(e.data);
            };
            
            voiceMediaRecorder.onstop = function() {
                isRecording = false;
                var audioBlob = new Blob(voiceChunks, { type: 'audio/webm' });
                console.log('Recording stopped, sending audio blob:', audioBlob.size, 'bytes');
                sendVoiceMessage(audioBlob);
                voiceChunks = [];
                
                // Stop all tracks in the stream
                stream.getTracks().forEach(function(track) { 
                    console.log('Stopping track:', track.kind);
                    track.stop(); 
                });
            };
            
            voiceMediaRecorder.start();
            voiceStartTime = Date.now();
            
            // Show recording indicator
            var micBtn = document.getElementById('micBtn');
            if (micBtn) {
                micBtn.classList.add('recording');
                micBtn.innerHTML = '<i class="fa fa-stop"></i>';
            }
            
            // Update duration every 100ms
            voiceInterval = setInterval(function() {
                var duration = Math.floor((Date.now() - voiceStartTime) / 1000);
                var min = Math.floor(duration / 60);
                var sec = duration % 60;
                var timeStr = ('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2);
                
                console.log('Recording: ' + timeStr);
                
                // Update UI
                var durationEl = document.getElementById('voiceDuration');
                if (durationEl) {
                    durationEl.textContent = timeStr;
                    durationEl.style.display = 'inline';
                }
                
                var micBtn = document.getElementById('micBtn');
                if (micBtn) {
                    micBtn.classList.add('recording');
                    micBtn.style.background = '#EF4444';
                    micBtn.style.color = 'white';
                }
                
                // Stop after 60 seconds
                if (duration >= 60) {
                    stopVoiceMessage();
                }
            }, 100);
        })
        .catch(function(err) {
            console.error('Microphone error:', err);
            showToast('Erreur microphone', 'error');
        });
}

function stopVoiceMessage() {
    isRecording = false;
    if (voiceMediaRecorder && voiceMediaRecorder.state === 'recording') {
        voiceMediaRecorder.stop();
        clearInterval(voiceInterval);
    }
    
    // Reset UI
    var micBtn = document.getElementById('micBtn');
    if (micBtn) {
        micBtn.classList.remove('recording');
        micBtn.style.background = '';
        micBtn.style.color = '';
        micBtn.innerHTML = '<i class="fa fa-microphone"></i>';
    }
    
    var durationEl = document.getElementById('voiceDuration');
    if (durationEl) {
        durationEl.textContent = '';
        durationEl.style.display = 'none';
    }
}

function cancelVoiceMessage() {
    if (voiceMediaRecorder && voiceMediaRecorder.state === 'recording') {
        voiceMediaRecorder.stop();
        clearInterval(voiceInterval);
        voiceChunks = [];
        
        var micBtn = document.getElementById('micBtn');
        if (micBtn) {
            micBtn.classList.remove('recording');
            micBtn.innerHTML = '<i class="fa fa-microphone"></i>';
        }
    }
}

function sendVoiceMessage(audioBlob) {
    console.log('Sending voice message, blob size:', audioBlob.size);
    
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    if (audioBlob.size === 0) {
        showToast('Audio vide', 'error');
        return;
    }
    
    var convId = messengerState.currentConversation.id;
    
    var formData = new FormData();
    formData.append('audio', audioBlob, 'voice.webm');
    formData.append('conversation_id', convId);
    
    showToast('Envoi message vocal...', 'info');
    
    fetch('/api/messages/upload-audio', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        console.log('Voice message sent:', data);
        if (data.success) {
            loadMessages(convId);
            showToast('Message vocal envoyé!', 'success');
        } else {
            showToast('Erreur: ' + (data.error || 'Inconnue'), 'error');
        }
    })
    .catch(function(err) {
        console.error('Send voice error:', err);
        showToast('Erreur envoi', 'error');
    });
}

window.toggleVoiceRecording = toggleVoiceRecording;
window.startVoiceMessage = startVoiceMessage;
window.stopVoiceMessage = stopVoiceMessage;
window.cancelVoiceMessage = cancelVoiceMessage;

// ==================== EMOJI PICKER ====================
var emojiPickerShown = false;

function toggleEmojiPicker() {
    var existing = document.getElementById('emojiPicker');
    if (existing) {
        existing.remove();
        emojiPickerShown = false;
        return;
    }
    
    emojiPickerShown = true;
    
    var picker = document.createElement('div');
    picker.id = 'emojiPicker';
    picker.style.cssText = 'position:absolute;bottom:70px;left:10px;background:#fff;border-radius:16px;padding:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);z-index:1000;width:300px;max-height:350px;';
    
    var categories = {
        '😀': ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗','😚','😙','🥲','😋'],
        '👋': ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','👇','☝️','👍','👎','✊','👊','🤛','🤜','👋'],
        '❤️': ['❤️','🧡','💛','💚','💙','💜','🤎','🖤','🤍','💔','❣️','💕','💞','💓','💗','💖','💘','💝','⭐','🌟','✨','💫','🔥','💯','🙏','👏','🙌','🤝','💪'],
        '😎': ['😎','🤓','🧐','😱','🥳','🥸','😈','👿','💀','☠️','💩','🤡','👹','👺','👻','👽','👾','🤖','🎉','🎊','🎁','🏆','🥇','🎯','🎲']
    };
    
    var tabs = '<div style="display:flex;gap:4px;margin-bottom:8px;padding-bottom:8px;border-bottom:1px solid #eee;">';
    var i = 0;
    for (var cat in categories) {
        tabs += '<button onclick="filterEmojis(\'' + i + '\', this)" style="flex:1;padding:6px;border:none;background:#f5f5f5;border-radius:6px;cursor:pointer;font-size:16px;">' + cat + '</button>';
        i++;
    }
    tabs += '</div>';
    
    var grid = '<div id="emojiGrid" style="display:grid;grid-template-columns:repeat(8,1fr);gap:4px;max-height:240px;overflow-y:auto;">';
    var allEmojis = [];
    for (var cat in categories) {
        allEmojis = allEmojis.concat(categories[cat]);
    }
    grid += allEmojis.map(function(e, idx) {
        return '<span class="emoji-item" style="cursor:pointer;font-size:22px;padding:4px;text-align:center;border-radius:6px;transition:background 0.2s;" onmouseover="this.style.background=#f0f0f0" onmouseout="this.style.background=transparent" onclick="insertEmoji(\'' + e + '\')">' + e + '</span>';
    }).join('');
    grid += '</div>';
    
    picker.innerHTML = tabs + grid;
    
    window.filterEmojis = function(idx, btn) {
        var emojis = Object.values(categories)[idx];
        var grid = document.getElementById('emojiGrid');
        if (grid) {
            grid.innerHTML = emojis.map(function(e) {
                return '<span style="cursor:pointer;font-size:22px;padding:4px;text-align:center;border-radius:6px;" onclick="insertEmoji(\'' + e + '\')">' + e + '</span>';
            }).join('');
        }
        document.querySelectorAll('#emojiPicker button').forEach(function(b) {
            b.style.background = '#f5f5f5';
        });
        btn.style.background = '#00B4D8';
        btn.style.color = 'white';
    };
    
    var inputArea = document.getElementById('chatInputArea');
    if (inputArea) {
        inputArea.appendChild(picker);
    }
    
    setTimeout(function() {
        document.addEventListener('click', function closeEmoji(e) {
            if (e.target.closest('#emojiPicker') || e.target.closest('.fa-smile-o')) return;
            var p = document.getElementById('emojiPicker');
            if (p) {
                p.remove();
                emojiPickerShown = false;
            }
            document.removeEventListener('click', closeEmoji);
        });
    }, 100);
}

function insertEmoji(emoji) {
    var input = document.getElementById('messageInput');
    if (input) {
        input.value += emoji;
        input.focus();
        var evt = new Event('input', {bubbles: true});
        input.dispatchEvent(evt);
    }
    var picker = document.getElementById('emojiPicker');
    if (picker) {
        picker.remove();
        emojiPickerShown = false;
    }
}

window.toggleEmojiPicker = toggleEmojiPicker;
window.insertEmoji = insertEmoji;

// ==================== GIF SUPPORT ====================
var gifPickerShown = false;

function toggleGifPicker() {
    var existing = document.getElementById('gifPicker');
    if (existing) {
        existing.remove();
        gifPickerShown = false;
        return;
    }
    
    gifPickerShown = true;
    
    var picker = document.createElement('div');
    picker.id = 'gifPicker';
    picker.style.cssText = 'position:absolute;bottom:80px;left:60px;background:#fff;border-radius:12px;padding:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);z-index:1000;width:320px;max-height:380px;display:flex;flex-direction:column;';
    picker.style.position = 'absolute';
    picker.style.bottom = '80px';
    picker.style.left = '60px';
    
    picker.innerHTML = '<div style="margin-bottom:10px;"><input type="text" id="gifSearch" placeholder="Rechercher un GIF..." style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:14px;"></div>' +
        '<div id="gifResults" style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;max-height:250px;overflow-y:auto;"></div>' +
        '<div id="gifEmpty" style="text-align:center;padding:20px;color:#999;">Tapez pour rechercher des GIFs</div>';
    
    document.getElementById('gifSearch').addEventListener('input', function(e) {
        searchGifs(e.target.value);
    });
    
    document.getElementById('gifSearch').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            searchGifs(e.target.value);
        }
    });
    
    var inputArea = document.getElementById('chatInputArea');
    if (inputArea) {
        inputArea.appendChild(picker);
    }
}

function searchGifs(query) {
    var results = document.getElementById('gifResults');
    var empty = document.getElementById('gifEmpty');
    
    if (!query || query.length < 1) {
        if (empty) empty.style.display = 'block';
        if (results) results.innerHTML = '';
        return;
    }
    
    if (empty) empty.style.display = 'none';
    if (!results) return;
    
    results.innerHTML = '<div style="grid-column:span 2;text-align:center;padding:20px;"><i class="fa fa-spinner fa-spin"></i> Recherche...</div>';
    
    var tenorKey = 'AIzaSyA4tvT2WqhK3lC2lLxf1K3lC2lLxf1K3lC2';
    var url = 'https://tenor.googleapis.com/v2/search?q=' + encodeURIComponent(query) + '&key=' + tenorKey + '&limit=20&media_filter=gif,tinygif';
    
    fetch(url)
    .then(function(r) { return r.json(); })
    .then(function(data) {
        console.log('Tenor response:', data);
        if (data.results && data.results.length > 0) {
            results.innerHTML = data.results.map(function(gif) {
                var imgUrl = gif.media_formats.gif.url;
                var previewUrl = gif.media_formats.tinygif.url;
                return '<div style="cursor:pointer;border-radius:8px;overflow:hidden;" onclick="sendGif(\'' + imgUrl.replace(/'/g, "\\'") + '\')">' +
                    '<img src="' + previewUrl + '" style="width:100%;display:block;">' +
                '</div>';
            }).join('');
        } else if (data.error) {
            results.innerHTML = '<div style="grid-column:span 2;text-align:center;padding:20px;color:#999;">API Error: ' + data.error.message + '</div>';
        } else {
            results.innerHTML = '<div style="grid-column:span 2;text-align:center;padding:20px;color:#999;">Aucun résultat</div>';
        }
    })
    .catch(function(err) {
        console.error('Tenor error:', err);
        results.innerHTML = '<div style="grid-column:span 2;text-align:center;padding:20px;color:#999;">Erreur:Vérifiez connexion</div>';
    });
}

function sendGif(gifUrl) {
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    var convId = messengerState.currentConversation.id;
    
    var formData = new FormData();
    formData.append('conversation_id', convId);
    formData.append('gif_url', gifUrl);
    
    fetch('/api/messages/send-gif', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            loadMessages(convId);
            showToast('GIF envoyé!', 'success');
        } else {
            showToast('Erreur: ' + (data.error || 'Inconnue'), 'error');
        }
    })
    .catch(function() {
        showToast('Erreur envoi GIF', 'error');
    });
    
    toggleGifPicker();
}

window.toggleGifPicker = toggleGifPicker;
window.searchGifs = searchGifs;
window.sendGif = sendGif;

// ==================== IMAGE UPLOAD ====================
function handleImageUpload(input) {
    var file = input.files[0];
    if (!file) return;
    
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    var allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (allowedTypes.indexOf(file.type) === -1) {
        showToast('Type d\'image non supporté', 'error');
        return;
    }
    
    showToast('Traitement de l\'image...', 'info');
    
    // Client-side compression for large images
    if (file.size > 1 * 1024 * 1024 && file.type !== 'image/gif') {
        compressImage(file, function(compressedBlob) {
            uploadMedia(compressedBlob, 'image');
        });
    } else {
        uploadMedia(file, 'image');
    }
    
    input.value = '';
}

function compressImage(file, callback) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function(event) {
        var img = new Image();
        img.src = event.target.result;
        img.onload = function() {
            var canvas = document.createElement('canvas');
            var MAX_WIDTH = 1200;
            var MAX_HEIGHT = 1200;
            var width = img.width;
            var height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            } else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }

            canvas.width = width;
            canvas.height = height;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            
            canvas.toBlob(function(blob) {
                callback(blob);
            }, 'image/jpeg', 0.8);
        };
    };
}

function uploadMedia(file, type) {
    var convId = messengerState.currentConversation.id;
    var formData = new FormData();
    formData.append(type, file);
    formData.append('conversation_id', convId);
    
    showToast('Envoi en cours...', 'info');
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', type === 'image' ? '/api/messages/upload-image' : '/api/messages/upload-video');
    
    xhr.onload = function() {
        try {
            var data = JSON.parse(xhr.responseText);
            if (data.success) {
                loadMessages(convId);
                showToast('Média envoyé!', 'success');
            } else {
                showToast('Erreur: ' + (data.error || 'Inconnue'), 'error');
            }
        } catch(e) {
            showToast('Erreur envoi', 'error');
        }
    };
    xhr.send(formData);
}

window.handleImageUpload = handleImageUpload;

// ==================== DARK MODE ====================
var isDarkMode = false;

function toggleDarkMode() {
    isDarkMode = !isDarkMode;
    
    if (isDarkMode) {
        document.documentElement.style.setProperty('--fg-bg', '#1a1a2e');
        document.documentElement.style.setProperty('--fg-bg2', '#16213e');
        document.documentElement.style.setProperty('--fg-white', '#1f2937');
        document.documentElement.style.setProperty('--fg-text', '#f3f4f6');
        document.documentElement.style.setProperty('--fg-text-secondary', '#9ca3af');
        document.documentElement.style.setProperty('--fg-gray-100', '#374151');
        document.documentElement.style.setProperty('--fg-gray-200', '#4b5563');
    } else {
        document.documentElement.style.setProperty('--fg-bg', '#F0F4F8');
        document.documentElement.style.setProperty('--fg-bg2', '#E8EEF5');
        document.documentElement.style.setProperty('--fg-white', '#FFFFFF');
        document.documentElement.style.setProperty('--fg-text', '#0F1E35');
        document.documentElement.style.setProperty('--fg-text-secondary', '#64748B');
        document.documentElement.style.setProperty('--fg-gray-100', '#F1F5F9');
        document.documentElement.style.setProperty('--fg-gray-200', '#DDE5EF');
    }
    
    localStorage.setItem('darkMode', isDarkMode);
}

function initDarkMode() {
    var saved = localStorage.getItem('darkMode');
    if (saved === 'true') {
        isDarkMode = true;
        toggleDarkMode();
    }
}

window.toggleDarkMode = toggleDarkMode;

// Initialize dark mode
initDarkMode();

// ==================== PAGINATION / INFINITE SCROLL ====================
var isLoadingMore = false;
var hasMoreMessages = true;

function setupInfiniteScroll() {
    var chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    chatMessages.addEventListener('scroll', function() {
        if (chatMessages.scrollTop < 100 && !isLoadingMore && hasMoreMessages) {
            loadMoreMessages();
        }
    });
}

function loadMoreMessages() {
    if (!messengerState.currentConversation || isLoadingMore || !hasMoreMessages) return;
    
    isLoadingMore = true;
    var convId = messengerState.currentConversation.id;
    var offset = messengerState.messages.length;
    
    fetch('/api/messages/conversation/' + convId + '?offset=' + offset, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.messages && data.messages.length > 0) {
            messengerState.messages = data.messages.concat(messengerState.messages);
            renderMessages();
        } else {
            hasMoreMessages = false;
        }
        isLoadingMore = false;
    })
    .catch(function() {
        isLoadingMore = false;
    });
}

window.loadMoreMessages = loadMoreMessages;

// Initialize infinite scroll
setupInfiniteScroll();

// ==================== NOTIFICATIONS ====================
var notificationSoundEnabled = true;
var notificationPermission = Notification.permission;

function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}

function showNotification(title, body, icon) {
    if (notificationPermission === 'granted') {
        new Notification(title, { body: body, icon: icon });
    }
}

function playNotificationSound() {
    if (!notificationSoundEnabled) return;
    
    var audio = new Audio('/assets/sounds/notification.mp3');
    audio.volume = 0.3;
    audio.play().catch(function() {});
}

// ==================== LOCATION SHARING ====================
function shareLocation() {
    if (!navigator.geolocation) {
        showToast('Géolocalisation non supportée', 'error');
        return;
    }
    
    if (!messengerState.currentConversation) {
        showToast('Sélectionnez une conversation', 'info');
        return;
    }
    
    showToast('Obtention de votre position...', 'info');
    
    navigator.geolocation.getCurrentPosition(function(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        var locationUrl = 'https://www.google.com/maps?q=' + lat + ',' + lon;
        
        sendMessage('📍 Ma position : ' + locationUrl);
    }, function(err) {
        showToast('Impossible d\'obtenir votre position', 'error');
    });
}

window.shareLocation = shareLocation;

// Initialize notifications on load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
notificationPermission = Notification.permission;