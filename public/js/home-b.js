/* configurator */
const sel={use:'office',type:'laptop',budget:'low'};
const ICONS={
  laptop:'<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M1 20h22"/>',
  work:'<rect x="3" y="3" width="18" height="14" rx="2"/><path d="M7 21h10M9 17v4M15 17v4"/>',
  server:'<rect x="2" y="3" width="20" height="5" rx="1"/><rect x="2" y="10" width="20" height="5" rx="1"/><rect x="2" y="17" width="20" height="5" rx="1"/>',
  desktop:'<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
};
const MATCHES={
  'laptop'   :{name:'Dell Latitude 5490',      spec:'i5 8th Gen · 8GB · 256GB SSD · 14" FHD · Grade A', price:'₹14,999',icon:'laptop'},
  'desktop'  :{name:'HP EliteDesk 800 G4 SFF', spec:'i5 8th Gen · 16GB · 512GB SSD · Grade A',          price:'₹13,499',icon:'desktop'},
  'workstation':{name:'Dell Precision 5540',   spec:'i7 9th Gen · 32GB · Quadro GPU · Grade B',          price:'₹38,999',icon:'work'},
  'server'   :{name:'HP ProLiant DL380 G9',    spec:'2× Xeon · 64GB RAM · 4× 600GB SAS · 2U',           price:'₹42,000',icon:'server'},
};

function renderMatch(){
  var key = sel.type;
  if(sel.use==='server') key='server';
  var m = MATCHES[key] || MATCHES.laptop;
  var el;
  el=document.getElementById('cfgName');   if(el) el.textContent=m.name;
  el=document.getElementById('cfgSpec');   if(el) el.textContent=m.spec;
  el=document.getElementById('cfgPrice');  if(el) el.textContent=m.price;
  el=document.querySelector('.cfg-match-icon svg'); if(el) el.innerHTML=ICONS[m.icon]||ICONS.laptop;
  el=document.querySelector('.cfg-go');
  if(el){ var n=40+Math.floor(Math.random()*180); el.textContent='View '+n+' matching machines →'; }
}

document.querySelectorAll('.cfg-opts').forEach(function(group){
  var g=group.dataset.group;
  group.querySelectorAll('.cfg-opt').forEach(function(opt){
    opt.addEventListener('click',function(){
      group.querySelectorAll('.cfg-opt').forEach(function(o){ o.classList.remove('sel'); });
      opt.classList.add('sel');
      sel[g]=opt.dataset.val;
      renderMatch();
    });
  });
});
