<?php
require_once __DIR__ . '/config.php';
$user = getCurrentUser();
$lang = getLang();
$activeTab = $_GET['tab'] ?? 'treasure';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title><?= t('promotions') ?> - <?= SITE_NAME ?></title>
    <link rel="icon" type="image/svg+xml" href="/public/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .promo-tabs {
            display: flex; overflow-x: auto; -webkit-overflow-scrolling: touch;
            scrollbar-width: none; gap: 0; border-bottom: 1px solid #1a1a1a;
            padding: 0 10px;
        }
        .promo-tabs::-webkit-scrollbar { display: none; }
        .promo-tab {
            flex-shrink: 0; padding: 10px 14px; font-size: 0.75rem; font-weight: 600;
            color: var(--text-muted); white-space: nowrap; cursor: pointer;
            border-bottom: 2px solid transparent; transition: all 0.2s;
        }
        .promo-tab.active { color: var(--color-gold); border-bottom-color: var(--color-gold); }
        .promo-tab:hover { color: var(--text-secondary); }
        .promo-content { padding: 16px 12px; }
        .promo-card-big {
            background: var(--bg-card); border-radius: 10px; padding: 18px;
            margin-bottom: 12px; border: 1px solid #1a1a1a;
        }
        .promo-card-big-title {
            font-size: 0.95rem; font-weight: 700; margin-bottom: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .promo-card-big-desc { font-size: 0.8rem; color: var(--text-secondary); line-height: 1.5; }
        .promo-card-big-btn {
            display: inline-block; margin-top: 10px; padding: 8px 16px;
            background: var(--color-red); color: #fff; border-radius: 6px;
            font-size: 0.75rem; font-weight: 700; cursor: pointer;
        }
        .referral-box {
            background: #0a0a0a; border: 1px solid #222; border-radius: 8px;
            padding: 14px; margin-top: 12px;
        }
        .referral-url {
            background: var(--bg-input); border: 1px solid #222; border-radius: 6px;
            padding: 10px; font-size: 0.7rem; color: var(--text-secondary);
            word-break: break-all; margin: 8px 0; display: flex;
            align-items: center; justify-content: space-between; gap: 8px;
        }
        .referral-url span { flex: 1; }
        .copy-btn {
            background: var(--color-green-dark); color: #fff; border: none;
            padding: 4px 10px; border-radius: 4px; font-size: 0.65rem;
            font-weight: 700; cursor: pointer;
        }
        .referral-stats { display: flex; gap: 12px; margin-top: 10px; }
        .referral-stat { font-size: 0.75rem; color: var(--text-secondary); }
        .referral-stat strong { color: var(--color-gold); }
        .share-icons { display: flex; gap: 12px; margin-top: 14px; justify-content: center; }
        .share-icon {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #fff; cursor: pointer;
        }
        .treasure-boxes { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 12px; }
        .treasure-box-item {
            aspect-ratio: 1; background: linear-gradient(135deg, #1a0a00, #2a1200);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; border: 1px solid rgba(255,170,9,0.15); cursor: pointer;
            transition: transform 0.2s;
        }
        .treasure-box-item:hover { transform: scale(1.05); }
        .mission-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px; background: var(--bg-card); border-radius: 8px;
            margin-bottom: 8px; border: 1px solid #1a1a1a;
        }
        .mission-icon {
            width: 40px; height: 40px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .mission-info { flex: 1; }
        .mission-name { font-size: 0.85rem; font-weight: 600; margin-bottom: 2px; }
        .mission-desc { font-size: 0.7rem; color: var(--text-secondary); }
        .mission-reward { font-size: 0.75rem; font-weight: 700; color: var(--color-gold); }
        .progress-bar { height: 4px; background: #222; border-radius: 2px; margin-top: 6px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--color-gold); border-radius: 2px; }
    </style>
</head>
<body>

<header class="navbar-top">
    <div class="navbar-left">
        <a href="/" style="color:#fff;font-size:1rem;"><i class="fa-solid fa-arrow-left"></i></a>
    </div>
    <div class="navbar-center"><span style="font-weight:700;font-size:1rem;"><?= t('promotions') ?></span></div>
    <div class="navbar-right"></div>
</header>
<div class="red-separator"></div>

<div style="margin-top:var(--navbar-height);">
    <!-- TABS like mangafogo -->
    <div class="promo-tabs">
        <a href="/promotions.php?tab=treasure" class="promo-tab <?= $activeTab === 'treasure' ? 'active' : '' ?>"><?= t('treasure_box') ?></a>
        <a href="/promotions.php?tab=mission" class="promo-tab <?= $activeTab === 'mission' ? 'active' : '' ?>"><?= t('mission') ?></a>
        <a href="/promotions.php?tab=affiliate" class="promo-tab <?= $activeTab === 'affiliate' ? 'active' : '' ?>"><?= t('affiliate') ?></a>
        <a href="/promotions.php?tab=events" class="promo-tab <?= $activeTab === 'events' ? 'active' : '' ?>"><?= t('events') ?></a>
        <a href="/promotions.php?tab=vip" class="promo-tab <?= $activeTab === 'vip' ? 'active' : '' ?>"><?= t('vip') ?></a>
    </div>

    <div class="promo-content" style="padding-bottom:calc(var(--bottom-nav-height) + 20px);">
        <?php if ($activeTab === 'treasure'): ?>
            <div class="promo-card-big">
                <div class="promo-card-big-title">🎁 <?= t('treasure_box') ?></div>
                <div class="promo-card-big-desc">
                    Abra caixas de tesouro diárias e ganhe prêmios incríveis! Cada caixa pode conter bônus de até R$ 500,00.
                </div>
            </div>
            <div class="treasure-boxes">
                <div class="treasure-box-item" onclick="openTreasure(this)">🎁</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🎁</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🎁</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🎁</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🔒</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🔒</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🔒</div>
                <div class="treasure-box-item" onclick="openTreasure(this)">🔒</div>
            </div>

        <?php elseif ($activeTab === 'mission'): ?>
            <div class="promo-card-big">
                <div class="promo-card-big-title">🎯 <?= t('mission') ?></div>
                <div class="promo-card-big-desc">Complete missões diárias para ganhar recompensas!</div>
            </div>
            <div class="mission-item">
                <div class="mission-icon" style="background:rgba(29,185,84,0.15);">💰</div>
                <div class="mission-info">
                    <div class="mission-name">Primeiro depósito do dia</div>
                    <div class="mission-desc">Deposite qualquer valor</div>
                    <div class="progress-bar"><div class="progress-fill" style="width:0%;"></div></div>
                </div>
                <div class="mission-reward">R$ 10</div>
            </div>
            <div class="mission-item">
                <div class="mission-icon" style="background:rgba(255,170,9,0.15);">🎮</div>
                <div class="mission-info">
                    <div class="mission-name">Jogue 10 partidas</div>
                    <div class="mission-desc">0/10 partidas jogadas</div>
                    <div class="progress-bar"><div class="progress-fill" style="width:0%;"></div></div>
                </div>
                <div class="mission-reward">R$ 25</div>
            </div>
            <div class="mission-item">
                <div class="mission-icon" style="background:rgba(214,0,0,0.15);">🔥</div>
                <div class="mission-info">
                    <div class="mission-name">Ganhe 5 rodadas</div>
                    <div class="mission-desc">0/5 vitórias</div>
                    <div class="progress-bar"><div class="progress-fill" style="width:0%;"></div></div>
                </div>
                <div class="mission-reward">R$ 50</div>
            </div>

        <?php elseif ($activeTab === 'affiliate'): ?>
            <div class="promo-card-big">
                <div class="promo-card-big-title">👥 <?= t('affiliate') ?></div>
                <div class="promo-card-big-desc">Convide amigos e ganhe comissões! Receba uma porcentagem de cada depósito dos seus convidados.</div>
            </div>
            <?php if ($user): ?>
            <div class="referral-box">
                <div style="font-size:0.8rem;font-weight:600;margin-bottom:6px;">URL do convite</div>
                <div class="referral-url">
                    <span id="referralUrl"><?= SITE_URL ?>?ref=<?= strtoupper(substr(md5($user['id']), 0, 8)) ?></span>
                    <button class="copy-btn" onclick="copyRef()">📋</button>
                </div>
                <div style="font-size:0.75rem;color:var(--text-secondary);">
                    Código de Convite: <strong style="color:var(--color-gold);"><?= strtoupper(substr(md5($user['id']), 0, 8)) ?></strong>
                </div>
                <div class="referral-stats">
                    <div class="referral-stat">Convidados diretos: <strong>0</strong></div>
                    <div class="referral-stat">Convidados válidos: <strong>0</strong></div>
                </div>
                <div class="share-icons">
                    <div class="share-icon" style="background:#E4405F;"><i class="fa-brands fa-instagram"></i></div>
                    <div class="share-icon" style="background:#1877F2;"><i class="fa-brands fa-facebook"></i></div>
                    <div class="share-icon" style="background:#229ED9;"><i class="fa-brands fa-telegram"></i></div>
                    <div class="share-icon" style="background:#25D366;"><i class="fa-brands fa-whatsapp"></i></div>
                </div>
            </div>
            <div style="margin-top:16px;padding:14px;background:var(--bg-card);border-radius:8px;border:1px solid #1a1a1a;">
                <div style="font-size:0.85rem;font-weight:600;margin-bottom:10px;">Qual é a quantidade efetiva de recomendação?</div>
                <div style="font-size:0.75rem;color:var(--text-secondary);line-height:1.6;">
                    (Deve corresponder às condições abaixo)
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:10px;font-size:0.75rem;color:var(--text-secondary);">
                    <span>Depósitos acumulados do convidado</span>
                    <span style="color:#fff;">13.00 ou mais</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:0.75rem;color:var(--text-secondary);">
                    <span>Apostas acumuladas do convidado</span>
                    <span style="color:#fff;">120.00 ou mais</span>
                </div>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:30px;">
                <div style="font-size:0.85rem;color:var(--text-muted);">Faça login para ver seu link de afiliado</div>
            </div>
            <?php endif; ?>

        <?php elseif ($activeTab === 'events'): ?>
            <div class="promo-card-big">
                <div class="promo-card-big-title">🎉 <?= t('events') ?></div>
                <div class="promo-card-big-desc">Eventos especiais com prêmios exclusivos! Fique atento às novidades.</div>
            </div>
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:0.85rem;">
                <div style="font-size:2rem;margin-bottom:10px;">🎊</div>
                Nenhum evento ativo no momento.<br>Volte em breve!
            </div>

        <?php elseif ($activeTab === 'vip'): ?>
            <div class="promo-card-big">
                <div class="promo-card-big-title">👑 <?= t('vip') ?></div>
                <div class="promo-card-big-desc">Programa VIP com cashback, bônus exclusivos e atendimento prioritário!</div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;">
                <div style="background:linear-gradient(135deg,#1a1a1a,#111);border:1px solid #333;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:1.3rem;margin-bottom:4px;">🥉</div>
                    <div style="font-size:0.8rem;font-weight:700;">Bronze</div>
                    <div style="font-size:0.65rem;color:var(--text-muted);margin-top:2px;">Cashback 1%</div>
                </div>
                <div style="background:linear-gradient(135deg,#1a1a2a,#111);border:1px solid #444;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:1.3rem;margin-bottom:4px;">🥈</div>
                    <div style="font-size:0.8rem;font-weight:700;">Prata</div>
                    <div style="font-size:0.65rem;color:var(--text-muted);margin-top:2px;">Cashback 3%</div>
                </div>
                <div style="background:linear-gradient(135deg,#2a1a00,#111);border:1px solid var(--color-gold);border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:1.3rem;margin-bottom:4px;">🥇</div>
                    <div style="font-size:0.8rem;font-weight:700;color:var(--color-gold);">Ouro</div>
                    <div style="font-size:0.65rem;color:var(--text-muted);margin-top:2px;">Cashback 5%</div>
                </div>
                <div style="background:linear-gradient(135deg,#1a0a2e,#111);border:1px solid #ca50f3;border-radius:8px;padding:14px;text-align:center;">
                    <div style="font-size:1.3rem;margin-bottom:4px;">💎</div>
                    <div style="font-size:0.8rem;font-weight:700;color:#ca50f3;">Diamante</div>
                    <div style="font-size:0.65rem;color:var(--text-muted);margin-top:2px;">Cashback 10%</div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<nav class="bottom-nav-v2">
    <a href="/" class="bnav-item"><i class="fa-solid fa-house"></i><span><?= t('home') ?></span></a>
    <a href="<?= $user ? '/auth.php?action=logout' : '#' ?>" class="bnav-item"><i class="fa-solid fa-right-from-bracket"></i><span><?= t('disconnect') ?></span></a>
    <a href="<?= $user ? '/deposit.php' : '#' ?>" class="bnav-item"><i class="fa-solid fa-wallet"></i><span><?= t('deposit') ?></span></a>
    <a href="/promotions.php" class="bnav-item active"><i class="fa-solid fa-gift"></i><span><?= t('promotions') ?></span></a>
    <a href="<?= $user ? '/profile.php' : '#' ?>" class="bnav-item"><i class="fa-solid fa-user"></i><span><?= t('profile') ?></span></a>
</nav>

<script src="/assets/js/app.js"></script>
<script>
function openTreasure(el) {
    <?php if (!$user): ?>
    openModal('loginModal');
    return;
    <?php endif; ?>
    el.innerHTML = '✨';
    el.style.borderColor = 'var(--color-gold)';
    el.style.background = 'linear-gradient(135deg, #2a1a00, #3a2200)';
    setTimeout(() => {
        showToast('Deposite para desbloquear caixas de tesouro!', 'error');
    }, 500);
}
function copyRef() {
    const url = document.getElementById('referralUrl');
    if (url) {
        navigator.clipboard.writeText(url.textContent).then(() => {
            showToast('Link copiado!', 'success');
        });
    }
}
</script>
</body>
</html>
