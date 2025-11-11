<?php

$page_title = "List cars";

require_once ('includes/header.php');
require_once('includes/database.php');

//select statement
$sql = "SELECT * FROM inventory_tbl, inventory_photos_tbl";
//execute the query
$query = $conn->query($sql);

//Handle selection errors
if (!$query) {
$errno = $conn->errno;
$errmsg = $conn->error;
echo "Selection failed with: ($errno) $errmsg<br/>\n";
$conn->close();
//require_once ('includes/footer.php');
exit;
}
//display results in a table
?>
<h2>Inventory</h2>

<table border="1px solid" border="collapse" align ="center" height='300'  width='750' >
<tr><center>
            <th></th>
    <th>Year</th>
    <th>Make</th>
    <th>Model</th>
    <th>Mileage</th>
            <th>Price</td>
    <th>Details</th>
</center>
</tr>

<?php
//insert a row into the table for each row of data
    while (($row = $query->fetch_assoc()) !==NULL){
        echo "<tr>";
        echo "<td><img src =" .$row['id']. $row['ext_1']. " width = 100     height = 100></td>";
        echo "<td>", $row['year'], "</td>";
        echo "<td>", $row['make'], "</td>";
        echo "<td>", $row['model'], "</td>";
        echo "<td>", number_format($row['mileage']), "</td>";
        echo "<td>","$", number_format($row['price']), "</td>";
        echo "<td><a href='cardetails.php?id=", $row['id'],"'>View Details</td>";
        echo "</tr>";
    }
?>