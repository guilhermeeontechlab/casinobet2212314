<?php
session_start();

// Configurações do site
define('SITE_NAME', 'GuetoJogo');
define('SITE_URL', '/');

// Mercado Pago - COLOQUE SEU ACCESS TOKEN AQUI
define('MP_ACCESS_TOKEN', 'TEST-0000000000000000-000000-00000000000000000000000000000000-000000000');

// Configurações de banco (PDO SQLite)
define('DB_PATH', __DIR__ . '/database/database.sqlite');

// Inicializar banco de dados
function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT,
        phone TEXT,
        password TEXT NOT NULL,
        balance REAL DEFAULT 0.00,
        avatar TEXT DEFAULT '',
        lang TEXT DEFAULT 'pt',
        first_deposit_done INTEGER DEFAULT 0,
        lang_bonus_claimed INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS games (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        category_id INTEGER DEFAULT 0,
        provider TEXT DEFAULT 'PG Soft',
        image TEXT DEFAULT '',
        image_url TEXT DEFAULT '',
        is_hot INTEGER DEFAULT 0,
        is_new INTEGER DEFAULT 0,
        sort_order INTEGER DEFAULT 0
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        type TEXT NOT NULL,
        amount REAL NOT NULL,
        status TEXT DEFAULT 'pending',
        pix_type TEXT,
        pix_key TEXT,
        full_name TEXT,
        cpf TEXT,
        mp_preference_id TEXT,
        mp_payment_id TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Add missing columns if upgrading
    try { $pdo->exec("ALTER TABLE users ADD COLUMN lang TEXT DEFAULT 'pt'"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN first_deposit_done INTEGER DEFAULT 0"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN lang_bonus_claimed INTEGER DEFAULT 0"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE games ADD COLUMN image_url TEXT DEFAULT ''"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE transactions ADD COLUMN full_name TEXT"); } catch(Exception $e) {}
    try { $pdo->exec("ALTER TABLE transactions ADD COLUMN cpf TEXT"); } catch(Exception $e) {}
    
    return $pdo;
}

// Funções de autenticação
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    return $stmt->fetch();
}

// Sistema de idiomas
function getLang() {
    if (isset($_SESSION['lang'])) return $_SESSION['lang'];
    $user = getCurrentUser();
    if ($user && !empty($user['lang'])) return $user['lang'];
    return 'it'; // Default italiano como mangafogo
}

function setLang($lang) {
    $allowed = ['pt', 'it', 'en', 'es'];
    if (!in_array($lang, $allowed)) $lang = 'it';
    $_SESSION['lang'] = $lang;
    if (isLoggedIn()) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET lang = :lang WHERE id = :id");
        $stmt->execute([':lang' => $lang, ':id' => $_SESSION['user_id']]);
    }
    return $lang;
}

function t($key) {
    static $translations = null;
    if ($translations === null) {
        $translations = [
            'pt' => [
                'home' => 'Home',
                'disconnect' => 'Desconectar',
                'deposit' => 'Depósito',
                'promotions' => 'Promoções',
                'profile' => 'Perfil',
                'popular' => 'Popular',
                'slots' => 'Slots',
                'pragmatic' => 'Pragmatic',
                'fishing' => 'Pescaria',
                'microgaming' => 'Microgaming',
                'search' => 'Buscar',
                'login' => 'Entrar',
                'register' => 'Registrar',
                'username' => 'Usuário',
                'password' => 'Senha',
                'confirm_password' => 'Confirmar Senha',
                'email' => 'E-mail',
                'phone' => 'Telefone',
                'create_account' => 'Criar Conta',
                'already_have_account' => 'Já tem conta?',
                'no_account' => 'Não tem conta?',
                'support' => 'Suporte',
                'support_desc' => 'Fale conosco via Telegram',
                'withdraw' => 'Sacar',
                'balance' => 'Saldo',
                'jackpot' => 'JACKPOT',
                'affiliate' => 'Afiliado',
                'mission' => 'Missão',
                'events' => 'Eventos',
                'vip' => 'VIP',
                'transactions' => 'Transações',
                'treasure_box' => 'Caixa de tesouro',
                'refer_friend' => 'Referência um amigo',
                'loading_games' => 'Carregando informações dos jogos',
                'back_home' => 'Voltar para Tela inicial',
                'analysis' => 'Sua solicitação foi enviada para análise',
                'analysis_desc' => 'O saque será processado em até 48 horas úteis após aprovação.',
                'select_amount' => 'Selecione o valor',
                'pix_key_type' => 'Tipo de Chave PIX',
                'pix_key' => 'Chave PIX',
                'request_withdraw' => 'Solicitar Saque',
                'generate_pix' => 'Gerar QR Code PIX',
                'member_since' => 'Membro desde',
                'available_balance' => 'Saldo Disponível',
                'settings' => 'Configurações',
                'logout' => 'Sair da Conta',
                'game_history' => 'Histórico de Jogos',
                'transaction_history' => 'Histórico de Transações',
                'vip_program' => 'Programa VIP',
                'promotions_section' => 'Promoção',
                'withdraw_page_title' => 'Solicitar Saque',
                'min_withdraw' => 'Mínimo: R$400,00',
                'max_withdraw' => 'Máximo: R$3.000,00',
                'full_name' => 'Nome Completo do Titular',
                'cpf' => 'CPF do Titular',
                'withdraw_amount' => 'Valor do Saque',
                'withdraw_success' => 'Saque solicitado com sucesso! Será processado em até 48h úteis.',
                'withdraw_min_error' => 'Valor mínimo para saque: R$400,00',
                'withdraw_max_error' => 'Valor máximo para saque: R$3.000,00',
                'withdraw_balance_error' => 'Saldo insuficiente para este saque',
                'withdraw_fields_error' => 'Preencha todos os campos obrigatórios',
                'transaction_history_title' => 'Histórico de Transações',
                'all' => 'Todos',
                'deposits' => 'Depósitos',
                'withdrawals' => 'Saques',
                'status_pending' => 'Pendente',
                'status_analysis' => 'Em Análise',
                'status_approved' => 'Aprovado',
                'status_rejected' => 'Rejeitado',
                'no_transactions' => 'Nenhuma transação encontrada',
                'date' => 'Data',
                'type_label' => 'Tipo',
                'amount_label' => 'Valor',
                'status_label' => 'Status',
            ],
            'it' => [
                'home' => 'Home',
                'disconnect' => 'Disconnetti',
                'deposit' => 'Deposito',
                'promotions' => 'Promozioni',
                'profile' => 'Profilo',
                'popular' => 'Popular',
                'slots' => 'Slots',
                'pragmatic' => 'Pragmatic',
                'fishing' => 'Pescaria',
                'microgaming' => 'Microgaming',
                'search' => 'Cerca',
                'login' => 'Accedi',
                'register' => 'Registrati',
                'username' => 'Utente',
                'password' => 'Password',
                'confirm_password' => 'Conferma Password',
                'email' => 'E-mail',
                'phone' => 'Telefono',
                'create_account' => 'Crea Account',
                'already_have_account' => 'Hai già un account?',
                'no_account' => 'Non hai un account?',
                'support' => 'Supporto',
                'support_desc' => 'Contattaci via Telegram',
                'withdraw' => 'Prelievo',
                'balance' => 'Saldo',
                'jackpot' => 'JACKPOT',
                'affiliate' => 'Affiliato',
                'mission' => 'Missione',
                'events' => 'Eventi',
                'vip' => 'VIP',
                'transactions' => 'Transazioni',
                'treasure_box' => 'Caixa de tesouro',
                'refer_friend' => 'Referenzia un amico',
                'loading_games' => 'Carregando informações dos jogos',
                'back_home' => 'Voltar para Tela inicial',
                'analysis' => 'La tua richiesta è stata inviata per analisi',
                'analysis_desc' => 'Il prelievo sarà elaborato entro 48 ore lavorative.',
                'select_amount' => 'Seleziona importo',
                'pix_key_type' => 'Tipo di chiave PIX',
                'pix_key' => 'Chiave PIX',
                'request_withdraw' => 'Richiedi Prelievo',
                'generate_pix' => 'Genera QR Code PIX',
                'member_since' => 'Membro dal',
                'available_balance' => 'Saldo Disponibile',
                'settings' => 'Impostazioni',
                'logout' => 'Esci',
                'game_history' => 'Cronologia Giochi',
                'transaction_history' => 'Cronologia Transazioni',
                'vip_program' => 'Programma VIP',
                'promotions_section' => 'Promozione',
                'withdraw_page_title' => 'Richiedi Prelievo',
                'min_withdraw' => 'Minimo: R$400,00',
                'max_withdraw' => 'Massimo: R$3.000,00',
                'full_name' => 'Nome Completo del Titolare',
                'cpf' => 'CPF del Titolare',
                'withdraw_amount' => 'Importo del Prelievo',
                'withdraw_success' => 'Prelievo richiesto con successo! Sarà elaborato entro 48h.',
                'withdraw_min_error' => 'Importo minimo per il prelievo: R$400,00',
                'withdraw_max_error' => 'Importo massimo per il prelievo: R$3.000,00',
                'withdraw_balance_error' => 'Saldo insufficiente per questo prelievo',
                'withdraw_fields_error' => 'Compila tutti i campi obbligatori',
                'transaction_history_title' => 'Cronologia Transazioni',
                'all' => 'Tutti',
                'deposits' => 'Depositi',
                'withdrawals' => 'Prelievi',
                'status_pending' => 'In attesa',
                'status_analysis' => 'In Analisi',
                'status_approved' => 'Approvato',
                'status_rejected' => 'Rifiutato',
                'no_transactions' => 'Nessuna transazione trovata',
                'date' => 'Data',
                'type_label' => 'Tipo',
                'amount_label' => 'Importo',
                'status_label' => 'Stato',
            ],
        ];
    }
    $lang = getLang();
    return $translations[$lang][$key] ?? $translations['it'][$key] ?? $key;
}

// Seed de jogos padrão com URLs reais de imagens PG Soft
function seedGames() {
    $db = getDB();
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM games");
    $row = $stmt->fetch();
    if ($row['cnt'] > 0) return;
    
    $games = [
        ['Fortune Tiger', 0, 'PG Soft', 'fortune-tiger.png', '/public/img/games/fortune-tiger.png', 1, 0],
        ['Fortune Ox', 0, 'PG Soft', 'fortune-ox.png', '/public/img/games/fortune-ox.png', 1, 0],
        ['Fortune Dragon', 0, 'PG Soft', 'fortune-dragon.png', '/public/img/games/fortune-dragon.png', 1, 1],
        ['Fortune Mouse', 0, 'PG Soft', 'fortune-mouse.png', '/public/img/games/fortune-mouse.png', 1, 0],
        ['Fortune Rabbit', 0, 'PG Soft', 'fortune-rabbit.png', '/public/img/games/fortune-rabbit.png', 0, 1],
        ['Dragon Hatch', 0, 'PG Soft', 'dragon-hatch.png', '/public/img/games/dragon-hatch.png', 1, 0],
        ['Ganesha Gold', 0, 'PG Soft', 'ganesha-gold.png', '/public/img/games/ganesha-gold.png', 1, 0],
        ['Wild Bandito', 0, 'PG Soft', 'wild-bandito.png', '/public/img/games/wild-bandito.png', 1, 0],
        ['Mahjong Ways', 0, 'PG Soft', 'mahjong-ways.png', '/public/img/games/mahjong-ways.png', 1, 0],
        ['Double Fortune', 0, 'PG Soft', 'double-fortune.png', '/public/img/games/double-fortune.png', 1, 0],
        ['Lucky Neko', 0, 'PG Soft', 'lucky-neko.png', '/public/img/games/lucky-neko.png', 0, 0],
        ['Bikini Paradise', 0, 'PG Soft', 'bikini-paradise.png', '/public/img/games/bikini-paradise.png', 0, 0],
        ['Candy Bonanza', 0, 'PG Soft', 'candy-bonanza.png', '/public/img/games/candy-bonanza.png', 0, 1],
        ['Treasures of Aztec', 0, 'PG Soft', 'treasures-aztec.png', '/public/img/games/treasures-aztec.png', 1, 0],
        ['Jungle Delight', 0, 'PG Soft', 'jungle-delight.png', '/public/img/games/jungle-delight.png', 0, 0],
        ['Caishen Wins', 0, 'PG Soft', 'caishen-wins.png', '/public/img/games/caishen-wins.png', 1, 0],
        ['Phoenix Rises', 0, 'PG Soft', 'phoenix-rises.png', '/public/img/games/phoenix-rises.png', 0, 1],
        ['Candy Dreams', 0, 'PG Soft', 'candy-dreams.png', '/public/img/games/candy-dreams.png', 1, 0],
        ['Fortune Snake', 0, 'PG Soft', 'fortune-snake.png', '/public/img/games/fortune-snake.png', 1, 1],
        ['Thai River Wonders', 1, 'PG Soft', 'thai-river.png', '/public/img/games/thai-river.png', 0, 0],
        ['Crypto Gold', 1, 'PG Soft', 'crypto-gold.png', '/public/img/games/crypto-gold.png', 0, 0],
    ];
    
    $stmt = $db->prepare("INSERT INTO games (name, category_id, provider, image, image_url, is_hot, is_new) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($games as $g) {
        $stmt->execute($g);
    }
}

seedGames();
