<?php
session_start();
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validation
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $message = 'Todos os campos são obrigatórios.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Formato de e-mail inválido.';
        $message_type = 'error';
    } elseif (strlen($senha) < 8) {
        $message = 'A senha deve ter no mínimo 8 caracteres.';
        $message_type = 'error';
    } elseif ($senha !== $confirmar_senha) {
        $message = 'As senhas não coincidem.';
        $message_type = 'error';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $message = 'Este e-mail já está cadastrado.';
            $message_type = 'error';
        } else {
            // Register user
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$nome, $email, $senha_hash])) {
                $_SESSION['success_message'] = 'Conta criada com sucesso! Faça o login.';
                header('Location: login.php');
                exit();
            } else {
                $message = 'Erro ao criar conta. Tente novamente.';
                $message_type = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Autenticação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
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
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuário</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="senha">Senha (mínimo 8 caracteres):</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <div class="form-group">
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            
            <button type="submit">Registrar</button>
        </form>
        
        <div class="links">
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
        </div>
    </div>
</body>
</html>