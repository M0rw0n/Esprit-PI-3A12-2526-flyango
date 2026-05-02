// ==================== FLY&GO CALL SYSTEM - COMPLETE WEBRTC ====================

var callState = {
    peerConnection: null,
    localStream: null,
    remoteStream: null,
    currentCall: null,
    callType: 'audio',
    isMuted: false,
    isVideoOff: false,
    isCallActive: false,
    callStartTime: null,
    durationInterval: null,
    currentUserId: null,
    signalingInterval: null,
    
    // STUN servers for WebRTC
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

var CallManager = {
    init: function() {
        this.loadCurrentUser();
        // Polling is kept as fallback, but Mercure is preferred
        this.startPolling();
    },

    handleSignaling: function(data) {
        console.log('📱 Call signaling received via Mercure:', data);
        
        if (data.call && data.call.status === 'calling' && !callState.isCallActive) {
            this.showIncomingCall(data.call);
        }
        
        if (data.offer && !callState.isCallActive) {
            console.log('Received offer, processing...');
            this.handleOffer(data.offer, data.callId);
        }
        
        if (data.answer && callState.peerConnection) {
            console.log('Received answer');
            this.handleAnswer(data.answer);
        }
        
        if (data.iceCandidate && callState.peerConnection) {
            console.log('Received ICE candidate');
            this.handleIceCandidate(data.iceCandidate);
        }
        
        if (data.type === 'call_ended') {
            this.endCall(false); // Don't notify server as it's already ended
        }
    },

    loadCurrentUser: function() {
        fetch('/api/user/me', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            callState.currentUserId = data.id;
            console.log('Call user ID:', data.id);
        })
        .catch(function() {});
    },

    startPolling: function() {
        if (callState.signalingInterval) clearInterval(callState.signalingInterval);
        
        callState.signalingInterval = setInterval(function() {
            CallManager.checkSignaling();
        }, 2000);
        
        CallManager.checkSignaling();
    },

    checkSignaling: function() {
        if (!callState.currentUserId) return;

        fetch('/api/messages/call/signaling', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.call && data.call.status === 'calling' && !callState.isCallActive) {
                CallManager.showIncomingCall(data.call);
            }
            
            if (data.offer && !callState.isCallActive) {
                console.log('Received offer, processing...');
                CallManager.handleOffer(data.offer, data.callId);
            }
            
            if (data.answer && callState.peerConnection) {
                console.log('Received answer');
                CallManager.handleAnswer(data.answer);
            }
            
            if (data.iceCandidate && callState.peerConnection) {
                console.log('Received ICE candidate');
                CallManager.handleIceCandidate(data.iceCandidate);
            }
        })
        .catch(function() {});
    },

    startCall: function(userId, callType) {
        console.log('Starting ' + callType + ' call with user:', userId);
        callState.callType = callType;
        
        callState.isCallActive = true;
        callState.currentCall = { id: Date.now(), toId: userId };
        CallManager.showCallScreen('connecting');
        
        CallManager.requestMedia(callType)
            .then(function(stream) {
                console.log('Media obtained');
                callState.localStream = stream;
                
                var localVideo = document.getElementById('localVideo');
                if (localVideo) {
                    localVideo.srcObject = stream;
                }
                
                CallManager.createPeerConnection();
                return CallManager.createOffer(userId);
            })
            .then(function(offer) {
                console.log('Offer created, sending...');
                CallManager.sendOffer(userId, offer);
                CallManager.showCallScreen('ringing');
            })
            .catch(function(err) {
                console.error('Call error:', err);
                CallManager.endCall();
                showToast('Erreur: ' + err.message, 'error');
            });
    },

    requestMedia: function(callType) {
        var constraints = {
            audio: true,
            video: callType === 'video'
        };
        
        console.log('Requesting media:', constraints);
        return navigator.mediaDevices.getUserMedia(constraints);
    },

    createPeerConnection: function() {
        var pc = new RTCPeerConnection({
            iceServers: callState.iceServers
        });
        
        pc.onicecandidate = function(event) {
            if (event.candidate) {
                console.log('ICE candidate:', event.candidate);
                CallManager.sendIceCandidate(event.candidate);
            }
        };
        
        pc.ontrack = function(event) {
            console.log('Received remote track');
            callState.remoteStream = event.streams[0];
            
            var remoteVideo = document.getElementById('remoteVideo');
            if (remoteVideo) {
                remoteVideo.srcObject = event.streams[0];
            }
            
            CallManager.showCallScreen('connected');
        };
        
        pc.onconnectionstatechange = function() {
            console.log('Connection state:', pc.connectionState);
            if (pc.connectionState === 'connected') {
                CallManager.showCallScreen('connected');
            } else if (pc.connectionState === 'disconnected' || pc.connectionState === 'failed') {
                CallManager.endCall();
            }
        };
        
        if (callState.localStream) {
            callState.localStream.getTracks().forEach(function(track) {
                pc.addTrack(track, callState.localStream);
            });
        }
        
        callState.peerConnection = pc;
        console.log('PeerConnection created');
    },

    createOffer: function(userId) {
        var pc = callState.peerConnection;
        if (!pc) throw new Error('No peer connection');
        
        return pc.createOffer({
            offerToReceiveAudio: true,
            offerToReceiveVideo: callState.callType === 'video'
        })
        .then(function(offer) {
            return pc.setLocalDescription(offer);
        })
        .then(function() {
            return pc.localDescription;
        });
    },

    handleOffer: function(offer, callId) {
        console.log('Handling offer...');
        
        callState.currentCall = { id: callId };
        callState.isCallActive = true;
        
        CallManager.requestMedia(callState.callType)
            .then(function(stream) {
                callState.localStream = stream;
                
                var localVideo = document.getElementById('localVideo');
                if (localVideo) localVideo.srcObject = stream;
                
                CallManager.createPeerConnection();
                
                return callState.peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
            })
            .then(function() {
                return callState.peerConnection.createAnswer();
            })
            .then(function(answer) {
                return callState.peerConnection.setLocalDescription(answer);
            })
            .then(function() {
                var otherUserId = callState.currentCall.fromId; // We need to store who sent the offer
                return fetch('/api/messages/call/' + callId + '/answer', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ 
                        answer: callState.peerConnection.localDescription,
                        toUserId: otherUserId
                    })
                });
            })
            .then(function() {
                CallManager.showCallScreen('connected');
            })
            .catch(function(err) {
                console.error('Handle offer error:', err);
            });
    },

    handleAnswer: function(answer) {
        var pc = callState.peerConnection;
        if (!pc) return;
        
        pc.setRemoteDescription(new RTCSessionDescription(answer))
            .then(function() {
                console.log('Answer set, connected');
                CallManager.showCallScreen('connected');
            })
            .catch(function(err) {
                console.error('Handle answer error:', err);
            });
    },

    handleIceCandidate: function(candidate) {
        var pc = callState.peerConnection;
        if (!pc) return;
        
        pc.addIceCandidate(new RTCIceCandidate(candidate))
            .catch(function(err) {
                console.error('Add ICE error:', err);
            });
    },

    sendOffer: function(userId, offer) {
        return fetch('/api/messages/call/start/' + userId, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({
                type: callState.callType,
                offer: offer
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                callState.currentCall.id = data.callId;
                console.log('Offer sent, call ID:', data.callId);
            }
        })
        .catch(function(err) {
            console.error('Send offer error:', err);
        });
    },

    sendIceCandidate: function(candidate) {
        if (!callState.currentCall || !callState.currentCall.id) return;
        
        // We need the other user ID to send signaling via Mercure
        var otherUserId = null;
        if (messengerState.currentConversation) {
            otherUserId = messengerState.currentConversation.otherUser ? messengerState.currentConversation.otherUser.id : messengerState.currentConversation.userId;
        }
        
        fetch('/api/messages/call/' + callState.currentCall.id + '/ice', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ 
                candidate: candidate,
                toUserId: otherUserId
            })
        }).catch(function() {});
    },

    showCallScreen: function(status) {
        var screen = document.getElementById('callScreen');
        var statusEl = document.getElementById('callStatus');
        
        if (screen) {
            screen.classList.add('show');
            
            if (status === 'connecting') {
                statusEl.textContent = 'Connexion...';
            } else if (status === 'ringing') {
                statusEl.textContent = 'Sonne...';
            } else if (status === 'connected') {
                statusEl.textContent = 'En appel';
                callState.callStartTime = Date.now();
                CallManager.startDurationTimer();
            }
        }
    },

    startDurationTimer: function() {
        if (callState.durationInterval) clearInterval(callState.durationInterval);
        
        callState.durationInterval = setInterval(function() {
            var duration = Math.floor((Date.now() - callState.callStartTime) / 1000);
            var min = Math.floor(duration / 60);
            var sec = duration % 60;
            var timeStr = ('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2);
            
            var durationEl = document.getElementById('callDuration');
            if (durationEl) durationEl.textContent = timeStr;
        }, 1000);
    },

    endCall: function(notifyServer) {
        if (notifyServer === undefined) notifyServer = true;
        
        if (callState.localStream) {
            callState.localStream.getTracks().forEach(function(track) {
                track.stop();
            });
            callState.localStream = null;
        }

        if (callState.peerConnection) {
            callState.peerConnection.close();
            callState.peerConnection = null;
        }

        if (callState.durationInterval) {
            clearInterval(callState.durationInterval);
            callState.durationInterval = null;
        }

        if (notifyServer && callState.currentCall && callState.currentCall.id) {
            fetch('/api/messages/call/' + callState.currentCall.id + '/end', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(function() {});
        }

        callState.isCallActive = false;
        callState.currentCall = null;
        callState.callStartTime = null;

        CallManager.hideCallScreen();
        console.log('Call ended');
    },

    hideCallScreen: function() {
        var screen = document.getElementById('callScreen');
        if (screen) screen.classList.remove('show');
    },

    showIncomingCall: function(call) {
        callState.currentCall = call;
        callState.currentCall.fromId = call.from ? call.from.id : null;
        callState.callType = call.type || 'audio';
        
        var popup = document.getElementById('callPopup');
        if (popup) {
            document.getElementById('callPopupName').textContent = call.from ? call.from.name : 'Appel entrant';
            document.getElementById('callPopupType').textContent = call.type === 'video' ? '📹 Video' : '📞 Audio';
            popup.classList.add('show');
        }
    },

    toggleMute: function() {
        if (callState.localStream) {
            var audioTrack = callState.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                callState.isMuted = !audioTrack.enabled;
                
                var btn = document.getElementById('muteBtn');
                if (btn) {
                    btn.innerHTML = callState.isMuted ? '<i class="fa fa-microphone-slash"></i>' : '<i class="fa fa-microphone"></i>';
                    btn.classList.toggle('active', callState.isMuted);
                }
            }
        }
    },

    toggleVideo: function() {
        if (callState.localStream) {
            var videoTrack = callState.localStream.getVideoTracks()[0];
            if (videoTrack) {
                videoTrack.enabled = !videoTrack.enabled;
                callState.isVideoOff = !videoTrack.enabled;
                
                var btn = document.getElementById('videoBtn');
                if (btn) {
                    btn.innerHTML = callState.isVideoOff ? '<i class="fa fa-video-slash"></i>' : '<i class="fa fa-video"></i>';
                    btn.classList.toggle('active', callState.isVideoOff);
                }
            }
        }
    },

    showFullScreen: function() {
        var screen = document.getElementById('callScreen');
        if (screen) screen.classList.toggle('fullscreen');
    },

    acceptCall: function(callId) {
        CallManager.hideCallPopup();
        callState.currentCall = { id: callId };
        
        callState.isCallActive = true;
        CallManager.showCallScreen('connecting');
        
        CallManager.requestMedia(callState.callType)
            .then(function(stream) {
                callState.localStream = stream;
                
                var localVideo = document.getElementById('localVideo');
                if (localVideo) localVideo.srcObject = stream;
                
                CallManager.createPeerConnection();
                
                return fetch('/api/messages/call/accept/' + callId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    CallManager.showCallScreen('connected');
                }
            })
            .catch(function(err) {
                console.error('Accept error:', err);
                showToast('Erreur de connexion', 'error');
            });
    },

    rejectCall: function(callId) {
        fetch('/api/messages/call/' + callId + '/reject', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function() {
            CallManager.hideCallPopup();
            showToast('Appel rejeté', 'info');
        });
    },

    hideCallPopup: function() {
        var popup = document.getElementById('callPopup');
        if (popup) popup.classList.remove('show');
    }
};

// ==================== WINDOW FUNCTIONS ====================

window.startAudioCall = function(userId) {
    CallManager.startCall(userId, 'audio');
};

window.startVideoCall = function(userId) {
    CallManager.startCall(userId, 'video');
};

window.acceptIncomingCall = function(callId) {
    CallManager.acceptCall(callId);
};

window.rejectIncomingCall = function(callId) {
    CallManager.rejectCall(callId);
};

window.endCurrentCall = function() {
    CallManager.endCall();
};

window.toggleMute = function() {
    CallManager.toggleMute();
};

window.toggleVideo = function() {
    CallManager.toggleVideo();
};

window.toggleFullScreen = function() {
    CallManager.showFullScreen();
};

document.addEventListener('DOMContentLoaded', function() {
    CallManager.init();
    window.CallManager = CallManager;
});