
var currentConversationId = null;
var conversations = [];
var selectedGroupMembers = [];
var currentMessengerTab = 'conversations';
var pendingFriendRequests = [];
var sentFriendRequests = [];
var friendsList = [];

// Audio Call Variables
var currentCallUserId = null;
var currentCallUserName = null;
var callStatus = 'idle'; // idle, connecting, ringing, connected, failed, ended, no_answer
var callStartTime = null;
var callTimerInterval = null;
var localStream = null;
var peerConnection = null;
var isMuted = false;
var isSpeakerOn = false;
var callTimeout = null;
var callData = null;

// Conversation Loading State
var conversationsLoaded = false;
var conversationsLoading = false;
var conversationsLoadTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
    loadFriendRequestsCount();
    
    var userSearchTimeout;
    document.getElementById('userSearchInput').addEventListener('input', function() {
        var query = this.value;
        clearTimeout(userSearchTimeout);
        if (query.length >= 2) {
            document.getElementById('userSearchResults').innerHTML = '<p style="padding:0.5rem;color:#64748B"><i class="fa fa-spinner fa-spin"></i> Recherche...</p>';
            userSearchTimeout = setTimeout(function() {
                searchUsers(query);
            }, 300);
        } else {
            document.getElementById('userSearchResults').innerHTML = '';
        }
    });
    
    var groupSearchTimeout;
    document.getElementById('groupUserSearch').addEventListener('input', function() {
        var query = this.value;
        clearTimeout(groupSearchTimeout);
        if (query.length >= 2) {
            document.getElementById('groupUserResults').innerHTML = '<p style="padding:0.5rem;color:#64748B"><i class="fa fa-spinner fa-spin"></i> Recherche...</p>';
            groupSearchTimeout = setTimeout(function() {
                searchUsers(query, 'group');
            }, 300);
        } else {
            document.getElementById('groupUserResults').innerHTML = '';
        }
    });
});

function loadConversations(forceRefresh) {
    var list = document.getElementById('conversationList');
    
    if (conversationsLoading) {
        return;
    }
    
    conversationsLoading = true;
    
    if (!conversationsLoaded || forceRefresh) {
        var skeletonHTML = 
            '<div class="skeleton-list" id="convSkeleton">' +
                '<div class="skeleton-conv-item">' +
                    '<div class="skeleton skeleton-avatar"></div>' +
                    '<div class="skeleton-conv-content">' +
                        '<div class="skeleton skeleton-name"></div>' +
                        '<div class="skeleton skeleton-preview"></div>' +
                    '</div>' +
                '</div>' +
                '<div class="skeleton-conv-item">' +
                    '<div class="skeleton skeleton-avatar"></div>' +
                    '<div class="skeleton-conv-content">' +
                        '<div class="skeleton skeleton-name" style="width:55%"></div>' +
                        '<div class="skeleton skeleton-preview" style="width:75%"></div>' +
                    '</div>' +
                '</div>' +
                '<div class="skeleton-conv-item">' +
                    '<div class="skeleton skeleton-avatar"></div>' +
                    '<div class="skeleton-conv-content">' +
                        '<div class="skeleton skeleton-name" style="width:70%"></div>' +
                        '<div class="skeleton skeleton-preview" style="width:45%"></div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        
        if (!conversationsLoaded) {
            list.innerHTML = skeletonHTML;
        }
    }
    
    var loadStartTime = Date.now();
    
    fetch('/api/messages/conversations', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        var loadTime = Date.now() - loadStartTime;
        var minDisplayTime = 300;
        
        if (loadTime < minDisplayTime && !conversationsLoaded) {
            conversationsLoadTimeout = setTimeout(function() {
                finishLoadingConversations(data.conversations || []);
            }, minDisplayTime - loadTime);
        } else {
            finishLoadingConversations(data.conversations || []);
        }
    })
    .catch(function(error) {
        console.error('Error loading conversations:', error);
        conversationsLoading = false;
        
        if (!conversationsLoaded) {
            list.innerHTML = 
                '<div class="empty-state error">' +
                    '<div class="empty-icon"><i class="fa fa-wifi"></i></div>' +
                    '<h4>Erreur de connexion</h4>' +
                    '<p>Impossible de charger les conversations</p>' +
                    '<button class="retry-btn" onclick="loadConversations(true)">' +
                        '<i class="fa fa-refresh"></i> Réessayer' +
                    '</button>' +
                '</div>';
        }
    });
}

function finishLoadingConversations(newConversations) {
    var list = document.getElementById('conversationList');
    
    if (conversationsLoadTimeout) {
        clearTimeout(conversationsLoadTimeout);
        conversationsLoadTimeout = null;
    }
    
    conversationsLoading = false;
    conversationsLoaded = true;
    
    if (newConversations.length > 0) {
        conversations = newConversations;
        list.style.opacity = '0';
        list.style.transition = 'opacity 0.2s ease';
        
        setTimeout(function() {
            renderConversations(conversations);
            list.style.opacity = '1';
        }, 50);
    } else {
        list.innerHTML = 
            '<div class="empty-state">' +
                '<div class="empty-icon"><i class="fa fa-comments"></i></div>' +
                '<h4>Aucune conversation</h4>' +
                '<p>Commencez une nouvelle conversation en cliquant sur le bouton <strong>+</strong></p>' +
            '</div>';
    }
}

function renderConversations(convs) {
    var list = document.getElementById('conversationList');
    if (!convs || convs.length === 0) {
        list.innerHTML = '<p style="padding:2rem;text-align:center;color:#64748B">Aucune conversation</p>';
        return;
    }
    var html = '';
    for (var i = 0; i < convs.length; i++) {
        var c = convs[i];
        var name = c.type === 'group' ? c.name : (c.otherUser ? c.otherUser.name : 'Utilisateur');
        var initial = name.charAt(0).toUpperCase();
        var avatar = c.type === 'group' ? null : (c.otherUser ? c.otherUser.avatar : null);
        var lastMsg = c.lastMessage && c.lastMessage.content ? c.lastMessage.content : 'Aucun message';
        var time = c.lastMessage && c.lastMessage.createdAt ? formatTime(c.lastMessage.createdAt) : '';
        var unread = c.unreadCount || 0;
        
        html += '<div class="conversation-item" data-id="' + c.id + '" data-name="' + name.replace(/"/g, '&quot;') + '" data-index="' + i + '">';
        html += '<div class="conv-avatar">' + (avatar ? '<img src="' + avatar + '">' : initial) + (c.type !== 'group' ? '<div class="online-indicator"></div>' : '') + '</div>';
        html += '<div class="conv-info"><div class="conv-info-header"><span class="conv-name">' + name + '</span><span class="conv-time">' + time + '</span></div>';
        html += '<div class="conv-preview">' + (lastMsg.length > 35 ? lastMsg.substring(0,35) + '...' : lastMsg) + (unread > 0 ? '<span class="conv-badge">' + unread + '</span>' : '') + '</div></div></div>';
    }
    list.innerHTML = html;
    
    var items = list.querySelectorAll('.conversation-item');
    for (var j = 0; j < items.length; j++) {
        items[j].addEventListener('click', function() {
            var id = parseInt(this.getAttribute('data-id'));
            var name = this.getAttribute('data-name');
            var index = parseInt(this.getAttribute('data-index'));
            openConversation(id, name, index);
        });
    }
}

// Check for errors in console - add debug


function openConversation(id, name, index) {
    currentConversationId = id;
    
    var items = document.querySelectorAll('.conversation-item');
    for (var i = 0; i < items.length; i++) {
        items[i].classList.remove('active');
    }
    if (items && items[index]) {
        items[index].classList.add('active');
    }
    
    document.getElementById('emptyChatState').style.display = 'none';
    document.getElementById('chatHeader').classList.add('active');
    document.getElementById('chatInputArea').style.display = 'flex';
    document.getElementById('chatName').textContent = name || 'Conversation';
    document.getElementById('chatAvatar').textContent = (name ? name.charAt(0).toUpperCase() : '?');
    
    // Show loading
    document.getElementById('messagesContainer').innerHTML = '<div style="text-align:center;padding:2rem;color:#64748B"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>';
    
    fetch('/api/messages/conversation/' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { 
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json(); 
    })
    .then(function(data) {
        if (data.messages && data.messages.length > 0) {
            renderMessages(data.messages);
        } else if (data.messages && data.messages.length === 0) {
            document.getElementById('messagesContainer').innerHTML = '<p style="padding:2rem;text-align:center;color:#64748B">Aucun message dans cette conversation</p>';
        } else if (data.error) {
            document.getElementById('messagesContainer').innerHTML = 
                '<div class="empty-state error">' +
                    '<div class="empty-icon"><i class="fa fa-exclamation-triangle"></i></div>' +
                    '<h4>Erreur</h4>' +
                    '<p>' + data.error + '</p>' +
                '</div>';
        }
    })
    .catch(function(error) {
        document.getElementById('messagesContainer').innerHTML = 
            '<div style="text-align:center;padding:2rem;color:#dc3545">' +
                '<i class="fa fa-exclamation-triangle"></i>' +
                '<p>Erreur: ' + error.message + '</p>' +
                '<button onclick="openConversation(' + id + ',\'' + (name || '').replace(/'/g, "\\'") + '\',' + index + ')" style="margin-top:1rem;padding:0.5rem 1.5rem;background:var(--fg-primary);color:white;border:none;border-radius:20px;cursor:pointer">' +
                    '<i class="fa fa-refresh"></i> Réessayer' +
                '</button>' +
            '</div>';
    });
}

function renderMessages(messages) {
    var container = document.getElementById('messagesContainer');
    
    if (!container) {
        alert('Container not found!');
        return;
    }
    
    if (!messages || messages.length === 0) {
        container.innerHTML = '<p style="padding:2rem;text-align:center;color:#64748B">Aucun message</p>';
        return;
    }
    
    var html = '';
    var prevSenderId = null;
    var prevDate = null;
    var prevIndex = -1;
    
    function getDateLabel(dateStr) {
        var msgDate = new Date(dateStr);
        msgDate.setHours(0, 0, 0, 0);
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        var yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (msgDate.getTime() === today.getTime()) {
            return 'Aujourd\'hui';
        } else if (msgDate.getTime() === yesterday.getTime()) {
            return 'Hier';
        } else {
            return msgDate.toLocaleDateString('fr-FR', {day: 'numeric', month: 'long', year: 'numeric'});
        }
    }
    
    function getTimeLabel(dateStr) {
        return new Date(dateStr).toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
    }
    
    function getBubbleStyle(isMe, position) {
        if (isMe) {
            if (position === 'first') return 'background:linear-gradient(135deg, #0D5BD7, #1273E4);color:#fff;padding:12px 16px;border-radius:20px 20px 4px 20px;box-shadow:0 2px 4px rgba(0,0,0,0.1);';
            if (position === 'middle') return 'background:linear-gradient(135deg, #0D5BD7, #1273E4);color:#fff;padding:10px 16px;border-radius:20px 20px 4px 4px;box-shadow:0 1px 2px rgba(0,0,0,0.05);';
            return 'background:linear-gradient(135deg, #0D5BD7, #1273E4);color:#fff;padding:12px 16px;border-radius:20px 20px 20px 4px;box-shadow:0 2px 4px rgba(0,0,0,0.1);';
        } else {
            if (position === 'first') return 'background:#FFFFFF;color:#0F1E35;padding:12px 16px;border-radius:4px 20px 20px 20px;box-shadow:0 1px 2px rgba(0,0,0,0.1);';
            if (position === 'middle') return 'background:#FFFFFF;color:#0F1E35;padding:10px 16px;border-radius:4px 20px 20px 20px;box-shadow:0 1px 1px rgba(0,0,0,0.05);';
            return 'background:#FFFFFF;color:#0F1E35;padding:12px 16px;border-radius:4px 20px 20px 20px;box-shadow:0 1px 2px rgba(0,0,0,0.1);';
        }
    }
    
    for (var i = 0; i < messages.length; i++) {
        var m = messages[i];
        var isMe = m.isMe;
        var senderId = m.sender ? m.sender.id : null;
        var isGrouped = prevSenderId === senderId && prevIndex === i - 1;
        
        var senderName = m.sender ? m.sender.name : 'User';
        var content = m.content || '';
        var msgDate = new Date(m.createdAt);
        var msgDateStr = msgDate.toDateString();
        var time = getTimeLabel(m.createdAt);
        
        prevSenderId = senderId;
        prevIndex = i;
        
        // Determine position
        var nextSame = (i < messages.length - 1) && (messages[i + 1].sender ? messages[i + 1].sender.id : null) === senderId;
        var prevSame = isGrouped;
        
        var position = 'single';
        if (prevSame && nextSame) position = 'middle';
        else if (prevSame) position = 'last';
        else if (nextSame) position = 'first';
        
        // Date separator
        if (prevDate !== msgDateStr) {
            html += '<div class="date-separator" style="text-align:center;margin:20px 0;font-size:0.75rem;color:#64748B;font-weight:500;position:relative;">' +
                '<span style="background:#F4F7FB;padding:0 12px;position:relative;z-index:1;">' + getDateLabel(m.createdAt) + '</span>' +
                '<div style="position:absolute;top:50%;left:0;right:0;height:1px;background:#E5E7EB;z-index:0;"></div></div>';
            prevDate = msgDateStr;
        }
        
        // Message content rendering
        var contentHtml = '';
        
        // Audio message
        var isAudioMsg = content.indexOf('🎤') === 0 || content.indexOf('Message vocal') !== -1 || (m.image && m.image.indexOf('/audio/') !== -1);
        
        if (isAudioMsg) {
            var audioSrc = m.image || '';
            // Ensure path starts with /
            if (audioSrc && !audioSrc.startsWith('/') && !audioSrc.startsWith('http')) {
                audioSrc = '/' + audioSrc;
            }
            var initialDuration = '...';
            contentHtml = '<div class="message-audio" style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:rgba(255,255,255,0.15);border-radius:24px;min-width:180px;max-width:240px;">' +
                '<audio id="audio-' + m.id + '" src="' + audioSrc + '" preload="metadata" onloadedmetadata="audioLoaded(' + m.id + ')" style="display:none;"></audio>' +
                '<button onclick="toggleAudioPlayback(' + m.id + ', this)" style="width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,0.25);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                '<i class="fa fa-play" style="color:#fff;font-size:14px;"></i></button>' +
                '<div style="flex:1;position:relative;height:24px;background:rgba(255,255,255,0.2);border-radius:12px;overflow:hidden;cursor:pointer;" onclick="seekAudio(' + m.id + ', event)">' +
                '<div id="audio-progress-' + m.id + '" style="width:0%;height:100%;background:rgba(255,255,255,0.6);border-radius:12px;transition:width 0.1s;"></div></div>' +
                '<span id="audio-duration-' + m.id + '" style="font-size:0.7rem;color:inherit;opacity:0.8;min-width:35px;text-align:center;">' + initialDuration + '</span></div>';
        }
        // Image message
        else if (m.image && (m.image.indexOf('.jpg') !== -1 || m.image.indexOf('.png') !== -1 || m.image.indexOf('.jpeg') !== -1)) {
            contentHtml = '<img src="' + m.image + '" style="max-width:260px;border-radius:12px;cursor:pointer;display:block;box-shadow:0 2px 8px rgba(0,0,0,0.15);" onclick="window.open(this.src)">';
        }
        // GIF message
        else if (m.image || content === '[GIF]' || content.indexOf('[GIF]') !== -1) {
            var fallbackGifs = [
                'https://media.tenor.com/Ru5ZLy6t4sUAAAAi/hi.gif',
                'https://media.tenor.com/xlYSE5h2lX6UAAAAi/waving-hand.gif',
                'https://media.tenor.com/Yes.gif'
            ];
var gifUrl = m.image || '';
            var isGifContent = content === '[GIF]' || content.indexOf('[GIF]') !== -1;
            if ((m.image && m.image.indexOf('.gif') !== -1) || isGifContent) {
                contentHtml = '<div class="gif-error-handler" data-msg-id="' + m.id + '" style="display:inline-block;">' +
                    '<img src="' + (m.image || '') + '" class="message-gif" style="max-width:180px;border-radius:12px;cursor:pointer;display:block;object-fit:contain;box-shadow:0 2px 8px rgba(0,0,0,0.1);" ' +
                    'onclick="window.open(this.src)" ' +
                    'onerror="handleGifError(this)">' +
                    '</div>';
            }
        // Regular text
        else {
            contentHtml = '<span style="word-wrap:break-word;line-height:1.4;">' + escapeHtml(content) + '</span>';
        }
        
        // Add reply preview if this message replies to another
        var replyPreviewHtml = '';
        if (m.replyTo) {
            var replySender = m.replyTo.sender && m.replyTo.sender.name ? m.replyTo.sender.name : 'Utilisateur';
            var replyContent = m.replyTo.content ? m.replyTo.content.substring(0, 40) : 'Message';
            replyPreviewHtml = '<div class="reply-preview" style="border-left:3px solid var(--fg-primary);background:rgba(0,0,0,0.03);padding:0.25rem 0.5rem;margin-bottom:0.25rem;border-radius:0 6px 6px 0;font-size:0.75rem;">' +
                '<span style="font-weight:600;color:var(--fg-primary);">↪ ' + replySender + '</span>' +
                '<span style="color:var(--fg-text-secondary);margin-left:0.25rem;">' + escapeHtml(replyContent) + '</span></div>';
        }
        
        // Build message with grouping
        if (isMe) {
            // My messages - right aligned
            var bubbleSty = getBubbleStyle(true, position);
            var marginTop = position === 'first' || position === 'single' ? '12px' : '2px';
            
            html += '<div style="display:flex;justify-content:flex-end;padding:2px 12px;animation:fadeIn 0.2s ease;" data-message-id="' + m.id + '" oncontextmenu="showMessageMenu(event, ' + m.id + ', true)">' +
                '<div style="max-width:70%;">' +
                '<div style="' + bubbleSty + 'margin-top:' + marginTop + ';word-wrap:break-word;line-height:1.4;">' + replyPreviewHtml + contentHtml + '</div>';
            
            // Show time only for last message in group or single
            if (position === 'last' || position === 'single') {
                html += '<div style="text-align:right;margin-top:4px;font-size:0.65rem;color:rgba(255,255,255,0.5);padding-right:4px;">' + time + '</div>';
            }
            html += '</div></div>';
        } else {
            // Other's messages - left aligned with grouping
            var avatarInitial = (m.sender && m.sender.name) ? m.sender.name.charAt(0).toUpperCase() : '?';
            var bubbleSty = getBubbleStyle(false, position);
            var marginTop = position === 'first' || position === 'single' ? '12px' : '2px';
            
            html += '<div style="display:flex;justify-content:flex-start;padding:2px 12px;animation:fadeIn 0.2s ease;" data-message-id="' + m.id + '" oncontextmenu="showMessageMenu(event, ' + m.id + ', false)">' +
                '<div style="display:flex;align-items:flex-end;max-width:75%;">';
            
            // Show avatar only for first in group
            if (position === 'first' || position === 'single') {
                html += '<div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg, #0D5BD7, #00B4D8);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.8rem;margin-right:8px;flex-shrink:0;box-shadow:0 2px 4px rgba(0,0,0,0.1);">' + avatarInitial + '</div>';
            } else {
                html += '<div style="width:36px;flex-shrink:0;"></div>';
            }
            
            html += '<div style="max-width:calc(100% - 44px);">' +
                '<div style="' + bubbleSty + 'margin-top:' + marginTop + ';word-wrap:break-word;line-height:1.4;">';
            
            // Show name only for first in group
            if (position === 'first' || position === 'single') {
                html += '<div class="sender-name" style="font-size:0.7rem;color:#64748B;margin-bottom:4px;font-weight:600;">' + senderName + '</div>';
            }
            
            html += contentHtml + '</div>';
            
            // Show time only for last message in group
            if (position === 'last' || position === 'single') {
                html += '<div style="margin-top:4px;font-size:0.65rem;color:#9CA3AF;padding-left:4px;">' + time + '</div>';
            }
            
            html += '</div></div></div>';
        }
    }
    
    container.innerHTML = html;
    container.style.display = 'block';
    container.style.minHeight = '200px';
    container.style.padding = '10px';
    
    // Force scroll to bottom after render
    setTimeout(function() {
        container.scrollTop = container.scrollHeight;
        var chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }, 50);
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function handleGifError(img) {
    img.style.display = 'none';
    var placeholder = document.createElement('div');
    placeholder.innerHTML = '<div style="padding:20px;background:#f3f4f6;border-radius:8px;text-align:center;color:#9ca3af;font-size:0.8rem;">' +
        '<i class="fa fa-image" style="font-size:1.5rem;margin-bottom:4px;"></i><br>GIF indisponible</div>';
    img.parentNode.insertBefore(placeholder, img);
}

var activeMessageMenu = null;

function showMessageMenu(e, messageId, isMe) {
    e.preventDefault();
    
    if (activeMessageMenu) {
        activeMessageMenu.remove();
        activeMessageMenu = null;
    }
    
    var menu = document.createElement('div');
    menu.className = 'message-menu show';
    menu.id = 'messageMenu-' + messageId;
    menu.style.left = e.pageX + 'px';
    menu.style.top = e.pageY + 'px';
    
    var items = '';
    
    items += '<div class="message-menu-item" onclick="reactToMessage(' + messageId + ')"><i class="fa fa-smile-o"></i> Réaction</div>';
    items += '<div class="message-menu-item" onclick="replyToMessageById(' + messageId + ')"><i class="fa fa-reply"></i> Répondre</div>';
    
    if (isMe) {
        items += '<div class="message-menu-item delete" onclick="deleteMessage(' + messageId + ')"><i class="fa fa-trash"></i> Supprimer</div>';
    }
    
    menu.innerHTML = items;
    document.body.appendChild(menu);
    activeMessageMenu = menu;
    
    document.addEventListener('click', function closeMenu(e) {
        if (!menu.contains(e.target)) {
            menu.remove();
            activeMessageMenu = null;
            document.removeEventListener('click', closeMenu);
        }
    });
}

function deleteMessage(messageId) {
    if (!confirm('Voulez-vous supprimer ce message ?')) {
        return;
    }
    
    fetch('/api/messages/delete/' + messageId, {
        method: 'DELETE',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var msgEl = document.querySelector('.message[data-message-id="' + messageId + '"]');
            if (!msgEl) {
                msgEl = document.querySelector('[data-message-id="' + messageId + '"]');
            }
            if (msgEl) msgEl.remove();
            showToast('Message supprimé', 'success');
            
            var menu = document.getElementById('messageMenu-' + messageId);
            if (menu) menu.remove();
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    });
}

function reactToMessage(messageId) {
    var menu = document.getElementById('messageMenu-' + messageId);
    if (menu) menu.remove();
    
    var picker = document.getElementById('reactionPicker');
    if (!picker) {
        picker = document.createElement('div');
        picker.id = 'reactionPicker';
        picker.className = 'reaction-picker';
        picker.innerHTML = '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">👍</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">❤️</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">😂</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">😮</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">😢</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">😡</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">🙏</span>' +
            '<span class="reaction-emoji" onclick="addReaction(' + messageId + ', this)">👎</span>';
        document.body.appendChild(picker);
    }
    
    var msgEl = document.querySelector('[data-message-id="' + messageId + '"]');
    if (msgEl) {
        var rect = msgEl.getBoundingClientRect();
        picker.style.position = 'fixed';
        picker.style.left = rect.left + 'px';
        picker.style.top = (rect.bottom + 5) + 'px';
        picker.classList.add('show');
        picker.dataset.messageId = messageId;
    }
}

function addReaction(messageId, emojiEl) {
    var emoji = emojiEl.textContent;
    var picker = document.getElementById('reactionPicker');
    
    fetch('/api/messages/react/' + messageId, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ reaction: emoji })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            picker.classList.remove('show');
            var msgEl = document.querySelector('[data-message-id="' + messageId + '"]');
            if (msgEl) {
                var reactionsDiv = msgEl.querySelector('.message-reactions');
                if (!reactionsDiv) {
                    reactionsDiv = document.createElement('div');
                    reactionsDiv.className = 'message-reactions';
                    msgEl.appendChild(reactionsDiv);
                }
                var existingBadge = reactionsDiv.querySelector('[data-emoji="' + emoji + '"]');
                if (existingBadge) {
                    var count = parseInt(existingBadge.dataset.count || 1);
                    existingBadge.dataset.count = count + 1;
                    existingBadge.querySelector('.count').textContent = count + 1;
                } else {
                    var badge = document.createElement('span');
                    badge.className = 'reaction-badge';
                    badge.dataset.emoji = emoji;
                    badge.dataset.count = 1;
                    badge.innerHTML = emoji + '<span class="count">1</span>';
                    badge.onclick = function() { toggleMyReaction(messageId, emoji); };
                    reactionsDiv.appendChild(badge);
                }
            }
        }
    });
}

function toggleMyReaction(messageId, emoji) {
    fetch('/api/messages/unreact/' + messageId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            var msgEl = document.querySelector('[data-message-id="' + messageId + '"]');
            if (msgEl) {
                var badge = msgEl.querySelector('.reaction-badge[data-emoji="' + emoji + '"]');
                if (badge) {
                    var count = parseInt(badge.dataset.count || 1);
                    if (count <= 1) {
                        badge.remove();
                    } else {
                        badge.dataset.count = count - 1;
                        badge.querySelector('.count').textContent = count - 1;
                    }
                }
            }
        }
    });
}

function replyToMessageById(messageId) {
    var menu = document.getElementById('messageMenu-' + messageId);
    if (menu) menu.remove();
    
    var msgEl = document.querySelector('[data-message-id="' + messageId + '"]');
    if (!msgEl) return;
    
    var contentEl = msgEl.querySelector('.message-bubble, .message-audio, .message-gif, img');
    var content = contentEl ? contentEl.textContent || contentEl.src || 'Message' : 'Message';
    
    var senderEl = msgEl.querySelector('.sender-name');
    var senderName = senderEl ? senderEl.textContent : 'Message';
    
    window.currentReplyTo = messageId;
    var wrapper = document.getElementById('inputWrapper');
    
    var replyHTML = '<div class="reply-preview" id="replyPreview">' +
        '<span class="reply-sender">↪ ' + senderName + '</span>' +
        '<span class="reply-content">' + content.substring(0, 50) + '</span>' +
        '<button class="reply-cancel" onclick="cancelReply()">✕</button></div>';
    
    wrapper.insertAdjacentHTML('beforebegin', replyHTML);
    document.getElementById('messageInput').focus();
}

function playAudio(btn, src) {
    var audio = btn.nextElementSibling;
    if (audio.paused) {
        audio.play();
        btn.innerHTML = '<i class="fa fa-pause"></i>';
    } else {
        audio.pause();
        btn.innerHTML = '<i class="fa fa-play"></i>';
    }
    audio.onended = function() {
        btn.innerHTML = '<i class="fa fa-play"></i>';
    };
}

function toggleAudioPlayback(messageId, btn) {
    var audio = document.getElementById('audio-' + messageId);
    
    if (!audio || !audio.src) {
        return;
    }
    
    if (audio.paused) {
        audio.play().then(function() {
            btn.innerHTML = '<i class="fa fa-pause" style="color:#fff;font-size:14px;"></i>';
            
            // Progress loop
            var progressLoop = function() {
                if (!audio.paused && !audio.ended) {
                    var progress = document.getElementById('audio-progress-' + messageId);
                    var durationLabel = document.getElementById('audio-duration-' + messageId);
                    
                    try {
                        var current = audio.currentTime || 0;
                        var total = audio.duration || 0;
                        if (total > 0) {
                            var percent = (current / total) * 100;
                            if (progress) progress.style.width = percent + '%';
                            if (durationLabel) {
                                // Use a different function name to avoid conflict
                                var timeStr = formatAudioTime(current);
                                durationLabel.textContent = timeStr;
                            }
                        }
                        requestAnimationFrame(progressLoop);
                    } catch(e) {
                    }
                }
            };
            progressLoop();
        }).catch(function(err) {
        });
    } else {
        audio.pause();
        btn.innerHTML = '<i class="fa fa-play" style="color:#fff;font-size:14px;"></i>';
    }
    
    audio.onended = function() {
        btn.innerHTML = '<i class="fa fa-play" style="color:#fff;font-size:14px;"></i>';
        var progress = document.getElementById('audio-progress-' + messageId);
        var durationLabel = document.getElementById('audio-duration-' + messageId);
        if (progress) progress.style.width = '0%';
        if (durationLabel) durationLabel.textContent = formatAudioTime(audio.duration);
    };
    
    audio.onerror = function(e) {
    };
}

function formatAudioTime(seconds) {
    if (!seconds || isNaN(seconds)) return '0:00';
    var mins = Math.floor(seconds / 60);
    var secs = Math.floor(seconds % 60);
    return mins + ':' + (secs < 10 ? '0' : '') + secs;
}

function updateAudioProgress(audio, progress, durationLabel) {
    if (!audio) {
        return;
    }
    
    if (audio.paused) {
        return;
    }
    
    try {
        var currentTime = audio.currentTime || 0;
        var duration = audio.duration || 0;
        
        if (!duration || isNaN(duration)) {
            return;
        }
        
        var percent = (currentTime / duration) * 100;
        
        if (progress) {
            progress.style.width = percent + '%';
        }
        if (durationLabel) {
            durationLabel.textContent = formatAudioTime(currentTime);
        }
        
        if (!audio.paused && !audio.ended) {
            requestAnimationFrame(function() { 
                updateAudioProgress(audio, progress, durationLabel); 
            });
        }
    } catch(e) {
    }
}

function formatTime(seconds) {
    if (!seconds || isNaN(seconds)) return '0:00';
    var mins = Math.floor(seconds / 60);
    var secs = Math.floor(seconds % 60);
    return mins + ':' + (secs < 10 ? '0' : '') + secs;
}

function audioLoaded(messageId) {
    var audio = document.getElementById('audio-' + messageId);
    var durationLabel = document.getElementById('audio-duration-' + messageId);
    if (audio && durationLabel && audio.duration && !isNaN(audio.duration)) {
        durationLabel.textContent = formatAudioTime(audio.duration);
    } else if (audio) {
        audio.load();
    }
}

function seekAudio(messageId, event) {
    var audio = document.getElementById('audio-' + messageId);
    if (!audio) return;
    
    var progressBar = event.currentTarget;
    var rect = progressBar.getBoundingClientRect();
    var percent = (event.clientX - rect.left) / rect.width;
    audio.currentTime = percent * audio.duration;
}

// Reply functions
window.currentReplyTo = null;

function replyToMessage(messageId, senderName, content) {
    window.currentReplyTo = messageId;
    var input = document.getElementById('messageInput');
    var wrapper = document.getElementById('inputWrapper');
    
    var replyHTML = '<div class="reply-preview" id="replyPreview">' +
        '<span class="reply-sender">↪ ' + escapeHtml(senderName) + '</span>' +
        '<span class="reply-content">' + escapeHtml(content.substring(0, 50)) + (content.length > 50 ? '...' : '') + '</span>' +
        '<button class="reply-cancel" onclick="cancelReply()">✕</button>' +
    '</div>';
    
    wrapper.insertAdjacentHTML('beforebegin', replyHTML);
    input.focus();
}

function cancelReply() {
    window.currentReplyTo = null;
    var preview = document.getElementById('replyPreview');
    if (preview) preview.remove();
}

// Reaction functions
function addReaction(messageId, emoji) {
    fetch('/api/messages/' + messageId + '/react', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'emoji=' + encodeURIComponent(emoji)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            loadReactionsForMessage(messageId);
        }
    })
    .catch(function(err) {
        console.error('Error adding reaction:', err);
    });
}

function loadReactionsForMessage(messageId) {
    fetch('/api/messages/' + messageId + '/reactions', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var container = document.getElementById('reactions-' + messageId);
        if (!container) return;
        
        if (data.reactions && data.reactions.length > 0) {
            var html = '';
            data.reactions.forEach(function(r) {
                html += '<span class="reaction-badge" onclick="addReaction(' + messageId + ', \'' + r.emoji + '\')">' +
                    r.emoji + ' <span class="count">' + r.count + '</span></span>';
            });
            container.innerHTML = html;
            container.style.display = 'flex';
        } else {
            container.innerHTML = '';
            container.style.display = 'none';
        }
    })
    .catch(function(err) {
        console.error('Error loading reactions:', err);
    });
}

function showReactionPicker(messageId, event) {
    event.stopPropagation();
    var picker = document.getElementById('picker-' + messageId);
    var allPickers = document.querySelectorAll('.reaction-picker');
    allPickers.forEach(function(p) { p.classList.remove('show'); });
    if (picker) picker.classList.add('show');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.reaction-picker') && !e.target.closest('.message-action-btn')) {
        var allPickers = document.querySelectorAll('.reaction-picker');
        allPickers.forEach(function(p) { p.classList.remove('show'); });
    }
});

function sendMessage(forumPostId) {
    var input = document.getElementById('messageInput');
    var content = input.value.trim();
    var sharedPostId = forumPostId || null;
    var replyToId = window.currentReplyTo || null;
    
    if (!content && !sharedPostId) {
        showToast('Tapez un message', 'error');
        return;
    }
    if (!currentConversationId) {
        showToast('Aucune conversation sélectionnée', 'error');
        return;
    }
    
    // Clear reply
    if (replyToId) {
        cancelReply();
    }
    
    var displayContent = content || (sharedPostId ? 'Forum partagé' : '');
    var container = document.getElementById('messagesContainer');
    var tempMsg = document.createElement('div');
    tempMsg.className = 'message sent sending-animation';
    tempMsg.innerHTML = 
        '<div class="message-avatar" style="background:linear-gradient(135deg, var(--fg-accent), #EA580C);"></div>' +
        '<div><div class="message-bubble" style="background:linear-gradient(135deg, var(--fg-primary), var(--fg-primary-dark)); color:white;">' + 
        '<span style="animation:sending 0.5s infinite">' + displayContent + '</span></div></div>';
    container.appendChild(tempMsg);
    container.scrollTop = container.scrollHeight;
    
    var formData = new FormData();
    formData.append('content', content);
    if (sharedPostId) {
        formData.append('forum_post_id', sharedPostId);
    }
    if (replyToId) {
        formData.append('reply_to', replyToId);
    }
    input.value = '';
    
    var sendBtn = document.querySelector('.send-btn');
    if (sendBtn) sendBtn.disabled = true;
    
    fetch('/api/messages/conversation/' + currentConversationId + '/messages', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            var name = document.getElementById('chatName').textContent;
            openConversation(currentConversationId, name);
        } else {
            tempMsg.remove();
            showToast(data.error || 'Erreur lors de l\'envoi', 'error');
            input.value = content;
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        tempMsg.remove();
        showToast('Erreur de connexion', 'error');
        input.value = content;
    })
    .finally(function() {
        if (sendBtn) sendBtn.disabled = false;
    });
}

function showToast(message, type) {
    var toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);padding:12px 24px;background:' + (type === 'error' ? '#dc3545' : '#28a745') + ';color:white;border-radius:8px;z-index:9999;font-size:14px;animation:fadeIn 0.3s';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.style.animation = 'fadeOut 0.3s';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

function handleEnter(e) { if (e.key === 'Enter') sendMessage(null); }

function formatTime(dateStr) {
    if (!dateStr) return '';
    var d = new Date(dateStr);
    var now = new Date();
    if (now - d < 86400000) return d.toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
    return d.toLocaleDateString('fr-FR', {day:'numeric', month:'short'});
}

function filterConversations() {
    var query = document.getElementById('conversationSearch').value.toLowerCase();
    var filtered = [];
    for (var i = 0; i < conversations.length; i++) {
        var name = conversations[i].type === 'group' ? conversations[i].name : (conversations[i].otherUser ? conversations[i].otherUser.name : '');
        if (name.toLowerCase().indexOf(query) !== -1) filtered.push(conversations[i]);
    }
    renderConversations(filtered);
}

function showNewChatModal() { document.getElementById('newChatModal').classList.add('show'); }
function closeNewChatModal() { 
    document.getElementById('newChatModal').classList.remove('show');
    document.getElementById('userSearchResults').innerHTML = '';
    document.getElementById('userSearchInput').value = '';
}
function showGroupModal() {
    document.getElementById('newChatModal').classList.remove('show');
    document.getElementById('groupModal').classList.add('show');
    document.getElementById('groupNameInput').value = '';
    document.getElementById('groupUserSearch').value = '';
    document.getElementById('groupMembersList').innerHTML = '';
    document.getElementById('groupUserResults').innerHTML = '';
    selectedGroupMembers = [];
}
function closeGroupModal() { document.getElementById('groupModal').classList.remove('show'); }

function searchUsers(query, type) {
    if (query.length < 2) return;
    
    fetch('/api/messages/users?q=' + encodeURIComponent(query), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        var results = type === 'group' ? document.getElementById('groupUserResults') : document.getElementById('userSearchResults');
        if (!data.users || data.users.length === 0) {
            results.innerHTML = '<div style="padding:1rem;text-align:center;color:#64748B"><i class="fa fa-search" style="font-size:1.5rem;margin-bottom:0.5rem;display:block"></i>Aucun utilisateur trouvé<br><small>Essayez un autre nom</small></div>';
            return;
        }
        var html = '';
        for (var i = 0; i < data.users.length; i++) {
            var u = data.users[i];
            var initials = '';
            var nameParts = u.name.split(' ');
            for (var p = 0; p < nameParts.length; p++) {
                if (nameParts[p].charAt(0)) initials += nameParts[p].charAt(0).toUpperCase();
                if (initials.length >= 2) break;
            }
            var avatarHtml = u.avatar 
                ? '<img src="' + u.avatar + '" alt="' + u.name + '">' 
                : '<span>' + initials + '</span>';
            
            if (type === 'group') {
                var alreadyAdded = false;
                for (var j = 0; j < selectedGroupMembers.length; j++) {
                    if (selectedGroupMembers[j].id === u.id) { alreadyAdded = true; break; }
                }
                if (!alreadyAdded) {
                    html += '<div class="user-result-item" onclick="addGroupMember({id:' + u.id + ',name:\'' + u.name.replace(/'/g, "\\'") + '\'})">';
                    html += '<div class="conv-avatar" style="width:36px;height:36px;font-size:0.85rem">' + avatarHtml + '</div>';
                    html += '<span style="flex:1">' + u.name + '</span>';
                    html += '<button style="background:var(--fg-primary);color:white;border:none;padding:4px 12px;border-radius:15px;font-size:0.8rem;cursor:pointer"><i class="fa fa-plus"></i> Ajouter</button></div>';
                }
            } else {
                html += '<div class="user-result-item" onclick="startConversationWith(' + u.id + ',\'' + u.name.replace(/'/g, "\\'") + '\')" style="padding:0.75rem;border-radius:10px;transition:background 0.2s;cursor:pointer">';
                html += '<div class="conv-avatar" style="width:42px;height:42px;font-size:1rem">' + avatarHtml + '</div>';
                html += '<div style="flex:1;margin-left:0.75rem"><strong style="color:var(--fg-text);font-size:0.95rem">' + u.name + '</strong></div>';
                html += '<i class="fa fa-comment" style="color:var(--fg-primary);font-size:1rem"></i></div>';
            }
        }
        results.innerHTML = html;
    })
    .catch(function(error) {
        console.error('Search error:', error);
        var results = type === 'group' ? document.getElementById('groupUserResults') : document.getElementById('userSearchResults');
        results.innerHTML = '<p style="padding:0.5rem;color:#dc3545">Erreur lors de la recherche</p>';
    });
}

function startConversationWith(userId, userName) {
    closeNewChatModal();
    
    document.getElementById('emptyChatState').style.display = 'none';
    document.getElementById('chatHeader').classList.add('active');
    document.getElementById('chatInputArea').classList.add('active');
    document.getElementById('chatName').textContent = userName;
    document.getElementById('chatAvatar').textContent = userName.charAt(0).toUpperCase();
    document.getElementById('chatAvatar').innerHTML = '<div class="loader-avatar" style="animation:none;background:linear-gradient(135deg, var(--fg-primary), var(--fg-accent));"></div>';
    
    document.getElementById('messagesContainer').innerHTML = 
        '<div class="loader-container">' +
            '<div class="loader-card">' +
                '<div class="loader-ring"></div>' +
                '<div class="loader-dots"><span></span><span></span><span></span></div>' +
                '<p class="loader-text">Création avec ' + userName + '...</p>' +
            '</div>' +
        '</div>';
    
    fetch('/api/messages/start/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        if (data && data.conversation) {
            currentConversationId = data.conversation.id;
            if (conversationsLoaded) {
                loadConversations(true);
            }
            
            document.getElementById('messagesContainer').innerHTML = 
                '<div class="loader-container">' +
                    '<div class="loader-card">' +
                        '<div class="loader-ring"></div>' +
                        '<div class="loader-dots"><span></span><span></span><span></span></div>' +
                        '<p class="loader-text">Chargement des messages...</p>' +
                    '</div>' +
                '</div>';
            
            fetch('/api/messages/conversation/' + data.conversation.id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(resp) { return resp.json(); })
            .then(function(convData) {
                if (convData.messages !== undefined) {
                    renderMessages(convData.messages || []);
                }
            });
        } else {
            document.getElementById('messagesContainer').innerHTML = 
                '<div class="loader-container">' +
                    '<div class="loader-card" style="text-align:center">' +
                        '<i class="fa fa-exclamation-circle" style="font-size:3rem;color:#dc3545;margin-bottom:1rem"></i>' +
                        '<p class="loader-text" style="color:#dc3545">Erreur lors de la création</p>' +
                    '</div>' +
                '</div>';
        }
    })
    .catch(function(error) {
        console.error('Error starting conversation:', error);
        document.getElementById('messagesContainer').innerHTML = 
            '<div class="loader-container">' +
                '<div class="loader-card" style="text-align:center">' +
                    '<i class="fa fa-wifi" style="font-size:3rem;color:#dc3545;margin-bottom:1rem"></i>' +
                    '<p class="loader-text" style="color:#dc3545">Erreur de connexion au serveur</p>' +
                    '<button onclick="startConversationWith(' + userId + ',\'' + userName + '\')" style="margin-top:1rem;padding:0.5rem 1.5rem;background:var(--fg-primary);color:white;border:none;border-radius:20px;cursor:pointer">' +
                        '<i class="fa fa-refresh"></i> Réessayer' +
                    '</button>' +
                '</div>' +
            '</div>';
    });
}

function addGroupMember(user) {
    var alreadyAdded = false;
    for (var i = 0; i < selectedGroupMembers.length; i++) {
        if (selectedGroupMembers[i].id === user.id) { alreadyAdded = true; break; }
    }
    if (!alreadyAdded) {
        selectedGroupMembers.push(user);
        renderGroupMembers();
    }
    document.getElementById('groupUserSearch').value = '';
    document.getElementById('groupUserResults').innerHTML = '';
}

function removeGroupMember(userId) {
    var newMembers = [];
    for (var i = 0; i < selectedGroupMembers.length; i++) {
        if (selectedGroupMembers[i].id !== userId) newMembers.push(selectedGroupMembers[i]);
    }
    selectedGroupMembers = newMembers;
    renderGroupMembers();
}

function renderGroupMembers() {
    var list = document.getElementById('groupMembersList');
    var html = '';
    for (var i = 0; i < selectedGroupMembers.length; i++) {
        var m = selectedGroupMembers[i];
        html += '<span style="display:inline-flex;align-items:center;padding:4px 12px;background:rgba(59,130,246,0.1);border-radius:20px;margin:4px;font-size:0.85rem;color:var(--fg-primary)">' + m.name + ' <i onclick="removeGroupMember(' + m.id + ')" style="margin-left:8px;cursor:pointer;color:var(--fg-primary)">×</i></span>';
    }
    list.innerHTML = html;
}

function createGroup() {
    var name = document.getElementById('groupNameInput').value.trim();
    if (!name) { alert('Entrez un nom de groupe'); return; }
    if (selectedGroupMembers.length === 0) { alert('Ajoutez au moins un membre'); return; }
    
    var members = [];
    for (var i = 0; i < selectedGroupMembers.length; i++) members.push(selectedGroupMembers[i].id);
    
    var formData = new FormData();
    formData.append('name', name);
    formData.append('members', members.join(','));
    
    fetch('/api/groups/create', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.conversation) {
            closeGroupModal();
            if (conversationsLoaded) {
                loadConversations(true);
            }
            openConversation(data.conversation.id, name);
        }
    });
}

function addEmoji(emoji) {
    var input = document.getElementById('messageInput');
    if (input) { 
        var start = input.selectionStart;
        var end = input.selectionEnd;
        var text = input.value;
        input.value = text.substring(0, start) + emoji + text.substring(end);
        input.setSelectionRange(start + emoji.length, start + emoji.length);
        input.focus();
    }
    document.getElementById('emojiPicker').style.display = 'none';
}

function toggleEmojiPicker() {
    var p = document.getElementById('emojiPicker');
    var g = document.getElementById('gifPicker');
    var currentDisplay = p.style.display;
    p.style.display = currentDisplay === 'block' ? 'none' : 'block';
    if (p.style.display === 'block') {
        g.style.display = 'none';
    }
}

function showEmojiCategory(category) {
    var tabs = document.querySelectorAll('.emoji-tab');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    event.target.classList.add('active');
    
    var content = document.getElementById('emojiContent');
    var html = '';
    
    if (category === 'smileys') {
        html = '<div class="emoji-category">Smileys & Émotions</div><div class="emoji-grid">' +
            '<button class="emoji-btn" onclick="addEmoji(\'😀\')">😀</button><button class="emoji-btn" onclick="addEmoji(\'😃\')">😃</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😄\')">😄</button><button class="emoji-btn" onclick="addEmoji(\'😁\')">😁</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😅\')">😅</button><button class="emoji-btn" onclick="addEmoji(\'😂\')">😂</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤣\')">🤣</button><button class="emoji-btn" onclick="addEmoji(\'😊\')">😊</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😇\')">😇</button><button class="emoji-btn" onclick="addEmoji(\'😍\')">😍</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🥰\')">🥰</button><button class="emoji-btn" onclick="addEmoji(\'😘\')">😘</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😎\')">😎</button><button class="emoji-btn" onclick="addEmoji(\'🥳\')">🥳</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤔\')">🤔</button><button class="emoji-btn" onclick="addEmoji(\'😴\')">😴</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤒\')">🤒</button><button class="emoji-btn" onclick="addEmoji(\'😷\')">😷</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤯\')">🤯</button><button class="emoji-btn" onclick="addEmoji(\'🤠\')">🤠</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🥺\')">🥺</button><button class="emoji-btn" onclick="addEmoji(\'😢\')">😢</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😭\')">😭</button><button class="emoji-btn" onclick="addEmoji(\'😤\')">😤</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'😡\')">😡</button><button class="emoji-btn" onclick="addEmoji(\'💀\')">💀</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'👻\')">👻</button><button class="emoji-btn" onclick="addEmoji(\'💩\')">💩</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤡\')">🤡</button></div>';
    } else if (category === 'gestures') {
        html = '<div class="emoji-category">Gestes & Mains</div><div class="emoji-grid">' +
            '<button class="emoji-btn" onclick="addEmoji(\'👍\')">👍</button><button class="emoji-btn" onclick="addEmoji(\'👎\')">👎</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'👏\')">👏</button><button class="emoji-btn" onclick="addEmoji(\'🙌\')">🙌</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤝\')">🤝</button><button class="emoji-btn" onclick="addEmoji(\'🙏\')">🙏</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💪\')">💪</button><button class="emoji-btn" onclick="addEmoji(\'🫶\')">🫶</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'✋\')">✋</button><button class="emoji-btn" onclick="addEmoji(\'🖐️\')">🖐️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'👌\')">👌</button><button class="emoji-btn" onclick="addEmoji(\'🤌\')">🤌</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤏\')">🤏</button><button class="emoji-btn" onclick="addEmoji(\'✌️\')">✌️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤞\')">🤞</button><button class="emoji-btn" onclick="addEmoji(\'🤟\')">🤟</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🤘\')">🤘</button><button class="emoji-btn" onclick="addEmoji(\'🤙\')">🤙</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'👈\')">👈</button><button class="emoji-btn" onclick="addEmoji(\'👉\')">👉</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'👆\')">👆</button><button class="emoji-btn" onclick="addEmoji(\'👇\')">👇</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'☝️\')">☝️</button><button class="emoji-btn" onclick="addEmoji(\'👋\')">👋</button></div>';
    } else if (category === 'travel') {
        html = '<div class="emoji-category">Voyages & Lieux</div><div class="emoji-grid">' +
            '<button class="emoji-btn" onclick="addEmoji(\'✈️\')">✈️</button><button class="emoji-btn" onclick="addEmoji(\'🛫\')">🛫</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🛬\')">🛬</button><button class="emoji-btn" onclick="addEmoji(\'🛩️\')">🛩️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🚀\')">🚀</button><button class="emoji-btn" onclick="addEmoji(\'🛶\')">🛶</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'⛵\')">⛵</button><button class="emoji-btn" onclick="addEmoji(\'🚢\')">🚢</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🚗\')">🚗</button><button class="emoji-btn" onclick="addEmoji(\'🚕\')">🚕</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🚌\')">🚌</button><button class="emoji-btn" onclick="addEmoji(\'🚎\')">🚎</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🏖️\')">🏖️</button><button class="emoji-btn" onclick="addEmoji(\'🏝️\')">🏝️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🏔️\')">🏔️</button><button class="emoji-btn" onclick="addEmoji(\'⛰️\')">⛰️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🏕️\')">🏕️</button><button class="emoji-btn" onclick="addEmoji(\'🏠\')">🏠</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🏰\')">🏰</button><button class="emoji-btn" onclick="addEmoji(\'🗼\')">🗼</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🗽\')">🗽</button><button class="emoji-btn" onclick="addEmoji(\'🎡\')">🎡</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🌍\')">🌍</button><button class="emoji-btn" onclick="addEmoji(\'🌎\')">🌎</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🌏\')">🌏</button></div>';
    } else if (category === 'objects') {
        html = '<div class="emoji-category">Objets & Symboles</div><div class="emoji-grid">' +
            '<button class="emoji-btn" onclick="addEmoji(\'📱\')">📱</button><button class="emoji-btn" onclick="addEmoji(\'💻\')">💻</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'📷\')">📷</button><button class="emoji-btn" onclick="addEmoji(\'📹\')">📹</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🎮\')">🎮</button><button class="emoji-btn" onclick="addEmoji(\'🎲\')">🎲</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🎸\')">🎸</button><button class="emoji-btn" onclick="addEmoji(\'🎹\')">🎹</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🎤\')">🎤</button><button class="emoji-btn" onclick="addEmoji(\'🎧\')">🎧</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'📚\')">📚</button><button class="emoji-btn" onclick="addEmoji(\'📖\')">📖</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'✏️\')">✏️</button><button class="emoji-btn" onclick="addEmoji(\'📝\')">📝</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💰\')">💰</button><button class="emoji-btn" onclick="addEmoji(\'💳\')">💳</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🔑\')">🔑</button><button class="emoji-btn" onclick="addEmoji(\'🛍️\')">🛍️</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🎁\')">🎁</button><button class="emoji-btn" onclick="addEmoji(\'🎀\')">🎀</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'❤️\')">❤️</button><button class="emoji-btn" onclick="addEmoji(\'🧡\')">🧡</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💛\')">💛</button><button class="emoji-btn" onclick="addEmoji(\'💚\')">💚</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💙\')">💙</button><button class="emoji-btn" onclick="addEmoji(\'💜\')">💜</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🖤\')">🖤</button><button class="emoji-btn" onclick="addEmoji(\'🤍\')">🤍</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💔\')">💔</button><button class="emoji-btn" onclick="addEmoji(\'💯\')">💯</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🔥\')">🔥</button><button class="emoji-btn" onclick="addEmoji(\'✨\')">✨</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'⭐\')">⭐</button><button class="emoji-btn" onclick="addEmoji(\'🌟\')">🌟</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'🎉\')">🎉</button><button class="emoji-btn" onclick="addEmoji(\'🎊\')">🎊</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'✅\')">✅</button><button class="emoji-btn" onclick="addEmoji(\'❌\')">❌</button>' +
            '<button class="emoji-btn" onclick="addEmoji(\'💬\')">💬</button><button class="emoji-btn" onclick="addEmoji(\'🔔\')">🔔</button></div>';
    }
    
    content.innerHTML = html;
}

function toggleGifPicker() {
    var g = document.getElementById('gifPicker');
    var p = document.getElementById('emojiPicker');
    if (g.style.display === 'block') {
        g.style.display = 'none';
    } else {
        g.style.display = 'block';
        p.style.display = 'none';
        loadTrendingGifs();
    }
}

function loadTrendingGifs() {
    var grid = document.getElementById('gifGrid');
    grid.innerHTML = '<div class="gif-loading"><i class="fa fa-spinner fa-spin"></i> Chargement...</div>';
    
    // Use Tenor API for trending
    var apiUrl = 'https://tenor.googleapis.com/v2/featured?key=AIzaSyAyimkuYQYF_FXVALexPuGQctUWRURdCYQ&limit=20&media_filter=gif';
    
    fetch(apiUrl)
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.results && data.results.length > 0) {
            var html = '';
            data.results.forEach(function(gif) {
                var gifUrl = gif.media_formats.gif.url;
                html += '<div class="gif-item" onclick="sendGif(\'' + gifUrl.replace(/'/g, "\\'") + '\')">' +
                    '<img src="' + gifUrl + '" style="width:100%;height:100%;object-fit:contain;border-radius:8px;" onerror="this.parentElement.remove()"></div>';
            });
            grid.innerHTML = html || '<div class="gif-loading">Aucun GIF disponible</div>';
        } else {
            grid.innerHTML = '<div class="gif-loading">Aucun GIF disponible</div>';
        }
    })
    .catch(function(err) {
        console.error('GIF load error:', err);
        grid.innerHTML = '<div class="gif-loading">Erreur de chargement</div>';
    });
}

function searchGifs(query) {
    var grid = document.getElementById('gifGrid');
    grid.innerHTML = '<div class="gif-loading"><i class="fa fa-spinner fa-spin"></i> Recherche...</div>';
    
    // Use Tenor API (free, no key needed for basic usage)
    var apiUrl = 'https://tenor.googleapis.com/v2/search?q=' + encodeURIComponent(query) + '&key=AIzaSyAyimkuYQYF_FXVALexPuGQctUWRURdCYQ&limit=20&media_filter=gif';
    
    fetch(apiUrl)
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.results && data.results.length > 0) {
            var html = '';
            data.results.forEach(function(gif) {
                var gifUrl = gif.media_formats.gif.url;
                html += '<div class="gif-item" onclick="sendGif(\'' + gifUrl.replace(/'/g, "\\'") + '\')">' +
                    '<img src="' + gifUrl + '" style="width:100%;height:100%;object-fit:contain;border-radius:8px;" onerror="this.parentElement.remove()"></div>';
            });
            grid.innerHTML = html || '<div class="gif-loading">Aucun GIF trouvé</div>';
        } else {
            grid.innerHTML = '<div class="gif-loading">Aucun résultat pour "' + query + '"</div>';
        }
    })
    .catch(function(err) {
        console.error('GIF search error:', err);
        grid.innerHTML = '<div class="gif-loading">Erreur de recherche</div>';
    });
}

function sendGif(url) {
    if (!currentConversationId) { alert('Sélectionnez une conversation'); return; }
    
    var formData = new FormData();
    formData.append('gif_url', url);
    formData.append('conversation_id', currentConversationId);
    
    fetch('/api/messages/send-gif', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('gifPicker').style.display = 'none';
            openConversation(currentConversationId, document.getElementById('chatName').textContent);
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
    });
}

document.getElementById('gifSearchInput').addEventListener('input', function() {
    var query = this.value;
    if (query.length >= 2) {
        searchGifs(query);
    } else {
        loadTrendingGifs();
    }
});

function sendImageMessage() {
    var input = document.getElementById('imageInput');
    var file = input.files[0];
    if (!file || !currentConversationId) return;
    
    var formData = new FormData();
    formData.append('image', file);
    formData.append('conversation_id', currentConversationId);
    
    fetch('/api/messages/upload-image', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            input.value = '';
            openConversation(currentConversationId, document.getElementById('chatName').textContent);
        }
    });
}

function startVoiceRecognition() {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert('Reconnaissance vocale non supportée');
        return;
    }
    document.getElementById('voiceModal').style.display = 'flex';
    document.getElementById('voiceStatus').textContent = 'Écoute en cours...';
    
    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    var recognition = new SpeechRecognition();
    recognition.lang = 'fr-FR';
    
    recognition.onresult = function(event) {
        var transcript = event.results[0][0].transcript;
        document.getElementById('messageInput').value = transcript;
    };
    
    recognition.onend = function() {
        document.getElementById('voiceModal').style.display = 'none';
    };
    
    recognition.start();
}

function stopVoiceRecognition() {
    document.getElementById('voiceModal').style.display = 'none';
}

// Voice Recording for Audio Messages
var mediaRecorder = null;
var audioChunks = [];
var recordingInterval = null;
var recordingSeconds = 0;
var isRecording = false;

function toggleVoiceRecording() {
    if (isRecording) {
        stopVoiceRecording();
    } else {
        startVoiceRecording();
    }
}

function startVoiceRecording() {
    if (!currentConversationId) {
        alert('Sélectionnez une conversation');
        return;
    }
    
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Enregistrement audio non supporté par votre navigateur');
        return;
    }
    
    navigator.mediaDevices.getUserMedia({ audio: true })
    .then(function(stream) {
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        
        mediaRecorder.ondataavailable = function(event) {
            if (event.data && event.data.size > 0) {
                audioChunks.push(event.data);
            }
        };
        
        mediaRecorder.onstop = function() {
            if (audioChunks.length === 0) {
                console.error('No audio data recorded');
                stream.getTracks().forEach(function(track) { track.stop(); });
                return;
            }
            
            var audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            
            if (audioBlob.size < 1000) {
                alert('Audio trop court ou problème d\'enregistrement');
                stream.getTracks().forEach(function(track) { track.stop(); });
                return;
            }
            
            sendVoiceMessage(audioBlob);
            
            stream.getTracks().forEach(function(track) {
                track.stop();
            });
        };
        
        mediaRecorder.start(100);
        isRecording = true;
        
        // Update UI
        var btn = document.getElementById('voiceRecordBtn');
        var panel = document.getElementById('voiceRecordingPanel');
        var inputWrapper = document.getElementById('inputWrapper');
        var sendBtn = document.querySelector('.send-btn');
        
        btn.classList.add('recording');
        panel.classList.add('active');
        if (inputWrapper) inputWrapper.style.display = 'none';
        if (sendBtn) sendBtn.style.display = 'none';
        
        // Start timer
        recordingSeconds = 0;
        recordingInterval = setInterval(function() {
            recordingSeconds++;
            var mins = Math.floor(recordingSeconds / 60);
            var secs = recordingSeconds % 60;
            document.getElementById('recordingTime').textContent = mins + ':' + (secs < 10 ? '0' : '') + secs;
        }, 1000);
    })
    .catch(function(err) {
        console.error('Error accessing microphone:', err);
        alert('Impossible d\'accéder au microphone');
    });
}

function stopVoiceRecording() {
    if (mediaRecorder && isRecording) {
        mediaRecorder.stop();
        isRecording = false;
        
        // Reset UI
        var btn = document.getElementById('voiceRecordBtn');
        var panel = document.getElementById('voiceRecordingPanel');
        var inputWrapper = document.getElementById('inputWrapper');
        var sendBtn = document.querySelector('.send-btn');
        
        btn.classList.remove('recording');
        panel.classList.remove('active');
        if (inputWrapper) inputWrapper.style.display = 'flex';
        if (sendBtn) sendBtn.style.display = 'flex';
        
        clearInterval(recordingInterval);
        recordingSeconds = 0;
    }
}

function sendVoiceMessage(audioBlob) {
    var formData = new FormData();
    formData.append('audio', audioBlob, 'voice_message.webm');
    formData.append('conversation_id', currentConversationId);
    
    fetch('/api/messages/upload-audio', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            openConversation(currentConversationId, document.getElementById('chatName').textContent);
        } else {
            alert('Erreur lors de l\'envoi du message vocal');
        }
    })
    .catch(function(error) {
        console.error('Error sending voice message:', error);
        alert('Erreur lors de l\'envoi du message vocal');
    });
}

document.addEventListener('click', function(e) {
    var p = document.getElementById('emojiPicker');
    var g = document.getElementById('gifPicker');
    if (p && p.style.display === 'block' && !e.target.closest('#emojiPicker') && !e.target.closest('[onclick*="toggleEmojiPicker"]')) {
        p.style.display = 'none';
    }
    if (g && g.style.display === 'block' && !e.target.closest('#gifPicker') && !e.target.closest('[onclick*="toggleGifPicker"]')) {
        g.style.display = 'none';
    }
});

document.getElementById('messageInput').addEventListener('input', function() {
    if (!currentConversationId) return;
    fetch('/api/messages/conversation/' + currentConversationId + '/typing', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
});

setInterval(function() {
    if (currentConversationId && !conversationsLoading) {
        fetch('/api/messages/conversation/' + currentConversationId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.messages) renderMessages(data.messages);
        })
        .catch(function() {});
    }
}, 10000);

setInterval(function() {
    if (conversationsLoaded && !conversationsLoading && conversations.length > 0) {
        loadConversations(true);
    }
}, 60000);

// ============= AUDIO CALL FUNCTIONS =============

function initiateCall(type) {
    if (!currentConversationId) { 
        showToast('Sélectionnez une conversation', 'error'); 
        return; 
    }
    
    if (type === 'video') {
        initiateVideoCall();
        return;
    }
    
    var userName = document.getElementById('chatName').textContent;
    var conversation = conversations.find(function(c) {
        return c.id === currentConversationId;
    });
    
    currentCallUserId = conversation && conversation.otherUser ? conversation.otherUser.id : null;
    currentCallUserName = userName;
    
    if (!currentCallUserId) {
        showToast('Impossible d\'initier l\'appel', 'error');
        return;
    }
    
    fetch('/api/messages/call/start/' + currentCallUserId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            startCall(data.call);
        } else {
            startCall({ id: 'local_call' });
        }
    })
    .catch(function(error) {
        console.error('Call error:', error);
        startCall({ id: 'local_call' });
    });
}

function startCall(callData) {
    callStatus = 'connecting';
    
    var overlay = document.getElementById('callOverlay');
    var avatar = document.getElementById('callAvatar');
    var nameEl = document.getElementById('callName');
    var statusEl = document.getElementById('callStatus');
    var wave = document.getElementById('callWave');
    var timer = document.getElementById('callTimer');
    var muteBtn = document.getElementById('muteBtn');
    var speakerBtn = document.getElementById('speakerBtn');
    
    avatar.textContent = currentCallUserName.charAt(0).toUpperCase();
    nameEl.textContent = currentCallUserName;
    statusEl.textContent = 'Connexion...';
    statusEl.className = 'call-status connecting';
    wave.style.display = 'flex';
    timer.style.display = 'none';
    timer.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    
    muteBtn.disabled = true;
    speakerBtn.disabled = true;
    muteBtn.className = 'call-btn mute disabled';
    speakerBtn.className = 'call-btn speaker disabled';
    
    overlay.classList.add('active');
    
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function(stream) {
            localStream = stream;
            
            createPeerConnection();
            
            localStream.getTracks().forEach(function(track) {
                peerConnection.addTrack(track, localStream);
            });
            
            peerConnection.createOffer()
                .then(function(offer) {
                    return peerConnection.setLocalDescription(offer);
                })
                .then(function() {
                    callTimeout = setTimeout(function() {
                        if (callStatus === 'connecting') {
                            callStatus = 'ringing';
                            statusEl.innerHTML = '<i class="fa fa-bell fa-spin"></i> Sonnerie en cours...';
                            statusEl.className = 'call-status ringing';
                            
                            callTimeout = setTimeout(function() {
                                if (callStatus === 'ringing' || callStatus === 'connecting') {
                                    callStatus = 'no_answer';
                                    statusEl.innerHTML = '<i class="fa fa-phone-slash"></i> Aucune réponse';
                                    statusEl.className = 'call-status failed';
                                    timer.style.display = 'block';
                                    timer.innerHTML = currentCallUserName + ' ne répond pas';
                                    
                                    showToast(currentCallUserName + ' ne répond pas', 'info');
                                    
                                    setTimeout(function() {
                                        if (callStatus === 'no_answer') {
                                            endCall();
                                        }
                                    }, 5000);
                                }
                            }, 30000);
                        }
                    }, 2000);
                })
                .catch(function(error) {
                    console.error('Error creating offer:', error);
                    setCallStatus('failed');
                });
        })
        .catch(function(error) {
            console.error('Error accessing microphone:', error);
            setCallStatus('failed', 'Accès microphone refusé');
            showToast('Impossible d\'accéder au microphone. Vérifiez les permissions.', 'error');
        });
}

function createPeerConnection() {
    var config = {
        iceServers: [
            { urls: 'stun:stun.l.google.com:19302' },
            { urls: 'stun:stun1.l.google.com:19302' }
        ]
    };
    
    peerConnection = new RTCPeerConnection(config);
    
    peerConnection.onconnectionstatechange = function() {
        console.log('Connection state:', peerConnection.connectionState);
        
        if (currentCallType === 'video') {
            switch (peerConnection.connectionState) {
                case 'connecting':
                    setVideoCallStatus('connecting', 'Connexion...');
                    break;
                case 'connected':
                    setVideoCallStatus('connected', 'Connecté');
                    updateRemoteAvatar(currentCallUserName);
                    break;
                case 'disconnected':
                    setVideoCallStatus('failed', 'Connexion perdue');
                    break;
                case 'failed':
                    setVideoCallStatus('failed', 'Échec de connexion');
                    break;
                case 'closed':
                    if (currentCallType === 'video') {
                        endVideoCall();
                    }
                    break;
            }
        } else {
            switch (peerConnection.connectionState) {
                case 'connecting':
                    setCallStatus('connecting');
                    break;
                case 'connected':
                    setCallStatus('connected');
                    break;
                case 'disconnected':
                    setCallStatus('disconnected');
                    break;
                case 'failed':
                    setCallStatus('failed');
                    break;
                case 'closed':
                    setCallStatus('ended');
                    break;
            }
        }
    };
    
    peerConnection.oniceconnectionstatechange = function() {
        console.log('ICE connection state:', peerConnection.iceConnectionState);
        
        if (peerConnection.iceConnectionState === 'connected') {
            if (currentCallType === 'video') {
                setVideoCallStatus('connected', 'Connecté');
                updateRemoteAvatar(currentCallUserName);
            } else {
                setCallStatus('connected');
            }
        } else if (peerConnection.iceConnectionState === 'failed') {
            if (currentCallType === 'video') {
                setVideoCallStatus('failed', 'Connexion échouée');
            } else {
                setCallStatus('failed', 'Connexion échouée');
            }
        }
    };
    
    peerConnection.ontrack = function(event) {
        console.log('Received remote track:', event.track, 'Kind:', event.track.kind);
        
        if (event.streams && event.streams[0]) {
            if (currentCallType === 'video') {
                var remoteVideo = document.getElementById('remoteVideo');
                remoteVideo.srcObject = event.streams[0];
                document.getElementById('remotePlaceholder').style.display = 'none';
            } else {
                var audio = document.createElement('audio');
                audio.srcObject = event.streams[0];
                audio.autoplay = true;
                audio.id = 'remoteAudio';
                document.body.appendChild(audio);
            }
        }
    };
    
    peerConnection.onicecandidate = function(event) {
        if (event.candidate === null) {
            console.log('ICE gathering complete');
        }
    };
}

function setCallStatus(status, message) {
    var statusEl = document.getElementById('callStatus');
    var timer = document.getElementById('callTimer');
    var wave = document.getElementById('callWave');
    var muteBtn = document.getElementById('muteBtn');
    var speakerBtn = document.getElementById('speakerBtn');
    
    callStatus = status;
    
    switch (status) {
        case 'connecting':
            statusEl.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Connexion...';
            statusEl.className = 'call-status connecting';
            wave.style.display = 'flex';
            timer.style.display = 'none';
            muteBtn.disabled = true;
            speakerBtn.disabled = true;
            break;
            
        case 'ringing':
            statusEl.innerHTML = '<i class="fa fa-bell fa-spin"></i> Sonnerie en cours...';
            statusEl.className = 'call-status ringing';
            wave.style.display = 'flex';
            muteBtn.disabled = true;
            speakerBtn.disabled = true;
            break;
            
        case 'connected':
            if (callTimeout) {
                clearTimeout(callTimeout);
                callTimeout = null;
            }
            statusEl.innerHTML = '<span style="color:#10B981">●</span> Connecté';
            statusEl.className = 'call-status connected';
            wave.style.display = 'none';
            timer.style.display = 'block';
            muteBtn.disabled = false;
            speakerBtn.disabled = false;
            muteBtn.className = 'call-btn mute';
            speakerBtn.className = 'call-btn speaker';
            callStartTime = new Date();
            startCallTimer();
            showToast('Appel connecté', 'success');
            
            if (callData && callData.id) {
                fetch('/api/messages/call/' + callData.id + '/accept', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            }
            break;
            
        case 'disconnected':
            statusEl.innerHTML = '<span style="color:#F59E0B">●</span> Connexion perdue';
            statusEl.className = 'call-status disconnected';
            wave.style.display = 'none';
            showToast('Connexion perdue', 'error');
            break;
            
        case 'failed':
            if (callTimeout) {
                clearTimeout(callTimeout);
                callTimeout = null;
            }
            statusEl.innerHTML = '<span style="color:#DC3545">●</span> ' + (message || 'Échec de connexion');
            statusEl.className = 'call-status failed';
            wave.style.display = 'none';
            timer.style.display = 'block';
            timer.innerHTML = 'Appel terminé';
            showToast(message || 'Échec de la connexion', 'error');
            
            setTimeout(function() {
                if (callStatus === 'failed') {
                    endCall();
                }
            }, 3000);
            break;
            
        case 'ended':
            if (callTimeout) {
                clearTimeout(callTimeout);
                callTimeout = null;
            }
            statusEl.innerHTML = '<i class="fa fa-phone-slash"></i> Appel terminé';
            statusEl.className = 'call-status ended';
            wave.style.display = 'none';
            break;
    }
}

function startCallTimer() {
    callTimerInterval = setInterval(function() {
        if (!callStartTime) return;
        var diff = Math.floor((new Date() - callStartTime) / 1000);
        var minutes = Math.floor(diff / 60).toString().padStart(2, '0');
        var seconds = (diff % 60).toString().padStart(2, '0');
        document.getElementById('callTimer').textContent = minutes + ':' + seconds;
    }, 1000);
}

function toggleMute() {
    isMuted = !isMuted;
    if (localStream) {
        localStream.getAudioTracks().forEach(function(track) {
            track.enabled = !isMuted;
        });
    }
    
    var btn = document.getElementById('muteBtn');
    if (isMuted) {
        btn.className = 'call-btn mute active';
        btn.innerHTML = '<i class="fa fa-microphone"></i>';
    } else {
        btn.className = 'call-btn mute';
        btn.innerHTML = '<i class="fa fa-microphone-slash"></i>';
    }
}

function toggleSpeaker() {
    isSpeakerOn = !isSpeakerOn;
    var btn = document.getElementById('speakerBtn');
    if (isSpeakerOn) {
        btn.className = 'call-btn speaker active';
        btn.innerHTML = '<i class="fa fa-volume-down"></i>';
    } else {
        btn.className = 'call-btn speaker';
        btn.innerHTML = '<i class="fa fa-volume-up"></i>';
    }
}

function endCall() {
    if (callTimeout) {
        clearTimeout(callTimeout);
        callTimeout = null;
    }
    
    if (localStream) {
        localStream.getTracks().forEach(function(track) {
            track.stop();
        });
        localStream = null;
    }
    
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    
    if (callTimerInterval) {
        clearInterval(callTimerInterval);
        callTimerInterval = null;
    }
    
    var remoteAudio = document.getElementById('remoteAudio');
    if (remoteAudio) {
        remoteAudio.remove();
    }
    
    callStatus = 'ended';
    setCallStatus('ended');
    
    var tempCallId = currentCallUserId ? 'call_' + currentCallUserId : 'local_call';
    
    fetch('/api/messages/call/' + tempCallId + '/end', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).catch(function() {});
    
    setTimeout(function() {
        callStatus = 'idle';
        callStartTime = null;
        currentCallUserId = null;
        currentCallUserName = null;
        document.getElementById('callOverlay').classList.remove('active');
        document.getElementById('callTimer').textContent = '00:00';
    }, 500);
}

function acceptCall() {
    document.getElementById('incomingCallModal').classList.remove('show');
    callStatus = 'connecting';
    startCall({ id: 'incoming_call' });
}

function declineCall() {
    document.getElementById('incomingCallModal').classList.remove('show');
    callStatus = 'idle';
    currentCallUserId = null;
    currentCallUserName = null;
}

function showIncomingCall(userName, userId) {
    currentCallUserId = userId;
    currentCallUserName = userName;
    callStatus = 'ringing';
    
    document.getElementById('incomingCallAvatar').textContent = userName.charAt(0).toUpperCase();
    document.getElementById('incomingCallName').textContent = userName;
    document.getElementById('incomingCallModal').classList.add('show');
}

// ============= VIDEO CALL FUNCTIONS =============

var currentCallType = 'audio';

function initiateVideoCall() {
    if (!currentConversationId) { 
        showToast('Sélectionnez une conversation', 'error'); 
        return; 
    }
    
    var userName = document.getElementById('chatName').textContent;
    var conversation = conversations.find(function(c) {
        return c.id === currentConversationId;
    });
    
    currentCallUserId = conversation && conversation.otherUser ? conversation.otherUser.id : null;
    currentCallUserName = userName;
    currentCallType = 'video';
    
    if (!currentCallUserId) {
        showToast('Impossible d\'initier l\'appel', 'error');
        return;
    }
    
    startVideoCall({ id: 'video_call_' + Date.now() });
}

function startVideoCall(callData) {
    callStatus = 'connecting';
    currentCallType = 'video';
    
    var overlay = document.getElementById('videoCallOverlay');
    var localVideo = document.getElementById('localVideo');
    var remoteVideo = document.getElementById('remoteVideo');
    var nameEl = document.getElementById('videoCallName');
    var statusEl = document.getElementById('videoCallStatus');
    var muteBtn = document.getElementById('videoMuteBtn');
    var videoBtn = document.getElementById('videoCameraBtn');
    var speakerBtn = document.getElementById('videoSpeakerBtn');
    
    nameEl.textContent = currentCallUserName;
    statusEl.textContent = 'Connexion vidéo...';
    localVideo.srcObject = null;
    remoteVideo.srcObject = null;
    
    muteBtn.disabled = true;
    videoBtn.disabled = true;
    speakerBtn.disabled = true;
    
    overlay.classList.add('active');
    
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then(function(stream) {
            localStream = stream;
            localVideo.srcObject = stream;
            
            createPeerConnection();
            
            localStream.getTracks().forEach(function(track) {
                peerConnection.addTrack(track, localStream);
            });
            
            peerConnection.createOffer()
                .then(function(offer) {
                    return peerConnection.setLocalDescription(offer);
                })
                .then(function() {
                    setVideoCallStatus('ringing', 'Sonnerie en cours...');
                    
                    callTimeout = setTimeout(function() {
                        if (callStatus === 'ringing' || callStatus === 'connecting') {
                            callStatus = 'no_answer';
                            setVideoCallStatus('failed', 'Aucune réponse');
                            
                            setTimeout(function() {
                                if (callStatus === 'no_answer') {
                                    showToast(currentCallUserName + ' ne répond pas', 'info');
                                    endVideoCall();
                                }
                            }, 5000);
                        }
                    }, 30000);
                })
                .catch(function(error) {
                    console.error('Error creating offer:', error);
                    setVideoCallStatus('failed', 'Erreur de connexion');
                });
        })
        .catch(function(error) {
            console.error('Error accessing camera/microphone:', error);
            setVideoCallStatus('failed', 'Caméra/Micro refusé');
            showToast('Impossible d\'accéder à la caméra ou au microphone', 'error');
        });
}

function setVideoCallStatus(status, message) {
    var statusEl = document.getElementById('videoCallStatus');
    var localVideo = document.getElementById('localVideo');
    var muteBtn = document.getElementById('videoMuteBtn');
    var videoBtn = document.getElementById('videoCameraBtn');
    var speakerBtn = document.getElementById('videoSpeakerBtn');
    
    callStatus = status;
    
    switch (status) {
        case 'connecting':
            statusEl.textContent = 'Connexion...';
            break;
        case 'ringing':
            statusEl.textContent = message || 'Sonnerie en cours...';
            break;
        case 'connected':
            if (callTimeout) {
                clearTimeout(callTimeout);
                callTimeout = null;
            }
            statusEl.innerHTML = '<span style="color:#10B981">●</span> Connecté';
            muteBtn.disabled = false;
            videoBtn.disabled = false;
            speakerBtn.disabled = false;
            showToast('Appel vidéo connecté', 'success');
            
            if (callData && callData.id) {
                fetch('/api/messages/call/' + callData.id + '/accept', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            }
            break;
        case 'failed':
            if (callTimeout) {
                clearTimeout(callTimeout);
                callTimeout = null;
            }
            statusEl.innerHTML = '<span style="color:#DC3545">●</span> ' + (message || 'Échec');
            showToast(message || 'Échec de la connexion', 'error');
            break;
    }
}

function toggleVideoMute() {
    if (!localStream) return;
    
    var audioTracks = localStream.getAudioTracks();
    if (audioTracks.length > 0) {
        audioTracks[0].enabled = !audioTracks[0].enabled;
        var btn = document.getElementById('videoMuteBtn');
        btn.classList.toggle('off', !audioTracks[0].enabled);
        showToast(audioTracks[0].enabled ? 'Microphone activé' : 'Microphone coupé', 'info');
    }
}

function toggleVideoCamera() {
    if (!localStream) return;
    
    var videoTracks = localStream.getVideoTracks();
    if (videoTracks.length > 0) {
        videoTracks[0].enabled = !videoTracks[0].enabled;
        var btn = document.getElementById('videoCameraBtn');
        btn.classList.toggle('off', !videoTracks[0].enabled);
        showToast(videoTracks[0].enabled ? 'Caméra activée' : 'Caméra désactivée', 'info');
    }
}

function toggleVideoSpeaker() {
    var remoteVideo = document.getElementById('remoteVideo');
    isSpeakerOn = !isSpeakerOn;
    remoteVideo.muted = !isSpeakerOn;
    
    var btn = document.getElementById('videoSpeakerBtn');
    btn.classList.toggle('off', !isSpeakerOn);
}

function switchCamera() {
    if (!localStream) return;
    
    navigator.mediaDevices.enumerateDevices()
        .then(function(devices) {
            var videoDevices = devices.filter(function(d) { return d.kind === 'videoinput'; });
            if (videoDevices.length < 2) {
                showToast('Pas d\'autre caméra disponible', 'info');
                return;
            }
            
            var currentDeviceId = localStream.getVideoTracks()[0].getSettings().deviceId;
            var nextDevice = videoDevices.find(function(d) { return d.deviceId !== currentDeviceId; });
            
            if (nextDevice) {
                navigator.mediaDevices.getUserMedia({ video: { deviceId: nextDevice.deviceId }, audio: false })
                    .then(function(newStream) {
                        var newVideoTrack = newStream.getVideoTracks()[0];
                        var oldVideoTrack = localStream.getVideoTracks()[0];
                        
                        var sender = peerConnection.getSenders().find(function(s) {
                            return s.track && s.track.kind === 'video';
                        });
                        
                        if (sender) {
                            sender.replaceTrack(newVideoTrack);
                        }
                        
                        localStream.removeTrack(oldVideoTrack);
                        localStream.addTrack(newVideoTrack);
                        
                        document.getElementById('localVideo').srcObject = localStream;
                        showToast('Caméra changée', 'success');
                    });
            }
        });
}

function togglePictureInPicture() {
    var remoteVideo = document.getElementById('remoteVideo');
    
    if (!remoteVideo || !remoteVideo.src) {
        showToast('Appel non actif', 'error');
        return;
    }
    
    if (document.pictureInPictureElement) {
        document.exitPictureInPicture();
    } else if (remoteVideo.readyState >= 2 && remoteVideo.requestPictureInPicture) {
        remoteVideo.requestPictureInPicture().catch(function() {
            showToast('PiP non disponible', 'error');
        });
    }
}

function updateRemoteAvatar(name) {
    var avatar = document.getElementById('remoteAvatar');
    var placeholder = document.getElementById('remotePlaceholder');
    if (avatar) {
        avatar.textContent = name.charAt(0).toUpperCase();
    }
}

function endVideoCall() {
    if (callTimeout) {
        clearTimeout(callTimeout);
        callTimeout = null;
    }
    
    if (localStream) {
        localStream.getTracks().forEach(function(track) {
            track.stop();
        });
        localStream = null;
    }
    
    if (peerConnection) {
        peerConnection.close();
        peerConnection = null;
    }
    
    document.getElementById('localVideo').srcObject = null;
    document.getElementById('remoteVideo').srcObject = null;
    document.getElementById('videoCallOverlay').classList.remove('active');
    
    callStatus = 'ended';
    currentCallType = 'audio';
    
    showToast('Appel terminé', 'info');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (callStatus !== 'idle' && callStatus !== 'ended') {
            endCall();
        }
        document.getElementById('incomingCallModal').classList.remove('show');
        closeConversationInfo();
    }
});

// ============= CONVERSATION INFO FUNCTIONS =============

function showConversationInfo() {
    if (!currentConversationId) {
        showToast('Sélectionnez une conversation', 'error');
        return;
    }
    
    var conversation = conversations.find(function(c) { return c.id === currentConversationId; });
    if (!conversation) return;
    
    var otherUser = conversation.otherUser;
    var userName = conversation.type === 'group' ? conversation.name : (otherUser ? otherUser.name : 'Utilisateur');
    
    document.getElementById('infoAvatar').textContent = userName.charAt(0).toUpperCase();
    document.getElementById('infoName').textContent = userName;
    
    document.getElementById('conversationInfoOverlay').classList.add('show');
    document.getElementById('conversationInfoSidebar').classList.add('show');
    
    fetch('/api/messages/conversation/' + currentConversationId + '/info', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.conversation) {
            document.getElementById('infoMessageCount').textContent = data.conversation.messageCount || 0;
            document.getElementById('infoCallCount').textContent = data.conversation.callCount || 0;
            document.getElementById('infoMissedCalls').textContent = data.conversation.missedCalls || 0;
        }
    });
    
    fetch('/api/messages/calls/history/' + currentConversationId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        var list = document.getElementById('callsHistoryList');
        if (data.calls && data.calls.length > 0) {
            var html = '';
            data.calls.forEach(function(call) {
                var icon = '';
                var statusText = '';
                var iconClass = '';
                
                switch (call.status) {
                    case 'accepted':
                        icon = '<i class="fa fa-phone"></i>';
                        iconClass = 'accepted';
                        statusText = call.isCaller ? 'Appel passé' : 'Appel reçu';
                        break;
                    case 'missed':
                    case 'no_answer':
                        icon = '<i class="fa fa-phone-slash"></i>';
                        iconClass = 'missed';
                        statusText = call.isCaller ? 'Sans réponse' : 'Appel manqué';
                        break;
                    case 'declined':
                        icon = '<i class="fa fa-times"></i>';
                        iconClass = 'declined';
                        statusText = call.isCaller ? 'Refusé' : 'Appel refusé';
                        break;
                    default:
                        icon = '<i class="fa fa-phone"></i>';
                        iconClass = '';
                        statusText = 'Appel';
                }
                
                var time = formatTime(call.createdAt);
                var typeIcon = call.type === 'video' ? '<i class="fa fa-video" style="margin-left:4px"></i>' : '';
                
                html += '<div class="call-history-item">';
                html += '<div class="call-history-icon ' + iconClass + '">' + icon + '</div>';
                html += '<div class="call-history-info">';
                html += '<div class="call-history-status">' + statusText + ' ' + typeIcon + '</div>';
                html += '<div class="call-history-time">' + time + '</div>';
                html += '</div>';
                if (call.status === 'accepted' && call.durationFormatted) {
                    html += '<div class="call-history-duration">' + call.durationFormatted + '</div>';
                }
                html += '</div>';
            });
            list.innerHTML = html;
        } else {
            list.innerHTML = '<p style="text-align:center;color:var(--fg-text-secondary);padding:1rem;font-size:0.9rem">Aucun appel</p>';
        }
    });
}

function closeConversationInfo() {
    document.getElementById('conversationInfoOverlay').classList.remove('show');
    document.getElementById('conversationInfoSidebar').classList.remove('show');
}

function updateFriendNickname() {
    var nicknameInput = document.getElementById('friendNickname');
    var nickname = nicknameInput.value.trim();
    
    if (!currentConversationId) return;
    
    var conversation = conversations.find(function(c) { return c.id === currentConversationId; });
    if (!conversation || !conversation.otherUser) return;
    
    var otherUserId = conversation.otherUser.id;
    
    fetch('/api/friend/nickname', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ userId: otherUserId, nickname: nickname })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Surnom mis à jour', 'success');
            conversation.otherUser.nickname = nickname;
            var displayName = nickname || conversation.otherUser.name;
            document.getElementById('chatName').textContent = displayName;
            document.getElementById('currentNickname').textContent = nickname ? 'Actuel: ' + nickname : '';
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    });
}

function changeTheme(theme) {
    if (!currentConversationId) {
        showToast('Sélectionnez une conversation', 'error');
        return;
    }
    
    fetch('/api/messages/theme', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ conversationId: currentConversationId, theme: theme })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Thème mis à jour', 'success');
            applyConversationTheme(theme);
        } else {
            showToast(data.error || 'Erreur', 'error');
        }
    });
}

function applyConversationTheme(theme) {
    var chatMessages = document.getElementById('chatMessages');
    if (!chatMessages) return;
    
    if (theme.indexOf('linear-gradient') !== -1) {
        chatMessages.style.background = theme;
    } else {
        chatMessages.style.background = theme;
    }
}

function callFromInfo(type) {
    closeConversationInfo();
    setTimeout(function() {
        initiateCall(type);
    }, 300);
}

function blockUser() {
    closeConversationInfo();
    showToast('Fonctionnalité à venir', 'info');
}

function deleteConversation() {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette conversation ?')) {
        return;
    }
    closeConversationInfo();
    showToast('Conversation supprimée', 'success');
}

function renderCallLog(call) {
    var icon = '';
    var statusText = '';
    var cssClass = '';
    
    switch (call.status) {
        case 'accepted':
            icon = '<i class="fa fa-phone"></i>';
            statusText = call.isCaller ? 'Appel terminé' : 'Appel';
            cssClass = 'accepted';
            if (call.durationFormatted) {
                statusText += ' • ' + call.durationFormatted;
            }
            break;
        case 'missed':
        case 'no_answer':
            icon = '<i class="fa fa-phone-slash"></i>';
            statusText = call.isCaller ? 'Appel sans réponse' : 'Appel manqué';
            cssClass = call.isCaller ? '' : 'missed-incoming';
            break;
        case 'declined':
            icon = '<i class="fa fa-times"></i>';
            statusText = call.isCaller ? 'Appel annulé' : 'Appel refusé';
            cssClass = 'declined';
            break;
        default:
            icon = '<i class="fa fa-phone"></i>';
            statusText = 'Appel';
            cssClass = '';
    }
    
    var typeIcon = call.type === 'video' ? '<i class="fa fa-video"></i>' : '<i class="fa fa-phone"></i>';
    var time = formatTime(call.createdAt);
    
    return '<div class="call-log ' + cssClass + '">' + icon + ' ' + statusText + ' • ' + time + '</div>';
}

// ==================== FRIEND REQUESTS ====================

function switchMessengerTab(tab) {
    document.querySelectorAll('.messenger-tab').forEach(function(t) {
        t.classList.remove('active');
    });
    document.querySelector('.messenger-tab[data-tab="' + tab + '"]').classList.add('active');
    
    document.getElementById('conversationList').style.display = tab === 'conversations' ? 'flex' : 'none';
    document.getElementById('conversationsSearch').style.display = tab === 'conversations' ? 'block' : 'none';
    document.getElementById('invitationsPanel').classList.toggle('active', tab === 'invitations');
    document.getElementById('friendsListPanel').classList.toggle('active', tab === 'friends');
    
    currentMessengerTab = tab;
    
    if (tab === 'invitations') {
        loadReceivedInvitations();
        loadSentInvitations();
    } else if (tab === 'friends') {
        loadFriendsList();
    }
}

function loadFriendRequestsCount() {
    fetch('/api/friend/pending/count', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        var badge = document.getElementById('invitationsBadge');
        if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'inline';
        } else {
            badge.style.display = 'none';
        }
    })
    .catch(function() {});
}

function loadReceivedInvitations() {
    var list = document.getElementById('receivedInvitationsList');
    list.innerHTML = '<div style="padding:1rem;text-align:center;color:var(--fg-text-secondary)"><i class="fa fa-spinner fa-spin" style="font-size:1.2rem"></i></div>';
    
    fetch('/api/friend/received', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        pendingFriendRequests = data.requests || [];
        if (pendingFriendRequests.length === 0) {
            list.innerHTML = '<p style="padding:1rem;text-align:center;color:var(--fg-text-secondary);font-size:0.9rem"><i class="fa fa-inbox" style="display:block;font-size:1.5rem;margin-bottom:0.5rem;opacity:0.5"></i>Aucune demande reçue</p>';
        } else {
            var html = '';
            pendingFriendRequests.forEach(function(req) {
                html += renderReceivedInvitation(req);
            });
            list.innerHTML = html;
        }
    })
    .catch(function() {
        list.innerHTML = '<p style="padding:1rem;text-align:center;color:var(--fg-error);font-size:0.9rem">Erreur de chargement</p>';
    });
}

function loadSentInvitations() {
    var list = document.getElementById('sentInvitationsList');
    list.innerHTML = '<div style="padding:1rem;text-align:center;color:var(--fg-text-secondary)"><i class="fa fa-spinner fa-spin" style="font-size:1.2rem"></i></div>';
    
    fetch('/api/friend/sent', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        sentFriendRequests = data.requests || [];
        if (sentFriendRequests.length === 0) {
            list.innerHTML = '<p style="padding:1rem;text-align:center;color:var(--fg-text-secondary);font-size:0.9rem"><i class="fa fa-paper-plane" style="display:block;font-size:1.5rem;margin-bottom:0.5rem;opacity:0.5"></i>Aucune demande envoyée</p>';
        } else {
            var html = '';
            sentFriendRequests.forEach(function(req) {
                html += renderSentInvitation(req);
            });
            list.innerHTML = html;
        }
    })
    .catch(function() {
        list.innerHTML = '<p style="padding:1rem;text-align:center;color:var(--fg-error);font-size:0.9rem">Erreur de chargement</p>';
    });
}

function renderReceivedInvitation(req) {
    var avatar = req.otherUser.avatar 
        ? '<img src="' + req.otherUser.avatar + '" alt="">' 
        : req.otherUser.name.charAt(0).toUpperCase();
    
    return '<div class="invite-item" id="receivedReq_' + req.id + '">' +
        '<div class="invite-avatar">' + avatar + '</div>' +
        '<div class="invite-info">' +
            '<div class="invite-name">' + req.otherUser.name + '</div>' +
            '<div class="invite-time">Demande reçue • ' + formatTime(req.createdAt) + '</div>' +
            '<div class="invite-actions">' +
                '<button class="invite-btn accept" onclick="acceptFriendRequest(' + req.id + ')"><i class="fa fa-check"></i> Accepter</button>' +
                '<button class="invite-btn reject" onclick="rejectFriendRequest(' + req.id + ')"><i class="fa fa-times"></i> Refuser</button>' +
            '</div>' +
        '</div>' +
    '</div>';
}

function renderSentInvitation(req) {
    var avatar = req.otherUser.avatar 
        ? '<img src="' + req.otherUser.avatar + '" alt="">' 
        : req.otherUser.name.charAt(0).toUpperCase();
    
    return '<div class="invite-item" id="sentReq_' + req.id + '">' +
        '<div class="invite-avatar">' + avatar + '</div>' +
        '<div class="invite-info">' +
            '<div class="invite-name">' + req.otherUser.name + '</div>' +
            '<div class="invite-time">En attente de réponse • ' + formatTime(req.createdAt) + '</div>' +
        '</div>' +
        '<button class="invite-btn cancel" onclick="cancelFriendRequest(' + req.id + ')"><i class="fa fa-times"></i></button>' +
    '</div>';
}

function acceptFriendRequest(requestId) {
    var item = document.getElementById('receivedReq_' + requestId);
    if (item) {
        item.style.opacity = '0.5';
        item.style.pointerEvents = 'none';
    }
    
    fetch('/api/friend/accept/' + requestId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showFriendNotification('Ami ajouté !', req.otherUser.name + ' est maintenant votre ami.');
            if (item) {
                item.style.animation = 'fadeOut 0.3s ease forwards';
                setTimeout(function() {
                    item.remove();
                    loadReceivedInvitations();
                    loadFriendRequestsCount();
                }, 300);
            }
        } else {
            if (item) {
                item.style.opacity = '1';
                item.style.pointerEvents = 'auto';
            }
            showToast(data.error || 'Erreur', 'error');
        }
    })
    .catch(function() {
        if (item) {
            item.style.opacity = '1';
            item.style.pointerEvents = 'auto';
        }
        showToast('Erreur de connexion', 'error');
    });
}

function rejectFriendRequest(requestId) {
    var item = document.getElementById('receivedReq_' + requestId);
    if (item) {
        item.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    fetch('/api/friend/reject/' + requestId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Demande refusée', 'info');
            if (item) {
                setTimeout(function() {
                    item.remove();
                    loadReceivedInvitations();
                    loadFriendRequestsCount();
                }, 300);
            }
        }
    })
    .catch(function() {
        showToast('Erreur de connexion', 'error');
    });
}

function cancelFriendRequest(requestId) {
    var item = document.getElementById('sentReq_' + requestId);
    if (item) {
        item.style.animation = 'fadeOut 0.3s ease forwards';
    }
    
    fetch('/api/friend/cancel/' + requestId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Demande annulée', 'info');
            if (item) {
                setTimeout(function() {
                    item.remove();
                    loadSentInvitations();
                }, 300);
            }
        }
    })
    .catch(function() {
        showToast('Erreur de connexion', 'error');
    });
}

function loadFriendsList() {
    var list = document.getElementById('friendsList');
    list.innerHTML = '<div style="padding:2rem;text-align:center;color:var(--fg-text-secondary)"><i class="fa fa-spinner fa-spin" style="font-size:1.5rem"></i></div>';
    
    fetch('/api/friend/list', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        friendsList = data.friends || [];
        if (friendsList.length === 0) {
            list.innerHTML = '<div class="empty-state" style="padding:2rem"><div class="empty-icon"><i class="fa fa-users"></i></div><h4>Aucun ami</h4><p>Ajoutez des amis pour discuter facilement</p></div>';
        } else {
            var html = '';
            friendsList.forEach(function(friend) {
                var avatar = friend.avatar 
                    ? '<img src="' + friend.avatar + '" alt="">' 
                    : friend.name.charAt(0).toUpperCase();
                html += '<div class="friend-item" onclick="openChatWithFriend(' + friend.id + ')">' +
                    '<div class="friend-avatar">' + avatar + '</div>' +
                    '<div class="friend-info">' +
                        '<div class="friend-name">' + friend.name + '</div>' +
                    '</div>' +
                '</div>';
            });
            list.innerHTML = html;
        }
    })
    .catch(function() {
        list.innerHTML = '<p style="padding:2rem;text-align:center;color:var(--fg-error)">Erreur de chargement</p>';
    });
}

function openChatWithFriend(userId) {
    fetch('/api/messages/start/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success && data.conversation) {
            switchMessengerTab('conversations');
            selectConversation(data.conversation.id);
        }
    })
    .catch(function() {});
}

function sendFriendRequest(userId, callback) {
    fetch('/api/friend/request/' + userId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (callback) callback(data);
    })
    .catch(function() {
        if (callback) callback({ success: false, error: 'Erreur de connexion' });
    });
}

function getFriendStatus(userId, callback) {
    fetch('/api/friend/status/' + userId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (callback) callback(data);
    })
    .catch(function() {
        if (callback) callback({ status: 'error' });
    });
}

function showFriendNotification(title, text) {
    var notif = document.createElement('div');
    notif.className = 'friend-request-notification';
    notif.innerHTML = '<i class="fa fa-user-plus"></i>' +
        '<div class="notif-content">' +
            '<div class="notif-title">' + title + '</div>' +
            '<div class="notif-text">' + text + '</div>' +
        '</div>';
    document.body.appendChild(notif);
    
    setTimeout(function() {
        notif.remove();
    }, 3000);
}

function showToast(message, type) {
    var toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;bottom:20px;right:20px;background:' + 
        (type === 'success' ? 'var(--fg-success)' : type === 'error' ? 'var(--fg-error)' : 'var(--fg-primary)') + 
        ';color:white;padding:0.75rem 1.5rem;border-radius:8px;font-size:0.9rem;font-weight:500;' +
        'box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:3000;animation:slideUp 0.3s ease';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(function() {
        toast.style.animation = 'fadeOutNotif 0.3s ease forwards';
        setTimeout(function() { toast.remove(); }, 300);
    }, 2500);
}

window.onerror = function(msg, url, line, col, error) {
    console.error('JS Error at line', line, ':', msg);
    return false;
};
