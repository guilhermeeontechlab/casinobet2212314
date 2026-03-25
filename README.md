# GuetoJogo - Casa de Apostas

Plataforma de entretenimento online com visual estilo casa de apostas chinesa.

## Como Rodar

```bash
C:\xampp\php\php.exe -S localhost:8080
```

Acesse: [http://localhost:8080](http://localhost:8080)

## Estrutura

| Arquivo | Descrição |
|---------|-----------|
| `index.php` | Página principal com jogos, banners, categorias |
| `auth.php` | Login, registro e logout |
| `deposit.php` | Página de depósito |
| `withdraw.php` | Página de saque |
| `profile.php` | Perfil do usuário |
| `config.php` | Configuração, banco SQLite via PDO |
| `includes/` | Componentes reutilizáveis (navbar, sidebar, footer, bottom nav) |
| `assets/css/` | CSS completo com tema escuro |
| `assets/js/` | JavaScript (carousel, modais, animações) |
| `public/games/` | SVGs dos jogos |
| `database/` | Banco SQLite (criado automaticamente) |

## Funcionalidades

- Tema escuro estilo cassino chinês (preto/vermelho/dourado)
- Carousel de banners com autoplay e setas
- Grid de 30 jogos com SVGs temáticos
- Sistema de login/registro com modais
- Depósito e saque (simulação)
- Sidebar navegável + bottom navigation mobile
- Busca de jogos em tempo real
- Filtro por categorias e provedores
- Jackpot animado com contagem ao vivo
- Ticker de ganhadores com animação
- Contagem de jogadores online
- Marquee de notícias
- Perfil do usuário com saldo
- 100% responsivo (desktop + mobile)

## Imagens dos Jogos

Os jogos usam SVGs placeholder. Para substituir por imagens reais:
1. Adicione as imagens em `public/games/` com o nome correspondente (`.png` ou `.svg`)
2. Os nomes seguem o padrão: `fortune-tiger.png`, `dragon-hatch.png`, etc.
