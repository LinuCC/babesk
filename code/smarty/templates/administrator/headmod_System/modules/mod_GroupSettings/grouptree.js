var group = new function() {

	var jstree = undefined;
	var tree = undefined;
	var that = this;

	/**
	 * Locks / unlocks the Group-Rdit-Buttons
	 */
	that.groupButtonsLock = function(lock) {

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

	/**
	 * Bind the Eventhandlers when Doc finished loading
	 */
	$(document).ready(function() {

		/**
		 * A hierarchical Tree to select and Edit the Groups
		 * @type JQuery
		 */
		tree = $('div.groupTree');

		/**
		 * Creates a new child-node at the selected tree-node
		 */
		$('#groupAdd').on('click', function(ev) {
			tree.jstree(
				'create',
				$('div.groupTree .jstree-clicked'),
				'last',
				{data: 'Neue Gruppe'}
			);
		});

		/**
		 * Removes the selected Element from the Tree
		 */
		$('#groupRemove').on('click', function(ev) {
			tree.jstree('remove');
		});

		/**
		 * Renames the selected Element in the Tree
		 */
		$('#groupRename').on('click', function(ev) {
			tree.jstree('rename');
		});

		/**
		 * Renames the selected Element in the Tree
		 */
		tree.on('dblclick', 'li a', function(ev) {
			tree.jstree('rename');
		});

		/**
		 * Handles the locking and unlocking of Group-Edit-Buttons
		 */
		tree.on('click', 'li a', function(ev) {
			//wait for jstree to change the class of the object
			setTimeout(function() {
				if($('div.groupTree .jstree-clicked').length) {
					that.groupButtonsLock(false);
					rights.update();
				}
				else {
					that.groupButtonsLock(true);
				}
			}, 50);
		});
	});

	/**
	 * Returns the Path of the Selected Group
	 * @return {String} Path of the Selected Group
	 */
	that.selectedGroupPathGet = function() {

		var selected = tree.jstree('get_selected');
		var path = tree.jstree(
			'get_path', selected).join('/');

		return path;
	}

	/**
	 * Fetches the Data needed for displaying the Grouptree
	 */
	that.fetch = function() {
		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|GroupSettings&action=groupsFetch',
			data: {},

			success: function(data) {
				parseData(data);
			},

			error: function(error) {
				adminInterface.errorShow('Ein Fehler ist beim verbinden mit' +
					'dem Server aufgetreten!');
			}
		});
	};

	/**
	 * Parses the Data fetched from the Server
	 */
	var parseData = function(data) {
		try {
			var res = $.parseJSON(data);
		} catch(e) {
			adminInterface.errorShow('Error parsing the server' +
				'response');
			return;
		}
		if(res) {
			if(res.value == 'success') {
				update(res.data);
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
	};

	/**
	 * Updates the Grouptree
	 */
	var update = function(grouptree) {

		/**
		 * gets called when User wants something changed
		 *
		 * Gets Executed before the Event itself (for example before
		 * "remove Node" - Event)
		 */
		var before = function(event, data) {

			//Confirm before removing a node
			if(data.func == "remove") {
				if(confirm(
					'Wollen sie die Gruppe wirklich löschen?')) {
					//User confirmed, let jstree remove that thing
				}
				else {
					// User declined, stop deletion process!
					event.stopImmediatePropagation();
					return false;
				}
			}
		};

		/**
		 * Gets Executed when Tree has finished loading
		 */
		var onLoaded = function(event, data) {

			tree.jstree('open_all');
			$('div.groupTree ul > li:first').attr('rel', 'root');
		};

		/**
		 * Gets Executed when user wants an additional Node to be created
		 */
		var onCreate = function(event, data) {

			parentPath = tree.jstree('get_path',
				data.rslt.parent).join('/');
			change({
				'action': 'add',
				'parentPath': parentPath,
				'name': data.rslt.name
			});
		};

		/**
		 * Gets executed when user wants a Node to be renamed
		 */
		var onRename = function(event, data) {

			parentPath = tree.jstree('get_path', data.rslt.obj)
				.slice(0,-1).join('/');
			change({
				'action': 'rename',
				'parentPath': parentPath,
				'oldName': data.rslt.old_name,
				'newName': data.rslt.new_name
			});
		}

		/**
		 * Gets executed when user wants a node to be removed
		 */
		var onRemove = function(event, data) {

			toDelete = data.rslt.obj.children('a').text();
			parentPath = tree.jstree('get_path',
				data.rslt.parent).join('/');
			change({
				'action': 'delete',
				'parentPath': parentPath,
				'name': toDelete.trim()
			});
		};

		tree.jstree({
			'json_data': {
				'data': grouptree,
			},
			'themes': {
				'theme': 'apple',
				'icons': false
			},
			"types" : {
				"valid_children" : [ "root" ],
				"types" : {
					"root" : {
						"valid_children" : [ "default" ],
						"hover_node" : false,
						"select_node" : function () {return false;}
					}
				}
			},
			'plugins' : [ 'themes', 'json_data', 'ui', 'crrm', 'types' ]

		}).bind("before.jstree", before)
			.bind("loaded.jstree", onLoaded)
			.bind("create.jstree", onCreate)
			.bind("rename.jstree", onRename)
			.bind("remove.jstree", onRemove);
	};

	/**
	 * Changes the Data based on the data given
	 */
	var change = function(data) {

		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|GroupSettings&action=groupsChange',
			data: {
				'data': data
			},
			success: function(data) {
				onSuccess(data);
			},
			error: function(data) {
				adminInterface.errorShow('Could not parse Servermessage');
				return false;
			}
		});

		/**
		 * When Ajax-Call succeeded
		 */
		var onSuccess = function(data) {

			console.log(data);
			try{
				var res = $.parseJSON(data);
			} catch(e) {
				adminInterface.errorShow('Could not parse Servermessage');
				that.fetch();
				return false;
			}
			if(res.value == 'success') {
				return true;
			}
			else {
				adminInterface.errorShow('Error:' + res.message);
				adminInterface.errorShow('Konnte die Gruppe nicht ' +
					'verändern');
				that.fetch();
				return false;
			}
		};
	};
};
