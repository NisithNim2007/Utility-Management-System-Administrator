<?php
session_start();
if(!isset($_SESSION['Username'])){
    header("Location: .././login.php");
    exit;
}
include ('../include/header.php'); ?>
<div class="flex">
<?php include ('../include/sidebar.php'); 
     include('../include/db.php');

 $search = trim($_POST['search'] ?? null);
 $status = $_POST['status'] ?? "";
 $utility = $_POST['utility'] ?? "";
 $params =[];
 $query="";

 if(!empty($search) || !empty($status) || !empty($utility))
{
    $query="EXEC sp_load_complaint @searchTerm = :searchTerm, @statusID = :statusID, @utility = :utility";
    $params =[
         ':searchTerm' => ($search !== "" ? $search : NULL),
         ':statusID' => ($status !== "" ? $status : NULL),
         ':utility' => ($utility !== "" ? $utility : NULL)
    ];
}else{
    $query= "EXEC sp_load_complaint @searchTerm= NULL, @statusID = NULL, @utility=NULL";
    $params=[];
}

$complaints = executeQuery($pdo,$query,$params,false);

$sql = "EXEC sp_complaintCount";
$stats = executeQuery($pdo,$sql,[],true);

$electricity = $stats['ele_complaints'];
$water = $stats['water_complaints'];
$gas = $stats['gas_complaints'];
    
?>

<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64 bg-gradient-to-br from-[#213655] to-[#e5d283]">
    <h2 class="text-3xl font-semibold text-[#ffffff] p-2 mb-7">Complaint Management</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">

        <div class="p-3 border-[2px] border-[#e5d283] text-center rounded-xl ">
            <h3 class="text-xl font-bold text-[#e5d283]">Electricity Complaints </h3>
            <p class="p-2 text-3xl font-bold text-[#e5d283]"><?php echo $electricity?> </p>
        </div>

        <div class="p-3 border-[2px] border-[#e5d283] text-center rounded-xl ">
            <h3 class="text-xl font-bold text-[#e5d283]">Water Complaints</h3>
            <p class="p-2 text-3xl font-bold text-[#e5d283]"><?php echo $water?> </p>   
        </div>

         <div class="p-3 border-[2px] border-[#e5d283] text-center rounded-xl ">
            <h3 class="text-xl font-bold text-[#e5d283]">Gas Complaints</h3>
            <p class="p-2 text-3xl font-bold text-[#e5d283]"><?php echo $gas?> </p>
        </div>

        <a href="complaintHistory.php">
        <div class="p-8 bg-[#e5d283] text-center rounded-xl hover:bg-[#d4c06f] cursor-pointer transition">
            <h3 class="text-lg font-bold text-[#162029]">Complaint History</h3>
        </div>
        </a>
    </div>

    <div class="flex gap-20 mb-6">
        <h3 class="text-2xl font-semibold mt-8 mb-4 text-[#ffffff]">Customer Information</h3>
        
            <form method="POST">
                <div class="flex w-full max-w-md mt-7">
                <input type="text" name="search" placeholder="ID/Name/NIC" class="flex-grow py-2 px-5 border border-[#b8c3d6] rounded-l-lg focus:outline-none focus:ring-[#213655]" value="<?= htmlspecialchars($search ?? '') ?>">
                <select name="status" class="border px-3 py-2">
                    <option value="" selected>All</option>
                    <option value="1" <?= ($status ?? '')=='1' ? 'selected':'' ?>>Pending</option>
                    <option value="2" <?= ($status?? '')=='2' ? 'selected':'' ?>>In Progress</option>
                </select>
                <select name="utility" class="border px-3 py-2">
                    <option value="" selected>All</option>
                    <option value="1" <?= ($utility ?? '')=='1' ? 'selected':'' ?>>Electricity</option>
                    <option value="2" <?= ($utility?? '')=='2' ? 'selected':'' ?>>Gas</option>
                     <option value="3" <?= ($utility?? '')=='3' ? 'selected':'' ?>>Water</option>
                </select>
                <button class="bg-[#162029] text-[#ffffff] px-5 rounded-r-lg hover:bg-[#162029]/60 transition" type="submit">Search</button>
                 <a href="complaint.php" class="bg-[#162029] text-white px-5 ml-5 py-2 rounded hover:bg-[#162029]/60 transition">Clear</a>
                </div>
            </form>
        
    </div>

    <div class="bg-[#ffffff] p-[0px] rounded-xl">
        <table class="w-full bg-[#f0f0f0] rounded-xl border-collapse  overflow-hidden table-fixed">
        <thead>
            <tr class="bg-[#213655] text-left text-[#ffffff]">
            <th class="p-3 w-32 text-center">Complaint Id</th>
            <th class="p-3 w-50 text-center">Name</th>
            <th class="p-3 w-32 text-center">Utility Type</th>
            <th class="p-3 w-85 text-center">Description</th>
            <th class="p-3 w-32 text-center">Date</th>
            <th class="p-3 w-32 text-center">Status</th>
            <th class="p-3 w-32 text-center">View</th>
        </tr> 
        </thead>   
        <tbody> 
            <?php if(!empty($complaints)): ?>
                <?php foreach($complaints as $row): ?>
                    <tr class="border-b h-12 border-[#b8c3d6]/60 hover:bg-[#b8c3d6]/30">
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintID'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['FullName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['UtilityTypeName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['ComplaintDescription'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= date('Y-m-d',strtotime($row['ComplaintDate']))?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap">
                            <?php if($row['ComplaintStatusName'] === 'Pending'): ?>
                                <span class="px-3 py-1 bg-yellow-300 text-yellow-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php elseif($row['ComplaintStatusName'] == 'In Progress'): ?>
                                <span class="px-3 py-1 bg-purple-300 text-purple-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-green-300 text-green-900 rounded-full"><?= $row['ComplaintStatusName']?></span>
                            <?php endif; ?>
                        </td>

                        <td class="p-3">
                            <button class="bg-[#213655] text-white px-4 py-1 rounded-lg hover:bg-[#162029] transition ml-5" 
                            onclick="openComplaintModal(
                                '<?= htmlspecialchars($row['ComplaintID'])?>','<?= htmlspecialchars($row['CustomerID'])?>','<?= htmlspecialchars($row['FullName'])?>','<?= htmlspecialchars($row['NIC'])?>','<?= htmlspecialchars($row['ConnectionID'])?>','<?= htmlspecialchars($row['ComplaintDescription'],ENT_QUOTES)?>','<?= htmlspecialchars($row['ComplaintDate'])?>',
                                '<?= htmlspecialchars($row['UtilityTypeName'])?>','<?= htmlspecialchars($row['ComplaintStatusName'])?>','<?= htmlspecialchars($row['UserName'])?>')">View</button>
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
</div>
</main>
</div>

<div id="complaintModal" class="fixed inset-0 bg-black bg-opacity-85 hidden items-center justify-center z-50">
    <div class="bg-[#213655] text-white text-xl w-[95%] max-w-3xl p-8 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto border border-[#e5d283]">
        <h2 class="text-2xl font-bold mb-6">Complaint details</h2>
        <table class="w-auto text-base mb-6 table-auto">
        <tbody class="space-y-1">
            <tr>
                <td class="font-semibold py-3 px-2 w-[160px] whitespace-nowrap">Complaint Id :</td>
                <td id="modalComplaintId"></td>
            </tr>

           <tr>
                <td class="font-semibold py-3 px-2">Customer Id :</td>
                <td id="modalCustomerId"></td>
            </tr>

            <tr>
                <td class="font-semibold py-3 px-2">Customer Name :</td>
                <td id="modalCustomerName"></td>
            </tr>

            <tr>
                <td class="font-semibold py-3 px-2">NIC :</td>
                <td id="modalNIC"></td>
            </tr>
        
           <tr>
                <td class="font-semibold py-3 px-2">Connection Id :</td>
                <td id="modalConnectionId"></td>
            </tr>

            <tr>
                <td class="font-semibold align-top py-3 px-2">Description :</td>
                <td id="modalDescription" class="break-words whitesapce-normal py-3"></td>
            </tr>

            <tr">
                <td class="font-semibold py-3 px-2">Utility Type :</td>
                <td id="modalUtility"></td>
            </tr>

             <tr>
                <td class="font-semibold py-3 px-2">Date :</td>
                <td id="modalDate"></td>
            </tr>

            <tr>
                <td class="font-semibold py-3 px-2">Status :</td>
                <td id="modalStatus">
                    <span id="modalStatus_"></span>
                </td>
            </tr>
         </tbody> 
        </table>

        <div class="flex items-center justify-between">
            <div id="assigned" style="display: none;">
            <table class="w-auto bg-[#e5d283] rounded-lg table-auto">
                <tr>
                    <td class="font-semibold text-[#213655] text-[18px] py-2 px-8">Assigned To : <span class="text-[#213655] font-bold text-[18px]" id="modalAssigned"></span></td>
                </tr>
            </table>
        </div>
            

        <div class="flex gap-6 font-semibold">
            <button class="bg-[#b8c3d6] text-[#213655] text-base px-4 py-2 rounded hover:bg-[#b8c3d6]/50" id="completebtn">Complete</button>
            <button class="bg-[#b8c3d6] text-[#213655] text-base px-4 py-2 rounded hover:bg-[#b8c3d6]/50" id="updatebtn">Update</button>
            <button onclick="closeComplaintModal()" class="border-[2px] border-[#e5d283] text-[#e5d283] text-base px-4 py-2 rounded hover:bg-[#e5d283] hover:text-[#213655]">Close</button>
        </div>
        </div>
        
    </div>
</div>

<script>
let selectedComplaintID = null;
let selectedStatus = null;

function openComplaintModal(id, customerId,FullName,customerNIC, connectionId, description,date,utilityType, status, assignedUser ) {

    selectedComplaintID = id;
    selectedStatus = status;
    document.getElementById("modalComplaintId").innerText = id;
    document.getElementById("modalCustomerId").innerText = customerId;
    document.getElementById("modalCustomerName").innerText = FullName;
    document.getElementById("modalNIC").innerText = customerNIC;
    document.getElementById("modalConnectionId").innerText = connectionId;
    document.getElementById("modalDescription").innerText = description;

    const dt =  new Date(date);
    const formatDt = dt.toLocaleString('en-GB', {

        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });

    document.getElementById("modalDate").innerText = formatDt;
    document.getElementById("modalUtility").innerText = utilityType;

    let statusSpan = document.getElementById("modalStatus");
    if(status === 'Pending') statusSpan.className ="px-3 py-1 inline-block bg-yellow-300 text-yellow-900 rounded-full";
    else statusSpan.className = "inline-block px-3  py-1 bg-purple-300 text-purple-900 rounded-full";
    statusSpan.innerText= status;

    const assigned= document.getElementById("assigned");
    const assignedCell = document.getElementById("modalAssigned");
    if(assignedUser && assignedUser !== "" && assignedUser !== "NULL"){
        assignedCell.innerText=assignedUser;
        assigned.style.display ="table-row";
    }else{
        assigned.style.display="none";
    }

    document.getElementById("complaintModal").classList.remove("hidden");
    document.getElementById("complaintModal").classList.add("flex");
}
function closeComplaintModal() {
    document.getElementById("complaintModal").classList.add("hidden");
    document.getElementById("complaintModal").classList.remove("flex");
}

document.getElementById("updatebtn").onclick = function(){
    if(selectedStatus !== "Pending"){
        alert("Already in progress.Press COMPLETE!");
        return;
    }
    if(!confirm("Are you sure you want to update complaint status as IN PROGRESS?")){
        return;
    }
    updateStatus(2);
};

document.getElementById("completebtn").onclick = function(){
     if(!confirm("Are you sure you want to update complaint status as COMPLETED?")){
        return;
    }
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
        console.log("RAW: ", data);
        if(data.trim() === "success"){
            alert("Status updated");
            window.location.reload();
        }else{
            alert("Error updating complaint")
        }
    });
}

</script>