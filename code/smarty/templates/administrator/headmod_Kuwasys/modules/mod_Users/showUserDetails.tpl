{extends file=$inh_path} {block name="content"}

<h2 class='moduleHeader'>Details des Schülers "{$user.forename} {$user.name}"</h2>

{literal}
<style type='text/css'  media='all'>
label {
	color: rgb(100,100,100);
	font-weight: bold;
	float:left;
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
	<div><label>ID:</label> <div class="valueDiv"> {$user.ID} </div></div><br>
	<div><label>Vorname:</label> <div class="valueDiv"> {$user.forename} </div></div><br>
	<div><label>Name:</label> <div class="valueDiv"> {$user.name} </div></div><br>
	<div><label>Benutzername:</label> <div class="valueDiv"> {$user.username} </div></div><br>
	<div><label>Geburtstag:</label> <div class="valueDiv"> {$user.birthday} </div></div><br>
	<div><label>Email-Adresse:</label> <div class="valueDiv"> {$user.email} </div></div><br>
	<div><label>Telefon:</label> <div class="valueDiv"> {$user.telephone} </div></div><br>
	<div><label>Letzter Login:</label> <div class="valueDiv"> 
		{if $user.last_login}{$user.last_login}{else}---{/if} 
	</div></div><br>
	{if isset($user.classes)}
	<div><label>Kurse:</label>
		{foreach $user.classes as $class}
			<a class="valueDiv"
			{if $class.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $class.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			 href="index.php?section=Kuwasys|Classes&action=changeLinkUserToClass&classId={$class.ID}&userId={$user.ID}">{$class.label} -- {$class.status}</a><br>
		{/foreach}
	</div>
	{else}
	<div><label>Kurse:</label> <div class="valueDiv"><b>kein Kurs</b></div></div><br>
	{/if}
	{if isset($user.gradeLabel)}
		<div><label>In Klasse:</label> <div class="valueDiv"> {$user.gradeValue} {$user.gradeLabel} </div></div>
	{else}
		<div><label>In Klasse:</label><div class="valueDiv"> <b>keine Klasse</b></div></div>
	{/if}<br>
</div>

{/block}