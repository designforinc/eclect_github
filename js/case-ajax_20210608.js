jQuery(function($) {
	var taxonomy = '';
	var term_slug = '';
	var page_num = 1;
	var ajax_query_posts = function () {
		$.ajax({
			url: case_items.ajax_url,
            type: 'POST',
			dataType: 'json',
			data: {
				action: 'case',
				page_num: page_num,
				taxonomy: taxonomy,
				term_slug: term_slug
			},
			async: true
		}).done(function(result){
			$('.mod-btn3').removeClass('loading');
			$('.case-items').append(result['html']);
			if (result['more'])
				$('.mod-btn3').show();
			else
				$('.mod-btn3').hide();
		});
	}

	$('.mod-btn3').click(function () {
		page_num++;
		if ($('input[name="taxonomy"]').length)
			taxonomy = $('input[name="taxonomy"]').val();
		if ($('input[name="term_slug"]').length)
			term_slug = $('input[name="term_slug"]').val();
		ajax_query_posts();
		$('.mod-btn3').addClass('loading');
		return false;
	});
});
