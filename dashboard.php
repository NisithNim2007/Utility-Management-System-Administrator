<?php
session_start();
//check if logged
if(!isset($_SESSION['Username'])){
    header("Location: .././login.php");
    exit;
}
include ('./include/header.php'); ?>

<div class="flex">

<?php include ('./include/sidebar.php'); 
     include('./include/db.php');

//fetch values from db (counts)
$stats = executeQuery($pdo,"EXEC sp_dashCount",[],true);


//assign to var
$tot_count = $stats['tot_consumers'];
$electricity = $stats['ele_conns'];
$water = $stats['water_conns'];
$gas = $stats['gas_conns'];
?>


<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64">
    <div class="flex flex-cols justify-between">
        <h2 class="text-3xl font-bold text-[#162029]">Dashboard overview</h2>
        <h2 class="text-3xl font-bold mb-10 text-gray-900">Welcome <?php echo htmlspecialchars($_SESSION['Username']); ?> </h2>
    </div>
    
    
    <!--cards-->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 w-full">
    
        <div class="p-8 bg-[#e5d283] text-center rounded-xl w-full ">
            <h3 class="text-xl font-semibold text-[#213655]">Total Customers</h3>
            <p class="mt-2 text-3xl font-bold text-[#213655]"><?php echo $tot_count ?></p>
        </div>

        <div class="p-8 bg-[#213655] text-center rounded-xl w-full">
            <h3 class="text-xl font-semibold text-white">Electricity Consumers</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $electricity ?></p>
        </div>

         <div class="p-8 bg-[#213655] text-center rounded-xl  w-full">
            <h3 class="text-xl font-semibold text-white">Water Consumers</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $water ?></p>
        </div>

        <div class="p-8 bg-[#213655] text-center rounded-xl w-full">
            <h3 class="text-xl font-semibold text-white">Gas Consumers</h3>
             <p class="mt-2 text-3xl font-bold text-white"><?php echo $gas ?></p>
        </div>

    </div>

   <div class="flex mt-10 gap-10 flex-1">

        <div class="flex-1 bg-white shadow rounded-xl p-6 overflow-y-auto">
            <a class="text-xl font-bold mb-5 text-blue-800 cursor-pointer hover:underline hover:text-blue-600">Recent complaints</a>
            
        </div>

        <div class="flex flex-col gap-4">
            <button class="bg-blue-900 text-white font-semibold py-4 px-6 rounded shadow hover:bg-blue-700 transition">Add new customer</button>
            <button class="bg-blue-900 text-white font-semibold py-4 px-6 rounded shadow hover:bg-blue-700 transition">Add new tariff plan</button>
            <button class="bg-blue-900 text-white font-semibold py-4 px-6 rounded shadow hover:bg-blue-700 transition">Add new user</button>
        </div>
        
   </div>

   


</main>

 </div>



 