CREATE DATABASE IF NOT EXISTS mobile_checker;
USE mobile_checker;

CREATE TABLE IF NOT EXISTS mobile_status (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  mobile_number VARCHAR(10) UNIQUE,
  status VARCHAR(20) DEFAULT 'trusted' CHECK (status IN ('trusted', 'fraud', 'blacklisted')),
  description TEXT
);

CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mobile_number VARCHAR(10),
  review ENUM('positive', 'negative')
);