<?php
// auth/register.php
require __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = "Invalid registration data.";
    } else {
        // check existing
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
            $stmt->execute([$name,$email,$hash]);
            $_SESSION['flash'] = "Registration successful. Please log in.";
            header("Location: /?p=login");
            exit;
        }
    }
}

// show form
?>
<?php include __DIR__ . '/../components/header.php'; ?>
<div class="auth-card">
  <h2>Register</h2>
  <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
  <form method="POST" action="?p=register">
    <label>Name <input name="name" required></label>
    <label>Email <input name="email" type="email" required></label>
    <label>Password <input name="password" type="password" minlength="6" required></label>
    <button>Register</button>
  </form>
  <p>Already have an account? <a href="?p=login">Login</a></p>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
