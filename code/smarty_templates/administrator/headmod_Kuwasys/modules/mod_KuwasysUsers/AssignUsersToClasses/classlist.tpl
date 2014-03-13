{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">Kursübersicht über temporäre Zuweisungen</h2>

<table class="dataTable">
	<tr>
		<th>Kursname</th>
		<th>Anzahl Schüler</th>
		<th>Wochentag</th>
		<th>Optionen</th>
	</tr>
	{foreach $classes as $class}
	<tr>
		<td>{$class.classlabel}</td>
		<td>{$class.usercount}</td>
		<td>{$class.weekday}</td>
		<td>
			<a href="#" classId="{$class.classId}" class="displayDetails">
				Details
			</a>
		</td>
	</tr>
	{/foreach}
</table>

<script type="text/javascript">

$(document).ready(function() {
	$('a.displayDetails').on('click', function(event) {
		event.preventDefault();
		var classId = $(this).attr('classId');
		window.location.href = 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Classdetails&classId=' + classId;
	});
});

</script>

{/block}
