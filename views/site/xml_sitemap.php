<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
//<lastmod>< ?= $item['lastmod'] ? ><!--</lastmod>-->
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($items as $item): ?>
    <url>
        <loc><?= $item['loc'] ?></loc>
        <changefreq><?= $item['changefreq'] ?></changefreq>
        <priority><?= $item['priority'] ?></priority>
    </url>
<?php endforeach; ?>
</urlset>