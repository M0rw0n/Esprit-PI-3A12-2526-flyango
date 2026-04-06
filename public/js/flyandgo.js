<<<<<<< HEAD
/* FLY&GO JS v5.0 */
function showToast(msg,type='success',ms=3800){const c=document.getElementById('toastBox');if(!c)return;const icons={success:'✅',error:'❌',info:'ℹ️'};const el=document.createElement('div');el.className=`toast toast--${type}`;el.innerHTML=`<span>${icons[type]||'✅'}</span> ${msg}`;c.appendChild(el);setTimeout(()=>{el.style.transition='all .35s ease';el.style.opacity='0';el.style.transform='translateX(12px)';setTimeout(()=>el.remove(),380);},ms);}
function openModal(id){const el=document.getElementById(id);if(el){el.classList.add('open');document.body.style.overflow='hidden';}}
function closeModal(id){const el=document.getElementById(id);if(el){el.classList.remove('open');document.body.style.overflow='';}}

/* Generic Wishlist Toggle */
function toggleWishlist(btn, id, type) {
    if (btn) {
        btn.classList.toggle('on');
        const isOn = btn.classList.contains('on');
        showToast(
            isOn ? '❤️ Ajouté à la wishlist' : 'Retiré de la wishlist',
            isOn ? 'success' : 'info'
        );
    }
}
document.addEventListener('click',e=>{if(e.target.classList.contains('modal-ov')){e.target.classList.remove('open');document.body.style.overflow='';}});
window.addEventListener('scroll',()=>{document.getElementById('fgNav')?.classList.toggle('fg-nav--scrolled',window.scrollY>60);},{passive:true});
document.addEventListener('DOMContentLoaded',()=>{
    const burger=document.getElementById('fgBurger'),mobile=document.getElementById('fgMobile');
    if(burger)burger.addEventListener('click',()=>{burger.classList.toggle('open');mobile?.classList.toggle('open');});
    
    /* User dropdown menu */
    const userBtn=document.getElementById('fgUserBtn'),userMenu=document.querySelector('.fg-user-menu');
    if(userBtn)userBtn.addEventListener('click',e=>{e.stopPropagation();userMenu?.classList.toggle('open');});
    document.addEventListener('click',e=>{if(!e.target.closest('.fg-user-menu'))userMenu?.classList.remove('open');});
    const notifBtn=document.getElementById('notifBtn'),notifPanel=document.getElementById('notifPanel');
    const wishlistBtn=document.getElementById('wishlistBtn'),wishlistPanel=document.getElementById('wishlistPanel');
    const overlay=document.getElementById('fgOverlay');
    if(notifBtn)notifBtn.addEventListener('click',e=>{e.stopPropagation();const open=notifPanel.classList.toggle('open');wishlistPanel?.classList.remove('open');overlay?.classList.toggle('open',open);});
    if(wishlistBtn)wishlistBtn.addEventListener('click',e=>{e.stopPropagation();const open=wishlistPanel.classList.toggle('open');notifPanel?.classList.remove('open');overlay?.classList.toggle('open',open);});
    overlay?.addEventListener('click',()=>{notifPanel?.classList.remove('open');wishlistPanel?.classList.remove('open');document.getElementById('langDrop')?.classList.remove('open');overlay.classList.remove('open');});
    const langBtn=document.getElementById('langBtn2'),langDrop=document.getElementById('langDrop');
    if(langBtn)langBtn.addEventListener('click',e=>{e.stopPropagation();langDrop.classList.toggle('open');overlay?.classList.toggle('open',langDrop.classList.contains('open'));});
    document.addEventListener('click',e=>{if(!e.target.closest('.fg-lang'))langDrop?.classList.remove('open');});
    renderWishlist();
    wishlist.forEach(w=>{document.querySelectorAll(`[data-id="${w.id}"] .acard__wish`).forEach(b=>b.classList.add('on'));});
    const obs=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target);}});},{threshold:.08,rootMargin:'0px 0px -40px 0px'});
    document.querySelectorAll('.acard,.dc,.fc,.fpost,.cat').forEach(el=>obs.observe(el));
    const cObs=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){const target=parseInt(e.target.dataset.target||0),suffix=e.target.dataset.suffix||'';let cur=0;const step=target/80;const t=setInterval(()=>{cur=Math.min(cur+step,target);e.target.textContent=Math.floor(cur).toLocaleString()+suffix;if(cur>=target)clearInterval(t);},20);cObs.unobserve(e.target);}});},{threshold:.6});
    document.querySelectorAll('[data-target]').forEach(el=>cObs.observe(el));
    document.querySelectorAll('a[href^="#"]').forEach(a=>{a.addEventListener('click',e=>{const t=document.querySelector(a.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'});}});});
    const scrollBtn=document.createElement('button');scrollBtn.innerHTML='<i class="fa fa-arrow-up"></i>';Object.assign(scrollBtn.style,{position:'fixed',bottom:'2rem',left:'2rem',zIndex:'500',background:'linear-gradient(135deg,var(--navy),var(--navy-dd))',color:'#fff',border:'none',width:'42px',height:'42px',borderRadius:'50%',cursor:'pointer',fontSize:'.88rem',boxShadow:'0 4px 14px rgba(27,58,107,.38)',opacity:'0',transition:'all .3s ease',display:'flex',alignItems:'center',justifyContent:'center'});document.body.appendChild(scrollBtn);scrollBtn.addEventListener('click',()=>window.scrollTo({top:0,behavior:'smooth'}));window.addEventListener('scroll',()=>{const show=window.scrollY>500;scrollBtn.style.opacity=show?'1':'0';scrollBtn.style.pointerEvents=show?'auto':'none';},{passive:true});
});
let wishlist=[];try{wishlist=JSON.parse(localStorage.getItem('fg_wl')||'[]');}catch(e){}
function renderWishlist(){const c=document.getElementById('wlItems'),b=document.getElementById('wlDot');if(!c)return;if(b){b.textContent=wishlist.length;b.style.display=wishlist.length?'flex':'none';}if(!wishlist.length){c.innerHTML=`<div class="fg-wl-empty"><i class="fa fa-heart-broken"></i><p>Wishlist vide</p><a href="/activites" class="btn btn-gold btn-sm" style="margin-top:.25rem">Découvrir</a></div>`;return;}c.innerHTML=wishlist.map(w=>`<div class="fg-wl-item"><img src="${w.image?'/'+w.image:'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&q=60'}" alt="${w.title}" onerror="this.src='https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=100&q=60'"><div class="fg-wl-item__info"><strong>${w.title}</strong><span>${parseFloat(w.price).toLocaleString('fr-TN',{minimumFractionDigits:3})} TND/pers.</span></div><button class="fg-wl-item__rm" onclick="removeFromWishlist(${w.id})"><i class="fa fa-times"></i></button></div>`).join('');}
function toggleWishlistItem(id,title,price,image){const idx=wishlist.findIndex(w=>w.id===id);if(idx===-1){wishlist.push({id,title,price,image});showToast(`❤️ "${title}" ajouté !`);}else{wishlist.splice(idx,1);showToast('Retiré de la wishlist','info');}try{localStorage.setItem('fg_wl',JSON.stringify(wishlist));}catch(e){}renderWishlist();document.querySelectorAll(`[data-id="${id}"] .acard__wish`).forEach(b=>b.classList.toggle('on',wishlist.some(w=>w.id===id)));}
function removeFromWishlist(id){wishlist=wishlist.filter(w=>w.id!==id);try{localStorage.setItem('fg_wl',JSON.stringify(wishlist));}catch(e){}renderWishlist();showToast('Retiré','info');}
let currentLang='fr';
async function switchLang(lang){if(lang===currentLang){document.getElementById('langDrop')?.classList.remove('open');return;}currentLang=lang;const names={fr:'FR',en:'EN',ar:'AR',de:'DE'};document.getElementById('currentLang').textContent=names[lang]||lang.toUpperCase();document.getElementById('langDrop')?.classList.remove('open');document.getElementById('fgOverlay')?.classList.remove('open');if(lang==='fr'){location.reload();return;}showToast(`Traduction ${lang.toUpperCase()}…`,'info');try{const els=document.querySelectorAll('h1,h2,h3,p,.acard__name,.acard__desc');const texts=[...els].map(e=>e.innerText).filter(t=>t.trim().length>2);const res=await fetch('/translate',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({texts,target_language:lang})});if(!res.ok)throw new Error();const data=await res.json();if(data.translated_texts){els.forEach((e,i)=>{if(data.translated_texts[i])e.innerText=data.translated_texts[i];});showToast('✅ Traduction terminée !');}}catch{showToast('Traduction indisponible','error');}}
function setRating(n){document.getElementById('ratingInput').value=n;document.querySelectorAll('#starPick .fa').forEach((s,i)=>{s.classList.toggle('on',i<n);s.style.color=i<n?'#F59E0B':'var(--border)';});}
function togglePanel(id){document.getElementById(id)?.classList.toggle('open');}
function clearNotifs(){document.getElementById('notifList').innerHTML='<div style="padding:2rem;text-align:center;color:var(--muted);font-size:.875rem">✅ Aucune notification</div>';document.getElementById('notifDot').style.display='none';showToast('Effacé','info');}
function toggleSidebar(){document.getElementById('adminSide')?.classList.toggle('open');}

/* ═══════ EXTENSIONS ═══════ */
function switchTab(id){document.querySelectorAll('.detail-tab,.tab-pane').forEach(el=>el.classList.remove('on'));document.querySelector('[data-tab="'+id+'"]')?.classList.add('on');document.getElementById('pane-'+id)?.classList.add('on');}
function setRatingAvis(n){const inp=document.getElementById('noteInput');if(inp)inp.value=n;document.querySelectorAll('#starPickAvis .fa').forEach((s,i)=>{s.style.color=i<n?'#F59E0B':'var(--border)';});}
function calcNuits(){const d1=document.getElementById('dateDebut'),d2=document.getElementById('dateFin'),res=document.getElementById('nuitesResult'),mont=document.getElementById('montantCalc');if(!d1||!d2)return;const t1=new Date(d1.value),t2=new Date(d2.value);if(t1&&t2&&t2>t1){const n=Math.round((t2-t1)/(1000*3600*24));const prix=parseFloat(document.getElementById('prixBase')?.value||0);if(res)res.textContent=n+' nuit'+(n>1?'s':'');if(mont)mont.textContent=(n*prix).toLocaleString('fr-TN',{minimumFractionDigits:3})+' TND';}}
function calcMontantCircuit(){const n=parseInt(document.getElementById('nbPersonnes')?.value||1),prix=parseFloat(document.getElementById('prixCircuit')?.value||0),el=document.getElementById('montantCircuit');if(el)el.textContent=(n*prix).toLocaleString('fr-TN',{minimumFractionDigits:3})+' TND';}
function calcMontantBooking(){const n=parseInt(document.getElementById('persons')?.value||1),prix=parseFloat(document.getElementById('prixActivity')?.value||0),el=document.getElementById('montantBooking');if(el)el.textContent=(n*prix).toLocaleString('fr-TN',{minimumFractionDigits:3})+' TND';}

/* Dashboard Charts */
function initDashboardCharts(revenusData,villesData){
  if(document.getElementById('chartRevenus')&&revenusData){
    new Chart(document.getElementById('chartRevenus'),{type:'line',data:{labels:revenusData.labels,datasets:[{label:'Revenus (TND)',data:revenusData.values,borderColor:'var(--cyan)',backgroundColor:'rgba(0,180,216,0.1)',borderWidth:2.5,tension:.4,fill:true,pointBackgroundColor:'var(--cyan)',pointRadius:4}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'var(--border)'},ticks:{callback:v=>v.toLocaleString()+' TND'}},x:{grid:{display:false}}}}});
  }
  if(document.getElementById('chartVilles')&&villesData){
    new Chart(document.getElementById('chartVilles'),{type:'doughnut',data:{labels:villesData.labels,datasets:[{data:villesData.values,backgroundColor:['#1B3A6B','#00B4D8','#FFB700','#10B981','#EF4444','#F97316','#8B5CF6'],borderWidth:0}]},options:{responsive:true,plugins:{legend:{position:'bottom',labels:{padding:16,font:{size:12}}}}}});
  }
}

/* ═══════ AJAX FAVORITES (ALL MODULES) ═══════ */
function toggleFavorite(id, type, btn) {
    if (!btn) btn = document.querySelector(`[data-id="${id}"] .favorite-btn`);
    fetch(`/ajax/favorite/${type}/${id}`, { method: 'POST' })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                btn?.classList.toggle('active', d.favorited);
                showToast(d.message, 'success');
                if (!d.favorited && window.location.pathname.includes('favoris')) {
                    const card = document.querySelector(`[data-id="${id}"]`);
                    card?.classList.add('fade-out');
                    setTimeout(() => card?.remove(), 300);
                }
            } else {
                showToast(d.message || 'Erreur', 'error');
            }
        })
        .catch(() => showToast('Erreur de connexion', 'error'));
}

/* ═══════ AJAX LIKES/DISLIKES ═══════ */
function toggleLike(id, type, btn, isLike = true) {
    const vote = isLike ? 1 : -1;
    const container = btn?.closest('.like-container');
    
    fetch(`/ajax/like/${type}/${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `vote=${vote}`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success && container) {
            container.querySelector('.likes-count').textContent = d.likes;
            container.querySelector('.dislikes-count').textContent = d.dislikes;
            
            const likeBtn = container.querySelector('.like-btn');
            const dislikeBtn = container.querySelector('.dislike-btn');
            
            likeBtn?.classList.toggle('active', d.userVote === 1);
            dislikeBtn?.classList.toggle('active', d.userVote === -1);
        }
    })
    .catch(() => showToast('Erreur de connexion', 'error'));
}

/* ═══════ AVAILABILITY CHECK ═══════ */
let availabilityTimeout;
function checkAvailability() {
    clearTimeout(availabilityTimeout);
    availabilityTimeout = setTimeout(() => {
        const hebId = document.getElementById('hebergementId')?.value;
        const dateDebut = document.getElementById('dateDebut')?.value;
        const dateFin = document.getElementById('dateFin')?.value;
        const availMsg = document.getElementById('availabilityMsg');
        
        if (!hebId || !dateDebut || !dateFin) return;
        
        fetch('/ajax/check-availability', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `hebergement_id=${hebId}&date_debut=${dateDebut}&date_fin=${dateFin}`
        })
        .then(r => r.json())
        .then(d => {
            if (availMsg) {
                availMsg.textContent = d.message;
                availMsg.className = 'availability-msg ' + (d.available ? 'available' : 'unavailable');
            }
        });
    }, 500);
}

/* ═══════ PROMO CODE ═══════ */
function applyPromoCode() {
    const code = document.getElementById('promoCodeInput')?.value;
    const price = parseFloat(document.getElementById('originalPrice')?.value || 0);
    const promoMsg = document.getElementById('promoMsg');
    const finalPriceEl = document.getElementById('finalPrice');
    const reductionEl = document.getElementById('reductionAmount');
    
    if (!code) { showToast('Entrez un code promo', 'error'); return; }
    
    fetch('/ajax/apply-promo', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `code=${encodeURIComponent(code)}&price=${price}`
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            if (promoMsg) {
                promoMsg.textContent = d.message;
                promoMsg.className = 'promo-success';
            }
            if (reductionEl) reductionEl.textContent = `-${d.reduction.toFixed(3)} TND`;
            if (finalPriceEl) finalPriceEl.textContent = `${d.finalPrice.toFixed(3)} TND`;
            showToast(d.message, 'success');
        } else {
            if (promoMsg) {
                promoMsg.textContent = d.message;
                promoMsg.className = 'promo-error';
            }
            showToast(d.message, 'error');
        }
    })
    .catch(() => showToast('Erreur de connexion', 'error'));
}

/* ═══════ DEBOUNCE SEARCH ═══════ */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/* ═══════ NIGHTS CALCULATOR (Enhanced) ═══════ */
function calcNuits() {
    const d1 = document.getElementById('dateDebut');
    const d2 = document.getElementById('dateFin');
    const res = document.getElementById('nuitesResult');
    const mont = document.getElementById('montantCalc');
    const promoInput = document.getElementById('promoCodeInput');
    
    if (!d1 || !d2) return;
    
    const t1 = new Date(d1.value);
    const t2 = new Date(d2.value);
    
    if (t1 && t2 && t2 > t1) {
        const n = Math.round((t2 - t1) / (1000 * 3600 * 24));
        const prix = parseFloat(document.getElementById('prixBase')?.value || 0);
        const total = n * prix;
        
        if (res) res.textContent = n + ' nuit' + (n > 1 ? 's' : '');
        if (mont) mont.textContent = total.toLocaleString('fr-TN', { minimumFractionDigits: 3 }) + ' TND';
        
        checkAvailability();
        
        if (promoInput?.value) {
            document.getElementById('originalPrice').value = total;
            applyPromoCode();
        }
    }
}
=======
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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
