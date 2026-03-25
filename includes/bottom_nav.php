<nav class="bottom-nav">
    <a href="/" class="bottom-nav-item <?= (!isset($currentPage) || $currentPage === 'home') ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
        <span>Início</span>
    </a>
    <a href="/?gameCategoryId=1" class="bottom-nav-item">
        <i class="fa-solid fa-fire"></i>
        <span>Popular</span>
    </a>
    <div class="bottom-nav-item bottom-nav-center">
        <div class="bottom-nav-center-btn" onclick="<?= $user ? "location.href='/deposit.php'" : "openModal('loginModal')" ?>">
            <i class="fa-solid fa-plus"></i>
        </div>
    </div>
    <a href="#" class="bottom-nav-item">
        <i class="fa-solid fa-gift"></i>
        <span>Bônus</span>
    </a>
    <a href="<?= $user ? '/profile.php' : '#' ?>" class="bottom-nav-item <?= (isset($currentPage) && $currentPage === 'profile') ? 'active' : '' ?>">
        <i class="fa-solid fa-user"></i>
        <span>Perfil</span>
    </a>
</nav>
