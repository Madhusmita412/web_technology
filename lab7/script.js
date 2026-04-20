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
  const stuOutputBody=document.getElementById('stu-output-body');

  // Product elements
  const prodNamesBtn=document.getElementById('prod-print-names');
  const prodPriceGtBtn=document.getElementById('prod-price-gt-80');
  const prodSortNameBtn=document.getElementById('prod-sort-name');
  const prodCatInput=document.getElementById('prod-cat-input');
  const prodFilterCatBtn=document.getElementById('prod-filter-cat');
  const prodOutputBody=document.getElementById('prod-output-body');

  function renderRows(tbody, rows, emptyMessage){
    tbody.innerHTML = '';

    if (!rows.length) {
      const tr = document.createElement('tr');
      const td = document.createElement('td');
      td.colSpan = 3;
      td.className = 'empty';
      td.textContent = emptyMessage;
      tr.appendChild(td);
      tbody.appendChild(tr);
      return;
    }

    rows.forEach((row)=>{
      const tr = document.createElement('tr');
      row.forEach((value)=>{
        const td = document.createElement('td');
        td.textContent = String(value);
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
  }

  function showStudents(list, emptyMessage){
    const rows = list.map((s)=>[s.name, s.dept, s.marks]);
    renderRows(stuOutputBody, rows, emptyMessage);
  }

  function showProducts(list, emptyMessage){
    const rows = list.map((p)=>[p.name, p.category, 'Rs ' + p.price]);
    renderRows(prodOutputBody, rows, emptyMessage);
  }

  // Students handlers
  stuNamesBtn.addEventListener('click',()=>{
    const namesOnly = students.map((s)=>({ name:s.name, dept:'-', marks:'-' }));
    showStudents(namesOnly, 'No student data found.');
  });

  stuGt80Btn.addEventListener('click',()=>{
    const filtered = students.filter((s)=>s.marks>80);
    showStudents(filtered, 'No students with marks greater than 80.');
  });

  stuTop3Btn.addEventListener('click',()=>{
    const top3 = students.slice().sort((a,b)=>b.marks-a.marks).slice(0,3);
    showStudents(top3, 'No student data found.');
  });

  stuFilterDeptBtn.addEventListener('click',()=>{
    const dept = (stuDeptInput.value||'').trim();
    if(!dept){ renderRows(stuOutputBody, [], 'Please enter a department.'); return }
    const out = students.filter((s)=>s.dept.toLowerCase()===dept.toLowerCase());
    showStudents(out, 'No students found in "' + dept + '".');
  });

  // Products handlers
  prodNamesBtn.addEventListener('click',()=>{
    const namesOnly = products.map((p)=>({ name:p.name, category:'-', price:'-' }));
    showProducts(namesOnly, 'No product data found.');
  });

  prodPriceGtBtn.addEventListener('click',()=>{
    const out = products.filter((p)=>p.price>80);
    showProducts(out, 'No products with price greater than 80.');
  });

  prodSortNameBtn.addEventListener('click',()=>{
    const sorted = products.slice().sort((a,b)=>a.name.localeCompare(b.name));
    showProducts(sorted, 'No product data found.');
  });

  prodFilterCatBtn.addEventListener('click',()=>{
    const cat = (prodCatInput.value||'').trim();
    if(!cat){ renderRows(prodOutputBody, [], 'Please enter a category.'); return }
    const out = products.filter((p)=>p.category.toLowerCase()===cat.toLowerCase());
    showProducts(out, 'No products found in "' + cat + '".');
  });

});