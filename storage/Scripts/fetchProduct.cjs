const puppeteer = require('puppeteer');
const fs = require('fs');

const shopId = process.argv[2];
const itemId = process.argv[3];

async function fetchShopeeProduct(shopId, itemId) {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();

    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36');

    // Load cookie kalau ada
    const cookiePath = './storage/cookies/shopee.json';
    if (fs.existsSync(cookiePath)) {
        const cookies = JSON.parse(fs.readFileSync(cookiePath));
        await page.setCookie(...cookies);
    }

    const productUrl = `https://shopee.co.id/${shopId}.${itemId}`;
    await page.goto(productUrl, { waitUntil: 'networkidle2' });

    await page.waitForSelector('div[data-sqe="product-info"]');

    const data = await page.evaluate(() => {
        try {
            return window.__PRELOADED_STATE__.pdp.detail || null;
        } catch (e) {
            return null;
        }
    });

    // Simpan cookie terbaru
    const cookies = await page.cookies();
    fs.writeFileSync(cookiePath, JSON.stringify(cookies, null, 2));

    await browser.close();
    return data;
}

fetchShopeeProduct(shopId, itemId).then(data => {
    console.log(JSON.stringify(data));
});
