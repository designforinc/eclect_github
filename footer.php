<?php
global $js;
?>
	<footer class="footer">
		<div class="page-top"></div>
		<div class="footer__inner">
			<div class="footer__row1">
				<div class="footer__row1__cols">
					<div class="footer__row1__col1">
						<div class="footer__logo"><img src="<?php bloginfo( 'template_url' ); ?>/img/logo2.png" alt=""></div>
						<div class="footer__btn-inquiry"><a href="https://reg.eclect.co.jp/public/application/add/65" target="_blank"><span>お問合せ</span></a></div>
					</div>
					<div class="footer__row1__col2">
						<nav>
							<div>
								<ul>
									<li><a href="<?php bloginfo( 'url' ); ?>/">HOME</a></li>
									<li><a href="<?php bloginfo( 'url' ); ?>/case/">導入事例</a></li>
									<li><a href="<?php bloginfo( 'url' ); ?>/seminar/">セミナー</a></li>
								</ul>
								<ul>
									<li><a href="<?php bloginfo( 'url' ); ?>/news/">お知らせ</a></li>
									<li><a href="<?php bloginfo( 'url' ); ?>/company/">会社情報</a></li>
									<li><span>採用情報</span></li>
								</ul>
							</div>
							<ul>
								<li>
									<a href="<?php bloginfo( 'url' ); ?>/service/">サービス</a>
									<ul>
<?php
$args = array (
	'get' => 'all',
);
$terms = get_terms( 'service_category', $args );
if ( $terms && !is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
		if ( !$term->parent ) {
?>
										<li><a href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a></li>
<?php
		}
	}
}
?>
									</ul>
								</li>
							</ul>
						</nav>
						<div class="footer__btn-inquiry sp-only"><a href="https://reg.eclect.co.jp/public/application/add/65" target="_blank"><span>お問合せ</span></a></div>
					</div>
				</div>
			</div>
			<div class="footer__row2">
				<ul>
					<li><a href="<?php bloginfo( 'url' ); ?>/privacy/">プライバシーポリシー</a></li>
					<li><a href="<?php bloginfo( 'url' ); ?>/security/">情報セキュリティポリシー</a></li>
				</ul>
				<div class="footer__copyright"><small>&copy; eclect, Inc.</small></div>
			</div>
		</div>
	</footer>
</div>
<script src="<?php bloginfo( 'template_url' ); ?>/js/jquery-3.4.1.min.js"></script>
<script src="<?php bloginfo( 'template_url' ); ?>/js/common.js"></script>
<?php wp_footer(); ?>
<?php if ( !empty ( $js ) ) echo $js; ?>
</body>
</html>
