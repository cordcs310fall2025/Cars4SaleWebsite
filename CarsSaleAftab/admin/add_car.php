<?php
require __DIR__ . '/../db.php';
session_start();
if (empty($_SESSION['user']) || !$_SESSION['user']['is_admin']) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = $_POST['make']; $model = $_POST['model']; $year = intval($_POST['year']);
    $price = floatval($_POST['price']); $desc = $_POST['description'];
    // handle image upload (basic)
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . '/../assets/images/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $imageName);
    }
    $stmt = $pdo->prepare("INSERT INTO cars (make,model,year,description,price,image) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$make,$model,$year,$desc,$price,$imageName]);
    $_SESSION['flash'] = "Car added.";
    header("Location: /?p=shop");
    exit;
}
?>
<?php include __DIR__ . '/../components/header.php'; ?>
<h1>Add Car (Admin)</h1>
<form method="POST" action="?p=admin_add" enctype="multipart/form-data">
  <label>Make <input name="make" required></label>
  <label>Model <input name="model" required></label>
  <label>Year <input name="year" type="number" min="1900" max="<?= date('Y') ?>" required></label>
  <label>Price <input name="price" type="number" step="0.01" required></label>
  <label>Image <input name="image" type="file" accept="image/*"></label>
  <label>Description <textarea name="description"></textarea></label>
  <button>Add Car</button>
</form>
<?php include __DIR__ . '/../components/footer.php'; ?>
