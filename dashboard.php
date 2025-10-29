<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user information
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Autenticação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-top: 0;
        }
        .welcome {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #e9f7ef;
            border-radius: 5px;
        }
        .features {
            margin: 30px 0;
        }
        .feature {
            padding: 15px;
            margin: 15px 0;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
        .logout {
            text-align: center;
            margin-top: 30px;
        }
        .logout a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout a:hover {
            background-color: #c82333;
        }
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .nav a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dashboard</h2>
        
        <div class="welcome">
            <h3>Bem-vindo, <?php echo htmlspecialchars($user_name); ?>!</h3>
            <p>Você está logado no sistema de autenticação.</p>
        </div>
        
        <div class="features">
            <h3>Funcionalidades implementadas:</h3>
            
            <div class="feature">
                <h4>✅ Registro de Usuário</h4>
                <p>Os usuários podem se registrar fornecendo nome, e-mail e senha.</p>
            </div>
            
            <div class="feature">
                <h4>✅ Login de Usuário</h4>
                <p>Os usuários podem fazer login com e-mail e senha.</p>
            </div>
            
            <div class="feature">
                <h4>✅ Recuperação de Senha</h4>
                <p>Os usuários podem solicitar redefinição de senha através do e-mail.</p>
            </div>
            
            <div class="feature">
                <h4>✅ Tokens Expiráveis</h4>
                <p>Os tokens de redefinição de senha expiram após 30 segundos.</p>
            </div>
            
            <div class="feature">
                <h4>✅ Contagem Regressiva</h4>
                <p>O tempo restante do token é exibido com contagem regressiva em JavaScript.</p>
            </div>
            
            <div class="feature">
                <h4>✅ Segurança</h4>
                <p>Senhas armazenadas com hash Bcrypt, Prepared Statements e proteção contra XSS.</p>
            </div>
        </div>
        
        <div class="logout">
            <a href="?logout=1">Sair</a>
        </div>
    </div>
</body>
</html>