<?php
require_once 'config.php';

try {
    // Create usuarios table
    $sql_usuarios = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        senha_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql_usuarios);
    
    // Create tokens_reset_senha table
    $sql_tokens = "CREATE TABLE IF NOT EXISTS tokens_reset_senha (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_usuario INT NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        data_expiracao DATETIME NOT NULL,
        usado TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql_tokens);
    
    echo "Database tables created successfully!";
} catch(PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>