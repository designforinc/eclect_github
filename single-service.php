<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
$post_title = strip_tags ( get_the_title() );

// カテゴリ
$cr_term_id = '';
$cr_term_name = '';
$cr_term_slug = '';
$cr_sub_term_name = '';
$taxonomy = $post_type . '_category';
if ( $terms = get_the_terms( $post->ID, $taxonomy ) ) {
	foreach ( $terms as  $term ) {
		if ( $term->parent ) {
			$cr_sub_term_name = '			<div class="service-sg-header__tag"><div class="service-tag"><span>' . esc_html( $term->name ) . '</span></div></div>';
		} else {
			$cr_term = $term;
			$cr_term_id = esc_html( $cr_term->term_id );
			$cr_term_name = esc_html( $cr_term->name );
			$cr_term_slug = esc_html( $cr_term->slug );
		}
	}
}

// topic_path
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>
				<li><a href="' . get_bloginfo( 'url' ) . '/' . $post_type . '/">サービス</a></li>' . "\n";
if ( $cr_term_name )
	$topic_path .= '				<li><a href="' . get_term_link( $cr_term ) . '">' . $cr_term_name . '</a></li>' . "\n";
$topic_path .= '				<li>' . $post_title . '</li>' . "\n";

// css
$css = '';
if ( file_exists ( dirname ( __FILE__ ) . '/css/' . $post_type . '.css' ) )
	$css .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/' . $post_type . '.css">' . "\n";

// js
$js = '';
if ( file_exists ( dirname ( __FILE__ ) . '/js/' . $post_type . '.js' ) )
	$js .= '<script src="' . get_bloginfo( 'template_url' ) . '/js/' . $post_type . '.js"></script>' . "\n";

get_header();

// ラインナップタイトル取得
$lineup_hd = '';
if ( $service_section = get_term_meta( $cr_term_id, 'service_section', true ) ) {
	foreach ( $service_section as $n => $type ) {
		if ( $type == 'lineup' ) {
			$lineup_hd = get_term_meta( $cr_term_id, 'service_section_' . $n . '_hd', true );
			break;
		}
	}
}
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
		<div class="service-sg-header">
<?php
echo $cr_sub_term_name;

if ( $attachment_id = get_post_meta( $post->ID, 'service_single_img', true ) ) {
	$src = wp_get_attachment_image_url( $attachment_id, 'full' );
	echo '			<figure><img src="' . $src . '" alt=""></figure>' . "\n";
}
?>
			<p class="service-sg-header__txt1"><?php echo get_post_meta( $post->ID, 'service_single_copy', true ); ?></p>
			<p class="service-sg-header__txt2"><?php echo get_post_meta( $post->ID, 'service_single_sub_text', true ); ?></p>
		</div>
		<hr>
<?php
if ( $service_section = get_post_meta( $post->ID, 'service_single_section', true ) ) {
	foreach ( $service_section as $n => $type ) {
		if ( $type == 'imgr' ) {
?>
		<section class="mod-sec">
			<div class="mod-cols1">
				<div class="mod-cols1__col1">
					<h2 class="mod-hd1"><?php echo get_post_meta( $post->ID, 'service_single_section_' . $n . '_hd', true ); ?></h2>
<?php echo wpautop( get_post_meta( $post->ID, 'service_single_section_' . $n . '_content', true ) ); ?>
				</div>
<?php
if ( $attachment_id = get_post_meta( $post->ID, 'service_single_section_' . $n . '_img', true ) ) {
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
					<h2 class="mod-hd1"><?php echo get_post_meta( $post->ID, 'service_single_section_' . $n . '_hd', true ); ?></h2>
<?php echo wpautop( get_post_meta( $post->ID, 'service_single_section_' . $n . '_content', true ) ); ?>
				</div>
<?php
if ( $attachment_id = get_post_meta( $post->ID, 'service_single_section_' . $n . '_img', true ) ) {
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
		} elseif ( $type == 'img' ) {
?>
		<section class="mod-sec">
			<h2 class="mod-hd1"><?php echo get_post_meta( $post->ID, 'service_single_section_' . $n . '_hd', true ); ?></h2>
<?php echo wpautop( get_post_meta( $post->ID, 'service_single_section_' . $n . '_content', true ) ); ?>
<?php
if ( $attachment_id = get_post_meta( $post->ID, 'service_single_section_' . $n . '_img', true ) ) {
	$src = wp_get_attachment_image_url( $attachment_id, 'full' );
?>
			<figure class="mod-img1"><img src="<?php echo $src; ?>" alt=""></figure>
<?php
}
?>
		</section>
		<hr>
<?php
		}
	}
}
?>
		<div class="service-sg-btn-back"><a href="<?php echo get_term_link( $cr_term ); ?>#lineup" class="mod-btn2--back"><span><?php echo $lineup_hd; ?>に戻る</span></a></div>
	</article>
<?php get_footer(); ?>
<?php endwhile; endif; ?>
