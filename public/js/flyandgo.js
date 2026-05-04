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
<<<<<<< HEAD
function toggleFavori(id, btn) {
    return toggleFavorite(id, 'hebergement', btn);
}

=======
>>>>>>> testsisi
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
