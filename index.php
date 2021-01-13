<?php
set_time_limit(0);

require_once './vendor/autoload.php';
require_once './src/models/Recipe.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$recipe = new Recipe();

$page = 1;

do {
    $crawlerCategory = $client->request('GET', 'https://www.cocinaconpoco.com/category/recetas/tapas-y-entrantes/page/' . $page . '/');
    $response = $client->getResponse();
    $statusCode = $response->getStatusCode();

    print 'STATUS_CODE => ' . $statusCode . PHP_EOL;

    $crawlerCategory->filter('h3.entry-title')->each(function (Crawler $parentCrawler, $i) {

        global $client;
        global $recipe;

        $urlRecipe = $parentCrawler->filter('a')->attr('href');
        print 'URL_RECIPE = ' . $urlRecipe . PHP_EOL;
        $recipe->urlRecipe = $urlRecipe;
        
        $crawler = $client->request('GET', $urlRecipe);
        $titleRecipe = $crawler->filter('h1.entry-title')->text();
        print 'TITLE_RECIPE = ' . $titleRecipe . PHP_EOL;
        $recipe->title = $titleRecipe;

        if ($crawler->filter('div.post-thumb img')->count() > 0) {
            $urlImage = $crawler->filter('div.post-thumb img')->attr('src');
            print 'URL_IMAGE = ' . $urlImage . PHP_EOL;
            $recipe->urlImage = $urlImage;
        }

        if ($crawler->filter('ul.ingredients-list li')->count() > 0) {
            print 'INGREDIENTS = \r\n';
            $crawler->filter('ul.ingredients-list li')->each(function (Crawler $parentCrawler, $i) {
                print $parentCrawler->text(). PHP_EOL;
            });
        } else if ($crawler->filter('div.shortcode-ingredients ul li')->count() > 0) {
            $crawler->filter('div.shortcode-ingredients ul li')->each(function (Crawler $parentCrawler, $i) {
                print $parentCrawler->text(). PHP_EOL;
            });
        }

        print 'CATEGORY_RECIPE = '.$crawler->filter('span.entry-category')->text().PHP_EOL;
        $recipe->categoryId = '1';
        $recipe->save();

    });

    sleep(1);
    $page++;

} while ($statusCode == 200)

?>