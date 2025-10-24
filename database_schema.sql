-- Database schema for the Authentication System

-- Create the database
CREATE DATABASE IF NOT EXISTS auth_system;
USE auth_system;

-- Create usuarios table
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create tokens_reset_senha table
CREATE TABLE IF NOT EXISTS tokens_reset_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    data_expiracao DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Sample data for testing (optional)
-- INSERT INTO usuarios (nome, email, senha_hash) VALUES 
-- ('Test User', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password