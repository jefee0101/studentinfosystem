<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch file paths for deletion
try {
    // Get profile picture
    $stmt = $pdo->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    $profilePic = $user['profile_pic'];

    // Get student ID
    $stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $student = $stmt->fetch();
    $student_id = $student['id'];

    // Get achievement images
    $stmt = $pdo->prepare("SELECT image_path FROM achievements WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $achievementImages = $stmt->fetchAll();

    // 1. Delete from users (CASCADE will remove students and achievements)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    // 2. Delete files (profile + achievement images)
    if ($profilePic && file_exists($profilePic)) {
        unlink($profilePic);
    }

    foreach ($achievementImages as $img) {
        if ($img['image_path'] && file_exists($img['image_path'])) {
            unlink($img['image_path']);
        }
    }

    // 3. Logout
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();

} catch (Exception $e) {
    echo "Error deleting account: " . $e->getMessage();
}
?>
