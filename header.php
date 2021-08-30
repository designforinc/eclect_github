<?php
global $css;
?><!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="initial-scale=1">
<meta name="format-detection" content="telephone=no">
<link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo( 'url' ); ?>/wp-content/uploads/2020/11/favicon.png" />
<title></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/style.css">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'template_url' ); ?>/css/module.css">
<?php if ( !empty ( $css ) ) echo $css; ?>
<?php wp_head(); ?>
	
<!-- SMP Tracking Tag Ver 3 -->
<script type="text/javascript">
<!--
(function(n){
var w = window, d = document;
w['ShanonCAMObject'] = n, w[n] = w[n] || function(){(w[n].q=w[n].q||[]).push(arguments)};
w[n].date = 1*new Date();
var e = d.createElement('script'), t = d.getElementsByTagName('script')[0];
e.async = 1, e.type='text/javascript', e.charset='utf-8', e.src = 'https://tracker.shanon-services.com/static/js/cam3.js' + "?_=" + w[n].date;
t.parentNode.insertBefore(e,t);
})('_cam');

_cam('create', 'aKEmwzyLJl-342', ['eclect.co.jp']);
_cam('require', 'crossLinker');
_cam('crossLinker:allLink', ['eclect.co.jp']);
_cam('send');
//-->
</script>
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
