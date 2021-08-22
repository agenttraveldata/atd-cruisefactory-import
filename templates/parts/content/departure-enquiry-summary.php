<?php

$departure = atd_cf_get_departure_details( get_query_var( 'departure_id', 0 ), get_query_var( 'departure_type' ) );

$paxTypes  = [ 'single', 'double', 'triple', 'quad' ];
$leadTypes = [ 'inside', 'outside', 'balcony', 'suite' ];

$pax        = get_query_var( 'pax', 'double' );
$pax        = $pax === 'twin' ? 'double' : $pax;
$cabinPrice = get_query_var( 'cabin_price', null );
$leadPrice  = get_query_var( 'lead_price', 'inside' );

?>
<h4>Departure Summary</h4>
<ul>
    <li>
        <strong>Cruise Name:</strong>
		<?php echo $departure->getSpecial() ? $departure->getSpecial()->getName() : $departure->getCruise()->getName(); ?>
    </li>
    <li>
        <strong>Cruise Line:</strong>
		<?php echo $departure->getCruise()->getCruiseLine()->getName(); ?>
    </li>
    <li>
        <strong>Ship:</strong>
		<?php echo $departure->getCruise()->getShip()->getName(); ?>
    </li>
    <li><strong>Duration:</strong> <?php echo $departure->getCruise()->getDuration(); ?> Nights</li>
    <li><strong>Departs:</strong> <?php echo $departure->getSailingDate()->format( 'j F Y' ); ?></li>
    <li>
        <strong>Cabin:</strong>
		<?php if ( $departure->getSpecial() ): ?>
			<?php if ( $cabinPrice ): ?>
				<?php echo $departure->getSpecialPrice()->getCabin()->getName(); ?>
			<?php else: ?>
				<?php echo ucfirst( $leadPrice ); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php if ( $cabinPrice ): ?>
				<?php echo $departure->getCruisePrice()->getCabin(); ?>
			<?php else: ?>
				<?php echo ucfirst( $leadPrice ); ?> Cabin
			<?php endif; ?>
		<?php endif; ?>
    </li>
    <li>
        <strong>Price:</strong>
		<?php if ( $departure->getSpecial() ): ?>
			<?php if ( $departure->getSpecialPrice() ): ?>
				<?php echo $departure->getSpecialPrice()->getCurrency()->getSign(); ?><?php echo $departure->getSpecialPrice()->getPrice(); ?>
			<?php elseif ( in_array( $leadPrice, $leadTypes ) ): ?>
				<?php echo $departure->getSpecial()->getCurrency()->getSign(); ?><?php echo $departure->getSpecialLeadPrice()->{'getPrice' . ucfirst( $leadPrice )}(); ?>
			<?php else: ?>
                Request Price
			<?php endif; ?>
		<?php else: ?>
			<?php if ( in_array( $pax, $paxTypes ) && $departure->getCruisePrice() ): ?>
				<?php echo $departure->getCruisePrice()->getCurrency(); ?><?php echo $departure->getCruisePrice()->{'getPrice' . ucfirst( $pax )}(); ?>
			<?php else: ?>
                Request Price
			<?php endif; ?>
		<?php endif; ?>
    </li>
</ul>