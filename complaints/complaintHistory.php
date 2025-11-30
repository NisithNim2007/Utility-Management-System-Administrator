<?php 
session_start();
//check if logged
if(!isset($_SESSION['Username'])){
    header("Location: .././login.php");
    exit;
}

include ('../include/header.php'); ?>
<div class="flex">
<?php include ('../include/sidebar.php'); 
    include('../include/db.php');

//search_PHP
 $search = trim($_POST['search'] ?? null);
 $status = 3;
 $utility = $_POST['utility'] ?? "";
 $params =[];
 $query="";


 if(!empty($search) || !empty($status) || !empty($utility))
{
    $query="EXEC sp_load_complaint @searchTerm = :searchTerm, @statusID = :statusID, @utility = :utility";
    $params =[
         ':searchTerm' => ($search !== "" ? "%$search%" : NULL),
         ':statusID' => ($status !== "" ? $status : NULL),
         ':utility' => ($utility !== "" ? $utility : NULL)
    ];
}else{
    $query= "EXEC sp_load_complaint @searchTerm= NULL, @statusID = NULL";
    $params=[];
}
$complaints_his = executeQuery($pdo,$query,$params,false);
    
 
?>

<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64">
    <h2 class="text-3xl font-semibold mb-6">Complaint History</h2>
    
    <div class="flex justify-between items-center mb-6 ">
        
        <form method="POST">
            <div class="flex w-full max-w-md mt-7">
            <input type="text" name="search" placeholder="Search here" class="w-full py-2 px-5 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-blue-400" value="<?= htmlspecialchars($search ?? '') ?>">
             <select name="utility" class="border px-3 py-2 rounded">
                    <option value="" selected>All</option>
                    <option value="1" <?= ($utility ?? '')=='1' ? 'selected':'' ?>>Electricity</option>
                    <option value="2" <?= ($utility?? '')=='2' ? 'selected':'' ?>>Gas</option>
                     <option value="3" <?= ($utility?? '')=='3' ? 'selected':'' ?>>Water</option>
                </select>
            <button class="bg-blue-600 text-white px-5 rounded-r-lg hover:bg-blue-700 transition" type="submit">Search</button>
            <a href="complaintHistory.php" class="bg-blue-600 text-white px-5 ml-10 py-2 rounded hover:bg-blue-700 transition">Clear</a>
            </div>   
        </form>
            
    </div>



  <!--display-->  
  <table class="w-full bg-white rounded-xl shadow border-4 boarder-collapse overflow-hidden table-fixed">
        <thead>
            <tr class="bg-[#f0f0f0] text-left text-[#162029]">
            <th class="p-3 w-32">Complaint Id</th>
            <!--<th class="p-3 w-32">Customer Id</th>-->
            <th class="p-3 w-50">Name</th>
            <!--<th class="p-3 w-32">Connection Id</th>-->
            <th class="p-3 w-32">Utility Type</th>
            <th class="p-3 w-85">Description</th>
            <th class="p-3 w-32">Date</th>
            <th class="p-3 w-32">Status</th>
            <th class="p-3 w-32">View</th>
        </tr> 
        </thead>   
        <tbody> 
            <?php if(!empty($complaints_his)): ?>
                <?php foreach($complaints_his as $row): ?>
                    <tr class="border-b h-12">
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintID'])?></td>
                    <!--<td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?//htmlspecialchars($row['CustomerID'])?></td>-->
                         <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['CustomerName'])?></td>
                    <!--<td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?//htmlspecialchars($row['ConnectionID'])?></td>-->
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['UtilityTypeName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintDescription'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= date('Y-m-d',strtotime($row['ComplaintDate']))?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap">
                                <span class="px-3 py-1 bg-green-300 text-green-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                        </td>

                        <!--view button-->
                        <td class="p-3">
                            <button class="bg-blue-800 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition ml-5" 
                            onclick="openComplaintModal(
                                '<?= htmlspecialchars($row['ComplaintID'])?>','<?= htmlspecialchars($row['CustomerID'])?>','<?= htmlspecialchars($row['CustomerName'])?>','<?= htmlspecialchars($row['ConnectionID'])?>','<?= htmlspecialchars($row['ComplaintDescription'],ENT_QUOTES)?>','<?= htmlspecialchars($row['ComplaintDate'])?>',
                                '<?= htmlspecialchars($row['ComplaintStatusName'])?>' , '<?= htmlspecialchars($row['UtilityTypeName'])?>')">View</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center p-4 font-semibold text-[#e43e4a]">No complaints found.</td>
                        </tr>
                <?php endif;?>
        </tbody>  


 </table>       



</main>

 </div>


 <!--pop up-->

<div id="complaintModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white w-[95%] max-w-3xl p-8 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6">Complaint details</h2>

        <table class="w-full text-base mb-6 table-auto">
        <tbody class="space-y-4">
        <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Complaint Id:</td>
                <td id="modalComplaintId"></td>
            </tr>

           <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Customer Id:</td>
                <td id="modalCustomerId"></td>
            </tr>

            <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Customer Name:</td>
                <td id="modalCustomerName"></td>
            </tr>
        
           <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Connection Id:</td>
                <td id="modalConnectionId"></td>
            </tr>

            <tr class="boarder-b">
                <td class="font-bold align-top py-3 px-2">Description:</td>
                <td id="modalDescription" class="break-words whitesapce-normal py-3"></td>
            </tr>

            <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Utility Type:</td>
                <td id="modalUtility"></td>
            </tr>

             <tr class="boarder-b ">
                <td class="font-bold py-3 px-2">Date:</td>
                <td id="modalDate"></td>
            </tr>

            <tr class="boarder-b">
                <td class="font-bold py-3 px-2">Status:</td>
                <td>
                    <span id="modalStatus" class="px-3 py-1 bg-green-300 text-green-900 rounded-full"></span>
                </td>
                
            </tr>
            </tbody> 
        </table>

            <button onclick="closeComplaintModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
        </div>

    </div>

</div>

<script>
function openComplaintModal(id, customerId, customerName, connectionId, description,date, status, utilityType) {
    document.getElementById("modalComplaintId").innerText = id;
    document.getElementById("modalCustomerId").innerText = customerId;
    document.getElementById("modalCustomerName").innerText = customerName;
    document.getElementById("modalConnectionId").innerText = connectionId;
    document.getElementById("modalDescription").innerText = description;

     const dt =  new Date(date);
    const formatDt = dt.toLocaleString('en-GB', {

        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });

    document.getElementById("modalDate").innerText = formatDt;

    document.getElementById("modalUtility").innerText = utilityType;
    document.getElementById("modalStatus").innerText = status;

    document.getElementById("complaintModal").classList.remove("hidden");
    document.getElementById("complaintModal").classList.add("flex");
}

function closeComplaintModal() {
    document.getElementById("complaintModal").classList.add("hidden");
    document.getElementById("complaintModal").classList.remove("flex");
}
</script>
