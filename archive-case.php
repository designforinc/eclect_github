<?php
// topic_path
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>
				<li>導入事例</li>' . "\n";

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
			<h1 class="cmn-visual__txt1">導入事例</h1>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
		<div class="mod-sec">
			<div class="case-items">
<?php
$perpage = 20;
if ( $wp_query->have_posts() ) {
	$i = 0;
	while ( $wp_query->have_posts() ) {
		$wp_query->the_post();

		$category = '';
		if ( $terms = get_the_terms( get_the_ID(), 'case_category' ) ) {
			$term = array_slice ( $terms, 0, 1 );
			$term = $term[0];
			$category = esc_html( $term->name );
		}

		$thumbnail = '';
		if ( $attachment_id = get_post_meta( $post->ID, 'case_thumbnail', true ) )
			$thumbnail = '<img src="' . wp_get_attachment_image_url( $attachment_id, 'm652x356' ) . '" alt="">';
		else
			$thumbnail = '<img src="' . get_bloginfo( 'template_url' ) . '/img/no_image_652x356.png" alt="">';
?>
				<article class="case-items__item"<?php echo $i >= $perpage ? ' style="display: none;"' : ''; ?>>
					<figure><?php echo $thumbnail; ?></figure>
					<div class="case-items__item__tag"><?php echo $category; ?></div>
					<div class="case-items__item__inner">
						<p class="case-items__item__title"><?php the_title(); ?></p>
						<p class="case-items__item__company"><?php echo get_post_meta( $post->ID, 'case_company', true ); ?></p>
						<div class="case-items__item__btn"><a href="<?php the_permalink(); ?>" class="mod-btn2"><span>View More</span></a></div>
					</div>
				</article>
<?php
		$i++;
	}
	wp_reset_postdata();
}
?>
			</div>
		</div>
<?php
if ( (int) $wp_query->found_posts > $perpage ) {
?>
		<div class="mod-btn3"><span>Load More</span></div>
<?php
}
?>
	</article>
<?php get_footer(); ?>
