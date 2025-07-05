<?php
session_start();
require "db.php";

// Step 1: Insert user
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Handle profile picture
$profile_path = '';
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['tmp_name']) {
    $pic_name = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
    $target_path = 'uploads/' . $pic_name;
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path);
    $profile_path = $target_path;
}

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, profile_pic) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $email, $password, $profile_path]);
$user_id = $pdo->lastInsertId();

// Step 2: Insert student info
$full_name = $_POST['full_name'];
$course = $_POST['course'];
$year_level = $_POST['year_level'];
$address = $_POST['address'];
$gender = $_POST['gender'];

$stmt = $pdo->prepare("INSERT INTO students (user_id, full_name, course, year_level, address, gender) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_id, $full_name, $course, $year_level, $address, $gender]);
$student_id = $pdo->lastInsertId();

// Step 3: Optional Achievements
$achievement_titles = $_POST['achievement_title'] ?? [];
$achievement_descriptions = $_POST['achievement_description'] ?? [];
$achievement_images = $_FILES['achievement_image'] ?? [];

if (count($achievement_titles) > 0) {
    for ($i = 0; $i < count($achievement_titles); $i++) {
        $title = htmlspecialchars($achievement_titles[$i]);
        $desc = htmlspecialchars($achievement_descriptions[$i]);

        $image_path = '';
        if (isset($achievement_images['tmp_name'][$i]) && $achievement_images['tmp_name'][$i]) {
            $img_name = uniqid() . '_' . basename($achievement_images['name'][$i]);
            $target_path = 'uploads/' . $img_name;
            move_uploaded_file($achievement_images['tmp_name'][$i], $target_path);
            $image_path = $target_path;
        }

        $stmt = $pdo->prepare("INSERT INTO achievements (student_id, title, description, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $title, $desc, $image_path]);
    }
}

// Set session and redirect
$_SESSION['user_id'] = $user_id;
$_SESSION['full_name'] = $full_name; // 👈 Save for welcome
header("Location: welcome.php");
exit();

