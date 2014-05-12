$(document).ready(function() {
	$('#sidebar-module-selection .sidebar-folder').on('click',
		function(ev) {
		$icon = $(this).find('.toggle-icon:first');
		$icon.toggleClass('icon-plus');
		$icon.toggleClass('icon-minus');
	});

	$('.sidebar-toggle').on('click', function(ev) {

		var $sidebar = $('.sidebar');
		var $mainWrapper = $('#main_wrapper');

		console.log(($sidebar.css('width')));

		if(parseInt($sidebar.css('width')) > 1) {
			$sidebar.animate({'width' : '0px'}, 400);
			$mainWrapper.animate({'margin-left': '0'}, 400);
		}
		else {
			$sidebar.animate({'width' : '250px'}, 400);
			$mainWrapper.animate({'margin-left': '250px'}, 400);
		}
	});
});