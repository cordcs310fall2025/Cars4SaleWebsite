// Simple search example
document.getElementById("searchInput")?.addEventListener("input", function() {
    let query = this.value.toLowerCase();
    let cars = document.querySelectorAll(".car-card");

    cars.forEach(car => {
        let model = car.querySelector("h3").innerText.toLowerCase();
        car.style.display = model.includes(query) ? "block" : "none";
    });
});
document.querySelectorAll(".add-cart").forEach(btn=>{
    btn.onclick = ()=> alert("Added to cart!");
});
