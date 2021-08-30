<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
$post_title = get_the_title();
$permalink = get_permalink();

// ancestors
$ancestors = get_post_ancestors( $post->ID );
$ancestors_title = '';
$ancestors_title .= $post_title . ' | ';
foreach ( $ancestors as $ancestor )
	$ancestors_title .= get_the_title( $ancestor ) . ' | ';
$ancestors_title = rtrim ( $ancestors_title, ' | ' );

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

// topic_path
$ancestors = array_reverse ( $ancestors );
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>' . "\n";
foreach ( $ancestors as $ancestor )
	$topic_path .= '				<li><a href="' . get_permalink( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a></li>' . "\n";
$topic_path .= '				<li>' . $post_title . '</li>' . "\n";

get_header();
?>
	<article class="contents">
		<div class="cmn-visual"><div>
			<h1 class="cmn-visual__txt1"><?php the_title(); ?></h1>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
		<div class="post-content">
<?php the_content(); ?>
		</div>
	</article>
<?php get_footer(); ?>
<?php endwhile; endif; ?>
