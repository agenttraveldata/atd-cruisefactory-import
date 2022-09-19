<?php
/**
 * The template for displaying a departure
 *
 * @package ATD - Cruise Factory - XML Import
 * @since 1.0.0
 */

get_header();

global $atdDeparture, $atdSpecial;

?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php atd_cf_get_template_part( 'content/single', 'departure' );

	// If comments are open or there is at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile; ?>

<?php get_footer(); ?>