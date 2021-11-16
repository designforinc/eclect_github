<?php
/**
# ------------------------------
# はじめに
# ------------------------------
 * ヘッダの不要なタグを除去
 * 管理バー表示時のズレを解消

# ------------------------------
# ACF
# ------------------------------
 * 設定を取得

# ------------------------------
# エディタ
# ------------------------------
 * ビジュアルエディタをカスタマイズ

# ------------------------------
# カスタム投稿
# ------------------------------
 * メインクエリの変更
 * カスタム投稿タイプ・カスタム分類の設定
 * カスタム投稿タイプのリライトルールを作成
 * カスタム投稿タイプ・カスタム分類の追加
 * カスタム投稿タイプのパーマリンクを変更する

# ------------------------------
# カスタム分類
# ------------------------------
 * カスタム分類のパーマリンクを変更する

# ------------------------------
# 固定ページ
# ------------------------------
 * 固定ページ一覧のカラムをカスタマイズ

# ------------------------------
# 投稿
# ------------------------------
 * 投稿一覧のカラムをカスタマイズ

# ------------------------------
# タクソノミー
# ------------------------------
 * ターム一覧のカラムをカスタマイズ

# ------------------------------
# メディア
# ------------------------------
 * メディアサイズを追加する
 * リサイズ時の圧縮率を変更する

# ------------------------------
# メソッド
# ------------------------------
 * ループ中の条件分岐
 * ファイル情報を取得する

# ------------------------------
# テーマ
# ------------------------------
 * アイキャッチを使用する
 * ショートコード

# ------------------------------
# その他
# ------------------------------
 * Ajax
*/


# ------------------------------
# はじめに
# ------------------------------


/**
 * ヘッダの不要なタグを除去
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );


/**
 * 管理バー表示時のズレを解消
 */
function mytheme_admin_bar() {
	echo '<style type="text/css" media="screen">
	html { margin-top: 0px !important; }
	* html body { margin-top: 0px !important; }
	@media screen and ( max-width: 782px ) {
		html { margin-top: 0px !important; }
		* html body { margin-top: 0px !important; }
	}
</style>';
}
add_action( 'wp_head', 'mytheme_admin_bar', 11 );


# ------------------------------
# ACF
# ------------------------------


/**
 * 設定を取得
 */
$mytheme_acf_init_post_type = array ();
$mytheme_acf_init_taxonomy = array ();
if ( $mytheme_acf_field_groups = get_posts( array ( 'post_type' => 'acf-field-group', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) ) {
	foreach ( $mytheme_acf_field_groups as $post_group ) {
		$mytheme_acf_rule = unserialize ( $post_group->post_content );
		$mytheme_acf_rule = $mytheme_acf_rule['location'];

		if ( $mytheme_acf_fields = get_posts( array ( 'post_type' => 'acf-field', 'post_parent' => $post_group->ID, 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) ) {
			$mytheme_acf_arr = array ();
			foreach ( $mytheme_acf_fields as $post_field ) {
				$options = unserialize ( $post_field->post_content );
				$choices = ( !empty ( $options['choices'] ) ) ? $options['choices'] : '';
				$mytheme_acf_arr[] = array (
					'label' => $post_field->post_title,
					'name' => $post_field->post_excerpt,
					'type' => $options['type'],
					'choices' => $choices,
				);
				// 2階層まで対応
				if ( $options['type'] == 'group' ) {
					if ( $fields = get_posts( array ( 'post_type' => 'acf-field', 'posts_per_page' => -1, 'post_parent' => (int) $post_field->ID, 'orderby' => 'menu_order', 'order' => 'ASC' ) )  ) {
						foreach ( $fields as $post_field_child ) {
							$options = unserialize ( $post_field_child->post_content );
							$choices = ( !empty ( $options['choices'] ) ) ? $options['choices'] : '';
							$mytheme_acf_arr[] = array (
								'label' => $post_field_child->post_title,
								'name' => $post_field->post_excerpt . '_' . $post_field_child->post_excerpt,
								'type' => $options['type'],
								'choices' => $choices,
							);
						}
					}
				}
			}
			foreach ( $mytheme_acf_rule as $row ) {
				if ( $row[0]['param'] == 'post_type' )
					$mytheme_acf_init_post_type[$row[0]['value']][] = $mytheme_acf_arr;
				elseif ( $row[0]['param'] == 'ef_taxonomy' || $row[0]['param'] == 'taxonomy' )
					$mytheme_acf_init_taxonomy[$row[0]['value']][] = $mytheme_acf_arr;
			}
		}
	}
}


# ------------------------------
# エディタ
# ------------------------------


/**
 * ビジュアルエディタをカスタマイズ
 */
function mytheme_custom_editor_settings( $initArray ) {
	global $allowedposttags, $post_type;

	// ビジュアルエディタにクラスを付与
	$initArray['body_class'] = 'post-content';

	// フォーマットの設定
	//$initArray['block_formats'] = "段落=p; 中見出し=h3; 小見出し=h4; 整形済みテキスト=pre";

	// クリーンアップ機能を停止
	$initArray['verify_html'] = false;

	// 除去しないタグを指定
	#$initArray['valid_children'] = '+body[style],+div[div|span],+span[span]';
	$initArray['valid_children'] = '+a[' . implode ( '|', array_keys ( $allowedposttags ) ) . ']';

	// インデントを有効化
	$initArray['indent'] = true;

	if ( $post_type == 'page' ) {
		// wpautopを停止
		$initArray['wpautop'] = false;
	}

	// 改行してもpタグを挿入させない
	$initArray['force_p_newlines'] = false;

	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'mytheme_custom_editor_settings' );

function mytheme_switch_editor_style() {
	global $post;
	add_editor_style( 'editor-style.css' );
	add_editor_style( get_bloginfo( 'template_url' ) . '/css/post-content.css' );
	add_editor_style( get_bloginfo( 'template_url' ) . '/css/module.css' );
	if ( $post->post_type == 'page' ) {
		if ( file_exists ( dirname ( __FILE__ ) . '/css/' . get_post_field( 'post_name', $post->ID ) . '.css' ) )
			add_editor_style( get_bloginfo( 'template_url' ) . '/css/' . get_post_field( 'post_name', $post->ID ) . '.css' );
	} elseif ( $post->post_type == 'lp' ) {
		add_editor_style( get_bloginfo( 'template_url' ) . '/css/post-content-lp.css' );
	}
}
add_action( 'admin_head-post.php', 'mytheme_switch_editor_style' );
add_action( 'admin_head-post-new.php', 'mytheme_switch_editor_style' );

// post-content.cssを詳細ページで使用する
function mytheme_enqueue_post_content_style() {
	if ( is_single() ) {
		wp_enqueue_style( 'post-content', get_bloginfo( 'template_url' ) . '/css/post-content.css' );

		if ( get_post_type( get_the_ID() ) == 'lp' ) {
			wp_enqueue_style( 'post-content-lp', get_bloginfo( 'template_url' ) . '/css/post-content-lp.css' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_post_content_style' );


# ------------------------------
# カスタム投稿
# ------------------------------

/**
 * メインクエリの変更
 */
function mytheme_pre_get_posts( $query ) {
	global $post_type_arr;
	if ( $query->is_main_query() ) {
		if ( !is_admin() ) {
			foreach ( $post_type_arr as $post_type => $post_args ) {
				if ( is_post_type_archive( $post_type ) ) {
					$query->set( 'posts_per_page', $post_args['posts_per_page'] );
				}
				if ( is_array ( $post_args['taxonomy'] ) ) {
					foreach ( $post_args['taxonomy'] as $tax_name => $tax_args ) {
						if ( is_tax( $tax_name ) ) {
							$query->set( 'posts_per_page', $tax_args['posts_per_page'] );
						}
					}
				}
			}
		}
	}
}
add_action( 'pre_get_posts', 'mytheme_pre_get_posts' );


/**
 * カスタム投稿タイプ・カスタム分類の設定
 * supports: 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats'
 */
$post_type_arr = array (
	'case' => array (
		'name' => '導入事例',
		'slug' => '',
		'menu_position' => 6,
		'front' => '',
		'back' => '',
		'supports' => array ( 'title', 'editor' ),
		'permalink_structure' => '',
		'has_archive' => true,
		'posts_per_page' => -1,
		'taxonomy' => array (
			'case_category' => array (
				'type' => 'category',
				'name' => 'カテゴリ',
				'front' => '',
				'ancestors' => false,
				'public' => false,
				'posts_per_page' => -1,
			),
		),
	),
	'service' => array (
		'name' => 'サービス',
		'slug' => '',
		'menu_position' => 7,
		'front' => '',
		'back' => '',
		'supports' => array ( 'title', 'editor' ),
		'permalink_structure' => '',
		'has_archive' => true,
		'posts_per_page' => -1,
		'taxonomy' => array (
			'service_category' => array (
				'type' => 'category',
				'name' => 'カテゴリ',
				'front' => 'category',
				'ancestors' => false,
				'public' => true,
				'posts_per_page' => -1,
			),
		),
	),
	'seminar' => array (
		'name' => 'セミナー',
		'slug' => '',
		'menu_position' => 8,
		'front' => '',
		'back' => '',
		'supports' => array ( 'title', 'editor' ),
		'permalink_structure' => '',
		'has_archive' => true,
		'posts_per_page' => -1,
		'taxonomy' => array (
			'seminar_category' => array (
				'type' => 'category',
				'name' => 'カテゴリ',
				'front' => 'category',
				'ancestors' => false,
				'public' => false,
				'posts_per_page' => -1,
			),
		),
	),
	'news' => array (
		'name' => 'お知らせ',
		'slug' => '',
		'menu_position' => 9,
		'front' => '',
		'back' => '',
		'supports' => array ( 'title', 'editor' ),
		'permalink_structure' => '',
		'has_archive' => true,
		'posts_per_page' => -1,
		'taxonomy' => array (
			'news_category' => array (
				'type' => 'category',
				'name' => 'カテゴリ',
				'front' => 'category',
				'ancestors' => false,
				'public' => true,
				'posts_per_page' => -1,
			),
		),
	),
	'support' => array (
		'name' => 'サポート実績企業',
		'slug' => '',
		'menu_position' => 10,
		'front' => '',
		'back' => '',
		'supports' => array ( 'title' ),
		'permalink_structure' => '',
		'has_archive' => false,
		'posts_per_page' => -1,
		'taxonomy' => '',
	),
);

$custom_post_types = array ();
foreach ( $post_type_arr as $key => $val )
	$custom_post_types[] = $key;


/**
 * カスタム投稿タイプのリライトルールを作成
 */
function mytheme_custom_rewrite_rules() {
	global $wp_rewrite, $post_type_arr;

	$new_rules = array ();

	if ( $post_type_arr ) {
		foreach ( $post_type_arr as $post_type => $post_args ) {
			if ( $post_args['has_archive'] ) {
				$base_dir = !empty ( $post_args['front'] ) ? $post_args['front'] . '/' : '';
				if ( $post_args['slug'] )
					$base_dir .= $post_args['slug'];
				else
					$base_dir .= $post_type;
				$base_dir .= !empty ( $post_args['back'] ) ? '/' . $post_args['back'] : '';

				$new_rules[$base_dir . '/?$']								= 'index.php?post_type=' . $post_type;
				$new_rules[$base_dir . '/feed/(feed|rdf|rss|rss2|atom)/?$']	= 'index.php?post_type=' . $post_type . '&feed=$matches[1]';
				$new_rules[$base_dir . '/(feed|rdf|rss|rss2|atom)/?$']		= 'index.php?post_type=' . $post_type . '&feed=$matches[1]';
				$new_rules[$base_dir . '/page/?([0-9]{1,})/?$']				= 'index.php?post_type=' . $post_type . '&paged=$matches[1]';

				if ( is_array ( $post_args['taxonomy'] ) ) {
					$tax_front = '';
					foreach ( $post_args['taxonomy'] as $tax_args )
						$tax_front .= '/' . $tax_args['front'] . '/|';
					$tax_front = rtrim ( $tax_front, '|' );
					foreach ( $post_args['taxonomy'] as $tax_name => $tax_args ) {
						if ( $tax_args['public'] ) {
							if ( $tax_args['front'] )
								$new_rules[$base_dir . '/' . $tax_args['front'] . '(/([^/]+))+?(/page/([0-9]+))?/?$'] = 'index.php?post_type=' . $post_type . '&' . $tax_name . '=$matches[2]&paged=$matches[4]';
							else
								$new_rules[$base_dir . '(?!(' . ( $tax_front ? $tax_front . '|' : '' ) . '/date/|/[0-9]+.html))' . ( $tax_args['front'] ? '/' . $tax_args['front'] : '' ) . '(/([^/]+))+?(/page/([0-9]+))?/?$'] = 'index.php?post_type=' . $post_type . '&' . $tax_name . '=$matches[3]&paged=$matches[5]';
						}
					}
				}
			}
		}
	}

	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
//	flush_rewrite_rules();
	#$rules = $wp_rewrite->rules; foreach ( $rules as $regex => $rule ) echo $regex . " => " . $rule . '<br />';
}
#add_action( 'generate_rewrite_rules', 'mytheme_custom_rewrite_rules' );

function mytheme_post_rewrite_rules( $post_rewrite ) {
	global $post_type_arr;

	if ( $post_type_arr ) {
		$add_post_rewrite = array ();

		foreach ( $post_type_arr as $post_type => $post_args ) {
			$base_dir = !empty ( $post_args['front'] ) ? $post_args['front'] . '/' : '';
			if ( $post_args['slug'] )
				$base_dir .= $post_args['slug'];
			else
				$base_dir .= $post_type;
			$base_dir .= !empty ( $post_args['back'] ) ? '/' . $post_args['back'] : '';
			foreach ( $post_rewrite as $regex => $rule )
				$add_post_rewrite[$base_dir . '/' . $regex] = str_replace ( 'index.php?', 'index.php?post_type=' . $post_type . '&', $rule );
		}

		$post_rewrite = array_merge ( $add_post_rewrite, $post_rewrite );
	}

	#foreach ( $post_rewrite as $regex => $rule ) echo $regex . " => " . $rule . '<br />';
	return $post_rewrite;
}
#add_filter( 'post_rewrite_rules', 'mytheme_post_rewrite_rules' );

function mytheme_date_rewrite_rules( $post_rewrite ) {
	global $post_type_arr;

	if ( $post_type_arr ) {
		$add_post_rewrite = array ();

		foreach ( $post_type_arr as $post_type => $post_args ) {
			$base_dir = !empty ( $post_args['front'] ) ? $post_args['front'] . '/' : '';
			if ( $post_args['slug'] )
				$base_dir .= $post_args['slug'];
			else
				$base_dir .= $post_type;
			$base_dir .= !empty ( $post_args['back'] ) ? '/' . $post_args['back'] : '';
			foreach ( $post_rewrite as $regex => $rule )
				$add_post_rewrite[$base_dir . '/' . $regex] = str_replace ( 'index.php?', 'index.php?post_type=' . $post_type . '&', $rule );
		}

		$post_rewrite = array_merge ( $add_post_rewrite, $post_rewrite );
	}

	#foreach ( $post_rewrite as $regex => $rule ) echo $regex . " => " . $rule . '<br />';
	return $post_rewrite;
}
#add_filter( 'date_rewrite_rules', 'mytheme_date_rewrite_rules' );


/**
 * カスタム投稿タイプ・カスタム分類の追加
 * - wp-includes/post.php
 * - wp-includes/taxonomy.php
 */
function mytheme_register_post_type_and_taxonomy() {
	global $post_type_arr;

	if ( $post_type_arr ) {
		foreach ( $post_type_arr as $post_type => $post_args ) {
			/** カスタム投稿タイプ */
			$labels = array (
				'name' => $post_args['name'],
				'singular_name' => $post_args['name'],
				'add_new' => '新規追加',
				'add_new_item' => '記事を追加する',
				'edit_item' => '記事を編集する',
				'new_item' => '新しい記事',
				'view_item' => '記事を表示する',
				'search_items' => '記事を検索する',
				'not_found' => '記事はありません。',
				'not_found_in_trash' => 'ゴミ箱に記事はありません。', 
				'parent_item_colon' => '',
			);
			$args = array (
				'labels' => $labels,
				'public' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => $post_args['menu_position'],
				'has_archive' => $post_args['has_archive'],
				'supports' => $post_args['supports'],
			); 
			register_post_type( $post_type, $args );

			if ( is_array ( $post_args['taxonomy'] ) ) {
				foreach ( $post_args['taxonomy'] as $tax_name => $tax_args ) {
					if ( $tax_args['type'] == 'category' ) {
						/** カスタム分類 */
						$args = array (
							'label' => $tax_args['name'],
							'labels' => array (
								'name' => $tax_args['name'],
								'singular_name' => $tax_args['name'],
								'search_items' => '検索する',
								'popular_items' => 'よく使われている' . $tax_args['name'],
								'all_items' => 'すべての' . $tax_args['name'],
								'parent_item' => '親を選択する',
								'edit_item' => '編集する',
								'update_item' => '更新する',
								'add_new_item' => '追加する',
								'new_item_name' => '新しい' . $tax_args['name'] . 'の名前' ),
							'public' => $tax_args['public'],
							'show_ui' => true,
							'hierarchical' => true,
							'rewrite' => false
						);
						register_taxonomy( $tax_name, $post_type, $args );
					} elseif ( $tax_args['type'] == 'tag' ) {
						$args = array (
							'label' => $tax_args['name'],
							'labels' => array (
								'name' => $tax_args['name'],
								'singular_name' => $tax_args['name'],
								'search_items' => '検索する',
								'popular_items' => 'よく使われている' . $tax_args['name'],
								'all_items' => 'すべての' . $tax_args['name'],
								'edit_item' => '編集する',
								'update_item' => '更新する',
								'add_new_item' => '追加する',
								'new_item_name' => '新しい' . $tax_args['name'] . 'の名前',
								'choose_from_most_used' => 'よく使われている' . $tax_args['name'] . 'から選ぶ' ),
							'public' => true,
							'show_ui' => true,
							'hierarchical' => false,
							'show_tagcloud' => true,
							'rewrite' => false
						);
						register_taxonomy( $tax_name, $post_type, $args );
					}
				}
			}
		}
	}
}
#add_action( 'init', 'mytheme_register_post_type_and_taxonomy', 1 );


/**
 * カスタム投稿タイプのパーマリンクを変更する
 * - wp-includes/link-template.php
 */
function mytheme_post_type_link( $post_link, $post ) {
	global $wp_rewrite, $custom_post_types, $post_type_arr;

	if ( in_array ( $post->post_type, $custom_post_types ) && $post->post_status == 'publish' ) {
		$custom_permalink = '';
		if ( $arr = get_option( 'permalink-manager-uris' ) ) {
			// Permalink Manager Lite
			if ( !empty ( $arr[(int) $post->ID] ) )
				$custom_permalink = $arr[(int) $post->ID];
		} else {
			// Custom Permalinks
			$custom_field_arr = get_post_custom( (int) $post->ID );
			$custom_permalink = $custom_field_arr['custom_permalink'][0];
		}
		if ( empty ( $custom_permalink ) ) {
			if ( $permalink_structure = $wp_rewrite->permalink_structure ) {
				if ( $post_type_arr[$post->post_type]['permalink_structure'] )
					$permalink_structure = $post_type_arr[$post->post_type]['permalink_structure'];
				$post_link = $permalink_structure;
				list ( $year, $monthnum, $day, $h, $i, $s ) = sscanf ( $post->post_date, '%d-%d-%d %d:%d:%d' );
				if ( preg_match_all ( '@%([^%]+)%@', $permalink_structure, $result ) ) {
					foreach ( $result[1] as $row ) {
						switch ( $row ) {
							case 'postname' :
								$post_link = preg_replace ( '@%postname%@', $post->post_name, $post_link );
								break;

							case 'post_id' :
								$post_link = preg_replace ( '@%post_id%@', (int) $post->ID, $post_link );
								break;

							case 'year' :
								$post_link = preg_replace ( '@%year%@', $year, $post_link );
								break;

							case 'monthnum' :
								$post_link = preg_replace ( '@%monthnum%@', $monthnum, $post_link );
								break;

							case 'day' :
								$post_link = preg_replace ( '@%day%@', $day, $post_link );
								break;

							case in_array ( $row, get_taxonomies() ) :
								if ( $term = get_the_terms( (int) $post->ID, $row ) ) {
									$tax_ancestors = get_ancestors( (int) $term[0]->term_id, $row );
									$tax_ancestors = array_reverse ( $tax_ancestors );
									$hierarchy = '';
									foreach ( $tax_ancestors as $val )
										$hierarchy .= esc_html( get_term_by( 'id', $val, $row )->slug ) . '/';
									$hierarchy .= esc_html( $term[0]->slug );
									$post_link = preg_replace ( '@%' . $row . '%@', $hierarchy, $post_link );
								} else {
									$post_link = preg_replace ( '@%' . $row . '%/@', '', $post_link );
								}
								break;
						}
					}
				}

				$base_dir = !empty ( $post_type_arr[$post->post_type]['front'] ) ? $post_type_arr[$post->post_type]['front'] . '/' : '';
				if ( $post_type_arr[$post->post_type]['slug'] )
					$base_dir .= $post_type_arr[$post->post_type]['slug'];
				else
					$base_dir .= $post->post_type;
				$base_dir .= !empty ( $post_type_arr[$post->post_type]['back'] ) ? '/' . $post_type_arr[$post->post_type]['back'] : '';

				$post_link = home_url( $base_dir . $post_link );
			}
		}
	}

	return $post_link;
}
#add_filter( 'post_type_link', 'mytheme_post_type_link', 99, 2 );


# ------------------------------
# カスタム分類
# ------------------------------


/**
 * カスタム分類のパーマリンクを変更する
 * - wp-includes/taxonomy.php
 */
function mytheme_term_link( $termlink, $term, $taxonomy ) {
	global $post_type_arr;

	if ( $taxonomy == 'category' ) {
	} elseif ( $taxonomy == 'tag' ) {
	} else {
		if ( $post_type_arr ) {
			foreach ( $post_type_arr as $post_type => $settings ) {
				if ( is_array ( $settings['taxonomy'] ) && array_key_exists ( $taxonomy, $settings['taxonomy'] ) ) {
					$termlink = !empty ( $settings['front'] ) ? $settings['front'] . '/' : '';

					if ( $post_type_arr[$post_type]['slug'] )
						$termlink .= $post_type_arr[$post_type]['slug'];
					else
						$termlink .= $post_type;

					$termlink .= !empty ( $settings['back'] ) ? '/' . $settings['back'] : '';
					if ( !empty ( $post_type_arr[$post_type]['taxonomy'][$taxonomy]['front'] ))
						$termlink .= '/' . $post_type_arr[$post_type]['taxonomy'][$taxonomy]['front'];
					$termlink .= '/';

					if ( !empty ( $post_type_arr[$post_type]['taxonomy'][$taxonomy]['ancestors'] ) ) {
						$tax_ancestors = get_ancestors( $term->term_id, $taxonomy );
						$tax_ancestors = array_reverse ( $tax_ancestors );

						foreach ( $tax_ancestors as $term_id )
							$termlink .= get_term_by( 'id', $term_id, $taxonomy )->slug . '/';
					}

					$termlink .= $term->slug . '/';
					$termlink = esc_url( home_url( $termlink ) );
				}
			}
		}
	}

	return $termlink;
}
#add_filter( 'term_link', 'mytheme_term_link', 10, 3 );


# ------------------------------
# 固定ページ
# ------------------------------


/**
 * 固定ページ一覧のカラムをカスタマイズ
 * - wp-admin/includes/class-wp-posts-list-table.php
 */
function mytheme_manage_pages_columns( $posts_columns ) {
	global $mytheme_acf_init_post_type;
	$default = $posts_columns;
	$posts_columns = array ();

	$posts_columns['cb'] = $default['cb'];
	$posts_columns['title'] = $default['title'];

	if ( $mytheme_acf_init_post_type ) {
		foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
			if ( $param == 'page' ) {
				foreach ( $acf_group as $acf_fields ) {
					foreach ( $acf_fields as $fields ) {
						// 表示しない例外カラム
						if (
							//$fields['name'] == 'フィールド名' ||
							$fields['type'] == 'group' ||
							$fields['type'] == 'repeater' ||
							$fields['type'] == 'wysiwyg'
						) continue;
						$posts_columns[$fields['name']] = $fields['label'];
					}
				}
			}
		}
	}

	//cb title author comments date
	#$posts_columns['cb'] = $default['cb'];
	#$posts_columns['title'] = $default['title'];
	#$posts_columns['author'] = $default['author'];
	#$posts_columns['comments'] = $default['comments'];
	$posts_columns['post_name'] = 'スラッグ';
	#$posts_columns['template'] = 'テンプレート';
	$posts_columns['permalink'] = 'パーマリンク';
	$posts_columns['date'] = $default['date'];

	return $posts_columns;
}
add_filter( 'manage_pages_columns', 'mytheme_manage_pages_columns' );

function mytheme_manage_pages_custom_column( $column_name, $post_id ) {
	global $mytheme_acf_init_post_type;

	$flag = false;
	/*
	if ( $column_name == 'maga_group' ) {
		$flag = true;
	}
	*/

	if ( !$flag && $mytheme_acf_init_post_type ) {
		foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
			if ( $param == 'page' ) {
				foreach ( $acf_group as $acf_fields ) {
					foreach ( $acf_fields as $fields ) {
						if ( $fields['name'] == $column_name ) {
							switch ( $fields['type'] ) {
								case 'text' :
								case 'number' :
								case 'textarea' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo esc_html( $meta );
									break;
								case 'select' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										if ( $fields['multiple'] )
											echo implode ( '、', $meta );
										else
											echo esc_html( $meta );
									}
									break;
								case 'image' :
									if ( $attachment_id = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<img src="' . wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) . '" style="max-width: 50px;">';
									break;
								case 'true_false' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '&#10004;';
									break;
								case 'relationship' :
									$html = '';
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										foreach ( $meta as $id )
											$html .= '<a href="' . get_admin_url() . 'edit.php?s=' . get_the_title( $id ) . '&post_status=all&post_type=' . $post_type . '" title="' . get_the_title( $id ) . '">' . get_the_title( $id ) . ' (#' . $id . ')</a>, ';
										$html = rtrim ( $html, ', ' );
									}
									echo $html;
									break;
								case 'radio' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo esc_html( $fields['choices'][$meta] );
									break;
								case 'checkbox' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo implode ( '、', $meta );
									break;
								case 'file' :
									$file_id = get_post_meta( $post_id, $fields['name'], true );
									if ( $file_id && ( $file_url = wp_get_attachment_url( $file_id ) ) ) {
										$file = mytheme_get_fileinfo ( $file_url );
										echo '<a href="' . $file_url . '" target="_blank">' . $file['filename'] . ' [' . $file['type'] . ' ' . $file['length'] . ']</a>';
									}
									break;
								case 'date_picker' :
									$date = '';
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										$week = '';
										$datetime = new DateTime( $meta );
										$week = array ( '日', '月', '火', '水', '木', '金', '土' );
										$week = $week[(int) $datetime->format( 'w' )];
										$date .= $datetime->format( 'Y.m.d' );
										$date .= '（' . $week . '）';
									}
										echo $date;
									break;
								case 'color_picker' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<span style="background: ' . $meta . '; display: inline-block; width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></span>' . $meta;
									break;
								case 'url' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<a href="' . $meta . '" target="_blank">' . $meta . '</a>';
									break;
								case 'user' :
									if ( $author_id = get_post_meta( $post_id, $fields['name'], true ) ) {
										if ( $meta = get_the_author_meta( 'display_name', $author_id ) )
											echo $meta;
									}
									break;
							}
						}
					}
				}
			}
		}
	}

	if ( $column_name == 'permalink' ) {
		$permalink = get_permalink( $post_id );
		echo '<a href="' . $permalink . '">' . str_replace ( get_bloginfo( 'url' ), '', $permalink ) . '</a>';
	} elseif ( $column_name == 'post_name' ) {
		echo get_post_field( 'post_name', $post_id );
	} elseif ( $column_name == 'page_tag' ) {
		$taxonomy_names = get_object_taxonomies( 'page' );
		if ( $taxonomy_names ) {
			foreach ( $taxonomy_names as $taxonomy ) {
				if ( $column_name == $taxonomy )
					echo get_the_term_list( $post_id, $taxonomy, '', ', ' );
			}
		}
	} elseif ( $column_name == 'template' ) {
		echo get_page_template_slug();
	}
}
add_action( 'manage_pages_custom_column', 'mytheme_manage_pages_custom_column', 10, 2 );


# ------------------------------
# 投稿
# ------------------------------


/**
 * 投稿一覧のカラムをカスタマイズ
 * - wp-admin/includes/class-wp-posts-list-table.php
 * - wp-admin/includes/class-wp-list-table.php
 * 使用可能なクエリ変数は$public_query_varsに定義される
 */
function mytheme_orderby_posts( $query_vars ) {
	global $mytheme_acf_init_post_type;

	if ( is_admin() ) {
		if ( $mytheme_acf_init_post_type ) {
			foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
				foreach ( $acf_group as $acf_fields ) {
					foreach ( $acf_fields as $fields ) {
						if ( !empty ( $query_vars['orderby'] ) && $query_vars['orderby'] == $fields['name'] ) {
							$query_vars = array_merge ( $query_vars, array (
								'meta_key' => $fields['name'],
								'orderby' => 'meta_value'
							) );
						}
					}
				}
			}
		}
	}

	return $query_vars;
}
add_filter( 'request', 'mytheme_orderby_posts' );

function mytheme_add_query_var( $public_query_vars ) {
	global $mytheme_acf_init_post_type;

	if ( $mytheme_acf_init_post_type ) {
		foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
			foreach ( $acf_group as $acf_fields ) {
				foreach ( $acf_fields as $fields ) {
					array_push ( $public_query_vars, $fields['name'] );
				}
			}
		}
	}

	return $public_query_vars;
}
add_filter( 'query_vars', 'mytheme_add_query_var' );

function mytheme_manage_posts_columns( $posts_columns, $post_type ) {
	global $mytheme_acf_init_post_type, $post_type_arr;

	if ( isset ( $post_type_arr[$post_type] ) ) {
		$default = $posts_columns;
		$posts_columns = array ();

		$posts_columns['cb'] = $default['cb'];
		$posts_columns['title'] = $default['title'];

		if ( $post_type == 'hoge' ) {
			$posts_columns['thumbnail'] = 'サムネイル';
		}

		if ( $mytheme_acf_init_post_type ) {
			foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
				if ( $param == $post_type ) {
					foreach ( $acf_group as $acf_fields ) {
						foreach ( $acf_fields as $fields ) {
							// 表示しない例外カラム
							if (
								//$fields['name'] == 'フィールド名' ||
								$fields['type'] == 'group' ||
								$fields['type'] == 'repeater' ||
								$fields['type'] == 'wysiwyg'
							) continue;
							$posts_columns[$fields['name']] = $fields['label'];
							add_filter( 'manage_edit-' . $post_type . '_sortable_columns', function ( $col ) use ( $fields ) { $col[$fields['name']] = $fields['name']; return $col; } );
						}
					}
				}
			}
		}

		if ( $post_type == 'hoge' || $post_type == 'foo' || $post_type == 'var' ) {
			$taxonomy_names = get_object_taxonomies( $post_type, 'objects' );
			if ( $taxonomy_names ) {
				foreach ( $taxonomy_names as $taxonomy => $object ) {
					if ( $taxonomy == 'post_format' ) {
						if ( post_type_supports( $post_type, 'post-formats' ) )
							$posts_columns[$taxonomy] = $object->label;
					} else {
						$posts_columns[$taxonomy] = $object->label;
					}
				}
			}

			if ( post_type_supports( $post_type, 'page-attributes' ) ) {
				$posts_columns['order'] = '順序';
				#add_filter( 'manage_edit-' . $post_type . '_sortable_columns', function ( $col ) { $col['order'] = 'menu_order'; return $col; } );
			}

			if ( !empty ( $default['comments'] ) )
				$posts_columns['comments'] = $default['comments'];

			//$posts_columns['categories'] = $default['categories'];
			$posts_columns['date'] = $default['date'];
		}

		$taxonomy_names = get_object_taxonomies( $post_type, 'objects' );
		if ( $taxonomy_names ) {
			foreach ( $taxonomy_names as $taxonomy => $object ) {
				if ( $taxonomy == 'post_format' ) {
					if ( post_type_supports( $post_type, 'post-formats' ) )
						$posts_columns[$taxonomy] = $object->label;
				} else {
					$posts_columns[$taxonomy] = $object->label;
				}
			}
		}
		$posts_columns['permalink'] = 'パーマリンク';
		$posts_columns['date'] = $default['date'];
	}

	return $posts_columns;
}
add_filter( 'manage_posts_columns', 'mytheme_manage_posts_columns', 10, 2 );

function mytheme_manage_posts_custom_column( $column_name, $post_id ) {
	global $mytheme_acf_init_post_type;
	$post_type = get_post_type( $post_id );

	$flag = false;
	/*
	if ( $column_name == 'maga_group' ) {
		$flag = true;
	}
	*/

	if ( !$flag && $mytheme_acf_init_post_type ) {
		foreach ( $mytheme_acf_init_post_type as $param => $acf_group ) {
			if ( $param == $post_type ) {
				foreach ( $acf_group as $acf_fields ) {
					foreach ( $acf_fields as $fields ) {
						if ( $fields['name'] == $column_name ) {
							switch ( $fields['type'] ) {
								case 'text' :
								case 'number' :
								case 'textarea' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo esc_html( $meta );
									break;
								case 'select' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										if ( $fields['multiple'] )
											echo implode ( '、', $meta );
										else
											echo esc_html( $meta );
									}
									break;
								case 'image' :
									if ( $attachment_id = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<img src="' . wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) . '" style="max-width: 50px;">';
									break;
								case 'true_false' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '&#10004;';
									break;
								case 'relationship' :
									$html = '';
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										foreach ( $meta as $id )
											$html .= '<a href="' . get_admin_url() . 'edit.php?s=' . get_the_title( $id ) . '&post_status=all&post_type=' . $post_type . '" title="' . get_the_title( $id ) . '">' . get_the_title( $id ) . ' (#' . $id . ')</a>, ';
										$html = rtrim ( $html, ', ' );
									}
									echo $html;
									break;
								case 'radio' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo esc_html( $fields['choices'][$meta] );
									break;
								case 'checkbox' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo implode ( '、', $meta );
									break;
								case 'file' :
									$file_id = get_post_meta( $post_id, $fields['name'], true );
									if ( $file_id && ( $file_url = wp_get_attachment_url( $file_id ) ) ) {
										$file = mytheme_get_fileinfo ( $file_url );
										echo '<a href="' . $file_url . '" target="_blank">' . $file['filename'] . ' [' . $file['type'] . ' ' . $file['length'] . ']</a>';
									}
									break;
								case 'date_picker' :
									$date = '';
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) ) {
										$week = '';
										$datetime = new DateTime( $meta );
										$week = array ( '日', '月', '火', '水', '木', '金', '土' );
										$week = $week[(int) $datetime->format( 'w' )];
										$date .= $datetime->format( 'Y.m.d' );
										$date .= '（' . $week . '）';
									}
										echo $date;
									break;
								case 'color_picker' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<span style="background: ' . $meta . '; display: inline-block; width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></span>' . $meta;
									break;
								case 'url' :
									if ( $meta = get_post_meta( $post_id, $fields['name'], true ) )
										echo '<a href="' . $meta . '" target="_blank">' . $meta . '</a>';
									break;
								case 'user' :
									if ( $author_id = get_post_meta( $post_id, $fields['name'], true ) ) {
										if ( $meta = get_the_author_meta( 'display_name', $author_id ) )
											echo $meta;
									}
									break;
							}
						}
					}
				}
			}
		}
	}
	switch ( $column_name ) {
		case 'permalink' :
			$permalink = get_permalink( $post_id );
			echo '<a href="' . $permalink . '" target="_blank">' . str_replace ( get_bloginfo( 'url' ), '', $permalink ) . '</a>';
			break;

		case 'order' :
			$post = get_post( $post_id );
			echo $post->menu_order;
			break;

		default :
			$taxonomy_names = get_object_taxonomies( $post_type );
			if ( $taxonomy_names ) {
				foreach ( $taxonomy_names as $taxonomy ) {
					if ( $column_name == $taxonomy )
						echo get_the_term_list( $post_id, $taxonomy, '', ', ' );
				}
			}
	}
}
add_action( 'manage_posts_custom_column', 'mytheme_manage_posts_custom_column', 10, 2 );


# ------------------------------
# タクソノミー
# ------------------------------


/**
 * ターム一覧のカラムをカスタマイズ
 * - wp-admin/includes/class-wp-terms-list-table.php
 */
if ( $mytheme_acf_init_taxonomy ) {
	foreach ( $mytheme_acf_init_taxonomy as $param => $acf_group ) {
		add_filter( 'manage_edit-' . $param . '_columns', function ( $columns ) {
			global $acf_group;
			$default = $columns;
			$columns = array ();

			$columns['cb'] = $default['cb'];
			$columns['name'] = $default['name'];

			foreach ( $acf_group as $acf_fields ) {
				foreach ( $acf_fields as $fields ) {
					// 表示しない例外カラム
					if (
						//$fields['name'] == 'フィールド名' ||
						$fields['type'] == 'group' ||
						$fields['type'] == 'repeater' ||
						$fields['type'] == 'wysiwyg'
					) continue;
					$columns[$fields['name']] = $fields['label'];
				}
			}

			$columns['description'] = $default['description'];
			$columns['slug'] = $default['slug'];
			$columns['posts'] = $default['posts'];

			return $columns;
		} );

		add_filter( 'manage_' . $param . '_custom_column' , function ( $string, $column_name, $term_id ) {
			global $acf_group, $param;
				foreach ( $acf_group as $acf_fields ) {
					foreach ( $acf_fields as $fields ) {
						if ( $fields['name'] == $column_name ) {
							switch ( $fields['type'] ) {
								case 'text' :
								case 'number' :
								case 'textarea' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo esc_html( $meta );
									break;
								case 'select' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) ) {
										if ( $fields['multiple'] )
											echo implode ( '、', $meta );
										else
											echo esc_html( $meta );
									}
									break;
								case 'image' :
									if ( $attachment_id = get_term_meta( $term_id, $fields['name'], true ) )
										echo '<img src="' . wp_get_attachment_image_url( $attachment_id, 'thumbnail' ) . '" style="max-width: 50px;">';
									break;
								case 'true_false' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo '&#10004;';
									break;
								case 'relationship' :
									$html = '';
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) ) {
										foreach ( $meta as $id )
											$html .= '<a href="' . get_admin_url() . 'edit.php?s=' . get_the_title( $id ) . '&post_status=all&post_type=' . $post_type . '" title="' . get_the_title( $id ) . '">' . get_the_title( $id ) . ' (#' . $id . ')</a>, ';
										$html = rtrim ( $html, ', ' );
									}
									echo $html;
									break;
								case 'radio' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo esc_html( $fields['choices'][$meta] );
									break;
								case 'checkbox' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo implode ( '、', $meta );
									break;
								case 'file' :
									$file_id = get_term_meta( $term_id, $fields['name'], true );
									if ( $file_id && ( $file_url = wp_get_attachment_url( $file_id ) ) ) {
										$file = mytheme_get_fileinfo ( $file_url );
										echo '<a href="' . $file_url . '" target="_blank">' . $file['filename'] . ' [' . $file['type'] . ' ' . $file['length'] . ']</a>';
									}
									break;
								case 'date_picker' :
									$date = '';
									if ( $meta = get_post_meta( $term_id, $fields['name'], true ) ) {
										$week = '';
										$datetime = new DateTime( $meta );
										$week = array ( '日', '月', '火', '水', '木', '金', '土' );
										$week = $week[(int) $datetime->format( 'w' )];
										$date .= $datetime->format( 'Y.m.d' );
										$date .= '（' . $week . '）';
									}
										echo $date;
									break;
								case 'color_picker' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo '<span style="background: ' . $meta . '; display: inline-block; width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></span>' . $meta;
									break;
								case 'url' :
									if ( $meta = get_term_meta( $term_id, $fields['name'], true ) )
										echo '<a href="' . $meta . '" target="_blank">' . $meta . '</a>';
									break;
								case 'user' :
									if ( $author_id = get_post_meta( $term_id, $fields['name'], true ) ) {
										if ( $meta = get_the_author_meta( 'display_name', $author_id ) )
											echo $meta;
									}
									break;
							}
						}
					}
				}
		}, 10, 3 );
	}
}


# ------------------------------
# メディア
# ------------------------------


/**
 * メディアサイズを追加する
 */
add_image_size( 'm652x356', 652, 356, true );
add_image_size( 'm1120x', 1120, 0, false );
add_image_size( 'm120x160', 120, 160, true );
add_image_size( 'm160x80', 160, 80, true );

// 追加したメディアサイズを投稿に添付時選択可能にする
function mytheme_image_size_names_choose( $org_sizes ) {
	$new_sizes = array ();
	$sizes = get_intermediate_image_sizes();
	foreach ( $sizes as $size )
		$new_sizes[$size] = $size;
	$new_sizes = array_merge ( $new_sizes, $org_sizes );
	return $new_sizes;
}
add_filter( 'image_size_names_choose', 'mytheme_image_size_names_choose', 11, 1 );


/**
 * リサイズ時の圧縮率を変更する
 */
function mytheme_jpeg_quality( $arg ) {
	return 100;
}
add_filter( 'jpeg_quality', 'mytheme_jpeg_quality' );


# ------------------------------
# メソッド
# ------------------------------


/**
 * ファイル情報を取得する
 */
function mytheme_get_fileinfo( $url ) {
	$info = array ();
	if ( ( $headers = get_headers ( $url, 1 ) ) && array_key_exists ( 'Content-Length', $headers ) ) {
		$length = (int) $headers['Content-Length'];
		$b = 1024;
		$mb = pow ( $b, 2 );
		$gb = pow ( $b, 3 );
		if ( $length >= $gb ) {
			$bite = $gb;
			$unit = 'GB';
		} elseif ( $length >= $mb ) {
			$bite = $mb;
			$unit = 'MB';
		} else {
			$bite = $b;
			$unit = 'KB';
		}
		$length = round ( $length / $bite, 2 );
		$length = number_format ( $length, 2 ) . $unit;
		$info['length'] = $length;

		$pathinfo = pathinfo ( $url );
		$info['filename'] = $pathinfo['filename'] . '.' . $pathinfo['extension'];

		switch ( $headers['Content-Type'] ) {
			case 'text/plain' :						$type = 'TXT'; break;
			case 'text/csv' :						$type = 'CSV'; break;
			case 'text/html' :						$type = 'HTML'; break;
			case 'text/css' :						$type = 'CSS'; break;
			case 'text/javascript' :				$type = 'JavaScript'; break;
			case 'application/octet-stream' :		$type = 'EXE'; break;
			case 'application/pdf' :				$type = 'PDF'; break;
			case 'application/vnd.ms-excel' :		$type = 'EXCEL'; break;
			case 'application/vnd.ms-powerpoint' :	$type = 'PowerPoint'; break;
			case 'application/msword' :				$type = 'WORD'; break;
			case 'image/jpeg' :						$type = 'JPEG'; break;
			case 'image/png' :						$type = 'PNG'; break;
			case 'image/gif' :						$type = 'GIF'; break;
			case 'image/bmp' :						$type = 'Bitmap'; break;
			case 'application/zip' :				$type = 'ZIP'; break;
			case 'application/x-lzh' :				$type = 'LZH'; break;
			case 'application/x-tar' :				$type = 'TAR'; break;
			case 'audio/mpeg' :						$type = 'MP3'; break;
			case 'audio/mp4' :						$type = 'MP4'; break;
			case 'video/mpeg' :						$type = 'MPEG'; break;
			default :								$type = '';
		}
		$info['type'] = $type;
	}
	return $info;
}


# ------------------------------
# テーマ
# ------------------------------


/**
 * アイキャッチを使用する
 */
function mytheme_add_theme_support() {
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'mytheme_add_theme_support' );


/**
 * ショートコード
 */
function mytheme_shortcode_case_items( $atts ) {
	global $post;

	$atts = shortcode_atts( array (
		'posts_per_page' => '-1',
	), $atts, 'case_items' );

	$args = array (
		'post_type' => 'case',
		'posts_per_page' => $atts['posts_per_page'],
	);
	$the_query = new WP_Query( $args );

	$html = '';
	if ( $the_query->have_posts() ) {
		$html .= '		<div class="top-case__slider">
			<div class="visual-animate">
				<ul class="visual-animate__slider">' . "\n";
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

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

			$html .= '					<li>
						<figure>' . $thumbnail . '</figure>
						<div class="top-case__slider__tag">' . $category . '</div>
						<div class="top-case__slider__inner">
							<p class="top-case__slider__title">' . get_the_title() . '</p>
							<p class="top-case__slider__company">' . get_post_meta( $post->ID, 'case_company', true ) . '</p>
							<div class="top-case__slider__btn"><a href="' . get_the_permalink() . '" class="mod-btn2"><span>View More</span></a></div>
						</div>
					</li>' . "\n";
		}
		$html .= '				</ul>
				<div class="top-case__slider__btn-prev"><div><span></span></div></div>
				<div class="top-case__slider__btn-next"><div><span></span></div></div>
			</div>
		</div>' . "\n";
		wp_reset_postdata();
	}
	return $html;
}
add_shortcode( 'case_items', 'mytheme_shortcode_case_items' );

function mytheme_shortcode_news_items( $atts ) {
	global $post;

	$atts = shortcode_atts( array (
		'posts_per_page' => '-1',
		'term_slug' => '',
	), $atts, 'news_items' );

	$args = array (
		'post_type' => 'news',
		'posts_per_page' => $atts['posts_per_page'],
	);
	if ( $atts['term_slug'] ) {
		$args['tax_query'] = array (
			'relation' => 'AND',
			array (
				'taxonomy' => 'news_category',
				'field' => 'slug',
				'terms' => $atts['term_slug'],
			),
		);
	}
	$the_query = new WP_Query( $args );

	$html = '';
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

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
				$title = '<a href="' . $href . '"' . $target . '>' . get_the_title() . ( $target ? '<img src="' . get_bloginfo( 'template_url' ) . '/img/cmn_ico_win2.svg" alt="">' : '' ) . '</a>';
			else
				$title = get_the_title();

			$html .= '				<dl>
					<dt>' . get_the_date( 'Y.m.d' ) . '</dt>
					<dd>' . $title . '</dd>
				</dl>' . "\n";
		}
		$html .= '				<div class="top-news__btn"><a href="' . get_bloginfo( 'url' ) . '/news/" class="mod-btn2"><span>View More</span></a></div>' . "\n";
		wp_reset_postdata();
	}
	return $html;
}
add_shortcode( 'news_items', 'mytheme_shortcode_news_items' );

function mytheme_shortcode_seminar_items( $atts ) {
	global $post;

	$atts = shortcode_atts( array (
		'posts_per_page' => '-1',
	), $atts, 'seminar_items' );

	$args = array (
		'post_type' => 'seminar',
		'posts_per_page' => $atts['posts_per_page'],
	);
	$the_query = new WP_Query( $args );

	$html = '';
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$date = '';
			$date_time = get_post_meta( $post->ID, 'seminar_date_time', true );
			if ( preg_match ( '@[0-9]+?\.[0-9]+?\.[0-9]+@', $date_time, $match ) )
				$date = $match[0];

			$html .= '				<dl>
					<dt>' . $date . '</dt>
					<dd><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></dd>
				</dl>' . "\n";
		}
		$html .= '				<div class="top-news__btn"><a href="' . get_bloginfo( 'url' ) . '/seminar/" class="mod-btn2"><span>View More</span></a></div>' . "\n";
		wp_reset_postdata();
	}
	return $html;
}
add_shortcode( 'seminar_items', 'mytheme_shortcode_seminar_items' );

function mytheme_shortcode_support_items( $atts ) {
	global $post;

	$atts = shortcode_atts( array (
		'posts_per_page' => '-1',
	), $atts, 'support_items' );

	$args = array (
		'post_type' => 'support',
		'posts_per_page' => $atts['posts_per_page'],
	);
	$the_query = new WP_Query( $args );

	$html = '';
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			$thumbnail = '';
			if ( $attachment_id = get_post_meta( $post->ID, 'support_logo', true ) )
				$thumbnail = '<img src="' . wp_get_attachment_image_url( $attachment_id, 'm160x80' ) . '" alt="' . esc_attr( get_the_title() ) . '">';

			$html .= '<li><a href="' . get_post_meta( $post->ID, 'support_url', true ) . '" target="_blank" rel="noopener noreferrer">' . $thumbnail . '</a></li>' . "\n";
		}
		wp_reset_postdata();
	}
	return $html;
}
add_shortcode( 'support_items', 'mytheme_shortcode_support_items' );


# ------------------------------
# その他
# ------------------------------


/**
 * Ajax
 */
function mytheme_enqueue_ajax_script() {
	global $post;
	if ( is_post_type_archive( 'case' ) ) {
		wp_enqueue_script( 'case_items', get_bloginfo( 'template_directory' ) . '/js/case-ajax.js', array ( 'jquery' ), false, true );
		wp_localize_script( 'case_items', 'case_items', array ( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	} elseif ( is_post_type_archive( 'seminar' ) ) {
		wp_enqueue_script( 'seminar', get_bloginfo( 'template_directory' ) . '/js/seminar-ajax.js', array ( 'jquery' ), false, true );
		wp_localize_script( 'seminar', 'seminar', array ( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	} elseif ( is_post_type_archive( 'news' ) || is_taxonomy( 'news_category' ) ) {
		wp_enqueue_script( 'news', get_bloginfo( 'template_directory' ) . '/js/news-ajax.js', array ( 'jquery' ), false, true );
		wp_localize_script( 'news', 'news', array ( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
}
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_ajax_script' );

function mytheme_case_ajax() {
	global $post, $post_type_arr;

	extract ( $_POST );

	$result = array (
		'html' => '',
		'found_posts' => 0,
		'more' => false,
	);

	// 初期表示の件数と1ページあたりの表示件数
	if ( $taxonomy && $term_slug )
		$posts_per_page = $post_type_arr['case']['taxonomy'][$taxonomy]['posts_per_page'];
	else
		$posts_per_page = $post_type_arr['case']['posts_per_page'];
	$initial_num = $posts_per_page;
	$per_page = $posts_per_page;

	// ページ数から開始位置とリミットを計算
	$offset = $per_page * ( $page_num - 2 ) + $initial_num;

	$args = array (
		'posts_per_page' => $per_page + 1,// ※次ページ確認のため1件多く読み込む
		'post_type' => 'case',
		'offset' => $offset,
	);

	if ( $taxonomy && $term_slug ) {
		$args['tax_query'] = array (
			'relation' => 'AND',
			array (
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $term_slug,
			),
		);
	}

	$the_query = new WP_Query( $args );
	$result['found_posts'] = (int) $the_query->found_posts;

	if ( $the_query->have_posts() ) {
		$i = 0;
		while ( $the_query->have_posts() ) {
			// 最大行取得できていれば More を表示
			if ( $i == $per_page ) {
				$result['more'] = true;
				break;
			}

			$the_query->the_post();

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

			$result['html'] .= '<article class="case-items__item">
	<figure>' . $thumbnail . '</figure>
	<div class="case-items__item__tag">' . $category . '</div>
	<div class="case-items__item__inner">
		<p class="case-items__item__title">' . get_the_title() . '</p>
		<p class="case-items__item__company">' . get_post_meta( $post->ID, 'case_company', true ) . '</p>
		<div class="case-items__item__btn"><a href="' . get_the_permalink() . '" class="mod-btn2"><span>View More</span></a></div>
	</div>
</article>' . "\n";
			$i++;
		}
		wp_reset_postdata();
	}

	$json = json_encode ( $result );
	header ( 'Content-Type: application/json; charset=utf-8' );
	echo $json;
	exit;
}
add_action( 'wp_ajax_case', 'mytheme_case_ajax' );
add_action( 'wp_ajax_nopriv_case', 'mytheme_case_ajax' );

function mytheme_seminar_ajax() {
	global $post, $post_type_arr;

	extract ( $_POST );

	$result = array (
		'html' => '',
		'found_posts' => 0,
		'more' => false,
	);

	// 初期表示の件数と1ページあたりの表示件数
	if ( $taxonomy && $term_slug )
		$posts_per_page = $post_type_arr['seminar']['taxonomy'][$taxonomy]['posts_per_page'];
	else
		$posts_per_page = $post_type_arr['seminar']['posts_per_page'];
	$initial_num = $posts_per_page;
	$per_page = $posts_per_page;

	// ページ数から開始位置とリミットを計算
	$offset = $per_page * ( $page_num - 2 ) + $initial_num;

	$args = array (
		'posts_per_page' => $per_page + 1,// ※次ページ確認のため1件多く読み込む
		'post_type' => 'seminar',
		'offset' => $offset,
	);

	if ( $taxonomy && $term_slug ) {
		$args['tax_query'] = array (
			'relation' => 'AND',
			array (
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $term_slug,
			),
		);
	}

	$the_query = new WP_Query( $args );
	$result['found_posts'] = (int) $the_query->found_posts;

	if ( $the_query->have_posts() ) {
		$i = 0;
		while ( $the_query->have_posts() ) {
			// 最大行取得できていれば More を表示
			if ( $i == $per_page ) {
				$result['more'] = true;
				break;
			}

			$the_query->the_post();

			$category = '';
			if ( $terms = get_the_terms( get_the_ID(), 'seminar_category' ) ) {
				$term = array_slice ( $terms, 0, 1 );
				$term = $term[0];
				$category = esc_html( $term->name );
			}

			$result['html'] .= '<article class="seminar-items__item">
	<div class="seminar-items__item__header">
		<div class="seminar-items__item__tag">' . $category . '</div>
		<div class="seminar-items__item__date">' . get_post_meta( $post->ID, 'seminar_date_time', true ) . '</div>
	</div>
	<p class="seminar-items__item__title">' . get_the_title() . '</p>
	<p class="seminar-items__item__excerpt">' . get_post_meta( $post->ID, 'seminar_archive_text', true ) . '</p>
	<div class="seminar-items__item__btn"><a href="' . get_the_permalink() . '" class="mod-btn2"><span>View More</span></a></div>
</article>' . "\n";
			$i++;
		}
		wp_reset_postdata();
	}

	$json = json_encode ( $result );
	header ( 'Content-Type: application/json; charset=utf-8' );
	echo $json;
	exit;
}
add_action( 'wp_ajax_seminar', 'mytheme_seminar_ajax' );
add_action( 'wp_ajax_nopriv_seminar', 'mytheme_seminar_ajax' );

function mytheme_news_ajax() {
	global $post, $post_type_arr;

	extract ( $_POST );

	$result = array (
		'html' => '',
		'found_posts' => 0,
		'more' => false,
	);

	// 初期表示の件数と1ページあたりの表示件数
	if ( $taxonomy && $term_slug )
		$posts_per_page = $post_type_arr['news']['taxonomy'][$taxonomy]['posts_per_page'];
	else
		$posts_per_page = $post_type_arr['news']['posts_per_page'];
	$initial_num = $posts_per_page;
	$per_page = $posts_per_page;

	// ページ数から開始位置とリミットを計算
	$offset = $per_page * ( $page_num - 2 ) + $initial_num;

	$args = array (
		'posts_per_page' => $per_page + 1,// ※次ページ確認のため1件多く読み込む
		'post_type' => 'news',
		'offset' => $offset,
	);

	if ( $taxonomy && $term_slug ) {
		$args['tax_query'] = array (
			'relation' => 'AND',
			array (
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $term_slug,
			),
		);
	}

	$the_query = new WP_Query( $args );
	$result['found_posts'] = (int) $the_query->found_posts;

	if ( $the_query->have_posts() ) {
		$i = 0;
		while ( $the_query->have_posts() ) {
			// 最大行取得できていれば More を表示
			if ( $i == $per_page ) {
				$result['more'] = true;
				break;
			}

			$the_query->the_post();

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

			$result['html'] .= '<div class="news-items__item">
	<div class="news-items__item__date">' . get_the_date( 'Y.m.d' ) . '</div>
	<div class="news-items__item__category"><div class="news-category">' . $category . '</div></div>
	<div class="news-items__item__title">' . $html . '</div>
</div>' . "\n" . $text;
			$i++;
		}
		wp_reset_postdata();
	}

	$json = json_encode ( $result );
	header ( 'Content-Type: application/json; charset=utf-8' );
	echo $json;
	exit;
}
add_action( 'wp_ajax_news', 'mytheme_news_ajax' );
add_action( 'wp_ajax_nopriv_news', 'mytheme_news_ajax' );


/**
 * 分類編集画面の横幅を広げる
 */
function mytheme_admin_head_term() {
	echo '<style type="text/css">
.form-field.term-description-wrap {
	display: none;
}
#edittag {
    max-width: 100%;
}
</style>' . "\n";
}
add_action( 'admin_head-term.php', 'mytheme_admin_head_term' );
