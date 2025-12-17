<!-- components/car-card.php -->
<div class="car-card">
    <img src="/images/<?php echo $car['image']; ?>" alt="<?php echo $car['model']; ?>">
    
    <div class="car-info">
        <h3><?php echo $car['make'] . " " . $car['model']; ?></h3>
        <p>Year: <?php echo $car['year']; ?></p>
        <p class="price">$<?php echo number_format($car['price']); ?></p>
        <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn">View Details</a>
    </div>
</div>
