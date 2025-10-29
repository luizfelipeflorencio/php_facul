<?php
session_start();
require_once 'config.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $message = 'Por favor, informe seu e-mail.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Formato de e-mail inválido.';
        $message_type = 'error';
    } else {
        // Check if email exists in database
        $stmt = $pdo->prepare("SELECT id, nome FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $token_hash = password_hash($token, PASSWORD_DEFAULT);
            
            // Set expiration time (30 seconds from now)
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 seconds'));
            
            // Store token in database
            $stmt = $pdo->prepare("INSERT INTO tokens_reset_senha (id_usuario, token_hash, data_expiracao) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token_hash, $expiracao]);
            
            // In a real application, you would send an email with the reset link
            // For this example, we'll just display the link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/redefinir-senha.php?token=" . $token;
            
            // For demonstration purposes, we'll show the link
            // In a real application, you would send this via email
            $message = 'Se uma conta com este e-mail existir, um link de redefinição foi enviado. Para fins de demonstração, aqui está o link: <a href="' . $reset_link . '">' . $reset_link . '</a>';
            $message_type = 'success';
        } else {
            // Generic success message to prevent email enumeration
            $message = 'Se uma conta com este e-mail existir, um link de redefinição foi enviado.';
            $message_type = 'success';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci minha senha - Sistema de Autenticação</title>
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
        input[type="email"] {
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
        .success a {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Esqueci minha senha</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <button type="submit">Enviar link de redefinição</button>
        </form>
        
        <div class="links">
            <p><a href="login.php">Voltar para o login</a></p>
        </div>
    </div>
</body>
</html>