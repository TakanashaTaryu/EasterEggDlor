-- Create database
CREATE DATABASE IF NOT EXISTS dino_game;
USE dino_game;

-- Create highscores table
CREATE TABLE IF NOT EXISTS highscores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_name VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);