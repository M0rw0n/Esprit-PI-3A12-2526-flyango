// Simple notification polling
(function() {
    'use strict';
    
    var NOTIF = {
        lastId: 0,
        interval: null,
        
        init: function() {
            var self = this;
            this.lastId = parseInt(localStorage.getItem('notif_lastId') || '0');
            this.check();
            this.interval = setInterval(function() { self.check(); }, 2000);
            console.log('Notifications: initialized, lastId:', this.lastId);
        },
        
        check: function() {
            var self = NOTIF;
            fetch('/sse/hebergement?lastId=' + this.lastId + '&t=' + Date.now())
                .then(function(r) { return r.text(); })
                .then(function(text) {
                    if (!text || text.length < 5) return;
                    var lines = text.split('\n');
                    for (var i = 0; i < lines.length; i++) {
                        var line = lines[i].trim();
                        if (line.indexOf('data:') === 0) {
                            try {
                                var data = JSON.parse(line.substring(5));
                                self.showToast(data.message || 'Notification!', 'success');
                                self.lastId++;
                                localStorage.setItem('notif_lastId', self.lastId);
                            } catch(e) {}
                        }
                    }
                })
                .catch(function(e) {});
        },
        
        showToast: function(msg, type) {
            if (!msg) return;
            var colors = {success:'#10B981',info:'#3B82F6',warning:'#F59E0B',error:'#EF4444'};
            var toast = document.createElement('div');
            toast.textContent = msg;
            toast.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:99999;background:' + (colors[type]||colors.success) + ';color:white;padding:15px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);font-family:system-ui,sans-serif;font-size:14px;font-weight:500;';
            document.body.appendChild(toast);
            console.log('Toast shown:', msg);
            setTimeout(function(){toast.remove();}, 5000);
        }
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { NOTIF.init(); });
    } else {
        NOTIF.init();
    }
    
    window.FlyAndGoNotif = NOTIF;
})();