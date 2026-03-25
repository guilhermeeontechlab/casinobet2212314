<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
if (!$user) { header('Location: /?msg=Faça login para acessar seu perfil&type=error'); exit; }
$lang = getLang();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('profile') ?> - <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="/public/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:1rem;"><?= t('profile') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div class="page-wrapper">
    <div class="profile-header">
        <div class="profile-avatar"><?= strtoupper(substr($user['username'], 0, 2)) ?></div>
        <div class="profile-name"><?= htmlspecialchars($user['username']) ?></div>
        <div class="profile-since"><?= t('member_since') ?> <?= date('d/m/Y', strtotime($user['created_at'])) ?></div>
    </div>

    <div class="balance-card">
        <div class="balance-label"><?= t('available_balance') ?></div>
        <div class="balance-amount">R$ <?= number_format($user['balance'], 2, ',', '.') ?></div>
    </div>

    <div class="profile-actions">
        <a href="/deposit.php" class="btn btn-deposit-profile"><i class="fa-solid fa-plus"></i> <?= t('deposit') ?></a>
        <a href="/withdraw.php" class="btn btn-withdraw-profile"><i class="fa-solid fa-money-bill-transfer"></i> <?= t('withdraw') ?></a>
    </div>

    <div class="profile-menu">
        <a href="/deposit.php" class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(29,185,84,0.15);color:var(--color-green);"><i class="fa-solid fa-plus-circle"></i></div>
            <span class="profile-menu-text"><?= t('deposit') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </a>
        <a href="/withdraw.php" class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(214,0,0,0.15);color:var(--color-red);"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <span class="profile-menu-text"><?= t('withdraw') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </a>
        <div class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(62,149,254,0.15);color:#3E95FE;"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <span class="profile-menu-text"><?= t('transaction_history') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </div>
        <div class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(202,80,243,0.15);color:#ca50f3;"><i class="fa-solid fa-gamepad"></i></div>
            <span class="profile-menu-text"><?= t('game_history') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </div>
        <div class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(255,170,9,0.15);color:var(--color-gold);"><i class="fa-solid fa-crown"></i></div>
            <span class="profile-menu-text"><?= t('vip_program') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </div>
        <div class="profile-menu-item">
            <div class="profile-menu-icon" style="background:rgba(255,255,255,0.05);color:var(--text-secondary);"><i class="fa-solid fa-gear"></i></div>
            <span class="profile-menu-text"><?= t('settings') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </div>
        <a href="/auth.php?action=logout" class="profile-menu-item" style="color:var(--color-red);">
            <div class="profile-menu-icon" style="background:rgba(214,0,0,0.15);color:var(--color-red);"><i class="fa-solid fa-right-from-bracket"></i></div>
            <span class="profile-menu-text"><?= t('logout') ?></span>
            <i class="fa-solid fa-chevron-right arrow"></i>
        </a>
    </div>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><i class="fa-solid fa-house"></i><span><?= t('home') ?></span></a>
    <a href="/auth.php?action=logout" class="bnav-item"><i class="fa-solid fa-right-from-bracket"></i><span><?= t('disconnect') ?></span></a>
    <a href="/deposit.php" class="bnav-item"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="/profile.php" class="bnav-item active"><i class="fa-solid fa-user"></i><span><?= t('profile') ?></span></a>
</nav>

<script src="/assets/js/app.js"></script>
</body>
</html>
