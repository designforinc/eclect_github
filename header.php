<?php
global $css;
?><!DOCTYPE HTML>
<html lang="ja">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-110330334-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-110330334-1');
</script>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="initial-scale=1">
<meta name="format-detection" content="telephone=no">
<meta name="keywords" content="株式会社エクレクト,eclect,えくれくと">
<meta name="description" content="エクレクトは、カスタマーサービスソフトウェア『Zendesk』の公式認定資格を全て取得している日本唯一のパートナー企業です。導入支援・データ移行・テンプレート制作・関連アプリケーション開発・既存システムとの連携など、ワンストップでご提供しています。">
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo( 'url' ); ?>/wp-content/uploads/2020/11/favicon.png" />
<title></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/style.css">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/module.css">
<?php if ( !empty ( $css ) ) echo $css; ?>
<?php wp_head(); ?>
</head>

<body>
<div class="layout">
	<header class="header"><div>
		<h1 class="header__logo"><a href="<?php bloginfo( 'url' ); ?>/"><img src="<?php bloginfo( 'template_url' ); ?>/img/logo1.png" alt="eclect"></a></h1>
		<nav class="header__gnav">
			<ul>
				<li><a href="<?php bloginfo( 'url' ); ?>/">HOME</a></li>
				<li><a href="<?php bloginfo( 'url' ); ?>/service/">サービス</a></li>
				<li><a href="<?php bloginfo( 'url' ); ?>/case/">導入事例</a></li>
				<li><a href="<?php bloginfo( 'url' ); ?>/seminar/">セミナー</a></li>
				<li><a href="<?php bloginfo( 'url' ); ?>/news/">お知らせ</a></li>
				<li><span>採用情報</span></li>
				<li><a href="<?php bloginfo( 'url' ); ?>/company/">会社情報</a></li>
				<li class="sp-only"><a href="https://reg.eclect.co.jp/public/application/add/65" target="_blank">お問合せ</a></li>
				<li class="sp-only"><a href="<?php bloginfo( 'url' ); ?>/privacy/">プライバシーポリシー</a></li>
				<li class="sp-only"><a href="<?php bloginfo( 'url' ); ?>/security/">情報セキュリティポリシー</a></li>
			</ul>
		</nav>
		<div class="header__btn-menu"></div>
		<div class="header__btn-inquiry"><a href="https://reg.eclect.co.jp/public/application/add/65" target="_blank"><span>お問合せ</span></a></div>
	</div></header>
