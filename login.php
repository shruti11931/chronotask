<?php
session_start();
include 'db.php';
$error = ""; // Initialize the variable here to prevent the warning

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... your existing login logic ...
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify hashed password
        if (password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role']; // Store Leader/Member role
            header("Location: index.php");
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ChronoTask</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">
    <div class="text-red-500 text-sm mb-4">
        <?= $error ?>
    </div>
    <div class="bg-white p-10 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <h1 class="text-3xl font-bold text-orange-600 mb-2 text-center">ChronoTask</h1>
        <p class="text-gray-500 text-center mb-6">Welcome back! Please login</p>

        <?php if($error): ?>
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-600 text-sm rounded-lg text-center">
                Account created! You can now login.
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com" 
                       class="w-full border border-gray-200 rounded-lg px-4 py-3 mt-1 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Password</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full border border-gray-200 rounded-lg px-4 py-3 mt-1 focus:ring-2 focus:ring-orange-500 outline-none transition">
            </div>
            <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3 rounded-lg hover:bg-orange-600 shadow-lg shadow-orange-100 transition duration-200">
                Login
            </button>
        </form>
        
      <p class="mt-4 text-sm text-center">
    New here? <a href="register.php" class="text-orange-500 font-bold">Join the team</a>
</p>
    </div>
</body>
</html>