<?php
session_start();
if(!isset($_SESSION['Username'])){
    header("Location: .././login.php");
    exit;
}
include ('./include/header.php'); ?>

<div class="flex">

<?php include ('./include/sidebar.php'); 
     include('./include/db.php');

$stats = executeQuery($pdo,"EXEC sp_dashCount",[],true);
$tot_count = $stats['tot_consumers'];
$electricity = $stats['ele_conns'];
$water = $stats['water_conns'];
$gas = $stats['gas_conns'];

$x= "SELECT * FROM vw_recentComplaint WHERE ComplaintStatusID in (1,2)";
$r_complaints = executeQuery($pdo,$x,[],false);

$c_count =executeQuery($pdo,"SELECT COUNT(*) AS cnt FROM Customers",[],true);
$c_count = isset($c_count['cnt']) ? (int)$c_count['cnt'] : 0;

$u_count =executeQuery($pdo,"SELECT COUNT(*) AS usr FROM Users",[],true);
$u_count = isset($u_count['usr']) ? (int)$u_count['usr'] : 0;
?>


<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64 bg-gradient-to-br from-[#213655] to-[#e5d283]">
    <div class="flex flex-cols justify-between mb-10">
        <h2 class="text-3xl font-semibold p-2 text-[#ffffff]">Dashboard overview</h2>
        <h2 class="text-3xl font-bold mb-10 text-[#ffffff]">Welcome Back, <?php echo htmlspecialchars($_SESSION['Username']); ?> </h2>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 w-full">
    
        <div class="p-8 bg-[#e5d283] text-center rounded-xl w-full ">
            <h3 class="text-xl font-semibold text-[#213655]">Total Connections</h3>
            <p class="text-3xl font-bold text-[#213655]"><?php echo $tot_count ?></p>
        </div>

        <div class="p-8 bg-[#162029] text-center rounded-xl w-full">
            <h3 class="text-xl font-semibold text-white">Electricity Connections</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $electricity ?></p>
        </div>

         <div class="p-8 bg-[#162029] text-center rounded-xl  w-full">
            <h3 class="text-xl font-semibold text-white">Water Connections</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $water ?></p>
        </div>

        <div class="p-8 bg-[#162029] text-center rounded-xl w-full">
            <h3 class="text-xl font-semibold text-white">Gas Connections</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $gas ?></p>
        </div>

    </div>

   <div class="flex mt-10 gap-10 flex-1">

        <div class="flex-1 bg-[#f0f0f0] shadow-lg rounded-xl p-6 overflow-y-auto">
            <a class="text-xl font-bold mb-8 text-[#213655] cursor-pointer hover:underline hover:text-blue-600">Recent complaints</a>
            <div class="space-y-4">
                <?php if(!empty($r_complaints)) : ?>
                    <?php foreach ($r_complaints as $r): ?>
                        <div class="border-b p-3">
                            <div class="flex justify-between">
                                <span class="font-semibold text-[#162029]">ID: <?= $r['ComplaintID']; ?></span>
                                <span class="text-sm text-[#213655]"><?= date('Y-m-d',strtotime($r['ComplaintDate'])); ?></span>
                            </div>

                            <p class="text-sm text-[#162029] mt-1 truncate">
                                <?= htmlspecialchars($r['ComplaintDescription']); ?>
                            </p>

                            <span class="inline-block mt-1 text-sm font-medium
                                <?= ($r['ComplaintStatusName']=="Pending" ? 'text-red-600' :
                                    ($r['ComplaintStatusName']=="In Progress" ? 'text-yellow-600' : 'text-green-600'
                    )); ?>">
                            <?= $r['ComplaintStatusName']; ?>
                            </span>

                        </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-[#213655] text-sm">No recent complaints.</p>
                    <?php endif; ?>
            </div> 
        </div>

        <div class="flex flex-col gap-5 items-center py-3">
                <div class="px-6 py-8 border-[#e5d283] border-[3px] text-center rounded-xl w-full ">
            <h3 class="text-xl font-bold text-[#213655]">Total Customers</h3>
            <p class="mt-2 text-3xl font-bold text-[#213655]"><?php echo $c_count ?></p>
        </div>

        <div class="px-6 py-8 border-[#e5d283] border-[3px] text-center rounded-xl w-full">
            <h3 class="text-xl font-bold text-[#213655]">Total Users</h3>
             <p class="mt-2 text-3xl font-bold text-[#213655]"><?php echo $u_count ?></p>
        </div>

        </div>

        <div class="flex flex-col gap-6">
            <button class="bg-[#213655] text-white font-semibold py-4 px-6 rounded shadow hover:bg-[#162029] transition"><a href="customer.php">Add new customer</button>
            <button class="bg-[#213655] text-white font-semibold py-4 px-6 rounded shadow hover:bg-[#162029] transition"><a href="Tariff1.php">Add new tariff plan</button>
            <button class="bg-[#213655] text-white font-semibold py-4 px-6 rounded shadow hover:bg-[#162029] transition"><a href="user/addUser.php">Add new user</a></button>
            <button class="bg-[#213655] text-white font-semibold py-4 px-6 rounded shadow hover:bg-[#162029] transition"><a href="customer.php">Add new connection</button>
        </div>
   </div>
</main>
 </div>



 