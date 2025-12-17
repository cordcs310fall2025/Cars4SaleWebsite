<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user info
$stmt = $conn->prepare("SELECT username, email, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $update = $conn->prepare(
        "UPDATE users SET username=?, email=?, address=? WHERE user_id=?"
    );
    $update->bind_param("sssi", $username, $email, $address, $user_id);
    $update->execute();

    header("Location: profile.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<style>
body { font-family: Arial; background:#f4f6fb; }
.card { max-width:500px; margin:60px auto; background:#fff; padding:30px; border-radius:10px; }
input, textarea { width:100%; padding:10px; margin:10px 0; }
button { background:#2563eb; color:white; padding:10px; border:none

    <?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, address=? WHERE user_id=?");
    $stmt->bind_param("sssi", $name, $email, $address, $user_id);
    $stmt->execute();
    $success = true;
}

$user = $conn->query("SELECT * FROM users WHERE user_id=$user_id")->fetch_assoc();
?>

<h2>My Profile</h2>

<?php if (!empty($success)) echo "<p style='color:green'>Profile Updated</p>"; ?>

<form method="post">
    <label>Name</label><br>
    <input name="name" value="<?= $user['name'] ?>" required><br><br>

    <label>Email</label><br>
    <input name="email" value="<?= $user['email'] ?>" required><br><br>

    <label>Address</label><br>
    <textarea name="address"><?= $user['address'] ?></textarea><br><br>

    <button type="submit">Save Changes</button>
</form>
