{extends file=$inh_path}{block name="content"}

<style type="text/css">

table a {
	display: block;
}

.ui-dialog label, .ui-dialog input, .ui-dialog select {
	display: block;
}

.ui-dialog label {
	font-size: 80%;
}

#addUserToClass {
	float:right;
}

</style>

<h2 class="moduleHeader">
	{_g('Classdetails of Class')}<br />
	{$class.label}
</h2>
<br /><br />
<a href="#" id="addUserToClass">Einen Benutzer hinzufügen...</a>

<h4>Aktive Schüler</h4>
<table id="activeUsers" class="dataTable">
</table>

<br />
<h4>Wartende Schüler</h4>
<table id="waitingUsers" class="dataTable">
</table>

<br />
<h4>Entfernte Schüler</h4>
<table id="removedUsers" class="dataTable">
</table>


<div class="dialog" id="addUserDialog" title="Benutzer hinzufügen">
	<p>Bitte suchen und wählen sie den Benutzer und den Status</p>
	<form>
		<fieldset>
		<label for="username">{_g('Username')}</label>
		<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" />
		<label for="status">{_g('Status')}</label>
			<select name="status">
				<option value="active" >
					Aktiv
				</option>
				<option value="waiting" >
					Wartend
				</option>
				<option value="removed" >
					Nicht in diesem Kurse
				</option>
			</select>
		</fieldset>
	</form>
</div>


<div class="dialog" id="moveStatusDialog" title="Status verändern">
	<p>Bitte wählen sie den neuen Status</p>
	<form>
		<fieldset>
		<label for="status">{_g('Status')}</label>
			<select name="status">
				<option value="active" >
					Aktiv
				</option>
				<option value="waiting" >
					Wartend
				</option>
				<option value="removed" >
					Nicht in diesem Kurse
				</option>
			</select>
		</fieldset>
	</form>
</div>


<div class="dialog" id="moveClassDialog" title="Kurs verändern">
	<p>Bitte wählen sie den neuen Kurs</p>
	<form>
		<fieldset>
		<label for="class">{_g('Class')}</label>
			<select name="class">
				{foreach $classes as $class}
				<option value="{$class.ID}">
					{$class.label}
				</option>
				{/foreach}
			</select>
		</fieldset>
	</form>
</div>


<script type="text/javascript">
	var classId = {$classId}
</script>

<script src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/classdetails.js">
</script>

{/block}
