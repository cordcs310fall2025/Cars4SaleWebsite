<?php include "../components/header.php"; ?>
<?php include "../config/db.php"; ?>

<h2>Payment Details</h2>

<form method="POST">
    <label>Card Number:</label>
    <input type="text" name="card">

    <label>Payment Holder:</label>
    <input type="text" name="holder">

    <label>Payer Contact:</label>
    <input type="text" name="contact">

    <label>Additional Info:</label>
    <textarea name="info"></textarea>

    <button type="submit">Complete Purchase</button>
</form>

<?php include "../components/footer.php"; ?>
<?php
if ($_POST['pay']) {
    header("Location: checkout.php?paid=1");
}
?>

<form method="POST">
    <button name="pay">Pay Now (Simulation)</button>
</form>
