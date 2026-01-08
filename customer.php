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
<div id="newCustomerModal"
     class="fixed inset-0 bg-[#0b121c]/80 hidden items-center justify-center z-50">

  <!-- POPUP CONTAINER -->
  <div class="bg-[#213655] border border-[#e5d283]
              w-full max-w-2xl max-h-[90vh]
              p-6 rounded-2xl shadow-2xl
              flex flex-col text-sm text-[#f0f0f0]">

    <!-- CLOSE BUTTON -->
    <button onclick="closeNewCustomerModal()"
      class="absolute top-3 right-4 text-2xl font-bold
             text-[#b8c3d6] hover:text-[#e43e4a] transition">
      ×
    </button>

    <!-- HEADER -->
    <h2 class="text-lg font-bold text-[#e5d283]
               border-b border-[#e5d283] pb-2 mb-4 shrink-0">
      Add New Customer
    </h2>

    
    <div class="flex-1 overflow-y-auto pr-1">

      <!-- PERSONAL INFO -->
      <h3 class="font-semibold mb-2 text-[#b8c3d6]">
        Personal Information
      </h3>

      <div class="grid grid-cols-2 gap-3 mb-4">

        <input id="firstName"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="First Name">

        <input id="middleName"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="Middle Name">

        <input id="lastName"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="Last Name">

        <input id="nic"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="NIC">

        <input id="email"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="Email">

        <input id="phone"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]
                 placeholder:text-[#b8c3d6]"
          placeholder="Phone Number">
      </div>

      <!-- CUSTOMER DETAILS -->
      <h3 class="font-semibold mb-2 text-[#b8c3d6]">
        Customer Details
      </h3>

      <div class="grid grid-cols-2 gap-3 mb-4">

        <select id="customerType"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
          <option value="" disabled selected>Select Customer Type</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
        </select>

        <input id="address"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]"
          placeholder="Address">

        <input id="city"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]"
          placeholder="City">

        <input id="state"
          class="p-2 rounded-lg bg-[#f0f0f0] text-[#162029]"
          placeholder="State">

        <input id="postalCode"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]"
          placeholder="Postal Code">

        <select id="customerStatus"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
          <option value="" disabled selected>Select Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>

      </div>

      <!-- UTILITY -->
      <h3 class="font-semibold mb-2 text-[#b8c3d6]">
        Utility Connection
      </h3>

      <div class="grid grid-cols-2 gap-3">

        <select id="utilityName"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
          <option value="" disabled selected>Select Utility</option>
          <option value="Gas">Gas</option>
          <option value="Electricity">Electricity</option>
          <option value="Water">Water</option>
        </select>

        <input id="connectionDate" type="date"
          class="col-span-2 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
      </div>

    </div>

    <!-- footer----->
    <div class="mt-4 shrink-0">
      <button onclick="saveNewCustomer()"
        class="w-full bg-[#e5d283] text-[#162029]
               p-3 rounded-xl font-semibold
               shadow-lg hover:bg-[#213655]
               hover:text-white transition">
        Create Customer
      </button>
    </div>

  </div>
</div>


<!-- PERSON DETAILS POPUP -->
<div id="detailsModal" class="fixed inset-0 bg-[#0b121c]/80 hidden items-center justify-center z-50">

  <!-- POPUP -->
  <div class="bg-[#213655] border border-[#e5d283]
              w-full max-w-3xl max-h-[90vh]
              p-6 rounded-2xl shadow-2xl
              flex flex-col text-sm text-[#f0f0f0]">

    <!-- close button -->
    <button onclick="closeModal()"
      class="absolute top-3 right-4 text-2xl font-bold
             text-[#b8c3d6] hover:text-[#e43e4a] transition">
      ×
    </button>

    <!-- Header -->
    <h2 class="text-lg font-bold mb-4 text-[#e5d283] border-b border-[#e5d283] pb-2 shrink-0">
      Person Details
    </h2>

    <!-- Person Details-->
    <div class="shrink-0">
      <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

       
        <div>
          <label class="font-semibold text-[#b8c3d6]">Person ID</label>
          <input id="d_personid" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">NIC</label>
          <input id="d_nic" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">First Name</label>
          <input id="d_fname" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Middle Name</label>
          <input id="d_mname" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Last Name</label>
          <input id="d_lname" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Email</label>
          <input id="d_email" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Phone</label>
          <input id="d_phone" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Reg Date</label>
          <input id="d_regdate" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Customer ID</label>
          <input id="d_custid" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Customer Type</label>
          <input id="d_custtype" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Address</label>
          <input id="d_address" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">City</label>
          <input id="d_city" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">State</label>
          <input id="d_state" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Postal</label>
          <input id="d_postal" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
        </div>

        <div>
          <label class="font-semibold text-[#b8c3d6]">Status</label>
          <select id="d_status" disabled
            class="w-full mt-1 p-2 rounded-lg bg-[#f0f0f0] text-[#162029]">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

      </div>
    </div>

   
    <div class="mt-6 border border-[#e5d283] rounded-xl overflow-hidden
                flex flex-col flex-1 min-h-0">

      <div class="bg-[#e5d283] text-[#162029] font-semibold px-4 py-2 shrink-0">
        Connections
      </div>

      <div class="flex-1 overflow-y-auto bg-[#f0f0f0]">
        <table id="connectionsTable" class="w-full text-sm border-collapse">

          <thead class="bg-[#b8c3d6] sticky top-0 z-10">
            <tr>
              <th class="px-3 py-2 text-left">Conn. ID</th>
              <th class="px-3 py-2 text-left">Utility</th>
              <th class="px-3 py-2 text-right">Balance</th>
              <th class="px-3 py-2 text-left">Status</th>
              <th class="px-3 py-2 text-left">Conn. Date</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td colspan="5" class="text-center py-6 text-gray-500">
                No data loaded yet.
              </td>
            </tr>
          </tbody>

        </table>
      </div>
    </div>

    <!---buttons---->
    <div class="mt-4 flex justify-end gap-3 shrink-0">

      <button id="addConnectionBtn"
        class="bg-[#162029] text-[#f0f0f0] px-4 py-2 rounded-lg
               hover:bg-[#213655] transition">
        Add Connection
      </button>

      <button id="editBtn" onclick="enableEdit()"
        class="bg-[#e5d283] text-[#162029] px-4 py-2 rounded-lg font-semibold
               hover:bg-[#213655] hover:text-white transition">
        Edit
      </button>

      <button id="saveBtn" onclick="saveChanges()"
        class="hidden bg-[#213655] text-[#f0f0f0] px-4 py-2 rounded-lg
               hover:bg-[#e5d283] hover:text-[#162029] transition">
        Save
      </button>

      <button id="cancelBtn" onclick="cancelEdit()"
        class="hidden bg-[#e43e4a] text-white px-4 py-2 rounded-lg
               hover:bg-[#213655] transition">
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
        border-radius:12px;
        box-shadow:0 12px 40px rgba(0,0,0,.45);
        background:#213655;
        color:#f0f0f0;
      ">

    <h3 style="margin-top:0; color:#e5d283;">Add Utility Connection</h3>

    <form id="addConnectionForm">

      <input type="hidden" id="ac_customerID" name="PersonID" value="">

      <div style="margin-bottom:12px;">
        <label for="ac_customerType" style="color:#b8c3d6;">Customer Type</label><br>
        <select id="ac_customerType" name="CustomerTypeID" required 
                style="width:100%;padding:8px;margin-top:6px;
                       background:#f0f0f0;color:#162029;border-radius:6px;border:none;">
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label for="ac_utilityType" style="color:#b8c3d6;">Utility Type</label><br>
        <select id="ac_utilityType" name="UtilityTypeID" required 
                style="width:100%;padding:8px;margin-top:6px;
                       background:#f0f0f0;color:#162029;border-radius:6px;border:none;">
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label for="ac_connectionDate" style="color:#b8c3d6;">Connection Date</label><br>
        <input type="date" id="ac_connectionDate" name="ConnectionDate" 
               style="width:100%;padding:8px;margin-top:6px;
                      background:#f0f0f0;color:#162029;border-radius:6px;border:none;" />
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px;">
        <button type="button" id="ac_cancelBtn" 
                style="padding:8px 16px;border-radius:8px;
                       background:#162029;color:#b8c3d6;border:none;">
          Cancel
        </button>

        <button type="submit" id="ac_saveBtn" 
                style="padding:8px 16px;border-radius:8px;
                       background:#e5d283;color:#0b121c;font-weight:600;">
          Save
        </button>
      </div>

      <div id="ac_msg" style="
            margin-top:10px;
            color:#e43e4a;
            display:none;
            font-weight:600;">
      </div>

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
    background: rgba(11,18,28,0.75); 
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

  const oldNIC = /^[0-9]{9}[VX]$/; 
  const newNIC = /^[0-9]{12}$/;   

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
  // NIC
  if (!isValidSriLankanNIC(nic)) {
    alert("Enter a valid NIC");
    nicInput.focus();
    return; 
  }


  fetch("check_nic.php?nic=" + encodeURIComponent(nic))
    .then(res => res.json())
    .then(result => {

      if (result.exists) {
        alert("This NIC is already registered");
        nicInput.focus();
        return; 
      }

    
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
      if (txt && /^\d+$/.test(txt)) return txt; 
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
    
    const cid = btn.dataset ? (btn.dataset.customerid || btn.getAttribute('data-customerid')) : btn.getAttribute('data-customerid');
    if (cid) {
      hid.value = cid;
      return;
    }
 
    const fallback = findPersonIDOnPage();
    if (fallback) hid.value = fallback;
  });


  const originalOpen = window.openAddConnectionModal;
  window.openAddConnectionModal = async function(customerID) {
    if (customerID && document.getElementById('ac_customerID')) {
      document.getElementById('ac_customerID').value = customerID;
    }

    if (typeof originalOpen === 'function') {
      await originalOpen(customerID);
    } else {
   
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


  function discoverPersonIDFallback(){
    
    const addBtn = document.querySelector('#addConnectionBtn') || document.querySelector('[data-customerid]');
    if (addBtn) {
      const d = addBtn.dataset ? (addBtn.dataset.customerid || addBtn.getAttribute('data-customerid')) : addBtn.getAttribute('data-customerid');
      if (d) return d;
    }

    const possible = [
      '#personID', '#PersonID', '#lblPersonID', '.person-id', '.current-person-id', '[data-personid]'
    ];
    for (let sel of possible){
      const el = document.querySelector(sel);
      if (el) {
 
        if (el.dataset && el.dataset.personid) return el.dataset.personid;
      
        const t = el.textContent && el.textContent.trim();
        if (t && /^\d+$/.test(t)) return t;
     
        const attr = el.getAttribute('data-personid');
        if (attr) return attr;
      }
    }
    return null;
  }

  // Loading dropdowns
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

  
  window.openAddConnectionModal = async function(customerID){

    const hid = $id('ac_customerID');
    if (hid && customerID) hid.value = customerID;
  
    const cd = $id('ac_connectionDate');
    if (cd && !cd.value) cd.value = new Date().toISOString().split('T')[0];
    await loadDropdowns();
    
    if (hid && !hid.value) {
      const fallback = discoverPersonIDFallback();
      if (fallback) hid.value = fallback;
    }
    showModal();
  };

  
  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('[data-customerid], #addConnectionBtn, .btn-add-connection');
    if (!btn) return;
   
    const cid = btn.dataset ? (btn.dataset.customerid || btn.getAttribute('data-customerid')) : btn.getAttribute('data-customerid');
    if (!cid) {
      
      const hid = $id('ac_customerID');
      const fallback = discoverPersonIDFallback();
      if (hid && fallback) hid.value = fallback;
      
      if (!hid || !hid.value) {
        alert('Customer ID missing from button.');
        return;
      }
      e.preventDefault();
      openAddConnectionModal(hid.value);
      return;
    }
    e.preventDefault();
    
    const hid = $id('ac_customerID');
    if (hid) hid.value = cid;
    openAddConnectionModal(cid);
  });

  // Cancel button
  const cancel = $id('ac_cancelBtn');
  if (cancel) cancel.addEventListener('click', function(){ hideModal(); });

  
  const form = $id('addConnectionForm');
  if (form) {
    form.addEventListener('submit', async function(e){
      e.preventDefault();
   
      const hid = $id('ac_customerID');
      if (hid && !hid.value) {
        const fallback = discoverPersonIDFallback();
        if (fallback) hid.value = fallback;
      }
      const personID = hid ? hid.value : '';
      const utilID = $id('ac_utilityType') ? $id('ac_utilityType').value : '';
      const custType = $id('ac_customerType') ? $id('ac_customerType').value : '';
      const connDate = $id('ac_connectionDate') ? $id('ac_connectionDate').value : '';

      // Defensive checkings
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

  
  const modal = $id('addConnectionModal');
  if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) hideModal(); });

});



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
      document.getElementById("d_status").value = data.Status ?? "Active";

      const tbody = document.querySelector("#connectionsTable tbody");
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="5" class="py-2">Loading connections…</td></tr>`;
      }

     
      const hidden = document.getElementById("ac_customerID");
      if (hidden) {
        hidden.value = data.CustomerID ?? data.PersonID ?? "";
      }


      const cid = data.CustomerID || data.PersonID || 0;
      if (cid) {
       
        if (typeof loadConnections === "function") {
          loadConnections(cid);
        } else {
       
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
    "d_city", "d_state", "d_postal", "d_custtype" , "d_status"
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
    "d_address", "d_city", "d_state", "d_postal" , "d_status"
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
  formData.append("Status", document.getElementById("d_status").value);

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

    tbody.innerHTML = '';
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

