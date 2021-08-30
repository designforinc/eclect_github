<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
$post_title = strip_tags ( get_the_title() );

// カテゴリ
$cr_term_name = '';
$cr_term_slug = '';
$taxonomy = $post_type . '_category';
if ( $terms = get_the_terms( $post->ID, $taxonomy ) ) {
	$terms = array_slice ( $terms, 0, 1 );
	$cr_term = $terms[0];
	$cr_term_name = esc_html( $cr_term->name );
	$cr_term_slug = esc_html( $cr_term->slug );
}

// topic_path
$topic_path = '				<li><a href="' . get_bloginfo( 'url' ) . '/">HOME</a></li>
				<li><a href="' . get_bloginfo( 'url' ) . '/' . $post_type . '/">セミナー</a></li>' . "\n";
//if ( $cr_term_name )
//	$topic_path .= '				<li>' . $cr_term_name . '</li>' . "\n";
//$topic_path .= '				<li>' . $post_title . '</li>' . "\n";

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
	<div class="contents">
		<div class="cmn-visual"><div>
			<div class="cmn-visual__txt1">セミナー</div>
		</div></div>
		<div class="cmn-topic-path">
			<ul>
<?php echo $topic_path; ?>
			</ul>
		</div>
		<article>
			<header class="mod-header">
				<div class="mod-header__row">
					<div class="mod-header__category"><?php echo $cr_term_name; ?></div>
					<div class="mod-header__date"><?php echo get_post_meta( $post->ID, 'seminar_date_time', true ); ?></div>
				</div>
				<h1 class="mod-header__title"><?php the_title(); ?></h1>
				<ul class="mod-share">
					<li class="mod-share__fb"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" rel="nofollow"><img src="<?php bloginfo( 'template_url' ); ?>/img/case/share_ico_fb.svg" alt="facebook"></a></li>
					<li class="mod-share__tw"><a href="https://twitter.com/intent/tweet?url=<?php echo urlencode ( get_the_permalink() ); ?>&amp;text=<?php echo urlencode ( get_the_title() ); ?>&amp;" target="_blank" rel="nofollow"><img src="<?php bloginfo( 'template_url' ); ?>/img/case/share_ico_tw.svg" alt="twitter"></a></li>
				</ul>
			</header>
			<div class="mod-sec post-content">
<?php the_content(); ?>
			</div>
			<hr>
<?php
if ( $n = get_post_meta( $post->ID, 'seminar_single_recommend', true ) ) {
?>
			<section class="mod-sec">
				<h2 class="mod-hd2">こんな方におすすめ</h2>
				<ul class="mod-check">
<?php
	for ( $i = 0; $i < $n; $i++ ) {
?>
					<li><?php echo get_post_meta( $post->ID, 'seminar_single_recommend_' . $i . '_text', true ); ?></li>
<?php
	}
?>
				</ul>
			</section>
			<hr>
<?php
}
?>
<?php
if ( $n = get_post_meta( $post->ID, 'seminar_single_outline', true ) ) {
?>
			<section class="mod-sec">
				<h2 class="mod-hd2">開催概要</h2>
				<table class="mod-table">
<?php
	for ( $i = 0; $i < $n; $i++ ) {
?>
					<tr>
						<th><?php echo get_post_meta( $post->ID, 'seminar_single_outline_' . $i . '_hd', true ); ?></th>
						<td><?php echo get_post_meta( $post->ID, 'seminar_single_outline_' . $i . '_contents', true ); ?></td>
					</tr>
<?php
	}
?>
				</table>
			</section>
			<hr>
<?php
}
?>
<?php
if ( $n = get_post_meta( $post->ID, 'seminar_single_attention', true ) ) {
?>
			<section class="mod-sec">
				<h2 class="mod-hd2">ご注意事項</h2>
				<table class="mod-table">
<?php
	for ( $i = 0; $i < $n; $i++ ) {
?>
					<tr>
						<th><?php echo get_post_meta( $post->ID, 'seminar_single_attention_' . $i . '_hd', true ); ?></th>
						<td><?php echo get_post_meta( $post->ID, 'seminar_single_attention_' . $i . '_contents', true ); ?></td>
					</tr>
<?php
	}
?>
				</table>
			</section>
			<hr>
<?php
}
?>
<?php
if ( $n = get_post_meta( $post->ID, 'seminar_single_program', true ) ) {
?>
			<section class="mod-sec">
				<h2 class="mod-hd2">プログラム</h2>
				<table class="mod-table">
<?php
	for ( $i = 0; $i < $n; $i++ ) {
?>
					<tr>
						<th><?php echo get_post_meta( $post->ID, 'seminar_single_program_' . $i . '_hd', true ); ?></th>
						<td><?php echo get_post_meta( $post->ID, 'seminar_single_program_' . $i . '_contents', true ); ?></td>
					</tr>
<?php
	}
?>
				</table>
			</section>
			<hr>
<?php
}
?>
<?php
if ( $n = get_post_meta( $post->ID, 'seminar_single_lecturer', true ) ) {
?>
			<section class="mod-sec">
				<h2 class="mod-hd2">講師情報</h2>
				<div class="seminar-sg-lecturers">
<?php
	for ( $i = 0; $i < $n; $i++ ) {
		$thumbnail = '';
		if ( $attachment_id = get_post_meta( $post->ID, 'seminar_single_lecturer_' . $i . '_img', true ) )
			$thumbnail = '<img src="' . wp_get_attachment_image_url( $attachment_id, 'm120x160' ) . '" alt="">';
		else
			$thumbnail = '<img src="' . get_bloginfo( 'template_url' ) . '/img/no_image_120x160.png" alt="">';
?>
					<div class="seminar-sg-lecturers__item">
						<figure><?php echo $thumbnail; ?></figure>
						<div class="seminar-sg-lecturers__content">
							<div class="seminar-sg-lecturers__name"><?php echo get_post_meta( $post->ID, 'seminar_single_lecturer_' . $i . '_name', true ); ?></div>
							<div class="seminar-sg-lecturers__company"><?php echo wpautop( get_post_meta( $post->ID, 'seminar_single_lecturer_' . $i . '_contents', true ) ); ?></div>
						</div>
					</div>
<?php
	}
?>
				</div>
			</section>
<?php
}
?>
			<div class="seminar-sg-lecturers__btn"><a href="<?php echo esc_attr( get_post_meta( $post->ID, 'seminar_single_application_url', true ) ); ?>" class="mod-btn1"<?php echo get_post_meta( $post->ID, 'seminar_single_application_target', true ) ? ' target="_blank"' : ''; ?>><span>お申し込みはこちら</span></a></div>
			<div class="mod-sec seminar-contact">
				<table class="mod-table">
					<tbody>
						<tr>
							<th>お問合せ窓口</th>
						</tr>
						<tr>
							<td>e-mail：info@eclect.co.jp<br>
							セミナー窓口：徳山</td>
						</tr>
					</tbody>
				</table>
			</div>
<?php
$prev_ID =  ( $prev_post = get_previous_post() ) ? $prev_post->ID : '';
$next_ID =  ( $next_post = get_next_post() ) ? $next_post->ID : '';
?>
			<div class="mod-adjacent">
<?php
if ( !empty ( $prev_ID ) ) {
?>
				<div class="mod-adjacent__prev"><a href="<?php echo get_permalink( $prev_ID ); ?>" class="mod-btn2"><img src="<?php bloginfo( 'template_url' ); ?>/img/cmn_ico_arrow2.svg" alt="前へ"></a></div>
<?php
} else {
?>
				<div class="mod-adjacent__prev">&nbsp;</div>
<?php
}
?>
				<div class="mod-adjacent__back"><a href="<?php bloginfo( 'url' ); ?>/seminar/" class="mod-btn2--back"><span>一覧に戻る</span></a></div>
<?php
if ( !empty ( $next_ID ) ) {
?>
				<div class="mod-adjacent__next"><a href="<?php echo get_permalink( $next_ID ); ?>" class="mod-btn2"><img src="<?php bloginfo( 'template_url' ); ?>/img/cmn_ico_arrow1.svg" alt="次へ"></a></div>
<?php
} else {
?>
				<div class="mod-adjacent__next">&nbsp;</div>
<?php
}
?>
			</div>
		</article>
	</div>
<?php get_footer(); ?>
<?php endwhile; endif; ?>
