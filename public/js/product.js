const star='<svg viewBox="0 0 24 24"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>';
const starDim='<svg viewBox="0 0 24 24" style="fill:var(--border)"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>';
document.getElementById('titleStars').innerHTML=star.repeat(5);
document.getElementById('revStars').innerHTML=star.repeat(5);
document.querySelectorAll('.rev-stars[data-stars]').forEach(el=>{ const n=+el.dataset.stars; el.innerHTML=star.repeat(n)+starDim.repeat(5-n); });

/* ── gallery thumbnails ── */
const VIEWS=[
  {label:'lid closed', svg:'<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>'},
  {label:'open 3/4', svg:'<path d="M3 5h18v10H3z"/><path d="M1 19h22l-2-4H3z"/>'},
  {label:'ports', svg:'<rect x="2" y="9" width="6" height="4" rx="1"/><rect x="10" y="9" width="5" height="4" rx="1"/><rect x="17" y="9" width="5" height="4" rx="1"/>'},
  {label:'keyboard', svg:'<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M6 9h.01M10 9h.01M14 9h.01M18 9h.01M6 13h.01M18 13h.01M9 13h6"/>'},
];
const thumbs=document.getElementById('thumbs'), stagePh=document.getElementById('stagePh');
function paintStage(i){ const v=VIEWS[i]; stagePh.innerHTML=`<span class="ph-label">dell latitude · ${v.label}</span><svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">${v.svg}</svg>`;
  [...thumbs.children].forEach((t,j)=>t.classList.toggle('on',j===i)); }
VIEWS.forEach((v,i)=>{ const t=document.createElement('div'); t.className='thumb'+(i===0?' on':''); t.innerHTML=`<svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">${v.svg}</svg>`; t.addEventListener('click',()=>paintStage(i)); thumbs.appendChild(t); });

/* ── favourite ── */
document.getElementById('favBtn').addEventListener('click',function(){ this.classList.toggle('on'); toast(this.classList.contains('on')?'Saved to wishlist':'Removed from wishlist'); });

/* ── grade selector ── */
const GRADES={ A:{price:'₹14,999',off:'67% off · Grade A',stamp:'GRADE A · LIKE NEW',cls:'gs-a',pct:'67% OFF'},
  B:{price:'₹12,999',off:'71% off · Grade B',stamp:'GRADE B · GOOD',cls:'gs-b',pct:'71% OFF'},
  C:{price:'₹10,999',off:'76% off · Grade C',stamp:'GRADE C · FUNCTIONAL',cls:'gs-c',pct:'76% OFF'} };
document.querySelectorAll('#gradeOpts .gopt').forEach(opt=>opt.addEventListener('click',()=>{
  document.querySelectorAll('#gradeOpts .gopt').forEach(o=>o.classList.remove('sel')); opt.classList.add('sel');
  const d=GRADES[opt.dataset.grade];
  document.getElementById('priceNow').textContent=d.price;
  document.getElementById('priceOff').textContent=d.pct;
  document.getElementById('bbNow').textContent=d.price;
  document.getElementById('bbOff').textContent=d.off;
  const stamp=document.getElementById('stageStamp'); stamp.textContent=d.stamp; stamp.className='stage-stamp '+d.cls;
}));

/* ── pincode ── */
document.getElementById('pinChk').addEventListener('click',()=>{ document.getElementById('delivOk').style.display='flex'; toast('Delivery available!'); });

/* ── spec accordions ── */
document.querySelectorAll('[data-spec] .spec-head').forEach(h=>h.addEventListener('click',()=>h.parentElement.classList.toggle('open')));

/* ── cart ── */
let cart=0;
const cartBtn=document.getElementById('cartBtn');
function setCart(n){ cart=n; cartBtn.textContent=`🛒 Cart (${n})`; }
function toast(msg){ const t=document.getElementById('toast'); document.getElementById('toastMsg').textContent=msg; t.classList.add('show'); clearTimeout(t._t); t._t=setTimeout(()=>t.classList.remove('show'),1800); }
function addToCart(btn){ setCart(cart+1); if(btn){ btn.classList.add('added'); const html=btn.innerHTML; setTimeout(()=>{ btn.classList.remove('added'); },1200); } toast('Added to cart'); }
document.getElementById('actCart').addEventListener('click',function(){ addToCart(this); });
document.getElementById('bbCart').addEventListener('click',function(){ addToCart(this); });
document.querySelector('.act-buy').addEventListener('click',()=>toast('Proceeding to checkout…'));
document.querySelector('.bb-buy').addEventListener('click',()=>toast('Proceeding to checkout…'));

/* ── related ── */
const ICONS={laptop:'<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>',desktop:'<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>'};
const REL=[
  {brand:'HP',name:'EliteBook 840 G6',grade:'a',chips:['i7 8th','16GB','512GB'],price:'₹22,500',mrp:'₹72,000',save:'69% off',icon:'laptop'},
  {brand:'Lenovo',name:'ThinkPad T480',grade:'a',chips:['i5 8th','8GB','256GB'],price:'₹16,999',mrp:'₹52,000',save:'67% off',icon:'laptop'},
  {brand:'Dell',name:'OptiPlex 7060 SFF',grade:'b',chips:['i5 8th','16GB','512GB'],price:'₹13,499',mrp:'₹40,000',save:'66% off',icon:'desktop'},
  {brand:'HP',name:'ProBook 450 G7',grade:'b',chips:['i5 10th','8GB','256GB'],price:'₹18,250',mrp:'₹58,000',save:'68% off',icon:'laptop'},
];
document.getElementById('rel').innerHTML=REL.map(p=>`
  <div class="prod-card">
    <div class="prod-img">
      <div class="ph"><span class="ph-label">${p.brand.toLowerCase()} · product shot</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">${ICONS[p.icon]}</svg></div>
      <span class="grade-stamp gs-${p.grade}">GRADE ${p.grade.toUpperCase()}</span>
      <span class="stock-live">In stock</span>
      <button class="wish-btn">♡</button>
    </div>
    <div class="prod-body">
      <div class="prod-brand">${p.brand}</div>
      <div class="prod-name">${p.name}</div>
      <div class="prod-chips">${p.chips.map(c=>`<span class="prod-chip">${c}</span>`).join('')}</div>
      <div class="prod-pricing"><span class="prod-price">${p.price}</span><span class="prod-mrp">${p.mrp}</span><span class="prod-save">${p.save}</span></div>
      <div class="prod-warranty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg> 6-month warranty included</div>
      <div class="prod-footer"><button class="prod-btn-add" onclick="addToCart(this)">Add to cart</button><button class="prod-btn-view">View</button></div>
    </div>
  </div>`).join('');
