<?php
// topic_path
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>
				<li>サービス</li>' . "\n";

// css
$css = '';
if ( file_exists ( dirname ( __FILE__ ) . '/css/' . $post_type . '.css' ) )
	$css .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/' . $post_type . '.css">' . "\n";

// js
$js = '';
if ( file_exists ( dirname ( __FILE__ ) . '/js/' . $post_type . '.js' ) )
	$js .= '<script src="' . get_bloginfo( 'template_url' ) . '/js/' . $post_type . '.js"></script>' . "\n";

get_header();
?>
	<article class="contents">
		<div class="cmn-visual"><div>
			<h1 class="cmn-visual__txt1">サービス</h1>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
<?php
$args = array (
	'get' => 'all',
);
$terms = get_terms( $post_type . '_category', $args );
if ( $terms && !is_wp_error( $terms ) ) {
	$i = 0;
	foreach ( $terms as $term ) {
		if ( !$term->parent ) {
?>
		<section class="mod-sec">
			<div class="mod-cols1<?php echo $i % 2 !== 0 ? '' : '--reverse'; ?>">
				<div class="mod-cols1__col1">
					<h2 class="mod-hd1"><?php echo esc_html( $term->name ); ?></h2>
					<?php echo wpautop( get_term_meta( $term->term_id, 'service_description', true ) ); ?>
					<div class="service__btn-more"><a href="<?php echo get_term_link( $term ); ?>" class="mod-btn2"><span>View More</span></a></div>
				</div>
<?php
if ( $attachment_id = get_term_meta( $term->term_id, 'service_img', true ) ) {
	$src = wp_get_attachment_image_url( $attachment_id, 'full' );
?>
				<figure><img src="<?php echo $src; ?>" alt=""></figure>
<?php
}
?>
			</div>
		</section>
<?php
			if ( $i < count ( $terms ) - 1 ) {
?>
		<hr>
<?php
			}
			$i++;
		}
	}
}
?>
	</article>
<?php get_footer(); ?>
