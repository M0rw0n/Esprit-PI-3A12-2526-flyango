/* ═══════════════════════════════════════════════════════
   FLY&GO — GLOBAL JS v2.0
   Toast · Modal · Navbar · Chatbot · Wishlist · Lang · Animations
   ═══════════════════════════════════════════════════════ */

/* ── TOAST ── */
function showToast(message, type = 'success', duration = 3800) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
    const toast = document.createElement('div');
    toast.className = `fg-toast fg-toast--${type}`;
    toast.innerHTML = `<span>${icons[type] || '✅'}</span> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.transition = 'all .35s ease';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px) scale(0.94)';
        setTimeout(() => toast.remove(), 380);
    }, duration);
}

/* ── MODAL ── */
function openModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}
document.addEventListener('click', e => {
    if (e.target.classList.contains('fg-modal-overlay')) {
        e.target.classList.remove('open');
        document.body.style.overflow = '';
    }
});

/* ── NAVBAR ── */
window.addEventListener('scroll', () => {
    const nav = document.getElementById('fgNavbar');
    if (nav) nav.classList.toggle('fg-navbar--scrolled', window.scrollY > 60);
}, { passive: true });

const hamburger  = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
if (hamburger) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu?.classList.toggle('open');
    });
}

/* ── NOTIFICATIONS ── */
const notifToggle = document.getElementById('notifToggle');
const notifPanel  = document.getElementById('notifPanel');
const overlay     = document.getElementById('fgOverlay');

if (notifToggle) {
    notifToggle.addEventListener('click', e => {
        e.stopPropagation();
        const isOpen = notifPanel.classList.toggle('open');
        wishlistPanel?.classList.remove('open');
        langDropdown?.classList.remove('open');
        overlay?.classList.toggle('open', isOpen);
    });
}
function clearNotifs() {
    document.getElementById('notifList').innerHTML =
        '<div style="padding:2rem;text-align:center;color:var(--text-muted);font-size:.875rem">✅ Aucune nouvelle notification</div>';
    const badge = document.getElementById('notifBadge');
    if (badge) badge.style.display = 'none';
    showToast('Notifications effacées', 'info');
}

/* ── WISHLIST ── */
const wishlistToggle = document.getElementById('wishlistToggle');
const wishlistPanel  = document.getElementById('wishlistPanel');
let wishlist = [];
try { wishlist = JSON.parse(localStorage.getItem('fg_wishlist') || '[]'); } catch(e) {}

function renderWishlist() {
    const container = document.getElementById('wishlistItems');
    const badge     = document.getElementById('wishlistBadge');
    if (!container) return;
    if (badge) { badge.textContent = wishlist.length; badge.style.display = wishlist.length ? 'flex' : 'none'; }
    if (!wishlist.length) {
        container.innerHTML = `<div class="fg-wishlist-empty">
            <i class="fa fa-heart-broken"></i>
            <p>Votre wishlist est vide</p>
            <a href="/activity" class="fg-btn fg-btn--orange" style="font-size:.8rem;padding:.5rem 1.1rem;margin-top:.25rem">Découvrir les activités</a>
        </div>`;
        return;
    }
    container.innerHTML = wishlist.map(item => `
        <div class="fg-wishlist-item">
            <img src="${item.image ? '/' + item.image : 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&q=60'}"
                 alt="${item.title}" onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&q=60'">
            <div class="fg-wishlist-item__info">
                <strong>${item.title}</strong>
                <span>${item.price} DT / pers.</span>
            </div>
            <button class="fg-wishlist-item__remove" onclick="removeFromWishlist(${item.id})" title="Retirer">
                <i class="fa fa-times"></i>
            </button>
        </div>`).join('');
}

function toggleWishlistItem(id, title, price, image) {
    const idx = wishlist.findIndex(w => w.id === id);
    if (idx === -1) {
        wishlist.push({ id, title, price, image });
        showToast(`❤️ "${title}" ajouté à la wishlist !`);
        document.querySelectorAll(`[data-id="${id}"] .fg-card__wishlist`).forEach(b => {
            b.classList.add('fg-card__wishlist--active');
        });
    } else {
        wishlist.splice(idx, 1);
        showToast(`"${title}" retiré de la wishlist`, 'info');
        document.querySelectorAll(`[data-id="${id}"] .fg-card__wishlist`).forEach(b => {
            b.classList.remove('fg-card__wishlist--active');
        });
    }
    try { localStorage.setItem('fg_wishlist', JSON.stringify(wishlist)); } catch(e) {}
    renderWishlist();
}

function removeFromWishlist(id) {
    wishlist = wishlist.filter(w => w.id !== id);
    try { localStorage.setItem('fg_wishlist', JSON.stringify(wishlist)); } catch(e) {}
    renderWishlist();
    showToast('Retiré de la wishlist', 'info');
}

function toggleWishlist() { wishlistPanel?.classList.toggle('open'); }

if (wishlistToggle) {
    wishlistToggle.addEventListener('click', e => {
        e.stopPropagation();
        const isOpen = wishlistPanel.classList.toggle('open');
        notifPanel?.classList.remove('open');
        langDropdown?.classList.remove('open');
        overlay?.classList.toggle('open', isOpen);
    });
}

overlay?.addEventListener('click', () => {
    notifPanel?.classList.remove('open');
    wishlistPanel?.classList.remove('open');
    langDropdown?.classList.remove('open');
    overlay.classList.remove('open');
});

document.addEventListener('DOMContentLoaded', () => {
    renderWishlist();
    wishlist.forEach(item => {
        document.querySelectorAll(`[data-id="${item.id}"] .fg-card__wishlist`).forEach(b => b.classList.add('fg-card__wishlist--active'));
    });
});

/* ── LANGUAGE SWITCHER ── */
const langBtn      = document.getElementById('langBtn');
const langDropdown = document.getElementById('langDropdown');
if (langBtn) {
    langBtn.addEventListener('click', e => {
        e.stopPropagation();
        langDropdown.classList.toggle('open');
        notifPanel?.classList.remove('open');
        wishlistPanel?.classList.remove('open');
        overlay?.classList.toggle('open', langDropdown.classList.contains('open'));
    });
}
document.addEventListener('click', e => {
    if (!e.target.closest('.fg-lang-switcher')) langDropdown?.classList.remove('open');
});

let currentLang = 'fr';
async function switchLang(lang) {
    if (lang === currentLang) { langDropdown?.classList.remove('open'); return; }
    currentLang = lang;
    const langNames = { fr: 'FR', en: 'EN', ar: 'AR', de: 'DE' };
    document.getElementById('currentLang').textContent = langNames[lang] || lang.toUpperCase();
    langDropdown?.classList.remove('open');
    overlay?.classList.remove('open');
    if (lang === 'fr') { location.reload(); return; }
    showToast(`Traduction en cours (${lang.toUpperCase()})…`, 'info');
    try {
        const elements = document.querySelectorAll('h1,h2,h3,p,.fg-card__title,.fg-card__desc,.fg-hero__subtitle');
        const texts = Array.from(elements).map(el => el.innerText).filter(t => t.trim().length > 2);
        const res = await fetch('/translate', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ texts, target_language: lang })
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        if (data.translated_texts) {
            elements.forEach((el, i) => { if (data.translated_texts[i]) el.innerText = data.translated_texts[i]; });
            showToast('✅ Traduction terminée !');
        }
    } catch(e) { showToast('Service de traduction indisponible', 'error'); }
}

/* ── CHATBOT ── */
const chatbot     = document.getElementById('chatbot');
const chatbotBody = document.getElementById('chatbotBody');
let chatbotOpen   = false;

const FAQ = [
    { keys: ['tunisie','carthage','sidi bou','djerba','sahara','medina','site','monument','visiter'],
      answer: '🏛️ <strong>Top destinations Tunisie :</strong><br>• Carthage — ruines puniques, UNESCO<br>• Sidi Bou Saïd — village bleu et blanc<br>• Sahara de Douz — dunes dorées 🏜️<br>• Djerba — île paradisiaque 🏝️<br>• Médina de Tunis — souks millénaires<br>• Kairouan — ville sainte, mosquées historiques' },
    { keys: ['budget','prix','cout','combien','argent','tarif'],
      answer: '💰 <strong>Budget Tunisie :</strong><br>• Hôtel 3★ : 30–60€/nuit<br>• Restaurant local : 5–15€/repas<br>• Transports : 2–10€/jour<br>• Sites touristiques : 2–8€<br><br>✨ Total estimé : <strong>30–60€/jour</strong>' },
    { keys: ['visa','passeport','document','formalit','entrée'],
      answer: '📋 <strong>Visa Tunisie :</strong><br>• 🇫🇷 Français : pas de visa (< 3 mois)<br>• 🇪🇺 UE : pas de visa requis<br>• Passeport valide obligatoire (6 mois min.)<br>• Assurance voyage recommandée' },
    { keys: ['sécurité','conseil','danger','safe','seul'],
      answer: '🛡️ <strong>Conseils sécurité :</strong><br>• Partagez votre itinéraire<br>• Gardez copies numériques de vos docs<br>• SIM locale à l\'arrivée<br>• Assurance voyage complète<br>• ✈️ Fly&Go vous accompagne à chaque étape !' },
    { keys: ['réserv','booking','activité','excursion','comment'],
      answer: '🎯 <strong>Réserver une activité :</strong><br>1. Parcourez notre catalogue<br>2. Choisissez votre expérience<br>3. Sélectionnez date et personnes<br>4. Remplissez le formulaire<br><br>👉 <a href="/activity" style="color:var(--blue);font-weight:700">Voir les activités</a>' },
    { keys: ['météo','saison','période','quand','climat','meilleur'],
      answer: '🌤️ <strong>Meilleure période :</strong><br>• 🌸 Printemps (mars–mai) : idéal !<br>• 🍂 Automne (sept–nov) : parfait aussi<br>• ☀️ Été : chaud, surtout le Sahara<br>• ❄️ Hiver : doux sur la côte<br><br>💡 Évitez juillet–août pour le désert' },
    { keys: ['bonjour','salut','hello','bonsoir','coucou','aide','help','start'],
      answer: '👋 Bonjour ! Je suis votre assistant <strong>Fly&Go</strong>.<br>Je peux vous aider avec :<br>• 🏛️ Destinations Tunisie<br>• 💰 Budget et prix<br>• 📋 Formalités et visa<br>• 🎯 Réservations<br>• 🛡️ Conseils sécurité' },
];

function toggleChatbot() {
    chatbotOpen = !chatbotOpen;
    chatbot?.classList.toggle('open', chatbotOpen);
    const btn = document.getElementById('chatbotToggle');
    if (btn) btn.innerHTML = chatbotOpen
        ? '<i class="fa fa-times"></i>'
        : '<i class="fa fa-comments"></i><span class="fg-chatbot__toggle-badge">FAQ</span>';
    if (chatbotOpen && chatbotBody) chatbotBody.scrollTop = chatbotBody.scrollHeight;
}

function askBot(q) {
    addMessage(q, 'user');
    document.getElementById('chatbotSuggestions')?.remove();
    showTyping();
    setTimeout(() => { hideTyping(); processBot(q); }, 800 + Math.random() * 400);
}

function sendChatbot() {
    const input = document.getElementById('chatbotInput');
    const q = input?.value.trim();
    if (!q) return;
    input.value = '';
    addMessage(q, 'user');
    document.getElementById('chatbotSuggestions')?.remove();
    showTyping();
    setTimeout(() => { hideTyping(); processBot(q); }, 800 + Math.random() * 500);
}

function processBot(q) {
    const lower = q.toLowerCase();
    let answer = null;
    for (const faq of FAQ) {
        if (faq.keys.some(k => lower.includes(k))) { answer = faq.answer; break; }
    }
    if (!answer) answer = `Je n'ai pas trouvé de réponse précise.<br><br>Essayez : <em>budget, visa, destinations, météo, réservation</em><br><br>📞 Contactez-nous : <strong>+216 71 000 000</strong>`;
    addMessage(answer, 'bot');
}

function addMessage(text, sender) {
    if (!chatbotBody) return;
    const div = document.createElement('div');
    div.className = `fg-chatbot__msg fg-chatbot__msg--${sender}`;
    const bubble = document.createElement('div');
    bubble.className = 'fg-chatbot__bubble';
    bubble.innerHTML = text.replace(/\n/g, '<br>');
    div.appendChild(bubble);
    chatbotBody.appendChild(div);
    chatbotBody.scrollTop = chatbotBody.scrollHeight;
}

function showTyping() {
    const t = document.createElement('div');
    t.className = 'fg-chatbot__typing'; t.id = 'chatTyping';
    t.innerHTML = '<span></span><span></span><span></span>';
    chatbotBody?.appendChild(t);
    chatbotBody.scrollTop = chatbotBody.scrollHeight;
}
function hideTyping() { document.getElementById('chatTyping')?.remove(); }

document.getElementById('chatbotInput')?.addEventListener('keydown', e => { if (e.key === 'Enter') sendChatbot(); });

/* ── STAR RATING ── */
function setRating(n) {
    const input = document.getElementById('ratingInput');
    if (input) input.value = n;
    document.querySelectorAll('#starPicker .fa').forEach((s, i) => {
        s.classList.toggle('active', i < n);
        s.style.color = i < n ? '#fbbf24' : '#e2e8f0';
    });
}

/* ── SCROLL TO TOP ── */
const scrollBtn = document.createElement('button');
scrollBtn.innerHTML = '<i class="fa fa-arrow-up"></i>';
scrollBtn.title = 'Retour en haut';
Object.assign(scrollBtn.style, {
    position: 'fixed', bottom: '6.5rem', left: '1.5rem', zIndex: '500',
    background: 'linear-gradient(135deg, var(--blue), var(--blue-deeper))',
    color: '#fff', border: 'none', width: '44px', height: '44px', borderRadius: '50%',
    cursor: 'pointer', fontSize: '.9rem',
    boxShadow: '0 4px 16px rgba(13,91,215,.4)',
    opacity: '0', transition: 'all .3s ease',
    display: 'flex', alignItems: 'center', justifyContent: 'center'
});
document.body.appendChild(scrollBtn);
scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
window.addEventListener('scroll', () => {
    const show = window.scrollY > 400;
    scrollBtn.style.opacity = show ? '1' : '0';
    scrollBtn.style.pointerEvents = show ? 'auto' : 'none';
    scrollBtn.style.transform = show ? 'none' : 'translateY(10px)';
}, { passive: true });

/* ── INTERSECTION OBSERVER — entrance animations ── */
document.addEventListener('DOMContentLoaded', () => {
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('fg-visible');
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.fg-card, .fg-dest-card, .fg-forum-card, .fg-forum-card--full, .fg-band__item, .fg-review-card').forEach(el => obs.observe(el));
});

/* ── NUMBER COUNTER ANIMATION ── */
function animateCounter(el, target, duration = 2000) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) { el.textContent = target + (el.dataset.suffix || ''); clearInterval(timer); }
        else el.textContent = Math.floor(start) + (el.dataset.suffix || '');
    }, 16);
}
const counterObs = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            const target = parseInt(e.target.dataset.target || 0);
            animateCounter(e.target, target);
            counterObs.unobserve(e.target);
        }
    });
}, { threshold: 0.5 });
document.querySelectorAll('[data-target]').forEach(el => counterObs.observe(el));

/* ── SMOOTH ANCHORS ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

/* ── PARTICLES (hero) ── */
function createParticles() {
    const container = document.querySelector('.fg-hero__particles');
    if (!container) return;
    for (let i = 0; i < 18; i++) {
        const p = document.createElement('div');
        p.className = 'fg-particle';
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${2 + Math.random() * 4}px;
            height: ${2 + Math.random() * 4}px;
            animation-duration: ${6 + Math.random() * 10}s;
            animation-delay: ${Math.random() * 6}s;
            opacity: ${0.2 + Math.random() * 0.5};
        `;
        container.appendChild(p);
    }
}
document.addEventListener('DOMContentLoaded', createParticles);
