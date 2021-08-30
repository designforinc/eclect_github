<?php
// topic_path
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>
				<li>お知らせ</li>' . "\n";

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
			<h1 class="cmn-visual__txt1">お知らせ</h1>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
		<div class="news-tab">
			<ul>
				<li<?php echo is_tax() ? '' : ' class="current"'; ?>><a href="/news/">全て</a></li>
<?php
$args = array (
	'get' => 'all',
);
$terms = get_terms( $post_type . '_category', $args );
if ( $terms && !is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
?>
				<li<?php echo $cr_term_slug == $term->slug ? ' class="current"' : ''; ?>><a href="<?php echo get_term_link( $term ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
<?php
	}
}
?>
			</ul>
		</div>
		<div class="news-items">
<?php
$perpage = 20;
if ( $wp_query->have_posts() ) {
	$i = 0;
	while ( $wp_query->have_posts() ) {
		$wp_query->the_post();

		$category = '';
		if ( $terms = get_the_terms( get_the_ID(), 'news_category' ) ) {
			$term = array_slice ( $terms, 0, 1 );
			$term = $term[0];
			$category = esc_html( $term->name );
		}

		$flag = false;
		$href = get_permalink();

		if ( $attachment_id = get_post_meta( $post->ID, 'link_file', true ) ) {
			$href = wp_get_attachment_url( $attachment_id );
			$flag = true;
		} elseif ( $meta = get_post_meta( $post->ID, 'link_url', true ) ) {
			$href = $meta;
			$flag = true;
		}

		$target = get_post_meta( $post->ID, 'link_target', true ) ? ' target="_blank"' : '';

		if ( $flag || get_the_content() )
			$html = '<a href="' . $href . '"' . $target . '>' . get_the_title() . ( $target ? '<img src="' . get_bloginfo( 'template_url' ) . '/img/cmn_ico_win2.svg" alt="">' : '' ) . '</a>';
		else
			$html = get_the_title();
?>
			<div class="news-items__item"<?php echo $i >= $perpage ? ' style="display: none;"' : ''; ?>>
				<div class="news-items__item__date"><?php echo get_the_date( 'Y.m.d' ); ?></div>
				<div class="news-items__item__category"><div class="news-category"><?php echo $category; ?></div></div>
				<div class="news-items__item__title"><?php echo $html; ?></div>
			</div>
<?php
		$i++;
	}
	wp_reset_postdata();
}
?>
		</div>
<?php
if ( (int) $wp_query->found_posts > $perpage ) {
?>
		<div class="mod-btn3"><span>Load More</span></div>
<?php
	if ( is_tax() )
		echo '<input type="hidden" name="taxonomy" value="' . $taxonomy . '"><input type="hidden" name="term_slug" value="' . $cr_term_slug . '">' . "\n";
}
?>
	</article>
<?php get_footer(); ?>
