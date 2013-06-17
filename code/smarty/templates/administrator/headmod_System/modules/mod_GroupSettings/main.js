$(document).ready(function() {

	$(document).tooltip();
	var showRights = false;
	var groupChanges = [];

	$('#groupAdd').on('click', function(ev) {
		$('div.groupTree').jstree(
			'create',
			$('div.groupTree .jstree-clicked'),
			'last',
			{data: 'Neue Gruppe'}
		);
	});

	$('#groupRemove').on('click', function(ev) {
		$('div.groupTree').jstree('remove');
	});

	$('#groupRename').on('click', function(ev) {
		$('div.groupTree').jstree('rename');
	});

	$('div.groupTree').on('dblclick', 'li a', function(ev) {
		$('div.groupTree').jstree('rename');
	});

	$('div.groupTree').on('click', 'li a', function(ev) {
		//wait for jstree to change the class of the object
		setTimeout(function() {
			if($('div.groupTree .jstree-clicked').length) {
				groupButtonsLock(false);
				if(showRights) {
					rightsUpdate();
				}
			}
			else {
				groupButtonsLock(true);
			}
		}, 50);

	});

	$('#groupChangeRights').on('click', function(ev) {

		showRights = !showRights;
		if(showRights) {
			rightsUpdate();
		}
		else {
			$('fieldset.groupRights').hide(500);
		}
	});

	$('#groupSubmit').on('click', function(ev) {
		$('body').prepend('<div id="groupSubmitConfirm" title="Gruppen wirklich ändern?"><p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;""></span>Die Gruppen werden dauerhaft geändert. Sicher?</p></div>');

		$( "#groupSubmitConfirm" ).dialog({
			resizable: false,
			height: 240,
			modal: true,
			buttons: {
				"Ändern": function() {
					$(this).dialog("close");
				},
				"Nicht ändern": function() {
					$(this).dialog("close");
				}
			}
		});
	});

	var groupsFetch = function groupsFetch() {
		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|GroupSettings&action=groupsFetch',
			data: {},

			success: function(data) {

				console.log(data);

				try {
					var res = $.parseJSON(data);
				} catch(e) {
					adminInterface.errorShow('Error parsing the server' +
						'response');
					return;
				}
				if(res) {
					if(res.value == 'success') {
						console.log(res.data);
						groupsTreeUpdate(res.data);
					}
					else if(res.value == 'error') {
						adminInterface.errorShow(res.message);
					}
					else {
						adminInterface.errorShow('Server returned unknown' +
							'value');
					}
				}
				else {
					adminInterface.errorShow('Error parsing the server' +
						'response');
					return;
				}
			},

			error: function(error) {
				adminInterface.errorShow('Ein Fehler ist beim verbinden mit' +
					'dem Server aufgetreten!');
			}
		});
	};

	var groupsTreeUpdate = function groupsTreeUpdate(groupTree) {

		$('div.groupTree').jstree({
			'json_data': {
				'data': groupTree
			},
			'themes': {
				'theme': 'apple',
				'icons': false
			},
			'plugins' : [ 'themes', 'json_data', 'ui', 'crrm' ]
		}).bind("create.jstree", function (event, data) {
			parentPath = $('div.groupTree').jstree('get_path', data.rslt.obj)
				.slice(0,-1).join('/');
			groupChanges.push({
				'action': 'add',
				'parentPath': parentPath,
				'name': data.rslt.name
			});
		}).bind("rename.jstree", function (event, data) {
			parentPath = $('div.groupTree').jstree('get_path', data.rslt.obj)
				.slice(0,-1).join('/');
			groupChanges.push({
				'action': 'rename',
				'parentPath': parentPath,
				'oldName': data.rslt.old_name,
				'newName': data.rslt.new_name
			});
		}).bind("remove.jstree", function (event, data) {
			parentPath = $('div.groupTree').jstree('get_path', data.rslt.obj)
				.slice(0,-1).join('/');
			toDelete = data.rslt.obj.children('a').text();
			groupChanges.push({
				'action': 'delete',
				'parentPath': parentPath,
				'name': toDelete
			})
		});
	};

	var groupButtonsLock = function groupButtonsLock(lock) {

		if(lock) {
			$('button#groupAdd').prop('disabled', true);
			$('button#groupRemove').prop('disabled', true);
			$('button#groupRename').prop('disabled', true);
			$('button#groupChangeRights').prop('disabled', true);
		}
		else {
			$('button#groupAdd').prop('disabled', false);
			$('button#groupRemove').prop('disabled', false);
			$('button#groupRename').prop('disabled', false);
			$('button#groupChangeRights').prop('disabled', false);
		}
	};

	var rightsUpdate = function rightsUpdate() {

		$('fieldset.groupRights').show(500);
		clickedGroup = $('div.groupTree .jstree-clicked');
		if(clickedGroup.length) {
			clickedGroup.children('ins').remove();
			$('.selectedGroupName').html(clickedGroup.html());
		}
		else {
			alert('Wählen sie zuerst eine Gruppe aus!');
		}
	}

	groupButtonsLock(true);
	groupsFetch();
	$('fieldset.groupRights').hide();

});
