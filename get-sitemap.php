<?php
function getFileRowCount($filename)
{
    $file = fopen($filename, "r");
    $rowCount = 0;

    while (!feof($file)) {
        fgets($file);
        $rowCount++;
    }

    fclose($file);

    return $rowCount;
}

// Tentukan base URL secara manual jika $_SERVER tidak tersedia
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'example.com'; // Default ke "example.com" jika tidak ditemukan
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$fullUrl = $protocol . "://" . $host . $requestUri;

// Parsing URL
$parsedUrl = parse_url($fullUrl);
$scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] : '';
$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';

// Gunakan 'https' secara eksplisit jika protokolnya tidak tersedia atau jika default http
if ($protocol !== 'https') {
    $protocol = 'https';
}

// Buat base URL
$baseUrl = $protocol . "://" . $host . $path;
$urlAsli = str_replace("get-sitemap.php", "", $baseUrl);

// Generate robots.txt dengan https
$robotsTxt = "User-agent: *" . PHP_EOL;
$robotsTxt .= "Allow: /" . PHP_EOL;
$robotsTxt .= "Sitemap: " . $urlAsli . "sitemap.xml" . PHP_EOL;
file_put_contents('robots.txt', $robotsTxt);

// Proses file untuk sitemap
$judulFile = "x.txt";

if (file_exists($judulFile)) {
    $jumlahBaris = getFileRowCount($judulFile);
    $sitemapFile = fopen("sitemap.xml", "w");
    fwrite($sitemapFile, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
    fwrite($sitemapFile, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);

    $fileLines = file($judulFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($fileLines as $index => $judul) {
        // Ubah URL menjadi menggunakan artikel
        $sitemapLink = $urlAsli . '?id=' . urlencode($judul);
        fwrite($sitemapFile, '  <url>' . PHP_EOL);
        fwrite($sitemapFile, '    <loc>' . $sitemapLink . '</loc>' . PHP_EOL);
        date_default_timezone_set('Asia/Jakarta');
        $currentTime = date('Y-m-d\TH:i:sP');
        fwrite($sitemapFile, '    <lastmod>' . $currentTime . '</lastmod>' . PHP_EOL);
        fwrite($sitemapFile, '    <changefreq>daily</changefreq>' . PHP_EOL);
        fwrite($sitemapFile, '  </url>' . PHP_EOL);
    }
    fwrite($sitemapFile, '</urlset>' . PHP_EOL);
    fclose($sitemapFile);

    echo "SITEMAP DONE CREATE!";
} else {
    echo "File " . $judulFile . " tidak ditemukan.";
}
?>
