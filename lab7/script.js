document.addEventListener('DOMContentLoaded',()=>{
  const students = [
    {name:'Asha',dept:'CSE',marks:88},
    {name:'Bikash',dept:'ECE',marks:76},
    {name:'Charu',dept:'CSE',marks:92},
    {name:'Deep',dept:'ME',marks:81},
    {name:'Esha',dept:'CSE',marks:69},
    {name:'Firoz',dept:'EE',marks:95}
  ];

  const products = [
    {name:'Headphones',category:'Electronics',price:120},
    {name:'Notebook',category:'Stationery',price:8},
    {name:'Smart Watch',category:'Electronics',price:199},
    {name:'Pen',category:'Stationery',price:3},
    {name:'Backpack',category:'Accessories',price:55},
    {name:'Keyboard',category:'Electronics',price:85}
  ];

  // Student elements
  const stuNamesBtn=document.getElementById('stu-print-names');
  const stuGt80Btn=document.getElementById('stu-marks-gt-80');
  const stuTop3Btn=document.getElementById('stu-top-3');
  const stuDeptInput=document.getElementById('stu-dept-input');
  const stuFilterDeptBtn=document.getElementById('stu-filter-dept');
  const stuOutput=document.getElementById('stu-output');

  // Product elements
  const prodNamesBtn=document.getElementById('prod-print-names');
  const prodPriceGtBtn=document.getElementById('prod-price-gt-80');
  const prodSortNameBtn=document.getElementById('prod-sort-name');
  const prodCatInput=document.getElementById('prod-cat-input');
  const prodFilterCatBtn=document.getElementById('prod-filter-cat');
  const prodOutput=document.getElementById('prod-output');

  function show(target,lines){ target.value = Array.isArray(lines) ? lines.join('\n') : String(lines); }

  // Students handlers
  stuNamesBtn.addEventListener('click',()=>{
    const names = students.map(s=>s.name);
    show(stuOutput,names);
  });

  stuGt80Btn.addEventListener('click',()=>{
    const filtered = students.filter(s=>s.marks>80).map(s=>`${s.name} (${s.marks})`);
    show(stuOutput, filtered.length? filtered : 'No students with marks > 80');
  });

  stuTop3Btn.addEventListener('click',()=>{
    const top3 = students.slice().sort((a,b)=>b.marks-a.marks).slice(0,3).map(s=>`${s.name} - ${s.marks}`);
    show(stuOutput, top3);
  });

  stuFilterDeptBtn.addEventListener('click',()=>{
    const dept = (stuDeptInput.value||'').trim();
    if(!dept){ show(stuOutput,'Please enter a department'); return }
    const out = students.filter(s=>s.dept.toLowerCase()===dept.toLowerCase()).map(s=>`${s.name} — ${s.dept} — ${s.marks}`);
    show(stuOutput,out.length?out:`No students found in "${dept}"`);
  });

  // Products handlers
  prodNamesBtn.addEventListener('click',()=>{
    show(prodOutput, products.map(p=>p.name));
  });

  prodPriceGtBtn.addEventListener('click',()=>{
    const out = products.filter(p=>p.price>80).map(p=>`${p.name} — $${p.price}`);
    show(prodOutput, out.length?out:'No products with price > 80');
  });

  prodSortNameBtn.addEventListener('click',()=>{
    const sorted = products.slice().sort((a,b)=>a.name.localeCompare(b.name)).map(p=>`${p.name} — ${p.category} — $${p.price}`);
    show(prodOutput, sorted);
  });

  prodFilterCatBtn.addEventListener('click',()=>{
    const cat = (prodCatInput.value||'').trim();
    if(!cat){ show(prodOutput,'Please enter a category'); return }
    const out = products.filter(p=>p.category.toLowerCase()===cat.toLowerCase()).map(p=>`${p.name} — $${p.price}`);
    show(prodOutput,out.length?out:`No products found in "${cat}"`);
  });

});