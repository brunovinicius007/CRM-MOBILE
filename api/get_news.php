<?php
// api/get_news.php
header('Content-Type: application/json');

try {
    $url = "https://g1.globo.com/dynamo/economia/rss2.xml";
    $rss = simplexml_load_file($url);
    
    $news = [];
    if ($rss) {
        $count = 0;
        foreach ($rss->channel->item as $item) {
            if ($count >= 5) break; // Pega apenas as 5 últimas
            
            $news[] = [
                'title' => (string)$item->title,
                'link' => (string)$item->link,
                'description' => strip_tags((string)$item->description),
                'pubDate' => date('d/m H:i', strtotime((string)$item->pubDate))
            ];
            $count++;
        }
    }
    
    echo json_encode($news);
} catch (Exception $e) {
    echo json_encode(['error' => 'Não foi possível carregar as notícias.']);
}
