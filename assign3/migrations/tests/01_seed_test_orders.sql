-- add in orders so that i dont have to manually seed when a new docker cntr is created
-- optional step so not numbered in the migrations
-- RUN THE CORE MIGRATIONS FIRST!!

-- products need to exist before any order_items can ref them
INSERT IGNORE INTO products (product_id, product, capacity, price) VALUES
(1, 'pokemon-usb', '32gb', 29.95),
(2, 'pokemon-usb', '64gb', 39.95),
(3, 'pokemon-usb', '128gb', 49.95),
(4, 'pokemon-usb', '256gb', 64.95),
(5, 'extreme-usb', '32gb', 89.95),
(6, 'extreme-usb', '64gb', 99.95),
(7, 'extreme-usb', '128gb', 109.95),
(8, 'extreme-usb', '256gb', 124.95),
(9, 'classic-usb-a', '32gb', 14.95),
(10, 'classic-usb-a', '64gb', 24.95),
(11, 'classic-usb-a', '128gb', 34.95),
(12, 'classic-usb-a', '256gb', 49.95);

INSERT IGNORE INTO extras (extra_id, extra_name, price) VALUES
(1, 'express-shipping', 9.95),
(2, 'gift-wrap', 4.00),
(3, 'extended-warranty', 12.00);

-- yes, customer names and details are AI generated to alleviate the tedium of coming up with and entering then manually
INSERT IGNORE INTO customers (customer_id, first_name, last_name, email, street_address, suburb, state,
    postcode, phone, preferred_contact) VALUES
(1, 'Alice', 'Nguyen', 'alice.nguyen@example.com', '12 Glenferrie Rd', 'Hawthorn', 'VIC', '3122', '0411111111', 'EMAIL'),
(2, 'Ben', 'Carter', 'ben.carter@example.com', '8 George St', 'Sydney', 'NSW', '2000', '0422222222', 'PHONE'),
(3, 'Chloe', 'Nguyen', 'chloe.nguyen@example.com', '45 Queen St', 'Brisbane', 'QLD', '4000', '0433333333', 'POST'),
(4, 'Daniel', 'Smith', 'daniel.smith@example.com', '3 Hay St', 'Perth', 'WA', '6000', '0444444444', 'EMAIL'),
(5, 'Emma', 'Smithers', 'emma.smithers@example.com', '27 Rundle Mall', 'Adelaide', 'SA', '5000', '0455555555', 'PHONE');

-- fill up with orders for each customer
INSERT IGNORE INTO orders (order_id, customer_id, comment, card_type, card_name, card_number, card_expiry,
    order_cost, order_time, order_status) VALUES
(1, 1, 'Birthday present', 'VISA', 'Alice Nguyen', '4111111111111111', '08-28', 0, '2026-10-01 09:15:00', 'PENDING'),
(2, 2, NULL, 'MASTERCARD', 'Ben Carter', '5555555555554444', '03-27', 0, '2026-10-03 14:40:00', 'PAID'),
(3, 3, 'Bulk order for the office', 'AMERICANEXPRESS', 'Chloe Nguyen', '378282246310005', '11-29', 0, '2026-10-05 11:05:00', 'FULFILLED'),
(4, 4, NULL, 'VISA', 'Daniel Smith', '4012888888881881', '01-27', 0, '2026-10-10 16:20:00', 'ARCHIVED'),
(5, 1, NULL, 'VISA', 'Alice Nguyen', '4111111111111111', '08-28', 0, '2026-10-15 08:30:00', 'PAID'),
(6, 5, 'Please leave at the door', 'MASTERCARD', 'Emma Smithers', '5105105105105100', '06-28', 0, '2026-10-20 19:45:00', 'PENDING'),
(7, 2, NULL, 'MASTERCARD', 'Ben Carter', '5555555555554444', '03-27', 0, '2026-10-28 12:00:00', 'PENDING'),
(8, 4, NULL, 'VISA', 'Daniel Smith', '4012888888881881', '01-27', 0, '2026-10-30 21:10:00', 'FULFILLED');

INSERT IGNORE INTO order_items (order_id, product_id, quantity) VALUES
(1, 2, 2),
(2, 8, 1),
(3, 9, 5),
(4, 3, 1),
(4, 10, 2),
(5, 7, 3),
(6, 12, 1),
(7, 1, 1),
(7, 6, 1),
(7, 11, 1),
(8, 4, 4);

INSERT IGNORE INTO order_extras (order_id, extra_id) VALUES
(1, 2),
(2, 1),
(2, 3),
(6, 1),
(7, 2),
(8, 3);

-- calc price of the order
UPDATE orders o SET order_cost =
    -- item_price * quantity    COALESCE turns potential nulls into 0
    (SELECT COALESCE(SUM(p.price * oi.quantity), 0) FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = o.order_id)
    + (SELECT COALESCE(SUM(e.price), 0) FROM order_extras oe
        JOIN extras e ON oe.extra_id = e.extra_id WHERE oe.order_id = o.order_id)
WHERE o.order_id BETWEEN 1 AND 8;
