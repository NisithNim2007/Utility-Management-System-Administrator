<?php 

    session_start();

    include ('include/header.php'); 
    include('include/db.php');
?>

<div class="min-h-screen bg-gradient-to-br from-[#213655] to-[#e5d283]">
    
    <?php include ('include/sidebar.php'); ?>

    <main class="p-8 overflow-auto ml-64">

        <h2 class="text-3xl font-bold text-[#f0f0f0] mb-4">TARIFF MANAGEMENT</h2>

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

        <div class="mb-6 pb-4">
            <h3 class="text-lg font-semibold text-[#f0f0f0] mb-3">Facility</h3>
            <div class="flex space-x-3" role="tablist" aria-label="Utilities">
                <button id="util-water" data-utility="water" class="px-4 py-2 text-white bg-[#2B456E] rounded-lg shadow-md font-medium hover:bg-[#2B456E] transition focus:outline-none" onclick="selectUtility('water')">Water Utility</button>
                <button id="util-electricity" data-utility="electricity" class="px-4 py-2 rounded-lg font-medium bg-[#213655] text-white hover:bg-[#2B456E] transition focus:outline-none" onclick="selectUtility('electricity')">Electricity Utility</button>
                <button id="util-gas" data-utility="gas" class="px-4 py-2 rounded-lg font-medium bg-[#213655] text-white hover:bg-[#2B456E] transition focus:outline-none" onclick="selectUtility('gas')">Gas Utility</button>
            </div>
        </div>

        <div class="mb-3">
            <h3 class="text-lg font-semibold text-[#f0f0f0] mb-3">Consumer Group Category</h3>
            <div id="category-tabs" class="flex border-b border-gray-300 overflow-x-auto space-x-4">
                <button id="cat-domestic" data-category="domestic" class="py-2 px-4 border-b-2 border-[#b8c3d6] text-[#f0f0f0] font-medium whitespace-nowrap focus:outline-none hover:text-[#213655] hover:bg-[#e5d283] hover:border-b-2 hover:border-[#e5d283] transition rounded" onclick="selectCategory('domestic')">Domestic Users</button>
                <button id="cat-commercial" data-category="commercial" class="py-2 px-4 text-[#f0f0f0] hover:text-[#213655] hover:bg-[#e5d283] hover:border-b-2 hover:border-[#e5d283] transition whitespace-nowrap rounded" onclick="selectCategory('commercial')">Commercial Users</button>
                <button id="cat-government" data-category="government" class="py-2 px-4 text-[#f0f0f0] hover:text-[#213655] hover:bg-[#e5d283] hover:border-b-2 hover:border-[#e5d283] transition whitespace-nowrap rounded" onclick="selectCategory('government')">Government Institutions</button>
                <button id="cat-nonprofit" data-category="nonprofit" class="py-2 px-4 text-[#f0f0f0] hover:text-[#213655] hover:bg-[#e5d283] hover:border-b-2 hover:border-[#e5d283] transition whitespace-nowrap rounded" onclick="selectCategory('nonprofit')">Religous/Non-profit Organizations</button>
            </div>
        </div>
        <?php
            $plans = executeQuery($pdo, "SELECT TariffPlanID, CustomerTypeID, UtilityTypeID FROM TariffPlans WHERE IsActive = 1 ORDER BY TariffPlanID");
            /*if (!empty($plans)) {
                $selectedPlan = $plans[0];
            } else {
                $selectedPlan = [
                    'EffectiveFrom' => null,
                    'EffectiveTo'   => null
                ];
            }*/

        $utils = executeQuery($pdo, "SELECT UtilityTypeID, UtilityTypeName FROM UtilityTypes");
        $cats  = executeQuery($pdo, "SELECT CustomerTypeID, CustomerTypeName FROM CustomerTypes");

        $utilsById = [];
        foreach ($utils as $u) $utilsById[$u['UtilityTypeID']] = $u['UtilityTypeName'];

        $catsById = [];
        foreach ($cats as $c) $catsById[$c['CustomerTypeID']] = $c['CustomerTypeName'];

        function normKey($s) {
            $s = strtolower(trim((string)$s));
            $s = preg_replace('/[^a-z0-9]+/', '-', $s);
            $s = str_replace(['-and-', ' and ', ' & '], '-', $s);
            $s = str_replace(['religous','religious','non-profit','nonprofit','non-profit-organizations'], 'nonprofit', $s);
            $s = str_replace(['domestic','commercial','government','nonprofit','water','gas','electricity','electric','gn','gc','ge','en','ed'], $s, $s);
            $s = preg_replace('/-+/', '-', $s);
            $s = trim($s, '-');
            return $s;
        }

        $planMapNumeric = [];
        $planMapString  = [];
        foreach ($plans as $p) {
            if (!isset($p['UtilityTypeID']) || !isset($p['CustomerTypeID'])) continue;
            $numKey = $p['UtilityTypeID'] . '-' . $p['CustomerTypeID'];
            $planMapNumeric[$numKey] = $p['TariffPlanID'];

             $uName = $utilsById[$p['UtilityTypeID']] ?? '';
            $cName = $catsById[$p['CustomerTypeID']] ?? '';
            if ($uName !== '' && $cName !== '') {
                $strKey = normKey($uName) . '-' . normKey($cName);
                $planMapString[$strKey] = $p['TariffPlanID'];
            }
        }

        ?>
        <script>
            const TARIFF_PLAN_MAP = <?= json_encode($planMapNumeric, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
            const TARIFF_PLAN_MAP_STR = <?= json_encode($planMapString, JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
        </script>


    <div class="bg-transparent p-4 rounded-xl mb-1">
    <div class="flex w-full items-center">
        
        <div class="flex-1 text-left">
            <span class="text-sm text-white mr-2 font-bold">Effective From:</span>
            <span class="text-sm font-medium text-[#213655] font-bold">
                2024-01-01
            </span>
        </div>

        <div class="flex-1 text-left">
            <span class="text-sm text-white mr-2 font-bold">Effective To:</span>
            <span class="text-sm font-medium text-[#213655] font-bold">
                2029-12-31
            </span>
        </div>
    </div>
    </div>
    
        <div class="bg-white p-6 rounded-xl shadow-lg relative">
            
            <!--Water-Domestic table-->
            <?php
                $utilityId = 3;    
                $customerTypeId = 1; 
            ?>

            <div id="table-water-domestic" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                
                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );

                $unit = $unitRow['UnitName'] ?? 'units';

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);

                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;

                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>";
                            echo "<button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>✏️ Edit</button>";
                            echo "</td>";
                        echo "</tr>";

                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;

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

            <?php
                $utilityId = 3;    
                $customerTypeId = 2; 
            ?>

            <div id="table-water-commercial" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units'; 

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;

                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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

            <?php
                $utilityId = 3;    
                $customerTypeId = 3; 
            ?>

            <div id="table-water-government" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;

                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
            
            <?php
                $utilityId = 3;    
                $customerTypeId = 4; 
            ?>

            <div id="table-water-nonprofit" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;

                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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

            <?php
                $utilityId = 1;    
                $customerTypeId = 1; 
            ?>

            <div id="table-electricity-domestic" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php
                
                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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

            <?php
                $utilityId = 1;    
                $customerTypeId = 2; 
            ?>
            <div id="table-electricity-commercial" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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

            <?php
                $utilityId = 1;    
                $customerTypeId = 3; 
            ?>
            <div id="table-electricity-government" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
            <?php
                $utilityId = 1;    
                $customerTypeId = 4; 
            ?>
            <div id="table-electricity-nonprofit" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>
                            <button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>
                                ✏️ Edit
                            </button>
                        </td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
                $utilityId = 2;    
                $customerTypeId = 1; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";

                            echo "<td class='px-4 py-3 whitespace-nowrap'>";
                            echo "<button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>✏️ Edit</button>";
                            echo "</td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
                $utilityId = 2;    
                $customerTypeId = 2; 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];
                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>";
                            echo "<button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>✏️ Edit</button>";
                            echo "</td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
            <?php
                $utilityId = 2;    
                $customerTypeId = 3; 
            ?>
            <div id="table-gas-government" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php 

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';                    
                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>";
                            echo "<button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>✏️ Edit</button>";
                            echo "</td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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
            <?php
                $utilityId = 2;    
                $customerTypeId = 4; 
            ?>
            <div id="table-gas-nonprofit" class="hidden" data-utility-id="<?= $utilityId ?>" data-customer-id="<?= $customerTypeId ?>">
            <div class="max-h-[380px] overflow-y-auto overflow-x-auto border border-gray-100 rounded-lg">
                
                <?php

                $unitRow = executeQuery($pdo,
                    "SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?",
                    [$utilityId],
                    true
                );
                $unit = $unitRow['UnitName'] ?? 'units';

                $rates = executeQuery($pdo, "EXEC dbo.sp_GetTariffRatesByUtilityAndCustomer ?, ?", [$utilityId, $customerTypeId]);
                ?>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#213655] sticky top-0 z-20">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">No. of Units (<?= htmlspecialchars($unit) ?>) </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Rate per Unit (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Fixed Charge (Rs.)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-[#ffffff] uppercase tracking-wider w-1/4">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php

                    if (!empty($rates)) {
                        $prevRatePerUnit = null;
                        $prevFixedCharge = null;
                        $prevSlabStart = null;
                        $prevSlabEnd = null;
                        foreach ($rates as $r) {
                            $rateId = (int)$r['RateID'];

                            $slabStart = isset($r['SlabStart']) ? (int)$r['SlabStart'] : 0;
                            $rawEnd = $r['SlabEnd'];

                            if ($rawEnd === null || (is_numeric($rawEnd) && intval($rawEnd) === 2147483647)) {
                                $slabEnd = null;
                            } else {
                                $slabEnd = (int)$rawEnd;
                            }

                            if ($slabEnd === null) {
                                $slabLabel = htmlspecialchars("Over {$slabStart}");
                            } else {
                                $slabLabel = htmlspecialchars("{$slabStart} - {$slabEnd}");
                            }

                            $ratePerUnitRaw = (float)$r['RatePerUnit'];
                            $fixedRaw = (float)$r['FixedCharge'];

                            $jsRateId = json_encode($rateId);
                            $jsSlabStart = json_encode($slabStart);
                            $jsSlabEnd = json_encode($slabEnd);
                            $jsRatePerUnit = json_encode($ratePerUnitRaw);
                            $jsFixed = json_encode($fixedRaw);
                            $jsPrevStart = json_encode($prevSlabStart);
                            $jsPrevEnd   = json_encode($prevSlabEnd);
                            $jsPrevRate  = json_encode($prevRatePerUnit);
                            $jsPrevFixed = json_encode($prevFixedCharge);
                            echo "<tr>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>{$slabLabel}</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($ratePerUnitRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>" . number_format($fixedRaw, 2) . "</td>";
                            echo "<td class='px-4 py-3 whitespace-nowrap'>";
                            echo "<button onclick='openEditSlabModal({$jsRateId}, {$jsSlabStart}, {$jsSlabEnd}, {$jsRatePerUnit}, {$jsFixed}, {$jsPrevStart}, {$jsPrevEnd}, {$jsPrevRate}, {$jsPrevFixed})' class='text-[#213655] hover:text-[#0b121c] transition mr-2'>✏️ Edit</button>";
                            echo "</td>";
                        echo "</tr>";
                        $prevRatePerUnit = $ratePerUnitRaw;
                        $prevFixedCharge = $fixedRaw;
                        $prevSlabStart   = $slabStart;
                        $prevSlabEnd     = $slabEnd;
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

            <div class="bg-white p-6 rounded-xl shadow-lg relative">

                <div class="flex items-center justify-start space-x-4 mb-4">

                    <button id="addTariffBtn"
                        data-prev-rate="<?= isset($lastRatePerUnit) ? $lastRatePerUnit : '' ?>"
                        data-prev-fixed="<?= isset($lastFixedCharge) ? $lastFixedCharge : '' ?>"
                        onclick="openAddSlabModal()"
                        class="px-3 py-1.5 bg-[#213655] text-white rounded-lg shadow hover:bg-[#e5d283] font-semibold transition">
                        + Add New Tariff Row
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="modalAddSlab" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
  <div class="bg-white rounded-lg w-11/12 max-w-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Add Slab</h3>
    <form method="post" action="tariff-backend.php" onsubmit="console.log('Submitting add form -> TariffPlanID=', this.TariffPlanID.value, 'SlabStart=', this.SlabStart.value, 'SlabEnd=', this.SlabEnd.value, 'Rate=', this.RatePerUnit.value, 'Fixed=', this.FixedCharge.value); return validateAddSlabForm(this);">
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
    function openAddSlabModal(utilityId, customerTypeId) {
        let planId = '';

        if (typeof utilityId !== 'undefined' && typeof customerTypeId !== 'undefined') {
            const keyN = utilityId + '-' + customerTypeId;
            planId = TARIFF_PLAN_MAP[keyN] || '';
        }

        if (!planId) {
            const visibleTable = document.querySelector('[id^="table-"]:not(.hidden)');
            if (visibleTable) {
                const uId = visibleTable.getAttribute('data-utility-id');
                const cId = visibleTable.getAttribute('data-customer-id');
                
                if (uId && cId) {
                    const keyN = uId + '-' + cId;
                    planId = TARIFF_PLAN_MAP[keyN] || '';
                    console.log('Found plan via visible table attributes:', keyN, '->', planId);
                }
            }
        }

        if (!planId) {
            const keyS = currentUtility + '-' + currentCategory; 
            planId = TARIFF_PLAN_MAP_STR[keyS] || '';
        }

        if (!planId) {
            console.error('Plan lookup failed. State:', currentUtility, currentCategory, 'Map:', TARIFF_PLAN_MAP_STR);
            alert('No tariff plan found for this combination (' + currentUtility + '-' + currentCategory + '). Please ensure a plan exists in the database.');
            return;
        }

        document.getElementById('addTariffPlanID').value = planId;
        console.log('openAddSlabModal -> planId:', planId);

        document.getElementById('addSlabStart').value = '';
        document.getElementById('addSlabEnd').value = '';
        document.getElementById('addRatePerUnit').value = '';
        document.getElementById('addFixedCharge').value = '';
        
        const modal = document.getElementById('modalAddSlab');
        modal.classList.remove('hidden');
    }


    function closeAddSlabModal(){
        document.getElementById('modalAddSlab').classList.add('hidden');
    }

    function openEditSlabModal(rateId, slabStart, slabEnd, ratePerUnit, fixed, prevStart, prevEnd, prevRate, prevFixed) {
        document.getElementById('editRateID').value = rateId;
        const label = slabStart + ' - ' + (slabEnd === null ? 'Over' : slabEnd);
        document.getElementById('editUsageRange').value = label;
        document.getElementById('editRatePerUnit').value = (ratePerUnit !== null && ratePerUnit !== undefined) ? ratePerUnit : '';
        document.getElementById('editFixedCharge').value = (fixed !== null && fixed !== undefined) ? fixed : '';

        const modal = document.getElementById('modalEditSlab');
        modal.dataset.prevStart = (prevStart !== undefined && prevStart !== null) ? String(prevStart) : '';
        modal.dataset.prevEnd   = (prevEnd   !== undefined && prevEnd   !== null) ? String(prevEnd)   : '';
        modal.dataset.prevRate  = (prevRate  !== undefined && prevRate  !== null) ? String(prevRate)  : '';
        modal.dataset.prevFixed = (prevFixed !== undefined && prevFixed !== null) ? String(prevFixed) : '';

        modal.classList.remove('hidden');
    }

    function closeEditSlabModal(){
        document.getElementById('modalEditSlab').classList.add('hidden');
    }

    function validateAddSlabForm(form){
        const s = parseInt(form.SlabStart.value || '0', 10);
        const eRaw = (form.SlabEnd.value || '').trim();
        const e = eRaw === '' ? null : parseInt(eRaw, 10);
        const r = parseFloat(form.RatePerUnit.value || '0');
        const f = parseFloat(form.FixedCharge.value || '0');


        if (isNaN(s) || s < 0) {
            alert('Slab start must be >= 0');
            return false;
        }
        if (e !== null && e <= s) {
            alert('Slab end must be > slab start');
            return false;
        }
        if (isNaN(r) || r < 0) {
            alert('Rate must be >= 0');
            return false;
        }
        if (isNaN(f) || f < 0) {
            alert('Fixed charge must be >= 0');
            return false;
        }

        const tableContainer = Array.from(document.querySelectorAll('[id^="table-"]')).find(t => !t.classList.contains('hidden'));
        if (!tableContainer) {
            return true;
        }

        const tbody = tableContainer.querySelector('tbody');
        if (!tbody) return true;

        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length === 0) return true;

        const lastRow = rows[rows.length - 1];

        const lastCols = lastRow.querySelectorAll('td');
        if (lastCols.length < 3) return true;

        const slabText = lastCols[0].innerText.trim();
        let prevStart = null;
        let prevEnd = null; 
        if (/^over\s+/i.test(slabText)) {
            prevStart = parseInt(slabText.replace(/^over\s+/i, '').trim(), 10);
            prevEnd = 2147483647;
        } else if (slabText.includes('-')) {
            const parts = slabText.split('-').map(p => p.trim());
            prevStart = parseInt(parts[0], 10);
            prevEnd = parts[1].toLowerCase() === 'over' ? 2147483647 : parseInt(parts[1], 10);
        }

        const prevRateRaw = lastCols[1].innerText.replace(/,/g, '').trim();
        const prevFixedRaw = lastCols[2].innerText.replace(/,/g, '').trim();
        const prevRate = prevRateRaw !== '' ? parseFloat(prevRateRaw) : null;
        const prevFixed = prevFixedRaw !== '' ? parseFloat(prevFixedRaw) : null;

        if (prevRate !== null && !isNaN(prevRate) && r < prevRate) {
            alert('Rate per unit must be greater than or equal to the previous slab rate (' + prevRate + ').');
            return false;
        }
        if (prevFixed !== null && !isNaN(prevFixed) && f < prevFixed) {
            alert('Fixed charge must be greater than or equal to the previous slab fixed charge (' + prevFixed + ').');
            return false;
        }

        const SENTINEL = 2147483647;
        if (prevEnd !== null && !isNaN(prevEnd)) {
            if (prevEnd === SENTINEL) {
                
                if (prevStart === null || isNaN(prevStart) || s <= prevStart) {
                    alert('New slab start must be greater than the existing final slab start (' + prevStart + ').');
                    return false;
                }
            } else {
                if (s <= prevEnd) {
                    alert('New slab start must be greater than previous slab end (' + prevEnd + ').');
                    return false;
                }
            }
        }

        return true;
    }

    function validateEditSlabForm(form) {
        const r = parseFloat(form.RatePerUnit.value || '0');
        const f = parseFloat(form.FixedCharge.value || '0');

        if (isNaN(r) || r < 0) { alert('Rate must be >= 0'); return false; }
        if (isNaN(f) || f < 0) { alert('Fixed charge must be >= 0'); return false; }

        const modal = document.getElementById('modalEditSlab');
        const prevRateRaw  = modal.dataset.prevRate  || '';
        const prevFixedRaw = modal.dataset.prevFixed || '';

        const prevRate  = prevRateRaw !== '' ? parseFloat(prevRateRaw) : null;
        const prevFixed = prevFixedRaw !== '' ? parseFloat(prevFixedRaw) : null;

        if (prevRate !== null && !isNaN(prevRate) && r < prevRate) {
            alert('Rate per unit must be greater than or equal to the previous slab rate (' + prevRate + ').');
            return false;
        }
        if (prevFixed !== null && !isNaN(prevFixed) && f < prevFixed) {
            alert('Fixed charge must be greater than or equal to the previous slab fixed charge (' + prevFixed + ').');
            return false;
        }
        return true;
    }

    let currentUtility = 'water';      
    let currentCategory = 'domestic';  


    function selectUtility(util) {
        currentUtility = util;
        document.querySelectorAll('[data-utility]').forEach(btn => {
            btn.classList.remove('bg-[#2B456E]', 'bg-[#213655]', 'text-white', 'shadow-md');
            if (btn.dataset.utility === util) {
                btn.classList.add('bg-[#2B456E]', 'text-white', 'shadow-md');
            } else {
                btn.classList.add('bg-[#213655]', 'text-white');
            }
        });
        showTable();
    }

    function selectCategory(cat) {
        currentCategory = cat;

        document.querySelectorAll('#category-tabs button').forEach(btn => {
            btn.classList.remove(
                'border-b-2', 'border-[#e5d283]', 'text-[#e5d283]', 'font-bold',
                'border-transparent', 'text-[#f0f0f0]', 'font-normal'           
            );

            if (btn.dataset.category === cat) {
                btn.classList.add('border-b-2', 'border-[#e5d283]', 'text-[#e5d283]', 'font-bold');
            } else {
                btn.classList.add('border-b-2', 'border-transparent', 'text-[#f0f0f0]', 'font-normal');
            }
        });

        showTable();
    }

    
    function showTable() {
      const expectedId = `table-${currentUtility}-${currentCategory}`;

      document.querySelectorAll('[id^="table-"]').forEach(el => {
        el.classList.add('hidden');
      });

      const target = document.getElementById(expectedId);
      if (target) {
        target.classList.remove('hidden');
      } else {
        
        console.warn('Missing table container for:', expectedId);
      }
    }


    document.addEventListener('DOMContentLoaded', function() {
      selectUtility(currentUtility);  
      selectCategory(currentCategory); 
    });

</script>
