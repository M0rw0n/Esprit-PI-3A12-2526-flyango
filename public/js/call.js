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
        // Mercure connection is handled in messenger.js
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
                // In real app, send ICE candidate via Mercure
            }
        };
        
        pc.ontrack = function(event) {
            console.log('Received remote track');
            callState.remoteStream = event.streams[0];
            var remoteVideo = document.getElementById('remoteVideo');
            if (remoteVideo) remoteVideo.srcObject = event.streams[0];
            CallManager.showCallScreen('connected');
        };
        
        if (callState.localStream) {
            callState.localStream.getTracks().forEach(track => {
                pc.addTrack(track, callState.localStream);
            });
        }
        
        callState.peerConnection = pc;
    },

    showCallScreen: function(status) {
        var screen = document.getElementById('callScreen');
        var statusEl = document.getElementById('callStatus');
        if (screen) {
            screen.classList.add('show');
            if (status === 'connecting') statusEl.textContent = 'Connexion...';
            else if (status === 'ringing') statusEl.textContent = 'Sonne...';
            else if (status === 'connected') {
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
            document.getElementById('callDuration').textContent = ('0' + min).slice(-2) + ':' + ('0' + sec).slice(-2);
        }, 1000);
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