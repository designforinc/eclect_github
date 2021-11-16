jQuery(function($) {
	$('.slider .visual-animate').visualAnimate({
		animate: 'slide',
		speed: 300,
		interval: 5000,
		dot: true,
		to_bg: false,
		autoStart: false
	});
	$('.case-items .visual-animate').visualAnimate({
		animate: 'slide',
		speed: 300,
		interval: 5000,
		dot: true,
		to_bg: false,
		autoStart: false,
		deactivate_width: '> 768'
	});
});
