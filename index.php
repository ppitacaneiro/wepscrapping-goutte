<?php
set_time_limit(0);

require_once './vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$page = 1;

do {
    $crawlerCategory = $client->request('GET', 'https://www.cocinaconpoco.com/category/recetas/tapas-y-entrantes/page/' . $page . '/');
    $response = $client->getResponse();
    $statusCode = $response->getStatusCode();

    print '<p>STATUS_CODE => ' . $statusCode . '</p>';

    $crawlerCategory->filter('h3.entry-title')->each(function (Crawler $parentCrawler, $i) {

        global $client;
        $urlRecipe = $parentCrawler->filter('a')->attr('href');
        print '<p>URL_RECIPE = ' . $urlRecipe."</p>";
        
        $crawler = $client->request('GET', $urlRecipe);
        print "<p>TITLE_RECIPE = ".$crawler->filter('h1.entry-title')->text()."</p>";

        if ($crawler->filter('div.post-thumb img')->count() > 0) {
            print "<p>URL_IMAGE = ".$crawler->filter('div.post-thumb img')->attr('src')."</p>";
        }

        if ($crawler->filter('ul.ingredients-list li')->count() > 0) {
            print '<p>INGREDIENTS = </p>';
            $crawler->filter('ul.ingredients-list li')->each(function (Crawler $parentCrawler, $i) {
                print $parentCrawler->text()."<BR>";
            });
        } else if ($crawler->filter('div.shortcode-ingredients ul li')->count() > 0) {
            $crawler->filter('div.shortcode-ingredients ul li')->each(function (Crawler $parentCrawler, $i) {
                print $parentCrawler->text()."<BR>";
            });
        }

        print "<p>CATEGORY_RECIPE = ".$crawler->filter('span.entry-category')->text()."</p>";

    });

    sleep(1);
    $page++;
} while ($statusCode == 200)

?>