<?php global $atdShip; ?>
<div class="atd-cfi-ar">
	<?php if ( $decks = $atdShip->getDecks() ): ?>
		<?php foreach ( $decks as $deck ) : ?>
            <div class="atd-cfi-ar__col">
                <div class="atd-cfi-ar-col__img">
                    <a data-action="atd-cfi-popover#image" href="<?php echo $deck->getImage(); ?>">
                        <img src="<?php echo $deck->getImage(); ?>" alt="">
                    </a>
                </div>
                <div class="atd-cfi-ar-col__details">
                    <h4><?php echo $deck->getName(); ?></h4>
					Level <?php echo $deck->getLevel(); ?>
                </div>
            </div>
		<?php endforeach; ?>
	<?php else: ?>
        <p>No deck details are currently available for this ship.</p>
	<?php endif; ?>
</div>