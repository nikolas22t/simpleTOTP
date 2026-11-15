<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="icon" type="image/x-icon" href="faviconkey.ico">
<title>SimpleTotp</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{--bg:#f4f6f9;--card:#fff;--line:#e3e7ed;--text:#1c2430;--muted:#6b7686;--accent:#2563eb;--danger:#dc2626}
*{box-sizing:border-box}
body{font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;margin:0;background:var(--bg);color:var(--text);min-height:100vh;font-size:14px}
header{position:sticky;top:0;z-index:20;background:#0f172a;color:#fff;box-shadow:0 2px 8px rgba(0,0,0,.15)}
.hbar{display:flex;align-items:center;gap:14px;padding:10px 24px;flex-wrap:wrap}
.brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:1.05rem;white-space:nowrap}
.brand .logo{width:30px;height:30px;border-radius:8px;background:var(--accent);display:grid;place-items:center;font-size:16px}
.brand small{display:block;font-weight:400;font-size:.72rem;color:#94a3b8}
.searchbox{flex:1;min-width:240px;max-width:560px;display:flex;gap:6px;margin-left:auto}
#search-totp{flex:1;padding:8px 12px;border-radius:8px;border:1px solid #334155;background:#1e293b;color:#fff;font-size:.9rem;outline:none}
#search-totp:focus{border-color:var(--accent)}
.hbtn{padding:8px 12px;border-radius:8px;border:1px solid #334155;background:#1e293b;color:#e2e8f0;cursor:pointer;font-size:.85rem;white-space:nowrap}
.hbtn:hover{background:#334155}
.hbtn.primary{background:var(--accent);border-color:var(--accent);color:#fff}
.timer{display:flex;align-items:center;gap:8px;font-size:.8rem;color:#94a3b8;white-space:nowrap}
.ring{width:30px;height:30px}
.ring circle{fill:none;stroke-width:4}
.ring .bgc{stroke:#334155}.ring .fg{stroke:#22c55e;stroke-linecap:round;transition:stroke-dashoffset 1s linear;transform:rotate(-90deg);transform-origin:50% 50%}
.panels{padding:0 24px}
.panel{display:none;background:var(--card);border:1px solid var(--line);border-radius:10px;padding:12px;margin-top:12px;gap:8px;flex-wrap:wrap;align-items:center;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.panel .lbl{font-weight:600;margin-right:6px;color:var(--muted);font-size:.8rem;text-transform:uppercase;letter-spacing:.04em}
.panel input{padding:8px 10px;border-radius:8px;border:1px solid var(--line);min-width:200px}
button{font-family:inherit}
.btn{padding:8px 14px;border-radius:8px;border:0;background:var(--accent);color:#fff;cursor:pointer;font-size:.85rem}
.btn.warn{background:#fee2e2;color:#991b1b}
main{padding:14px 24px 30px}
#tabs-bar{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px;align-items:center}
.tab{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:999px;background:#fff;border:1px solid var(--line);color:var(--text);font-size:.82rem;cursor:pointer;user-select:none}
.tab:hover{border-color:#cbd5e1}
.tab.active{background:var(--accent);border-color:var(--accent);color:#fff}
.tab span.x{opacity:.5;font-size:.75rem}.tab span.x:hover{opacity:1}
.count{margin-left:auto;color:var(--muted);font-size:.8rem}
#entries-list{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:10px}
.tile{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:10px 12px 0;position:relative;overflow:hidden;transition:box-shadow .15s,border-color .15s}
.tile:hover{border-color:#cbd5e1;box-shadow:0 4px 12px rgba(15,23,42,.08)}
.head{display:flex;align-items:center;gap:8px;min-width:0;padding-right:16px}
.av{flex:none;width:28px;height:28px;border-radius:7px;display:grid;place-items:center;color:#fff;font-weight:700;font-size:.8rem}
.av.logo-on{background:#fff!important;border:1px solid var(--line)}
.av img{width:20px;height:20px;object-fit:contain}
.nm{min-width:0;line-height:1.2}
.svc{font-weight:600;font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.acct{font-size:.72rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-height:.9em}
.code{display:flex;justify-content:space-between;align-items:baseline;margin:8px 0 9px;font-family:"SF Mono",ui-monospace,Menlo,Consolas,monospace}
.code div{cursor:pointer;user-select:text;border-radius:4px}
.current{font-size:1.35rem;font-weight:600;letter-spacing:.5px;color:var(--text)}
.next{font-size:.78rem;color:var(--muted)}
.next::before{content:"next ";font-family:Inter,system-ui,sans-serif;font-size:.68rem;color:#a0aab8}
.code div:hover{background:#eef2ff}
.time{display:none}
.bar{height:3px;background:#eef1f5;margin:0 -12px}
.bar i{display:block;height:100%;background:#22c55e;transition:width 1s linear}
.del{position:absolute;top:6px;right:6px;padding:2px 6px;border:0;border-radius:6px;background:none;color:#94a3b8;font-size:.75rem;cursor:pointer;opacity:0}
.tile:hover .del{opacity:1}.del:hover{background:#fee2e2;color:var(--danger)}
.copied{position:absolute;inset:0;background:rgba(37,99,235,.92);color:#fff;display:grid;place-items:center;font-weight:600;border-radius:10px;opacity:0;pointer-events:none;transition:opacity .2s}
.copied.on{opacity:1}
.muted{color:var(--muted)}
.empty{grid-column:1/-1;text-align:center;color:var(--muted);padding:40px}
@media(max-width:1500px){#entries-list{grid-template-columns:repeat(6,minmax(0,1fr))}}
@media(max-width:1250px){#entries-list{grid-template-columns:repeat(5,minmax(0,1fr))}}
@media(max-width:1000px){#entries-list{grid-template-columns:repeat(4,minmax(0,1fr))}}
@media(max-width:780px){#entries-list{grid-template-columns:repeat(2,minmax(0,1fr))}.hbar,.panels,main{padding-left:12px;padding-right:12px}.searchbox{max-width:none;margin-left:0}}
@media(max-width:420px){#entries-list{grid-template-columns:1fr}}
.ed{right:26px}
.tile:hover .ed{opacity:1}.ed:hover{background:#e0e7ff!important;color:var(--accent)!important}
.head{padding-right:40px}
.tile{cursor:grab}.tile.dragging{opacity:.4}.tile.over{border-color:var(--accent);box-shadow:0 0 0 2px var(--accent)}
#edit-modal{display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center}
#edit-modal.on{display:flex}
.mbox{background:#fff;border-radius:12px;padding:20px;width:min(420px,92vw);box-shadow:0 20px 50px rgba(0,0,0,.3)}
.mbox h3{margin:0 0 14px;font-size:1rem}
.mbox label{display:block;font-size:.8rem;color:var(--muted);margin-bottom:10px}
.mbox input{display:block;width:100%;margin-top:4px;padding:9px 10px;border:1px solid var(--line);border-radius:8px;font-size:.9rem;color:var(--text)}
.mact{display:flex;justify-content:flex-end;gap:8px;margin-top:6px}
</style>
</head>
<body>
<header><div class="hbar">
  <div class="brand"><div class="logo">🔐</div><div>SimpleTotp<small>Stored locally in this browser</small></div></div>
  <div class="searchbox"><input type="text" id="search-totp" placeholder="Search accounts…" autocomplete="off"><button class="hbtn primary" onclick="saveTab()" title="Save this search as a permanent tab">+ Tab</button></div>
  <button class="hbtn" onclick="toggleSection('add-section')">＋ Add</button>
  <button class="hbtn" onclick="toggleSection('import-section')">⇅ Import / Export</button>
  <button class="hbtn" onclick="toggleSection('sort-section')">↕ Sort</button>
  <div class="timer"><svg class="ring" viewBox="0 0 36 36"><circle class="bgc" cx="18" cy="18" r="15"/><circle class="fg" id="ringfg" cx="18" cy="18" r="15" stroke-dasharray="94.25" stroke-dashoffset="0"/></svg><span id="gtime">30s</span></div>
</div></header>
<div class="panels">
  <div class="panel" id="add-section"><span class="lbl">Add account</span>
    <input id="add_name" placeholder="Name (e.g. GitHub user@mail.com)">
    <input id="add_secret" placeholder="Secret (Base32)">
    <button class="btn" onclick="addEntry()">Add</button>
    <button onclick="clearAll()" class="btn warn" style="margin-left:auto">Clear all</button>
  </div>
  <div class="panel" id="import-section"><span class="lbl">Backup</span>
    <button class="btn" onclick="exportEntries()" title="All accounts, in your current (drag-and-drop) order">Export all JSON</button>
    <button class="btn" onclick="exportView()" title="Only the accounts currently shown (search / tab), in the order shown">Export current view</button>
    <input type="file" id="import_file" accept="application/json">
    <button class="btn" onclick="importEntries()">Import JSON</button>
  </div>
  <div class="panel" id="sort-section"><span class="lbl">Sort</span>
    <button class="btn" onclick="sortEntries()">Sort by name (A→Z)</button>
  </div>
</div>
<main>
<div id="tabs-bar"></div>
<div id="totp-entries"><div id="entries-list"></div></div>
<p class="muted" style="font-size:.78rem;margin-top:18px;text-align:center">Double-click a code to copy · All data stays in your browser — use Export to back up.</p>
</main>
<div id="edit-modal" onclick="if(event.target===this)closeEdit()"><div class="mbox">
  <h3>Edit account</h3>
  <label>Name<input id="ed_name" autocomplete="off"></label>
  <label>Secret (Base32)<input id="ed_secret" autocomplete="off" spellcheck="false"></label>
  <div class="mact"><button class="btn warn" onclick="closeEdit()">Cancel</button><button class="btn" onclick="saveEdit()">Save</button></div>
</div></div>

<script>
const timeStep=30, digits=6;

// --- Local Storage ---
function loadEntries(){return JSON.parse(localStorage.getItem('totp_entries')||'[]');}
function saveEntries(entries){localStorage.setItem('totp_entries',JSON.stringify(entries));}

// --- Add/Delete/Move/Sort ---
function addEntry(){
  const name=document.getElementById('add_name').value.trim();
  const secret=document.getElementById('add_secret').value.trim();
  if(!name||!secret) return alert('Name and Secret required');
  const entries=loadEntries();
  entries.push({name,secret});
  saveEntries(entries);
  document.getElementById('add_name').value='';
  document.getElementById('add_secret').value='';
  renderEntryElements();
}
function deleteEntry(i){
  if(!confirm('Delete '+loadEntries()[i].name+'?')) return;
  const entries=loadEntries();
  entries.splice(i,1);
  saveEntries(entries);
  renderEntryElements();
}
function moveEntry(i,dir){
  const entries=loadEntries();
  if(dir==='up'&&i>0)[entries[i-1],entries[i]]=[entries[i],entries[i-1]];
  if(dir==='down'&&i<entries.length-1)[entries[i],entries[i+1]]=[entries[i+1],entries[i]];
  saveEntries(entries);
  renderEntryElements();
}
function clearAll(){if(confirm('Clear all entries?')){localStorage.removeItem('totp_entries'); renderEntryElements();}}
function sortEntries(){const entries=loadEntries();entries.sort((a,b)=>a.name.localeCompare(b.name));saveEntries(entries);renderEntryElements();}

// --- Export / Import ---
function exportEntries(){const entries=loadEntries();const blob=new Blob([JSON.stringify(entries,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='totp_entries.json';a.click();}
function importEntries(){const fileInput=document.getElementById('import_file');if(fileInput.files.length===0)return alert('Select a file');const reader=new FileReader();reader.onload=()=>{try{const entries=JSON.parse(reader.result);saveEntries(entries);renderEntryElements();}catch(e){alert('Invalid JSON file');}};reader.readAsText(fileInput.files[0]);}

function exportView(){const f=document.getElementById('search-totp').value.toLowerCase();const entries=loadEntries().filter(e=>e.name.toLowerCase().includes(f));if(!entries.length)return alert('No accounts in the current view');const tag=f.trim()?'_'+f.trim().replace(/[^a-z0-9]+/g,'-'):'';const blob=new Blob([JSON.stringify(entries,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='totp_entries'+tag+'.json';a.click();}

// --- Base32 Decode ---
function base32Decode(s){const alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';let bits='';s=s.replace(/=+$/,'').toUpperCase();for(let c of s){const val=alphabet.indexOf(c);if(val===-1)continue;bits+=val.toString(2).padStart(5,'0');}let bytes=[];for(let i=0;i+8<=bits.length;i+=8)bytes.push(parseInt(bits.substring(i,i+8),2));return new Uint8Array(bytes);}

// --- TOTP ---
async function totp(secret,shift=0){const key=base32Decode(secret);const counter=Math.floor((Date.now()/1000+shift)/timeStep);const buffer=new ArrayBuffer(8);const view=new DataView(buffer);view.setUint32(4,counter);const cryptoKey=await crypto.subtle.importKey('raw',key,{name:'HMAC',hash:'SHA-1'},false,['sign']);const sig=new Uint8Array(await crypto.subtle.sign('HMAC',cryptoKey,buffer));const offset=sig[sig.length-1]&0xf;const binary=((sig[offset]&0x7f)<<24)|((sig[offset+1]&0xff)<<16)|((sig[offset+2]&0xff)<<8)|(sig[offset+3]&0xff);return(binary%10**digits).toString().padStart(digits,'0');}

// --- Check if user is selecting ---
function isUserSelecting(){const sel=window.getSelection();return sel&&sel.toString().length>0;}

// --- Clipboard helpers (NEW) ---
function clearSelection(){
  const sel = window.getSelection && window.getSelection();
  if(sel && sel.removeAllRanges){
    sel.removeAllRanges();
  }
}

async function copyTextToClipboard(text){
  if(!text) return;
  try{
    if(navigator.clipboard && navigator.clipboard.writeText){
      await navigator.clipboard.writeText(text);
    }else{
      // Fallback for older browsers
      const textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.style.position = 'fixed';
      textarea.style.left = '-9999px';
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
    }
  }catch(e){
    console.error('Copy failed:', e);
  }finally{
    clearSelection();
  }
}

// --- Render Entries ---
let entryElements=[];
function renderEntryElements(){
  const entries=loadEntries();
  const list=document.getElementById('entries-list');
  list.innerHTML='';
  entryElements=[];
  const filter=document.getElementById('search-totp').value.toLowerCase();
  entries.forEach((entry,i)=>{
    if(!entry.name.toLowerCase().includes(filter))return;
    const row=document.createElement('div');row.className='tile';row.title=entry.name;
    const parts=entry.name.trim().split(/\s+/);const svc=parts.shift()||'';const acct=parts.join(' ');
    let h=0;for(const c of svc.toLowerCase())h=(h*31+c.charCodeAt(0))%360;
    const head=document.createElement('div');head.className='head';
    const av=document.createElement('div');av.className='av';av.style.background=`hsl(${h},55%,48%)`;av.textContent=svc.charAt(0).toUpperCase();
    const lg=new Image();lg.alt='';lg.onload=()=>{av.textContent='';av.classList.add('logo-on');av.appendChild(lg);};lg.src='logo.php?n='+encodeURIComponent(svc);
    const nm=document.createElement('div');nm.className='nm';
    const nameDiv=document.createElement('div');nameDiv.className='svc';nameDiv.textContent=svc;
    const acctDiv=document.createElement('div');acctDiv.className='acct';acctDiv.textContent=acct;
    nm.appendChild(nameDiv);nm.appendChild(acctDiv);head.appendChild(av);head.appendChild(nm);row.appendChild(head);
    const timeDiv=document.createElement('div');timeDiv.className='time';row.appendChild(timeDiv);
    const codeDiv=document.createElement('div');codeDiv.className='code';
    const currentDiv=document.createElement('div');currentDiv.className='current';currentDiv.title='Current – double-click to copy';
    const nextDiv=document.createElement('div');nextDiv.className='next';nextDiv.title='Next – double-click to copy';
    const flash=document.createElement('div');flash.className='copied';flash.textContent='Copied';
    const doCopy=el=>{copyTextToClipboard(el.dataset.code||'');flash.classList.add('on');setTimeout(()=>flash.classList.remove('on'),700);};
    currentDiv.addEventListener('dblclick',()=>doCopy(currentDiv));
    nextDiv.addEventListener('dblclick',()=>doCopy(nextDiv));
    codeDiv.appendChild(currentDiv);codeDiv.appendChild(nextDiv);row.appendChild(codeDiv);
    const bar=document.createElement('div');bar.className='bar';const barI=document.createElement('i');bar.appendChild(barI);row.appendChild(bar);
    const del=document.createElement('button');del.className='del';del.title='Delete';del.textContent='✕';del.onclick=()=>deleteEntry(i);row.appendChild(del);
    const ed=document.createElement('button');ed.className='del ed';ed.title='Edit';ed.textContent='✎';ed.onclick=()=>openEdit(i);row.appendChild(ed);
    row.draggable=true;row.dataset.idx=i;
    row.addEventListener('dragstart',ev=>{dragIdx=i;row.classList.add('dragging');ev.dataTransfer.effectAllowed='move';ev.dataTransfer.setData('text/plain',String(i));});
    row.addEventListener('dragend',()=>{row.classList.remove('dragging');document.querySelectorAll('.tile.over').forEach(t=>t.classList.remove('over'));});
    row.addEventListener('dragover',ev=>{if(dragIdx===null||dragIdx===i)return;ev.preventDefault();row.classList.add('over');});
    row.addEventListener('dragleave',()=>row.classList.remove('over'));
    row.addEventListener('drop',ev=>{ev.preventDefault();row.classList.remove('over');if(dragIdx===null||dragIdx===i)return;const en=loadEntries();const [m]=en.splice(dragIdx,1);en.splice(i,0,m);saveEntries(en);dragIdx=null;renderEntryElements();renderTabs();updateCodes();});
    row.appendChild(flash);
    list.appendChild(row);
    entryElements.push({currentDiv,nextDiv,timeDiv,barI,secret:entry.secret});
  });
}

// --- Update Codes ---
async function updateCodes(){
  if(isUserSelecting()) return;
  const now=Date.now();
  const secondsLeft=timeStep-Math.floor(now/1000%timeStep);
  const col=secondsLeft<=5?'#dc2626':secondsLeft<=10?'#f59e0b':'#22c55e';
  const fg=document.getElementById('ringfg');if(fg){fg.style.strokeDashoffset=(94.25*(1-secondsLeft/timeStep)).toFixed(2);fg.style.stroke=col;}
  const gt=document.getElementById('gtime');if(gt)gt.textContent=secondsLeft+'s';
  const fmt=c=>c.slice(0,3)+' '+c.slice(3);
  for(let e of entryElements){
    const c=await totp(e.secret,0),n=await totp(e.secret,timeStep);
    e.currentDiv.dataset.code=c;e.nextDiv.dataset.code=n;
    e.currentDiv.textContent=fmt(c);e.nextDiv.textContent=fmt(n);
    e.timeDiv.textContent=secondsLeft+'s';e.barI.style.width=(secondsLeft/timeStep*100)+'%';e.barI.style.background=col;
  }
}

// --- Edit + drag state ---
let dragIdx=null,editIdx=null;
function openEdit(i){const e=loadEntries()[i];if(!e)return;editIdx=i;document.getElementById('ed_name').value=e.name;document.getElementById('ed_secret').value=e.secret;document.getElementById('edit-modal').classList.add('on');document.getElementById('ed_name').focus();}
function closeEdit(){document.getElementById('edit-modal').classList.remove('on');editIdx=null;}
function saveEdit(){if(editIdx===null)return;const name=document.getElementById('ed_name').value.trim();const secret=document.getElementById('ed_secret').value.trim().replace(/\s+/g,'');if(!name||!secret)return alert('Name and Secret required');const en=loadEntries();en[editIdx]={...en[editIdx],name,secret};saveEntries(en);closeEdit();renderEntryElements();renderTabs();updateCodes();}
document.addEventListener('keydown',ev=>{const m=document.getElementById('edit-modal');if(!m.classList.contains('on'))return;if(ev.key==='Escape')closeEdit();if(ev.key==='Enter')saveEdit();});

// --- Saved search tabs ---
function loadTabs(){const m=document.cookie.match(/(?:^|; )totp_tabs=([^;]*)/);if(!m)return [];try{return JSON.parse(decodeURIComponent(m[1]));}catch(e){return [];}}
function saveTabs(t){document.cookie="totp_tabs="+encodeURIComponent(JSON.stringify(t))+"; max-age=315360000; path=/; SameSite=Lax; Secure";}
function saveTab(){const q=document.getElementById("search-totp").value.trim();if(!q)return alert("Type a search first");const t=loadTabs();if(!t.some(x=>x.toLowerCase()===q.toLowerCase())){t.push(q);saveTabs(t);}renderTabs();}
function deleteTab(i,ev){ev.stopPropagation();const t=loadTabs();if(!confirm("Remove tab "+t[i]+"?"))return;const was=t[i];t.splice(i,1);saveTabs(t);if(document.getElementById("search-totp").value===was)selectTab("");else renderTabs();}
function selectTab(q){document.getElementById("search-totp").value=q;renderEntryElements();renderTabs();updateCodes();}
function renderTabs(){const bar=document.getElementById("tabs-bar");const cur=document.getElementById("search-totp").value;bar.innerHTML="";
  const mk=(label,q,i)=>{const d=document.createElement("div");d.className="tab"+(cur.toLowerCase()===q.toLowerCase()?" active":"");d.textContent=label;d.onclick=()=>selectTab(q);
    if(i!==undefined){const x=document.createElement("span");x.className="x";x.textContent="✕";x.title="Remove tab";x.onclick=e=>deleteTab(i,e);d.appendChild(x);}bar.appendChild(d);};
  mk("All","");loadTabs().forEach((q,i)=>mk(q,q,i));const c=document.createElement("span");c.className="count";c.textContent=entryElements.length+" of "+loadEntries().length+" accounts";bar.appendChild(c);
  if(!entryElements.length){document.getElementById("entries-list").innerHTML='<div class="empty">No accounts match.</div>';}}
document.getElementById("search-totp").addEventListener("input",renderTabs);

// --- Event Listeners ---
document.getElementById('search-totp').addEventListener('input',renderEntryElements);
function toggleSection(id){document.querySelectorAll('.panel').forEach(p=>{if(p.id!==id)p.style.display='none';});const s=document.getElementById(id);s.style.display=(s.style.display==='flex')?'none':'flex';}

// --- Initialize ---
renderEntryElements();
renderTabs();
setInterval(updateCodes,1000);

// --- Mobile Detection (optional extra JS class) ---
function isMobile(){return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);}
if(isMobile()){document.body.classList.add('mobile');}
</script>
</body>
</html>
