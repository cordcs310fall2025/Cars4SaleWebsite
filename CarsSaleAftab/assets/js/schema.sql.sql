-- Create database
CREATE DATABASE carmart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE carmart;

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Cars table
CREATE TABLE cars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  make VARCHAR(100) NOT NULL,
  model VARCHAR(100) NOT NULL,
  year SMALLINT NOT NULL,
  description TEXT,
  price DECIMAL(12,2) NOT NULL,
  image VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders table
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(12,2) NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Order items
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  car_id INT NOT NULL,
  qty INT DEFAULT 1,
  price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO cars (make, model, year, description, price, image)
VALUES
('Toyota','Supra',2020,'Sports coupe',45000,'supra.jpg'),
('BMW','M3',2021,'High-performance sedan',70000,'m3.jpg'),
('Ford','Mustang',2019,'Classic muscle car',35000,'mustang.jpg'),
('Audi','R8',2022,'Luxury sports car',150000,'r8.jpg'); 
INSERT INTO users (name, email, password, is_admin)
VALUES
('Admin User','admin@example.com','adminpass',1),
('Regular User','user@example.com','userpass',0);

ALTER TABLE users ADD role ENUM('admin','staff','user') DEFAULT 'user';
