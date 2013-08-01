
$(document).ready(function() {

	adminInterface.warningShow('Hier verändern sie grundlegende Eigenschaften des Systems! Bitte tun sie dies nur, wenn sie genau wissen, was sie tun!');
});

/**
 * Represents the Moduledetails-Part of the View, fetches and changes data
 */
var details = new function() {

	var that = this;
	var container = $('div.moduledetails');

	/**
	 * Clears the Details-Container
	 */
	that.clear = function() {
		container.html('Wählen sie ein Module aus');
	}

	/**
	 * Adds an onclick-Handler for the Change-Module-Button
	 */
	var changeHandlerAdd = function() {

		$('button#changeModuleSubmit').on('click', function() {

			var module = {
				'id': moduletree.selectedModuleIdGet(),
				'name': $('input[name="moduleName"]').val(),
				'isEnabled': $('input[name="isEnabled"]').prop('checked'),
				'displayInMenu': $('input[name="displayInMenu"]')
					.prop('checked'),
				'executablePath': $('input[name="execPath"]').val()
			};

			change(module);
		});
	};

	/**
	 * Updates the Details-Field with Data from the Server
	 *
	 * @param  {integer} moduleId The ID of the Moduledata to display
	 */
	that.update = function(moduleId) {

		/**
		 * Displays a fatal Error
		 */
		var fatalError = function() {
			adminInterface.errorShow('Konnte die Daten des Modules nicht' +
				'abrufen');
		}

		/**
		 * Parses the JSON-Data fetched from the Server
		 *
		 * @param  {String} data The JSON-Data from the Server
		 * @return {Array} the Parsed data, or false when Error occured
		 */
		var dataParse = function(data) {

			try {
				res = JSON.parse(data);

			} catch(e) {
				adminInterface.errorShow('Unbekannte Serverantwort');
				return false;
			}

			return res;
		};

		/**
		 * Displays the Error the Server returned
		 *
		 * @param  {Array} res The parsed JSON-String from the Server
		 */
		var errorShow = function(res) {

			if(typeof res.message) {
				adminInterface.errorShow(res.message);
			}
			else {
				adminInterface.errorShow('Ein Fehler ist beim abrufen der' +
					'Fehlermeldung des Servers entstanden.');
			}
		};

		/**
		 * Fills the Div with the res-Data
		 *
		 * @param  {Object} res The Data to be filled in
		 * @return {boolean} True if everything went okay, False if not
		 */
		var detailsHtmlFill = function(res) {

			/**
			 * Creates the HTML for the Name-Inputfield
			 *
			 * @param  {Object} res The Data
			 * @return {String} The Html-Code
			 */
			var nameHtml = function(res) {

				var name = '<div class="simpleForm">' +
					'<p class="inputItem">Name:</p>' +
					'<input type="text" class="inputItem"' +
					'name="moduleName" value="{0}"/></div>';

				return name.format(res.data.name);
			}

			/**
			 * Creates the HTML for the ExecutablePath-Inputfield
			 *
			 * @param  {Object} res The Data
			 * @return {String} The Html-Code
			 */
			var executablePathHtml = function(res) {

				var execPath = '<div class="simpleForm"><p class="inputItem">' +
					'Ausführungspfad:</p>' +
					'<input class="inputItem" type="text" name="execPath"' +
					'value="{0}" /></div>';

				return execPath.format(res.data.executablePath);
			}

			/**
			 * Creates the HTML for the displayInMenu-Checkbox
			 *
			 * @param  {Object} res The Data
			 * @return {String} The Html-Code
			 */
			var displayInMenuHtml = function(res) {

				var displayInMenu = '<div class="simpleForm">' +
						'<p class="inputItem">Wird im Menü angezeigt:</p>' +
						'<input class="inputItem" type="checkbox" '+
						'name="displayInMenu" {0} /></div>';
				if(res.data.displayInMenu != 0) {
					var displayInMenuChecked = 'checked="checked"';
				}
				else {
					var displayInMenuChecked = '';
				}

				return displayInMenu.format(displayInMenuChecked);
			}

			/**
			 * Creates the HTML for the isEnabled-Checkbox
			 *
			 * @param  {Object} res The Data
			 * @return {String} The Html-Code
			 */
			var isEnabledHtml = function(res) {

				var isEnabled = '<div class="simpleForm">' +
						'<p class="inputItem">Ist aktiviert:</p>' +
						'<input class="inputItem" type="checkbox" ' +
						'name="isEnabled" {0} title="Gibt an, ob das Modul installiert ist und von verschiedenen Teilen des Programms verwendet werden kann." /></div>';
				if(res.data.enabled != 0) {
					var enabledChecked = 'checked="checked"';
				}
				else {
					var enabledChecked = '';
				}

				return isEnabled.format(enabledChecked);
			}

			/**
			 * Checks if the Data can be used for filling the details-Div
			 *
			 * @param  {Object} res The Data
			 * @return {boolean} True if Data is correct, else false
			 */
			var resultCheck = function(res) {

				return typeof res.data.name != 'undefined' &&
					typeof res.data.executablePath != 'undefined';
			}

			//Html-Code for the Change-Submit-button
			var change = '<button id="changeModuleSubmit">ändern</button>';

			if(resultCheck(res)) {
				container.html('');
				container.append(nameHtml(res));
				container.append(executablePathHtml(res));
				container.append(isEnabledHtml(res));
				container.append(displayInMenuHtml(res));
				container.append(change);
				changeHandlerAdd();
			}
			else {
				adminInterface.errorShow('unbekannte Serverantwort!');
				return false;
			}

			return true;
		};

		/**
		 * Handles the Result from the Server
		 * @param  {Object} res The Result from the Server
		 * @return {boolean} True if Result was not an Error, else false
		 */
		var resultHandle = function(res) {

			if(typeof res.value != 'undefined') {
				switch(res.value) {
					case 'success':
						detailsHtmlFill(res);
						break;
					case 'error':
						errorShow(res);
						break;
					default:
						adminInterface.errorShow('Unbekannte Serverantwort');
						return false;
				}
			}
			else {
				adminInterface.errorShow('Unbekannte Serverantwort');
				return false;
			}

			return true;
		};

		/**
		 * Gets called when Server responded
		 *
		 * @param  {String} data The data from the Server
		 */
		var onSuccess = function(data) {

			if(res = dataParse(data)) {
				if(resultHandle(res)) {
					//All fine, Nothing to do here
				}
				else {
					fatalError();
					return;
				}
			}
		};

		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|ModuleSettings&action=moduleGet',
			data: {
				'moduleId': moduleId
			},
			success: onSuccess,
			error: fatalError
		});
	};

	/**
	 * Changes the data of a Module in the Database
	 *
	 * @param  {Object} module The moduledata to change
	 */
	var change = function(module) {

		/**
		 * Displays a fatal Error to the User
		 */
		var fatalError = function() {
			adminInterface.errorShow(
				'Konnte die Daten nicht verändern');
		};

		/**
		 * Checks if the given Moduledata is correct
		 *
		 * @param  {Object} module The Moduledata
		 * @return {boolean} True if the data is correct, else false
		 */
		var moduledataToChangeCheck = function(module) {

			return typeof module.id != 'undefined' &&
				typeof module.name != 'undefined' &&
				typeof module.executablePath != 'undefined';
		};

		/**
		 * Commits the Change to the Server
		 *
		 * @param  {Object} module The Moduledata to change
		 */
		var changeToDb = function(module) {

			/**
			 * Parses the Server-JSON-String to an Object
			 *
			 * @param  {String} data The Serverresponse
			 * @return {Object} The parsed object, on error false
			 */
			var dataParse = function(data) {

				try {
					res = JSON.parse(data);

				} catch(e) {
					adminInterface.errorShow('unbekannte Serverantwort;' +
						'konnte JSON nicht parsen');
					return false;
				}

				return res;
			};

			/**
			 * Handles the parsed Result of the Server
			 *
			 * @param  {Object} res The parsed Serverresponse
			 * @return {boolean} True if Change successfull, else false
			 */
			var resultHandle = function(res) {

				if(typeof res.value != 'undefined') {

					switch(res.value) {
						case 'success':
							adminInterface.successShow(
									'Das Modul wurde erfolgreich verändert');
							moduletree.fetch();
							container.html('');
							return true;
							break;
						case 'error':
							adminInterface.errorShow('Fehler: ' +
								res.message);
							return false;
							break;
						default:
							adminInterface.errorShow('Unbekannte ' +
								'Serverantwort; Value ist falsch definiert');
							return false;
							break;
					}
				}
				else {
					adminInterface.errorShow('unbekannte Serverantwort;' +
						' Value ist nicht definiert');
					return false;
				}
			};

			/**
			 * Handles the Result fetched from the Server
			 *
			 * @param  {String} data The Serverresponse
			 */
			var onSuccess = function(data) {
				console.log(data);
				if(res = dataParse(data)) {
					if(resultHandle(res)) {
						//Nothing to do here, yay
					}
					else {
						fatalError();
					}
				}
				else {
					fatalError();
				}
			};

			$.ajax({
				type: 'POST',
				url: 'index.php?section=System|ModuleSettings&' +
					'action=moduleChange',
				data: {
					'id': module.id,
					'name': module.name,
					'isEnabled': module.isEnabled,
					'displayInMenu': module.displayInMenu,
					'executablePath': module.executablePath
				},
				success: onSuccess,
				error: fatalError
			});
		}

		if(moduledataToChangeCheck(module)) {
			changeToDb(module);
		}
		else {
			adminInterface.errorShow(
				'Die zu verändernden Daten sind inkorrekt');
		}
	};
};

/**
 * Represents a Treeview of the Modules
 *
 * The treeview has a Contextmenu for Adding and deleting modules,
 * if a Node is clicked the details get shown by calling the details-Object.
 */
var moduletree = new function() {

	var that = this;
	//The treeObject
	var tree = 'undefined';

	/**
	 * Initialize the Treeobject when Document finished loading
	 */
	$(document).ready(function() {
		tree = $('div.moduletree');
	});

	/**
	 * Returns the ModuleID of the selected Module
	 *
	 * @return {String} The ID of the Module
	 */
	that.selectedModuleIdGet = function() {

		var id = $(tree.jstree('get_selected')).data('id');
		return id;
	};

	/**
	 * Fetches all of the Modules
	 */
	that.fetch = function() {

		/**
		 * Displays a fatal Error
		 */
		var fatalError = function() {
			adminInterface.errorShow('Konnte die Module nicht abrufen!');
		};

		/**
		 * Displays the Error the Server responded
		 *
		 * @param  {Object} res The parsed Serverresponse
		 */
		var serverErrorShow = function(res) {
			if(typeof res.message != 'undefined') {
				adminInterface.errorShow(res.message);
			}
			else {
				adminInterface.errorShow('Could not display Servererror!');
			}
		};

		/**
		 * Parses the Serverresponse
		 *
		 * @param  {String} data The Serverresponse
		 * @return {Object}      The parsed data, on Error false
		 */
		var jsonParse = function(data) {
			try {
				var res = JSON.parse(data);

			} catch(e) {
				var res = false;
				adminInterface.errorShow('Error parsing the Serverresponse');
			}
			return res;
		};

		/**
		 * Handles the parsed Serverresult
		 *
		 * @param  {object} res The parsed Serverresponse
		 * @return {Object}     The Modules on success, else false
		 */
		var resultHandle = function(res) {

			if(typeof res.value != 'undefined') {
				modules = res.data;
				switch(res.value) {
					case 'success':
						modules = resultAlterForJstree(modules);
						return modules;
						break;
					case 'error':
						serverErrorShow(res);
						fatalError();
						break;
					default:
						adminInterface.errorShow('Unknown Serverresponse!');
						return false;
						break;
				}
			}
			else {
				adminInterface.errorShow('Unknown Serverresponse!');
				return false;
			}
		};

		/**
		 * Alters the Result of the Server for the Jstree to display
		 *
		 * @param  {Object} module The Modules to alter
		 * @return {Object}        The altered Modules
		 */
		var resultAlterForJstree = function(module) {

			if(typeof module['childs'] != 'undefined' &&
				module['childs'].length > 0) {

				for(var index = 0; index < module['childs'].length; index += 1) {
					module['childs'][index] = resultAlterForJstree(
						module['childs'][index]);
				}
			}
			module = resultArraykeysChange(module);
			return module;
		};

		/**
		 * Changes the Arraykeys of the Module for Jstree
		 *
		 * @param  {Object} module The Module
		 * @return {Object}        The altered Module
		 */
		var resultArraykeysChange = function(module) {

			module = resultArraykeysAdd(module);
			module = resultArraykeysRemoveOld(module);
			return module;
		}

		/**
		 * Adds Arraykeys needed by Jstree to the Module
		 *
		 * @param  {Object} module The Module
		 * @return {Object}        The altered Module
		 */
		var resultArraykeysAdd = function(module) {

			module['children'] = module['childs'];
			module['data'] = module['name'];
			module['metadata'] = {"id": module['id']};
			return module;
		}

		/**
		 * Removes now unneeded Arraykeys of the Module
		 *
		 * @param  {Object} module The Module to alter
		 * @return {Object}        The altered Module
		 */
		var resultArraykeysRemoveOld = function(module) {

			delete module['childs'];
			delete module['name'];
			delete module['id'];
			return module;
		}

		/**
		 * Handles the Serverresponse
		 *
		 * @param  {String} data The Serverresponse
		 */
		var dataParse = function(data) {

			if(res = jsonParse(data)) {
				if(modules = resultHandle(res)) {
					update(modules);
				}
			}
			else {
				fatalError();
				return;
			}
		};

		$.ajax({
			type: 'POST',
			url: 'index.php?section=System|ModuleSettings&action=modulesFetch',
			data: {},
			success: dataParse,
			error: fatalError
		});
	};

	/**
	 * Updates the Module-Jstree
	 *
	 * @param  {Object} moduletree The Modules
	 */
	var update = function(moduletree) {

		/**
		 * When Node gets selected, changes the moduledetails
		 */
		var onSelect = function(event, data) {
			var moduleId = $(data.rslt.obj).data('id');
			details.update(moduleId);
		};

		function beforeRemove(event, data) {

			if(confirm('Wollen sie das Modul wirklich löschen?')) {
				//Confirmed, move on
			}
			else {
				event.stopImmediatePropagation();
				return false;
			}
		};

		function onRemove(event, data) {

			var removalSucceeded = false;
			var moduleId = $(data.rslt.obj).data('id');
			removeModule();

			if(removalSucceeded) {
				reload();
				return false;
			}
			else {
				return true;
			}

			function removeModule() {

				$.ajax({
					type: 'POST',
					url: 'index.php?section=System|ModuleSettings' +
						'&action=moduleRemove',
					data: {
						'moduleId': moduleId
					},
					success: onSuccess,
					error: fatalError
				});

				function fatalError() {
					adminInterface.errorShow(
						'Konnte das Modul nicht löschen!');
				}

				function onSuccess(data) {
					console.log(data);
					if(res = responseStringParse(data)) {
						if(resultHandle(res)) {
							adminInterface.successShow(
									'Modul erfolgreich gelöscht');
						}
					}

					function responseStringParse(data) {

						try {
							res = JSON.parse(data);

						} catch(e) {
							adminInterface.errorShow(
								'Konnte die Serverantwort nicht parsen!');
							return false;
						}
						return res;
					}

					function resultHandle(res) {

						switch(res.value) {
							case 'success':
							removalSucceeded = true;
								return true;
								break;
							case 'error':
								adminInterface.errorShow(res.message);
								return false;
								break;
							default:
								adminInterface.errorShow(
									'Unbekannte Serverantwort!');
								return false;
								break;
						}
					}
				}
			};
		};

		/**
		 * Creates a new Module
		 */
		var onCreate = function(event, newNodeData) {

			var creationSucceeded = false;

			/**
			 * Returns the Parents of the Module as a Path
			 * @return {String} The Parentpath
			 */
			var parentPathGet = function() {
				return tree.jstree('get_path', newNodeData.rslt.parent).join('/');
			};

			/**
			 * Creates a new Module in the Database
			 *
			 * @param  {String} name       The Name of the String
			 * @param  {String} parentPath The Parents as a Path
			 */
			var createModule = function(name, parentPath) {

				/**
				 * Displays a fatal Error
				 */
				var fatalError = function() {

					adminInterface.errorShow('Das Modul konnte nicht hinzugefügt werden! ');
				};

				/**
				 * Parses the Serverresponse
				 *
				 * @param  {String} data The Serverresponse
				 * @return {Object}      The parsed Response
				 */
				var dataParse = function(data) {

					try {
						res = JSON.parse(data);

					} catch(e) {
						adminInterface.errorShow('Konnte die Serverantwort ' +
							'nicht parsen');
						return false;
					}

					return res;
				};

				/**
				 * Continues based on the Serverresponse
				 *
				 * @param  {Object} res The parsed Serverresponse
				 * @return {boolean}    True on Success, false on Error
				 */
				var resultHandle = function(res) {

					if(typeof res.value != 'undefined') {
						switch(res.value) {
							case 'success':
								metadataModuleIdSet();
								return true;
								break;
							case 'error':
								adminInterface.errorShow(res.message);
								return false;
								break;
							default:
								adminInterface.errorShow('Unbekannte ' +
									'Serverantwort; Value hat einen ' +
									'unbekannten Wert');
								return false;
								break;
						}
					}
					else {
						adminInterface.errorShow('Unbekannte Serverantwort; ' +
							'Value ist nicht definiert');
					}
				};

				/**
				 * Sets the Metadata-ID of the newly created Module-Node
				 */
				function metadataModuleIdSet() {

					newNodeData.rslt.obj.data('id', res.data.moduleId);
				}

				/**
				 * Gives User Feedback if Module was added
				 *
				 * @param  {String} data The Serverresponse
				 */
				function onSuccess(data) {

					console.log(data);

					if(res = dataParse(data)) {
						if(resultHandle(res)) {
							adminInterface.successShow('Das Modul wurde ' +
								'erfolgreich hinzugefügt');
							creationSucceeded = true;
						}
						else {
							fatalError();
						}
					}
					else {
						fatalError();
					}
				};

				$.ajax({
					type: 'POST',
					async: false,
					url: 'index.php?section=System|ModuleSettings' +
						'&action=moduleAdd',
					data: {
						'name': name,
						'parentPath': parentPath
					},
					success: onSuccess,
					error: fatalError
				});
			}

			var name = newNodeData.rslt.name;
			var parentPath = parentPathGet();

			createModule(name, parentPath);

			/*
			 * If Server had Error creating the new node, reload the Tree
			 * so that the correct Nodes are displayed. Jstree is kinda silly
			 * when it comes to stop creating a node
			 */
			if(!creationSucceeded) {
				reload();
			}
			else {
				return true;
			}
		};

		var jstreeMenu = function(node) {

			var items = {

				createItem: {
					label: 'Untermodul erstellen',
					action: function(node) {
						return {createItem: this.create(node)};
					}
				},
				deleteItem: {
					label: "Modul Löschen",
					action: function(node) {
						return {deleteItem: this.remove(node) };
					}
				}
			};

			return items;
		};

		tree.jstree({
			'json_data': {
				'data': moduletree
			},
			'themes': {
				'theme': 'apple',
				'icons': false
			},
			'plugins' : [
				'themes',
				'json_data',
				'ui',
				'crrm',
				'types',
				'contextmenu'
			],
			'contextmenu': {
				items: jstreeMenu
			}
		}).bind("select_node.jstree", onSelect)
		.bind("loaded.jstree", function() {
			tree.jstree('open_all');
		}).bind("create.jstree", onCreate
		).bind("remove.jstree", onRemove
		).bind("before.jstree", function(event, data) {
			if(data.func == "remove") {
				return beforeRemove(event, data);
			}
		});
	};
};

function reload() {
	moduletree.fetch();
	details.clear();
}

$(document).ready(function() {


	moduletree.fetch();
});
