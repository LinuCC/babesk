{include file='web/header.tpl' title='Hauptmenü des Kurswahlsystems'}

<script type="text/javascript" src="../smarty/templates/web/headmod_Kuwasys/classDescriptionSwitch.js">
</script>

<style type='text/css'  media='all'>


a.classListing {
	float:right;
}
p.classListing {
	float:right;
}

div.classListing {
	
	border-style: solid;
	border-width: 1px;
	border-color: #2e6132;
	-webkit-border-radius: 20px;
  -khtml-border-radius: 20px;
  -moz-border-radius: 20px;
	border-radius: 20px;
	margin: 0 auto;
	padding: 15px;
	width: 650px;
}

</style>

<h2>Hauptmenü des Kurswahlsystems</h2><br>
Im anderen Modul Verhindern das eine Parameteränderung von ID zu Problemen führen kann!
<div class="classListing">
<h4 >Übersicht der Kurse</h4>
{foreach $classes as $class}
	<form style="float: right" action="">
		<input id="showClassDescriptionOn#{$class.ID}" type="button" value="Beschreibung anzeigen" onclick="displayClassDescription('{$class.ID}')">
		<input id="showClassDescriptionOff#{$class.ID}" type="button" value="Beschreibung verstecken" onclick="hideClassDescription('{$class.ID}')" hidden="hidden">
	</form><pre style="float: right">   </pre>
	<b>
	{if $class.registrationEnabled}
		<a class="classListing"
			{if $class.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $class.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			href="index.php?section=Kuwasys|ChangeClass&classId={$class.ID}">{$class.label}</a>
	{else}		
		<p class="classListing"
			{if $class.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $class.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			href="index.php?section=Kuwasys|ChangeClass&classId={$class.ID}">{$class.label}</p>
	{/if}</b>
	<br>
	<div id="classDescription#{$class.ID}" class="classDescription" hidden="hidden">
		<p>{$class.status}</p>
		<p>{$class.description}</p>
	</div>
{/foreach}
<br><br>
<form action="index.php?section=Kuwasys|ClassList" method="post">
	<input type="submit" value="Zur Kurswahlliste">
</form>
</div>
{include file='web/footer.tpl'}