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

$alert = "";
if(isset($_SESSION['success'])){
    $msg = addslashes($_SESSION['success']);
    $alert = "alert('Success: $msg');";
    unset($_SESSION['success']);
}   
if(isset($_SESSION['error'])){
    $msg = addslashes($_SESSION['error']);
    $alert = "alert('Error: $msg');";
    unset($_SESSION['error']);
}  
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $pwd = trim($_POST['pwd']);
    $role = (int)$_POST['role'];

    try{
        $pdo -> beginTransaction();

        $sql = "INSERT INTO Persons(FirstName, MiddleName, LastName, Email, PhoneNumber)
        VALUES (:fname, :mname, :lname, :email, :phone)";

        $params = [
            'fname' => $fname,
            'mname' => $mname ?: null,
            'lname' => $lname,
            'email' => $email,
            'phone' => $phone,
        ];

        $result1 = executeNonQuery($pdo,$sql,$params);
        if($result1 !== "success"){
            throw new Exception("Person insert failed: " . $result1);
        }
        $userid = $pdo -> lastInsertId();
        $hashPwd = password_hash($pwd, PASSWORD_DEFAULT);
        $sql2 = "INSERT INTO Users(UserID,Username,RoleID, Password, IsActive)
        VALUES(:userid, :username, :role, :pwd_hash, :isActive)";
        $para = [
            'userid' => $userid,
            'username' => $username,
            'role' => $role,
            'pwd_hash' => $hashPwd,
            'isActive' => 1
        ];
        $result2 = executeNonQuery($pdo, $sql2, $para);
        if($result2 !== "success"){
            throw new Exception("User insert failed: " . $result2);
        }
        $pdo ->commit();
        $_SESSION['success'] = "User added successfully";
        header('Location: addUser.php');
        exit;

    }catch (Exception $e){
        $pdo -> rollBack();
        $ms = $e->getMessage();
        error_log("User Create Error: $ms");

        if(str_contains($ms, 'duplicate') || str_contains($ms, 'Duplicate')){
            $_SESSION['error'] = "Username, email or phone number already exists!";
        }else{
             $_SESSION['error'] = "Unable to create user. Please try again";
        }
        header('Location: addUser.php');
        exit;
    }
} 
?>

<main class="flex-1 p-8 overflow-y-auto min-h-screen ml-64 flex justify-center items-center bg-gradient-to-br from-[#213655] to-[#e5d283]">
<div class="max-w-4xl w-full bg-white p-10 rounded-xl shadow-xl">
    <h2 class="text-3xl font-bold mb-8 text-center text-[#162029]">Add new user</h2>
    <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-8" id="user_form">

        <div>
            <label class="block mb-1 font-semibold text-[#162029]">First Name</label>
            <input type="text" name="fname" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="2">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Middle Name</label>
            <input type="text" name="mname" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" >
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Last Name</label>
            <input type="text" name="lname" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="2">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">NIC</label>
            <input type="text" name="nic" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="10">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required>
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Phone number</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="10">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Username</label>
            <input type="text" name="username" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="4">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Password</label>
            <input type="password" name="pwd" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required minlength="3">
        </div>
        <div>
            <label class="block mb-1 font-semibold text-[#162029]">Select Role</label>
                <select name="role" class="w-full border rounded px-3 py-2 border-[#b8c3d6] bg-[#f0f0f0] text-[#162029] focus:outline-none focus:ring-2 focus:ring-[#e5d283] focus:border-[#e5d283]" required>
                    <option value="1">Admin</option>
                    <option value="3">Cashier </option>
                    <option value="2">Field Officer</option>
                    <option value="4">Manager</option>
                </select>
        </div>
        <div class="flex gap-6 items-center" >
            <button type="submit" class="w-[180px] bg-[#213655] text-[#f0f0f0] py-3 rounded-lg text-lg font-semibold hover:bg-[#162029] hover:text-[#e5d283] transition">Add user</button>
            <button type="button" id="clearBtn" class="w-[180px] bg-[#213655] text-[#f0f0f0] py-3 rounded-lg text-lg font-semibold hover:bg-[#162029] hover:text-[#e5d283] transition"><a href="addUser.php">Clear</a></button>
        </div>
    </form>
</div>
</main>


<script>
    <?= $alert ?>

    const nameRegex = /^[A-Za-z]+$/;
    const emailRegex = /^[^\s@]+@[^\@]+\.[^\s@]+$/;

    document.getElementById("user_form").addEventListener("submit", function(e){
        const fname = document.querySelector("input[name='fname']").value.trim();
        const mname = document.querySelector("input[name='mname']").value.trim();
        const lname = document.querySelector("input[name='lname']").value.trim();
        const nic = document.querySelector("input[name='nic']").value.trim();
        const email = document.querySelector("input[name='email']").value.trim();
        const phone = document.querySelector("input[name='phone']").value.trim();
        const pwd = document.querySelector("input[name='pwd']").value.trim();

        if(!nameRegex.test(fname) || (mname && !nameRegex.test(mname)) || !nameRegex.test(lname)){
            alert("Name must contain only characters");
            e.preventDefault();
            return;
        }

        if(!emailRegex.test(email)){
            alert("Invalid email address");
            e.preventDefault();
            result;
        }

        if(!/^[0-9]{10}$/.test(phone)){
            alert("Phone number must be exactly 10 digits");
            e.preventDefault();
            return;
        }

        if(pwd.length <3){
            alert("Password must be atleast 3 characters");
            e.preventDefault();
            return;
        }
        if(nic.length <12){
            alert("NIC must be 12 characters");
            e.preventDefault();
            return;
        
    });

</script>