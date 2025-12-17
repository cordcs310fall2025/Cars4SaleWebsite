<?php include "../components/header.php"; ?>
<?php include "../config/db.php"; ?>

<h2>Seller Contact Information</h2>

<form method="POST">
    <label>Organization:</label>
    <input type="text" name="organization">

    <label>Contact Number:</label>
    <input type="text" name="contact">

    <label>Seller Name:</label>
    <input type="text" name="name">

    <label>Seller Origin:</label>
    <input type="text" name="origin">

    <label>Seller Location:</label>
    <input type="text" name="location">

    <label>Vendor Info:</label>
    <textarea name="vendor_info"></textarea>

    <button type="submit">Submit</button>
</form>

<?php include "../components/footer.php"; ?>
