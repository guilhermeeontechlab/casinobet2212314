// ============================================
// GUETOJOGO - V3 - Main JavaScript
// ============================================

// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const menuToggle = document.getElementById('menuToggle');

if (menuToggle) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    });
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
}

// User dropdown
const userAvatarBtn = document.getElementById('userAvatarBtn');
const userDropdown = document.getElementById('userDropdown');

if (userAvatarBtn) {
    userAvatarBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('active');
    });
    document.addEventListener('click', (e) => {
        if (userDropdown && !userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });
}

// Deposit dropdown
const depositDropdownBtn = document.getElementById('depositDropdownBtn');
const depositDropdown = document.getElementById('depositDropdown');

if (depositDropdownBtn) {
    depositDropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        depositDropdown.classList.toggle('active');
    });
    document.addEventListener('click', (e) => {
        if (depositDropdown && !depositDropdown.contains(e.target) && e.target !== depositDropdownBtn) {
            depositDropdown.classList.remove('active');
        }
    });
}

// Language selector dropdown
const langBtn = document.getElementById('langBtn');
const langDropdown = document.getElementById('langDropdown');

if (langBtn) {
    langBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        langDropdown.classList.toggle('active');
    });
    document.addEventListener('click', (e) => {
        if (langDropdown && !langDropdown.contains(e.target) && e.target !== langBtn) {
            langDropdown.classList.remove('active');
        }
    });
}

// Modal functions
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function switchModal(fromId, toId) {
    closeModal(fromId);
    setTimeout(() => openModal(toId), 200);
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// Search/filter games
function filterGames() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    const query = input.value.toLowerCase();
    const cards = document.querySelectorAll('.game-card-v2');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        card.style.display = name.includes(query) ? '' : 'none';
    });
}

// Game launch (loading screen)
function launchGame(gameName) {
    const modal = document.getElementById('gameLaunchModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        // Simulate loading then close after 3s
        setTimeout(() => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            showToast('Jogo ' + gameName + ' em manutenção', 'error');
        }, 3000);
    }
}

// Jackpot counter animation - like mangafogo
function animateJackpot() {
    const el = document.getElementById('jackpotValue');
    if (!el) return;
    
    let baseValue = 48766428.10;
    
    setInterval(() => {
        baseValue += (Math.random() * 80 + 10);
        el.textContent = baseValue.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }, 2500);
}
animateJackpot();

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'notification-toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 3000);
}

// Banner carousel
let currentBanner = 0;
const bannerWrapper = document.getElementById('bannerSlidesWrapper');
const bannerDots = document.querySelectorAll('.banner-dot');
const bannerSlides = document.querySelectorAll('.banner-slide-v2');
let bannerAutoInterval = null;

function goToSlide(index) {
    if (!bannerWrapper || bannerSlides.length === 0) return;
    currentBanner = ((index % bannerSlides.length) + bannerSlides.length) % bannerSlides.length;
    bannerWrapper.style.transform = 'translateX(-' + (currentBanner * 100) + '%)';
    bannerDots.forEach(d => d.classList.remove('active'));
    if (bannerDots[currentBanner]) bannerDots[currentBanner].classList.add('active');
}

function nextSlide() { goToSlide(currentBanner + 1); }
function prevSlide() { goToSlide(currentBanner - 1); }

function startBannerAuto() {
    stopBannerAuto();
    bannerAutoInterval = setInterval(nextSlide, 4000);
}

function stopBannerAuto() {
    if (bannerAutoInterval) clearInterval(bannerAutoInterval);
}

bannerDots.forEach((dot, index) => {
    dot.addEventListener('click', () => { goToSlide(index); startBannerAuto(); });
});

startBannerAuto();

// Swipe support for banner
let touchStartX = 0;
const bannerEl = document.getElementById('bannerCarousel');
if (bannerEl) {
    bannerEl.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    
    bannerEl.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) nextSlide();
            else prevSlide();
            startBannerAuto();
        }
    }, { passive: true });
}

// Phone mask
function phoneMask(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    
    if (value.length > 6) {
        value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
    } else if (value.length > 2) {
        value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
    } else if (value.length > 0) {
        value = '(' + value;
    }
    input.value = value;
}

// Deposit amount buttons (page)
document.querySelectorAll('.deposit-amount-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.deposit-amount-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const input = document.getElementById('depositAmount');
        if (input) input.value = btn.getAttribute('data-amount');
    });
});
