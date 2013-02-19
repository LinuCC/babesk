#{extends file=$inh_path} {block name="content"}

<h2 class='moduleHeader'>Details des Schülers "{$user.forename} {$user.name}"</h2>

{literal}
<style type='text/css'  media='all'>
label.left {
	color: rgb(100,100,100);
	font-weight: bold;
	float:left;
}

label.classList {
	color: rgb(50,50,50);
	font-weight: bold;
	padding-left: 5%;
}

table.dataTable {
	line-height: 180%;
	margin-left: 5%;
	width: 100%;
}
table.dataTable th {
	padding-left:10px;
	padding-right:10px;
}
table.dataTable td {
	text-align: center;
}

.contentDiv {
	width: 400px;
	margin: 0 auto;
}

.valueDiv {
	float: right;
}
</style>
{/literal}

<div class="contentDiv">
	<div><label class="left">ID:</label> <div class="valueDiv"> {$user.ID} </div></div><br>
	<div><label class="left">Vorname:</label> <div class="valueDiv"> {$user.forename} </div></div><br>
	<div><label class="left">Name:</label> <div class="valueDiv"> {$user.name} </div></div><br>
	<div><label class="left">Benutzername:</label> <div class="valueDiv"> {$user.username} </div></div><br>
	<div><label class="left">Geburtstag:</label> <div class="valueDiv"> {$user.birthday} </div></div><br>
	<div><label class="left">Email-Adresse:</label> <div class="valueDiv"> {$user.email} </div></div><br>
	<div><label class="left">Telefon:</label> <div class="valueDiv"> {$user.telephone} </div></div><br>
	<div><label class="left">Letzter Login:</label> <div class="valueDiv">
		{if $user.last_login}{$user.last_login}{else}---{/if}
	</div></div><br>
	{if isset($user.gradeLabel)}
		<div><label class="left">In Klasse:</label> <div class="valueDiv"> {$user.gradeValue} {$user.gradeLabel} </div></div>
	{else}
		<div><label class="left">In Klasse:</label><div class="valueDiv"> <b>keine Klasse</b></div></div>
	{/if}<br>
	{if isset($user.classes) and is_array($user.classes)}
	<div><label class="left">Kurse:</label><br>
		{foreach $user.classes as $unit}
		<label class="classList">{$unit.0.unit.translatedName}:</label>
				<table class="dataTable">
					<tr>
						<th>Kursname</th>
						<th>Status</th>
						<th>Wochentag</th>
					</tr>
					{foreach $unit as $class}
						<tr>
							<td><a href="index.php?section=Kuwasys|Classes&action=showClassDetails&ID={$class.ID}">{$class.label}</a></td>
							<td><a href="index.php?section=Kuwasys|Users&action=changeUserToClass&classId={$class.ID}&userId={$user.ID}&classStatus={if $class.status}{$class.status}{else}Fehler!{/if}">{if $class.status}{$class.status.translatedName}{else}Fehler!{/if}</a></td>
							<td>{$class.unit.translatedName}</td>
						</tr>
					{/foreach}
				</table>
		{/foreach}
	</div>
	{else}
	<div><label class="left">Kurse:</label> <div class="valueDiv"><b>kein Kurs</b></div></div><br>
	{/if}
</div>

{/block}