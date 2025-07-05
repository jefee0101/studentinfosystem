<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare("SELECT students.*, users.profile_pic FROM students 
                       JOIN users ON students.user_id = users.id 
                       WHERE students.full_name LIKE ? OR students.course LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Directory</title>
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
<body class="text-white min-h-screen p-4 sm:p-6">
  <div class="max-w-6xl mx-auto">

    <!-- Topbar -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Student Directory</h1>
      <div class="flex items-center space-x-4">
        <a href="edit_profile.php" class="bg-blue-600 hover:bg-blue-700 px-4 py-1 rounded">Profile</a>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded">Logout</a>
      </div>
    </div>

    <!-- Search bar -->
    <form method="GET" class="mb-6 max-w-xl mx-auto">
      <input type="text" name="search" placeholder="Search student..." value="<?= htmlspecialchars($search) ?>" 
             class="w-full p-2 sm:p-3 rounded bg-white bg-opacity-20 text-white placeholder-white focus:outline-none">
    </form>

    <!-- Students List -->
    <div class="space-y-4" id="student-list">
      <?php foreach ($students as $index => $student): ?>
        <div class="bg-white bg-opacity-10 border border-white border-opacity-20 rounded-lg shadow">
          <button onclick="toggleStudent(<?= $index ?>)" class="w-full text-left p-4 hover:bg-white hover:bg-opacity-10 transition">
            <div class="flex items-center space-x-4">
              <img src="<?= htmlspecialchars($student['profile_pic']) ?>" alt="Profile" class="w-12 h-12 object-cover rounded-full">
              <div>
                <h2 class="text-xl font-semibold"><?= htmlspecialchars($student['full_name']) ?></h2>
                <p class="text-sm"><?= htmlspecialchars($student['course']) ?> — <?= htmlspecialchars($student['year_level']) ?></p>
              </div>
            </div>
          </button>

          <div id="student-details-<?= $index ?>" class="hidden px-6 pb-4 transition-all">
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>

            <?php
              $stmt = $pdo->prepare("SELECT * FROM achievements WHERE student_id = ?");
              $stmt->execute([$student['id']]);
              $achievements = $stmt->fetchAll();
            ?>
            <?php if ($achievements): ?>
              <h3 class="text-lg font-bold mt-4 mb-2">Achievements</h3>
              <div class="grid md:grid-cols-2 gap-4">
                <?php foreach ($achievements as $ach): ?>
                  <div class="bg-white bg-opacity-10 p-4 rounded-lg">
                    <p><strong>Title:</strong> <?= htmlspecialchars($ach['title']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($ach['description']) ?></p>
                    <?php if ($ach['image_path']): ?>
                      <img src="<?= $ach['image_path'] ?>" class="w-full mt-2 rounded">
                    <?php endif; ?>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="mt-2 italic">No achievements listed.</p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script>
    let activeIndex = null;

    function toggleStudent(index) {
      const details = document.querySelectorAll('[id^="student-details-"]');

      if (activeIndex === index) {
        // Collapse the current
        document.getElementById('student-details-' + index).classList.add('hidden');
        activeIndex = null;
      } else {
        // Collapse others
        details.forEach(div => div.classList.add('hidden'));
        // Expand selected
        document.getElementById('student-details-' + index).classList.remove('hidden');
        activeIndex = index;
      }
    }
  </script>
</body>
</html>
