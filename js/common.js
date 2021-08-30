var breakpoint_pc = 1200;
var breakpoint_tb = 1024;
var breakpoint_sp = 768;

(function($) {
	$.fn.smoothScroll = function(options) {
		options = $.extend({
			displace: 0,
			displace_pc_elm: '.header',
			displace_tb_elm: '.header',
			displace_sp_elm: '.header',
			speed: 1000,
			easing: 'swing',
			location_flag: false,
			hash: '',
			live: ''
		}, options);

		if (options.displace_pc_elm || options.displace_tb_elm || options.displace_sp_elm) {
			var timer, win_w, win_w_old;
			var fn_load = function() {
				if (timer !== false)
					clearTimeout(timer);
				timer = setTimeout(function() {
					win_w_old = win_w;
					if (typeof (win_w = window.innerWidth) === 'undefined')
						win_w = $(document).width();

					if (win_w_old !== win_w) {
						if (win_w <= breakpoint_sp)
							options.displace = options.displace_sp_elm ? parseInt($(options.displace_sp_elm).outerHeight()) * -1 - 20 : 0;
						else if (win_w <= breakpoint_tb)
							options.displace = options.displace_tb_elm ? parseInt($(options.displace_tb_elm).outerHeight()) * -1 - 20 : 0;
						else
							options.displace = options.displace_pc_elm ? parseInt($(options.displace_pc_elm).outerHeight()) * -1 - 20 : 0;
					}
				}, 0);
			};
			$(window).on('resize orientationchange', fn_load);
			fn_load();
		}

		var scroll = function() {
			var hash = $(this)[0].hash;

			if (options.hash)
				hash = options.hash;

			var targetOffset;

			if (hash)
				targetOffset = $(hash).offset().top;
			else
				targetOffset = 0;

			$('html, body').animate({
				scrollTop: targetOffset + options.displace
			}, options.speed, options.easing, function() {
				if (options.location_flag)
					location.hash = hash;
			});

			return false;
		};

		if (options.live) {
			$(document).off('click.smoothScroll', options.live).on('click.smoothScroll', options.live, scroll);
		} else {
			$(this).off('click.smoothScroll').on('click.smoothScroll', scroll);
		}
	};

	$.fn.linkWrapper = function(options) {
		if (options == 'off') {
			return this.each(function() {
				$(this)
					.css('cursor', 'default')
					.off('click');
			});
		} else {
			options = $.extend({
				target_elm: false
			}, options);

			return this.each(function() {
				var a;

				if (options.target_elm)
					a = $($(this).find(options.target_elm)[0]);
				else
					a = $($(this).find('a')[0]);

				var href = a.attr('href');

				if (href) {
					a.click(function(e) {e.preventDefault();});
					if (!$._data($(this).get(0)).events) {
						$(this)
							.css('cursor', 'pointer')
							.on('click', function() {
								if (a.attr('target') == '_blank')
									window.open().location.href = href;
								else
									window.location.href = href;
							});
					}
				}
			});
		}
	};

	$.fn.cover_js = function(options) {
		options = $.extend({
			bg_size: 'cover',
			bg_color: '',
			target: ''
		}, options);

		return this.each(function() {
			var target;
			var src = $(this).attr('src');

			$(this).attr('style', '');
			if (!options.target)
				target = $(this).parent();
			else
				target = $(this).parents(options.target).first();

			target.css({
				'background': options.bg_color + ' url(' + src + ') 50% 50% no-repeat',
				'background-size': options.bg_size
			});

			$(this).remove();
		});
	};

	$.fn.ellipsis_js = function(options) {
		options = $.extend({
			ellipsis: '…',
			height: 0
		}, options);

		return this.each(function() {
			var height_org = $(this).height();
			var height = options.height > 0 ? options.height : height_org;
			var html = $(this).html();
			var len = html.length;
			$(this).css({
				height: 'auto'
			});
			$(this).html('');
			for (var i = 0; i < len; i++) {
				$(this).append(html.substr(i, 1));
				if ($(this).height() > height) {
					$(this).html($(this).html().substr(0, i - 1) + options.ellipsis);
					$(this).css({
						height: height_org
					});
					return false;
				}
				if (i === (len - 1)) {
					$(this).css({
						height: height_org
					});
				}
			}
		});
	};
})(jQuery);

// タッチデバイス判定
var isTouchDevice = function() {
	return ('ontouchend' in window || navigator.msPointerEnabled) ? true : false;
};

jQuery(function($) {
	$('.cover').cover_js();
	$('a.smoothScroll').smoothScroll();

	var click_event_type = isTouchDevice ? 'touchend' : 'click';
	$(document).on(click_event_type, '.header__btn-menu', function() {
		if ($('.header__gnav').is(':visible')) {
			$('.header__gnav').hide();
			$('.header').removeClass('opened');
			$(this).removeClass('opened');
			$('.header__logo img').attr('src', '/wp-content/themes/eclect_github/img/logo1.png');
		} else {
			$('.header__gnav').show();
			$('.header').addClass('opened');
			$(this).addClass('opened');
			$('.header__logo img').attr('src', '/wp-content/themes/eclect_github/img/logo2.png');
		}
	});

	// header fixed
	var fixed_pos = 150;
	var scrollTimer = false;
	var header_fixed = function() {
		if (fixed_pos < $(window).scrollTop()) {
			if (!$('.header').hasClass('fixed') && !$('.header').hasClass('opened')) {
				$('.header').addClass('fixed').css('top', parseInt($('.header').height()) * -1).animate({
					top: 0
				}, 500, 'swing');
			}
		} else {
			if ($('.header').hasClass('fixed')) {
				$('.header').removeClass('fixed').css('top', 0);
			}
		}
	};

	var device, device_old;
	var win_w, win_w_old, win_h;
	var timer = false;
	var fn_load = function() {
		if (timer !== false)
			clearTimeout(timer);
		timer = setTimeout(function() {
			device_old = device;
			win_w_old = win_w;
			if (typeof (win_w = window.innerWidth) === 'undefined')
				win_w = $(document).width();
			if (typeof (win_h = window.innerHeight) === 'undefined')
				win_h = $(document).height();

			if (win_w_old !== win_w) {
				if (win_w <= breakpoint_sp)
					device = 'sp';
				else if (win_w <= breakpoint_tb)
					device = 'tb';
				else if (win_w <= breakpoint_pc)
					device = breakpoint_pc;
				else
					device = 'pc';

				// win_w に変化があったときの処理

				// デバイスが変わったときの処理
				if (device_old != device) {
					$('.header__btn-menu').removeClass('opened');

					if (device == 'sp') {
						// スマートフォン
						$('.header__gnav').hide();

						$('.mod-cols1, .mod-cols1--reverse').each(function() {
							if (!$('figure', this).next('.mod-hd1').length) {
								var html = $('.mod-hd1', this).html();
								$('.mod-hd1', this).remove();
								$('figure', this).after('<h2 class="mod-hd1">' + html + '</h2>');
							}
						});

						$('.header__gnav .smoothScroll').on('click.gnav', function() {
							$('.header__gnav').hide();
							$('.header').removeClass('opened');
							$('.header__btn-menu').removeClass('opened');
							$('.header__logo img').attr('src', '/wp-content/themes/eclect_github/img/logo1.png');
						});
					} else if (device == breakpoint_pc || device == 'pc' || device == 'tb') {
						// デスクトップ or タブレット
						$('.header__gnav').show();
						$('.header').removeClass('opened');
						$(this).removeClass('opened');
						$('.header__logo img').attr('src', '/wp-content/themes/eclect_github/img/logo1.png');

						$('.mod-cols1, .mod-cols1--reverse').each(function() {
							if (!$('.mod-cols1__col1', this).next('.mod-hd1').length) {
								var html = $('.mod-hd1', this).html();
								$('.mod-hd1', this).remove();
								$('.mod-cols1__col1', this).prepend('<h2 class="mod-hd1">' + html + '</h2>');
							}
						});

						$('.header__gnav .smoothScroll').off('click.gnav');
					}
				}
			}

			// 常に実行する処理
			if (device == 'sp' ) {
				// スマートフォン
			} else if (device == 'tb') {
				// タブレット
			} else if (device == breakpoint_pc || device == 'pc') {
				// デスクトップ
			}
			header_fixed();
			$(window).off('scroll.header_fixed').on('scroll.header_fixed', header_fixed);
		}, 0);
	};
	$(window).on('resize orientationchange', fn_load);
	fn_load();
});

jQuery(function($) {
	var elm = '.page-top';
	if ($(elm).length) {
		var page_top_flg = false;
		var page_top_bottom_fixed = '40';//string 固定する位置（vw可）
		var page_top_right_fixed = '40';//string 固定する位置（vw可）
		var page_top_bottom_fixed_sp = '4vw';//string 固定する位置（vw可）
		var page_top_right_fixed_sp = '4vw';//string 固定する位置（vw可）
		var opacity = false;//透過の有無
		var ini_pos, page_top_bottom_fixed_int, page_top_right_fixed_int;
		var page_top_w = $(elm).outerWidth();
		var page_top_h = $(elm).outerHeight();
		var st = $(window).scrollTop();
		var stopPos = 0;
		var page_top_timer1 = false;
		var vw_conv = function(pos) {
			var match_results = pos.match(/(.+?)vw/);
			if (match_results)
				return $(window).width() * (match_results[1] / 100);
			else
				return parseInt(pos);
		};

		// デバイス
		var device;
		var win_w, win_w_old;
		var timer = false;
		var fn_load = function() {
			if (timer !== false)
				clearTimeout(timer);
			timer = setTimeout(function() {
				win_w_old = win_w;
				if (typeof (win_w = window.innerWidth) === 'undefined')
					win_w = $(document).width();

				if (win_w_old !== win_w) {
					if (win_w <= breakpoint_sp)
						device = 'sp';
					else if (win_w <= breakpoint_tb)
						device = 'tb';
					else if (win_w <= breakpoint_pc)
						device = breakpoint_pc;
					else
						device = 'pc';

					if (device == 'sp') {
						page_top_bottom_fixed_int = vw_conv(page_top_bottom_fixed_sp);
						page_top_right_fixed_int  = vw_conv(page_top_right_fixed_sp);
					} else {
						page_top_bottom_fixed_int = vw_conv(page_top_bottom_fixed);
						page_top_right_fixed_int  = vw_conv(page_top_right_fixed);
					}

					page_top_w = $(elm).outerWidth();
					page_top_h = $(elm).outerHeight();
					st = $(window).scrollTop();

					// win_w に変化があったときの処理

					if (st >= 100) {
						page_top_flg = true;
						if (!opacity)
							$(elm).stop(true, false).animate({right: page_top_right_fixed_int}, 200, 'swing');
						else
							$(elm).stop(true, false).animate({right: page_top_right_fixed_int, opacity: 1}, 200, 'swing');
					} else if (st < 100) {
						page_top_flg = false;
						if (!opacity)
							$(elm).stop(true, false).animate({right: '-' + page_top_w}, 200, 'swing');
						else
							$(elm).stop(true, false).animate({right: '-' + page_top_w, opacity: 0}, 200, 'swing');
					}

					if (page_top_timer1 !== false)
						clearTimeout(page_top_timer1);
					page_top_timer1 = setTimeout(function() {
						setTimeout(function() {
							// 初期ポジション取得
							$(elm).removeClass('fixed');
							ini_pos = $(elm).offset().top;
							$(elm).addClass('fixed');
							stopPos = ini_pos - $(window).height() + page_top_bottom_fixed_int + page_top_h;
							if (st > stopPos)
								$(elm).removeClass('fixed');
						}, 1000);
					}, 200);
				}
			}, 0);
		};
		$(window).on('resize orientationchange', fn_load);
		fn_load();

		$(window).on('scroll', function() {
			st = $(this).scrollTop();
			if (st >= 100 && !page_top_flg) {
				if (!opacity)
					$(elm).stop(true, false).animate({right: page_top_right_fixed_int}, 200, 'swing');
				else
					$(elm).stop(true, false).animate({right: page_top_right_fixed_int, opacity: 1}, 200, 'swing');
				page_top_flg = true;
			} else if (st < 100 && page_top_flg) {
				if (!opacity)
					$(elm).stop(true, false).animate({right:'-' + page_top_w}, 200, 'swing');
				else
					$(elm).stop(true, false).animate({right:'-' + page_top_w, opacity: 0}, 200, 'swing');
				page_top_flg = false;
			}

			if (opacity) {
				var page_top_timer2 = false;

				$(elm).css({
					opacity: 1
				});

				if (page_top_timer2 !== false)
					clearTimeout(page_top_timer2);

				page_top_timer2 = setTimeout(function() {
					$(elm).css({
						opacity: 0.2
					});
				}, 500);
			}

			if (st > stopPos)
				$(elm).removeClass('fixed');
			else
				$(elm).addClass('fixed');
		});

		$(elm).smoothScroll();
	}

	var hash = location.hash;
	if (hash) {
		$(window).scrollTop($(hash).offset().top + parseInt($('.header').outerHeight()) * -1 - 20);
	}
});

jQuery(function($) {
	var dir = '';
	var path = location.pathname;
	var trimHost = new RegExp('((https?:)?//)' + location.hostname + '/', 'i');
	var trimIndex = new RegExp('index\\.(html?|php)(\\?[^\\/]+)?$', 'i');
	if ( path.match(trimIndex) )
		path = path.replace(trimIndex, '');

	$('.header__gnav li').each(function() {
		if ($('a', this).length) {
			var href = '/' + $('a', this).attr('href').replace(/^[\.\/]+/, '').replace(trimIndex, '');
			href = href.replace(trimHost, '');
			var reg = new RegExp('^' + href, 'i');
			if ((href != '/' + dir && path.match(reg)) || href == path) {
				$(this).addClass('current');
			}
		}
	});
});
