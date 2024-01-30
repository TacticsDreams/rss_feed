<article class="feed__item feed__item--alt">
    <h3><?php echo '<a href="'.htmlspecialchars($item['link']).'" target="_blank">'.htmlspecialchars($item['title']). '</a>' ?></h3>
    <p class="publish-date"><?php echo date('M j, Y', strtotime($item['pubDate'])) ?></p>
    <p class="read-more"><a href="<?php echo htmlspecialchars($item['link']) ?>" target="_blank">Read More</a></p>
</article>