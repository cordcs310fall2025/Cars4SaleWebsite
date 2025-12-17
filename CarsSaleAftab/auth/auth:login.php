<?php
// auth/login.php
require __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id,name,email,password,is_admin FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // remove password before storing in session
        unset($user['password']);
        $_SESSION['user'] = $user;
        header("Location: /?p=shop");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<?php include __DIR__ . '/../components/header.php'; ?>
<div class="auth-card">
  <h2>Login</h2>
  <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
  <?php if (!empty($_SESSION['flash'])) { echo '<p class="info">'.htmlspecialchars($_SESSION['flash']).'</p>'; unset($_SESSION['flash']); } ?>
  <form method="POST" action="?p=login">
    <label>Email <input name="email" type="email" required></label>
    <label>Password <input name="password" type="password" required></label>
    <button>Login</button>
  </form>
  <p>Don't have an account? <a href="?p=register">Register</a></p>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>

<?php
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../home.php");
    exit;
}
