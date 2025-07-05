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
  <title>Edit Profile</title>
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

    select, option {
      color: black !important;
    }
  </style>
</head>
<body class="text-white min-h-screen py-10 px-4 font-sans">
  <div class="max-w-4xl mx-auto p-6 bg-white bg-opacity-10 backdrop-blur-md rounded-xl shadow-lg border border-white border-opacity-20">
    <h2 class="text-2xl font-bold mb-6 text-center">Edit Your Profile</h2>
    <form action="edit_profile_submit.php" method="POST" enctype="multipart/form-data" class="space-y-6">

      <!-- Account Info -->
      <fieldset class="border p-4 rounded-lg border-white border-opacity-30">
        <legend class="text-lg font-semibold mb-2">Account Info</legend>
        <label class="block mb-2">Username:
          <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block mb-2">Email:
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block mb-2">New Password (optional):
          <input type="password" name="password" class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block mb-2">Change Profile Picture:
          <input type="file" name="profile_pic" accept="image/*" class="mt-1 block text-white">
        </label>
        <?php if ($user['profile_pic']): ?>
          <img src="<?= $user['profile_pic'] ?>" alt="Current Profile" class="w-24 h-24 object-cover rounded-full mt-2">
        <?php endif; ?>
      </fieldset>

      <!-- Student Info -->
      <fieldset class="border p-4 rounded-lg border-white border-opacity-30">
        <legend class="text-lg font-semibold mb-2">Student Info</legend>
        <label class="block mb-2">Full Name:
          <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block mb-2">Course:
          <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
        </label>
        <label class="block mb-2">Year Level:
          <select name="year_level" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-90 text-black">
            <option value="1st Year" <?= $student['year_level'] == '1st Year' ? 'selected' : '' ?>>1st Year</option>
            <option value="2nd Year" <?= $student['year_level'] == '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
            <option value="3rd Year" <?= $student['year_level'] == '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
            <option value="4th Year" <?= $student['year_level'] == '4th Year' ? 'selected' : '' ?>>4th Year</option>
          </select>
        </label>
        <label class="block mb-2">Address:
          <textarea name="address" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white"><?= htmlspecialchars($student['address']) ?></textarea>
        </label>
        <label class="block mb-2">Gender:
          <select name="gender" required class="mt-1 w-full p-2 border rounded bg-white bg-opacity-90 text-black">
            <option <?= $student['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
            <option <?= $student['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
            <option <?= $student['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
          </select>
        </label>
      </fieldset>

      <!-- Achievements (optional) -->
      <fieldset class="border p-4 rounded-lg border-white border-opacity-30">
        <legend class="text-lg font-semibold mb-2">Achievements (Optional)</legend>
        <?php foreach ($achievements as $i => $ach): ?>
          <div class="mb-6">
            <label class="block mb-2">Title:
              <input type="text" name="achievement_title[<?= $ach['id'] ?>]" value="<?= htmlspecialchars($ach['title']) ?>" class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white">
            </label>
            <label class="block mb-2">Description:
              <textarea name="achievement_description[<?= $ach['id'] ?>]" class="mt-1 w-full p-2 border rounded bg-white bg-opacity-20 text-white"><?= htmlspecialchars($ach['description']) ?></textarea>
            </label>
            <label class="block mb-2">Change Proof Image (optional):
              <input type="file" name="achievement_image[<?= $ach['id'] ?>]" accept="image/*" class="mt-1 block text-white">
            </label>
            <?php if ($ach['image_path']): ?>
              <img src="<?= $ach['image_path'] ?>" alt="Proof" class="w-full max-w-xs mt-2 rounded">
            <?php endif; ?>
            <hr class="my-4">
          </div>
        <?php endforeach; ?>
      </fieldset>

      <div class="text-center">
        <input type="submit" value="Update Profile" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
      </div>
      <p class="text-center mt-4"><a href="dashboard.php" class="text-blue-400 hover:underline">Back to Dashboard</a></p>
    </form>
  </div>
</body>
</html>
