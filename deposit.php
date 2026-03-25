<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
if (!$user) { header('Location: /?msg=Faça login para depositar&type=error'); exit; }
$lang = getLang();
$bonusActive = isset($_SESSION['lang_bonus_active']) && $_SESSION['lang_bonus_active'] && !$user['first_deposit_done'];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('deposit') ?> - <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="/public/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:1rem;"><?= t('deposit') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div class="page-wrapper">
    <div class="balance-card">
        <div class="balance-label"><?= t('available_balance') ?></div>
        <div class="balance-amount">R$ <?= number_format($user['balance'], 2, ',', '.') ?></div>
    </div>

    <?php if ($bonusActive): ?>
    <div style="background:rgba(29,185,84,0.1);border:1px solid rgba(29,185,84,0.3);border-radius:8px;padding:12px;margin-bottom:16px;text-align:center;">
        <div style="font-size:0.85rem;font-weight:700;color:var(--color-green);margin-bottom:4px;">
            <i class="fa-solid fa-gift"></i> Bônus Ativo!
        </div>
        <div style="font-size:0.75rem;color:var(--text-secondary);">
            +R$ 400,00 será adicionado ao seu primeiro depósito!
        </div>
    </div>
    <?php endif; ?>

    <form action="/process_deposit.php" method="POST">
        <label style="font-size:0.8rem;font-weight:600;color:var(--text-secondary);display:block;margin-bottom:8px;">
            <?= t('select_amount') ?>
        </label>
        <div class="deposit-amounts">
            <div class="deposit-amount-btn" data-amount="20" onclick="selectAmount(this,20)">R$ 20</div>
            <div class="deposit-amount-btn" data-amount="50" onclick="selectAmount(this,50)">R$ 50</div>
            <div class="deposit-amount-btn" data-amount="100" onclick="selectAmount(this,100)">R$ 100</div>
            <div class="deposit-amount-btn" data-amount="200" onclick="selectAmount(this,200)">R$ 200</div>
            <div class="deposit-amount-btn" data-amount="500" onclick="selectAmount(this,500)">R$ 500</div>
            <div class="deposit-amount-btn" data-amount="1000" onclick="selectAmount(this,1000)">R$ 1.000</div>
        </div>

        <input type="number" name="amount" id="depositAmount" class="form-control-page" placeholder="Ou digite o valor (mín R$ 10)" min="10" step="0.01" required>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-qrcode"></i> <?= t('generate_pix') ?>
        </button>

        <div class="info-text">
            <i class="fa-solid fa-shield-halved"></i> Depósito instantâneo via Mercado Pago • PIX • 24h
        </div>
    </form>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><i class="fa-solid fa-house"></i><span><?= t('home') ?></span></a>
    <a href="/auth.php?action=logout" class="bnav-item"><i class="fa-solid fa-right-from-bracket"></i><span><?= t('disconnect') ?></span></a>
    <a href="/deposit.php" class="bnav-item active"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="/profile.php" class="bnav-item"><i class="fa-solid fa-user"></i><span><?= t('profile') ?></span></a>
</nav>

<script src="/assets/js/app.js"></script>
<script>
function selectAmount(el, amount) {
    document.querySelectorAll('.deposit-amount-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('depositAmount').value = amount;
}
</script>
<?php if (isset($_GET['msg'])): ?>
<script>showToast('<?= htmlspecialchars($_GET['msg']) ?>', '<?= htmlspecialchars($_GET['type'] ?? 'success') ?>');</script>
<?php endif; ?>
</body>
</html>
