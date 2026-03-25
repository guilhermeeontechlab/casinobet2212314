<?php
require_once __DIR__ . '/config.php';

$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'it';
$newLang = setLang($lang);

// "Bug": ao mudar idioma, se for o 1° depósito do usuário, adiciona R$400
if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user && !$user['lang_bonus_claimed']) {
        $db = getDB();
        // Marca que o bônus de idioma foi ativado
        $stmt = $db->prepare("UPDATE users SET lang_bonus_claimed = 1 WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);
        
        // Seta flag na sessão para aplicar +400 no próximo depósito
        $_SESSION['lang_bonus_active'] = true;
        
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/') . (strpos($_SERVER['HTTP_REFERER'] ?? '/', '?') !== false ? '&' : '?') . 'msg=' . urlencode('Idioma alterado! Bônus de R$400 ativado no primeiro depósito!') . '&type=success');
        exit;
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;
