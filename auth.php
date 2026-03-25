<?php
require_once __DIR__ . '/config.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        header('Location: /?msg=Preencha todos os campos&type=error');
        exit;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: /?msg=Bem-vindo de volta, ' . urlencode($user['username']) . '!&type=success');
        exit;
    } else {
        header('Location: /?msg=Usuário ou senha incorretos&type=error');
        exit;
    }
}

if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    if (empty($username) || empty($password)) {
        header('Location: /?msg=Preencha os campos obrigatórios&type=error');
        exit;
    }
    
    if (strlen($password) < 6) {
        header('Location: /?msg=A senha deve ter no mínimo 6 caracteres&type=error');
        exit;
    }
    
    if ($password !== $passwordConfirm) {
        header('Location: /?msg=As senhas não coincidem&type=error');
        exit;
    }
    
    $db = getDB();
    
    // Check if username exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    if ($stmt->fetch()) {
        header('Location: /?msg=Este nome de usuário já está em uso&type=error');
        exit;
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("INSERT INTO users (username, email, phone, password, balance) VALUES (:username, :email, :phone, :password, 0.00)");
    
    if ($stmt->execute([':username' => $username, ':email' => $email, ':phone' => $phone, ':password' => $hashedPassword])) {
        $_SESSION['user_id'] = $db->lastInsertId();
        header('Location: /?msg=Conta criada com sucesso! Bem-vindo!&type=success');
        exit;
    } else {
        header('Location: /?msg=Erro ao criar conta. Tente novamente.&type=error');
        exit;
    }
}

if ($action === 'logout') {
    session_destroy();
    header('Location: /?msg=Você saiu da sua conta&type=success');
    exit;
}

header('Location: /');
exit;
