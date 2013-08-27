{extends file=$inh_path}{block name="content"}

<style type="text/css">

table a {
	display: block;
}

</style>

<h2 class="moduleHeader">
	{_g('Classdetails')}
</h2>

<table id="activeUsers" class="dataTable">
	<tr><th colspan="4">Aktive Schüler</th></tr>
	<tr>
		<th>Name</th>
		<th>Klasse</th>
		<th>Wahlstatus</th>
		<th>Optionen</th>
	</tr>
</table>
<br /><br />
<table id="waitingUsers" class="dataTable">
	<tr><th colspan="4">Wartende Schüler</th></tr>
	<tr>
		<th>Name</th>
		<th>Klasse</th>
		<th>Wahlstatus</th>
		<th>Optionen</th>
	</tr>
</table>
<br /><br />
<table id="removedUsers" class="dataTable">
	<tr><th colspan="4">Entfernte Schüler</th></tr>
	<tr>
		<th>Name</th>
		<th>Klasse</th>
		<th>Wahlstatus</th>
		<th>Optionen</th>
	</tr>
</table>

<script type="text/javascript">
	var classId = {$classId}
</script>

<script src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/classdetails.js">
</script>

{/block}
