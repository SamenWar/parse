<?php
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

// URL страницы, которую нужно спарсить
$url = 'https://lotok.ua/discount/cat1';

// Создаем новый экземпляр Guzzle клиента
$client = new Client();

try {
    // Отправляем GET-запрос на указанный URL
    $response = $client->request('GET', $url);
} catch (\Exception $e) {
    // Если произошла ошибка, выводим ее сообщение
    echo $e->getMessage();
    exit;
}

// Получаем HTML-код страницы из ответа
$html = $response->getBody()->getContents();

// Создаем новый экземпляр класса Crawler
$crawler = new Crawler($html);

// Создаем пустой массив для хранения данных
$products = [];

// Ищем все слайды на странице и выполняем действия для каждого слайда
$crawler->filter('.products-thumb-slider__slide')->each(function (Crawler $node) use (&$products) {
    $title = $node->filter('.js-title')->text();
    $weight = $node->filter('.js-weight')->text();
    $oldPrice = $node->filter('.js-old')->text();
    $currentPrice = $node->filter('.js-current')->text();
    $image = $node->filter('.js-img img')->attr('src');

    $product = [
        'title' => $title,
        'weight' => $weight,
        'old_price' => $oldPrice,
        'current_price' => $currentPrice,
        'image' => $image
    ];

    // Добавляем данные продукта в массив
    $products[] = $product;
});

// Сохраняем данные в файл products.json в директории storage/app
Storage::disk('local')->put('products.json', json_encode($products));

// Выводим данные на экран для проверки
var_dump($products);
