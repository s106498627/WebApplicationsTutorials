CREATE TABLE IF NOT EXISTS customers (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(25) NOT NULL,
  last_name VARCHAR(25) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  street_address VARCHAR(40) NOT NULL,
  suburb VARCHAR(20) NOT NULL,
  state VARCHAR(3) NOT NULL,
  postcode CHAR(4) NOT NULL,
  phone VARCHAR(10) NOT NULL,
  preferred_contact ENUM('PHONE','EMAIL') DEFAULT 'EMAIL'
);

CREATE TABLE IF NOT EXISTS products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  product VARCHAR(30) NOT NULL, -- major product variant
  capacity VARCHAR(10) NOT NULL, -- storage size
  price DECIMAL(8,2) NOT NULL -- price of the item
);

CREATE TABLE IF NOT EXISTS orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL, -- link to customer details
  comment TEXT,
  card_type VARCHAR(20) NOT NULL,
  card_name VARCHAR(40) NOT NULL,
  card_number VARCHAR(16) NOT NULL,
  card_expiry CHAR(5) NOT NULL, -- MM-YY
  order_cost DECIMAL(10,2) NOT NULL, -- total price, decimal to properly store currency
  order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  order_status ENUM('PENDING','FULFILLED','PAID','ARCHIVED') DEFAULT 'PENDING',
  FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE IF NOT EXISTS order_items ( -- transition table to relate order to several products
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  PRIMARY KEY (order_id, product_id),
  FOREIGN KEY (order_id) REFERENCES orders(order_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS extras (
  extra_id INT AUTO_INCREMENT PRIMARY KEY,
  extra_name VARCHAR(30) NOT NULL,
  price DECIMAL(8,2) NOT NULL
);

CREATE TABLE IF NOT EXISTS order_extras ( -- transition table to relate order to the extras chosen
  order_id INT NOT NULL,
  extra_id INT NOT NULL,
  PRIMARY KEY (order_id, extra_id),
  FOREIGN KEY (order_id) REFERENCES orders(order_id),
  FOREIGN KEY (extra_id) REFERENCES extras(extra_id)
);