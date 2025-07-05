<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Helper: Upload file and return new path
function uploadFile($file, $targetDir) {
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . "_" . basename($file['name']);
        $targetPath = $targetDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $targetPath;
        }
    }
    return null;
}

// Directories
$profileUploadDir = "uploads/profile_pics/";
$achievementUploadDir = "uploads/achievements/";
if (!is_dir($profileUploadDir)) mkdir($profileUploadDir, 0777, true);
if (!is_dir($achievementUploadDir)) mkdir($achievementUploadDir, 0777, true);

try {
    // 1. Update users table
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // optional
    $newProfilePath = null;

    // Upload new profile picture if provided
    if (!empty($_FILES['profile_pic']['name'])) {
        $newProfilePath = uploadFile($_FILES['profile_pic'], $profileUploadDir);
    }

    // Build update query
    $sql = "UPDATE users SET username = ?, email = ?";
    $params = [$username, $email];

    if (!empty($password)) {
        $sql .= ", password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if ($newProfilePath) {
        $sql .= ", profile_pic = ?";
        $params[] = $newProfilePath;
    }

    $sql .= " WHERE id = ?";
    $params[] = $user_id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // 2. Update student info
    $fullName = $_POST['full_name'];
    $course = $_POST['course'];
    $yearLevel = $_POST['year_level'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    $stmt = $pdo->prepare("UPDATE students SET full_name = ?, course = ?, year_level = ?, address = ?, gender = ? WHERE user_id = ?");
    $stmt->execute([$fullName, $course, $yearLevel, $address, $gender, $user_id]);

    // 3. Update achievements
    if (isset($_POST['achievement_title'])) {
        foreach ($_POST['achievement_title'] as $achId => $title) {
            $desc = $_POST['achievement_description'][$achId];
            $newImage = $_FILES['achievement_image']['name'][$achId] ?? '';

            $params = [$title, $desc];
            $sql = "UPDATE achievements SET title = ?, description = ?";

            // Handle new image
            if (!empty($newImage)) {
                $newPath = uploadFile([
                    'name' => $_FILES['achievement_image']['name'][$achId],
                    'tmp_name' => $_FILES['achievement_image']['tmp_name'][$achId],
                    'error' => $_FILES['achievement_image']['error'][$achId]
                ], $achievementUploadDir);

                if ($newPath) {
                    $sql .= ", image_path = ?";
                    $params[] = $newPath;
                }
            }

            $sql .= " WHERE id = ?";
            $params[] = $achId;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
    }

    header("Location: dashboard.php");
    exit();

} catch (Exception $e) {
    echo "Error updating profile: " . $e->getMessage();
}
?>
