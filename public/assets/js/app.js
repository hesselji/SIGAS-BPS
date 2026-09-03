(() => {
  const toggle=document.querySelector('[data-menu-toggle]');
  const menu=document.querySelector('[data-menu]');
  if(toggle&&menu) toggle.addEventListener('click',()=>menu.classList.toggle('open'));

  const password=document.getElementById('passwordInput');
  const eye=document.getElementById('passwordToggle');
  if(password&&eye) eye.addEventListener('click',()=>{password.type=password.type==='password'?'text':'password';eye.textContent=password.type==='password'?'👁':'🙈';});

  const budget=document.getElementById('usesBudget');
  const archive=document.getElementById('archiveType');
  const parent=document.getElementById('classificationParent');
  const child=document.getElementById('classificationChild');
  const preview=document.getElementById('scopePreview');
  if(budget&&archive&&parent&&child){
    const scope=()=> budget.value && archive.value ? budget.value + (archive.value==='FASILITATIF'?'F':'S') : '';
    const fill=(select,rows,placeholder)=>{select.innerHTML=`<option value="">${placeholder}</option>`+rows.map(r=>`<option value="${r.code}">${r.code} — ${r.name}</option>`).join('');select.disabled=false;};
    async function loadParents(){const s=scope();preview.textContent=s||'—';parent.disabled=true;child.disabled=true;parent.innerHTML='<option>Pilih kondisi dahulu</option>';child.innerHTML='<option>Pilih KKA level 2 dahulu</option>';if(!s)return;const res=await fetch(`/api/classifications?scope=${encodeURIComponent(s)}`);const json=await res.json();if(json.success)fill(parent,json.data,'Pilih KKA level 2');}
    budget.addEventListener('change',loadParents);archive.addEventListener('change',loadParents);
    parent.addEventListener('change',async()=>{const s=scope();child.disabled=true;child.innerHTML='<option>Memuat...</option>';if(!parent.value)return;const res=await fetch(`/api/classifications?scope=${encodeURIComponent(s)}&parent=${encodeURIComponent(parent.value)}`);const json=await res.json();if(json.success)fill(child,json.data,'Pilih KKA level 3');});
  }
})();
