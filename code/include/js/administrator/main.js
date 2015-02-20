$(document).ready(function() {
	$('#sidebar-module-selection .sidebar-folder').on('click',
		function(ev) {
		$icon = $(this).find('.toggle-icon:first');
		$icon.toggleClass('fa-plus');
		$icon.toggleClass('fa-minus');
	});

	$('.sidebar-toggle').on('click', function(ev) {

		$('#body-wrapper').toggleClass('show-sidebar');
		return;
		var $sidebar = $('.sidebar');
		var $mainWrapper =

		console.log(($sidebar.css('width')));

		if(parseInt($sidebar.css('width')) > 1) {
			$sidebar.css('width', '0px');
			$mainWrapper.css('margin-left', '0px');
			//$sidebar.animate({'width' : '0px'}, 400);
			//$mainWrapper.animate({'margin-left': '0'}, 400);
		}
		else {
			$sidebar.css('width', '250px');
			$mainWrapper.css('margin-left', '250px');
			//$sidebar.animate({'width' : '250px'}, 400);
			//$mainWrapper.animate({'margin-left': '250px'}, 400);
		}
	});
});