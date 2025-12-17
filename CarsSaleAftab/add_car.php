<?php
session_start();
require 'db.php';

// Simple admin check — replace with your own auth check if needed
// We'll allow only logged-in users to add cars. Improve for real admin roles.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize
    $title = trim($_POST['title'] ?? '');
    $make = trim($_POST['make'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $trim = trim($_POST['trim'] ?? null);
    $year = intval($_POST['year'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock = intval($_POST['stock'] ?? 1);

    // Basic validation
    if ($title === '' || $make === '' || $model === '' || $year <= 1900 || $price <= 0) {
        $errors[] = "Title, make, model, year and price are required and must be valid.";
    }

    // Handle image upload (optional)
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file = $_FILES['image'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Image upload error (code {$file['error']}).";
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            $errors[] = "Only JPG/PNG/WebP/GIF images are allowed.";
        } elseif ($file['size'] > 4 * 1024 * 1024) {
            $errors[] = "Image must be smaller than 4 MB.";
        } else {
            // Generate safe filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safe = preg_replace('/[^A-Za-z0-9_\-]/', '_', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
            $newName = $safe . '_' . time() . '.' . $ext;
            $dest = $uploadDir . $newName;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                $errors[] = "Failed to move uploaded file.";
            } else {
                // Save relative path for DB
                $image_path = 'images/' . $newName;
            }
        }
    }

    // Insert into DB if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO cars (title, make, model, trim, year, price, description, image, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisssi", $title, $make, $model, $trim, $year, $price, $description, $image_path, $stock);
        if ($stmt->execute()) {
            $success = "Car added successfully.";
            // clear form values
            $title = $make = $model = $trim = $description = '';
            $year = $price = $stock = 0;
        } else {
            $errors[] = "DB error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Car — Admin</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f4f6fb;padding:20px}
.container{max-width:800px;margin:20px auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 6px 20px rgba(0,0,0,0.06)}
h2{margin-bottom:12px}
input[type=text], input[type=number], textarea { width:100%; padding:10px; margin:8px 0;border:1px solid #dcdfe6;border-radius:6px }
button{background:#007BFF;color:#fff;padding:10px 14px;border:none;border-radius:6px;cursor:pointer}
.alert{padding:10px;border-radius:6px;margin-bottom:12px}
.alert.error{background:#fff1f0;color:#9b1c1c}
.alert.success{background:#eefbe7;color:#1b6a1b}
.small{font-size:13px;color:#6b7280}
</style>
</head>
<body>
<div class="container">
    <h2>Add / Upload a Car</h2>

    <?php if(!empty($errors)): ?>
        <div class="alert error"><strong>Errors:</strong><ul><?php foreach($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Title (display name)</label>
        <input type="text" name="title" value="<?= htmlspecialchars($title ?? ''); ?>" required>

        <label>Make (brand)</label>
        <input type="text" name="make" value="<?= htmlspecialchars($make ?? ''); ?>" required>

        <label>Model</label>
        <input type="text" name="model" value="<?= htmlspecialchars($model ?? ''); ?>" required>

        <label>Trim (e.g., S, SV, Platinum)</label>
        <input type="text" name="trim" value="<?= htmlspecialchars($trim ?? ''); ?>">

        <label>Year</label>
        <input type="number" name="year" value="<?= htmlspecialchars($year ?? ''); ?>" required>

        <label>Price (numbers only)</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price ?? ''); ?>" required>

        <label>Stock (quantity)</label>
        <input type="number" name="stock" value="<?= htmlspecialchars($stock ?? 1); ?>">

        <label>Description</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($description ?? ''); ?></textarea>

        <label>Image (JPG/PNG/WebP, max 4MB)</label>
        <input type="file" name="image" accept="image/*">

        <div style="margin-top:12px;">
            <button type="submit">Add Car</button>
            <a href="home.php" style="margin-left:12px;text-decoration:none;color:#333">Back to Home</a>
        </div>
    </form>
    <p class="small">Images are stored in the <code>/images/</code> folder. Make sure that folder is writable by Apache/PHP.</p>
</div>
</body>
</html>
