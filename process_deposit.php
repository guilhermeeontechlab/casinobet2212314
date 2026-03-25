<?php
require_once __DIR__ . '/config.php';

$user = getCurrentUser();
if (!$user) {
    header('Location: /?msg=Faça login para depositar&type=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /deposit.php');
    exit;
}

$amount = floatval($_POST['amount'] ?? 0);

if ($amount < 10) {
    header('Location: /deposit.php?msg=Valor mínimo é R$ 10,00&type=error');
    exit;
}

if ($amount > 50000) {
    header('Location: /deposit.php?msg=Valor máximo é R$ 50.000,00&type=error');
    exit;
}

$db = getDB();

// ============================================
// MERCADO PAGO - Criar preferência de pagamento
// ============================================
$mpToken = MP_ACCESS_TOKEN;

// Se o token for o padrão de teste, simula o depósito direto
if (strpos($mpToken, 'TEST-0000') === 0) {
    // Modo simulação - credita direto
    $bonusAmount = 0;
    $bonusMsg = '';

    // "Bug" do idioma: +R$400 no primeiro depósito se lang_bonus_active
    if (isset($_SESSION['lang_bonus_active']) && $_SESSION['lang_bonus_active'] && !$user['first_deposit_done']) {
        $bonusAmount = 400;
        $bonusMsg = ' + Bônus R$ 400,00 (idioma)';
        unset($_SESSION['lang_bonus_active']);
        $stmtBonus = $db->prepare("UPDATE users SET first_deposit_done = 1 WHERE id = :id");
        $stmtBonus->execute([':id' => $user['id']]);
    } elseif (!$user['first_deposit_done']) {
        // Marca primeiro depósito mesmo sem bônus
        $stmtBonus = $db->prepare("UPDATE users SET first_deposit_done = 1 WHERE id = :id");
        $stmtBonus->execute([':id' => $user['id']]);
    }

    $totalCredit = $amount + $bonusAmount;
    $newBalance = $user['balance'] + $totalCredit;
    $stmt = $db->prepare("UPDATE users SET balance = :balance WHERE id = :id");
    $stmt->execute([':balance' => $newBalance, ':id' => $user['id']]);

    // Registrar transação
    $stmtTx = $db->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (:uid, 'deposit', :amount, 'approved')");
    $stmtTx->execute([':uid' => $user['id'], ':amount' => $totalCredit]);

    $formattedAmount = number_format($amount, 2, ',', '.');
    $msg = 'Depósito de R$ ' . $formattedAmount . ' realizado com sucesso!' . $bonusMsg;
    header('Location: /?msg=' . urlencode($msg) . '&type=success');
    exit;
}

// ============================================
// MERCADO PAGO - Modo produção com API real
// ============================================
$preferenceData = [
    'items' => [
        [
            'title' => 'Depósito ' . SITE_NAME,
            'quantity' => 1,
            'unit_price' => $amount,
            'currency_id' => 'BRL',
        ]
    ],
    'back_urls' => [
        'success' => SITE_URL . 'mp_callback.php?status=approved',
        'failure' => SITE_URL . 'mp_callback.php?status=rejected',
        'pending' => SITE_URL . 'mp_callback.php?status=pending',
    ],
    'auto_return' => 'approved',
    'external_reference' => $user['id'] . '_' . $amount . '_' . time(),
    'payment_methods' => [
        'excluded_payment_types' => [
            ['id' => 'credit_card'],
            ['id' => 'debit_card'],
            ['id' => 'ticket'],
        ],
        'default_payment_method_id' => 'pix',
    ],
];

$ch = curl_init('https://api.mercadopago.com/checkout/preferences');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($preferenceData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $mpToken,
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 201 || $httpCode === 200) {
    $data = json_decode($response, true);
    $preferenceId = $data['id'] ?? '';
    $initPoint = $data['init_point'] ?? '';

    // Salvar transação pendente
    $stmtTx = $db->prepare("INSERT INTO transactions (user_id, type, amount, status, mp_preference_id) VALUES (:uid, 'deposit', :amount, 'pending', :pref)");
    $stmtTx->execute([':uid' => $user['id'], ':amount' => $amount, ':pref' => $preferenceId]);

    // Redirecionar para o checkout do Mercado Pago
    header('Location: ' . $initPoint);
    exit;
} else {
    header('Location: /deposit.php?msg=Erro ao processar pagamento. Tente novamente.&type=error');
    exit;
}
