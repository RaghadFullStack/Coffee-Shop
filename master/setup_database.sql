-- Create the coffee_machine database
CREATE DATABASE IF NOT EXISTS coffee_machine;
USE coffee_machine;

-- Create the Product table
CREATE TABLE IF NOT EXISTS Product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    size VARCHAR(50),
    Color VARCHAR(50),
    ProductOverview TEXT,
    Vimage VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO Product (name, Price, size, Color, ProductOverview, Vimage) VALUES
('OZTURKBAY ODC-10 12 Cups, Black', 65.00, 'Large', 'Black', 'Premium coffee maker with auto-pause feature. This 12-cup coffee maker features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.', '9.jpg'),
('Glass Carafe Coffee Maker, 12 Cups', 55.00, 'Large', 'Black', 'Coffee maker with pause and serve feature. Features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.', '6.webp'),
('Auto Pause Coffee Maker', 65.00, 'Medium', 'Black', 'Coffee Maker with Auto Pause and Glass Carafe. Features a glass carafe with easy-pour spout, auto-pause and serve function, and programmable timer.', '2.webp'),
('Programmable 10-Cup Coffee Maker', 49.99, 'Medium', 'Silver', 'Wake up to fresh coffee with this programmable coffee maker. Features a 10-cup glass carafe, programmable timer, auto shut-off, and brew strength selector.', '1.jpg'),
('Drip Coffee Maker with Thermal Carafe', 79.99, 'Large', 'Stainless Steel', 'Keep your coffee hot for hours with the thermal carafe. Features a 12-cup capacity, programmable timer, auto-pause and serve function, and brew strength selector.', '3.jpg'),
('Compact 4-Cup Coffee Maker', 29.99, 'Small', 'White', 'Perfect for singles or small households. This compact coffee maker features a 4-cup glass carafe, auto shut-off, and brew strength selector.', '4.jpg'),
('Espresso Machine with Milk Frother', 129.99, 'Medium', 'Black', 'Make café-quality espresso drinks at home. Features a 15-bar pump pressure, steam wand for milk frothing, removable water tank, and compact design.', '7.jpg'),
('8-Cup Coffee Maker with Grinder', 89.99, 'Medium', 'Black', 'Fresh ground coffee with every brew. Features a built-in conical burr grinder, 8-cup glass carafe, programmable timer, auto-pause and serve function, and brew strength selector.', '8.webp'),
('Single Serve Coffee Maker', 39.99, 'Small', 'Red', 'Brew your favorite single-serve coffee pods or ground coffee. Features a 10-oz water reservoir, brew strength selector, and auto shut-off.', '11.jpg');