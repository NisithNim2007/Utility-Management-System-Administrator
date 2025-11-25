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
 $params= [];
 $query="";

 if(!empty($search))
{
    $query="select * from vw_UserDetails
    where cast(UserID as nvarchar(50)) like ?
    or Username like ?
    or RoleName like ?";

    $search_val = "%$search%";

    $params =[
         $search_val,
         $search_val,
         $search_val
    ];
}
else{
    $query = "select * from vw_UserDetails";
    $params=[];
    }


$user_load = executeQuery($pdo,$query,$params,false);
    
 
?>

<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64">
    <h2 class="text-3xl font-semibold mb-6">User Management</h2>
    
    <div class="flex justify-between items-center mb-6 ">
        
        <!--search bar(userId/username/role)-->
        <div>
            <form method="POST">
            <div class="flex w-full max-w-md mt-7">
            <input type="text" name="search" placeholder="Search here" value="<?= htmlspecialchars($search ?? '') ?>"
            class="w-full py-2 px-5 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-blue-400">
            <button class="bg-blue-600 text-white px-5 rounded-r-lg hover:bg-blue-700 transition">Search</button>
            <a href="user.php" class="bg-blue-600 text-white px-5 ml-10 py-2 rounded hover:bg-blue-700 transition">Clear</a>
            </div>   
        </form>
        </div>
        
         <button class="bg-green-600 text-white p-5 rounded-lg text-xl font-semibold hover:bg-green-700 transition">Add User</button>

        
            
    </div>



  <!--display-->  
  <table class="w-full bg-white rounded-xl shadow boarder-collapse overflow-hidden table-fixed">
        <thead>
            <tr class="bg-gray-700 text-left text-white">
            <th class="p-3 w-32">User Id</th>
            <th class="p-3 w-32">Full name</th>
            <th class="p-3 w-32">Username</th>
            <th class="p-3 w-32">Role</th>
            <th class="p-3 w-81">Email</th>
            <th class="p-3 w-32">Registration Date</th>
            <th class="p-3 w-32">Password</th>
            <th class="p-3 w-32">Account</th>
        </tr> 
        </thead>   
        <tbody> 
            <?php if(!empty($user_load)): ?>
                <?php foreach($user_load as $row): ?>
                    <tr class="border-b h-12">
                        <td class="p-3"><?= htmlspecialchars($row['UserID'])?></td>
                         <td class="p-3"><?= htmlspecialchars($row['FirstName']. ' ' . $row['LastName'])?></td>
                        <td class="p-3"><?= htmlspecialchars($row['Username'])?></td>
                        <td class="p-3"><?= htmlspecialchars($row['RoleName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['Email'])?></td>
                        <td class="p-3"><?= htmlspecialchars(date('Y-m-d', strtotime($row['RegistrationDate'])))?></td>
                        <td class="p-3">

                    <!--pwd update-->
                            <button class="bg-blue-800 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition ml-5" 
                            onclick="openPasswordModal(<?= $row['UserID'] ?>, '<?= addslashes($row['Username']) ?>')">Update</button>
                        </td>
                    <!-- Acc status-->
                        <td class="p-3">
                            <select onchange="updateStatus(<?= $row['UserID'] ?>,this.value)" class="border p-1 rounded">
                                <option value="1" <?= $row['IsActive']==1? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $row['IsActive']==0? 'selected' : '' ?>>Deactive</option>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center p-4 font-semibold text-[#e43e4a]">No User found.</td>
                        </tr>
                <?php endif;?>
        </tbody>  


 </table>       



</main>

 </div>


 <!--pwd modal-->
 <div id="pwd-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-bold mb-4">Update password for: <span id=pwd-modal-username></span></h2>
        <input type="text" id="pwd-new" type="password" placeholder="New password" class="border p-2 w-full mb-4 rounded">
        <div class="flex justify-end">
            <button onclick="PasswordUpdate()" class="bg-blue-800 text-white px-4 py-1 rounded-lg hover:bg-blue-700 transition mr-2">Update</button>
            <button onclick="closeModal()" class="bg-gray-400 px-4 py-1 rounded-lg hover:bg-gray-300 transition">Cancel</button>
        </div>
    </div>
 </div>

 <script>
    let userForPwd = null;

    function openPasswordModal(userId, username){
        userForPwd = userId;
        document.getElementById('pwd-modal-username').textContent= username;
        document.getElementById('pwd-modal').classList.remove('hidden');
    }

    function closeModal(){
        document.getElementById('pwd-modal').classList.add('hidden');
        document.getElementById('pwd-new').value = '';
    }

 async function PasswordUpdate() {
    const pwd= document.getElementById('pwd-new').value.trim();
    if(!pwd || pwd.length<8) return alert('Passwordmust be atleast 8 characters')

    //const body= {userId: currentUserForPwd, newPassword: pwd};

    try{
        const res = await fetch("/update_password.php",{
            method: "POST",
            headers: {"Content-Type" : "application/json"},
            body: JSON.stringify({
                userId: userForPwd,
                newPassword: pwd
            })
        });

        const data = await res.json();

        if(data.success){
            alert("Password updated successfully.");
            closeModal();
        }else{
            alert("Error: " + data.message);
        }
 } catch(err){
        console.err(err);
        alert("Request failed. CHeck console.");
 } 
 
}


async function updateStatus(userId, status){
    try{
        const res = await fetch('/update_account.php',{
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({userId: userId, isActive:parseInt(status)})
        });
        const data= await res.json();
        if(!data.success){
            alert('Error updating status: ' + data.message);
        }
    }catch (err){
        console.error(err);
        alert('Request failed. Check console');
    }
}
 </script>