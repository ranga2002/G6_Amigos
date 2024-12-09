<?php
session_start();
require_once "../utils/db_connection.php";
require_once "../classes/User.php";

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to access your profile.";
    header('Location: login.php');
    exit();
}

$conn = getDBConnection();
$user = new User($conn);
$userId = $_SESSION['user_id'];

// Fetch user details
$userDetails = $user->getUserById($userId);

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    $updated = $user->updateUser($userId, $username, $email, $password);

    if ($updated) {
        $_SESSION['success_message'] = "Profile updated successfully.";
        $_SESSION['username'] = $username; // Update session username
        header('Location: profile.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include "../includes/header.php"; ?>

    <div class="container">
        <h1>Profile</h1>
        <?php if (isset($_SESSION['success_message'])) { ?>
            <div class="alert success">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['error_message'])) { ?>
            <div class="alert error">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php } ?>

        <form method="POST" action="profile.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($userDetails['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($userDetails['email']) ?>" required>

            <label for="password">New Password (Leave blank to keep current):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Update Profile</button>
        </form>
    </div>
    <?php include "../includes/footer.php"; ?>
</body>
</html>
