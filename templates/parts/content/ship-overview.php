<?php
/** @var ATD\CruiseFactory\Entity\Ship $atdShip */
global $atdShip;
?>
<a href="<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url() : $atdShip->getImage(); ?>" data-action="atd-cfi-popover#image" class="atd-cfi__float-end atd-cfi__mw-40 atd-cfi__ml-2 atd-cfi__mb-2">
    <img class="atd-cfi__img-fluid" src="<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url() : $atdShip->getImage(); ?>" alt="<?php the_title(); ?>">
</a>
<?php the_content(); ?>
<div class="atd-cfi__clearfix"></div>
<div class="atd-cfi__cols atd-cfi__mt-2">
    <div class="atd-cfi-cols__column">
        <h4>Ship Profile &amp; Stats</h4>
		<?php if ( $atdShip->getMaidenVoyage() ): ?>
            <div>Maiden voyage: <?php echo $atdShip->getMaidenVoyage(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getRefurbished() ): ?>
            <div>Refurbished: <?php echo $atdShip->getRefurbished(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getTonnage() ): ?>
            <div>Tonnage: <?php echo $atdShip->getTonnage(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getLength() ): ?>
            <div>Length: <?php echo $atdShip->getLength(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getBeam() ): ?>
            <div>Beam: <?php echo $atdShip->getBeam(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getDraft() ): ?>
            <div>Draft: <?php echo $atdShip->getDraft(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getSpeed() ): ?>
            <div>Speed: <?php echo $atdShip->getSpeed(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getShipRego() ): ?>
            <div>Ship registration: <?php echo $atdShip->getShipRego(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getPassCapacity() ): ?>
            <div>Passenger capacity (dbl): <?php echo $atdShip->getPassCapacity(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getPassSpace() ): ?>
            <div>Passenger space: <?php echo $atdShip->getPassSpace(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getCrewSize() ): ?>
            <div>Crew size: <?php echo $atdShip->getCrewSize(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getNatCrew() ): ?>
            <div>Crew nationality: <?php echo $atdShip->getNatCrew(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getNatOfficers() ): ?>
            <div>Officer nationality: <?php echo $atdShip->getNatOfficers(); ?></div>
		<?php endif; ?>
		<?php if ( $atdShip->getNatDining() ): ?>
            <div>Dining nationality: <?php echo $atdShip->getNatDining(); ?></div>
		<?php endif; ?>
    </div>
	<?php if ( $atdShip->getAmenity() ): ?>
        <div class="atd-cfi-cols__column">
            <h4>Ship Amenities</h4>
			<?php foreach ( $atdShip->getAmenity() as $option ): ?>
                <div><?php echo $option->getName(); ?></div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
	<?php if ( $atdShip->getFacility() ): ?>
        <div class="atd-cfi-cols__column">
            <h4>Ship Facilities</h4>
			<?php foreach ( $atdShip->getFacility() as $option ): ?>
                <div><?php echo $option->getName(); ?></div>
			<?php endforeach; ?>
        </div>
	<?php endif; ?>
</div>
