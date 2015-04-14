$(document).ready(function() {
	$('#sidebar-module-selection .sidebar-folder').on('click',
		function(ev) {
		$icon = $(this).find('.toggle-icon:first');
		$icon.toggleClass('fa-plus');
		$icon.toggleClass('fa-minus');
	});

	$('.sidebar-toggle').on('click', function(ev) {
		$('#body-wrapper').toggleClass('show-sidebar');
	});

	if($('body').width() < 1000) {
		$('#body-wrapper').removeClass('show-sidebar');
	}
});