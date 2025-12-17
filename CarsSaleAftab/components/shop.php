<?php include "../components/header.php"; ?>

<h2 class="section-title">All Cars</h2>

<div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search by model...">
    <select id="filterMake">
        <option>All Brands</option>
        <option>Toyota</option>
        <option>BMW</option>
        <option>Audi</option>
    </select>

    <select id="sortPrice">
        <option>Sort by Price</option>
        <option value="low">Low → High</option>
        <option value="high">High → Low</option>
    </select>
</div>

<div class="car-grid" id="carList">
    <?php
    // Later: fetch from DB
    $cars = [
        ["id"=>1,"make"=>"Nissan","model"=>"GTR","year"=>2022,"price"=>120000,"image"=>"gtr.jpg"],
        ["id"=>2,"make"=>"Audi","model"=>"R8","year"=>2019,"price"=>95000,"image"=>"r8.jpg"]
    ];

    foreach ($cars as $car) {
        include "../components/car-card.php";
    }
    ?>
</div>

<?php include "../components/footer.php"; ?>
