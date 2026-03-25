<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="/" class="sidebar-logo"><?= SITE_NAME ?></a>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-section-title">Menu Principal</div>
        <a href="/?gameCategoryId=0" class="sidebar-item <?= (!isset($categoryId) || $categoryId === 0) ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            <span>Início</span>
        </a>
        <a href="/?gameCategoryId=0" class="sidebar-item">
            <i class="fa-solid fa-fire"></i>
            <span>Populares</span>
            <span class="badge-hot">HOT</span>
        </a>
        <a href="/?gameCategoryId=2" class="sidebar-item">
            <i class="fa-solid fa-bolt"></i>
            <span>Novos Jogos</span>
            <span class="badge-new">NEW</span>
        </a>
        <a href="/?gameCategoryId=3" class="sidebar-item">
            <i class="fa-solid fa-video"></i>
            <span>Cassino ao Vivo</span>
        </a>
        
        <div class="sidebar-divider"></div>
        <div class="sidebar-section-title">Categorias</div>
        
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-dice"></i>
            <span>Slots</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-table-cells"></i>
            <span>Mesa</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-spade"></i>
            <span>Cartas</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-futbol"></i>
            <span>Esportes</span>
        </a>
        
        <div class="sidebar-divider"></div>
        <div class="sidebar-section-title">Suporte</div>
        
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-headset"></i>
            <span>Suporte 24h</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-brands fa-telegram"></i>
            <span>Telegram</span>
        </a>
        <a href="#" class="sidebar-item">
            <i class="fa-solid fa-circle-question"></i>
            <span>FAQ</span>
        </a>
    </nav>
    <div class="sidebar-social">
        <a href="#"><i class="fa-brands fa-telegram"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-youtube"></i></a>
    </div>
</aside>
