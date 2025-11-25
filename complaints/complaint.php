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
 $status = $_POST['status'] ?? "";
 $params =[];
 $query="";


 if(!empty($search) || !empty($status))
{
    $query="EXEC sp_load_complaint @searchTerm = :searchTerm, @statusID = :statusID";
    $params =[
         ':searchTerm' => ($search !== "" ? "%$search%" : NULL),
         ':statusID' => ($status !== "" ? $status : NULL)
    ];
}else{
    $query= "EXEC sp_load_complaint @searchTerm= NULL, @statusID = NULL";
    $params=[];
}

$complaints = executeQuery($pdo,$query,$params,false);


//fetch values from db (counts)
$sql = "EXEC sp_complaintCount";
$stats = executeQuery($pdo,$sql,[],true);


//assign to var
$electricity = $stats['ele_complaints'];
$water = $stats['water_complaints'];
$gas = $stats['gas_complaints'];
    
?>

<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64">
    <h2 class="text-3xl font-bold text-[#162029] mb-10">Complaint Management</h2>
    
    <!--cards-->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

        <div class="p-4 bg-[#213655] text-center rounded-xl ">
            <h3 class="text-lg font-bold text-white">Electricity Complaints </h3>
            <p class="mt-2 text-3xl font-semibold text-white"><?php echo $electricity?> </p>

        </div>

        <div class="p-4 bg-[#213655] text-center rounded-xl ">
            <h3 class="text-lg font-bold text-white">Water Complaints</h3>
            <p class="mt-2 text-3xl font-semibold text-white"><?php echo $water?> </p>
            
        </div>

         <div class="p-4 bg-[#213655] text-center rounded-xl ">
            <h3 class="text-lg font-bold text-white">Gas Complaints</h3>
            <p class="mt-2 text-3xl font-semibold text-white"><?php echo $gas?> </p>
        </div>

        <a href="complaintHistory.php">
        <div class="p-8 bg-[#e5d283] text-center rounded-xl hover:bg-[#e43e4a] cursor-pointer transition">
            <h3 class="text-lg font-bold text-[#162029] ">Complaint History</h3>
        </div>
        </a>

    </div>

    
    <div class="flex justify-between items-center mb-6 ">
        <h3 class="text-2xl font-semibold mt-10 mb-4 text-[#162029]">Customer Information</h3>
        <!--search bar (customerID/connectionID/complaintID)-->
        
            <form method="POST">
                <div class="flex w-full max-w-md mt-7">
                <input type="text" name="search" placeholder="Search here" class="w-full py-2 px-5 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-blue-400" value="<?= htmlspecialchars($search ?? '') ?>">
                <select name="status" class="border px-3 py-2 rounded">
                    <option value="">All</option>
                    <option value="1" <?= ($status ?? '')=='1' ? 'selected':'' ?>>Pending</option>
                    <option value="2" <?= ($status?? '')=='2' ? 'selected':'' ?>>In Progress</option>
                </select>
                <button class="bg-[#b8c3d6] text-[#0b121c] px-5 rounded-r-lg hover:bg-[#213655] hover:text-white transition" type="submit">Filter</button>
                 <a href="complaint.php" class="bg-blue-600 text-white px-5 ml-10 py-2 rounded hover:bg-blue-700 transition">Clear</a>
                </div>
            </form>
        
    </div>

    
        

    <!--display-->
       
        <table class="w-full bg-white rounded-xl shadow border-4  border-collapse overflow-hidden table-fixed">
        <thead>
            <tr class="bg-[#f0f0f0] text-left text-[#162029]">
            <th class="p-3 w-32">Complaint Id</th>
            <th class="p-3 w-32">Customer Id</th>
            <th class="p-3 w-32">Full Name</th>
            <th class="p-3 w-32">Connection Id</th>
            <th class="p-3 w-32">Utility Type</th>
            <th class="p-3 w-81">Description</th>
            <th class="p-3 w-32">Date</th>
            <th class="p-3 w-32">Status</th>
            <th class="p-3 w-32">View</th>
        </tr> 
        </thead>   
        <tbody> 
            <?php if(!empty($complaints)): ?>
                <?php foreach($complaints as $row): ?>
                    <tr class="border-b h-12">
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintID'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['CustomerID'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['CustomerName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ConnectionID'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['UtilityTypeName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintDescription'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintDate'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap">
                            <?php if($row['ComplaintStatusName'] === 'Pending'): ?>
                                <span class="px-3 py-1 bg-yellow-300 text-yellow-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php elseif($row['ComplaintStatusName'] == 'In Progress'): ?>
                                <span class="px-3 py-1 bg-purple-300 text-purple-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-green-300 text-green-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php endif; ?>
                        </td>

                        <!--view button-->
                        <td class="p-3">
                            <button class="bg-blue-800 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition ml-5" 
                            onclick="openComplaintModal(
                                '<?= htmlspecialchars($row['ComplaintID'])?>','<?= htmlspecialchars($row['CustomerID'])?>','<?= htmlspecialchars($row['CustomerName'])?>','<?= htmlspecialchars($row['ConnectionID'])?>','<?= htmlspecialchars($row['ComplaintDescription'],ENT_QUOTES)?>','<?= htmlspecialchars($row['ComplaintDate'])?>',
                                '<?= htmlspecialchars($row['UtilityTypeName'])?>','<?= htmlspecialchars($row['ComplaintStatusName'])?>')">View</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center font-semibold p-4 text-[#e43e4a]">No complaints found.</td>
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
                <td id="modalStatus">
                    <span id="modalStatus"></span>
                </td>
            </tr>
            </tbody> 
        </table>


        <div class="flex gap-6">
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Complete</button>
            <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Update</button>
            <button onclick="closeComplaintModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
        </div>

    </div>

</div>

<script>
let selectedComplaintID = null;
let selectedStatus = null;

function openComplaintModal(id, customerId,customerName, connectionId, description,date,utilityType, status ) {

    selectedComplaintID = id;
    selectedStatus = status;

    document.getElementById("modalComplaintId").innerText = id;
    document.getElementById("modalCustomerId").innerText = customerId;
    document.getElementById("modalCustomerName").innerText = customerName;
    document.getElementById("modalConnectionId").innerText = connectionId;
    document.getElementById("modalDescription").innerText = description;
    document.getElementById("modalDate").innerText = date;
    document.getElementById("modalUtility").innerText = utilityType;
    //document.getElementById("modalStatus").innerText = status;

    let statusSpan = document.getElementById("modalStatus");
    if(status === 'Pending') statusSpan.className ="px-3 py-1 inline-block bg-yellow-300 text-yellow-900 rounded-full";
    else statusSpan.className = "inline-block px-3  py-1 bg-purple-300 text-purple-900 rounded-full";

    statusSpan.innerText= status;


    document.getElementById("complaintModal").classList.remove("hidden");
    document.getElementById("complaintModal").classList.add("flex");
}

function closeComplaintModal() {
    document.getElementById("complaintModal").classList.add("hidden");
    document.getElementById("complaintModal").classList.remove("flex");
}


//update status button
document.querySelector(".bg-purple-600").onclick = function(){
    if(selectedStatus !== "Pending"){
        alert("Already in progress");
        return;
    }

    updateStatus(2);
};

document.querySelector(".bg-green-600").onclick = function(){
    updateStatus(3);
}



function updateStatus(newStatus){
    const formData = new FormData();
    formData.append("complaintID", selectedComplaintID);
    formData.append("newStatus", newStatus);

    fetch("updateComplaintStatus.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if(data.trim() === "Success"){
            alert("Status updated");
            window.location.reload();
        }else{
            alert("Error updating complaint")
        }
    });
}

</script>