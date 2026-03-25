const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const https = require('https');
const http = require('http');

const TARGET_URL = 'https://mangafogo-pg.bet/home/game/register?code=0J4EK0C1DX';
const OUTPUT_DIR = path.join(__dirname, 'extracted-images');

async function downloadImage(url, filepath) {
    return new Promise((resolve, reject) => {
        const protocol = url.startsWith('https') ? https : http;
        protocol.get(url, { headers: { 'User-Agent': 'Mozilla/5.0' } }, (res) => {
            if (res.statusCode === 301 || res.statusCode === 302) {
                return downloadImage(res.headers.location, filepath).then(resolve).catch(reject);
            }
            if (res.statusCode !== 200) {
                return reject(new Error(`Failed to download ${url}: status ${res.statusCode}`));
            }
            const fileStream = fs.createWriteStream(filepath);
            res.pipe(fileStream);
            fileStream.on('finish', () => { fileStream.close(); resolve(); });
            fileStream.on('error', reject);
        }).on('error', reject);
    });
}

function getFilenameFromUrl(url, index) {
    try {
        const parsed = new URL(url);
        const basename = path.basename(parsed.pathname);
        if (basename && basename.includes('.')) {
            return `${index}_${basename}`;
        }
    } catch {}
    return `image_${index}.png`;
}

(async () => {
    console.log('Iniciando navegador...');
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });

    const page = await browser.newPage();
    await page.setViewport({ width: 1920, height: 1080 });
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

    // Coletar URLs de imagens carregadas via network
    const networkImages = new Set();
    page.on('response', (response) => {
        const url = response.url();
        const contentType = response.headers()['content-type'] || '';
        if (contentType.startsWith('image/') || /\.(png|jpg|jpeg|gif|svg|webp|ico|bmp)(\?|$)/i.test(url)) {
            networkImages.add(url);
        }
    });

    console.log(`Acessando: ${TARGET_URL}`);
    try {
        await page.goto(TARGET_URL, { waitUntil: 'networkidle2', timeout: 60000 });
    } catch (e) {
        console.log('Aviso: timeout ao carregar, continuando mesmo assim...');
    }

    // Esperar mais um pouco para conteúdo dinâmico
    console.log('Aguardando conteúdo dinâmico...');
    await new Promise(r => setTimeout(r, 5000));

    // Scroll para carregar lazy images
    await page.evaluate(async () => {
        await new Promise((resolve) => {
            let totalHeight = 0;
            const distance = 300;
            const timer = setInterval(() => {
                window.scrollBy(0, distance);
                totalHeight += distance;
                if (totalHeight >= document.body.scrollHeight) {
                    clearInterval(timer);
                    resolve();
                }
            }, 200);
        });
    });
    await new Promise(r => setTimeout(r, 3000));

    // Extrair imagens do DOM
    const domImages = await page.evaluate(() => {
        const imgs = new Set();

        // <img> tags
        document.querySelectorAll('img').forEach(img => {
            if (img.src) imgs.add(img.src);
            if (img.dataset.src) imgs.add(img.dataset.src);
        });

        // background-image em CSS inline
        document.querySelectorAll('*').forEach(el => {
            const bg = getComputedStyle(el).backgroundImage;
            if (bg && bg !== 'none') {
                const matches = bg.match(/url\(["']?(.*?)["']?\)/g);
                if (matches) {
                    matches.forEach(m => {
                        const url = m.replace(/url\(["']?/, '').replace(/["']?\)/, '');
                        if (url.startsWith('http')) imgs.add(url);
                    });
                }
            }
        });

        // <source>, <video poster>, <link> icons
        document.querySelectorAll('source[srcset], video[poster], link[rel="icon"], link[rel="apple-touch-icon"]').forEach(el => {
            const src = el.srcset || el.poster || el.href;
            if (src) imgs.add(src);
        });

        // SVG images
        document.querySelectorAll('svg image').forEach(img => {
            const href = img.getAttribute('href') || img.getAttribute('xlink:href');
            if (href) imgs.add(href);
        });

        return [...imgs];
    });

    // Combinar todas as imagens
    const allImages = new Set([...networkImages, ...domImages]);

    // Filtrar data URIs e URLs inválidas
    const validImages = [...allImages].filter(url =>
        url &&
        url.startsWith('http') &&
        !url.startsWith('data:')
    );

    console.log(`\n=== IMAGENS ENCONTRADAS: ${validImages.length} ===\n`);
    validImages.forEach((url, i) => console.log(`  [${i + 1}] ${url}`));

    // Tirar screenshot da página
    if (!fs.existsSync(OUTPUT_DIR)) {
        fs.mkdirSync(OUTPUT_DIR, { recursive: true });
    }

    const screenshotPath = path.join(OUTPUT_DIR, 'page_screenshot.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`\nScreenshot salvo: ${screenshotPath}`);

    // Salvar lista de URLs
    const listPath = path.join(OUTPUT_DIR, 'image_urls.txt');
    fs.writeFileSync(listPath, validImages.join('\n'), 'utf-8');
    console.log(`Lista de URLs salva: ${listPath}`);

    // Baixar imagens
    console.log('\nBaixando imagens...');
    let downloaded = 0;
    let failed = 0;

    for (let i = 0; i < validImages.length; i++) {
        const url = validImages[i];
        const filename = getFilenameFromUrl(url, i + 1);
        const filepath = path.join(OUTPUT_DIR, filename);

        try {
            await downloadImage(url, filepath);
            downloaded++;
            console.log(`  ✓ [${i + 1}/${validImages.length}] ${filename}`);
        } catch (err) {
            failed++;
            console.log(`  ✗ [${i + 1}/${validImages.length}] ${filename} - ${err.message}`);
        }
    }

    console.log(`\n=== RESULTADO ===`);
    console.log(`  Total encontradas: ${validImages.length}`);
    console.log(`  Baixadas com sucesso: ${downloaded}`);
    console.log(`  Falhas: ${failed}`);
    console.log(`  Salvas em: ${OUTPUT_DIR}`);

    await browser.close();
    console.log('\nConcluído!');
})();
