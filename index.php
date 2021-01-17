<?php
set_time_limit(0);

require_once './vendor/autoload.php';
require_once './src/core/Model.php';
require_once './src/models/Recipe.php';
require_once './src/models/Category.php';
require_once './src/models/Ingredient.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

const URL_WEB_RECIPES = 'https://www.cocinaconpoco.com/category/recetas/tapas-y-entrantes/page/';
const STATUS_CODE_OK = 200;

$client = new Client();
$page = 1;

do {
    $crawlerCategory = $client->request('GET', URL_WEB_RECIPES . $page . '/');
    $response = $client->getResponse();
    $statusCode = $response->getStatusCode();

    $crawlerCategory->filter('h3.entry-title')->each(function (Crawler $parentCrawler, $i) use ($client) {
        $recipe = new Recipe();
        $recipe->urlRecipe = $parentCrawler->filter('a')->attr('href');
        
        $crawler = $client->request('GET', $recipe->urlRecipe);
        $recipe->title = $crawler->filter('h1.entry-title')->text();
        print 'RECIPE => ' . $recipe->title . PHP_EOL;

        $category = new Category();
        $category->name = $crawler->filter('span.entry-category')->text();
        $idCategory = $category->save();
        $recipe->categoryId = $idCategory;

        if ($crawler->filter('div.post-thumb img')->count() > 0) {
            $recipe->urlImage = $crawler->filter('div.post-thumb img')->attr('src');
        }

        $idRecipe = $recipe->save();
        if ($crawler->filter('ul.ingredients-list li')->count() > 0) {
            $crawler->filter('ul.ingredients-list li')->each(function (Crawler $parentCrawler, $i) use ($idRecipe) {
                saveIngredient($parentCrawler->text(),$idRecipe);
            });
        } else if ($crawler->filter('div.shortcode-ingredients ul li')->count() > 0) {
            $crawler->filter('div.shortcode-ingredients ul li')->each(function (Crawler $parentCrawler, $i) use ($idRecipe) {
                saveIngredient($parentCrawler->text(),$idRecipe);
            });
        }
    });

    sleep(1);
    $page++;

} while ($statusCode == STATUS_CODE_OK);

function saveIngredient($ingredientName,$idRecipe) {
    $ingredient = new Ingredient();
    $ingredient->name = $ingredientName;
    $ingredient->recipeId = $idRecipe;
    $ingredient->save();
}

?>