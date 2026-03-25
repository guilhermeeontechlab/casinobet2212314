<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
if (!$user) { header('Location: /'); exit; }
$db = getDB();
$stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = :uid AND type = 'withdraw' ORDER BY id DESC LIMIT 1");
$stmt->execute([':uid' => $user['id']]);
$tx = $stmt->fetch();
$lang = getLang();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('withdraw') ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:1rem;"><?= t('withdraw') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div class="page-wrapper" style="text-align:center;padding-top:60px;">
    <div style="margin:40px auto;max-width:320px;">
        <div style="width:80px;height:80px;border-radius:50%;background:rgba(255,170,9,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
            <i class="fa-solid fa-clock" style="font-size:2rem;color:var(--color-gold);"></i>
        </div>
        <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:10px;color:#fff;"><?= t('analysis') ?></h2>
        <p style="font-size:0.85rem;color:var(--text-secondary);line-height:1.5;margin-bottom:20px;">
            <?= t('analysis_desc') ?>
        </p>
        <?php if ($tx): ?>
        <div style="background:var(--bg-card);border-radius:var(--border-radius-sm);padding:16px;margin-bottom:20px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:0.8rem;color:var(--text-muted);">Valor</span>
                <span style="font-size:0.9rem;font-weight:700;color:var(--color-gold);">R$ <?= number_format($tx['amount'], 2, ',', '.') ?></span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:0.8rem;color:var(--text-muted);">Status</span>
                <span style="font-size:0.8rem;font-weight:600;color:var(--color-gold);">
                    <i class="fa-solid fa-hourglass-half"></i> Em análise
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;">
                <span style="font-size:0.8rem;color:var(--text-muted);">PIX</span>
                <span style="font-size:0.8rem;color:var(--text-secondary);"><?= htmlspecialchars($tx['pix_key'] ?? '') ?></span>
            </div>
        </div>
        <?php endif; ?>
        <a href="/" class="btn-submit" style="display:block;text-decoration:none;text-align:center;">
            <i class="fa-solid fa-house"></i> <?= t('home') ?>
        </a>
    </div>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><i class="fa-solid fa-house"></i><span><?= t('home') ?></span></a>
    <a href="/auth.php?action=logout" class="bnav-item"><i class="fa-solid fa-right-from-bracket"></i><span><?= t('disconnect') ?></span></a>
    <a href="/deposit.php" class="bnav-item"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="/profile.php" class="bnav-item"><i class="fa-solid fa-user"></i><span><?= t('profile') ?></span></a>
</nav>

</body>
</html>
