

var rights = new function() {

	var showRights = false;
	var that = this;
	var tree = undefined;
	var tempScrollPos = 0;

	that.update = function() {

		if(!showRights) {
			return;
		}

		var path = group.selectedGroupPathGet();
		tempScrollPos = $(window).scrollTop();

		$('fieldset.grouprights').show(500);

		if(path.length) {

			$.ajax({
				type: 'POST',
				url: 'index.php?' +
					'section=System|GroupSettings&action=modulesFetch',
				data: {
					'grouppath': path
				},
				success: function(data) {
					updateResponseParse(data);
				},
				error: function(data) {
					adminInterface.errorShow(
						'Konnte die Rechte nicht abrufen!');
				}
			});

		}
		else {
			alert('Wählen sie zuerst eine Gruppe aus!');
		}
	};

	/**
	 * Parses the Server-Response from the update-function
	 */
	var updateResponseParse = function(data) {

		console.log(data);
		try {
			res = JSON.parse(data);
		} catch(e) {
			adminInterface.errorShow(
				'Konnte die Serverdaten nicht verarbeiten');
			return false;
		}
		if(res.value == 'success') {
			treeUpdate(res.data);
		}
		else if(res.value == 'quickfix') {
			//Just buggy behaviour of dblclick and jstree, ignore
		}
		else if(res.value == 'error') {
			adminInterface.errorShow(res.message);
		}
		else {
			adminInterface.errorShow(
				'Konnte die Serverdaten nicht verarbeiten');
		}
	};

	/**
	 * Updates the View of the Rights-Tree
	 */
	var treeUpdate = function(data) {

		var onLoaded = function(event, data) {

			tree.jstree('open_all');
			rootnode = tree.jstree(
				'select_node',
				'div.grouprights ul > li:first');
			rootnode.find('li').each(function (index, value) {

				if($(this).attr('user_has_access') === 'true') {
					$(this).children('a').addClass('modAllowed');
				}
				else {
					$(this).children('a').addClass('modNotAllowed');
				}

				if($(this).attr('module_enabled') === 'true') {
					$(this).children('a').removeClass('changeNotAllowed');
				}
				else {
					$(this).children('a').addClass('changeNotAllowed');
				}
			});
			$(window).scrollTop(tempScrollPos);
		};

		/**
		 * Create the JS-Tree
		 */
		tree.jstree({
			'json_data': {
				'data': data,
			},
			'themes': {
				'theme': 'apple',
				'icons': false
			},
			'types': {
				'valid_children': ['root'],
				'types': {
					"notChangeable" : {
						"valid_children" : [ "default" ],
						"hover_node" : false,
						"select_node" : function () {return false;}
					},
					"root" :{
						"valid_children" : [ "default" ],
						"hover_node" : false,
						"select_node" : function () {return false;}
					},
				}
			},
			'plugins' : [ 'themes', 'json_data', 'ui', 'crrm', 'types' ]

		}).bind('loaded.jstree', onLoaded);

	};

	/**
	 * Executes rightchanges
	 */
	var change = function(moduleId) {

		var grouppath = group.selectedGroupPathGet();

		var onSuccess = function(data) {

			console.log(data);
			try {
				result = JSON.parse(data);
			} catch(e) {
				adminInterface.errorShow('unbekannte Serverantwort');
				return;
			}
			if(result.value == 'success') {
				that.update();
			}
			else if(result.value == 'error') {
				adminInterface.errorShow(result.message);
			}
		};

		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|GroupSettings&action=rightChange',
			data: {
				'grouppath': grouppath,
				'moduleId': moduleId
			},

			success: onSuccess,
			error: function(data) {
				adminInterface.errorShow('Ein Fehler ist beim Verbinden mit dem Server aufgetreten');
			}
		});
	};

	$(document).ready(function() {

		/**
		 * A hierarchical Tree to display all modules and change rights of the
		 * groups
		 * @type JQuery
		 */
		tree = $('div.grouprights');

		tree.on('dblclick', 'li a', function(ev) {
			if($(this).parent().attr('module_enabled') === 'true') {
				var id = $(this).parent().attr('id')
					.substring('module_'.length);
				change(id);
			}
		});

		$('#groupChangeRights').on('click', function(ev) {

			showRights = !showRights;
			if(showRights) {
				rights.update();
			}
			else {
				$('fieldset.grouprights').hide(500);
			}
		});
	});

}
