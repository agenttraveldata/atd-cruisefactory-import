<?php
/**
 * The template for displaying cruise search results
 *
 * @package ATD - Cruise Factory - XML Import
 * @since 1.0.0
 */

get_header();

?>

<?php if ( have_posts() ) : ?>
	<?php echo do_shortcode( '[atd-cfi-search-form]' ); ?>

    <h4>Found <?php echo $GLOBALS['wp_query']->found_posts; ?> results.</h4>
    <div class="atd-cfi-sr" data-controller="atd-cfi-popover">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php atd_cf_get_template_part( 'content/content', 'result' ); ?>
		<?php endwhile; ?>
    </div>

	<?php the_posts_pagination(); ?>
<?php else: ?>
    No results found.
<?php endif; ?>

<?php get_footer(); ?>
