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
    
    startRealTimePolling();
}

var mercureHubUrl = null;

function getMercureHubUrl() {
    if (mercureHubUrl) return mercureHubUrl;
    
    var defaultUrl = '/.well-known/mercure';
    return defaultUrl;
}

function connectMercure() {
    if (mercureEventSource) mercureEventSource.close();
    
    var hubUrl = getMercureHubUrl();
    console.log('Connecting to Mercure at:', hubUrl);
    
    mercureEventSource = new EventSource(hubUrl + '?topic=conversation/*');
    
    mercureEventSource.onmessage = function(event) {
        try {
            var data = JSON.parse(event.data);
            console.log('Mercure message received:', data);
            
            if (data.type === 'new_message') {
                handleNewMessage(data);
            } else if (data.type === 'typing') {
                updateTypingIndicator(data.users);
            } else if (data.type === 'message_read') {
                updateReadStatus(data.messageId);
            } else if (data.type === 'user_online') {
                handleUserOnlineStatus(data);
            }
        } catch (e) {
            console.log('Mercure parse error:', e);
        }
    };
    
    mercureEventSource.onerror = function(err) {
        console.log('Mercure error, falling back to polling');
        if (mercureEventSource) {
            mercureEventSource.close();
            mercureEventSource = null;
        }
        startRealTimePolling();
    };
    
    mercureEventSource.onopen = function() {
        console.log('Mercure connected!');
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
    if (!conv.lastMessage) return 'Aucun message';
    
    if (conv.lastMessage.type === 'SHARE_ACTIVITY') return '📍 Activité partagée';
    if (conv.lastMessage.type === 'SHARE_CIRCUIT') return '🗺️ Circuit partagé';
    if (conv.lastMessage.type === 'STORY_REPLY') return '📸 Réponse à une story';
    if (conv.lastMessage.image) return '📷 Image';
    if (conv.lastMessage.audio) return '🎤 Message vocal';
    
    return conv.lastMessage.content || '...';
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
var currentOffset = 0;
var hasMoreMessages = true;

function openConversation(convId) {
    console.log('Opening conversation:', convId);
    var conv = messengerState.conversations.find(function(c) { return c.id === convId; });
    if (!conv) return;

    messengerState.currentConversation = conv;
    currentOffset = 0;
    hasMoreMessages = true;
    messengerState.messages = [];

    // Update UI
    document.getElementById('emptyChatState').style.display = 'none';
    document.getElementById('chatHeader').classList.add('active');
    document.getElementById('chatInputArea').classList.add('active');

    // Set nickname if exists
    var nicknameInput = document.getElementById('nicknameInput');
    if (nicknameInput) nicknameInput.value = conv.nickname || '';

    // Update chat header
    updateChatHeader();
    document.getElementById('chatAvatar').innerHTML = getConversationAvatar(conv);

    // Load messages
    loadMessages(convId);
}

function loadMessages(convId, append = false) {
    var container = document.getElementById('messagesContainer');
    if (!convId) return;
    
    if (!append && container) {
        container.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement...</p></div>';
    }
    
    var url = '/api/messages/conversation/' + convId + '?limit=50&offset=' + currentOffset;
    
    fetch(url, {
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var newMessages = data.messages || [];
        hasMoreMessages = data.hasMore;
        
        if (append) {
            messengerState.messages = newMessages.concat(messengerState.messages);
        } else {
            messengerState.messages = newMessages;
        }
        
        if (data.conversation) {
            messengerState.currentConversation = data.conversation;
        }
        
        var maxId = 0;
        messengerState.messages.forEach(function(m) { if (m.id > maxId) maxId = m.id; });
        if (maxId > 0) messengerState.lastMessageId = maxId;
        
        renderMessages(append);
        markConversationAsRead(convId);
    })
    .catch(function(err) {
        console.error('Error loading messages:', err);
    });
}

function renderMessages(isAppending = false) {
    var container = document.getElementById('messagesContainer');
    if (!container) return;
    
    if (messengerState.messages.length === 0) {
        container.innerHTML = '<div class="messenger-empty"><i class="fa fa-commenting-o"></i><p>Aucun message</p></div>';
        return;
    }
    
    var currentUserId = messengerState.currentUserId;
    var lastDate = '';
    var lastSenderId = null;
    var html = '';
    
    messengerState.messages.forEach(function(msg, index) {
        // Date separator
        var msgDate = new Date(msg.createdAt).toLocaleDateString('fr-FR');
        if (msgDate !== lastDate) {
            html += '<div class="date-separator"><span>' + formatDateSeparator(msg.createdAt) + '</span></div>';
            lastDate = msgDate;
            lastSenderId = null; // Reset grouping on new date
        }
        
        var isMe = (msg.sender && msg.sender.id == currentUserId) || (msg.senderId == currentUserId);
        var senderId = msg.sender ? msg.sender.id : msg.senderId;
        var isGrouped = senderId === lastSenderId;
        lastSenderId = senderId;
        
        var senderName = msg.sender ? msg.sender.name : 'Utilisateur';
        var avatar = senderName.charAt(0).toUpperCase();
        var time = new Date(msg.createdAt).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        var status = isMe ? getMessageStatusIcon(msg.status) : '';
        
        var content = '';
        if (msg.image) {
            content = '<div class="message-image" onclick="window.open(\'' + msg.image + '\', \'_blank\')"><img src="' + msg.image + '" loading="lazy"></div>';
        }
        
        if (msg.audio) {
            content += '<div class="voice-message"><button class="play-voice-btn" onclick="playVoiceMessage(this, \'' + msg.audio + '\')"><i class="fa fa-play"></i></button><div class="voice-progress"><div class="voice-progress-bar"></div></div><span class="voice-duration">0:00</span></div>';
        }
        
        if (msg.content) {
            // Check if content is a GIF URL
            if (msg.content.match(/\.(gif|giphy\.com|tenor\.com)/i)) {
                content += '<div class="message-gif"><img src="' + msg.content + '" loading="lazy" style="max-width: 200px; border-radius: 10px;"></div>';
            } else if (msg.content !== '🎤 Message vocal') {
                content += '<div class="message-text">' + escapeHtml(msg.content) + '</div>';
            }
        }

        // --- NEW: SHARE RENDERING ---
        if (msg.type === 'SHARE_ACTIVITY' || msg.type === 'SHARE_CIRCUIT') {
            const data = msg.metadata || {};
            const link = msg.type === 'SHARE_ACTIVITY' ? '/activites/' + data.id : '/circuit/' + data.id;
            const label = msg.type === 'SHARE_ACTIVITY' ? 'Activité partagée' : 'Circuit partagé';
            const imageUrl = data.image || 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=300';
            
            content = `
                <div class="share-preview" onclick="window.location.href='${link}'" style="background:#fff;border-radius:12px;overflow:hidden;width:100%;max-width:260px;box-shadow:0 4px 12px rgba(0,0,0,0.1);border:1px solid #f1f2f6;cursor:pointer;transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <div style="position:relative">
                        <img src="${imageUrl}" style="width:100%;height:130px;object-fit:cover" onerror="this.src='https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=300'">
                        <div style="position:absolute;top:8px;left:8px;background:var(--fg-primary);color:#fff;padding:2px 8px;border-radius:10px;font-size:0.65rem;font-weight:700">
                            <i class="fa fa-share-alt"></i> ${label}
                        </div>
                    </div>
                    <div style="padding:0.75rem">
                        <h4 style="font-size:0.95rem;margin-bottom:0.4rem;color:var(--fg-text);font-weight:700;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${data.title || 'Sans titre'}</h4>
                        <div style="font-size:0.75rem;color:var(--fg-text-secondary);margin-bottom:0.5rem">
                            <i class="fa fa-map-marker-alt" style="color:var(--fg-accent)"></i> ${data.location || 'Tunisie'}
                            ${data.duration ? ' <i class="fa fa-clock" style="margin-left:8px;color:var(--fg-accent)"></i> ' + data.duration : ''}
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:0.5rem;border-top:1px solid #f1f2f6">
                            <span style="font-weight:800;color:var(--fg-primary);font-size:0.9rem">${data.price || '0'} TND</span>
                            <span style="background:var(--gold);color:#fff;padding:4px 10px;border-radius:15px;font-size:0.7rem;font-weight:600;cursor:pointer">Voir détails <i class="fa fa-arrow-right" style="font-size:0.6rem"></i></span>
                        </div>
                    </div>
                </div>
            `;
        } else if (msg.type === 'STORY_REPLY') {
            const data = msg.metadata;
            content = `
                <div class="story-reply-preview" style="background:rgba(0,0,0,0.03); border-radius:12px; padding:8px; border-left:4px solid var(--fg-accent);">
                    <div style="display:flex; gap:10px; margin-bottom:8px; align-items:center; background:#fff; padding:6px; border-radius:8px; cursor:pointer;" onclick="window.location.href='/'">
                        <img src="${data.storyMedia}" style="width:40px; height:60px; object-fit:cover; border-radius:4px;">
                        <div style="font-size:0.75rem;">
                            <div style="font-weight:700; color:var(--fg-primary);">Réponse à la story</div>
                            <div style="color:var(--fg-text-secondary);">de ${data.storyAuthor}</div>
                        </div>
                    </div>
                    <div class="message-text" style="padding:4px 0 0 4px;">${escapeHtml(data.text)}</div>
                </div>
            `;
        }
        // ---------------------------

        var reactionsHtml = '';
        if (msg.reactions && msg.reactions.length > 0) {
            reactionsHtml = '<div class="message-reactions-container">';
            msg.reactions.forEach(function(r) {
                reactionsHtml += '<span class="reaction-btn' + (r.users && r.users.includes(messengerState.currentUserId) ? ' active' : '') + '" onclick="addReaction(' + msg.id + ', \'' + r.emoji + '\')">' + r.emoji + ' <span class="count">' + r.count + '</span></span>';
            });
            reactionsHtml += '</div>';
        }

        html += '<div class="message-row ' + (isMe ? 'sent' : 'received') + (isGrouped ? ' grouped' : '') + '" data-id="' + msg.id + '">' +
            (!isMe && !isGrouped ? '<div class="message-avatar">' + avatar + '</div>' : (!isMe ? '<div class="message-avatar-spacer"></div>' : '')) +
            '<div class="message-content">' +
                (!isMe && !isGrouped ? '<div class="message-sender-name">' + senderName + '</div>' : '') +
                '<div class="message-bubble">' + content + '</div>' +
                '<div class="message-meta"><span class="message-time">' + time + '</span>' + status + '</div>' +
                reactionsHtml +
            '</div>' +
            '<div class="message-actions">' +
                '<button onclick="showReplyForm(' + msg.id + ')" title="Répondre"><i class="fa fa-reply"></i></button>' +
                '<button onclick="showReactionPicker(' + msg.id + ', event)" title="Réaction"><i class="fa fa-smile"></i></button>' +
                '<button onclick="showMessageMenu(' + msg.id + ')" title="Plus"><i class="fa fa-ellipsis-h"></i></button>' +
            '</div>' +
        '</div>';
    });
    
    var oldHeight = container.scrollHeight;
    container.innerHTML = html;
    
    if (isAppending) {
        container.scrollTop = container.scrollHeight - oldHeight;
    } else {
        scrollToBottom(true);
    }
}

// Add scroll listener for infinite scroll
document.addEventListener('DOMContentLoaded', function() {
    var chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.addEventListener('scroll', function() {
            if (chatMessages.scrollTop < 50 && hasMoreMessages && !messengerState.loading) {
                currentOffset += 50;
                loadMessages(messengerState.currentConversation.id, true);
            }
        });
    }
});

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
                var durationSpan = btn.parentElement.querySelector('.voice-duration');
                if (durationSpan) durationSpan.textContent = formatAudioTime(duration);
            }
        };
        
        audio.ontimeupdate = function() {
            if (audio.duration) {
                var progress = (audio.currentTime / audio.duration) * 100;
                var progressBar = btn.parentElement.querySelector('.voice-progress-bar');
                if (progressBar) progressBar.style.width = progress + '%';
                
                var durationSpan = btn.parentElement.querySelector('.voice-duration');
                if (durationSpan) durationSpan.textContent = formatAudioTime(audio.currentTime);
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

function formatAudioTime(seconds) {
    if (isNaN(seconds)) return "0:00";
    var min = Math.floor(seconds / 60);
    var sec = Math.floor(seconds % 60);
    return min + ":" + (sec < 10 ? "0" : "") + sec;
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
    document.getElementById('archivesPanel').classList.toggle('active', tab === 'archives');
    document.getElementById('newChatPanel').style.display = 'none';
    
    messengerState.currentMessengerTab = tab;
    
    if (tab === 'invitations') {
        loadReceivedInvitations();
    } else if (tab === 'friends') {
        loadFriendsList();
    } else if (tab === 'archives') {
        loadArchivedConversations();
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
    if (!confirm('Supprimer ce message?')) return;
    
    fetch('/api/messages/delete/' + msgId, {
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

// ==================== MERCURE REAL TIME ====================
var mercureEventSource = null;

function connectMercure() {
    if (mercureEventSource) mercureEventSource.close();
    
    // We subscribe to multiple topics
    var hubUrl = getMercureHubUrl();
    var topics = [
        'conversation/*',
        'user/' + messengerState.currentUserId + '/calls',
        'user/status'
    ];
    
    var url = new URL(hubUrl, window.location.origin);
    topics.forEach(topic => url.searchParams.append('topic', topic));
    
    console.log('Connecting to Mercure at:', url.toString());
    
    mercureEventSource = new EventSource(url);
    
    mercureEventSource.onmessage = function(event) {
        try {
            var data = JSON.parse(event.data);
            console.log('Mercure event:', data);
            
            if (data.type === 'new_message') {
                handleNewMessage(data);
            } else if (data.type === 'typing') {
                handleTypingIndicator(data);
            } else if (data.type === 'message_read') {
                handleMessageRead(data);
            } else if (data.type === 'user_online') {
                handleUserOnlineStatus(data);
            } else if (data.type === 'incoming_call') {
                if (window.CallManager) window.CallManager.showIncomingCall(data);
            } else if (data.type === 'call_accepted') {
                if (window.CallManager) window.CallManager.handleCallAccepted(data);
            } else if (data.type === 'call_rejected' || data.type === 'call_ended') {
                if (window.CallManager) window.CallManager.handleCallEnd(data);
            }
        } catch (e) {
            console.error('Mercure parse error:', e);
        }
    };
    
    mercureEventSource.onerror = function(err) {
        console.warn('Mercure connection lost, retrying in 5s...');
        setTimeout(connectMercure, 5000);
    };
}

function handleNewMessage(data) {
    if (messengerState.currentConversation && data.conversationId === messengerState.currentConversation.id) {
        // If it's the current conversation, add message to list
        if (!messengerState.messages.find(m => m.id === data.message.id)) {
            messengerState.messages.push(data.message);
            renderMessages();
            markConversationAsRead(data.conversationId);
        }
    } else {
        // Otherwise just refresh conversation list to show unread badge
        loadConversations();
        showToast('Nouveau message de ' + (data.message.sender ? data.message.sender.name : 'quelqu\'un'), 'info');
    }
}

function handleTypingIndicator(data) {
    if (messengerState.currentConversation && data.conversationId === messengerState.currentConversation.id) {
        if (data.userId !== messengerState.currentUserId) {
            showTypingIndicator(data.userName);
        }
    }
}

function handleMessageRead(data) {
    if (messengerState.currentConversation && data.conversationId === messengerState.conversationId) {
        var msg = messengerState.messages.find(m => m.id === data.messageId);
        if (msg) {
            msg.status = 'read';
            renderMessages();
        }
    }
}

function handleUserOnlineStatus(data) {
    if (messengerState.currentConversation && messengerState.currentConversation.otherUser && 
        messengerState.currentConversation.otherUser.id === data.userId) {
        updateOnlineIndicator(true);
    }
    
    // Update in messenger state
    if (!messengerState.onlineUsers.includes(data.userId)) {
        messengerState.onlineUsers.push(data.userId);
    }
}

function updateOnlineIndicator(isOnline) {
    var statusEl = document.getElementById('chatStatus');
    if (!statusEl) return;
    
    if (isOnline) {
        statusEl.innerHTML = '<span class="online-dot"></span>En ligne';
    } else {
        statusEl.innerHTML = '<span class="offline-dot"></span>Hors ligne';
    }
}

function showTypingIndicator(name) {
    var statusEl = document.getElementById('chatStatus');
    if (!statusEl) return;
    
    var originalContent = statusEl.innerHTML;
    statusEl.innerHTML = '<span class="typing-indicator"><i>.</i><i>.</i><i>.</i></span> ' + name + ' écrit...';
    
    if (messengerState.typingTimeout) clearTimeout(messengerState.typingTimeout);
    messengerState.typingTimeout = setTimeout(function() {
        statusEl.innerHTML = originalContent;
    }, 3000);
}

// ==================== REAL TIME POLLING (REDUCED) ====================
function startRealTimePolling() {
    // We only poll for things that don't have Mercure events yet
    // or as a safety net every 30 seconds instead of 1s
    if (messengerState.pollingInterval) clearInterval(messengerState.pollingInterval);
    
    messengerState.pollingInterval = setInterval(function() {
        updateMyOnlineStatus();
    }, 30000);
    
    updateMyOnlineStatus();
}

function updateMyOnlineStatus() {
    fetch('/api/messages/user/online', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).catch(function() {});
}

// ==================== VOICE RECORDING ====================
var mediaRecorder = null;
var audioChunks = [];
var recordingTimer = null;
var recordingStartTime = null;

function toggleVoiceRecording() {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        stopRecording();
    } else {
        startRecording();
    }
}

function startRecording() {
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };
            
            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                uploadVoiceMessage(audioBlob);
                stream.getTracks().forEach(track => track.stop());
            };
            
            mediaRecorder.start();
            
            document.getElementById('micBtn').classList.add('recording');
            document.getElementById('voiceDuration').classList.add('recording');
            
            recordingStartTime = Date.now();
            recordingTimer = setInterval(updateRecordingTimer, 100);
        })
        .catch(err => {
            console.error('Mic access denied:', err);
            showToast('Accès micro refusé', 'error');
        });
}

function stopRecording() {
    if (mediaRecorder) {
        mediaRecorder.stop();
        document.getElementById('micBtn').classList.remove('recording');
        document.getElementById('voiceDuration').classList.remove('recording');
        clearInterval(recordingTimer);
    }
}

function updateRecordingTimer() {
    var diff = Date.now() - recordingStartTime;
    var sec = Math.floor(diff / 1000);
    var ms = Math.floor((diff % 1000) / 100);
    document.getElementById('voiceDuration').textContent = sec + '.' + ms + 's';
    
    if (sec >= 60) stopRecording(); // Max 60s
}

function uploadVoiceMessage(blob) {
    if (!messengerState.currentConversation) return;
    
    var formData = new FormData();
    formData.append('audio', blob);
    formData.append('conversation_id', messengerState.currentConversation.id);
    
    fetch('/api/messages/upload-audio', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Message will come back via Mercure
        }
    })
    .catch(err => {
        console.error('Upload failed:', err);
        showToast('Erreur envoi audio', 'error');
    });
}

// ==================== EMOJI & GIF PICKER ====================
function toggleEmojiPicker() {
    var picker = document.getElementById('emojiPicker');
    if (!picker) {
        picker = document.createElement('div');
        picker.id = 'emojiPicker';
        var emojis = ['👍', '❤️', '😂', '😮', '😢', '😡', '🙏', '👎', '✨', '🔥', '✅', '❌', '🚀', '⭐', '📱', '💻'];
        picker.innerHTML = emojis.map(e => `<button onclick="insertEmoji('${e}')">${e}</button>`).join('');
        document.getElementById('chatInputArea').appendChild(picker);
    } else {
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    }
}

function insertEmoji(emoji) {
    var input = document.getElementById('messageInput');
    input.value += emoji;
    input.focus();
    document.getElementById('emojiPicker').style.display = 'none';
}

function toggleGifPicker() {
    var picker = document.getElementById('gifPicker');
    if (!picker) {
        picker = document.createElement('div');
        picker.id = 'gifPicker';
        picker.className = 'messenger-gif-picker';
        picker.innerHTML = '<div class="gif-picker-header">' +
            '<input type="text" id="gifSearchInput" placeholder="Rechercher un GIF..." onkeyup="if(event.key===\'Enter\') searchGifs(this.value)">' +
            '<button onclick="searchGifs(document.getElementById(\'gifSearchInput\').value)"><i class="fa fa-search"></i></button>' +
            '</div>' +
            '<div id="gifResults" class="gif-results"></div>';
        document.getElementById('chatInputArea').appendChild(picker);
        searchGifs('trending');
    } else {
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        if (picker.style.display === 'block') {
            document.getElementById('gifSearchInput').focus();
        }
    }
}

function searchGifs(query) {
    var resultsContainer = document.getElementById('gifResults');
    if (!resultsContainer) return;
    
    resultsContainer.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i></div>';
    
    var url = (!query || query === 'trending') ? 
        '/api/gif/trending' :
        '/api/gif/search?q=' + encodeURIComponent(query);
        
    fetch(url)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.gifs && data.gifs.length > 0) {
                renderGifResults(data.gifs);
            } else {
                throw new Error('No results');
            }
        })
        .catch(err => {
            console.error('GIF search failed:', err);
            resultsContainer.innerHTML = '<div class="messenger-error" style="font-size:0.8rem;padding:10px;">' +
                'Service GIF temporairement indisponible.<br>' +
                'Réessayez plus tard.' +
                '</div>';
        });
}

function renderGifResults(gifs) {
    var resultsContainer = document.getElementById('gifResults');
    if (!resultsContainer) return;
    
    resultsContainer.innerHTML = gifs.map(gif => 
        '<img src="' + gif.thumb + '" onclick="sendGif(\'' + gif.url + '\')" class="gif-item" loading="lazy">'
    ).join('');
}

function sendGif(url) {
    sendMessage(url);
    var picker = document.getElementById('gifPicker');
    if (picker) picker.style.display = 'none';
}

// ==================== IMAGE UPLOAD ====================
function handleImageUpload(input) {
    if (!input.files || !input.files[0] || !messengerState.currentConversation) return;
    
    var file = input.files[0];
    var formData = new FormData();
    formData.append('image', file);
    formData.append('conversation_id', messengerState.currentConversation.id);
    
    fetch('/api/messages/upload-image', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Mercure will handle it
        }
    })
    .catch(err => showToast('Erreur upload image', 'error'));
}

// ==================== TYPING INDICATOR ====================
function sendTypingIndicator() {
    if (!messengerState.currentConversation) return;
    
    var convId = messengerState.currentConversation.id;
    fetch('/api/messages/conversation/' + convId + '/typing', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).catch(function() {});
}

function onMessageInput() {
    sendTypingIndicator();
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

// Initialize notifications on load
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
notificationPermission = Notification.permission;

// ==================== CONVERSATION SETTINGS ====================
var convSettingsOpen = false;

function toggleConvSettings() {
    var dropdown = document.getElementById('convSettingsDropdown');
    convSettingsOpen = !convSettingsOpen;
    if (dropdown) {
        dropdown.classList.toggle('show', convSettingsOpen);
    }
    document.addEventListener('click', closeConvSettingsOnClickOutside);
}

function closeConvSettingsOnClickOutside(e) {
    if (!e.target.closest('.conv-settings-dropdown') && !e.target.closest('[onclick="toggleConvSettings()"]')) {
        var dropdown = document.getElementById('convSettingsDropdown');
        if (dropdown) dropdown.classList.remove('show');
        convSettingsOpen = false;
        document.removeEventListener('click', closeConvSettingsOnClickOutside);
    }
}

function saveNickname() {
    var nicknameInput = document.getElementById('nicknameInput');
    if (!nicknameInput || !messengerState.currentConversation) return;

    var nickname = nicknameInput.value.trim();
    var convId = messengerState.currentConversation.id;

    fetch('/api/messages/conversation/' + convId + '/nickname', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ nickname: nickname })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            messengerState.currentConversation.nickname = nickname || null;
            updateChatHeader();
            loadConversations();
            if (nickname) {
                showToast('Pseudo enregistré', 'success');
            } else {
                showToast('Pseudo supprimé', 'success');
            }
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error saving nickname:', err);
        showToast('Erreur de connexion', 'error');
    });
}

function setChatTheme(el) {
    if (!el || !messengerState.currentConversation) return;

    var theme = el.dataset.theme;
    var chatMessages = document.getElementById('chatMessages');

    document.querySelectorAll('.theme-color').forEach(c => c.classList.remove('active'));
    el.classList.add('active');

    chatMessages.className = 'chat-messages';
    if (theme === 'gradient') chatMessages.classList.add('chat-theme-gradient');
    else if (theme === 'sunset') chatMessages.classList.add('chat-theme-sunset');
    else if (theme === 'ocean') chatMessages.classList.add('chat-theme-ocean');
    else if (theme === 'forest') chatMessages.classList.add('chat-theme-forest');
    else if (theme === 'dark') chatMessages.classList.add('chat-theme-dark');
    else if (theme === 'light') chatMessages.classList.add('chat-theme-blue');
    else if (theme === '#FF6B2C') chatMessages.classList.add('chat-theme-orange');
    else if (theme === '#10B981') chatMessages.classList.add('chat-theme-green');
    else if (theme === '#8B5CF6') chatMessages.classList.add('chat-theme-purple');
    else if (theme === '#EC4899') chatMessages.classList.add('chat-theme-pink');

    fetch('/api/messages/conversation/' + messengerState.currentConversation.id + '/theme', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ theme: theme })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showToast('Thème appliqué', 'success');
    })
    .catch(err => console.error('Error saving theme:', err));
}

function toggleMute() {
    if (!messengerState.currentConversation) return;

    var toggle = document.getElementById('muteToggle');
    toggle.classList.toggle('active');

    fetch('/api/messages/conversation/' + messengerState.currentConversation.id + '/mute', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.muted ? 'Notifications désactivées' : 'Notifications activées', 'success');
        }
    })
    .catch(err => console.error('Error toggling mute:', err));
}

function archiveConversation() {
    if (!messengerState.currentConversation) return;
    if (!confirm('Archiver cette conversation ?')) return;

    var convId = messengerState.currentConversation.id;

    fetch('/api/messages/conversation/' + convId + '/archive', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Conversation archivée', 'success');
            document.getElementById('convSettingsDropdown').classList.remove('show');
            messengerState.currentConversation = null;
            document.getElementById('chatHeader').classList.remove('active');
            document.getElementById('chatInputArea').classList.remove('active');
            document.getElementById('emptyChatState').style.display = 'flex';
            document.getElementById('messagesContainer').innerHTML = '';
            loadConversations();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error archiving:', err);
        showToast('Erreur lors de l\'archivage', 'error');
    });
}

function deleteConversation() {
    if (!messengerState.currentConversation) return;
    if (!confirm('Supprimer cette conversation ? Cette action est irréversible.')) return;

    var convId = messengerState.currentConversation.id;

    fetch('/api/messages/conversation/' + convId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Conversation supprimée', 'success');
            document.getElementById('convSettingsDropdown').classList.remove('show');
            messengerState.currentConversation = null;
            document.getElementById('chatHeader').classList.remove('active');
            document.getElementById('chatInputArea').classList.remove('active');
            document.getElementById('emptyChatState').style.display = 'flex';
            document.getElementById('messagesContainer').innerHTML = '';
            loadConversations();
        } else {
            showToast(data.error || 'Erreur lors de la suppression', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error deleting conversation:', err);
        showToast('Erreur lors de la suppression', 'error');
    });
}

function blockUser() {
    if (!messengerState.currentConversation || !messengerState.currentConversation.otherUser) return;
    if (!confirm('Bloquer cette personne ? Elle ne pourra plus vous envoyer de messages.')) return;

    var userId = messengerState.currentConversation.otherUser.id;
    fetch('/api/friend/block/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Utilisateur bloqué', 'success');
            document.getElementById('convSettingsDropdown').classList.remove('show');
            messengerState.currentConversation = null;
            document.getElementById('chatHeader').classList.remove('active');
            document.getElementById('chatInputArea').classList.remove('active');
            document.getElementById('emptyChatState').style.display = 'flex';
            document.getElementById('messagesContainer').innerHTML = '';
            loadConversations();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error blocking user:', err);
        showToast('Erreur lors du blocage', 'error');
    });
}

function updateChatHeader() {
    if (!messengerState.currentConversation) return;
    var name = messengerState.currentConversation.nickname ||
                (messengerState.currentConversation.otherUser ? messengerState.currentConversation.otherUser.name : 'Conversation');
    document.getElementById('chatName').textContent = name;
    var nicknameInput = document.getElementById('nicknameInput');
    if (nicknameInput) nicknameInput.value = messengerState.currentConversation.nickname || '';
    var inlineInput = document.getElementById('inlineNicknameInput');
    if (inlineInput) inlineInput.value = messengerState.currentConversation.nickname || '';
}

function toggleInlineNickname() {
    var container = document.getElementById('nicknameEditContainer');
    if (!container) return;
    var isVisible = container.style.display !== 'none';
    container.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) {
        var input = document.getElementById('inlineNicknameInput');
        if (input) {
            input.value = messengerState.currentConversation?.nickname || '';
            input.focus();
        }
    }
}

function saveInlineNickname() {
    var input = document.getElementById('inlineNicknameInput');
    if (!input || !messengerState.currentConversation) return;
    var nickname = input.value.trim();
    document.getElementById('nicknameInput').value = nickname;
    saveNickname();
    document.getElementById('nicknameEditContainer').style.display = 'none';
}

// ==================== MESSAGE REACTIONS ====================
var reactionPicker = null;

function showReactionPicker(msgId, event) {
    event.stopPropagation();

    closeAllReactionPickers();

    reactionPicker = document.createElement('div');
    reactionPicker.className = 'reaction-picker show';
    reactionPicker.innerHTML = '<button onclick="addReaction(' + msgId + ', \'❤️\')">❤️</button>' +
        '<button onclick="addReaction(' + msgId + ', \'👍\')">👍</button>' +
        '<button onclick="addReaction(' + msgId + ', \'😂\')">😂</button>' +
        '<button onclick="addReaction(' + msgId + ', \'😮\')">😮</button>' +
        '<button onclick="addReaction(' + msgId + ', \'😢\')">😢</button>' +
        '<button onclick="addReaction(' + msgId + ', \'😡\')">😡</button>';

    var msgRow = document.querySelector('.message-row[data-id="' + msgId + '"]');
    if (msgRow) {
        var msgContent = msgRow.querySelector('.message-content');
        if (msgContent) {
            msgContent.style.position = 'relative';
            msgContent.appendChild(reactionPicker);
        }
    }

    document.addEventListener('click', closeAllReactionPickers);
}

function closeAllReactionPickers() {
    document.querySelectorAll('.reaction-picker').forEach(p => p.remove());
    document.removeEventListener('click', closeAllReactionPickers);
}

function addReaction(msgId, emoji) {
    fetch('/api/messages/message/' + msgId + '/react', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ emoji: emoji })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeAllReactionPickers();
            loadMessages(messengerState.currentConversation.id);
        }
    })
    .catch(err => console.error('Error adding reaction:', err));
}

function renderMessagesWithReactions() {
    renderMessages();
}

// ==================== ARCHIVED CONVERSATIONS ====================
var archivedConversations = [];

function loadArchivedConversations() {
    var list = document.getElementById('archivesList');
    if (list) list.innerHTML = '<div class="messenger-loading"><i class="fa fa-spinner fa-spin"></i><p>Chargement...</p></div>';
    
    fetch('/api/messages/archived?t=' + Date.now(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        console.log('Archived conversations loaded:', data);
        archivedConversations = data.conversations || [];
        renderArchivedConversations();
        updateArchivesBadge();
    })
    .catch(function(err) {
        console.error('Error loading archived conversations:', err);
        if (list) list.innerHTML = '<div class="messenger-empty"><i class="fa fa-exclamation-triangle"></i><p>Erreur de chargement</p></div>';
    });
}

function renderArchivedConversations() {
    var list = document.getElementById('archivesList');
    if (!list) return;
    
    if (archivedConversations.length === 0) {
        list.innerHTML = '<div class="messenger-empty"><i class="fa fa-archive"></i><p>Aucune conversation archivée</p><small>Les conversations archivées apparaîtront ici</small></div>';
        return;
    }
    
    list.innerHTML = archivedConversations.map(function(conv) {
        var name = conv.nickname || (conv.otherUser ? conv.otherUser.name : 'Conversation');
        var avatar = conv.otherUser && conv.otherUser.avatar ? '<img src="' + conv.otherUser.avatar + '" alt="">' : name.charAt(0).toUpperCase();
        
        return '<div class="conversation-item" style="opacity:0.7">' +
            '<div class="conv-avatar" style="background:#9ca3af">' + avatar + '</div>' +
            '<div class="conv-content" style="flex:1">' +
                '<div class="conv-header"><span class="conv-name">' + escapeHtml(name) + '</span></div>' +
                '<div class="conv-preview" style="font-size:0.75rem;color:var(--fg-text-secondary)"><i class="fa fa-archive"></i> Archivée</div>' +
            '</div>' +
            '<button class="invite-btn" onclick="unarchiveConversation(' + conv.id + ')" title="Désarchiver" style="background:#10b981">' +
                '<i class="fa fa-inbox"></i>' +
            '</button>' +
            '<button class="invite-btn reject" onclick="deleteConversationFromArchive(' + conv.id + ')" title="Supprimer">' +
                '<i class="fa fa-trash"></i>' +
            '</button>' +
        '</div>';
    }).join('');
}

function updateArchivesBadge() {
    var badge = document.getElementById('archivesBadge');
    if (badge) {
        if (archivedConversations.length > 0) {
            badge.textContent = archivedConversations.length;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    }
}

function unarchiveConversation(convId) {
    fetch('/api/messages/conversation/' + convId + '/unarchive', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Conversation désarchivée', 'success');
            loadArchivedConversations();
            loadConversations();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error unarchiving:', err);
        showToast('Erreur lors de la désarchivage', 'error');
    });
}

function deleteConversationFromArchive(convId) {
    if (!confirm('Supprimer cette conversation archivée ? Cette action est irréversible.')) return;
    
    fetch('/api/messages/conversation/' + convId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Conversation supprimée', 'success');
            loadArchivedConversations();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function(err) {
        console.error('Error deleting:', err);
        showToast('Erreur lors de la suppression', 'error');
    });
}

window.loadArchivedConversations = loadArchivedConversations;
window.unarchiveConversation = unarchiveConversation;
window.deleteConversationFromArchive = deleteConversationFromArchive;

window.toggleConvSettings = toggleConvSettings;
window.saveNickname = saveNickname;
window.setChatTheme = setChatTheme;
window.toggleMute = toggleMute;
window.archiveConversation = archiveConversation;
window.deleteConversation = deleteConversation;
window.blockUser = blockUser;
window.showReactionPicker = showReactionPicker;
window.addReaction = addReaction;
window.toggleInlineNickname = toggleInlineNickname;
window.saveInlineNickname = saveInlineNickname;
window.updateChatHeader = updateChatHeader;