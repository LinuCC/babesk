var oldHeadModule = '';

function getHeadModuleIdentifier(mod_ident) {

	var mod_ident_arr = mod_ident.split('|');
	return mod_ident_arr [0];
}

function makeTooltipDisappear() {
	$('#ToolTip').hide("clip", 500);
}

$(document).ready(function() {

	oldHeadModule = '';

	/**
	 * If clicked on the Headmodul-Button, Show all Modules available
	 * @param  {[type]} event [description]
	 * @return {[type]}       [description]
	 */
	$('.HeadItem').on('click', function(event){

		event.preventDefault();

		var headModule = $(this);

		makeTooltipDisappear();
		if(oldHeadModule != headModule.attr('id')) {
			headModule.addClass('selected');
		}

		if(oldHeadModule != '') {
			$('#' + oldHeadModule).removeClass('selected');
		}

		$('.menu_item').each(function(index) {
			var elHeadmod = getHeadModuleIdentifier($(this).attr('id'));
			if(elHeadmod == headModule.attr('id')) {
				$(this).show(500);
			}
			else {
				$(this).hide(500);
			}
		});

		oldHeadModule = headModule.attr('id');
	});
});