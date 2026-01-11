<?php 
session_start();
if(!isset($_SESSION['Username'])){
    header("Location: ../login.php");
    exit;
}
include ('./include/header.php'); ?>
<div class="flex">
<?php include ('./include/sidebar.php'); 
    include('./include/db.php');

 $search = trim($_POST['search'] ?? null);
 $role = trim($_POST['role'] ?? null);
 $params= [];
 $where=[];

 if(!empty($search))
{
    if(is_numeric($search)){
        $where[] = " UserID = ?";
        $params[] = $search;
    }
    
    $like = "%$search%";
    $where[] = "(Username like ?
    or FirstName like ?
    or MiddleName like ?
    or LastName like ?)";

    array_push($params,$like,$like,$like,$like);
}

    if($role !== ''){
        $where[] = "RoleName = ?";
        $params[] = $role;
    }

    if(!empty($where)){
        $query="select * from vw_UserDetails where " . implode(" and ",$where);
    }
else{
    $query = "select * from vw_UserDetails";
    }

$user_load = executeQuery($pdo,$query,$params,false);
?>

<main class="flex-1 p-5 overflow-y-auto min-h-screen ml-64 bg-gradient-to-br from-[#213655] to-[#e5d283]">
    <h2 class="text-3xl text-[#ffffff] font-semibold p-2">User Management</h2>
    
    <div class="flex justify-between items-center mb-6 ">
        <div>
            <form method="POST">
            <div class="flex w-full max-w-md mt-7">
            <input type="text" name="search" placeholder="ID/Username/Name" value="<?= htmlspecialchars($search ?? '') ?>"
            class="flex-grow py-2 px-5 border border-[#b8c3d6] rounded-l-lg focus:outline-none focus:ring-[#213655]">
            <select name="role" class="border px-3 py-2">
                    <option value="" selected>All</option>
                    <option value="Admin">Admin</option>
                    <option value="Cashier">Cashier </option>
                    <option value="Field Officer">Field Officer</option>
                    <option value="Manager">Manager</option>
                </select>
            <button class="bg-[#162029] text-white px-5 rounded-r-lg hover:bg-[#162029]/60 transition">Search</button>
            <a href="user.php" class="bg-[#162029] text-white px-5 ml-10 py-2 rounded hover:bg-[#162029]/60 transition">Clear</a>
            </div>   
        </form>
        </div>
        
        <a href="addUser.php">
         <button class="bg-[#e5d283] text-[#0b121c] px-9 py-3 rounded-lg text-xl font-semibold hover:bg-[#d4c06f] transition">Add User</button>
        </a>       
    </div>

    <div class="bg-[#ffffff] p-[0px] rounded-xl">
    <table class="w-full bg-[#f0f0f0] rounded-xl border-collapse overflow-hidden ">
        <thead>
            <tr class="bg-[#213655] text-left text-white">
            <th class="p-3 w-24">User Id</th>
            <th class="p-3 w-[250px] text-center">Name</th>
            <th class="p-3 w-[150px] text-center">Username</th>
            <th class="p-3 w-[120px] text-center">Role</th>
            <th class="p-3 w-[250px] text-center">Email</th>
            <th class="p-3 w-[140px] text-center">Registration Date</th>
            <th class="p-3 w-[100px] text-center">Password</th>
            <th class="p-3 w-[100px] text-center">Account</th>
        </tr> 
        </thead>   
        <tbody> 
            <?php if(!empty($user_load)): ?>
                <?php foreach($user_load as $row): ?>
                    <tr class="border-b h-12 border-[#b8c3d6]/60 hover:bg-[#b8c3d6]/30">
                        <td class="p-3"><?= htmlspecialchars($row['UserID'])?></td>
                        <td class="p-3"><?= htmlspecialchars($row['FirstName']. ' ' . $row['MiddleName']. ' '. $row['LastName'])?></td>
                        <td class="p-3"><?= htmlspecialchars($row['Username'])?></td>
                        <td class="p-3"><?= htmlspecialchars($row['RoleName'])?></td>
                        <td class="p-3 overflow-hidden text-ellipsis whitespace-nowrap"><?= htmlspecialchars($row['Email'])?></td>
                        <td class="p-3"><?= htmlspecialchars(date('Y-m-d', strtotime($row['RegistrationDate'])))?></td>
                        <td class="p-3">
                            <button class="bg-[#213655] text-white px-4 py-1 rounded-lg hover:bg-[#162029] transition ml-5" 
                            onclick="openPasswordModal(<?= $row['UserID'] ?>, '<?= addslashes($row['Username']) ?>')">Update</button>
                        </td>
                        <td class="p-3">
                            <select onchange="updateStatus(<?= $row['UserID'] ?>,this.value)" class="border border-[#b8c3d6] p-1 rounded focus:ring-2 focus:ring-[#213655]">
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
</div>
</main>
</div>

 <div id="pwd-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h2 class="text-lg font-bold mb-4">Update password for: <span id=pwd-modal-username></span></h2>
        <input id="pwd-new" type="password" placeholder="New password" class="border p-2 w-full mb-4 rounded">
        <div class="flex justify-end">
            <button onclick="PasswordUpdate()" class="bg-[#213655] text-white px-4 py-1 rounded-lg hover:bg-[#162029] transition mr-2">Update</button>
            <button onclick="closeModal()" class="bg-[#b8c3d6] text-[#0b121c] px-4 py-1 rounded-lg hover:bg-[#a5b3c9] transition">Cancel</button>
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
    if(!pwd || pwd.length<3) return alert('Password must be atleast 3 characters')

    if(!confirm('Are you sure you want to update this password?')){
        return;
    }
    
    try{
        const res = await fetch("update_password.php",{
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
            alert("Failed to update password. Please try again later.");
            console.error("DB Error: " + data.message);
        }
    } catch(err){
        console.error(err);
        alert("Request failed. Check console.");
    } 
 
}

async function updateStatus(userId, status){
    if(!confirm('Are you sure you want to update status of this user account?')){
        return;
    }
    try{
        const res = await fetch('update_account.php',{
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({userId: userId, isActive:parseInt(status)})
        });
        const data= await res.json();
        if(!data.success){
            alert(data.message);
            console.error('Error updating status: ' + data.message);
            location.reload();
        }
        else{
            alert("User account updated successfully");
            location.reload();
        }
    }catch (err){
        console.error(err);
        alert('Request failed. Check console');
    }
}
 </script>