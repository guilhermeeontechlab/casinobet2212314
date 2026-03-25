<?php
require_once __DIR__ . '/config.php';

$user = getCurrentUser();
$status = $_GET['status'] ?? 'pending';
$paymentId = $_GET['payment_id'] ?? '';
$externalRef = $_GET['external_reference'] ?? '';

if ($status === 'approved' && $externalRef) {
    // Parse external reference: user_id_amount_timestamp
    $parts = explode('_', $externalRef);
    $userId = (int)($parts[0] ?? 0);
    $amount = floatval($parts[1] ?? 0);

    if ($userId > 0 && $amount > 0) {
        $db = getDB();

        // Verificar se já não foi creditado (evita duplicidade)
        $stmtCheck = $db->prepare("SELECT id FROM transactions WHERE mp_preference_id = :ref AND status = 'approved'");
        $stmtCheck->execute([':ref' => $externalRef]);
        if (!$stmtCheck->fetch()) {
            // Verificar bônus de idioma
            $stmtUser = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmtUser->execute([':id' => $userId]);
            $depositUser = $stmtUser->fetch();

            $bonusAmount = 0;
            if ($depositUser && !$depositUser['first_deposit_done'] && isset($_SESSION['lang_bonus_active']) && $_SESSION['lang_bonus_active']) {
                $bonusAmount = 400;
                unset($_SESSION['lang_bonus_active']);
            }

            $totalCredit = $amount + $bonusAmount;

            // Creditar saldo
            $stmtCredit = $db->prepare("UPDATE users SET balance = balance + :amount, first_deposit_done = 1 WHERE id = :id");
            $stmtCredit->execute([':amount' => $totalCredit, ':id' => $userId]);

            // Atualizar transação
            $stmtTx = $db->prepare("UPDATE transactions SET status = 'approved', mp_payment_id = :pid WHERE mp_preference_id = :ref");
            $stmtTx->execute([':pid' => $paymentId, ':ref' => $externalRef]);

            $bonusMsg = $bonusAmount > 0 ? ' + Bônus R$ 400,00!' : '';
            $formattedAmount = number_format($amount, 2, ',', '.');
            header('Location: /?msg=' . urlencode('Depósito de R$ ' . $formattedAmount . ' aprovado!' . $bonusMsg) . '&type=success');
            exit;
        }
    }

    header('Location: /?msg=' . urlencode('Depósito aprovado!') . '&type=success');
    exit;
}

if ($status === 'pending') {
    header('Location: /?msg=' . urlencode('Pagamento pendente. Aguarde a confirmação.') . '&type=success');
    exit;
}

header('Location: /?msg=' . urlencode('Pagamento não aprovado. Tente novamente.') . '&type=error');
exit;
