<?php 

    session_start();
    
    include ('include/header.php'); 
    include('include/db.php');
?>

<div class="min-h-screen bg-[#f0f0f0]">
    
    <?php include ('include/sidebar.php'); ?>

    <main class="p-8 overflow-auto ml-64">

        <!--div class="h-[calc(100vh-80px)] flex flex-col bg-white p-6 rounded-xl shadow-lg"-->
        <h2 class="text-3xl font-bold text-[#213655] mb-4">TARIFF MANAGEMENT</h2>

        <!-- UPDATED: FLASH MESSAGES -->
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>
        <!-- END UPDATED -->

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
                <button id="cat-commercial" data-category="commercial" class="py-2 px-4 text-[#213655] hover:text-white hover:bg-[#213655] hover:border-b-2 hover:border-[#213655] transition whitespace-nowrap rounded" onclick="selectCategory('commercial')">Commercial Users</button>
                <button id="cat-government" data-category="government" class="py-2 px-4 text-[#213655] hover:text-white hover:bg-[#213655] hover:border-b-2 hover:border-[#213655] transition whitespace-nowrap rounded" onclick="selectCategory('government')">Government Institutions</button>
                <button id="cat-nonprofit" data-category="nonprofit" class="py-2 px-4 text-[#213655] hover:text-white hover:bg-[#213655] hover:border-b-2 hover:border-[#213655] transition whitespace-nowrap rounded" onclick="selectCategory('nonprofit')">Religous/Non-profit Organizations</button>
            </div>
        <!--/div-->

        <!-- UPDATED: Plan selector + Effective date + Fixed charge scope -->
        <?php
        // fetch active plans for current utility/category to populate selector
            $plans = executeQuery($pdo, "SELECT TariffPlanID, CustomerTypeID, UtilityTypeID, EffectiveFrom, EffectiveTo FROM TariffPlans WHERE IsActive = 1 ORDER BY TariffPlanID");
            if (!empty($plans)) {
                $selectedPlan = $plans[0];
            } else {
                $selectedPlan = [
                    'EffectiveFrom' => null,
                    'EffectiveTo'   => null
                ];
            }
        
        // Build JS-friendly UtilityType-CustomerType → TariffPlanID map
        // --- Build plan maps for JS ---
        // fetch utility & customer names so we can build string keys (e.g. 'water-domestic')
        $utils = executeQuery($pdo, "SELECT UtilityTypeID, UtilityTypeName FROM UtilityTypes");
        $cats  = executeQuery($pdo, "SELECT CustomerTypeID, CustomerTypeName FROM CustomerTypes");

        // build lookup arrays
        $utilsById = [];
        foreach ($utils as $u) $utilsById[$u['UtilityTypeID']] = $u['UtilityTypeName'];

        $catsById = [];
        foreach ($cats as $c) $catsById[$c['CustomerTypeID']] = $c['CustomerTypeName'];

        // helper to normalise names to the short keys used in UI
        function normKey($s) {
            $s = strtolower(trim((string)$s));
            // common normalisations used in UI:
            $s = preg_replace('/[^a-z0-9]+/', '-', $s);
            $s = str_replace(['-and-', ' and ', ' & '], '-', $s);
            $s = str_replace(['religous','religious','non-profit','nonprofit','non-profit-organizations'], 'nonprofit', $s);
            $s = str_replace(['domestic','commercial','government','nonprofit','water','gas','electricity','electric','gn','gc','ge','en','ed'], $s, $s);
            $s = preg_replace('/-+/', '-', $s);
            $s = trim($s, '-');
            return $s;
        }

        // build numeric map (legacy) and string-key map (for UI)
        $planMapNumeric = [];
        $planMapString  = [];
        foreach ($plans as $p) {
            if (!isset($p['UtilityTypeID']) || !isset($p['CustomerTypeID'])) continue;
            $numKey = $p['UtilityTypeID'] . '-' . $p['CustomerTypeID'];
            $planMapNumeric[$numKey] = $p['TariffPlanID'];

             $uName = $utilsById[$p['UtilityTypeID']] ?? '';
            $cName = $catsById[$p['CustomerTypeID']] ?? '';
            if ($uName !== '' && $cName !== '') {
                $strKey = normKey($uName) . '-' . normKey($cName); // e.g. 'water-domestic'
                $planMapString[$strKey] = $p['TariffPlanID'];
            }
        }

        ?>
        <script>
            const TARIFF_PLAN_MAP = <?= json_encode($planMapNumeric, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
            const TARIFF_PLAN_MAP_STR = <?= json_encode($planMapString, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
        </script>


    <div class="bg-white p-4 rounded-xl shadow-sm mb-6">
    <div class="flex w-full items-center">
        
        <!-- Middle Column: Effective From -->
        <div class="flex-1 text-left">
            <span class="text-sm text-gray-600 mr-2">Effective From:</span>
            <span class="text-sm font-medium text-gray-900">
                <?= $selectedPlan && $selectedPlan['EffectiveFrom'] 
                    ? htmlspecialchars($selectedPlan['EffectiveFrom']) 
                    : '—' ?>
            </span>
        </div>

        <!-- Right Column: Effective To -->
        <div class="flex-1 text-left">
            <span class="text-sm text-gray-600 mr-2">Effective To:</span>
            <span class="text-sm font-medium text-gray-900">
                <?= $selectedPlan && $selectedPlan['EffectiveTo'] 
                    ? htmlspecialchars($selectedPlan['EffectiveTo']) 
                    : '—' ?>
            </span>
        </div>

    </div>
</div>

        <script>
        // UPDATED: populate EffectiveFrom/To fields when plan changes
        function onPlanChange(){
            const sel = document.getElementById('TariffPlanID');
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('EffectiveFrom').value = opt.dataset.from || '';
            document.getElementById('EffectiveTo').value = opt.dataset.to || '';
        }
        // set initial values when DOM ready
        document.addEventListener('DOMContentLoaded', function(){
            if (document.getElementById('TariffPlanID')) onPlanChange();
        });
        </script>
        <!-- END UPDATED: Plan selector -->


        <div class="bg-white p-6 rounded-xl shadow-lg relative">
            
            <!--Water-Demostic table-->
            <div id="table-water-domestic" class="">
            <div class="flex-1 overflow-y-auto overflow-x-auto border border-gray-200 rounded-lg">

                <?php
                $utilityId = 3  /* REPLACE_THIS */;    
                $customerTypeId = 1  /* REPLACE_THIS */;
                
                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );

                $unit = $unitRow['UnitName'] ?? 'units';

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);

                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                            $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Water-commercial table-->

            <div id="table-water-commercial" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 3  /* REPLACE_THIS */;    
                $customerTypeId = 2 /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; 

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units WC (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Water-government table-->

            <div id="table-water-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 3  /* REPLACE_THIS */;    
                $customerTypeId = 3  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units WG (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({{$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Water-Nonprofit table-->
            
            <div id="table-water-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 3  /* REPLACE_THIS */;    
                $customerTypeId = 4  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units WN (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>
            
            <!--Electricity-Domestic table-->

            <div id="table-electricity-domestic" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 1  /* REPLACE_THIS */;    
                $customerTypeId = 1  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units ED (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Electricity-Commercial table-->

            <div id="table-electricity-commercial" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 1  /* REPLACE_THIS */;    
                $customerTypeId = 2  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units EC (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Electricity-Government table-->

            <div id="table-electricity-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 1  /* REPLACE_THIS */;    
                $customerTypeId = 3  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units EG (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Electricity-Nonprofit table-->

            <div id="table-electricity-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 1  /* REPLACE_THIS */;    
                $customerTypeId = 4  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units EN (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Domestic table-->

            <div id="table-gas-domestic" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 2  /* REPLACE_THIS */;    
                $customerTypeId = 1  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GD (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Commercial table-->

            <div id="table-gas-commercial" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 2  /* REPLACE_THIS */;    
                $customerTypeId = 2  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GC (<?= htmlspecialchars($unit) ?>)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Government table-->

            <div id="table-gas-government" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 2  /* REPLACE_THIS */;    
                $customerTypeId = 3  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default                    
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GG (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                              $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>

            <!--Gas-Nonprofit table-->

            <div id="table-gas-nonprofit" class="hidden">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                $utilityId = 2  /* REPLACE_THIS */;    
                $customerTypeId = 4  /* REPLACE_THIS */; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; // safe default

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">No. of Units GN (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];
                            $slabStart = (int)$r['SlabStart'];
                             $slabEnd = $r['SlabEnd'] === null ? null : (int)$r['SlabEnd'];
                            $slabLabel = htmlspecialchars($r['SlabStart'] . ' - ' . ($r['SlabEnd'] === null ? 'Over' : $r['SlabEnd']));
                            // raw numbers for JS
                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                             // JSON encode to avoid quoting issues
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            // Actions: only Edit button (opens edit modal)
                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick=\"openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed})\" class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                    }
                    } else {
                        echo "<tr><td colspan='4' class='p-4 text-center'>No tariff rows defined for this plan.</td></tr>";
                    }
                    ?>
                </tbody>   
                </table>
            </div>
            </div>


            <!--TABLES OVERRRRRRRRRRRRRRR!!!!!!!!!!!!!!!!!!!!!!!!!!!-->

            <div class="bg-white p-6 rounded-xl shadow-lg relative"> <!--Main white card(container)-->

                <div class="flex items-center justify-start space-x-4 mb-4">

                    <button onclick="openAddSlabModal()" class="px-3 py-1.5 bg-[#213655] text-white rounded-lg shadow hover:bg-[#e5d283] font-semibold transition">
                        + Add New Tariff Row
                    </button>

                    <!-- CHANGESSS!!!!! -->

                    <!--button onclick="openCategoryManagementModal()" class="px-3 py-1.5 bg-[#e5d283] text-white rounded-lg shadow hover:bg-[#213655] font-semibold transition">
                        Category Management
                    </button-->
                </div>

                <!--div class="flex justify-end items-center space-x-4 mb-6">
                     <button class="px-3 py-1.5 bg-[#213655] text-white rounded-lg shadow hover:bg-[#162029] font-semibold transition">
                        Save Changes
                    </button>
                    <button class="px-3 py-1.5 bg-[#b8c3d6] text-[#213655] rounded-lg shadow hover:bg-gray-400 transition">
                        Reset Table
                    </button>
                </div-->
            </div>

        </div>

    </main>
</div>

<!--div id="addTariffModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white p-10 rounded-xl shadow-2xl w-full max-w-xs">
    <h3 class="text-2xl text-[#213655] font-bold mb-4" id="modalTitle">Add / Edit Tariff Slab</h3>

    <form method="post" action="tariff-backend.php" onsubmit="return validateAddSlabForm(this);">
      <input type="hidden" name="action" value="add_slab"> 
      <input type="hidden" name="TariffPlanID" id="modalTariffPlanID" value="">
      <input type="hidden" name="RateID" id="modalRateID" value="">

      <div class="mb-4 grid grid-cols-2 gap-2">
        <div>
          <label class="block text-gray-700 text-sm font-medium mb-1">Slab Start (units)</label>
          <input type="number" name="SlabStart" id="addSlabStart" class="w-full border p-2 rounded" min="0" required>
        </div>
        <div>
          <label class="block text-gray-700 text-sm font-medium mb-1">Slab End (units)</label>
          <input type="number" name="SlabEnd" id="addSlabEnd" class="w-full border p-2 rounded" min="0">
        </div>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Rate per Unit (Rs.)</label>
        <input type="number" step="0.01" id="addRatePerUnit" name="RatePerUnit" class="w-full border p-2 rounded" required>
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Fixed Charge (Rs.)</label>
        <input type="number" step="0.01" id="addFixedCharge" name="FixedCharge" class="w-full border p-2 rounded" required>
      </div>

      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeAddTariffModal()" class="px-3 py-1.5 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
        <button type="submit" class="px-3 py-1.5 bg-[#213655] text-white rounded-lg hover:bg-[#162029]">Save Tariff</button>
      </div>
    </form>
  </div>
</div-->
<!-- END REPLACE -->

<!--        CHANGESSS!!!!         -->

<!--div id="categoryManagementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
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
</div-->

<!-- Add Slab Modal (hidden by default) -->
<div id="modalAddSlab" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
  <div class="bg-white rounded-lg w-11/12 max-w-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Add Slab</h3>
    <form method="post" action="tariff-backend.php" onsubmit="return validateAddSlabForm(this);">
      <input type="hidden" name="action" value="add_slab">
      <input type="hidden" name="TariffPlanID" id="addTariffPlanID" value="">
      <div class="grid grid-cols-2 gap-4 mb-3">
        <div>
          <label class="block text-sm font-medium mb-1">Slab Start (units)</label>
          <input type="number" name="SlabStart" id="addSlabStart" min="0" required class="w-full border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Slab End (units — leave empty for Over)</label>
          <input type="number" name="SlabEnd" id="addSlabEnd" min="0" class="w-full border p-2 rounded">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium mb-1">Rate per Unit (Rs.)</label>
          <input type="number" step="0.01" name="RatePerUnit" id="addRatePerUnit" required class="w-full border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Fixed Charge (Rs.)</label>
          <input type="number" step="0.01" name="FixedCharge" id="addFixedCharge" required class="w-full border p-2 rounded">
        </div>
      </div>

      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeAddSlabModal()" class="px-4 py-2 rounded border text-[#213655] hover:bg-gray-400 bg-[#b8c3d6]">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-[#213655] hover:bg-[#162029] text-white rounded">Save Tariff</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Slab Modal -->
<div id="modalEditSlab" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
  <div class="bg-white rounded-lg w-11/12 max-w-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Edit Slab</h3>
    <form method="post" action="tariff-backend.php" onsubmit="return validateEditSlabForm(this);">
      <input type="hidden" name="action" value="update_slab">
      <input type="hidden" name="RateID" id="editRateID" value="">
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Usage Range (read-only)</label>
        <input type="text" id="editUsageRange" readonly class="w-full border p-2 rounded bg-gray-100">
      </div>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium mb-1">Rate per Unit (Rs.)</label>
          <input type="number" step="0.01" name="RatePerUnit" id="editRatePerUnit" required class="w-full border p-2 rounded">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Fixed Charge (Rs.)</label>
          <input type="number" step="0.01" name="FixedCharge" id="editFixedCharge" required class="w-full border p-2 rounded">
        </div>
      </div>

      <div class="flex justify-end space-x-3">
        <button type="button" onclick="closeEditSlabModal()" class="px-4 py-2 rounded border text-[#213655] bg-[#b8c3d6] hover:bg-gray-400">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-[#213655] hover:bg-[#162029] text-white rounded">Save Tariff</button>
      </div>
    </form>
  </div>
</div>


<script>
    // This script controls the visibility of the Modals/Popups by manipulating their Tailwind classes.

    function openAddSlabModal(utilityId, customerTypeId){
    // If utilityId/customerTypeId are provided as numbers prefer them (backwards compatible).
    let planId = '';
    if (typeof utilityId !== 'undefined' && typeof customerTypeId !== 'undefined') {
        // numeric form: try numeric key
        const keyN = utilityId + '-' + customerTypeId;
        planId = TARIFF_PLAN_MAP[keyN] || '';
    } else {
        // default: build string key from currentUtility/currentCategory
        const keyS = currentUtility + '-' + currentCategory; // e.g. 'water-domestic'
        planId = TARIFF_PLAN_MAP_STR[keyS] || '';
    }

    if(!planId){
        alert('No tariff plan found for this combination. Create the plan first.');
        return;
    }
    document.getElementById('addTariffPlanID').value = planId;
    document.getElementById('addSlabStart').value = '';
    document.getElementById('addSlabEnd').value = '';
    document.getElementById('addRatePerUnit').value = '';
    document.getElementById('addFixedCharge').value = '';
    document.getElementById('modalAddSlab').classList.remove('hidden');
    }


    function closeAddSlabModal(){
        document.getElementById('modalAddSlab').classList.add('hidden');
    }

    function openEditSlabModal(rateId, slabStart, slabEnd, ratePerUnit, fixed){
        document.getElementById('editRateID').value = rateId;
        const label = slabStart + ' - ' + (slabEnd===null ? 'Over' : slabEnd);
        document.getElementById('editUsageRange').value = label;
        document.getElementById('editRatePerUnit').value = ratePerUnit;
        document.getElementById('editFixedCharge').value = fixed;
        document.getElementById('modalEditSlab').classList.remove('hidden');
    }

    function closeEditSlabModal(){
        document.getElementById('modalEditSlab').classList.add('hidden');
    }

    // client-side validation for add
    function validateAddSlabForm(form){
        const s = parseInt(form.SlabStart.value || '0', 10);
        const eRaw = form.SlabEnd.value.trim();
        const e = eRaw === '' ? null : parseInt(eRaw, 10);
        const r = parseFloat(form.RatePerUnit.value || '0');
        const f = parseFloat(form.FixedCharge.value || '0');

        if(isNaN(s) || s < 0){ alert('Slab start must be >= 0'); return false; }
        if(e !== null && e < s){ alert('Slab end must be >= slab start'); return false; }
        if(isNaN(r) || r < 0){ alert('Rate must be >= 0'); return false; }
        if(isNaN(f) || f < 0){ alert('Fixed charge must be >= 0'); return false; }
        return true;
    }

    // client-side validation for edit
    function validateEditSlabForm(form){
        const r = parseFloat(form.RatePerUnit.value || '0');
        const f = parseFloat(form.FixedCharge.value || '0');
        if(isNaN(r) || r < 0){ alert('Rate must be >= 0'); return false; }
        if(isNaN(f) || f < 0){ alert('Fixed charge must be >= 0'); return false; }
        return true;
    }



    // --- Add New Tariff Modal Functions ---
    /*function openAddTariffModal() {
    const form = document.querySelector('#addTariffModal form');
    if (form) form.querySelector('input[name="action"]').value = 'add_slab';
    document.getElementById('modalTariffPlanID').value = (document.getElementById('TariffPlanID') ? document.getElementById('TariffPlanID').value : '');
    document.getElementById('modalRateID').value = '';
    document.getElementById('addSlabStart').value = '';
    document.getElementById('addSlabEnd').value = '';
    document.getElementById('addRatePerUnit').value = '';
    document.getElementById('addFixedCharge').value = '';
    document.getElementById('addTariffModal').classList.remove('hidden');
    document.getElementById('addTariffModal').classList.add('flex');
}

    function closeAddTariffModal() {
        // Adds 'hidden' and removes 'flex' to hide the modal.
        document.getElementById("addTariffModal").classList.add("hidden");
        document.getElementById("addTariffModal").classList.remove("flex");
    }

    // --- Category Management Modal Functions (Works the same way) ---
    /*function openCategoryManagementModal() {
        document.getElementById("categoryManagementModal").classList.remove("hidden");
        document.getElementById("categoryManagementModal").classList.add("flex");
    }*/

    /*function closeCategoryManagementModal() {
        document.getElementById("categoryManagementModal").classList.add("hidden");
        document.getElementById("categoryManagementModal").classList.remove("flex");
    }*/
    
    // --- Edit Tariff Modal Stub (Placeholder function for the table's Edit button) ---
    /*function openEditTariffModal(rateId, slabStart, slabEnd, ratePerUnit, fixedCharge, rateUnitId) {
    const form = document.querySelector('#addTariffModal form');
    document.getElementById('modalTitle').innerText = rateId ? 'Edit Tariff Slab' : 'Add Tariff Slab';
    document.getElementById('addSlabStart').value = slabStart !== undefined && slabStart !== null ? slabStart : '';
    document.getElementById('addSlabEnd').value = slabEnd !== undefined && slabEnd !== null ? slabEnd : '';
    document.getElementById('addRatePerUnit').value = ratePerUnit !== undefined ? ratePerUnit : '';
    document.getElementById('addFixedCharge').value = fixedCharge !== undefined ? fixedCharge : '';
    document.getElementById('modalRateID').value = rateId || '';
    document.getElementById('modalTariffPlanID').value = (document.getElementById('TariffPlanID') ? document.getElementById('TariffPlanID').value : '');

    // set form action to update if editing
    if (form) {
        form.querySelector('input[name="action"]').value = rateId ? 'update_slab' : 'add_slab';
        form.querySelector('input[name="TariffPlanID"]').value = document.getElementById('modalTariffPlanID').value;
    }

    openAddTariffModal();
    }

    function validateAddSlabForm(form){
        const start = parseInt(form.SlabStart.value || '0', 10);
        const endRaw = form.SlabEnd.value;
        const end = (endRaw === '' ? null : parseInt(endRaw, 10));
        const rate = parseFloat(form.RatePerUnit.value);
        if (isNaN(start) || start < 0) { alert('Slab start must be >= 0'); return false; }
        if (end !== null && end < start) { alert('Slab end must be >= start'); return false; }
        if (isNaN(rate) || rate < 0) { alert('Rate must be >= 0'); return false; }
        // TariffPlanID must be set
        const sel = document.getElementById('TariffPlanID');
        if(sel) form.TariffPlanID.value = sel.value;
        return true;
    }*/

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
