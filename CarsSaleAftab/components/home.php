<?php include "../components/header.php"; ?>
<?php include "../config/db.php"; ?>

<h1>Available Cars</h1>

<form method="GET" action="index.php">
    <input type="hidden" name="page" value="gallery">

    <label>Model:</label>
    <input type="text" name="model">

    <label>Build:</label>
    <input type="text" name="build">

    <label>Type:</label>
    <input type="text" name="type">

    <label>Date Built:</label>
    <input type="number" name="year">

    <button type="submit">Search Cars</button>
</form>

<?php include "../components/footer.php"; ?>
