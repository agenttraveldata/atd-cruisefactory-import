<?php global $atdDeparture, $atdSpecial; ?>
<input type="hidden" name="departure_id" value="<?php echo $atdSpecial
	? $atdSpecial->getDepartureId()
	: $atdDeparture->getId(); ?>">
<input type="hidden" name="departure_type" value="<?php echo $atdSpecial ? 'special' : 'cruise'; ?>">

<div class="atd-cfi__mb-2">
    <label for="atd-cfi-departure-price-request">Cabin type</label>
    <select name="request_cabin" class="atd-cfi__input" id="atd-cfi-departure-price-request">
		<?php /** @var ATD\CruiseFactory\Entity\Cabin $cabin */
		foreach ( $atdDeparture->getCruise()->getShip()->getCabins()->uasort( function ( $a, $b ) {
			return [ $a->getCategory(), $b->getOrder() ] <=> [ $b->getCategory(), $a->getOrder() ];
		} ) as $cabin ): ?>
		<?php if ( empty( $cat ) || $cat !== $cabin->getCategory() ): ?>
		<?php if ( ! empty( $cat ) ): ?></optgroup><?php endif; ?>
        <optgroup label="<?php echo ucfirst( $cabin->getCategory() ); ?>"><?php $cat = $cabin->getCategory();
			endif; ?>
            <option value="<?php echo $cabin->getId(); ?>"><?php echo $cabin->getName(); ?></option>
			<?php endforeach; ?>
        </optgroup>
    </select>
</div>
