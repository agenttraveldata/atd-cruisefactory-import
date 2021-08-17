<?php
/**
 * The template for displaying a ship
 *
 * @package ATD - Cruise Factory - XML Import
 * @since 1.0.0
 */

get_header();

global $atdShip;

?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php atd_cf_get_template_part( 'content/single', 'ship' );

	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile; ?>

<?php get_footer(); ?>