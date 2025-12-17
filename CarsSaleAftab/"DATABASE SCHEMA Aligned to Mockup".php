"DATABASE SCHEMA Aligned to Mockup"
CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(80),
    build_type VARCHAR(50),
    car_type VARCHAR(50),
    date_built YEAR,
    price DECIMAL(10,2),
    brand VARCHAR(40),
    location VARCHAR(80),
    image VARCHAR(255),
    description TEXT
);
CREATE TABLE sellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organization VARCHAR(120),
    contact_number VARCHAR(40),
    seller_name VARCHAR(120),
    seller_origin VARCHAR(120),
    seller_location VARCHAR(120),
    vendor_info TEXT
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_number VARCHAR(20),
    payment_holder VARCHAR(120),
    payer_contact VARCHAR(40),
    additional_info TEXT,
    car_id INT,
    FOREIGN KEY (car_id) REFERENCES cars(id)
);
