<?php
include 'db.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = $_POST['role'];

    try {
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')";
        if ($conn->query($sql) === TRUE) {
            header("Location: login.php?msg=success");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { // 1062 is the MySQL code for Duplicate Entry
            $error_msg = "This email is already registered. Please login instead.";
        } else {
            $error_msg = "Something went wrong. Please try again.";
        }
    }
}
?>

<?php if($error_msg): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
        <?= $error_msg ?>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Join ChronoTask</title>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-96">
        <h2 class="text-2xl font-bold mb-6 text-orange-600">Create Student Account</h2>
        <form method="POST" class="space-y-4">
            <input type="text" name="full_name" placeholder="Full Name" required class="w-full border p-3 rounded-xl">
            <input type="email" name="email" placeholder="Student Email" required class="w-full border p-3 rounded-xl">
            <input type="password" name="password" placeholder="Password" required class="w-full border p-3 rounded-xl">
            <select name="role" class="w-full border p-3 rounded-xl bg-white">
                <option value="Member">Team Member (Student)</option>
                <option value="Leader">Team Leader (Project Head)</option>
                <option value="Mentor">Faculty Mentor (Supervisor)</option>
            </select>
            <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition">Sign Up</button>
        </form>
        <p class="mt-4 text-sm text-center">Already have an account? <a href="login.php" class="text-orange-500">Login</a></p>
    </div>
</body>
</html>