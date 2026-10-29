-- run once on databases made with the old add_tables.sql
-- card_type is better as an enum
ALTER TABLE orders MODIFY card_type ENUM('VISA', 'MASTERCARD', 'AMERICANEXPRESS');
-- oopsie missed the post option :(
ALTER TABLE customers MODIFY preferred_contact ENUM('PHONE','EMAIL','POST') DEFAULT 'EMAIL';
