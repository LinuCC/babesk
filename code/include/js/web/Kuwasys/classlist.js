$(document).ready(function() {

	classlist();

	function classlist() {

		$('.btn-group button').on('click', function(ev) {
			if($(this).hasClass('to-primary')) {
				var un = unit($(this).closest('.unit-panel'));
				un.removeActivationOfCategoryClass('to-primary');
				var classCont = classContainer($(this).closest('.class-container'));
				classCont.activate();
				$(this).addClass('active');
			}
			else if($(this).hasClass('to-secondary')) {
				var un = unit($(this).closest('.unit-panel'));
				un.removeActivationOfCategoryClass('to-secondary');
				var classCont = classContainer($(this).closest('.class-container'));
				classCont.activate();
				$(this).addClass('active');
			}
			else if($(this).hasClass('to-disabled')) {
				var classCont = classContainer($(this).closest('.class-container'));
				var un = unit($(this).closest('.unit-panel'));
				classCont.removeActivation();
			}
			else {
				toastr['error']('???');
			}
		});

		$('button.submit-button').on('click', function(ev) {
			var inp = parseInput();
			if(testInput(inp)) {
				commit(inp);
			}
		});

		$('[id^="unit-accordion-body_"]').on('show.bs.collapse', function(ev) {
			if(!$(ev.target).hasClass('class-container-body')) {
				$(this).parent().find('.icon:first').removeClass('icon-plus').
						html('').addClass('icon-minus');
			}
		});
		$('[id^="unit-accordion-body_"]').on('hide.bs.collapse', function(ev) {
			if(!$(ev.target).hasClass('class-container-body')) {
				$(this).parent().find('.icon:first').removeClass('icon-minus').
					html('').addClass('icon-plus');
			}
		});

		$('[id^="class-accordion-body_"]').on('show.bs.collapse', function(ev) {
				$(this).parent().find('.icon:first').removeClass('icon-plus').
					html('').addClass('icon-minus');
		});
		$('[id^="class-accordion-body_"]').on('hide.bs.collapse', function(ev) {
				$(this).parent().find('.icon:first').removeClass('icon-minus').
					html('').addClass('icon-plus');
		});

		function classContainer($classContainer) {

			var that = this;

			that.removeActivation = function() {
				$classContainer.find('.selection-buttons .btn.active').
					removeClass('active');
				$classContainer.find('.selection-buttons button.to-disabled').
					addClass('disabled');
				$classContainer.removeClass('panel-info').addClass('panel-default');
				return that;
			};

			that.activate = function() {
				$classContainer.removeClass('panel-default').addClass('panel-info');
				$classContainer.find('.selection-buttons button.to-disabled').
					removeClass('disabled');
				$classContainer.find('.selection-buttons button.active').
					removeClass('active');
				return that;
			};

			return that;
		};

		function unit($unit) {

			var that = this;

			that.removeActivationOfCategoryClass = function(cat) {
				$cc = $unit.find('.class-container .active.' + cat)
					.closest('.class-container');
				for(var i = 0; i < $cc.length; i++) {
					ccontainer = classContainer($cc);
					ccontainer.removeActivation();
				}
			};

			return that;
		};

		function parseInput() {
			var selected = [];
			$.each($('.selection-buttons button.active'), function(ind, el) {
				var $el = $(el);
				var unitId = $el.closest('.unit-panel').attr('unitId');
				var classId = $el.closest('.class-container').attr('classId');
				var status = $el.attr('category');
				selected.push({
					'unitId': unitId,
					'classId': classId,
					'status': status
				});
			});
			return selected;
		}

		function testInput(inp) {

			var ret = true;
			if(!selectedAnything(inp)) {
				ret = false;
			}
			if(!maxOneCategoryPerClassSelected(inp)) {
				ret = false;
			}
			if(!onlyDistinctCategoriesPerUnitSelected(inp)) {
				ret = false;
			}

			return ret;

			function selectedAnything(inp) {

				if(inp.length > 0) {
					return true;
				}
				else {
					toastr['error']('Du hast nichts ausgewählt!', 'Inkorrekte Eingabe');
				}
			}

			/*
			 * Selected only one category per class?
			 * @param  {Array}  inp The input by the user
			 * @return {Bool}       True on no error, else false
			 */
			function maxOneCategoryPerClassSelected(inp) {

				var selectedClasses = [];
				for(var i = 0; i < inp.length; i++) {
					if($.inArray(inp[i]['classId'], selectedClasses) == -1) {
						selectedClasses.push(inp[i]['classId']);
					}
					else {
						toastr['error']('Du hast für eine Klasse mehrere Wahlen \
							angegeben. Bitte pro Klasse eine Auswahl treffen!',
							'Inkorrekte Eingabe'
						);
						return false;
					}
				}
				return true;
			};

			/**
			 * Selected only distinct statuses per unit? Selected not only secondary?
			 * @param  {Array}  inp The input by the user
			 * @return {Bool}       True on no error, else false
			 */
			function onlyDistinctCategoriesPerUnitSelected(inp) {

				var units = {};

				for(var i = 0; i < inp.length; i++) {
					var uId = inp[i]['unitId'];
					var status = inp[i]['status'];
					if(typeof units[uId] != 'undefined') {
						if($.inArray(status, units[uId]) == -1) {
							units[uId].push(status);
						}
						else {
							toastr['error']('Du hast für einen Tag mehrere Wahlen des ' +
								'gleichen Typs angegeben (Erstwunsch / Zweitwunsch). Pro ' +
								'Tag darf maximal ein Erst- und ein Zweitwunsch gewählt ' +
								'werden!', 'Inkorrekte Eingabe'
							);
							return false;
						}
					}
					else {
						units[uId] = [status];
					}
				}

				var ret = true;
				$.each(units, function(unitId, statuses) {
					if($.inArray('request2', statuses) != -1 &&
						$.inArray('request1', statuses) == -1
					) {
						toastr['error']('Du hast an einem Tag einen Zweitwunsch ' +
							'angegeben, ohne einen Erstwunsch anzugeben. Falls du nur ' +
							'einen Kurs an diesen Tag wählen möchtest, wähle ihn bitte ' +
							'als Erstwunsch.', 'Inkorrekte Eingabe'
						);
						ret = false;
					}
				});

				return ret;
			};
		};

		function commit(inp) {

			var selections = {};
			$.each(inp, function(ind, el) {
				if(typeof selections[el['unitId']] == 'undefined') {
					selections[el['unitId']] = {};
				}
				selections[el['unitId']][el['status']] = el['classId'];
			});
			console.log(selections);

			$.ajax({
				'type': 'post',
				'url': 'index.php?module=web|Kuwasys|ClassList|UserSelectionsApply',
				'data': {
					'choices': selections,
					'ajax': true
				},
				'success': function(data) {
					console.log(data);
					try {
						data = JSON.parse(data);
					} catch(e) {
						toastr['error'](
							'Konnte die Serverantwort nicht parsen!',
							'Verbindungsfehler'
						);
					}
					if(data.val == 'success') {
						toastr['success'](data.msg, 'Erfolgreich angemeldet!');
						$('#content').html(
							'<div class="panel panel-success"> <div class="panel-heading"> <div class="panel-title"> Erfolgreich angemeldet! </div> </div> </div> <a class="btn btn-primary pull-right" href="index.php?module=web|Kuwasys"> zum Hauptmenü </a>'
						);
					}
					else if(data.val == 'message') {
						toastr['info'](data.msg, 'Hinweis');
					}
					else if(data.val == 'error') {
						toastr['error'](data.msg, 'Fehler');
					}
					else {
						toastr['error'](
							'Konnte die Serverantwort nicht lesen!',
							'Verbindungsfehler'
						);
					}
				},
				'error': function(data) {
					console.log(data);
					toastr['error'](
						'Ein Fehler ist beim Verbinden mit dem Server aufgetreten!',
						'Verbindungsfehler'
					);
				}
			});
		};
	};
});