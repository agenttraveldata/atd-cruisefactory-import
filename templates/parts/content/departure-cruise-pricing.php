<?php global $atdDeparture, $atdFactory; ?>
<div class="atd-cfi-departure-pricing__type atd-cfi__mb-2">
    <label for="atd-cfi-select-pax-pricing">Passenger pricing</label>
    <select name="pax" class="atd-cfi__input" id="atd-cfi-select-pax-pricing" data-atd-cfi-toggle-element-target="selector" data-action="atd-cfi-toggle-element#toggle">
        <option value="single">Single</option>
        <option value="twin" selected>Twin</option>
        <option value="triple">Triple</option>
        <option value="quad">Quad</option>
    </select>
</div>

<div class="atd-cfi__mb-2" data-atd-cfi-toggle-element-target="element">
    <label for="atd-cfi-departure-price-single">Single pricing (per person)</label>
    <select name="cabin_price" class="atd-cfi__input" id="atd-cfi-departure-price-single">
		<?php $prices = $atdDeparture->getCruisePrices()->filter( function ( $p ) {
			return $p->getPriceSingle() > 0;
		} ); if ( $prices->count() > 0 ): foreach ( $prices as $price ): ?>
            <option value="<?php echo $price->getId(); ?>">
				<?php echo $price->getCabin()->getName(); ?> - <?php echo $price->getCurrency() . number_format( $price->getPriceSingle() ) . 'pp'; ?>
            </option>
		<?php endforeach;
		else: ?>
            <option value="">Request Price</option>
		<?php endif; ?>
    </select>
</div>
<div class="atd-cfi__mb-2" data-atd-cfi-toggle-element-target="element">
    <label for="atd-cfi-departure-price-twin">Twin pricing (per person)</label>
    <select name="cabin_price" class="atd-cfi__input" id="atd-cfi-departure-price-twin">
		<?php $prices = $atdDeparture->getCruisePrices()->filter( function ( $p ) {
			return $p->getPriceDouble() > 0;
        } ); if ( $prices->count() > 0 ): foreach ( $prices as $price ): ?>
            <option value="<?php echo $price->getId(); ?>">
				<?php echo $price->getCabin()->getName(); ?> - <?php echo $price->getCurrency() . number_format( $price->getPriceDouble() ) . 'pp'; ?>
            </option>
		<?php endforeach;
		else: ?>
            <option value="">Request Price</option>
		<?php endif; ?>
    </select>
</div>
<div class="atd-cfi__mb-2" data-atd-cfi-toggle-element-target="element">
    <label for="atd-cfi-departure-price-triple">Triple pricing (per person)</label>
    <select name="cabin_price" class="atd-cfi__input" id="atd-cfi-departure-price-triple">
		<?php $prices = $atdDeparture->getCruisePrices()->filter( function ( $p ) {
			return $p->getPriceTriple() > 0;
        } ); if ( $prices->count() > 0 ): foreach ( $prices as $price ): ?>
            <option value="<?php echo $price->getId(); ?>">
				<?php echo $price->getCabin()->getName(); ?> - <?php echo $price->getCurrency() . number_format( $price->getPriceTriple() ) . 'pp'; ?>
            </option>
		<?php endforeach;
		else: ?>
            <option value="">Request Price</option>
		<?php endif; ?>
    </select>
</div>
<div class="atd-cfi__mb-2" data-atd-cfi-toggle-element-target="element">
    <label for="atd-cfi-departure-price-quad">Quad pricing (per person)</label>
    <select name="cabin_price" class="atd-cfi__input" id="atd-cfi-departure-price-quad">
		<?php $prices = $atdDeparture->getCruisePrices()->filter( function ( $p ) {
			return $p->getPriceQuad() > 0;
        } ); if ( $prices->count() > 0 ): foreach ( $prices as $price ): ?>
            <option value="<?php echo $price->getId(); ?>">
				<?php echo $price->getCabin()->getName(); ?> - <?php echo $price->getCurrency() . number_format( $price->getPriceQuad() ) . 'pp'; ?>
            </option>
		<?php endforeach;
		else: ?>
            <option value="">Request Price</option>
		<?php endif; ?>
    </select>
</div>
