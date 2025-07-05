<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: students.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0c4a6e, #1e293b);
      background-size: 400% 400%;
      animation: gradient 10s ease infinite;
    }

    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white font-sans">
  <div class="w-full max-w-5xl flex flex-col md:flex-row bg-white bg-opacity-10 backdrop-blur-md rounded-xl shadow-lg overflow-hidden border border-white border-opacity-20">

    <!-- Left Side - Title -->
    <div class="md:w-1/2 w-full flex items-center justify-center bg-gradient-to-br from-blue-900 to-sky-800 p-10 hidden md:flex">
      <h1 class="text-4xl md:text-5xl font-bold text-center leading-tight">Student<br>Information<br>System</h1>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full md:w-1/2 p-8 md:p-12">
      <h2 class="text-3xl font-bold mb-6 text-center">Login</h2>

      <?php if (!empty($error)): ?>
        <p class="text-red-400 text-sm mb-4 text-center"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="POST" action="" class="space-y-6">
        <label class="block">
          <span class="text-white">Email:</span>
          <input type="email" name="email" required
                 class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white border border-white border-opacity-30 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                 placeholder="Enter your email">
        </label>

        <label class="block">
          <span class="text-white">Password:</span>
          <input type="password" name="password" required
                 class="mt-1 w-full p-2 rounded bg-white bg-opacity-20 text-white border border-white border-opacity-30 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
                 placeholder="Enter your password">
        </label>

        <button type="submit"
                class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded transition duration-300">
          Login
        </button>
      </form>

      <p class="text-center text-sm mt-4">
        Don’t have an account? <a href="register.php" class="text-blue-300 hover:underline">Register here</a>
      </p>
    </div>
  </div>
</body>
</html>
