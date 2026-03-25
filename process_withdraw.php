<?php
require_once __DIR__ . '/config.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: /?msg=Faça login para sacar&type=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /withdraw.php');
    exit;
}

$amount = floatval($_POST['amount'] ?? 0);
$pixType = $_POST['pix_type'] ?? '';
$pixKey = trim($_POST['pix_key'] ?? '');

if (empty($pixType) || empty($pixKey)) {
    header('Location: /withdraw.php?msg=Preencha todos os campos&type=error');
    exit;
}

if ($amount < 20) {
    header('Location: /withdraw.php?msg=Valor mínimo para saque é R$ 20,00&type=error');
    exit;
}

if ($amount > $user['balance']) {
    header('Location: /withdraw.php?msg=Saldo insuficiente&type=error');
    exit;
}

$db = getDB();

// Debita o saldo
$newBalance = $user['balance'] - $amount;
$stmt = $db->prepare("UPDATE users SET balance = :balance WHERE id = :id");
$stmt->execute([':balance' => $newBalance, ':id' => $user['id']]);

// Registra transação como "em análise" (nunca será aprovada automaticamente)
$stmtTx = $db->prepare("INSERT INTO transactions (user_id, type, amount, status, pix_type, pix_key) VALUES (:uid, 'withdraw', :amount, 'analysis', :pix_type, :pix_key)");
$stmtTx->execute([
    ':uid' => $user['id'],
    ':amount' => $amount,
    ':pix_type' => $pixType,
    ':pix_key' => $pixKey,
]);

// Redireciona para página de análise
header('Location: /withdraw_status.php');
exit;
