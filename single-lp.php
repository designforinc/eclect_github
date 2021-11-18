<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
// css
$css = '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/jquery.visualAnimateJS.css">' . "\n";
if ( file_exists ( dirname ( __FILE__ ) . '/css/' . $post_type . '.css' ) )
	$css .= '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/css/' . $post_type . '.css">' . "\n";

// js
$js = '<script src="' . get_bloginfo( 'template_url' ) . '/js/jquery.visualAnimateJS.js"></script>' . "\n";
if ( file_exists ( dirname ( __FILE__ ) . '/js/' . $post_type . '.js' ) )
	$js .= '<script src="' . get_bloginfo( 'template_url' ) . '/js/' . $post_type . '.js"></script>' . "\n";

get_header();
?>
<article class="contents">
	<div class="cmn-visual">
		<picture>
			<source media="(min-width: 769px)" srcset="<?php
if ( $attachment_id = get_post_meta( $post->ID, 'lp_mv_pc', true ) ) {
	echo wp_get_attachment_image_url( $attachment_id, 'full' );
}
?>">
			<img src="<?php
if ( $attachment_id = get_post_meta( $post->ID, 'lp_mv_sp', true ) ) {
	echo wp_get_attachment_image_url( $attachment_id, 'full' );
}
?>"  alt="">
		</picture>
	</div>
<?php
if ( $section = get_post_meta( $post->ID, 'lp_section', true ) ) {
	foreach ( $section as $n => $type ) {
		if ( $type == 'cta' ) {
			if ( !get_post_meta( $post->ID, 'lp_section_' . $n . '_display', true ) ) {
?>
	<section class="section cta<?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_bg_color', true ) ? ' lp-bg' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_top', true ) ? ' lp-pt0' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_bottom', true ) ? ' lp-pb0' : ''; ?>">
<?php
if ( $meta = get_post_meta( $post->ID, 'lp_section_' . $n . '_heading', true ) ) {
?>
		<h2 class="lp-hd"><?php echo $meta; ?></h2>
<?php
}
?>
		<div class="cta__btn"><a href="<?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_button_url', true ); ?>" class="mod-btn1"><span><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_button_text', true ); ?></span></a></div>
	</section>
<?php
			}
		} else if ( $type == 'free' ) {
			if ( !get_post_meta( $post->ID, 'lp_section_' . $n . '_display', true ) ) {
?>
	<section class="section free<?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_bg_color', true ) ? ' lp-bg' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_width', true ) ? ' lp-w736' : ' lp-w1120'; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_top', true ) ? ' lp-pt0' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_bottom', true ) ? ' lp-pb0' : ''; ?>"><div>
<?php
if ( $meta = get_post_meta( $post->ID, 'lp_section_' . $n . '_heading', true ) ) {
?>
		<h2 class="lp-hd"><?php echo $meta; ?></h2>
<?php
}
?>
<?php
if ( $meta = get_post_meta( $post->ID, 'lp_section_' . $n . '_sub_heading', true ) ) {
?>
		<div class="lp-sub"><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_sub_heading', true ); ?></div>
<?php
}
?>
		<div class="post-content">
<?php echo wpautop( get_post_meta( $post->ID, 'lp_section_' . $n . '_body', true ) ); ?>
		</div>
	</div></section>
<?php
			}
		} else if ( $type == 'works' ) {
			if ( !get_post_meta( $post->ID, 'lp_section_' . $n . '_display', true ) ) {
?>
	<section class="section works<?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_bg_color', true ) ? ' lp-bg' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_top', true ) ? ' lp-pt0' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_bottom', true ) ? ' lp-pb0' : ''; ?>"><div>
		<h2 class="lp-hd">サポート実績企業</h2>
		<div class="lp-sub">〜300社を超える幅広い業種や業界のお客様を支援〜</div>
		<div class="slider pc-only">
			<div class="visual-animate">
				<ul class="visual-animate__slider">
					<li>
						<div class="works-items">
							<div><a href="https://i-ne.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ine.png" alt="株式会社Ｉ－ｎｅ"></a></div>
							<div><a href="https://www.ill.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ill.png" alt="株式会社アイル"></a></div>
							<div><a href="https://eplus.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_eplus-160x80.png" alt="株式会社イープラス"></a></div>
							<div><a href="https://www.uchida.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_uchida.png" alt="株式会社内田洋行"></a></div>
							<div><a href="https://www.airtrip.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_eatori.png" alt="株式会社エアトリ"></a></div>
							<div><a href="https://www.nttpc.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ntt.png" alt="株式会社エヌ・ティ・ティピー・シーコミュニケーションズ"></a></div>
							<div><a href="https://www.nttls.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_nttls.png" alt="エヌ・ティ・ティラーニングシステムズ株式会社"></a></div>
							<div><a href="https://mtame.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_mtame.png" alt="Mtame株式会社"></a></div>
							<div><a href="http://www.kyoto-u.ac.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_kyoto.png" alt="京都大学"></a></div>
							<div><a href="https://www.godiva.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_godiva-160x80.png" alt="ゴディバ ジャパン株式会社"></a></div>
							<div><a href="https://classi.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/08/achievements_logo_classi-160x80.png" alt="Classi株式会社"></a></div>
							<div><a href="https://satori.marketing/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_satori.png" alt="SATORI株式会社"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.gmocloud.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_gmocloud-160x80.png" alt="GMOグローバルサイン・ホールディングス株式会社"></a></div>
							<div><a href="https://lp.snaq.me/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_snaqme.png" alt="株式会社スナックミー"></a></div>
							<div><a href="https://www.tepco.co.jp/ep/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_tepco.png" alt="東京電力エナジーパートナー株式会社"></a></div>
							<div><a href="https://www.telecomsquare.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/04/achievements_logo_telecomdquare-160x80.png" alt="株式会社テレコムスクエア"></a></div>
							<div><a href="https://nohara-inc.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_nohara.png" alt="野原ホールディングス株式会社"></a></div>
							<div><a href="https://www.bizreach.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/12/achievements_logo_bizreach-160x80.png" alt="株式会社ビズリーチ"></a></div>
							<div><a href="https://www.fujifilm.com/jp/ja" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_fujifilm.png" alt="富士フイルム株式会社"></a></div>
							<div><a href="https://www.happy-bears.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_bears-160x80.png" alt="株式会社ベアーズ"></a></div>
							<div><a href="https://www.hokende.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_hokenichiba.png" alt="株式会社アドバンスクリエイト"></a></div>
							<div><a href="https://www.mcdonalds.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/04/achievements_logo_mcdonalds-160x80.png" alt="日本マクドナルド株式会社"></a></div>
							<div><a href="https://moneyforward.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_moneyforward.png" alt="株式会社マネーフォワード"></a></div>
							<div><a href="https://mixi.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_mixi.png" alt="株式会社ミクシィ"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.yazuya.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_yazuya.png" alt="株式会社やずや"></a></div>
							<div><a href="http://www.ritsumei.ac.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ritsumei.png" alt="立命館大学"></a></div>
							<div><a href="https://loadstarcapital.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_loadstarcapital.png" alt="ロードスターキャピタル株式会社"></a></div>
						</div>
					</li>
				</ul>
				<div class="visual-animate__btn-prev"><div><span></span></div></div>
				<div class="visual-animate__btn-next"><div><span></span></div></div>
			</div>
		</div>
		<div class="slider sp-only">
			<div class="visual-animate">
				<ul class="visual-animate__slider">
					<li>
						<div class="works-items">
							<div><a href="https://i-ne.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ine.png" alt="株式会社Ｉ－ｎｅ"></a></div>
							<div><a href="https://www.ill.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ill.png" alt="株式会社アイル"></a></div>
							<div><a href="https://eplus.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_eplus-160x80.png" alt="株式会社イープラス"></a></div>
							<div><a href="https://www.uchida.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_uchida.png" alt="株式会社内田洋行"></a></div>
							<div><a href="https://www.airtrip.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_eatori.png" alt="株式会社エアトリ"></a></div>
							<div><a href="https://www.nttpc.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ntt.png" alt="株式会社エヌ・ティ・ティピー・シーコミュニケーションズ"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.nttls.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_nttls.png" alt="エヌ・ティ・ティラーニングシステムズ株式会社"></a></div>
							<div><a href="https://mtame.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_mtame.png" alt="Mtame株式会社"></a></div>
							<div><a href="http://www.kyoto-u.ac.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_kyoto.png" alt="京都大学"></a></div>
							<div><a href="https://www.godiva.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_godiva-160x80.png" alt="ゴディバ ジャパン株式会社"></a></div>
							<div><a href="https://classi.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/08/achievements_logo_classi-160x80.png" alt="Classi株式会社"></a></div>
							<div><a href="https://satori.marketing/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_satori.png" alt="SATORI株式会社"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.gmocloud.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_gmocloud-160x80.png" alt="GMOグローバルサイン・ホールディングス株式会社"></a></div>
							<div><a href="https://lp.snaq.me/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_snaqme.png" alt="株式会社スナックミー"></a></div>
							<div><a href="https://www.tepco.co.jp/ep/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_tepco.png" alt="東京電力エナジーパートナー株式会社"></a></div>
							<div><a href="https://www.telecomsquare.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/04/achievements_logo_telecomdquare-160x80.png" alt="株式会社テレコムスクエア"></a></div>
							<div><a href="https://nohara-inc.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_nohara.png" alt="野原ホールディングス株式会社"></a></div>
							<div><a href="https://www.bizreach.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/12/achievements_logo_bizreach-160x80.png" alt="株式会社ビズリーチ"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.fujifilm.com/jp/ja" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_fujifilm.png" alt="富士フイルム株式会社"></a></div>
							<div><a href="https://www.happy-bears.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/03/achievements_logo_bears-160x80.png" alt="株式会社ベアーズ"></a></div>
							<div><a href="https://www.hokende.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_hokenichiba.png" alt="株式会社アドバンスクリエイト"></a></div>
							<div><a href="https://www.mcdonalds.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2021/04/achievements_logo_mcdonalds-160x80.png" alt="日本マクドナルド株式会社"></a></div>
							<div><a href="https://moneyforward.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_moneyforward.png" alt="株式会社マネーフォワード"></a></div>
							<div><a href="https://mixi.co.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_mixi.png" alt="株式会社ミクシィ"></a></div>
						</div>
					</li>
					<li>
						<div class="works-items">
							<div><a href="https://www.yazuya.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_yazuya.png" alt="株式会社やずや"></a></div>
							<div><a href="http://www.ritsumei.ac.jp/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_ritsumei.png" alt="立命館大学"></a></div>
							<div><a href="https://loadstarcapital.com/" target="_blank" rel="noopener noreferrer"><img src="https://eclect.co.jp/wp-content/uploads/2020/10/achievements_logo_loadstarcapital.png" alt="ロードスターキャピタル株式会社"></a></div>
						</div>
					</li>
				</ul>
				<div class="visual-animate__btn-prev"><div><span></span></div></div>
				<div class="visual-animate__btn-next"><div><span></span></div></div>
			</div>
		</div>
	</div></section>
<?php
			}
		} elseif ( $type == 'case' ) {
			if ( !get_post_meta( $post->ID, 'lp_section_' . $n . '_display', true ) ) {
?>
	<section class="section case<?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_bg_color', true ) ? ' lp-bg' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_top', true ) ? ' lp-pt0' : ''; ?><?php echo get_post_meta( $post->ID, 'lp_section_' . $n . '_padding_bottom', true ) ? ' lp-pb0' : ''; ?>"><div>
		<h2 class="lp-hd">導入事例</h2>
		<div class="lp-sub">〜エクレクト×ZendeskでCX向上を実現したお客様の声をご紹介します〜</div>
		<div class="case-items">
			<div class="visual-animate">
				<ul class="visual-animate__slider">
<?php
if ( $related_posts = get_post_meta( $post->ID, 'lp_section_' . $n . '_relation', true ) ) {
	foreach ( $related_posts as $related_post_id ) {
		$category = '';
		if ( $terms = get_the_terms( $related_post_id, 'case_category' ) ) {
			$term = array_slice ( $terms, 0, 1 );
			$term = $term[0];
			$category = esc_html( $term->name );
		}

		$thumbnail = '';
		if ( $attachment_id = get_post_meta( $related_post_id, 'case_thumbnail', true ) )
			$thumbnail = '<img src="' . wp_get_attachment_image_url( $attachment_id, 'm652x356' ) . '" alt="">';
		else
			$thumbnail = '<img src="' . get_bloginfo( 'template_url' ) . '/img/no_image_652x356.png" alt="">';
?>
					<li>
						<figure><?php echo $thumbnail; ?></figure>
						<div class="case-items__item__tag"><?php echo $category; ?></div>
						<div class="case-items__item__inner">
							<p class="case-items__item__title"><?php echo get_the_title( $related_post_id ); ?></p>
							<p class="case-items__item__company"><?php echo get_post_meta( $related_post_id, 'case_company', true ); ?></p>
							<div class="case-items__item__btn"><a href="<?php echo get_the_permalink( $related_post_id ); ?>" class="mod-btn2"><span>View More</span></a></div>
						</div>
					</li>
<?php
	}
}
?>
				</ul>
				<div class="visual-animate__btn-prev"><div><span></span></div></div>
				<div class="visual-animate__btn-next"><div><span></span></div></div>
			</div>
		</div>
	</div></section>
<?php
			}
		}
	}
}
?>
</article>
<?php get_footer(); ?>
<?php endwhile; endif; ?>
