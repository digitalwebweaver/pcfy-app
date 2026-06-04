const inr=n=>'₹'+n.toLocaleString('en-IN');
const ICONS={
  laptop:'<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>',
  desktop:'<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
  server:'<rect x="2" y="3" width="20" height="5" rx="1"/><rect x="2" y="10" width="20" height="5" rx="1"/><rect x="2" y="17" width="20" height="5" rx="1"/>',
  work:'<rect x="3" y="3" width="18" height="14" rx="2"/><path d="M7 21h10M9 17v4M15 17v4"/>',
  storage:'<rect x="3" y="4" width="18" height="6" rx="1"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M7 7h.01M7 17h.01"/>',
};
/* dataset */
const PRODUCTS=[
  {brand:'Dell',name:'Latitude 5490',cat:'Laptops',grade:'A',price:14999,mrp:45000,cpu:'i5',ram:8,storage:256,rating:4.6,icon:'laptop'},
  {brand:'HP',name:'EliteBook 840 G6',cat:'Laptops',grade:'A',price:22500,mrp:72000,cpu:'i7',ram:16,storage:512,rating:4.7,icon:'laptop'},
  {brand:'Lenovo',name:'ThinkPad T480',cat:'Laptops',grade:'A',price:16999,mrp:52000,cpu:'i5',ram:8,storage:256,rating:4.5,icon:'laptop'},
  {brand:'Dell',name:'Precision 5540',cat:'Workstations',grade:'B',price:38999,mrp:105000,cpu:'i7',ram:32,storage:512,rating:4.4,icon:'work'},
  {brand:'HP',name:'ProBook 450 G7',cat:'Laptops',grade:'B',price:18250,mrp:58000,cpu:'i5',ram:8,storage:256,rating:4.3,icon:'laptop'},
  {brand:'Lenovo',name:'ThinkPad X1 Carbon',cat:'Laptops',grade:'A',price:34999,mrp:128000,cpu:'i7',ram:16,storage:512,rating:4.8,icon:'laptop'},
  {brand:'Apple',name:'MacBook Air M1',cat:'Laptops',grade:'A',price:49999,mrp:99900,cpu:'M1',ram:8,storage:256,rating:4.9,icon:'laptop'},
  {brand:'Dell',name:'OptiPlex 7060 SFF',cat:'Desktops',grade:'B',price:13499,mrp:40000,cpu:'i5',ram:16,storage:512,rating:4.2,icon:'desktop'},
  {brand:'HP',name:'EliteDesk 800 G4',cat:'Desktops',grade:'A',price:15999,mrp:46000,cpu:'i5',ram:16,storage:512,rating:4.5,icon:'desktop'},
  {brand:'Lenovo',name:'ThinkCentre M720',cat:'Desktops',grade:'C',price:9999,mrp:34000,cpu:'i3',ram:8,storage:256,rating:4.0,icon:'desktop'},
  {brand:'HP',name:'ProLiant DL380 G9',cat:'Servers',grade:'A',price:42000,mrp:180000,cpu:'Xeon',ram:64,storage:1024,rating:4.6,icon:'server'},
  {brand:'Dell',name:'PowerEdge R640',cat:'Servers',grade:'B',price:58000,mrp:240000,cpu:'Xeon',ram:64,storage:1024,rating:4.4,icon:'server'},
  {brand:'Dell',name:'Latitude 3410',cat:'Laptops',grade:'C',price:10999,mrp:38000,cpu:'i3',ram:4,storage:128,rating:3.9,icon:'laptop'},
  {brand:'Asus',name:'ExpertBook B9',cat:'Laptops',grade:'B',price:27999,mrp:89000,cpu:'i7',ram:16,storage:512,rating:4.3,icon:'laptop'},
  {brand:'HP',name:'Z2 Tower G4',cat:'Workstations',grade:'A',price:44999,mrp:135000,cpu:'Xeon',ram:32,storage:1024,rating:4.5,icon:'work'},
  {brand:'Lenovo',name:'ThinkPad E14',cat:'Laptops',grade:'B',price:19999,mrp:62000,cpu:'i5',ram:8,storage:512,rating:4.2,icon:'laptop'},
  {brand:'Apple',name:'MacBook Pro 13" M1',cat:'Laptops',grade:'B',price:64999,mrp:129900,cpu:'M1',ram:16,storage:512,rating:4.8,icon:'laptop'},
  {brand:'Dell',name:'OptiPlex 3070 Micro',cat:'Desktops',grade:'A',price:12999,mrp:39000,cpu:'i5',ram:8,storage:256,rating:4.3,icon:'desktop'},
  {brand:'Asus',name:'VivoBook 15',cat:'Laptops',grade:'C',price:11999,mrp:42000,cpu:'i3',ram:8,storage:256,rating:3.8,icon:'laptop'},
  {brand:'HP',name:'ProDesk 600 G5',cat:'Desktops',grade:'B',price:14499,mrp:44000,cpu:'i5',ram:16,storage:512,rating:4.1,icon:'desktop'},
];
PRODUCTS.forEach(p=>{ p.save=Math.round((1-p.price/p.mrp)*100); });

/* facet builders */
const FACETS={
  category:['Laptops','Desktops','Workstations','Servers'],
  brand:['Dell','HP','Lenovo','Apple','Asus'],
  cpu:[['i3','Core i3'],['i5','Core i5'],['i7','Core i7'],['Xeon','Intel Xeon'],['M1','Apple M1']],
  ram:[[8,'8 GB & up'],[16,'16 GB & up'],[32,'32 GB & up'],[64,'64 GB']],
  storage:[[256,'256 GB & up'],[512,'512 GB & up'],[1024,'1 TB']],
};
const state={ category:new Set(), brand:new Set(), grade:new Set(), cpu:new Set(), ram:new Set(), storage:new Set(), rating:0, min:0, max:200000, sort:'relevance', search:'' };
let shown=9;

function countFor(key,val){ return PRODUCTS.filter(p=>matches(p,{...state,[key]:new Set([val])},[key])).length; }

function chk(id,name,val,label,cnt){ return `<label class="fopt"><input type="checkbox" data-facet="${name}" value="${val}"><span class="fbox"><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg></span><span class="lbl">${label}</span><span class="cnt">${cnt}</span></label>`; }

document.getElementById('fCategory').innerHTML=FACETS.category.map(c=>chk(0,'category',c,c,PRODUCTS.filter(p=>p.cat===c).length)).join('');
document.getElementById('fBrand').innerHTML=FACETS.brand.map(b=>chk(0,'brand',b,b,PRODUCTS.filter(p=>p.brand===b).length)).join('');
document.getElementById('fCpu').innerHTML=FACETS.cpu.map(([v,l])=>chk(0,'cpu',v,l,PRODUCTS.filter(p=>p.cpu===v).length)).join('');
document.getElementById('fRam').innerHTML=FACETS.ram.map(([v,l])=>chk(0,'ram',v,l,PRODUCTS.filter(p=>p.ram>=v).length)).join('');
document.getElementById('fStorage').innerHTML=FACETS.storage.map(([v,l])=>chk(0,'storage',v,l,PRODUCTS.filter(p=>p.storage>=v).length)).join('');
const starRow=n=>{ let s=''; for(let i=1;i<=5;i++) s+=`<svg viewBox="0 0 24 24" class="${i<=n?'':'dim'}"><path d="M12 2l3 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.9 21l1.2-6.8-5-4.9 6.9-1z"/></svg>`; return s; };
document.getElementById('fRating').innerHTML=[4,3,0].map(r=>`<label class="frate"><input type="radio" name="rating" value="${r}" ${r===0?'checked':''}><span class="fradio"></span><span class="rstars">${r?starRow(r):''}</span><span class="lbl">${r?`${r}★ & up`:'Any rating'}</span></label>`).join('');

/* matching logic */
function matches(p,st,skip=[]){
  if(!skip.includes('category') && st.category.size && !st.category.has(p.cat)) return false;
  if(!skip.includes('brand') && st.brand.size && !st.brand.has(p.brand)) return false;
  if(st.grade.size && !st.grade.has(p.grade)) return false;
  if(st.cpu.size && !st.cpu.has(p.cpu)) return false;
  if(st.ram.size && ![...st.ram].some(v=>p.ram>=v)) return false;
  if(st.storage.size && ![...st.storage].some(v=>p.storage>=v)) return false;
  if(st.rating && p.rating < st.rating) return false;
  if(p.price < st.min || p.price > st.max) return false;
  if(st.search){ const q=st.search.toLowerCase(); if(!(p.brand+' '+p.name+' '+p.cat).toLowerCase().includes(q)) return false; }
  return true;
}
function sortList(list){
  const s=state.sort;
  if(s==='price-asc') list.sort((a,b)=>a.price-b.price);
  else if(s==='price-desc') list.sort((a,b)=>b.price-a.price);
  else if(s==='rating') list.sort((a,b)=>b.rating-a.rating);
  else if(s==='discount') list.sort((a,b)=>b.save-a.save);
  return list;
}

function card(p){ return `
  <div class="prod-card" onclick="if(!event.target.closest('button'))location.href='Product Page.html'">
    <div class="prod-img">
      <div class="ph"><span class="ph-label">${p.brand.toLowerCase()} · product shot</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="var(--navy)" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">${ICONS[p.icon]}</svg></div>
      <span class="grade-stamp gs-${p.grade.toLowerCase()}">GRADE ${p.grade}</span>
      <span class="stock-live">In stock</span>
      <button class="wish-btn">♡</button>
    </div>
    <div class="prod-body">
      <div class="prod-brand">${p.brand}</div>
      <div class="prod-name">${p.name}</div>
      <div class="prod-chips"><span class="prod-chip">${p.cpu==='M1'?'Apple M1':'Core '+p.cpu}</span><span class="prod-chip">${p.ram}GB</span><span class="prod-chip">${p.storage>=1024?'1TB':p.storage+'GB'}</span></div>
      <div class="prod-pricing"><span class="prod-price">${inr(p.price)}</span><span class="prod-mrp">${inr(p.mrp)}</span><span class="prod-save">${p.save}% off</span></div>
      <div class="prod-warranty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/></svg> 6-month warranty included</div>
      <div class="prod-footer"><button class="prod-btn-add" onclick="addToCart()">Add to cart</button><button class="prod-btn-view" onclick="location.href='Product Page.html'">View</button></div>
    </div>
  </div>`; }

function activeChips(){
  const out=[];
  const add=(facet,val,label)=>out.push(`<span class="achip">${label}<button data-facet="${facet}" data-val="${val}" aria-label="remove"><svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg></button></span>`);
  state.category.forEach(v=>add('category',v,v));
  state.brand.forEach(v=>add('brand',v,v));
  state.grade.forEach(v=>add('grade',v,'Grade '+v));
  state.cpu.forEach(v=>add('cpu',v,v==='M1'?'Apple M1':'Core '+v));
  state.ram.forEach(v=>add('ram',v,v+'GB+ RAM'));
  state.storage.forEach(v=>add('storage',v,(v>=1024?'1TB':v+'GB')+'+ storage'));
  if(state.rating) add('rating',state.rating,state.rating+'★ & up');
  if(state.min>0||state.max<200000) add('price','price',`${inr(state.min)} – ${inr(state.max)}`);
  const chips=document.getElementById('chips');
  chips.innerHTML = out.length ? out.join('')+'<button class="chip-clear" id="chipClear">Clear all</button>' : '';
}

function render(){
  let list=PRODUCTS.filter(p=>matches(p,state));
  list=sortList(list);
  const grid=document.getElementById('grid');
  document.getElementById('resTotal').textContent=PRODUCTS.length;
  document.getElementById('resCount').textContent=list.length;
  const slice=list.slice(0,shown);
  grid.innerHTML=slice.map(card).join('');
  document.getElementById('empty').style.display=list.length?'none':'block';
  document.getElementById('loadmore').style.display=list.length>shown?'flex':'none';
  activeChips();
  const fc=state.category.size+state.brand.size+state.grade.size+state.cpu.size+state.ram.size+state.storage.size+(state.rating?1:0)+((state.min>0||state.max<200000)?1:0);
  const mb=document.getElementById('mFilterCount'); mb.textContent=fc; mb.style.display=fc?'flex':'none';
}

/* events: checkboxes */
document.querySelectorAll('.fg-inner input[type=checkbox]').forEach(cb=>cb.addEventListener('change',()=>{
  const f=cb.dataset.facet; const v=isNaN(+cb.value)?cb.value:+cb.value;
  if(cb.checked) state[f].add(v); else state[f].delete(v);
  shown=9; render();
}));
/* rating radios */
document.querySelectorAll('input[name=rating]').forEach(r=>r.addEventListener('change',()=>{ state.rating=+r.value; shown=9; render(); }));
/* grade chips */
document.querySelectorAll('.gchip').forEach(g=>g.addEventListener('click',()=>{
  const v=g.dataset.g; g.classList.toggle('on');
  if(state.grade.has(v)) state.grade.delete(v); else state.grade.add(v);
  shown=9; render();
}));
/* filter group accordions */
document.querySelectorAll('[data-fg] .fg-head').forEach(h=>h.addEventListener('click',()=>h.parentElement.classList.toggle('open')));

/* price range */
const rMin=document.getElementById('rMin'),rMax=document.getElementById('rMax'),fill=document.getElementById('rangeFill');
function paintRange(){
  let a=+rMin.value,b=+rMax.value; if(a>b){ [a,b]=[b,a]; }
  state.min=a; state.max=b;
  const max=+rMin.max;
  fill.style.left=(a/max*100)+'%'; fill.style.right=(100-b/max*100)+'%';
  document.getElementById('rMinV').textContent=inr(a);
  document.getElementById('rMaxV').textContent=b>=max?'₹2,00,000+':inr(b);
}
function rangeChanged(){ paintRange(); shown=9; render(); }
rMin.addEventListener('input',paintRange); rMax.addEventListener('input',paintRange);
rMin.addEventListener('change',rangeChanged); rMax.addEventListener('change',rangeChanged);

/* chip removal (delegated) */
document.getElementById('chips').addEventListener('click',e=>{
  const btn=e.target.closest('button[data-facet]'); 
  if(e.target.closest('#chipClear')){ clearAll(); return; }
  if(!btn) return;
  const f=btn.dataset.facet, raw=btn.dataset.val;
  if(f==='price'){ state.min=0; state.max=200000; rMin.value=0; rMax.value=200000; paintRange(); }
  else if(f==='rating'){ state.rating=0; document.querySelector('input[name=rating][value="0"]').checked=true; }
  else if(f==='grade'){ state.grade.delete(raw); document.querySelector(`.gchip[data-g="${raw}"]`).classList.remove('on'); }
  else { const v=isNaN(+raw)?raw:+raw; state[f].delete(v); const cb=document.querySelector(`input[data-facet="${f}"][value="${raw}"]`); if(cb) cb.checked=false; }
  shown=9; render();
});

/* clear all */
function clearAll(){
  ['category','brand','grade','cpu','ram','storage'].forEach(k=>state[k].clear());
  state.rating=0; state.min=0; state.max=200000; state.search='';
  document.querySelectorAll('.fg-inner input[type=checkbox]').forEach(cb=>cb.checked=false);
  document.querySelectorAll('.gchip').forEach(g=>g.classList.remove('on'));
  document.querySelector('input[name=rating][value="0"]').checked=true;
  rMin.value=0; rMax.value=200000; paintRange();
  document.getElementById('searchInput').value='';
  shown=9; render();
}
document.getElementById('clearAll').addEventListener('click',clearAll);
document.getElementById('drawerClear').addEventListener('click',clearAll);

/* sort */
document.getElementById('sortSel').addEventListener('change',e=>{ state.sort=e.target.value; render(); });

/* search */
function doSearch(){ state.search=document.getElementById('searchInput').value.trim(); shown=9; render(); }
document.getElementById('searchBtn').addEventListener('click',doSearch);
document.getElementById('searchInput').addEventListener('keydown',e=>{ if(e.key==='Enter') doSearch(); });

/* load more */
document.getElementById('loadmoreBtn').addEventListener('click',()=>{ shown+=6; render(); });

/* cart */
let cart=0; const cartBtn=document.getElementById('cartBtn');
function addToCart(){ cart++; cartBtn.textContent=`🛒 Cart (${cart})`; }

/* ── mobile drawer ── */
const filters=document.getElementById('filters'), backdrop=document.getElementById('fbackdrop');
function openDrawer(){ filters.classList.add('show'); backdrop.classList.add('show'); document.body.style.overflow='hidden'; }
function closeDrawer(){ filters.classList.remove('show'); backdrop.classList.remove('show'); document.body.style.overflow=''; }
document.getElementById('openFilters').addEventListener('click',openDrawer);
document.getElementById('closeFilters').addEventListener('click',closeDrawer);
document.getElementById('drawerApply').addEventListener('click',closeDrawer);
backdrop.addEventListener('click',closeDrawer);

/* ── mobile sort sheet ── */
const sortSheet=document.getElementById('sortSheet'), sortBack=document.getElementById('sortBack');
document.getElementById('openSort').addEventListener('click',()=>{ sortSheet.classList.add('show'); sortBack.classList.add('show'); });
function closeSort(){ sortSheet.classList.remove('show'); sortBack.classList.remove('show'); }
sortBack.addEventListener('click',closeSort);
document.querySelectorAll('.sort-opt').forEach(o=>o.addEventListener('click',()=>{
  document.querySelectorAll('.sort-opt').forEach(x=>x.classList.remove('on')); o.classList.add('on');
  state.sort=o.dataset.sort; document.getElementById('sortSel').value=o.dataset.sort; render(); closeSort();
}));

paintRange(); render();
