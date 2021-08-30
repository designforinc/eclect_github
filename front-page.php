<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
$post_title = get_the_title();
$permalink = get_permalink();

// css
$css = '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/jquery.visualAnimateJS.css">
<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/top.css">' . "\n";
$css .= ( $meta = get_post_meta( $post->ID, 'styles', true ) ) ? $meta . "\n" : '';

// js
$js = '<script src="' . get_bloginfo( 'template_url' ) . '/js/jquery.visualAnimateJS.js"></script>
<script>
jQuery(function($) {
	$(\'.top-case__slider .visual-animate\').visualAnimate({
		animate: \'slide\',
		speed: 300,
		interval: 5000,
		to_bg: false,
		dot: true,
		prev_btn_class: \'.top-case__slider__btn-prev\',
		next_btn_class: \'.top-case__slider__btn-next\',
		autoStart: false,
		slide_click: false,
		colindex: {768:0,9999:1},
	});
});
</script>' . "\n";
$js .= ( $meta = get_post_meta( $post->ID, 'scripts', true ) ) ? $meta . "\n" : '';

get_header();
?>
<?php the_content(); ?>
<?php get_footer(); ?>
<?php endwhile; endif; ?>
