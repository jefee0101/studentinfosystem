<?php
session_start();
if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}
$fullName = htmlspecialchars($_SESSION['full_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta http-equiv="refresh" content="3;url=students.php"> <!-- Auto-redirect in 3s -->
  <style>
    body {
      background: linear-gradient(-45deg, #0f172a, #1e3a8a, #0c4a6e, #1e293b);
      background-size: 400% 400%;
      animation: gradient 10s ease infinite;
      cursor: pointer;
    }
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .fade-in {
      animation: fadeIn 2s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  </style>
  <script>
    // 👇 Instantly redirect on click anywhere
    document.addEventListener("DOMContentLoaded", () => {
      document.body.addEventListener("click", () => {
        window.location.href = "students.php";
      });
    });
  </script>
</head>
<body class="flex items-center justify-center h-screen text-white">
  <div class="text-center fade-in select-none">
    <h1 class="text-4xl font-bold mb-4">Welcome, <?= $fullName ?>!</h1>
    <p class="text-lg opacity-80">Redirecting to student directory...<br><span class="text-sm opacity-50">(Click anywhere to skip)</span></p>
  </div>
</body>
</html>
