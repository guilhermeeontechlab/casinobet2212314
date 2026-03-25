<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
if (!$user) { header('Location: /?msg=Faça login para sacar&type=error'); exit; }
$lang = getLang();

// Process withdraw
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $cpf = preg_replace('/\D/', '', trim($_POST['cpf'] ?? ''));
    $pixType = $_POST['pix_type'] ?? '';
    $pixKey = trim($_POST['pix_key'] ?? '');

    if (empty($fullName) || empty($cpf) || empty($pixType) || empty($pixKey)) {
        header('Location: /withdraw.php?msg=' . urlencode(t('withdraw_fields_error')) . '&type=error');
        exit;
    }
    if ($amount < 400) {
        header('Location: /withdraw.php?msg=' . urlencode(t('withdraw_min_error')) . '&type=error');
        exit;
    }
    if ($amount > 3000) {
        header('Location: /withdraw.php?msg=' . urlencode(t('withdraw_max_error')) . '&type=error');
        exit;
    }
    if ($amount > $user['balance']) {
        header('Location: /withdraw.php?msg=' . urlencode(t('withdraw_balance_error')) . '&type=error');
        exit;
    }

    $db = getDB();
    // Debit balance
    $stmt = $db->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
    $stmt->execute([':amount' => $amount, ':id' => $user['id']]);

    // Insert transaction — always 'analysis' status
    $stmt = $db->prepare("INSERT INTO transactions (user_id, type, amount, status, pix_type, pix_key, full_name, cpf) VALUES (:uid, 'withdraw', :amount, 'analysis', :pix_type, :pix_key, :full_name, :cpf)");
    $stmt->execute([
        ':uid' => $user['id'],
        ':amount' => $amount,
        ':pix_type' => $pixType,
        ':pix_key' => $pixKey,
        ':full_name' => $fullName,
        ':cpf' => $cpf,
    ]);

    header('Location: /withdraw.php?msg=' . urlencode(t('withdraw_success')) . '&type=success');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('withdraw') ?> - <?= SITE_NAME ?></title>
    <link rel="icon" type="image/png" href="/public/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;padding:8px;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:0.95rem;"><?= t('withdraw_page_title') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div class="page-wrapper">
    <div class="balance-card">
        <div class="balance-label"><?= t('available_balance') ?></div>
        <div class="balance-amount">R$ <?= number_format($user['balance'], 2, ',', '.') ?></div>
    </div>

    <div style="display:flex;justify-content:space-between;margin-bottom:14px;padding:10px 12px;background:#111;border-radius:8px;border:1px solid #222;">
        <span style="font-size:0.75rem;color:var(--color-gold);font-weight:600;"><i class="fa-solid fa-info-circle"></i> <?= t('min_withdraw') ?></span>
        <span style="font-size:0.75rem;color:var(--color-gold);font-weight:600;"><?= t('max_withdraw') ?></span>
    </div>

    <form method="POST" action="/withdraw.php">
        <div class="form-group">
            <label><?= t('full_name') ?> *</label>
            <input type="text" name="full_name" class="form-control" placeholder="<?= t('full_name') ?>" required>
        </div>

        <div class="form-group">
            <label><?= t('cpf') ?> *</label>
            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00" oninput="cpfMask(this)" maxlength="14" required>
        </div>

        <div class="form-group">
            <label><?= t('pix_key_type') ?> *</label>
            <select name="pix_type" class="form-control" style="background:var(--bg-input);" required>
                <option value="">Selecione...</option>
                <option value="cpf">CPF</option>
                <option value="phone">Telefone</option>
                <option value="email">E-mail</option>
                <option value="random">Chave Aleatória</option>
            </select>
        </div>

        <div class="form-group">
            <label><?= t('pix_key') ?> *</label>
            <input type="text" name="pix_key" class="form-control" placeholder="<?= t('pix_key') ?>" required>
        </div>

        <div class="form-group">
            <label><?= t('withdraw_amount') ?> *</label>
            <input type="number" name="amount" id="withdrawAmount" class="form-control" placeholder="R$ 400,00 - R$ 3.000,00" min="400" max="3000" step="0.01" required>
        </div>

        <div class="deposit-amounts" style="margin-bottom:14px;">
            <div class="deposit-amount-btn" onclick="setW(this,400)">R$ 400</div>
            <div class="deposit-amount-btn" onclick="setW(this,500)">R$ 500</div>
            <div class="deposit-amount-btn" onclick="setW(this,1000)">R$ 1.000</div>
            <div class="deposit-amount-btn" onclick="setW(this,1500)">R$ 1.500</div>
            <div class="deposit-amount-btn" onclick="setW(this,2000)">R$ 2.000</div>
            <div class="deposit-amount-btn" onclick="setW(this,3000)">R$ 3.000</div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-paper-plane"></i> <?= t('request_withdraw') ?>
        </button>

        <div class="info-text">
            <i class="fa-solid fa-shield-halved"></i> Processamento em até 48h úteis via PIX
        </div>
    </form>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><img src="/public/img/icons/home.png" alt="" class="bnav-icon"><span><?= t('home') ?></span></a>
    <a href="/auth.php?action=logout" class="bnav-item"><img src="/public/img/icons/enter.png" alt="" class="bnav-icon"><span><?= t('disconnect') ?></span></a>
    <a href="/deposit.php" class="bnav-item"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="/profile.php" class="bnav-item"><img src="/public/img/icons/profile.png" alt="" class="bnav-icon"><span><?= t('profile') ?></span></a>
</nav>

<script src="/assets/js/app.js"></script>
<script>
function setW(el, amount) {
    document.querySelectorAll('.deposit-amount-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('withdrawAmount').value = amount;
}
function cpfMask(input) {
    let v = input.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 9) v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6,9) + '-' + v.slice(9);
    else if (v.length > 6) v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6);
    else if (v.length > 3) v = v.slice(0,3) + '.' + v.slice(3);
    input.value = v;
}
</script>
<?php if (isset($_GET['msg'])): ?>
<script>showToast('<?= htmlspecialchars($_GET['msg']) ?>', '<?= htmlspecialchars($_GET['type'] ?? 'success') ?>');</script>
<?php endif; ?>
</body>
</html>
