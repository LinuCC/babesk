{extends file=$inh_path}


{block name=popup_dialogs}

<div id="print-dialog" class="modal fade" tabindex="-1" role="dialog"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">
					&times;
				</button>
				<h4 class="modal-title">
					Teilnahmebestätigungen
				</h4>
			</div>
			<div class="modal-body">
				<p>
					Bitte wählen sie die Klasse für die sie die Teilnahmebestätigungen erstellen wollen
				</p>
				<span class="input-group form-group">
					<span class="input-group-addon">Klasse</span>
					<select name="grade" class="form-control">
						{foreach $grades as $id => $name}
						<option value="{$id}">
							{$name}
						</option>
						{/foreach}
					</select>
				</span>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal">
					Abbrechen
				</button>
				<button id="print-submit" class="btn btn-primary">
					Erstellen
				</button>
			</div>
		</div>
	</div>
</div>

{/block}


{block name="content"}

<h2 class="module-header">
	{t}Kuwasys User-Mainmenu{/t}
</h2>

<fieldset class="smallContainer">
	<legend>
		{t}Printables{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a id="printParticipationConfirmation" class="submodulelink"
				data-toggle="modal" data-target="#print-dialog" href="#"
				title="{t}Here you can Print PDFs confirming the Participation of Users at their Classes of this Year{/t}">
				{t}Create Participation Confirmation{/t}
			</a>
		</li>
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>
		{t}Bulk-Changes{/t}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a class="submodulelink" href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses"
			title="{t}Here you can assign the Users that submitted requests to Classes of the active Year{/t}">
				{t}Assign the Users to the Classes{/t}
			</a>
		</li>
	</ul>
</fieldset>

{/block}


{block name=js_include append}

<script type="text/javascript">

$(document).ready(function() {

	$('#print-submit').on('click', function(ev) {
		var id = $('select[name=grade] option:selected').val();
		window.location.href = 'index.php?module=administrator|Kuwasys|KuwasysUsers|PrintParticipationConfirmation&gradeId=' + id;
	});
});
</script>

{/block}
