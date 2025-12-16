<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Person Dashboard</title>

    <!-- Tailwind-->
     <script src="https://cdn.tailwindcss.com"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primaryDark: "#213655",
            baseDark: "#0b121c",
            baseMid: "#162029",
            gold: "#e5d283",
            softWhite: "#f0f0f0",
            softBlue: "#b8c3d6",
            alertRed: "#e43e4a",
          }
        }
      }
    };
  </script>

  <style>
    table { font-size: 0.85rem; }
    th, td { white-space: nowrap; }
  </style>

</head>

<body class="bg-baseDark text-softWhite">

  <!-- LEFT NAVBAR -->
  <nav class="bg-[#213655] text-white w-64 h-screen fixed top-0 left-0 flex flex-col p-6 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-10 leading-tight">Utility Management System</h1>

    <ul class="flex flex-col space-y-4 w-full">
      <li><a class="flex items-center justify-center w-full p-5 hover:bg-blue-300 hover:text-gray-900 rounded transition">Dashboard</a></li>
      <li><a class="flex items-center justify-center w-full p-5 bg-blue-300 text-[#213655] rounded font-semibold">Person Details</a></li>
      <li><a class="flex items-center justify-center w-full p-5 hover:bg-blue-300 hover:text-[#213655] rounded">Tariff Plans</a></li>
      <li><a class="flex items-center justify-center w-full p-5 hover:bg-blue-300 hover:text-[#213655] rounded">Complaint Handling</a></li>
      <li><a class="flex items-center justify-center w-full p-5 hover:bg-blue-300 hover:text-[#213655] rounded">User Management</a></li>
    </ul>

    <div class="mt-auto w-full">
      <a class="block text-center text-[#162029] p-3 mt-6 bg-[#e5d283] hover:bg-red-500 rounded font-semibold transition">Log out</a>
    </div>
  </nav>

  <!-- MAIN AREA -->
  <main class="ml-64 p-8">
    <h1 class="text-gold text-3xl font-bold mb-8">Person Dashboard</h1>

    <section class="bg-softWhite text-baseDark rounded-xl shadow-lg p-6 border border-gold">

      <!-- HEADER -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-xl text-primaryDark">Person Information</h2>

        <div class="flex items-center space-x-2">
          <div class="relative">
            <input id="searchInput" type="text" placeholder="Search..."
              class="pl-8 pr-3 py-1 rounded-full bg-softWhite text-baseDark border border-gold focus:ring-2 focus:ring-gold" />

            <svg xmlns="http://www.w3.org/2000/svg"
              class="absolute left-2 top-1.5 h-5 w-5 text-primaryDark"
              fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
            </svg>
          </div>

          <button class="bg-primaryDark text-softWhite px-4 py-1 rounded-full hover:bg-gold hover:text-baseDark transition">Search</button>
          <!--ADD Button-->
          <button id="openPopupBtn" onclick="openNewCustomerModal()"
  class="bg-gold text-baseDark px-4 py-1 rounded-full font-semibold hover:bg-primaryDark hover:text-softWhite transition">
  Add
</button>

        </div>
      </div>

      <hr class="border-gold mb-6">

      <!-- TABLE -->
      <div class="bg-softWhite rounded-lg border border-gold shadow-lg overflow-x-auto">
        <table class="w-full table-auto text-sm">
          <thead class="bg-gold text-baseDark">
            <tr>
              <th class="px-2 py-2 w-14">ID</th>
              <th class="px-2 py-2 w-24">NIC</th>
              <th class="px-2 py-2 w-28">First</th>
              <th class="px-2 py-2 w-28">Middle</th>
              <th class="px-2 py-2 w-28">Last</th>
              <th class="px-2 py-2 w-56">Email</th>
              <th class="px-2 py-2 w-32">Phone</th>
              <th class="px-2 py-2 w-40">Reg Date</th>
              <th class="px-2 py-2 text-center w-20">View</th>
            </tr>
          </thead>

          <tbody id="personTable" class="divide-y divide-softBlue"></tbody>

        </table>
      </div>

    </section>
  </main>


  <!-- ADD NEW CUSTOMER POPUP -->
<div id="newCustomerModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center">
  <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-2xl relative text-sm">

    <!-- Close button -->
    <button onclick="closeNewCustomerModal()"
            class="absolute top-2 right-2 text-lg font-bold text-gray-600 hover:text-red-500">×</button>

    <h2 class="text-xl font-bold text-center mb-4 text-primaryDark">Add New Customer</h2>

    <!-- PERSONS SECTION -->
    <h3 class="font-semibold mb-2 text-primaryDark">Personal Information</h3>
    <div class="grid grid-cols-2 gap-2">
      <input id="firstName" class="border p-2 rounded" style="color:#000 !important;" placeholder="First Name">
      <input id="middleName" class="border p-2 rounded" style="color:#000 !important;" placeholder="Middle Name">
      <input id="lastName" class="border p-2 rounded" style="color:#000 !important;" placeholder="Last Name">
      <input id="nic" class="border p-2 rounded" style="color:#000 !important;" placeholder="NIC">
      <input id="email" class="border p-2 rounded col-span-2" style="color:#000 !important;" placeholder="Email">
      <input id="phone" class="border p-2 rounded col-span-2" style="color:#000 !important;" placeholder="Phone Number">
    </div>

    <!-- CUSTOMER SECTION -->
    <h3 class="font-semibold mt-4 mb-2 text-primaryDark">Customer Details</h3>
    <div class="grid grid-cols-2 gap-2">


<select id="customerType" class="border p-2 rounded col-span-2" style="color:#000 ">
    <option value="" disabled selected>Select Customer Type</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
</select>


      <input id="address" class="border p-2 rounded col-span-2" style="color:#000 !important;" placeholder="Address">
      <input id="city" class="border p-2 rounded" style="color:#000 !important;" placeholder="City">
      <input id="state" class="border p-2 rounded" style="color:#000 !important;" placeholder="State">
      <input id="postalCode" class="border p-2 rounded col-span-2" style="color:#000 !important;" placeholder="Postal Code">
    </div>
    <select id="customerStatus" class="border p-2 rounded col-span-2" style="color:#000 !important;">
    <option value="" disabled selected>Select Status</option>
    <option value="Active">Active</option>
    <option value="Inactive">Inactive</option>
</select>


    <!-- UTILITY SECTION -->
    <h3 class="font-semibold mt-4 mb-2 text-primaryDark">Utility Connection</h3>
    <div class="grid grid-cols-2 gap-2">
   <select id="utilityName" class="border p-2 rounded col-span-2" style="color:#000 !important;">
    <option value="" disabled selected>Select Utility</option>
    <option value="Gas">Gas</option>
    <option value="Electricity">Electricity</option>
    <option value="Water">Water</option>
</select>

   


      <input id="connectionDate" type="date" class="border p-2 rounded col-span-2" style="color:#000 !important;">
    </div>

    <button onclick="saveNewCustomer()"
            class="mt-5 w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-800">
      Create Customer
    </button>
  </div>
</div>

 <!-- PERSON DETAILS POPUP -->
<div id="detailsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center">

  <div class="bg-softWhite border-4 border-gold w-full max-w-sm p-3 rounded-lg shadow-2xl relative text-xs">

    <button onclick="closeModal()" 
            class="absolute top-1 right-1 text-lg font-bold text-primaryDark hover:text-alertRed">×</button>

    <h2 class="text-base font-bold mb-2 text-primaryDark border-b border-gold pb-1">
      Person Details
    </h2>

    <!-- FORM GRID -->
    <div class="grid grid-cols-2 gap-2">
      <div><label class="font-semibold text-primaryDark">Person ID</label><input id="d_personid" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">NIC</label><input id="d_nic" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">First</label><input id="d_fname" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Middle</label><input id="d_mname" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Last</label><input id="d_lname" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Email</label><input id="d_email" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Phone</label><input id="d_phone" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Reg Date</label><input id="d_regdate" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Customer ID</label><input id="d_custid" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Cust. Type</label><input id="d_custtype" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Address</label><input id="d_address" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">City</label><input id="d_city" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">State</label><input id="d_state" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
      <div><label class="font-semibold text-primaryDark">Postal</label><input id="d_postal" class="w-full border p-1 rounded text-xs bg-white text-primaryDark" disabled></div>
    </div>

<!-- CONNECTIONS SECTION -->
<div class="mt-3 border border-gold rounded">

  <!-- Table Header (fixed) -->
  <div class="bg-gold text-baseDark font-semibold px-2 py-1 text-xs">
    Connections
  </div>

  <!-- Scrollable Table -->
  <div class="max-h-48 overflow-y-auto bg-white">
    <table id="connectionsTable" class="w-full text-xs border-collapse">

      <thead class="bg-[#e6d87f] sticky top-0">
        <tr>
          <th class="text-left px-2 py-1">Conn. ID</th>
          <th class="text-left px-2 py-1">Utility</th>
          <th class="text-right px-2 py-1">Balance</th>
          <th class="text-left px-2 py-1">Status</th>
          <th class="text-left px-2 py-1">Conn. Date</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td colspan="5" class="text-center py-2 text-gray-500">
            No data loaded yet.
          </td>
        </tr>
      </tbody>

    </table>
  </div>

</div>

<style>
  /* force visible text (overrides dark-site themes) */
  #connectionsTable, #connectionsTable th, #connectionsTable td {
    color: #111 !important;
    background: transparent !important;
  }
</style>

    <!-- BUTTONS UNDER DETAILS -->
    <div class="mt-3 flex justify-end space-x-2">
      <!-- in the view popup where Add Connection button is -->

<!-- safer button: avoids PHP warning if $personID is not set -->
<button id="addConnectionBtn"
        class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
  Add Connection
</button>




        <button id="editBtn" onclick="enableEdit()" 
            class="bg-gold text-primaryDark font-semibold px-3 py-1 rounded-md text-xs hover:bg-primaryDark hover:text-white">
            Edit
        </button>
        <button id="saveBtn" onclick="saveChanges()" 
            class="hidden bg-primaryDark text-softWhite font-semibold px-3 py-1 rounded-md text-xs hover:bg-gold hover:text-baseDark">
            Save
        </button>
        <button id="cancelBtn" onclick="cancelEdit()" 
            class="hidden bg-alertRed text-white font-semibold px-3 py-1 rounded-md text-xs hover:bg-primaryDark">
            Cancel
        </button>
    </div>

  </div>
</div>





<!-- Add Connection Modal  -->
<div id="addConnectionModal" class="modal" style="display:none;">
  <div class="modal-content" style="
        max-width:560px;
        padding:20px;
        border-radius:8px;
        box-shadow:0 8px 30px rgba(0,0,0,.25);
        background:#fff;
        color:#000 !important;       
      ">

    <h3 style="margin-top:0; color:#000 !important;">Add Utility Connection</h3>

    <form id="addConnectionForm" style="color:#000 !important;">
      <!-- Name matches server side (PersonID) -->
      <input type="hidden" id="ac_customerID" name="PersonID" value="">

      <div style="margin-bottom:12px; color:#000 !important;">
        <label for="ac_customerType" style="color:#000 !important;">Customer Type</label><br>
        <select id="ac_customerType" name="CustomerTypeID" required 
                style="width:100%;padding:8px;margin-top:6px;color:#000 !important;background:#fff;">
        </select>
      </div>

      <div style="margin-bottom:12px; color:#000 !important;">
        <label for="ac_utilityType" style="color:#000 !important;">Utility Type</label><br>
        <select id="ac_utilityType" name="UtilityTypeID" required 
                style="width:100%;padding:8px;margin-top:6px;color:#000 !important;background:#fff;">
        </select>
      </div>

      <div style="margin-bottom:12px; color:#000 !important;">
        <label for="ac_connectionDate" style="color:#000 !important;">Connection Date</label><br>
        <input type="date" id="ac_connectionDate" name="ConnectionDate" 
               style="width:100%;padding:8px;margin-top:6px;color:#000 !important;background:#fff;" />
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
        <button type="button" id="ac_cancelBtn" 
                style="padding:8px 14px;border-radius:6px;border:1px solid #ccc;background:#fff;color:#000 !important;">
          Cancel
        </button>

        <button type="submit" id="ac_saveBtn" 
                style="padding:8px 14px;border-radius:6px;border:0;background:#1e88e5;color:#fff !important;">
          Save
        </button>
      </div>

      <div id="ac_msg" style="margin-top:10px;color:#d32f2f;display:none;font-weight:600;"></div>
    </form>

  </div>
</div>




<style>
  .modal { 
    position: fixed; 
    inset: 0; 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    background: rgba(0,0,0,0.4); 
    z-index:9999; 
  }
</style>




<!-- JS -->
<script>

/////////////////////
// POPUPS
/////////////////////

function openNewCustomerModal() {
  document.getElementById("newCustomerModal").classList.remove("hidden");
  document.getElementById("newCustomerModal").classList.add("flex");

  loadCustomerTypes();
  loadUtilityTypes();
}

function closeNewCustomerModal() {
  document.getElementById("newCustomerModal").classList.add("hidden");
}

function openModal() {
  document.getElementById("detailsModal").classList.remove("hidden");
  document.getElementById("detailsModal").classList.add("flex");
}

function closeModal() {
  document.getElementById("detailsModal").classList.add("hidden");
}

function openAddConnectionModal() {
  document.getElementById("personID_input").value =
    document.getElementById("d_personid").value;

  document.getElementById("addConnectionModal").classList.remove("hidden");
  document.getElementById("addConnectionModal").classList.add("flex");
}

function closeAddConnectionModal() {
  document.getElementById("addConnectionModal").classList.add("hidden");
}

/////////////////////
// LOAD DROPDOWNS
/////////////////////

function loadCustomerTypes() {
  fetch("fetch_customer_types.php")
    .then(res => res.json())
    .then(data => {
      let ct = document.getElementById("customerType");
      ct.innerHTML = `<option value="">Select Customer Type</option>`;
      data.forEach(row => {
        ct.innerHTML += `<option value="${row.CustomerTypeID}">${row.CustomerTypeName}</option>`;
      });
    });
}

function loadUtilityTypes() {
  fetch("fetch_utility_types.php")
    .then(res => res.json())
    .then(data => {
      let ut = document.getElementById("utilityType");
      if (!ut) return;
      ut.innerHTML = `<option value="">Select Utility Type</option>`;
      data.forEach(row => {
        ut.innerHTML += `<option value="${row.UtilityTypeID}">${row.UtilityTypeName}</option>`;
      });
    });
}

//NIc validation
function isValidSriLankanNIC(nic) {
  nic = nic.trim().toUpperCase();

  const oldNIC = /^[0-9]{9}[VX]$/; // 123456789V
  const newNIC = /^[0-9]{12}$/;    // 200012345678

  return oldNIC.test(nic) || newNIC.test(nic);
}

//phone number validation

function isValidPhoneNumber(phone) {
  phone = phone.trim();
  return /^[0-9]{10}$/.test(phone);
}
/////////////////////
// SAVE NEW CUSTOMER
/////////////////////

function saveNewCustomer() {


    const nicInput = document.getElementById("nic");
  const phoneInput = document.getElementById("phone");

  const nic = nicInput.value.trim().toUpperCase();
  const phone = phoneInput.value.trim();


    // PHONE VALIDATION (10 digits only)
  if (!isValidPhoneNumber(phone)) {
    alert("Enter a valid 10-digit phone number");
    phoneInput.focus();
    return;
  }
  // NIC FORMAT CHECK
  if (!isValidSriLankanNIC(nic)) {
    alert("Enter a valid NIC");
    nicInput.focus();
    return; 
  }

  //CHECK IF NIC ALREADY EXISTS (AJAX)
  fetch("check_nic.php?nic=" + encodeURIComponent(nic))
    .then(res => res.json())
    .then(result => {

      if (result.exists) {
        alert("This NIC is already registered");
        nicInput.focus();
        return; //  stop submission
      }

      //  IF EVERYTHING IS OK → SAVE CUSTOMER
      let fd = new FormData();

      fd.append("firstName", document.getElementById("firstName").value);
      fd.append("middleName", document.getElementById("middleName").value);
      fd.append("lastName", document.getElementById("lastName").value);
      fd.append("nic", nic);
      fd.append("email", document.getElementById("email").value);
      fd.append("phone", document.getElementById("phone").value);

      fd.append("customerType", document.getElementById("customerType").value);
      fd.append("address", document.getElementById("address").value);
      fd.append("city", document.getElementById("city").value);
      fd.append("state", document.getElementById("state").value);
      fd.append("postalCode", document.getElementById("postalCode").value);

      fd.append("utilityName", document.getElementById("utilityName").value);
      fd.append("connectionDate", document.getElementById("connectionDate").value);

      fetch("save_new_customer.php", {
        method: "POST",
        body: fd
      })
      .then(res => res.text())
      .then(data => {
        alert(data);
        closeNewCustomerModal();
        loadPersons();
      });

    })
    .catch(err => {
      alert("Error checking NIC. Please try again.");
      console.error(err);
    });
}



(function(){
  function findPersonIDOnPage() {
  
    const selectors = [
      '[data-personid]',
      '#personID', '#PersonID', '#lblPersonID',
      '.person-id', '.current-person-id',
      '.person-id-value', '[data-customerid]'
    ];
    for (const sel of selectors) {
      const el = document.querySelector(sel);
      if (!el) continue;

      if (el.dataset && (el.dataset.personid || el.dataset.customerid)) {
        return el.dataset.personid || el.dataset.customerid;
      }
      const txt = (el.textContent || el.value || '').trim();
      if (txt && /^\d+$/.test(txt)) return txt; // numeric id
      const attr = el.getAttribute('data-personid') || el.getAttribute('data-customerid');
      if (attr) return attr;
    }
    return null;
  }

  document.addEventListener('click', function(e) {
    const btn = e.target.closest && e.target.closest('#addConnectionBtn, [data-customerid], .btn-add-connection');
    if (!btn) return;
    const hid = document.getElementById('ac_customerID');
    if (!hid) return;
    // prefer button attribute
    const cid = btn.dataset ? (btn.dataset.customerid || btn.getAttribute('data-customerid')) : btn.getAttribute('data-customerid');
    if (cid) {
      hid.value = cid;
      return;
    }
    // fallback: discover on page
    const fallback = findPersonIDOnPage();
    if (fallback) hid.value = fallback;
  });

  // Also attempt one last fallback when the modal opens programmatically
  const originalOpen = window.openAddConnectionModal;
  window.openAddConnectionModal = async function(customerID) {
    if (customerID && document.getElementById('ac_customerID')) {
      document.getElementById('ac_customerID').value = customerID;
    }
    // call original (if exists) to do dropdowns and show
    if (typeof originalOpen === 'function') {
      await originalOpen(customerID);
    } else {
      // if original missing, do the fallback and show modal
      const hid = document.getElementById('ac_customerID');
      if (hid && !hid.value) hid.value = findPersonIDOnPage() || '';
      const m = document.getElementById('addConnectionModal');
      if (m) m.style.display = 'flex';
    }
  };
})();







/////////////////////
// SAVE UTILITY CONNECTION
/////////////////////
// Robust connections-add.js (replace existing)
// Robust connections-add.js (replace existing)
document.addEventListener('DOMContentLoaded', function () {
  const FETCH_CUSTOMER_TYPES = 'fetch_customer_types.php';
  const FETCH_UTILITY_TYPES  = 'fetch_utility_types.php';
  const ADD_CONNECTION_URL   = 'add_connection.php';

  function $id(id){ return document.getElementById(id); }
  function showModal(){ const m=$id('addConnectionModal'); if(m) m.style.display='flex'; }
  function hideModal(){ const m=$id('addConnectionModal'); if(m) m.style.display='none'; }

  function showMessage(text, isError=true){
    const box = $id('ac_msg');
    if (!box) { alert(text); return; }
    box.style.display = 'block';
    box.style.color = isError ? '#d32f2f' : '#2e7d32';
    box.textContent = text;
  }

  // Try to discover current PersonID from page if not provided
  function discoverPersonIDFallback(){
    // 1) from the Add button
    const addBtn = document.querySelector('#addConnectionBtn') || document.querySelector('[data-customerid]');
    if (addBtn) {
      const d = addBtn.dataset ? (addBtn.dataset.customerid || addBtn.getAttribute('data-customerid')) : addBtn.getAttribute('data-customerid');
      if (d) return d;
    }
    // 2) from an element that often contains the person id in your view popup
    // Try several common selectors - adjust if your view uses another id/class
    const possible = [
      '#personID', '#PersonID', '#lblPersonID', '.person-id', '.current-person-id', '[data-personid]'
    ];
    for (let sel of possible){
      const el = document.querySelector(sel);
      if (el) {
        // prefer dataset attribute if present
        if (el.dataset && el.dataset.personid) return el.dataset.personid;
        // otherwise use text content trimmed
        const t = el.textContent && el.textContent.trim();
        if (t && /^\d+$/.test(t)) return t;
        // or data attribute
        const attr = el.getAttribute('data-personid');
        if (attr) return attr;
      }
    }
    return null;
  }

  // Load dropdowns (safe)
  async function loadDropdowns(){
    const customerTypeSelect = $id('ac_customerType');
    const utilityTypeSelect  = $id('ac_utilityType');
    if (!customerTypeSelect || !utilityTypeSelect) return;
    try {
      const [cRes, uRes] = await Promise.all([fetch(FETCH_CUSTOMER_TYPES), fetch(FETCH_UTILITY_TYPES)]);
      if (!cRes.ok || !uRes.ok) throw new Error('Failed dropdown fetch');
      const custJson = await cRes.json();
      const utilJson = await uRes.json();
      customerTypeSelect.innerHTML = '';
      custJson.forEach(ct => {
        const o = document.createElement('option'); o.value = ct.CustomerTypeID; o.textContent = ct.CustomerTypeName;
        customerTypeSelect.appendChild(o);
      });
      utilityTypeSelect.innerHTML = '';
      utilJson.forEach(u => {
        const o = document.createElement('option'); o.value = u.UtilityTypeID; o.textContent = u.UtilityTypeName;
        utilityTypeSelect.appendChild(o);
      });
    } catch (err){
      console.error('Dropdown error', err);
      showMessage('Could not load dropdowns. Try reloading the page.');
    }
  }

  // Public open function (keeps compatibility)
  window.openAddConnectionModal = async function(customerID){
    // if customerID provided, set it. Otherwise we'll attempt fallback later.
    const hid = $id('ac_customerID');
    if (hid && customerID) hid.value = customerID;
    // default connection date
    const cd = $id('ac_connectionDate');
    if (cd && !cd.value) cd.value = new Date().toISOString().split('T')[0];
    await loadDropdowns();
    // if hidden is still empty, try fallback discovery
    if (hid && !hid.value) {
      const fallback = discoverPersonIDFallback();
      if (fallback) hid.value = fallback;
    }
    showModal();
  };

  // Delegated click handler: sets PersonID and opens modal
  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('[data-customerid], #addConnectionBtn, .btn-add-connection');
    if (!btn) return;
    // prefer dataset
    const cid = btn.dataset ? (btn.dataset.customerid || btn.getAttribute('data-customerid')) : btn.getAttribute('data-customerid');
    if (!cid) {
      // If no cid on button, but modal may still open from other code - set fallback to hidden input
      const hid = $id('ac_customerID');
      const fallback = discoverPersonIDFallback();
      if (hid && fallback) hid.value = fallback;
      // If still missing, warn and continue (open modal without person id)
      if (!hid || !hid.value) {
        alert('Customer ID missing from button.');
        return;
      }
      e.preventDefault();
      openAddConnectionModal(hid.value);
      return;
    }
    e.preventDefault();
    // set hidden then open
    const hid = $id('ac_customerID');
    if (hid) hid.value = cid;
    openAddConnectionModal(cid);
  });

  // Cancel button
  const cancel = $id('ac_cancelBtn');
  if (cancel) cancel.addEventListener('click', function(){ hideModal(); });

  // Submit handler with extra checks
  const form = $id('addConnectionForm');
  if (form) {
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      // ensure hidden PersonID is set; try fallback one last time
      const hid = $id('ac_customerID');
      if (hid && !hid.value) {
        const fallback = discoverPersonIDFallback();
        if (fallback) hid.value = fallback;
      }
      const personID = hid ? hid.value : '';
      const utilID = $id('ac_utilityType') ? $id('ac_utilityType').value : '';
      const custType = $id('ac_customerType') ? $id('ac_customerType').value : '';
      const connDate = $id('ac_connectionDate') ? $id('ac_connectionDate').value : '';

      // Defensive checks
      if (!personID) {
        showMessage('Missing PersonID. Please open the modal from the Add Connection button for the correct customer.');
        return;
      }
      if (!utilID) {
        showMessage('Please select a Utility Type.');
        return;
      }

      const saveBtn = $id('ac_saveBtn');
      if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

      const params = new URLSearchParams();
      params.append('PersonID', personID);
      params.append('UtilityTypeID', utilID);
      params.append('CustomerTypeID', custType);
      if (connDate) params.append('ConnectionDate', connDate);

      try {
        const res = await fetch(ADD_CONNECTION_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: params.toString()
        });

        const text = await res.text();
        let json = null;
        try { json = JSON.parse(text); } catch (err) { json = null; }

        if (!res.ok) {
          showMessage((json && json.message) ? json.message : text || 'Server error.');
        } else {
          if (json && json.success) {
            showMessage('Connection added successfully.', false);
            setTimeout(()=>{ hideModal(); if (typeof loadConnections === 'function') loadConnections(personID); }, 700);
          } else {
            // server might return plain text or JSON
            showMessage((json && json.message) ? json.message : (text || 'Failed to add connection.'));
          }
        }
      } catch (err) {
        console.error(err);
        showMessage('Network error. Check console.');
      } finally {
        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save'; }
      }
    });
  }

  // Close modal clicking outside
  const modal = $id('addConnectionModal');
  if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) hideModal(); });

}); // DOMContentLoaded end



/////////////////////
// LOAD PERSONS TABLE
/////////////////////

let allPersons = [];

function loadPersons() {
  fetch("fetch_persons.php")
    .then(res => res.json())
    .then(persons => {
      allPersons = persons;
      renderTable(persons);
    });
}

function renderTable(list) {
  let table = document.getElementById("personTable");
  table.innerHTML = "";

  list.forEach(p => {
    table.innerHTML += `
      <tr class="hover:bg-softBlue/30 transition">
        <td class="px-2 py-2">${p.PersonID}</td>
        <td class="px-2 py-2">${p.NIC}</td>
        <td class="px-2 py-2">${p.FirstName}</td>
        <td class="px-2 py-2">${p.MiddleName}</td>
        <td class="px-2 py-2">${p.LastName}</td>
        <td class="px-2 py-2">${p.Email}</td>
        <td class="px-2 py-2">${p.PhoneNumber}</td>
        <td class="px-2 py-2">${p.RegDate}</td>

        <td class="px-2 py-2 text-center">
          <button onclick="viewDetails(${p.PersonID})"
            class="bg-primaryDark text-softWhite px-3 py-1 rounded-md hover:bg-gold hover:text-baseDark transition">
            View
          </button>
        </td>
      </tr>
    `;
  });
}

/////////////////////
// SEARCH
/////////////////////

document.getElementById("searchInput").addEventListener("input", function () {
  let keyword = this.value.toLowerCase();

  let filtered = allPersons.filter(p =>
    Object.values(p).some(val =>
      String(val).toLowerCase().includes(keyword)
    )
  );

  renderTable(filtered);
});

/////////////////////
// VIEW DETAILS POPUP
/////////////////////
    



/////////////////////
// VIEW DETAILS POPUP
/////////////////////

function viewDetails(personID) {

  fetch("fetch_person_details.php?id=" + personID)
    .then(res => res.json())
    .then(data => {
      // SAFETY CHECK
      if (!data) {
        alert("No data found for this person!");
        return;
      }

      // FILL PERSON FIELDS (your existing IDs)
      document.getElementById("d_personid").value = data.PersonID ?? "";
      document.getElementById("d_nic").value      = data.NIC ?? "";
      document.getElementById("d_fname").value    = data.FirstName ?? "";
      document.getElementById("d_mname").value    = data.MiddleName ?? "";
      document.getElementById("d_lname").value    = data.LastName ?? "";
      document.getElementById("d_email").value    = data.Email ?? "";
      document.getElementById("d_phone").value    = data.PhoneNumber ?? "";
      document.getElementById("d_regdate").value  = data.RegDate ?? "";

      // FILL CUSTOMER FIELDS
      document.getElementById("d_custid").value   = data.CustomerID ?? "";
      document.getElementById("d_custtype").value = data.CustomerTypeID ?? "";
      document.getElementById("d_address").value  = data.Address ?? "";
      document.getElementById("d_city").value     = data.City ?? "";
      document.getElementById("d_state").value    = data.State ?? "";
      document.getElementById("d_postal").value   = data.PostalCode ?? "";

      // Ensure connections table exists and show loading placeholder
      const tbody = document.querySelector("#connectionsTable tbody");
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-2">Loading connections…</td></tr>`;
      }

      // Set the hidden input used by the Add Connection modal (so add button works)
      const hidden = document.getElementById("ac_customerID");
      if (hidden) {
        hidden.value = data.CustomerID ?? data.PersonID ?? "";
      }

      // LOAD CONNECTIONS only if we have a CustomerID (non-empty, non-zero)
      const cid = data.CustomerID || data.PersonID || 0;
      if (cid) {
        // call your connections loader (must exist)
        if (typeof loadConnections === "function") {
          loadConnections(cid);
        } else {
          // debug hint if loadConnections missing
          console.warn("loadConnections() not found — connections will not load automatically.");
          if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="py-2 text-red-600">Connections loader not found.</td></tr>`;
        }
      } else {
        if (tbody) {
          tbody.innerHTML = `
            <tr>
              <td colspan="5" class="text-center py-2 text-primaryDark">
                No customer record found
              </td>
            </tr>`;
        }
      }

      // OPEN MODAL
      // prefer your existing openModal() if present; otherwise try a modal element id
      if (typeof openModal === "function") {
        openModal();
      } else {
        const modal = document.getElementById("personDetailsModal") || document.querySelector(".person-details-modal");
        if (modal) {
          modal.style.display = "flex";
        } else {
          console.warn("No openModal() and no #personDetailsModal found — modal not explicitly opened.");
        }
      }
    })
    .catch(err => {
      console.error("View error:", err);
      alert("Failed to load person details!");
    });
}



/////////////////////
// EDIT MODE
/////////////////////

let originalData = {};

function enableEdit() {
  const fields = [
    "d_nic", "d_fname", "d_mname", "d_lname",
    "d_email", "d_phone", "d_address",
    "d_city", "d_state", "d_postal", "d_custtype"
  ];

  fields.forEach(f => {
    originalData[f] = document.getElementById(f).value;
    document.getElementById(f).disabled = false;
  });

  document.getElementById("editBtn").classList.add("hidden");
  document.getElementById("saveBtn").classList.remove("hidden");
  document.getElementById("cancelBtn").classList.remove("hidden");
}


function disableAllFields() {
  const all = [
    "d_nic", "d_fname", "d_mname", "d_lname",
    "d_email", "d_phone", "d_regdate",
    "d_custid", "d_custtype",
    "d_address", "d_city", "d_state", "d_postal"
  ];

  all.forEach(f => {
    if (document.getElementById(f)) {
      document.getElementById(f).disabled = true;
    }
  });

  document.getElementById("editBtn").classList.remove("hidden");
  document.getElementById("saveBtn").classList.add("hidden");
  document.getElementById("cancelBtn").classList.add("hidden");
}


function cancelEdit() {
  Object.keys(originalData).forEach(f => {
    document.getElementById(f).value = originalData[f];
  });

  disableAllFields();
}
function saveChanges() {
  let formData = new FormData();
  
  formData.append("PersonID", document.getElementById("d_personid").value);
  formData.append("NIC", document.getElementById("d_nic").value);
  formData.append("FirstName", document.getElementById("d_fname").value);
  formData.append("MiddleName", document.getElementById("d_mname").value);
  formData.append("LastName", document.getElementById("d_lname").value);
  formData.append("Email", document.getElementById("d_email").value);
  formData.append("Phone", document.getElementById("d_phone").value);

  formData.append("CustomerID", document.getElementById("d_custid").value);
  formData.append("CustomerTypeID", document.getElementById("d_custtype").value);
  formData.append("Address", document.getElementById("d_address").value);
  formData.append("City", document.getElementById("d_city").value);
  formData.append("State", document.getElementById("d_state").value);
  formData.append("PostalCode", document.getElementById("d_postal").value);

  fetch("update_persons_customers.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(resp => {
    alert(resp.message);
    closeModal();
    loadPersons();
  })
  .catch(err => {
    console.error("Update error:", err);
    alert("Update failed. Check console.");
  });
}

// INITIAL LOAD
/////////////////////

loadPersons();

</script>



<script>
async function loadConnections(customerID) {
  const tbody = document.querySelector('#connectionsTable tbody');
  if (!tbody) return console.warn('connectionsTable tbody not found');

  tbody.innerHTML = '<tr><td colspan="5">Loading…</td></tr>';

  try {
    const res = await fetch('fetch_connections.php?customerID=' + encodeURIComponent(customerID), { cache: 'no-store' });
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); } catch(err) {
      console.error('fetch_connections returned invalid JSON:', text);
      tbody.innerHTML = '<tr><td colspan="5" style="color:#b71c1c">Server returned invalid response (see console)</td></tr>';
      return;
    }

    if (!Array.isArray(data) || data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5">No connections found for this customer.</td></tr>';
      return;
    }

    tbody.innerHTML = ''; // clear and render
    data.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="padding:8px; color:#111;">${r.ConnectionID ?? ''}</td>
        <td style="padding:8px; color:#111;">${r.UtilityTypeName ?? r.UtilityTypeID ?? ''}</td>
        <td style="padding:8px; color:#111; text-align:right;">${(r.CurrentBalance !== null && r.CurrentBalance !== undefined) ? parseFloat(r.CurrentBalance).toFixed(2) : ''}</td>
        <td style="padding:8px; color:#111;">${r.Status ?? ''}</td>
        <td style="padding:8px; color:#111;">${r.ConnectionDate ?? ''}</td>
      `;
      tbody.appendChild(tr);
    });

  } catch (err) {
    console.error('loadConnections error:', err);
    tbody.innerHTML = '<tr><td colspan="5" style="color:#b71c1c">Error loading connections (check console)</td></tr>';
  }
}
</script>



</body>
</html>

