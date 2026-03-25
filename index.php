<?php
require_once __DIR__ . '/config.php';

// Handle language change via GET
if (isset($_GET['lang'])) {
    setLang($_GET['lang']);
}

$user = getCurrentUser();
$db = getDB();
$lang = getLang();

// Categoria ativa
$categoryId = isset($_GET['gameCategoryId']) ? (int)$_GET['gameCategoryId'] : 0;

// Buscar jogos
if ($categoryId === 0) {
    $stmt = $db->query("SELECT * FROM games ORDER BY is_hot DESC, sort_order ASC, id ASC");
} else {
    $stmt = $db->prepare("SELECT * FROM games WHERE category_id = :cat ORDER BY is_hot DESC, sort_order ASC, id ASC");
    $stmt->execute([':cat' => $categoryId]);
}
$gamesList = $stmt->fetchAll();

$categories = [
    ['id' => 0, 'name' => t('popular'), 'icon' => '/public/img/icons/fire.png'],
    ['id' => 1, 'name' => t('slots'), 'icon' => '/public/img/icons/slots.png'],
    ['id' => 2, 'name' => t('pragmatic'), 'icon' => '/public/img/icons/cat-pragmatic.png'],
    ['id' => 3, 'name' => t('fishing'), 'icon' => '/public/img/icons/fishing.png'],
    ['id' => 4, 'name' => t('microgaming'), 'icon' => '/public/img/icons/cat-microgaming.png'],
];

$flags = [
    'pt' => '🇧🇷',
    'it' => '🇮🇹',
    'en' => '🇺🇸',
    'es' => '🇪🇸',
];
$currentFlag = $flags[$lang] ?? '🇮🇹';

// Auto-detect banners from directory
$bannerDir = __DIR__ . '/public/img/banners';
$bannerFiles = [];
if (is_dir($bannerDir)) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    $files = scandir($bannerDir);
    foreach ($files as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $bannerFiles[] = '/public/img/banners/' . $f;
        }
    }
    sort($bannerFiles);
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?= SITE_NAME ?></title>
    <link rel="icon" type="image/png" href="/public/img/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/" class="sidebar-logo"><img src="/public/img/logo.png" alt="<?= SITE_NAME ?>" style="height:36px;"></a>
    </div>
    <nav class="sidebar-nav">
        <a href="/" class="sidebar-item active"><i class="fa-solid fa-house"></i><span>Início</span></a>
        <?php foreach ($categories as $cat): ?>
        <a href="/?gameCategoryId=<?= $cat['id'] ?>" class="sidebar-item">
            <img src="<?= $cat['icon'] ?>" alt="" style="width:20px;height:20px;">
            <span><?= $cat['name'] ?></span>
        </a>
        <?php endforeach; ?>
        <div class="sidebar-divider"></div>
        <a href="#" class="sidebar-item"><i class="fa-solid fa-headset"></i><span>Suporte 24h</span></a>
        <a href="#" class="sidebar-item"><i class="fa-brands fa-telegram"></i><span>Telegram</span></a>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- NAVBAR TOP - Idêntico mangafogo -->
<header class="navbar-top">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars-staggered"></i>
        </button>
        <?php if ($user): ?>
            <div class="navbar-balance-pill">
                <i class="fa-solid fa-coins"></i>
                <span><?= number_format($user['balance'], 2, ',', '.') ?></span>
                <i class="fa-solid fa-rotate"></i>
            </div>
        <?php endif; ?>
    </div>
    <div class="navbar-center">
        <a href="/" class="navbar-logo">
            <img src="/public/img/logo.png" alt="<?= SITE_NAME ?>" class="navbar-logo-img">
        </a>
    </div>
    <div class="navbar-right">
        <?php if ($user): ?>
            <div class="deposit-dropdown-wrapper" id="depositDropdownWrapper">
                <button class="btn-deposit-nav" id="depositDropdownBtn">
                    <?= t('deposit') ?> <i class="fa-solid fa-chevron-down" style="font-size:0.55rem;margin-left:3px;"></i>
                </button>
                <div class="deposit-dropdown" id="depositDropdown">
                    <a href="/deposit.php" class="deposit-dropdown-item">
                        <i class="fa-solid fa-arrow-down" style="color:var(--color-green);"></i> <?= t('deposit') ?>
                    </a>
                    <a href="/withdraw.php" class="deposit-dropdown-item">
                        <i class="fa-solid fa-arrow-up" style="color:var(--color-gold);"></i> <?= t('withdraw') ?>
                    </a>
                    <a href="/transactions.php" class="deposit-dropdown-item">
                        <i class="fa-solid fa-clock-rotate-left" style="color:var(--text-secondary);"></i> <?= t('transaction_history') ?>
                    </a>
                </div>
            </div>
            <!-- Language selector -->
            <div class="lang-selector" id="langSelector">
                <button class="lang-btn" id="langBtn">
                    <span style="font-size:1.2rem;"><?= $currentFlag ?></span>
                </button>
                <div class="lang-dropdown" id="langDropdown">
                    <a href="/change_lang.php?lang=pt" class="lang-option <?= $lang === 'pt' ? 'active' : '' ?>">
                        <span>🇧🇷</span> Português
                    </a>
                    <a href="/change_lang.php?lang=it" class="lang-option <?= $lang === 'it' ? 'active' : '' ?>">
                        <span>🇮🇹</span> Italiano
                    </a>
                    <a href="/change_lang.php?lang=en" class="lang-option <?= $lang === 'en' ? 'active' : '' ?>">
                        <span>🇺🇸</span> English
                    </a>
                    <a href="/change_lang.php?lang=es" class="lang-option <?= $lang === 'es' ? 'active' : '' ?>">
                        <span>🇪🇸</span> Español
                    </a>
                </div>
            </div>
        <?php else: ?>
            <button class="btn-login-nav" onclick="openModal('loginModal')"><?= t('login') ?></button>
            <button class="btn-register-nav" onclick="openModal('registerModal')"><?= t('register') ?></button>
        <?php endif; ?>
    </div>
</header>

<!-- RED LINE -->
<div class="red-separator"></div>

<!-- MAIN CONTENT -->
<main class="main-content-v2">

    <!-- BANNER CAROUSEL (auto-detect) -->
    <?php if (count($bannerFiles) > 0): ?>
    <div class="banner-section-v2">
        <div class="banner-carousel-v2" id="bannerCarousel">
            <div class="banner-slides-wrapper" id="bannerSlidesWrapper">
                <?php foreach ($bannerFiles as $banner): ?>
                <div class="banner-slide-v2">
                    <img src="<?= $banner ?>" alt="Banner" class="banner-img">
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($bannerFiles) > 1): ?>
            <div class="banner-indicators">
                <?php for ($i = 0; $i < count($bannerFiles); $i++): ?>
                <div class="banner-dot <?= $i === 0 ? 'active' : '' ?>" data-slide="<?= $i ?>"></div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- JACKPOT SECTION -->
    <div class="jackpot-section">
        <div class="jackpot-img-wrapper">
            <img src="/public/img/jackpot-bg.png" alt="Jackpot" class="jackpot-banner-img">
            <div class="jackpot-value-overlay">
                <div class="jackpot-number" id="jackpotValue">48.766.428,10</div>
            </div>
        </div>
    </div>

    <!-- CATEGORY ICONS BAR -->
    <div class="category-icons-bar">
        <div class="category-icons-scroll">
            <?php foreach ($categories as $cat): ?>
                <a href="/?gameCategoryId=<?= $cat['id'] ?>" 
                   class="category-icon-item <?= $categoryId === $cat['id'] ? 'active' : '' ?>">
                    <div class="category-icon-emoji"><img src="<?= $cat['icon'] ?>" alt="" class="cat-icon-img"></div>
                    <span><?= $cat['name'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="category-icon-item search-icon" onclick="document.getElementById('searchModal').classList.add('active');document.body.style.overflow='hidden';">
            <div class="category-icon-emoji"><img src="/public/img/icons/search.png" alt="" class="cat-icon-img" style="width:20px;height:20px;filter:brightness(0.7);"></div>
        </div>
    </div>

    <!-- GAMES SECTION -->
    <div class="games-section-v2">
        <div class="games-section-header-v2">
            <span class="games-fire-icon"><img src="/public/img/icons/fire.png" alt="" style="width:18px;height:18px;"></span>
            <span class="games-section-title-v2">
                <?php
                $titles = [t('popular'), t('slots'), t('pragmatic'), t('fishing'), t('microgaming')];
                echo $titles[$categoryId] ?? t('popular');
                ?>
            </span>
        </div>
        <div class="games-grid-v2" id="gamesGrid">
            <?php foreach ($gamesList as $game): ?>
                <div class="game-card-v2" data-name="<?= htmlspecialchars(strtolower($game['name'])) ?>"
                     onclick="<?= $user ? "launchGame('" . htmlspecialchars($game['name']) . "')" : "openModal('loginModal')" ?>">
                    <?php
                    $localSvg = '/public/games/' . $game['image'];
                    $localSvgReal = __DIR__ . $localSvg;
                    $imgUrl = $game['image_url'] ?? '';
                    ?>
                    <?php if (file_exists($localSvgReal)): ?>
                        <img src="<?= $localSvg ?>" alt="<?= htmlspecialchars($game['name']) ?>" loading="lazy">
                    <?php elseif (!empty($imgUrl)): ?>
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($game['name']) ?>" loading="lazy"
                             onerror="this.onerror=null;this.src='';this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="game-card-placeholder" style="background:linear-gradient(135deg,#1a0a2e,#2a1a4e);display:none;">
                            <span style="font-size:0.7rem;color:rgba(255,255,255,0.6);text-align:center;padding:8px;"><?= htmlspecialchars($game['name']) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="game-card-placeholder" style="background:linear-gradient(135deg,#1a0a2e,#2a1a4e);">
                            <span style="font-size:0.7rem;color:rgba(255,255,255,0.6);text-align:center;padding:8px;"><?= htmlspecialchars($game['name']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($game['provider'] === 'PG Soft'): ?>
                        <div class="game-badge-pg">PG</div>
                    <?php endif; ?>
                    <div class="game-card-overlay-v2">
                        <div class="game-play-btn-v2"><i class="fa-solid fa-play"></i></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUPPORTO TELEGRAM -->
    <div class="support-card">
        <div class="support-card-icon"><i class="fa-brands fa-telegram"></i></div>
        <div class="support-card-text">
            <div class="support-card-title"><?= t('support') ?></div>
            <div class="support-card-desc"><?= t('support_desc') ?></div>
        </div>
    </div>

    <!-- PROMOÇÃO SECTION -->
    <div class="promo-section">
        <div class="promo-title"><?= t('promotions_section') ?></div>
        <div class="promo-grid">
            <a href="/promotions.php" class="promo-card" style="background:linear-gradient(135deg,#2a1a4e,#1a0a2e);">
                <span class="promo-card-label" style="color:#ca50f3;"><?= t('transactions') ?></span>
                <span class="promo-card-emoji">💰</span>
            </a>
            <a href="/promotions.php" class="promo-card" style="background:linear-gradient(135deg,#4e1a1a,#2e0a0a);">
                <span class="promo-card-label" style="color:#f45c4e;"><?= t('affiliate') ?></span>
                <span class="promo-card-emoji">🎁</span>
            </a>
            <a href="/promotions.php" class="promo-card" style="background:linear-gradient(135deg,#1a3a1a,#0a2a0a);">
                <span class="promo-card-label" style="color:#1db954;"><?= t('mission') ?></span>
                <span class="promo-card-emoji">👑</span>
            </a>
            <a href="/promotions.php" class="promo-card" style="background:linear-gradient(135deg,#3a2a0a,#2a1a00);">
                <span class="promo-card-label" style="color:#ffaa09;"><?= t('vip') ?></span>
                <span class="promo-card-emoji">💎</span>
            </a>
            <a href="/promotions.php" class="promo-card" style="background:linear-gradient(135deg,#1a1a3a,#0a0a2a);">
                <span class="promo-card-label" style="color:#3e95fe;"><?= t('events') ?></span>
                <span class="promo-card-emoji">💍</span>
            </a>
        </div>
    </div>

    <!-- SIDEBAR MENU ITEMS (Mobile visible) -->
    <div class="mobile-menu-section">
        <a href="#" class="mobile-menu-item">
            <i class="fa-solid fa-headset"></i>
            <span><?= t('support') ?></span>
        </a>
        <a href="/promotions.php" class="mobile-menu-item">
            <i class="fa-solid fa-gift"></i>
            <span><?= t('promotions') ?></span>
        </a>
        <a href="#" class="mobile-menu-item">
            <i class="fa-solid fa-user-plus"></i>
            <span><?= t('refer_friend') ?></span>
        </a>
    </div>

    <!-- FOOTER -->
    <footer class="footer-v2">
        <div class="footer-logo-v2"><img src="/public/img/logo.png" alt="<?= SITE_NAME ?>" style="height:50px;margin:0 auto;"></div>
        <div class="footer-text-v2">Plataforma de entretenimento. Jogue com responsabilidade. +18</div>
        <div class="footer-copyright-v2">&copy; <?= date('Y') ?> <?= SITE_NAME ?></div>
    </footer>

</main>

<!-- BOTTOM NAVIGATION - 5 itens como mangafogo -->
<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item active">
        <img src="/public/img/icons/home.png" alt="" class="bnav-icon">
        <span><?= t('home') ?></span>
    </a>
    <a href="<?= $user ? '/auth.php?action=logout' : '#' ?>" class="bnav-item" <?= $user ? '' : "onclick=\"openModal('loginModal')\"" ?>>
        <img src="/public/img/icons/enter.png" alt="" class="bnav-icon">
        <span><?= t('disconnect') ?></span>
    </a>
    <a href="<?= $user ? '/deposit.php' : '#' ?>" class="bnav-item" <?= $user ? '' : "onclick=\"openModal('loginModal')\"" ?>>
        <i class="fa-solid fa-wallet"></i>
        <span><?= t('deposit') ?></span>
    </a>
    <a href="/promotions.php" class="bnav-item">
        <i class="fa-solid fa-gift"></i>
        <span><?= t('promotions') ?></span>
    </a>
    <a href="<?= $user ? '/profile.php' : '#' ?>" class="bnav-item" <?= $user ? '' : "onclick=\"openModal('loginModal')\"" ?>>
        <img src="/public/img/icons/profile.png" alt="" class="bnav-icon">
        <span><?= t('profile') ?></span>
    </a>
</nav>

<!-- SEARCH MODAL -->
<div class="modal-overlay" id="searchModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><?= t('search') ?></div>
            <div class="modal-close" onclick="closeModal('searchModal')"><i class="fa-solid fa-xmark"></i></div>
        </div>
        <div class="modal-body">
            <div class="search-bar-modal">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="<?= t('search') ?>..." id="searchInput" onkeyup="filterGames()">
            </div>
        </div>
    </div>
</div>

<!-- LOGIN MODAL -->
<div class="modal-overlay" id="loginModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><?= t('login') ?></div>
            <div class="modal-close" onclick="closeModal('loginModal')"><i class="fa-solid fa-xmark"></i></div>
        </div>
        <div class="modal-body">
            <form action="/auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label><?= t('username') ?></label>
                    <input type="text" name="username" class="form-control" placeholder="<?= t('username') ?>" required>
                </div>
                <div class="form-group">
                    <label><?= t('password') ?></label>
                    <input type="password" name="password" class="form-control" placeholder="<?= t('password') ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-login"><i class="fa-solid fa-right-to-bracket"></i> <?= t('login') ?></button>
                </div>
                <div class="form-link"><?= t('no_account') ?> <a href="#" onclick="switchModal('loginModal','registerModal')"><?= t('register') ?></a></div>
            </form>
        </div>
    </div>
</div>

<!-- REGISTER MODAL -->
<div class="modal-overlay" id="registerModal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title"><?= t('create_account') ?></div>
            <div class="modal-close" onclick="closeModal('registerModal')"><i class="fa-solid fa-xmark"></i></div>
        </div>
        <div class="modal-body">
            <form action="/auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label><?= t('username') ?></label>
                    <input type="text" name="username" class="form-control" placeholder="<?= t('username') ?>" required>
                </div>
                <div class="form-group">
                    <label><?= t('email') ?></label>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com">
                </div>
                <div class="form-group">
                    <label><?= t('phone') ?></label>
                    <input type="text" name="phone" class="form-control" placeholder="(00) 00000-0000" oninput="phoneMask(this)">
                </div>
                <div class="form-group">
                    <label><?= t('password') ?></label>
                    <input type="password" name="password" class="form-control" placeholder="<?= t('password') ?>" required>
                </div>
                <div class="form-group">
                    <label><?= t('confirm_password') ?></label>
                    <input type="password" name="password_confirm" class="form-control" placeholder="<?= t('confirm_password') ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-login"><i class="fa-solid fa-user-plus"></i> <?= t('create_account') ?></button>
                </div>
                <div class="form-link"><?= t('already_have_account') ?> <a href="#" onclick="switchModal('registerModal','loginModal')"><?= t('login') ?></a></div>
            </form>
        </div>
    </div>
</div>

<!-- GAME LAUNCH MODAL (loading screen) -->
<div class="modal-overlay" id="gameLaunchModal">
    <div style="text-align:center;padding:40px;">
        <div style="font-size:0.95rem;color:var(--text-secondary);margin-bottom:30px;"><?= t('loading_games') ?></div>
        <div class="loading-spinner"></div>
        <div style="margin-top:40px;">
            <a href="/" style="color:var(--text-muted);font-size:0.85rem;"><?= t('back_home') ?></a>
        </div>
    </div>
</div>

<script src="/assets/js/app.js"></script>

<?php if (isset($_GET['msg'])): ?>
<script>
    showToast('<?= htmlspecialchars($_GET['msg']) ?>', '<?= isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'success' ?>');
</script>
<?php endif; ?>

</body>
</html>
