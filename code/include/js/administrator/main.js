$(document).ready(function() {
	$('#sidebar-module-selection .sidebar-folder').on('click',
		function(ev) {
		$icon = $(this).find('.toggle-icon:first');
		$icon.toggleClass('icon-plus');
		$icon.toggleClass('icon-minus');
	});
});