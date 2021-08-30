jQuery(function($) {
	var page_num = 1;
	var hash = parseInt(window.location.hash.replace('#', ''));
	if (Number.isInteger(hash))
		page_num = hash;
	var perpage = 20;
	var flag = false;
	var get_posts = function() {
		for (let i = 0; i <= perpage * page_num; i++) {
			if ($($('.case-items__item')[i]).length) {
				if (i < perpage * page_num)
					$($('.case-items__item')[i]).show();
			} else {
				flag = true;
			}

			if (i == perpage * page_num) {
				if (flag)
					$('.mod-btn3').hide();
			}
		}
	};
	get_posts();
	$('.mod-btn3').click(function () {
		page_num++;
		get_posts();
		window.location.hash = page_num;
		return false;
	});
});
