<?php

$cruiseLine = atd_cf_get_post_by_meta_value( 'cruise-line', $args['summary']->getCruise()->getCruiseLine()->getId() );
$ship       = atd_cf_get_post_by_meta_value( 'ship', $args['summary']->getCruise()->getShip()->getId() );

?>
<h4>Thank you for your enquiry, <?php echo $args['first_name']; ?>!</h4>
<p>You have enquired on the following:</p>
<ul>
    Reference ID: <?php echo $args['summary']->getId(); ?>
	<?php if ( $args['summary']->getPost() ): ?>
        <li>
            Departure: <strong>
                <a href="<?php echo get_permalink( $args['summary']->getPost()->ID ); ?>"><?php echo $args['summary']->getPost()->post_title; ?></a>
            </strong>
        </li>
	<?php endif; ?>
    <li>
        Cruise Line: <strong>
            <a href="<?php echo get_permalink( $cruiseLine->ID ); ?>"><?php echo $cruiseLine->post_title; ?></a>
        </strong>
    </li>
    <li>
        Ship: <strong>
            <a href="<?php echo get_permalink( $ship->ID ); ?>"><?php echo $ship->post_title; ?></a>
        </strong>
    </li>
    <li>Departs: <strong><?php echo $args['summary']->getSailingDate()->format( 'j F Y' ); ?></strong></li>
    <li>Duration: <strong><?php echo $args['summary']->getCruise()->getDuration(); ?> Nights</strong></li>
    <li>
        Offer Type: <strong>
			<?php echo $args['summary']->getSpecial() ? $args['summary']->getSpecial()->getType() : 'Cruise Only'; ?>
        </strong>
    </li>
    <li>
        Embark Ship: <strong><?php echo $args['summary']->getCruise()->getEmbarkPort()->getName(); ?></strong>
    </li>
    <li>
        Disembark Ship: <strong><?php echo $args['summary']->getCruise()->getDisembarkPort()->getName(); ?></strong>
    </li>
</ul>
<p>We will endeavour to respond to your request as soon as possible.</p>