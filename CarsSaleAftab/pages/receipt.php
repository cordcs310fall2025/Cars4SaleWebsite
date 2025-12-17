<?php require "includes/db.php"; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
</head>
<body>

<h2>Purchase Successful</h2>

<p>Order ID: <?= $_SESSION['last_order'] ?></p>

<p>Thank you for your purchase!</p>

<a href="home.php">Back to Home</a>

</body>
use Dompdf\Dompdf;
$dompdf = new Dompdf();

$html = "<h1>Receipt</h1><p>Total: $$total</p>";
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream("receipt.pdf");

</html>
