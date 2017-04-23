<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($sitemaps as $sitemap): ?>
    <sitemap>
        <loc><?= $sitemap['loc'] ?></loc>
        <lastmod><?= $sitemap['lastmod'] ?></lastmod>
    </sitemap>
<?php endforeach; ?>
</sitemapindex>