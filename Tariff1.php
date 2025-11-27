<?php 
    // This file would typically include logic to fetch tariff data from your MSSQL database
    
    // --- PHP Templating ---
    // 1. Include the start of the HTML document, <head>, the Tailwind CDN link, and <body> tag.
    include ('includes/header.php'); 
?>

<div class="min-h-screen bg-[#f0f0f0]">
    
    <?php include ('includes/sidebar.php'); ?>

    <main class="p-8 overflow-auto ml-64">

        <!--div class="h-[calc(100vh-80px)] flex flex-col bg-white p-6 rounded-xl shadow-lg"-->
        <h2 class="text-3xl font-bold text-[#213655] mb-4">TARIFF MANAGEMENT</h2>

        <div class="mb-6 border-b border-gray-200 pb-4">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Facility</h3>
            <div class="flex space-x-3" role="tablist" aria-label="Utilities">
                <button id="util-water" data-utility="water" class="px-4 py-2 text-white bg-[#213655] rounded-lg shadow-md font-medium hover:text-white hover:bg-[#213655] transition focus:outline-none" onclick="selectUtility('water')">Water Utility</button>
                <button id="util-electricity" data-utility="electricity" class="px-4 py-2 rounded-lg font-medium bg-gray-200 text-gray-700 hover:bg-[#213655] hover:text-white transition focus:outline-none" onclick="selectUtility('electricity')">Electricity Utility</button>
                <button id="util-gas" data-utility="gas" class="px-4 py-2 rounded-lg font-medium bg-gray-200 text-gray-700 hover:bg-[#213655] hover:text-white transition focus:outline-none" onclick="selectUtility('gas')">Gas Utility</button>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-3">Consumer Group Category</h3>
            <div id="category-tabs" class="flex border-b border-gray-300 overflow-x-auto space-x-4">
                <button id="cat-domestic" data-category="domestic" class="py-2 px-4 border-b-2 border-[#213655] text-[#213655] font-medium whitespace-nowrap focus:outline-none hover:text-white hover:bg-[#213655] hover:border-b-2 hover:[#213655] transition rounded" onclick="selectCategory('domestic')">Domestic Users</button>
                <button id="cat-government" data-category="government" class="py-2 px-4 text-[#213655] hover:text-white hover:bg-[#213655] hover:border-b-2 hover:border-[#213655] transition whitespace-nowrap rounded" onclick="selectCategory('government')">Government Institutions</button>
                <button id="cat-nonprofit" data-category="nonprofit" class="py-2 px-4 text-[#213655] hover:text-white hover:bg-[#213655] hover:border-b-2 hover:border-[#213655] transition whitespace-nowrap rounded" onclick="selectCategory('nonprofit')">Religous/Non-profit Organizations</button>
            </div>
        <!--/div-->

        <div class="bg-white p-6 rounded-xl shadow-lg relative">
            
            <!--Water-Demostic table-->
            <div id="table-water-domestic" class="">
            <!--div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg"-->
            <div class="flex-1 overflow-y-auto overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Water-government table-->

            <div id="table-water-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units WG(Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Water-Nonprofit table-->
            
            <div id="table-water-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units WN (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>
            
            <!--Electricity-Domestic table-->

            <div id="table-electricity-domestic" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units ED (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Electricity-Government table-->

            <div id="table-electricity-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units EG (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Electricity-Nonprofit table-->

            <div id="table-electricity-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units EN (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Domestic table-->

            <div id="table-gas-domestic" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GD (Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Government table-->

            <div id="table-gas-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GG(Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Nonprofit table-->

            <div id="table-gas-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GN(Usage Range)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">00 – 05</td>
                        <td class="px-4 py-3 whitespace-nowrap">50.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('00 - 05', 50.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">06 – 10</td>
                        <td class="px-4 py-3 whitespace-nowrap">70.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('06 - 10', 70.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">11 – 15</td>
                        <td class="px-4 py-3 whitespace-nowrap">90.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">300.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('11 - 15', 90.00, 300.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">16 – 20</td>
                        <td class="px-4 py-3 whitespace-nowrap">100.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">400.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('16 – 20', 100.00, 400.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">21 – 25</td>
                        <td class="px-4 py-3 whitespace-nowrap">120.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">500.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('21 – 25', 120.00, 500.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">26 - 30</td>
                        <td class="px-4 py-3 whitespace-nowrap">150.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">600.00</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <button onclick="openEditTariffModal('26 - 30', 150.00, 600.00)" class="text-[#213655] hover:text-[#0b121c] transition mr-2">✏️ Edit</button>
                            <button class="text-gray-600 hover:text-[#e43e4a] transition">🗑 Delete</button>
                        </td>
                    </tr>
                    </tbody>   
                </table>
            </div>
            </div>


            <!--TABLES OVERRRRRRRRRRRRRRR!!!!!!!!!!!!!!!!!!!!!!!!!!!-->

            <div class="bg-white p-6 rounded-xl shadow-lg relative"> <!--Main white card(container)-->

                <div class="flex items-center justify-start space-x-4 mb-4">
                <!--div class= "mt-4 flex justify-end gap-4"-->
                    <button onclick="openAddTariffModal()" class="px-3 py-1.5 bg-[#213655] text-white rounded-lg shadow hover:bg-[#162029] font-semibold transition">
                        + Add New Tariff Row
                    </button>
                    <button onclick="openCategoryManagementModal()" class="px-3 py-1.5 bg-[#e5d283] text-white rounded-lg shadow hover:bg-[#213655] font-semibold transition">
                        Category Management
                    </button>
                </div>

                <div class="flex justify-end items-center space-x-4 mb-6">
                     <button class="px-3 py-1.5 bg-[#213655] text-white rounded-lg shadow hover:bg-[#162029] font-semibold transition">
                        Save Changes
                    </button>
                    <button class="px-3 py-1.5 bg-[#b8c3d6] text-[#213655] rounded-lg shadow hover:bg-gray-400 transition">
                        Reset Table
                    </button>
                    <button class="px-3 py-1.5 bg-transparent-500 border-2 border-[#213655] text-[#213655] rounded-lg shadow hover:bg-[#213655] hover:text-white transition">
                        Export Tariff Data
                    </button>
                </div>
            </div>

        </div>

    </main>
</div>

<div id="addTariffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-xs">
        <h3 class="text-2xl text-[#213655] font-bold mb-4">Add New Tariff</h3>
        <form>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-1">Usage Range (Ex: 51–75 units)</label>
                <input type="text" id="addUsageRange" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-1">Rate per Unit (Rs.)</label>
                <input type="number" step="0.01" id="addRatePerUnit" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-1">Fixed Charge (Rs.)</label>
                <input type="number" step="0.01" id="addFixedCharge" class="w-full border border-gray-300 p-2 rounded-lg focus:ring-[#e5d283] focus:border-blue-500" required>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddTariffModal()" class="px-3 py-1.5 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-3 py-1.5 bg-[#213655] text-white rounded-lg hover:bg-[#162029]">Save Tariff</button>
            </div>
        </form>
    </div>
</div>

<div id="categoryManagementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-5 rounded-xl shadow-2xl w-full max-w-xs">
        <h3 class="text-2xl font-bold text-[#213655] mb-4">Category Management</h3>
        
        <div class="space-y-4">
            <button class="w-full text-left p-2 border bg-[#213655] text-white border-[#213655] rounded-lg text-md hover:bg-[#e5d283] hover:border-[#e5d283] transition">
                ➕ Add New Category
            </button>
            <button class="w-full text-left p-2 bg-[#213655] text-white border border-[#213655] rounded-lg text-md hover:bg-[#e5d283] hover:border-[#e5d283] transition">
                ✏️ Rename Existing Category
            </button>
            <button class="w-full text-left p-2 bg-[#213655] border border-[#213655] rounded-lg text-md text-white hover:bg-[#e5d283] hover:border-[#e5d283] transition">
                🗑 Delete Category
            </button>
        </div>

        <div class="flex justify-end mt-6">
            <button onclick="closeCategoryManagementModal()" class="px-3 py-1.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm">Close</button>
        </div>
    </div>
</div>

<script>
    // This script controls the visibility of the Modals/Popups by manipulating their Tailwind classes.

    // --- Add New Tariff Modal Functions ---
    function openAddTariffModal() {
        // Removes 'hidden' and adds 'flex' to make the modal visible and centered.
        document.getElementById("addTariffModal").classList.remove("hidden");
        document.getElementById("addTariffModal").classList.add("flex");
    }

    function closeAddTariffModal() {
        // Adds 'hidden' and removes 'flex' to hide the modal.
        document.getElementById("addTariffModal").classList.add("hidden");
        document.getElementById("addTariffModal").classList.remove("flex");
    }

    // --- Category Management Modal Functions (Works the same way) ---
    function openCategoryManagementModal() {
        document.getElementById("categoryManagementModal").classList.remove("hidden");
        document.getElementById("categoryManagementModal").classList.add("flex");
    }

    function closeCategoryManagementModal() {
        document.getElementById("categoryManagementModal").classList.add("hidden");
        document.getElementById("categoryManagementModal").classList.remove("flex");
    }
    
    // --- Edit Tariff Modal Stub (Placeholder function for the table's Edit button) ---
    function openEditTariffModal(range, rate, fixedCharge) {
        // This 'alert' is just temporary to prove the data is being passed correctly.
        alert(`Editing Tariff:\nRange: ${range}\nRate: ${rate}\nFixed Charge: ${fixedCharge}`);
        // In a final app, you would populate the input fields of the 'addTariffModal' 
        // with the passed data and change the modal title before opening it.
        openAddTariffModal(); 
    }

    // Toggles which table to show when specific buttons are clicked

    //store current selections
    let currentUtility = 'water';      // default utility (matches initial UI state)
    let currentCategory = 'domestic';  // default category (matches initial UI state)

    // Called when a utility button is clicked
    function selectUtility(util) {
    // update current utility variable
      currentUtility = util;

      // update active/visual state of utility buttons
      document.querySelectorAll('[data-utility]').forEach(btn => {
      // if button's data-utility matches selected -> active style, else -> neutral
        if (btn.dataset.utility === util) {
          btn.classList.remove('bg-gray-200', 'text-gray-700');
          btn.classList.add('bg-[#213655]', 'text-white');
        } else {
          btn.classList.remove('bg-[#213655]', 'text-white');
          btn.classList.add('bg-gray-200', 'text-gray-700');
        }
      });

    // optionally reset category to default when utility changes
    // currentCategory = 'domestic';
    // document.getElementById('cat-domestic').click();

    // show the correct tariff table for the active utility+category
      showTable();
    }

  // Called when a category tab is clicked
    function selectCategory(cat) {
    // update current category variable
      currentCategory = cat;

    // update active/visual state of category tabs
    document.querySelectorAll('#category-tabs button').forEach(btn => {
        if (btn.dataset.category === cat) {
          // active style: blue text and bottom border
          btn.classList.add('border-b-2', 'border-[#213655]', 'text-[#213655]', 'font-medium');
        } else {
          // remove active style
          btn.classList.remove('border-b-2', 'border-[#213655]', 'text-[#213655]', 'font-medium');
        }
    });

    // show the correct tariff table for the active utility+category
      showTable();
    }

    // Shows the correct table container and hides the others
    function showTable() {
      // build expected container id: table-{utility}-{category}
      const expectedId = `table-${currentUtility}-${currentCategory}`;

      // hide all table containers that follow naming convention
      document.querySelectorAll('[id^="table-"]').forEach(el => {
        el.classList.add('hidden'); // hide
      });

      // show the expected container if it exists
      const target = document.getElementById(expectedId);
      if (target) {
        target.classList.remove('hidden');
      } else {
        // if target not found, you can show a placeholder or console for debugging
        console.warn('Missing table container for:', expectedId);
      }
    }

    //run once at load to set correct initial view
    document.addEventListener('DOMContentLoaded', function() {
      selectUtility(currentUtility);   // set active utility styles
      selectCategory(currentCategory); // set active category styles and show table
    });
    
</script>

<?php 
    // 3. Include the closing </body> and </html> tags.
    include ('includes/footer.php'); 
?>