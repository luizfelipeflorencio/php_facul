<?php
session_start();
require_once 'config.php';

$message = '';
$message_type = '';
$token_valid = false;
$expiracao_timestamp = null;

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $message = 'Token inválido ou expirado. Por favor, solicite um novo link.';
    $message_type = 'error';
} else {
    $token = $_GET['token'];
    
    // Validate token
    $stmt = $pdo->prepare("SELECT t.id, t.id_usuario, t.token_hash, t.data_expiracao, t.usado, u.nome 
                           FROM tokens_reset_senha t 
                           JOIN usuarios u ON t.id_usuario = u.id 
                           WHERE t.usado = 0 
                           AND t.data_expiracao > NOW()");
    $stmt->execute();
    
    $valid_token_found = false;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($token, $row['token_hash'])) {
            $token_valid = true;
            $token_id = $row['id'];
            $user_id = $row['id_usuario'];
            $user_name = $row['nome'];
            $expiracao_timestamp = strtotime($row['data_expiracao']);
            $valid_token_found = true;
            break;
        }
    }
    
    if (!$valid_token_found) {
        $message = 'Token inválido ou expirado. Por favor, solicite um novo link.';
        $message_type = 'error';
    }
}

// Process password reset
if ($token_valid && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    if (empty($nova_senha) || empty($confirmar_senha)) {
        $message = 'Todos os campos são obrigatórios.';
        $message_type = 'error';
    } elseif (strlen($nova_senha) < 8) {
        $message = 'A senha deve ter no mínimo 8 caracteres.';
        $message_type = 'error';
    } elseif ($nova_senha !== $confirmar_senha) {
        $message = 'As senhas não coincidem.';
        $message_type = 'error';
    } else {
        // Update password
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE usuarios SET senha_hash = ? WHERE id = ?");
        if ($stmt->execute([$senha_hash, $user_id])) {
            // Mark token as used
            $stmt = $pdo->prepare("UPDATE tokens_reset_senha SET usado = 1 WHERE id = ?");
            $stmt->execute([$token_id]);
            
            $_SESSION['success_message'] = 'Senha alterada com sucesso!';
            header('Location: login.php');
            exit();
        } else {
            $message = 'Erro ao redefinir senha. Tente novamente.';
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir senha - Sistema de Autenticação</title>
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
        input[type="password"] {
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
        #timer {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #d9534f;
            margin: 20px 0;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Redefinir senha</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($token_valid): ?>
            <div id="timer">Você tem <span id="countdown">--</span> segundos para redefinir sua senha.</div>
            
            <form method="POST" id="reset-form">
                <div class="form-group">
                    <label for="nova_senha">Nova Senha (mínimo 8 caracteres):</label>
                    <input type="password" id="nova_senha" name="nova_senha" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Nova Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                
                <button type="submit">Redefinir Senha</button>
            </form>
        <?php endif; ?>
        
        <div class="links">
            <p><a href="login.php">Voltar para o login</a></p>
        </div>
    </div>

    <?php if ($token_valid && $expiracao_timestamp): ?>
    <script>
        // Pass expiration time to JavaScript
        const expirationTime = <?php echo $expiracao_timestamp; ?> * 1000; 
        const countdownElement = document.getElementById('countdown');
        const resetForm = document.getElementById('reset-form');
        
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = expirationTime - now;
            
            // Calculate seconds only
            const seconds = Math.floor(distance / 1000);
            
            // Display the result
            if (distance >= 0) {
                countdownElement.textContent = seconds.toString();
            } else {
                // Timer expired
                countdownElement.textContent = '0';
                resetForm.classList.add('hidden');
                document.querySelector('.message').innerHTML = 'Token expirado. Por favor, solicite um novo link.';
                document.querySelector('.message').className = 'message error';
            }
        }
        
        // Update the countdown every second
        const timer = setInterval(updateCountdown, 1000);
        
        // Initial call to display the countdown immediately
        updateCountdown();
    </script>
    <?php endif; ?>
</body>
</html>