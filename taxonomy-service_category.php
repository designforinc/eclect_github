<?php
$cr_term_slug = get_query_var( 'term' );
$taxonomy = get_query_var( 'taxonomy' );
$term = get_term_by( 'slug', $cr_term_slug, $taxonomy );
$cr_term_name = esc_html( $term->name );
$cr_term_id = (int) $term->term_id;
$post_type = get_post_type();

if ( $taxonomy == $post_type . '_category' ) {
	$tax_ancestors = get_ancestors( $cr_term_id, $taxonomy );

	// topic_path
	$topic_path = array ();
	$topic_path[] = $paged ? '				<li><a href="' . get_term_link( $term ) . '">' . $cr_term_name . '</a></li>
			<li>' . $paged . ' / ' . (int) $wp_query->max_num_pages . '</li>' . "\n" : '				<li>' . $cr_term_name . '</li>' . "\n";

	foreach ( $tax_ancestors as $val ) {
		$term = get_term_by( 'id', $val, $taxonomy );

		// topic_path
		$topic_path[] = '				<li><a href="' . get_term_link( $term ) . '">' . esc_html( $term->name ) . '</a></li>' . "\n";
	}

	$topic_path[] = '				<li><a href="' . get_bloginfo( 'url' ) . '/' . $post_type . '/">サービス</a></li>' . "\n";
	$topic_path[] = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>' . "\n";
	$topic_path = array_reverse ( $topic_path );
	$topic_path = implode ( $topic_path );
}

// css
$css = '';
if ( file_exists ( dirname ( __FILE__ ) . '/css/' . $post_type . '.css' ) )
	$css .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/' . $post_type . '.css">' . "\n";
if ( get_term_meta( $cr_term_id, 'service_style', true ) ) {
	$css .= '<style type="text/css">' . "\n";
	$css .= get_term_meta( $cr_term_id, 'service_style', true ) . "\n";
	$css .= '</style>' . "\n";
}

// js
$js = '';
if ( file_exists ( dirname ( __FILE__ ) . '/js/' . $post_type . '.js' ) )
	$js .= '<script src="' . get_bloginfo( 'template_url' ) . '/js/' . $post_type . '.js"></script>' . "\n";

get_header();
?>
	<article class="contents">
		<div class="cmn-visual"><div>
			<h1 class="cmn-visual__txt1"><?php echo get_term_meta( $cr_term_id, 'service_category_name', true ); ?></h1>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
<?php
ob_start ();
if ( $service_section = get_term_meta( $cr_term_id, 'service_section', true ) ) {
	$i = 0;
	foreach ( $service_section as $n => $type ) {
		if ( get_term_meta( $cr_term_id, 'service_section_' . $n . '_hide', true ) ) {
			$i++;
			continue;
		}

		if ( $type == 'imgr' ) {
?>
		<section class="mod-sec">
			<div class="mod-cols1">
				<div class="mod-cols1__col1">
					<h2 class="mod-hd1"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true ); ?></h2>
<?php echo wpautop( get_term_meta( $cr_term_id, 'service_section_' . $n . '_content', true ) ); ?>
				</div>
<?php
if ( $attachment_id = get_term_meta( $cr_term_id, 'service_section_' . $n . '_img', true ) ) {
	$src = wp_get_attachment_image_url( $attachment_id, 'full' );
?>
				<figure><img src="<?php echo $src; ?>" alt=""></figure>
<?php
}
?>
			</div>
		</section>
		<hr>
<?php
		} elseif ( $type == 'imgl' ) {
?>
		<section class="mod-sec">
			<div class="mod-cols1--reverse">
				<div class="mod-cols1__col1">
					<h2 class="mod-hd1"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true ); ?></h2>
<?php echo wpautop( get_term_meta( $cr_term_id, 'service_section_' . $n . '_content', true ) ); ?>
				</div>
<?php
if ( $attachment_id = get_term_meta( $cr_term_id, 'service_section_' . $n . '_img', true ) ) {
	$src = wp_get_attachment_image_url( $attachment_id, 'full' );
?>
				<figure><img src="<?php echo $src; ?>" alt=""></figure>
<?php
}
?>
			</div>
		</section>
		<hr>
<?php
		} elseif ( $type == 'free' ) {
?>
		<section class="mod-sec">
			<h2 class="mod-hd1 align-center"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true ); ?></h2>
			<div class="post-content">
<?php echo wpautop( get_term_meta( $cr_term_id, 'service_section_' . $n . '_content', true ) ); ?>
			</div>
		</section>
		<hr>
<?php
		} elseif ( $type == 'lineup' ) {
?>
		<section class="mod-sec" id="lineup">
			<h2 class="mod-hd1 align-center"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true ); ?></h2>
			<article class="service-items">
<?php
if ( $wp_query->have_posts() ) {
	while ( $wp_query->have_posts() ) {
		$wp_query->the_post();

		$thumbnail = '';
		if ( $attachment_id = get_post_meta( $post->ID, 'service_archive_img', true ) ) {
			$src = wp_get_attachment_image_url( $attachment_id, 'full' );
			$thumbnail = '<figure><img src="' . $src . '" alt=""></figure>';
		}

		$category_html = '';
		if ( $terms = get_the_terms( $post->ID, $post_type . '_category' ) ) {
			foreach ( $terms as  $term ) {
				if ( $term->parent ) {
					$category_html = '<div class="service-tag"><span>' . esc_html( $term->name ) . '</span></div>' . "\n";
					break;
				}
			}
		}
?>
				<div class="service-items__item<?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hide_btn', true ) ? ' hide_btn' : ''; ?>">
					<h2 class="service-items__item__title"><?php the_title(); ?></h2>
<?php echo $thumbnail; ?>
					<p class="service-items__item__copy"><?php echo get_post_meta( $post->ID, 'service_single_copy', true ); ?></p>
<?php echo $category_html; ?>
					<div class="service-items__item__description"><?php the_content(); ?></div>
					<div class="service-items__btn-more"><a href="<?php the_permalink(); ?>" class="mod-btn2"><span>View More</span></a></div>
				</div>
<?php
	}
	wp_reset_postdata();
}
?>
			</article>
		</section>
		<hr>
<?php
		} elseif ( $type == 'step' ) {
?>
		<section class="mod-sec service-sg-step">
			<h2 class="mod-hd1 align-center"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true ); ?></h2>
			<p class="mod-lead"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_description', true ); ?></p>
			<div class="service-sg-step__items">
<?php
if ( $nn = get_term_meta( $cr_term_id, 'service_section_' . $n . '_step', true ) ) {
	for ( $i = 0; $i < $nn; $i++ ) {
?>
				<dl>
					<dt>
						<div class="service-sg-step__num">STEP<?php echo $i + 1; ?></div>
						<div class="service-sg-step__title"><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_step_' . $i . '_hd', true ); ?></div>
					</dt>
					<dd>
						<p><?php echo get_term_meta( $cr_term_id, 'service_section_' . $n . '_step_' . $i . '_content', true ); ?></p>
					</dd>
				</dl>
<?php
	}
}
?>
			</div>
		</section>
		<hr>
<?php
		}
	}
}
$html = ob_get_clean ();
echo preg_replace ( '@<hr>([\n\s]*)$@', '', $html );
?>
	</article>
<?php get_footer(); ?>
