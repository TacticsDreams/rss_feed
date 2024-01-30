<?php
$feedSources = [
    'BBC News' => 'http://feeds.bbci.co.uk/news/rss.xml',
    'New York Times' => 'http://rss.nytimes.com/services/xml/rss/nyt/World.xml',
    'TechCrunch' => 'https://techcrunch.com/feed/',
    'NASA Breaking News' => 'https://www.nasa.gov/rss/dyn/breaking_news.rss',
    'ESPN Sports' => 'https://www.espn.com/espn/rss/news'
];

include 'functions/xmlToArray.php';

$maxItemsperPage = 6;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$displayOption = isset($_GET['display']) && $_GET['display'] === 'alt' ? 'alt' : 'default';
if ($displayOption === 'alt') $maxItemsperPage = 9;

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Décodeur RSS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
<header>
    <h1>Decodeur de Flux RSS</h1>
    <p>Cette page est un exercice de PHP.</p>
</header>
<div class="display-option-toggle">
    <label for="displayOptionCheckbox">Use Alternate Display</label>
    <input type="checkbox" id="displayOptionCheckbox" <?php echo isset($_GET['display']) && $_GET['display'] === 'alt' ? 'checked' : ''; ?>>
</div>
<div class="tabs">
    <?php
    $firstIteration = true;
    foreach ($feedSources as $name => $feedUrl) {
        $isActive = (isset($_GET['source']) && $_GET['source'] === $feedUrl) || ($firstIteration && !isset($_GET['source']));
        echo '<div class="tab' . ($isActive ? ' active' : '') . '"><a href="?source=' . urlencode($feedUrl) . '">' . htmlspecialchars($name) . '</a></div>';
        $firstIteration = false;
    }
    ?>
</div>

<?php
$selectedFeedUrl = isset($_GET['source']) ? urldecode($_GET['source']) : null;

foreach($feedSources as $name => $feedUrl) {
    $xml = file_get_contents($feedUrl);

    if($xml !== false) {
        libxml_use_internal_errors(true); // Enable libxml error handling.
        $rss = simplexml_load_string($xml);

        if ($rss === false) {
            echo 'Error parsing XML for feed: ' . $feedUrl;
            foreach(libxml_get_errors() as $error) {
                echo '<br>', $error->message;
            }
            libxml_clear_errors(); // Clear libxml error buffer
            continue; // Move to the next feed
        }

        if ($selectedFeedUrl === $feedUrl) {
            echo '<h2>'. htmlspecialchars($rss->channel->title) .'</h2>';

            $itemsArray = xmlToArray($rss->channel->item);

            $totalItems = count($itemsArray);
            $startIndex = ($currentPage - 1) * $maxItemsperPage;
            $endIndex = $startIndex + $maxItemsperPage;
            $displayItems = array_slice($itemsArray, $startIndex, $maxItemsperPage);

            ?><div class="feed__items"><?php
            foreach($displayItems as $item) {
                if ($displayOption === 'alt') {
                    include('partials/item_short.php');
                } else {
                    include('partials/item.php');
                }
            }
            ?></div><?php

            $totalPages=($totalItems / $maxItemsperPage);
            ?>
            <div class="pagination">
                <?php 
                    for ($i = 1; $i <= min($totalPages, 8); $i++) {
                        $activeClass = ($i == $currentPage) ? 'active' : '';
                        $pageUrl = '?page=' . $i;
                        if ($selectedFeedUrl) {
                            $pageUrl .= '&source=' . urlencode($selectedFeedUrl);
                        }
                        echo '<a href="' . $pageUrl . '" class="' . $activeClass . '">' . $i . '</a>';
                    }
                ?>
            </div>
            <?php
        }

    } else {
        echo 'Une erreur est survenue lors de l\'accès aux flux RSS: ' . $feedUrl;
    }
}
?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const displayOptionCheckbox = document.getElementById('displayOptionCheckbox');

        displayOptionCheckbox.addEventListener('change', function() {
            const isChecked = displayOptionCheckbox.checked;
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);

            if (isChecked) {
                params.set('display', 'alt');
            } else {
                params.delete('display');
            }

            currentUrl.search = params.toString();
            window.location.href = currentUrl.toString();
        });
    });
</script>
</body>
</html>
