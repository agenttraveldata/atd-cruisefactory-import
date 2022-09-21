<?php
/**
 * @var ATD\CruiseFactory\Entity\Factory $atdFactory
 * @var ATD\CruiseFactory\Entity\Special $atdSpecial
 */
global $atdSpecial, $atdFactory; ?>
    <input type="hidden" name="departure_id" value="<?php echo $atdSpecial->getDepartureId(); ?>">
    <input type="hidden" name="departure_type" value="special">

<?php if ( $atdSpecial->getSpecialLeadPrice() ): ?>
    <div class="atd-cfi__mb-2">
        <h4>Lead pricing</h4>
		<?php foreach ( ATD_CF_XML_LEAD_CATEGORIES as $price ): ?>
			<?php if ( ! $atdSpecial->getSpecialLeadPrice()->{'getPrice' . ucfirst( $price )}() ):
				continue;
			endif; ?>
            <div>
                <input type="radio" name="lead_price" value="<?php echo $price; ?>"
                       id="atd-cfi-special-lead-pricing-<?php echo $price; ?>">
                <label for="atd-cfi-special-lead-pricing-<?php echo $price; ?>">
					<?php echo ucfirst( $price ); ?>
                    from <?php echo $atdSpecial->getCurrency()->getSign(); ?><?php echo number_format( $atdSpecial->getSpecialLeadPrice()->{'getPrice' . ucfirst( $price )}() ); ?>
                </label>
            </div>
		<?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ( $atdSpecial->getSpecialPrices()->count() > 0 ): ?>
    <div class="atd-cfi__mb-2">
        <h4>Cabin pricing</h4>
        <label for="atd-cfi-departure-special-price">Twin pricing (per person)</label>
        <select name="cabin_price" class="atd-cfi__input" id="atd-cfi-departure-special-price">
			<?php foreach ( $atdSpecial->getSpecialPrices() as $price ): ?>
                <option value="<?php echo $price->getId(); ?>">
					<?php echo $price->getCabin()->getName(); ?>
                    - <?php echo $atdSpecial->getCurrency()->getSign() . number_format( $price->getPrice() ) . 'pp'; ?>
                </option>
			<?php endforeach; ?>
        </select>
    </div>
<?php endif; ?>