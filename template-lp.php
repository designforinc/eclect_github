<?php
/*
Template Name: LP
*/
?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
// css
$css = '';
if ( file_exists ( dirname ( __FILE__ ) . '/css/' . get_post_field( 'post_name', $post->ID ) . '.css' ) )
	$css .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/' . get_post_field( 'post_name', $post->ID ) . '.css">' . "\n";
$css .= ( $meta = get_post_meta( $post->ID, 'styles', true ) ) ? $meta . "\n" : '';

// js
$js = '';
if ( file_exists ( dirname ( __FILE__ ) . '/js/' . get_post_field( 'post_name', $post->ID ) . '.js' ) )
	$js .= '<script src="' . get_bloginfo( 'template_url' ) . '/js/' . get_post_field( 'post_name', $post->ID ) . '.js"></script>' . "\n";
$js .= ( $meta = get_post_meta( $post->ID, 'scripts', true ) ) ? $meta . "\n" : '';

// body class
$body_class = ( $meta = get_post_meta( $post->ID, 'body_class', true ) ) ? $meta : '';

get_header();
?>
<?php the_content(); ?>

<?php get_footer(); ?>
<?php endwhile; endif; ?>
