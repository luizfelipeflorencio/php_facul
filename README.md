# Sistema de Autenticação Básico em PHP

Este é um sistema de autenticação de usuários desenvolvido em PHP com as seguintes funcionalidades:

## Funcionalidades

1. **Registro de Usuário**
   - Formulário com validação de campos
   - Verificação de e-mail único
   - Armazenamento seguro de senhas com hash

2. **Login de Usuário**
   - Autenticação segura
   - Sessões de usuário
   - Mensagens de erro genéricas para segurança

3. **Recuperação de Senha**
   - Solicitação de redefinição por e-mail
   - Geração de tokens seguros e expiráveis
   - Link único para redefinição

4. **Redefinição de Senha com Timer**
   - Formulário de redefinição protegido por token
   - Contagem regressiva em JavaScript mostrando tempo restante (30 segundos)
   - Tokens de uso único com expiração automática

## Requisitos

- PHP 7.0 ou superior
- Servidor web (Apache, Nginx, etc.)
- MySQL ou MariaDB

## Configuração

1. **Configurar o Banco de Dados**
   - Crie um banco de dados MySQL chamado `auth_system`
   - Atualize as credenciais no arquivo [config.php](localhost/php_facul/config.php) se necessário

2. **Inicializar as Tabelas**
   - Execute o script [init_db.php](localhost/Downloads/php_facul/init_db.php) para criar as tabelas necessárias
   - Ou execute o script SQL manualmente: [database_schema.sql](localhost/php_facul/database_schema.sql)

3. **Iniciar o Servidor**
   - Coloque os arquivos em um servidor web
   - Acesse [registrar.php](localhost/php_facul/registrar.php) para começar

## Estrutura do Banco de Dados

```sql
-- Tabela de usuários
usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

-- Tabela de tokens de redefinição de senha
tokens_reset_senha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    data_expiracao DATETIME NOT NULL,
    usado TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
)
```

## Segurança

- Senhas armazenadas com `password_hash()` (Bcrypt)
- Tokens criptograficamente seguros gerados com `random_bytes()`
- Prevenção de SQL Injection com Prepared Statements
- Proteção contra XSS com `htmlspecialchars()`
- Mensagens de erro genéricas para evitar enumeração de usuários

## Arquivos

- [config.php](/php_facul/config.php) - Configuração do banco de dados
- [init_db.php](/php_facul/init_db.php) - Script de inicialização do banco de dados
- [registrar.php](/php_facul/registrar.php) - Página de registro
- [login.php](/php_facul/login.php) - Página de login
- [esqueci-senha.php](/php_facul/esqueci-senha.php) - Página de recuperação de senha
- [redefinir-senha.php](/php_facul/redefinir-senha.php) - Página de redefinição de senha com timer
- [dashboard.php](/php_facul/dashboard.php) - Área restrita após login

## Como Usar

1. Acesse [registrar.php](localhost/php_facul/registrar.php) para criar uma conta
2. Faça login em [login.php](localhost/php_facul/login.php)
3. Acesse o dashboard em [dashboard.php](localhost/php_facul/dashboard.php)
4. Teste a recuperação de senha em [esqueci-senha.php](localhost/php_facul/esqueci-senha.php)