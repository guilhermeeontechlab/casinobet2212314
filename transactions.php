<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
if (!$user) { header('Location: /?msg=Faça login&type=error'); exit; }
$lang = getLang();
$db = getDB();

// Filter by tab
$filter = $_GET['filter'] ?? 'all';
$validFilters = ['all', 'deposit', 'withdraw'];
if (!in_array($filter, $validFilters)) $filter = 'all';

if ($filter === 'all') {
    $stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = :uid ORDER BY created_at DESC");
    $stmt->execute([':uid' => $user['id']]);
} else {
    $stmt = $db->prepare("SELECT * FROM transactions WHERE user_id = :uid AND type = :type ORDER BY created_at DESC");
    $stmt->execute([':uid' => $user['id'], ':type' => $filter]);
}
$transactions = $stmt->fetchAll();

function statusLabel($status) {
    $map = [
        'pending' => ['status_pending', '#ffaa09', 'fa-clock'],
        'analysis' => ['status_analysis', '#ffaa09', 'fa-hourglass-half'],
        'approved' => ['status_approved', '#1db954', 'fa-check-circle'],
        'completed' => ['status_approved', '#1db954', 'fa-check-circle'],
        'rejected' => ['status_rejected', '#d60000', 'fa-times-circle'],
    ];
    return $map[$status] ?? ['status_pending', '#ffaa09', 'fa-clock'];
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('transaction_history_title') ?> - <?= SITE_NAME ?></title>
    <link rel="icon" type="image/png" href="/public/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;padding:8px;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:0.95rem;"><?= t('transaction_history_title') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div class="page-wrapper">
    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-label"><?= t('available_balance') ?></div>
        <div class="balance-amount">R$ <?= number_format($user['balance'], 2, ',', '.') ?></div>
    </div>

    <!-- Tabs -->
    <div class="tx-tabs">
        <a href="/transactions.php?filter=all" class="tx-tab <?= $filter === 'all' ? 'active' : '' ?>"><?= t('all') ?></a>
        <a href="/transactions.php?filter=deposit" class="tx-tab <?= $filter === 'deposit' ? 'active' : '' ?>"><?= t('deposits') ?></a>
        <a href="/transactions.php?filter=withdraw" class="tx-tab <?= $filter === 'withdraw' ? 'active' : '' ?>"><?= t('withdrawals') ?></a>
    </div>

    <!-- Transactions List -->
    <?php if (empty($transactions)): ?>
        <div class="tx-empty">
            <i class="fa-solid fa-receipt" style="font-size:2rem;color:#333;margin-bottom:10px;"></i>
            <p><?= t('no_transactions') ?></p>
        </div>
    <?php else: ?>
        <div class="tx-list">
            <?php foreach ($transactions as $tx):
                $sInfo = statusLabel($tx['status']);
                $isDeposit = $tx['type'] === 'deposit';
                $icon = $isDeposit ? 'fa-arrow-down' : 'fa-arrow-up';
                $iconColor = $isDeposit ? '#1db954' : '#ffaa09';
                $typeLabel = $isDeposit ? t('deposits') : t('withdrawals');
                $date = date('d/m/Y H:i', strtotime($tx['created_at']));
            ?>
            <div class="tx-item">
                <div class="tx-item-left">
                    <div class="tx-icon" style="background:<?= $iconColor ?>20;color:<?= $iconColor ?>;">
                        <i class="fa-solid <?= $icon ?>"></i>
                    </div>
                    <div class="tx-info">
                        <div class="tx-type"><?= $typeLabel ?></div>
                        <div class="tx-date"><?= $date ?></div>
                    </div>
                </div>
                <div class="tx-item-right">
                    <div class="tx-amount" style="color:<?= $isDeposit ? '#1db954' : '#ffaa09' ?>;">
                        <?= $isDeposit ? '+' : '-' ?> R$ <?= number_format($tx['amount'], 2, ',', '.') ?>
                    </div>
                    <div class="tx-status" style="color:<?= $sInfo[1] ?>;">
                        <i class="fa-solid <?= $sInfo[2] ?>" style="font-size:0.6rem;"></i> <?= t($sInfo[0]) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><img src="/public/img/icons/home.png" alt="" class="bnav-icon"><span><?= t('home') ?></span></a>
    <a href="/auth.php?action=logout" class="bnav-item"><img src="/public/img/icons/enter.png" alt="" class="bnav-icon"><span><?= t('disconnect') ?></span></a>
    <a href="/deposit.php" class="bnav-item"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="/profile.php" class="bnav-item"><img src="/public/img/icons/profile.png" alt="" class="bnav-icon"><span><?= t('profile') ?></span></a>
</nav>

<script src="/assets/js/app.js"></script>
<?php if (isset($_GET['msg'])): ?>
<script>showToast('<?= htmlspecialchars($_GET['msg']) ?>', '<?= htmlspecialchars($_GET['type'] ?? 'success') ?>');</script>
<?php endif; ?>
</body>
</html>
