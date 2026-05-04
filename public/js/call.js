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
<<<<<<< HEAD
        // Mercure connection is handled in messenger.js
=======
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
>>>>>>> testsisi
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

<<<<<<< HEAD
    startCall: function(userId, callType) {
        if (!messengerState.currentConversation) return;
        
        console.log('Starting ' + callType + ' call with user:', userId);
        callState.callType = callType;
        
        var formData = new FormData();
        formData.append('callee_id', userId);
        formData.append('conversation_id', messengerState.currentConversation.id);
        formData.append('type', callType);

        fetch('/api/calls/initiate', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                callState.currentCall = data.call;
                callState.isCallActive = true;
                CallManager.showCallScreen('ringing');
                
                // Start media and peer connection after initiation
                return CallManager.requestMedia(callType);
            } else {
                throw new Error(data.error);
            }
        })
        .then(stream => {
            callState.localStream = stream;
            var localVideo = document.getElementById('localVideo');
            if (localVideo) localVideo.srcObject = stream;
            
            CallManager.createPeerConnection();
            return CallManager.createOffer();
        })
        .catch(err => {
            console.error('Call start error:', err);
            showToast('Erreur appel: ' + err.message, 'error');
            CallManager.endCall();
        });
    },

    handleIncomingCall: function(data) {
        console.log('Incoming call:', data);
        callState.currentCall = { id: data.callId };
        callState.callType = data.callType;
        
        var popup = document.getElementById('callPopup');
        var nameEl = document.getElementById('callPopupName');
        var typeEl = document.getElementById('callPopupType');
        
        if (popup && nameEl && typeEl) {
            nameEl.textContent = data.caller.name;
            typeEl.innerHTML = data.callType === 'video' ? '<i class="fa fa-video-camera"></i> Vidéo' : '<i class="fa fa-phone"></i> Audio';
            popup.classList.add('show');
            
            // Play ringtone
            var ringtone = document.getElementById('ringtoneAudio');
            if (ringtone) ringtone.play().catch(() => {});
        }
    },

    acceptCall: function() {
        if (!callState.currentCall) return;
        
        var callId = callState.currentCall.id;
        var formData = new FormData();
        formData.append('call_id', callId);

        fetch('/api/calls/accept', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                CallManager.hideCallPopup();
                CallManager.showCallScreen('connecting');
                
                return CallManager.requestMedia(callState.callType);
            }
        })
        .then(stream => {
            callState.localStream = stream;
            var localVideo = document.getElementById('localVideo');
            if (localVideo) localVideo.srcObject = stream;
            
            CallManager.createPeerConnection();
            // Wait for offer from caller
        })
        .catch(err => console.error('Accept error:', err));
    },

    rejectCall: function() {
        if (!callState.currentCall) return;
        
        var callId = callState.currentCall.id;
        var formData = new FormData();
        formData.append('call_id', callId);

        fetch('/api/calls/reject', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(() => {
            CallManager.hideCallPopup();
            CallManager.endCall();
        });
    },

    handleCallAccepted: function(data) {
        console.log('Call accepted by remote');
        if (callState.peerConnection) {
            // Peer connection should already be created by startCall
            // Now we just wait for tracks
            CallManager.showCallScreen('connected');
        }
    },

    handleCallEnd: function(data) {
        console.log('Call ended by remote');
        CallManager.endCall();
        showToast('Appel terminé', 'info');
    },

    createOffer: function() {
        var pc = callState.peerConnection;
        return pc.createOffer()
            .then(offer => pc.setLocalDescription(offer))
            .then(() => {
                // In a real WebRTC, we would send this offer via Mercure to the callee
                // For now, let's assume the callee is waiting for it
            });
    },

    endCall: function() {
        if (callState.currentCall) {
            var formData = new FormData();
            formData.append('call_id', callState.currentCall.id);
            fetch('/api/calls/end', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            }).catch(() => {});
        }

        if (callState.localStream) {
            callState.localStream.getTracks().forEach(track => track.stop());
            callState.localStream = null;
        }

        if (callState.peerConnection) {
            callState.peerConnection.close();
            callState.peerConnection = null;
        }

        callState.isCallActive = false;
        callState.currentCall = null;
        
        var screen = document.getElementById('callScreen');
        if (screen) screen.classList.remove('show');
        
        var ringtone = document.getElementById('ringtoneAudio');
        if (ringtone) {
            ringtone.pause();
            ringtone.currentTime = 0;
        }
    },

    hideCallPopup: function() {
        var popup = document.getElementById('callPopup');
        if (popup) popup.classList.remove('show');
        
        var ringtone = document.getElementById('ringtoneAudio');
        if (ringtone) {
            ringtone.pause();
            ringtone.currentTime = 0;
        }
    },

=======
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

>>>>>>> testsisi
    requestMedia: function(callType) {
        var constraints = {
            audio: true,
            video: callType === 'video'
        };
<<<<<<< HEAD
=======
        
>>>>>>> testsisi
        console.log('Requesting media:', constraints);
        return navigator.mediaDevices.getUserMedia(constraints);
    },

    createPeerConnection: function() {
        var pc = new RTCPeerConnection({
            iceServers: callState.iceServers
        });
        
        pc.onicecandidate = function(event) {
            if (event.candidate) {
<<<<<<< HEAD
                // In real app, send ICE candidate via Mercure
=======
                console.log('ICE candidate:', event.candidate);
                CallManager.sendIceCandidate(event.candidate);
>>>>>>> testsisi
            }
        };
        
        pc.ontrack = function(event) {
            console.log('Received remote track');
            callState.remoteStream = event.streams[0];
<<<<<<< HEAD
            var remoteVideo = document.getElementById('remoteVideo');
            if (remoteVideo) remoteVideo.srcObject = event.streams[0];
            CallManager.showCallScreen('connected');
        };
        
        if (callState.localStream) {
            callState.localStream.getTracks().forEach(track => {
=======
            
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
>>>>>>> testsisi
                pc.addTrack(track, callState.localStream);
            });
        }
        
        callState.peerConnection = pc;
<<<<<<< HEAD
=======
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
>>>>>>> testsisi
    },

    showCallScreen: function(status) {
        var screen = document.getElementById('callScreen');
        var statusEl = document.getElementById('callStatus');
<<<<<<< HEAD
        if (screen) {
            screen.classList.add('show');
            if (status === 'connecting') statusEl.textContent = 'Connexion...';
            else if (status === 'ringing') statusEl.textContent = 'Sonne...';
            else if (status === 'connected') {
=======
        
        if (screen) {
            screen.classList.add('show');
            
            if (status === 'connecting') {
                statusEl.textContent = 'Connexion...';
            } else if (status === 'ringing') {
                statusEl.textContent = 'Sonne...';
            } else if (status === 'connected') {
>>>>>>> testsisi
                statusEl.textContent = 'En appel';
                callState.callStartTime = Date.now();
                CallManager.startDurationTimer();
            }
        }
    },

    startDurationTimer: function() {
        if (callState.durationInterval) clearInterval(callState.durationInterval);
<<<<<<< HEAD
=======
        
>>>>>>> testsisi
        callState.durationInterval = setInterval(function() {
            var duration = Math.floor((Date.now() - callState.callStartTime) / 1000);
            var min = Math.floor(duration / 60);
            var sec = duration % 60;
<<<<<<< HEAD
            document.getElementById('callDuration').textContent = ('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2);
        }, 1000);
    },

=======
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

>>>>>>> testsisi
    toggleMute: function() {
        if (callState.localStream) {
            var audioTrack = callState.localStream.getAudioTracks()[0];
            if (audioTrack) {
                audioTrack.enabled = !audioTrack.enabled;
                callState.isMuted = !audioTrack.enabled;
<<<<<<< HEAD
=======
                
>>>>>>> testsisi
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
<<<<<<< HEAD
=======
                
>>>>>>> testsisi
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
<<<<<<< HEAD
=======
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
>>>>>>> testsisi
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