{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">
	{_g('Kuwasys User-Mainmenu')}
</h2>

<fieldset class="smallContainer">
	<legend>
		{_g('Printables')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a id="printParticipationConfirmation" class="submodulelink"
			href="#"
			title="{_g('Here you can Print PDFs confirming the Participation of Users at their Classes of this Year')}">
				{_g('Print Participation Confirmation')}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{_g('Bulk-Changes')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a class="submodulelink" href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses"
			title="{_g('Here you can assign the Users that submitted requests to Classes of the active Year')}">
				{_g('Assign the Users to the Classes')}
			</a>
		</li>
	</ul>
</fieldset>

<div class="dialog" id="printDialog" title="Teilnahmebestätigungen">
	<p>Bitte wählen sie die Klasse für die sie die Teilnahmebestätigungen ausdrucken wollen</p>
	<form>
		<fieldset>
		<label for="grade">Klasse</label>
			<select name="grade">
				{foreach $grades as $id => $name}
				<option value="{$id}">
					{$name}
				</option>
				{/foreach}
			</select>
		</fieldset>
	</form>
</div>

<script type="text/javascript">

$(document).ready(function() {

	$('#printParticipationConfirmation').on('click', function(event) {
		event.preventDefault();
		$('div#printDialog').dialog('open');
	});

	$('div#printDialog').dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,

		buttons: {

			"Drucken": function() {
				var id = $('select[name=grade] option:selected').val();
				window.location.href = 'index.php?module=administrator|Kuwasys|KuwasysUsers|PrintParticipationConfirmation&gradeId=' + id;
				$(this).dialog("close");
			},

			"Abbrechen": function() {
				$(this).dialog("close");
			}

		}
	});
});
</script>

{/block}
