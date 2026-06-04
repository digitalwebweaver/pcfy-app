/* ── live clock ── */
function tick(){ const d=new Date(); let h=d.getHours()%12||12; const m=String(d.getMinutes()).padStart(2,'0'); document.getElementById('clock').textContent=`${h}:${m}`; }
tick(); setInterval(tick,10000);

/* ── promo dots ── */
const promo=document.getElementById('promo'), dots=document.getElementById('dots');
for(let i=0;i<3;i++){ const d=document.createElement('div'); d.className='dot'+(i===0?' on':''); dots.appendChild(d); }
promo.addEventListener('scroll',()=>{ const i=Math.round(promo.scrollLeft/promo.clientWidth); [...dots.children].forEach((d,j)=>d.classList.toggle('on',j===i)); });

/* ── featured products ── */
const ICONS={
  laptop:'<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>',
  work:'<rect x="3" y="3" width="18" height="14" rx="2"/><path d="M7 21h10M9 17v4M15 17v4"/>',
  server:'<rect x="2" y="3" width="20" height="5" rx="1"/><rect x="2" y="10" width="20" height="5" rx="1"/><rect x="2" y="17" width="20" height="5" rx="1"/>',
  desktop:'<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
};
const FEAT=[
  {brand:'Dell',name:'Latitude 5490',grade:'a',chips:['i5 8th','8GB','256GB SSD'],price:'₹14,999',mrp:'₹45,000',icon:'laptop'},
  {brand:'HP',name:'EliteBook 840 G6',grade:'a',chips:['i7 8th','16GB','512GB'],price:'₹22,500',mrp:'₹72,000',icon:'laptop'},
  {brand:'HP',name:'ProLiant DL380 G9',grade:'a',chips:['2× Xeon','64GB','4× 600GB'],price:'₹42,000',mrp:'₹1,80,000',icon:'server'},
  {brand:'Dell',name:'Precision 5540',grade:'b',chips:['i7 9th','32GB','Quadro'],price:'₹38,999',mrp:'₹1,05,000',icon:'work'},
];
document.getElementById('rail').innerHTML=FEAT.map(p=>`
  <div class="pcard" onclick="if(!event.target.closest('.padd'))location.href='Product Page.html'">
    <div class="pcard-img">
      <div class="ph"><span class="ph-label">${p.brand.toLowerCase()} · shot</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">${ICONS[p.icon]}</svg></div>
      <span class="pstamp ${p.grade}">GRADE ${p.grade.toUpperCase()}</span>
      <span class="pstock">In stock</span>
    </div>
    <div class="pbody">
      <div class="pbrand">${p.brand}</div>
      <div class="pname">${p.name}</div>
      <div class="pchips">${p.chips.map(c=>`<span class="pchip">${c}</span>`).join('')}</div>
      <div class="pprice"><span class="p">${p.price}</span><span class="m">${p.mrp}</span></div>
      <button class="padd"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add to cart</button>
    </div>
  </div>`).join('');

/* ── cart ── */
let cart=0;
function setCart(n){ cart=n; const b=document.getElementById('cartBadge'), tb=document.getElementById('tabBadge');
  [b,tb].forEach(el=>{ el.textContent=n; el.style.display=n>0?'flex':'none'; }); }
document.querySelectorAll('.padd').forEach(btn=>btn.addEventListener('click',()=>{
  setCart(cart+1); btn.classList.add('added'); btn.innerHTML='<svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg> Added';
  setTimeout(()=>{ btn.classList.remove('added'); btn.innerHTML='<svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> Add to cart'; },1100);
}));

/* ── configurator ── */
const sel={use:'office',type:'laptop',budget:'low'};
const MATCHES={
  laptop:{name:'Dell Latitude 5490',spec:'i5 8th Gen · 8GB · 256GB SSD · 14" · Grade A',price:'₹14,999',icon:'laptop'},
  desktop:{name:'HP EliteDesk 800 G4 SFF',spec:'i5 8th Gen · 16GB · 512GB SSD · Grade A',price:'₹13,499',icon:'desktop'},
  workstation:{name:'Dell Precision 5540',spec:'i7 9th Gen · 32GB · Quadro GPU · Grade B',price:'₹38,999',icon:'work'},
  server:{name:'HP ProLiant DL380 G9',spec:'2× Xeon · 64GB · 4× 600GB SAS · 2U',price:'₹42,000',icon:'server'},
};
function renderMatch(){
  let key=sel.type; if(sel.use==='server')key='server';
  const m=MATCHES[key]||MATCHES.laptop;
  document.getElementById('cfgName').textContent=m.name;
  document.getElementById('cfgSpec').textContent=m.spec;
  document.getElementById('cfgPrice').textContent=m.price;
  document.getElementById('cfgIco').innerHTML=ICONS[m.icon];
  const n=40+Math.floor(Math.random()*180);
  document.getElementById('cfgGo').textContent=`View ${n} matches →`;
}
document.querySelectorAll('.cfg-opts').forEach(group=>{
  const g=group.dataset.group;
  group.querySelectorAll('.cfg-opt').forEach(opt=>opt.addEventListener('click',()=>{
    group.querySelectorAll('.cfg-opt').forEach(o=>o.classList.remove('sel'));
    opt.classList.add('sel'); sel[g]=opt.dataset.val; renderMatch();
  }));
});

/* ── bottom tabs ── */
document.querySelectorAll('.tab').forEach(t=>t.addEventListener('click',()=>{
  document.querySelectorAll('.tab').forEach(x=>x.classList.remove('on'));
  t.classList.add('on');
  if(t.dataset.tab==='build') document.getElementById('cfg').scrollIntoView({behavior:'smooth',block:'center'});
  if(t.dataset.tab==='home') document.getElementById('scroll').scrollTo({top:0,behavior:'smooth'});
}));
