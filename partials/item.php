<article class="feed__item">
    <h3><?php echo '<a href="'.htmlspecialchars($item['link']).'" target="_blank">'.htmlspecialchars($item['title']). '</a>' ?></h3>
    <p><?php echo htmlspecialchars($item['description']) ?></p>
    <p><a href="<?php echo htmlspecialchars($item['link']) ?>" class="read-more-button" target="_blank">Read More</a></p>
    <p>Publié le: <?php echo date('F j, Y', strtotime($item['pubDate'])) ?></p>
</article>