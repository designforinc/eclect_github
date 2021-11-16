;(function($) {
	$.fn.visualAnimate = function(options) {
		options = $.extend({
			interval: 3000,// スライドの間隔
			speed: 300,// スライドのスピード
			animate: 'slide',// アニメーション fade/slide/zoom
			autoStart: true,// 自動スタート
			to_bg: true,// <img>を背景化
			dot: false,// ドットのナビゲーション表示
			num: false,// スライド数表示
			cont_anim: false,// スライド画像に載せるテキストなどのアニメーション
			deactivate_width: false,// スライダーを無効化するデバイスの幅
			highlight: false,// 前後のスライドにマスクをかける
			delay: 0,// 自動スタートのタイミングを遅らせる
			sp_bp: 600,// スマートフォンのブレークポイント
			zoom_type: 'in',// zoom の時の方向
			loop: true,// 折り返しの処理
			type: 'copy',//copy:複製する/pos:位置を移動する
			thumbnail: '',// サムネイルのセレクター
			slide_click: false,// スライドをクリックすると次へ
			prev_btn_class: '',
			next_btn_class: '',
			distance: 1,// 一度にスライドさせる毎数
			colindex: 0,// int or obj ex.{560:1,960:3,9999:5},
			multicolumn: 1,// int or obj ex.{560:1,960:3,9999:5},
			swaip: true,// スワイプを有効にする
			align_left: false,// マルチカラムのときセルをセンタリングではなく左揃えにする
		}, options);

		return this.each(function() {
			var target = this;
			var startp, leftp;
			var colindex = options.colindex;
			var default_colindex = colindex;
			var slideTimer;
			var ul_obj = $('.visual-animate__slider', target);
			var slwidth;
			var margin = 0;
			var column = 1;
			var len = ul_obj.find('> li').length;
			var resizeTimer = false;
			var winW, winW_old, activate;
			var touchstartPosHorizontal, touchendPosHorizontal;
			var touchstartPosVertical, touchendPosVertical, touchReturnPos;
			var touchMoveDirection;
			var slideStartPos;
			var device = '';
			var device_old;
			var _ua;
			var clickEventType;

			var get_column = function(winW) {
				if (typeof options.multicolumn === 'object') {
					var flen = Object.keys(options.multicolumn).length;
					for (let i = 0; i < flen; i++) {
						let device_width = Object.keys(options.multicolumn)[i];
						if (winW <= device_width) {
							column = options.multicolumn[device_width];
							//return true;
							break;
						}
					}
				} else {
					column = options.multicolumn;
				}
			};

			if (options.loop == false)
				options.type = false;

			var get_ua = function() {
				_ua = (function(u) {
					return {
					Tablet:
					   (u.indexOf("windows")	!= -1 && u.indexOf("touch") != -1 && u.indexOf("tablet pc") == -1)
					||  u.indexOf("ipad")		!= -1
					|| (u.indexOf("android")	!= -1 && u.indexOf("mobile") == -1)
					|| (u.indexOf("firefox")	!= -1 && u.indexOf("tablet") != -1)
					||  u.indexOf("kindle")		!= -1
					||  u.indexOf("silk")		!= -1
					||  u.indexOf("playbook")	!= -1,
					Mobile:
					   (u.indexOf("windows")	!= -1 && u.indexOf("phone") != -1)
					||  u.indexOf("iphone")		!= -1
					||  u.indexOf("ipod")		!= -1
					|| (u.indexOf("android")	!= -1 && u.indexOf("mobile") != -1)
					|| (u.indexOf("firefox")	!= -1 && u.indexOf("mobile") != -1)
					||  u.indexOf("blackberry")	!= -1
					};
				})(window.navigator.userAgent.toLowerCase());
				clickEventType = (_ua.Tablet || _ua.Mobile) && window.ontouchstart !== 'undefined' ? 'touchstart' : 'click';
			};

			// animation:zoom のスケール設定
			var scale_val;
			if (options.zoom_type == 'in')
				scale_val = 1;
			else if (options.zoom_type == 'out')
				scale_val = 1.2;

			// スライダーを無効化するオプション値を解析
			var deactivate_operator, deactivate_width;
			if (options.deactivate_width) {
				var deactivate_width_arr = options.deactivate_width.match(/^([<>])\s*([0-9]+)$/);
				deactivate_operator = deactivate_width_arr[1];
				deactivate_width = deactivate_width_arr[2];
			}

			// prev/next button
			var prev_btn, next_btn;
			if (options.prev_btn_class)
				prev_btn = $(options.prev_btn_class, target);
			else
				prev_btn = $('.visual-animate__btn-prev', target);
			if (options.next_btn_class)
				next_btn = $(options.next_btn_class, target);
			else
				next_btn = $('.visual-animate__btn-next', target);

			// クリックイベントの設定
			var addClickEvent = function() {
				if (options.next_btn_class)
					$(target, document).on(clickEventType, options.next_btn_class, nextClick);
				else
					$(target, document).on(clickEventType, '.visual-animate__btn-next span', nextClick);
				if (options.slide_click){
					if (!_ua.Tablet && !_ua.Mobile)
						$(target, document).on(clickEventType, '.visual-animate__slider', nextClick);
				}
				if (options.prev_btn_class)
					$(target, document).on(clickEventType, options.prev_btn_class, prevClick);
				else
					$(target, document).on(clickEventType, '.visual-animate__btn-prev span', prevClick);
				if (options.dot)
					$(target, document).on(clickEventType, 'ul.visual-animate__dot li:not(".current")', dotClick);

				if ((_ua.Tablet || _ua.Mobile) && options.swaip) {
					$(target, document).on('touchstart', function(event) {
						touchMoveDirection = '';
						touchstartPosHorizontal = event.originalEvent.touches[0].pageX;
						touchstartPosVertical = event.originalEvent.touches[0].screenY;
						touchReturnPos = parseInt(ul_obj.position().left);
						//console.log('touchstart');
					});
					$(target, document).on('touchend', function(event) {
						touchendPosHorizontal = event.originalEvent.changedTouches[0].pageX;
						touchendPosVertical = event.originalEvent.changedTouches[0].screenY;
						if (touchMoveDirection == 'horizontal') {
							if (touchendPosHorizontal < touchstartPosHorizontal ) {
								if (colindex != (len - 1) || options.loop) {
									nextClick();
								} else {
									leftp = ((startp + ((slwidth + margin) * colindex)) * -1);
									ul_obj.animate({
										left: leftp
									}, options.speed, 'swing');
								}
							} else if (touchendPosHorizontal > touchstartPosHorizontal ) {
								if (colindex != 0 || options.loop) {
									prevClick();
								} else {
									leftp = ((startp + ((slwidth + margin) * colindex)) * -1);
									ul_obj.animate({
										left: leftp
									}, options.speed, 'swing');
								}
							}
						}
						//console.log('touchend');
					});
					if (options.animate == 'slide') {
						$(target, document).on('touchmove', function(event) {
							if (!touchMoveDirection && (-10 > (event.originalEvent.touches[0].screenY - touchstartPosVertical) || (event.originalEvent.touches[0].screenY - touchstartPosVertical) > 10)) {
								touchMoveDirection = 'vertical';
								removeClickEvent();
								$(target, document).on('touchend', function(event) {
									addClickEvent();
								});
								//console.log(touchMoveDirection);
							} else if (-10 > (event.originalEvent.touches[0].pageX - touchstartPosHorizontal) || (event.originalEvent.touches[0].pageX - touchstartPosHorizontal) > 10 ) {
								touchMoveDirection = 'horizontal';
								event.preventDefault();
								var touchmovePos = event.originalEvent.touches[0].pageX;
								ul_obj
									.css('left', touchReturnPos - (touchstartPosHorizontal - touchmovePos));
								//console.log(touchMoveDirection);
							}
						});
					}
				}
			};

			// クリックイベントを解除
			var removeClickEvent = function() {
				get_ua();
				if (options.prev_btn_class) $(target, document).off('click', options.prev_btn_class);
				if (options.next_btn_class) $(target, document).off('click', options.next_btn_class);
				$(target, document).off('click');
				if ((_ua.Tablet || _ua.Mobile)) {
					if (options.prev_btn_class) {
						$(target, document).off('touchstart', options.prev_btn_class);
						$(target, document).off('touchend', options.prev_btn_class);
						$(target, document).off('touchmove', options.prev_btn_class);
					}
					if (options.next_btn_class) {
						$(target, document).off('touchstart', options.next_btn_class);
						$(target, document).off('touchend', options.next_btn_class);
						$(target, document).off('touchmove', options.next_btn_class);
					}
					$(target, document).off('touchstart');
					$(target, document).off('touchend');
					$(target, document).off('touchmove');
				}
			};

			// ループしない場合のボタン制御
			var no_loop_btn_ctl = function() {
				if (!options.loop) {
					// 前へ
					if (colindex > default_colindex)
						prev_btn.show();
					else if (colindex == default_colindex)
						prev_btn.hide();

					// 次へ
					if (colindex < (len - 1 - default_colindex))
						next_btn.show();
					else if (colindex == (len - 1 - default_colindex))
						next_btn.hide();
				}
			};

			// ドットをクリックしたときのイベント
			var dotAnimate = function() {
				switch (options.animate) {
					case 'slide':
						slideStart('dot');
						break;
					case 'fade':
						fadeStart('dot');
						break;
					case 'zoom':
						zoomDelay('dot');
						break;
				}
			};

			// ドットをクリックしたときの処理
			var dotClick = function() {
				if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);
				colindex = $('ul.visual-animate__dot li', target).index(this);
				colindex = colindex + default_colindex;
				dotAnimate();
			};

			// ドットをリセット
			var dotReset = function() {
				if (options.dot) {
					$('ul.visual-animate__dot li', target).removeClass('current');
					$($('ul.visual-animate__dot li', target)[0]).addClass('current');
				}
			};

			// ドットの現在位置処理
			var doting = function() {
				var index;
				if ( colindex < default_colindex )
					index = len - default_colindex + colindex;
				else
					index = colindex - default_colindex;
				if (options.dot) {
					$('ul.visual-animate__dot li', target).removeClass('current');
					$($('ul.visual-animate__dot li', target)[index]).addClass('current');
				}
			};

			// サムネイルをクリックしたときのイベント
			if (options.thumbnail) {
				$(options.thumbnail).click(function() {
					$(options.thumbnail).removeClass('current');
					$(this).addClass('current');
					colindex = $(this).index();
					slideStart('dot');
					stop_autoplay();
				});
			}

			// サムネイルの現在位置
			var thumbnail_add_current = function(colindex) {
				if (options.thumbnail) {
					$(options.thumbnail).removeClass('current');
					$($(options.thumbnail)[colindex]).addClass('current');
				}
			};
			if (options.thumbnail) {
				$($(options.thumbnail)[0]).addClass('current');
			}

			// ＞をクリックしたときの処理
			var nextClick = function() {
				if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);

				// ループさせない＆スライドをクリックすると次へが有効な場合
				if (!options.loop && options.slide_click) {
					if (colindex == (len - 1))
						return false;
				}

				nextAnimate();
			};
			var nextAnimate = function() {
				switch (options.animate) {
					case 'slide':
						slideStart('next');
						break;
					case 'fade':
						fadeStart('next');
						break;
					case 'zoom':
						zoomDelay('next');
						break;
				}
			};

			// ＜をクリックしたときの処理
			var prevClick = function() {
				if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);
				prevAnimate();
			};
			var prevAnimate = function() {
				switch (options.animate) {
					case 'slide':
						slideStart('prev');
						break;
					case 'fade':
						fadeStart('prev');
						break;
					case 'zoom':
						zoomDelay('prev');
						break;
				}
			};

			// 自動スタート
			var stop_autoplay = function() {
				if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);
			};

			// スライド番号の表示処理
			var numing = function() {
				$(target).data('current-index', colindex);
				if (options.num)
					$('.visual-animate__num span', target).html((colindex + 1) + ' / ' + len);
			};

			// animate:slide 開始処理
			var slideStart = function(type) {
				if (typeof type === 'undefined')
					type = false;
				removeClickEvent();
				switch (type) {
					case 'next':
						stop_autoplay();
						leftp = slideStartPos - (slwidth + margin) * options.distance;
						break;
					case 'prev':
						stop_autoplay();
						leftp = slideStartPos + (slwidth + margin) * options.distance;
						break;
					case 'dot':
						stop_autoplay();
						if (options.type == 'copy') {
							leftp = ((startp + ((slwidth + margin) * colindex)) * -1);
						} else if (!options.type || options.type == 'pos') {
							if (ul_obj.find('.visual-animate__last').index() === 0) {
								if (colindex === (len - 1))
									leftp = (((slwidth + margin) * colindex) * -1) + (slwidth + margin);
								else
									leftp = (((slwidth + margin) * colindex) * -1) - (slwidth + margin);
							} else if (ul_obj.find('.visual-animate__first').index() === (len - 1)) {
								leftp = (((slwidth + margin) * colindex) * -1) + (slwidth + margin);
							} else {
								leftp = (((slwidth + margin) * colindex) * -1);
							}
						}
						break;
					default:
						leftp = slideStartPos - (slwidth + margin);
						slideTimer = setTimeout(slideStart, options.interval + options.speed);
						break;
				}

				ul_obj
					.animate({
						left: leftp
					}, options.speed, 'swing', function() {
						if (type)
							if (typeof slideTimer !== 'undefined') slideTimer = setTimeout(slideStart, options.interval + options.speed);

						switch (type) {
							case 'next':
								stop_autoplay();
							case false:
								colindex += options.distance;
								if (colindex >= len) {
									$(this).css('left', '-' + startp + 'px');
									colindex = 0;
								}
								break;
							case 'prev':
								stop_autoplay();
								colindex -= options.distance;
								if (colindex < 0) {
									$(this).css('left', '-' + (startp + ((slwidth + margin) * ( len - 1))) + 'px');
									colindex = len - 1;
								}
								break;
							case 'dot':
								stop_autoplay();
								break;
						}
						no_loop_btn_ctl();

						if (options.cont_anim) {
							$('li .visual-animate__contents', this).css(contents_style_default);
							$($('> li', this)[colindex + len]).find('.visual-animate__contents').animate(contents_style_show, 1000, 'swing');
						}
						slideStartPos = $(this).position().left;
						addClickEvent();
						doting();
						numing();

						if ((!options.type || options.type == 'pos') && options.loop) {
							// 最後のスライドのとき、
							if (colindex === len - 1) {
								// 最後のスライドを最後に移動
								if (ul_obj.find('.visual-animate__last').index() === 0) {
									slideStartPos =  slideStartPos - (slwidth + margin);
									ul_obj.css({'left': slideStartPos}).find('.visual-animate__last').insertAfter(ul_obj.find('> li:last-child'));
								}

								// 1枚目を最後に移動
								if (ul_obj.find('.visual-animate__first').index() === 0) {
									slideStartPos =  slideStartPos + (slwidth + margin);
									ul_obj.css({'left': slideStartPos}).find('.visual-animate__first').insertAfter(ul_obj.find('> li:last-child'));
								}
							}
							// 最初のスライドのとき
							else if (colindex === 0) {
								slideStartPos = (slwidth + margin) * -1;
								ul_obj.css({
									'left': slideStartPos,
									'padding-left': 0
								});
								// 1枚目を元の位置に戻す
								ul_obj.find('.visual-animate__first').insertBefore(ul_obj.find('> li:first-child'));
								// 最後のスライドを一番最初に移動
								ul_obj.find('.visual-animate__last').insertBefore(ul_obj.find('> li:first-child'));
							}
							// 最初と最後以外のときはデフォルトの配置に戻る
							else {
								if (ul_obj.find('.visual-animate__last').index() === 0) {
									slideStartPos = slideStartPos + (slwidth + margin);
									ul_obj.css({'left': slideStartPos}).find('.visual-animate__last').insertAfter(ul_obj.find('> li:last-child'));
								}
								if (ul_obj.find('.visual-animate__first').index() !== 0) {
									slideStartPos = slideStartPos - (slwidth + margin);
									ul_obj.css({'left': slideStartPos}).find('.visual-animate__first').insertBefore(ul_obj.find('> li:first-child'));
								}
							}
						}

						thumbnail_add_current(colindex);
					});

				if (options.cont_anim) {
					if (type == 'dot') {
						ul_obj.find('> li .visual-animate__contents').animate(contents_style_hide, 1000, 'swing', function() {
							$(this).css(contents_style_default);
						});
					} else {
						$(ul_obj.find('> li')[colindex + len]).find('.visual-animate__contents').animate(contents_style_hide, 1000, 'swing');
					}
				}
			};

			// animate:fade 開始処理
			var fadeStart = function(type) {
				if (typeof type === 'undefined')
					type = false;
				removeClickEvent();
				var obj_backward = type == 'dot' ? ul_obj.find('> li:visible') : $(ul_obj.find('> li')[colindex]);
				obj_backward.animate({
					opacity: 'hide'
				}, options.speed, 'swing', function() {
					addClickEvent();
					if (type)
						if (typeof slideTimer !== 'undefined') slideTimer = setTimeout(startAnimation, options.interval + options.speed);
					switch (type) {
						case 'next':
							stop_autoplay();
						case false:
							colindex++;
							if (colindex >= len) {
								colindex = 0;
							}
							break;
						case 'prev':
							stop_autoplay();
							colindex--;
							if (colindex < 0) {
								colindex = len - 1;
							}
							break;
					}
					no_loop_btn_ctl();
					doting();
					numing();
				});
				if (options.cont_anim) {
					obj_backward.find('.visual-animate__contents').animate(contents_style_hide, 1000, 'swing', function() {
						$(this).css(contents_style_default);
					});
				}
				var obj_current;
				switch (type) {
					case 'next':
						stop_autoplay();
					case false:
						obj_current = obj_backward.next('li').length ? obj_current = obj_backward.next('li') : obj_current = ul_obj.find('> li:first');
						if (!type)
							slideTimer = setTimeout(fadeStart, options.interval + options.speed);
						break;
					case 'prev':
						stop_autoplay();
						obj_current = obj_backward.prev('li').length ? obj_current = obj_backward.prev('li') : obj_current = ul_obj.find('> li:last');
						break;
					case 'dot':
						stop_autoplay();
						obj_current = $(ul_obj.find('> li')[colindex]);
						break;
				}
				obj_current.animate({
					opacity: 'show'
				}, options.speed, 'swing');
				if (options.cont_anim) {
					obj_current.find('.visual-animate__contents').animate(contents_style_show, 1000, 'swing');
				}
			};

			// animate:zoom 開始処理
			var zoomStart = function() {
				var flg = false;
				removeClickEvent();
				$(ul_obj.find('> li')[colindex])
					.show()
					.animate({
						'margin-bottom': 1
					}, {
						duration : options.interval + options.speed * 2,
						step: function (s) {
							if (options.zoom_type == 'in')
								$(this).css('transform', 'scale(' + (scale_val + (s * 0.2)) + ')');
							else if (options.zoom_type == 'out')
								$(this).css('transform', 'scale(' + (scale_val - (s * 0.2)) + ')');
							if (s >= (1 - options.speed / (options.interval + options.speed * 2)) && !flg) {
								flg = true;
								if (len > column && options.autoStart) {
									$('.visual-animate__bg', this).fadeOut(options.speed, 'swing', function() {
										$(this).parents('li').first().hide();
									});
									colindex++;
									if (colindex >= len) {
										colindex = 0;
									}
									no_loop_btn_ctl();
									zoomStart();
								}
								doting();
								numing();
							}
						},
						complete : function() {
							$(this).animate({
								'margin-bottom': 0,
								'transform': 'scale(' + scale_val + ')'
							}, 0);
						}
					})
					.find('.visual-animate__bg').fadeIn(options.speed, 'swing', function() {
						addClickEvent();
					});
				if (options.cont_anim) {
					$(ul_obj.find('> li')[colindex]).find('.visual-animate__contents').animate(contents_style_show, 1000, 'swing');
				}
			};
			var zoomDelay = function(type) {
				if (typeof type === 'undefined')
					type = false;
				removeClickEvent();

				var obj = type == 'dot' ? ul_obj.find('> li:visible') : $(ul_obj.find('> li')[colindex]);
				obj.stop(true, false)
					.animate({
						'margin-bottom': 0,
						'transform': 'scale(' + scale_val + ')'
					}, 0)
					.find('.visual-animate__bg').fadeOut(options.speed, 'swing', function() {
						$(this).parents('li').first().hide();
					});
				if (type != 'dot') {
					switch (type) {
						case 'prev':
							colindex--;
							if (colindex < 0) {
								colindex = len - 1;
							}
							break;
						default:
							colindex++;
							if (colindex >= len) {
								colindex = 0;
							}
							break;
					}
					no_loop_btn_ctl();
				}
				doting();
				numing();
				zoomStart();
			};

			// スライドの開始
			var startAnimation = function() {
				if (options.delay > 0) {
					setTimeout(function() {
						switch (options.animate) {
							case 'slide':
								slideStart(false);
								break;
							case 'fade':
								fadeStart(false);
								break;
							case 'zoom':
								zoomStart(false);
								break;
						}
					}, options.delay);
				} else {
					switch (options.animate) {
						case 'slide':
							slideStart(false);
							break;
						case 'fade':
							fadeStart(false);
							break;
						case 'zoom':
							zoomStart(false);
							break;
					}
				}
			};

			// <img>タグの背景化処理
			var to_bg = function() {
				if (options.to_bg) {
					ul_obj.find('> li').each(function() {
						var srcset = '';
						if (srcset = $(this).find('figure img').data('srcset')) {
							srcset = srcset.split(',');
							for (var i = 0; i < srcset.length; i++) {
								var rslt = srcset[i].match(/^(.+?)\s+([0-9]+?)w$/);
								if (rslt[2] >= winW) {
									$('.visual-animate__bg', this).css({
										'background-image': "url('" + rslt[1] + "')"
									});
								} else {
									$('.visual-animate__bg', this).css({
										'background-image': "url('" + $(this).find('figure img').attr('src') + "')"
									});
								}
							}
						} else {
							var src = $(this).find('figure img').attr('src');
							$('.visual-animate__bg', this).css({
								'background-image': "url('" + src + "')"
							});
						}
						$(this).find('figure').hide();
					});
				}
			};
			
			var set_btns = function(winW, activate) {
				var column;
				if (activate == 'activate') {
					if (typeof options.multicolumn === 'object') {
						var flen = Object.keys(options.multicolumn).length;
						for (let i = 0; i < flen; i++) {
							let device_width = Object.keys(options.multicolumn)[i];
							if (winW <= device_width) {
								column = options.multicolumn[device_width];
								if (len <= column) {
									prev_btn.hide();
									next_btn.hide();
								} else {
									// ループさせない場合はボタンを制御
									if (options.loop)
										prev_btn.show();
									else
										prev_btn.hide();
									next_btn.show();
								}
								//return true;
								break;
							}
						}
					} else {
						column = options.multicolumn;
						if (len <= column) {
							prev_btn.hide();
							next_btn.hide();
						} else {
							// ループさせない場合はボタンを制御
							if (options.loop)
								prev_btn.show();
							else
								prev_btn.hide();
							next_btn.show();
						}
					}
				} else {
					prev_btn.hide();
					next_btn.hide();
				}
			};
			
			var set_colindex = function(winW, activate) {
				if (activate == 'activate') {
					if (typeof options.colindex === 'object') {
						var flen = Object.keys(options.colindex).length;
						for (let i = 0; i < flen; i++) {
							let device_width = Object.keys(options.colindex)[i];
							if (winW <= device_width) {
								colindex = options.colindex[device_width];
								default_colindex = colindex;
								return true;
							}
						}
					} else {
						colindex = options.colindex;
					}
				}
			};

			// コンテンツの初期スタイル
			var contents_style_default = {
				bottom: -10,
				opacity: 0,
				position: 'relative'
			};

			// コンテンツの表示スタイル
			var contents_style_show = {
				bottom: 0,
				opacity: 1
			};

			// コンテンツの非表示スタイル
			var contents_style_hide = {
				bottom: 10,
				opacity: 0
			};

			// HTML調整
			ul_obj.wrap('<div class="visual-animate__layer"><div class="visual-animate__layer__inner"></div></div>');
			ul_obj.find('> li').wrapInner('<div class="visual-animate__bg"><div class="visual-animate__bg__layer"><div class="visual-animate__bg__layer__inner"><div class="visual-animate__contents"></div></div></div></div>');
			if (options.cont_anim) {
				ul_obj.find('.visual-animate__contents').css(contents_style_default);
			}
			ul_obj.addClass(options.animate);

			// 最初と最後のスライドにクラスを付与する
			ul_obj.find('> li:first-child').addClass('visual-animate__first');
			ul_obj.find('> li:last-child').addClass('visual-animate__last');

			var load_slide = function() {
				// 表示状態になるまで処理を待つ
				var delayTimer = false;
				var delay_processing = function() {
					delayTimer = setTimeout(function() {
						if (!$(target).is(":hidden")) {
							clearTimeout(delayTimer);
							if (resizeTimer !== false)
								clearTimeout(resizeTimer);
							resizeTimer = setTimeout(function() {
								winW_old = winW;
								device_old = device;
								activate_old = activate;
								if (typeof (winW = window.innerWidth) === 'undefined')
									winW = $(document).outerWidth();

								if (winW_old !== winW) {
									get_column(winW);
	
									if (options.type == 'copy' && len > column && options.loop == true) {
										var html = ul_obj.html();
										html = html.replace(/<li ([^>]*?)class="([^\"]+)"([^>]*?)>/g, '<li $1class="$2 copy"$3>');
										html = html.replace(/<li>/g, '<li class="copy">');
				
										if (!$('li.copy', target).length) {
											if (column < len) {
												ul_obj
													.append(html)
													.append(html);
											}
										}
	
										$('ul.visual-animate__dot', target).show();
									} else {
										$('li.copy', target).remove();
										//$('ul.visual-animate__dot', target).hide();
									}

									ul_obj.stop(true, true);

									activate = ((deactivate_operator && deactivate_width) && (deactivate_operator == '>' && winW > deactivate_width) || (deactivate_operator == '<' && winW < deactivate_width)) ? 'deactivate' : 'activate';

									// カラム数に満たない場合はボタンを表示しない
									set_btns(winW, activate);

									// colindex
									set_colindex(winW, activate);

									if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);
									if (activate == 'deactivate') {
										// 無効化クラス付与
										$(target).parent().addClass('deactivate');

										//$(target).outerHeight('auto');
										ul_obj
											.outerWidth('100%');
											//.find('> li').outerHeight('auto');
										colindex = 0;
									} else {
										get_ua();

										// 無効化クラス除去
										$(target).parent().removeClass('deactivate');

										// 数値再取得
										slwidth = $('.visual-animate__layer__inner', target).outerWidth();
										margin = parseInt($(ul_obj.find('> li')[0]).css('margin-right'));
										$(target).outerHeight('100%');

										if (options.type == 'copy') {
											startp = (slwidth + margin) * len + (options.align_left ? column / 2 * (slwidth + margin) - slwidth / 2 - margin / 2 : 0);
											if (len > column) {
												ul_obj.css('left', '-' + ((slwidth + margin) * len + (slwidth + margin) * colindex) - (options.align_left ? column / 2 * (slwidth + margin) - slwidth / 2 - margin / 2 : 0));
											} else {
												ul_obj.css('left', $(target).width() / -2 + slwidth / 2 + (options.align_left ? 0 : (column - len) / 2 * (slwidth + margin)));
											}
											ul_obj
												.outerWidth((slwidth + margin) * len * 3)
												.find('> li').outerWidth(slwidth);
										} else if (!options.type || options.type == 'pos') {
											startp = (slwidth + margin) * -1;
											if (len > column) {
												if (options.loop) {
													ul_obj
														.css('left', startp)
														.find('.visual-animate__last').insertBefore(ul_obj.find('> li:first-child'));
												} else {
													ul_obj.css('left', '0');
												}
											} else {
												ul_obj.css('left', '0');
											}
											ul_obj
												.outerWidth((slwidth + margin) * len)
												.find('> li').outerWidth(slwidth);
										}

										if (len > column && options.autoStart)
											slideTimer = setTimeout(startAnimation, options.interval + options.speed);
										if (!options.to_bg) {
											ul_obj.find('> li').css('height', 'auto');
											$(target).outerHeight(ul_obj.height());
											ul_obj.find('> li').outerHeight(ul_obj.height());
										}
									}
									if (activate != activate_old) {
										if (activate == 'deactivate') {
											removeClickEvent();
											ul_obj
												.css('left', 0)
												.outerWidth(slwidth)
												.find('> li').first().find('.visual-animate__contents')
													.css(contents_style_show);
											prev_btn.hide();
											next_btn.hide();

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).hide();
												$('.visual-animate__num', target).hide();
											}

											if (options.type == 'copy') {
												$('li.copy', target).hide();
											}
										} else if (activate == 'activate') {
											addClickEvent();

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).show();
												$('.visual-animate__num', target).show();
											}

											if (options.type == 'copy') {
												$('li.copy', target).show();
											}
										}
									}
									slideStartPos = ul_obj.position().left;

									if (options.sp_bp) {
										if (winW <= options.sp_bp)
											device = 'sp';
										else
											device = 'pc';

										if ((device && device != device_old) || !device) {
											removeClickEvent();
											addClickEvent();
											if (activate == 'activate')
												to_bg();
										}
									}

									// srcset 属性があれば
									if (ul_obj.find('> li figure img').attr('srcset')) {
										if (activate == 'activate')
											to_bg();
									}

									// ドットをリセット
									dotReset();

									// サムネイルをリセット
									if (options.thumbnail) {
										$(options.thumbnail).removeClass('current');
										$($(options.thumbnail)[0]).addClass('current');
									}

									// 番号をリセット
									if (options.num) {
										$('.visual-animate__num').html('<div class=""><span>1/' + len + '</span></div>');
									}
								}
							}, 0);
						} else {
							delay_processing();
						}
					}, 200);
				};
				delay_processing();
			};

			var load_fade = function() {
				// 表示状態になるまで処理を待つ
				var delayTimer = false;
				var delay_processing = function() {
					delayTimer = setTimeout(function() {
						if (!$(target).is(":hidden")) {
							clearTimeout(delayTimer);
							if (resizeTimer !== false)
								clearTimeout(resizeTimer);
							resizeTimer = setTimeout(function() {
								winW_old = winW;
								device_old = device;
								activate_old = activate;
								if (typeof (winW = window.innerWidth) === 'undefined')
									winW = $(document).outerWidth();

								get_column(winW);
	
								slwidth = $('.visual-animate__layer__inner', target).outerWidth();
								if (winW_old !== winW) {
									activate = ((deactivate_operator && deactivate_width) && (deactivate_operator == '>' && winW > deactivate_width) || (deactivate_operator == '<' && winW < deactivate_width)) ? 'deactivate' : 'activate';

									// カラム数に満たない場合はボタンを表示しない
									set_btns(winW, activate);

									// colindex
									set_colindex(winW, activate);

									if (activate == 'deactivate') {
										$(target).parent().addClass('deactivate');
									} else {
										get_ua();
										$(target).parent().removeClass('deactivate');
									}
									if (activate != activate_old) {
										if (activate == 'deactivate') {
											removeClickEvent();
											if (typeof slideTimer !== 'undefined') clearTimeout(slideTimer);
											ul_obj.find('> li').hide();
											ul_obj.find('> li:first').show();
											ul_obj.find('> li').first().find('.visual-animate__contents').css('opacity', 1);
											prev_btn.hide();
											next_btn.hide();

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).hide();
												$('.visual-animate__num', target).hide();
											}
										} else if (activate == 'activate') {
											addClickEvent();
											if (len > column && options.autoStart)
												slideTimer = setTimeout(startAnimation, options.interval);

											ul_obj.find('> li').hide();
											$(ul_obj.find('> li')[colindex]).show();

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).show();
												$('.visual-animate__num', target).show();
											}
										}
									}

									if (options.sp_bp) {
										if (winW <= options.sp_bp)
											device = 'sp';
										else
											device = 'pc';

										if ((device && device != device_old) || !device) {
											removeClickEvent();
											addClickEvent();
											if (activate == 'activate')
												to_bg();
										}
									}

									// srcset 属性があれば
									if (ul_obj.find('> li figure img').attr('srcset')) {
										if (activate == 'activate')
											to_bg();
									}

									// ドットをリセット
									dotReset();
								}
							}, 0);
						} else {
							delay_processing();
						}
					}, 200);
				};
				delay_processing();
			};

			var load_zoom = function() {
				// 表示状態になるまで処理を待つ
				var delayTimer = false;
				var delay_processing = function() {
					delayTimer = setTimeout(function() {
						if (!$(target).is(":hidden")) {
							clearTimeout(delayTimer);
							if (resizeTimer !== false)
								clearTimeout(resizeTimer);
							resizeTimer = setTimeout(function() {
								winW_old = winW;
								device_old = device;
								activate_old = activate;
								if (typeof (winW = window.innerWidth) === 'undefined')
									winW = $(document).outerWidth();

								get_column(winW);
	
								slwidth = $('.visual-animate__layer__inner', target).outerWidth();
								if (winW_old !== winW) {
									activate = ((deactivate_operator && deactivate_width) && (deactivate_operator == '>' && winW > deactivate_width) || (deactivate_operator == '<' && winW < deactivate_width)) ? 'deactivate' : 'activate';

									// カラム数に満たない場合はボタンを表示しない
									set_btns(winW, activate);

									// colindex
									set_colindex(winW, activate);

									if (activate == 'deactivate') {
										$(target).parent().addClass('deactivate');
									} else {
										get_ua();
										$(target).parent().removeClass('deactivate');
									}
									if (activate != activate_old) {
										if (activate == 'deactivate') {
											removeClickEvent();
											ul_obj.find('> li').stop(true, false).hide();
											ul_obj.find('> li:first')
												.show()
												.find('.visual-animate__bg')
													.show();
											prev_btn.hide();
											next_btn.hide();

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).hide();
												$('.visual-animate__num', target).hide();
											}
										} else if (activate == 'activate') {
											addClickEvent();
											if (len > column && options.autoStart) {
												startAnimation();
											} else {
												ul_obj.find('> li').hide();
												$(ul_obj.find('> li')[colindex])
													.show()
													.find('.visual-animate__bg')
														.show()
														.find('.visual-animate__contents')
															.css(contents_style_show);
											}

											if (options.type == 'copy' && len > column && options.loop == true) {
												$('ul.visual-animate__dot', target).show();
												$('.visual-animate__num', target).show();
											}
										}
									}

									if (options.sp_bp) {
										if (winW <= options.sp_bp)
											device = 'sp';
										else
											device = 'pc';

										if ((device && device != device_old) || !device) {
											removeClickEvent();
											addClickEvent();
											if (activate == 'activate')
												to_bg();
										}
									}

									// srcset 属性があれば
									if (ul_obj.find('> li figure img').attr('srcset')) {
										if (activate == 'activate')
											to_bg();
									}

									// ドットをリセット
									dotReset();
								}
							}, 0);
						} else {
							delay_processing();
						}
					}, 200);
				};
				delay_processing();
			};

			switch (options.animate) {
				case 'slide':
					slwidth = $('.visual-animate__layer__inner', target).outerWidth();
					$(window).on('resize orientationchange', load_slide);
					load_slide();

					$(ul_obj.find('> li')[len]).find('.visual-animate__contents').css('opacity', 1);
					break;
				case 'fade':
					slwidth = $('.visual-animate__layer__inner', target).outerWidth();
					$(window).on('resize orientationchange', load_fade);
					load_fade();
					ul_obj.find('> li').hide();
					ul_obj.find('> li:first').show();
					ul_obj.find('> li').first().find('.visual-animate__contents').css('opacity', 1);
					break;
				case 'zoom':
					slwidth = $('.visual-animate__layer__inner', target).outerWidth();
					$(window).on('resize orientationchange', load_zoom);
					load_zoom();
					ul_obj.find('> li').hide();
					ul_obj.find('> li:first').show();
					ul_obj.find('> li').each(function(i) {
						$(this).css({
							'margin-bottom': 0,
							'transform': 'scale(' + scale_val + ')'
						}).find('.visual-animate__bg').hide();
					});
					break;
			}

			if (!ul_obj.find('> li figure img').attr('srcset') && !options.sp_bp) {
				to_bg();
			}

			// 前後のスライドにマスクをかける
			if (options.highlight && len > column) {
				$(target).append('<div class="visual-animate__overlay visual-animate__overlay--left"></div><div class="visual-animate__overlay visual-animate__overlay--right"></div>');
			}

			if (len > column) {
				if (options.dot && !$('ul.visual-animate__dot', target).length) {
					$(target).append('<ul class="visual-animate__dot"></ul>');
					for (i = 0; i < len; i++) {
						if (i === 0)
							$('ul.visual-animate__dot', target).append('<li class="current"></li>');
						else
							$('ul.visual-animate__dot', target).append('<li></li>');
					}
				}
				if (options.num && !$('.visual-animate__num', target).length) {
					$(target).append('<div class="visual-animate__num"><span>1/' + len + '</span></div>');
				}
			} else {
				prev_btn.hide();
				next_btn.hide();
			}

			// 最初に戻す
			/*
			if ($('.visual-animate__btn-back').length) {
				$('.visual-animate__btn-back').on('click', function() {
					$('.visual-animate__btn-back').hide();
					$('.visual-animate__btn-prev').hide();
					$('.visual-animate__btn-next').hide();
					ul_obj.animate({
						left: 0
					}, options.speed, 'swing', function() {
						colindex = 0;
						slideStartPos = 0;
						$('.visual-animate__btn-next').show();
					});
				});
			}
			*/
		});
	};
})(jQuery);
