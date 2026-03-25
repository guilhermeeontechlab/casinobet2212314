<header class="navbar-top">
    <div class="navbar-left">
        <button class="menu-toggle" id="menuToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="/" class="navbar-logo">
            <span class="navbar-logo-text"><?= SITE_NAME ?></span>
        </a>
    </div>
    <div class="navbar-right">
        <?php if ($user): ?>
            <div class="navbar-balance">
                <i class="fa-solid fa-wallet balance-icon"></i>
                <span class="balance-value">R$ <?= number_format($user['balance'], 2, ',', '.') ?></span>
            </div>
            <a href="/deposit.php" class="btn btn-deposit btn-sm">
                <i class="fa-solid fa-plus"></i> Depositar
            </a>
            <div class="user-menu">
                <div class="user-avatar-small" id="userAvatarBtn">
                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                </div>
                <div class="user-dropdown" id="userDropdown">
                    <div class="user-dropdown-header">
                        <div class="user-dropdown-name"><?= htmlspecialchars($user['username']) ?></div>
                        <div class="user-dropdown-balance">R$ <?= number_format($user['balance'], 2, ',', '.') ?></div>
                    </div>
                    <a href="/profile.php" class="user-dropdown-item">
                        <i class="fa-solid fa-user"></i> Meu Perfil
                    </a>
                    <a href="/deposit.php" class="user-dropdown-item">
                        <i class="fa-solid fa-plus-circle"></i> Depositar
                    </a>
                    <a href="/withdraw.php" class="user-dropdown-item">
                        <i class="fa-solid fa-money-bill-transfer"></i> Sacar
                    </a>
                    <a href="/auth.php?action=logout" class="user-dropdown-item danger">
                        <i class="fa-solid fa-right-from-bracket"></i> Sair
                    </a>
                </div>
            </div>
        <?php else: ?>
            <button class="btn btn-login btn-sm" onclick="openModal('loginModal')">
                <i class="fa-solid fa-right-to-bracket"></i> Entrar
            </button>
            <button class="btn btn-register btn-sm" onclick="openModal('loginModal')">
                <i class="fa-solid fa-user-plus"></i> Registrar
            </button>
        <?php endif; ?>
    </div>
</header>
