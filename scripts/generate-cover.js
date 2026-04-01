
import puppeteer from 'puppeteer';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname  = path.dirname(__filename);

// ---------------------------------------------------------------------------
// Paleta de cores por categoria
// ---------------------------------------------------------------------------
const CATEGORY_PALETTE = {
    'romance':                    { bg: '#e8dfc8', border: '#6b4c2a', ornament: '#8b6340', dark: false },
    'conto':                      { bg: '#f5eed8', border: '#8a6420', ornament: '#b08030', dark: false },
    'novela':                     { bg: '#e4e6ea', border: '#3a4a5a', ornament: '#4a6080', dark: false },
    'crônica':                    { bg: '#e8e4d4', border: '#3a5a3a', ornament: '#4a7a4a', dark: false },
    'cronica':                    { bg: '#e8e4d4', border: '#3a5a3a', ornament: '#4a7a4a', dark: false },
    'poesia':                     { bg: '#f2ece0', border: '#7a2030', ornament: '#9e3040', dark: false },
    'ensaio':                     { bg: '#15202e', border: '#b8973a', ornament: '#d4af4f', dark: true  },
    'memórias & autobiografia':   { bg: '#e8dfc8', border: '#4a3a6a', ornament: '#6a5a8a', dark: false },
    'memorias & autobiografia':   { bg: '#e8dfc8', border: '#4a3a6a', ornament: '#6a5a8a', dark: false },
    'memórias':                   { bg: '#e8dfc8', border: '#4a3a6a', ornament: '#6a5a8a', dark: false },
    'memorias':                   { bg: '#e8dfc8', border: '#4a3a6a', ornament: '#6a5a8a', dark: false },
    'autobiografia':              { bg: '#e8dfc8', border: '#4a3a6a', ornament: '#6a5a8a', dark: false },
    'teatro':                     { bg: '#1c1a14', border: '#8a7a3a', ornament: '#b0a050', dark: true  },
};

const DEFAULT_PALETTE = CATEGORY_PALETTE['romance'];

/**
 * Retorna a paleta correspondente à categoria (case-insensitive, tolerante a acentos).
 */
function getPalette(category) {
    if (!category) return DEFAULT_PALETTE;
    const key = category.toLowerCase().trim();
    return CATEGORY_PALETTE[key] ?? DEFAULT_PALETTE;
}

// ---------------------------------------------------------------------------
// Geração do HTML da capa
// ---------------------------------------------------------------------------
function buildHtml(title, author, year, palette) {
    const titleColor = palette.dark ? '#f0ead8' : palette.border;

    // Escapa caracteres especiais para inserção segura no HTML
    const esc = (str) =>
        String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');

    return `<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    width: 420px; height: 620px; overflow: hidden;
    background: transparent;
    font-family: 'Georgia', serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 5px 7px 10px 13px;
  }
  .book-cover-wrapper {
    box-shadow:
      -7px 0 0 0 #c4aa82,
      -12px 5px 10px rgba(0, 0, 0, 0.28),
      5px 5px 16px rgba(0, 0, 0, 0.18);
    display: inline-block;
    line-height: 0;
  }
  .cover {
    width: 400px; height: 600px;
    background: ${palette.bg};
    border: 6px double ${palette.border};
    display: flex; flex-direction: column;
    align-items: center; justify-content: space-between;
    padding: 32px 18px 20px 18px;
    position: relative;
    overflow: hidden;
    isolation: isolate;
  }
  .cover::before {
    content: '';
    position: absolute; inset: 12px;
    border: 1px solid ${palette.border};
    pointer-events: none;
    z-index: 1;
  }
  .cover::after {
    content: '';
    position: absolute; inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='600'%3E%3Cfilter id='paper'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='400' height='600' filter='url(%23paper)' opacity='0.07'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 2;
  }
  .spine-shadow {
    position: absolute;
    top: 0; left: 0;
    width: 28px; height: 100%;
    background: linear-gradient(
      to right,
      rgba(0, 0, 0, 0.20) 0%,
      rgba(0, 0, 0, 0.08) 55%,
      transparent 100%
    );
    pointer-events: none;
    z-index: 3;
  }
  .footer {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 4;
    margin-top: auto;
    padding-bottom: 5px;
  }
  .site-tag {
    font-size: 13px; letter-spacing: 3px;
    color: ${palette.border}; text-transform: uppercase;
    font-family: Arial, sans-serif; text-align: center;
    width: 100%;
    position: relative; z-index: 4;
  }
  .vignette {
    color: ${palette.ornament}; font-size: 42px;
    text-align: center; line-height: 1;
    margin: 4px 0;
    position: relative; z-index: 4;
  }
  .book-title {
    color: ${titleColor}; font-size: 36px;
    text-align: center; line-height: 1.2;
    font-weight: bold; flex: 1;
    display: flex; align-items: center;
    justify-content: center;
    padding: 0 8px;
    position: relative; z-index: 4;
    /* Removido text-shadow problemático */
  }
  .divider {
    color: ${palette.border}; font-size: 16px;
    text-align: center; letter-spacing: 8px;
    line-height: 1;
  }
  .author {
    font-size: 17px; color: ${palette.border};
    text-align: center; font-style: italic;
    line-height: 1.2;
  }
  .year {
    font-size: 13px; color: ${palette.ornament};
    text-align: center; letter-spacing: 2px;
    font-family: Arial, sans-serif;
    line-height: 1;
  }
</style>
</head>
<body>
  <div class="book-cover-wrapper">
    <div class="cover">
      <div class="spine-shadow"></div>
      <div class="site-tag">✦ Leia Livre ✦</div>
      <div class="vignette">❧</div>
      <div class="book-title">${esc(title)}</div>
      <div class="footer">
        <div class="divider">— ◆ —</div>
        <div class="author">${esc(author)}</div>
        <div class="year">${esc(year ?? '')}</div>
      </div>
    </div>
  </div>
</body>
</html>`;
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------
async function main() {
    const rawArg = process.argv[2];

    if (!rawArg) {
        process.stderr.write('Erro: argumento JSON ausente.\n');
        process.stderr.write('Uso: node scripts/generate-cover.js \'{"slug":"...","title":"...","author":"...","year":1882,"category":"Romance"}\'\n');
        process.exit(1);
    }

    let bookData;
    try {
        bookData = JSON.parse(rawArg);
    } catch (err) {
        process.stderr.write(`Erro ao parsear JSON: ${err.message}\n`);
        process.exit(1);
    }

    const { slug, title, author, year, category } = bookData;

    if (!slug || !title) {
        process.stderr.write('Erro: campos "slug" e "title" são obrigatórios.\n');
        process.exit(1);
    }

    // Resolve o caminho de saída relativo à raiz do projeto Laravel
    const projectRoot = path.resolve(__dirname, '..');
    const coversDir   = path.join(projectRoot, 'storage', 'app', 'public', 'covers');
    const outputPath  = path.join(coversDir, `${slug}.webp`);

    // Garante que o diretório existe
    fs.mkdirSync(coversDir, { recursive: true });

    const palette = getPalette(category);
    const html    = buildHtml(title, author, year, palette);

    let browser;
    try {
        browser = await puppeteer.launch({
            args: ['--no-sandbox', '--disable-setuid-sandbox'],
            headless: true,
        });

        const page = await browser.newPage();

        await page.setViewport({ width: 420, height: 620, deviceScaleFactor: 2 });
        await page.setContent(html, { waitUntil: 'networkidle0' });

        await page.screenshot({
            path: outputPath,
            type: 'webp',
            quality: 90,
            clip: { x: 0, y: 0, width: 420, height: 620 },
        });

        process.stdout.write(`OK:${outputPath}\n`);
        process.exit(0);
    } catch (err) {
        process.stderr.write(`Erro ao gerar capa: ${err.message}\n`);
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close().catch(() => {});
        }
    }
}

main();
