<?php global $atdCruiseLine; ?>

<div class="atd-cfi__float-end atd-cfi__mw-40 atd-cfi__ml-2 atd-cfi__mb-2">
    <img class="atd-cfi__img-fluid" src="<?php echo has_post_thumbnail() ? get_the_post_thumbnail_url() : $atdCruiseLine->getImage(); ?>" alt="<?php the_title(); ?>">
</div>
<?php the_content(); ?>