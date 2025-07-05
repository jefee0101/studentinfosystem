<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM achievements WHERE student_id = ?");
$stmt->execute([$student['id']]);
$achievements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
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
<body class="text-white font-sans min-h-screen flex items-center justify-center">
  <div class="w-full max-w-4xl mx-auto p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl shadow border border-white border-opacity-20">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
      <?php if ($user['profile_pic']): ?>
        <img src="<?= $user['profile_pic'] ?>" alt="Profile Picture" class="w-32 h-32 rounded-full object-cover border-4 border-white">
      <?php endif; ?>
      <div>
        <h2 class="text-3xl font-bold mb-2">Welcome, <?= htmlspecialchars($student['full_name']) ?></h2>
        <p class="text-sm text-white/80">@<?= htmlspecialchars($user['username']) ?> | <?= htmlspecialchars($user['email']) ?></p>
        <p class="mt-2 text-sm"><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?> | <strong>Year:</strong> <?= htmlspecialchars($student['year_level']) ?></p>
        <p class="text-sm"><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?> | <strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
        <div class="mt-4 space-x-4">
          <a href="edit_profile.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Edit Profile</a>
          <a href="students.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">View All Students</a>
          <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Logout</a>
        </div>
      </div>
    </div>

    <div class="mt-10">
      <h3 class="text-2xl font-semibold mb-4">Achievements</h3>
      <?php if (!empty($achievements)): ?>
        <div class="grid md:grid-cols-2 gap-6">
          <?php foreach ($achievements as $ach): ?>
            <div class="bg-white bg-opacity-20 rounded-lg p-4 border border-white border-opacity-30">
              <h4 class="text-lg font-semibold"><?= htmlspecialchars($ach['title']) ?></h4>
              <p class="text-sm text-white/80 mb-2"><?= nl2br(htmlspecialchars($ach['description'])) ?></p>
              <?php if ($ach['image_path']): ?>
                <img src="<?= htmlspecialchars($ach['image_path']) ?>" class="w-full max-h-40 object-contain rounded">
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-white/80">You haven't added any achievements yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
