{include file='web/header.tpl' title='Hauptmenü des Kurswahlsystems'}

<style type='text/css'  media='all'>

a.classListing {
	float:right;
}
p.classListing {
	float:right;
}

div.classListing {
	
	border-style: solid;
	border-width: 5px;
	border-color: #2e6132;
	-webkit-border-radius: 20px;
  -khtml-border-radius: 20px;
  -moz-border-radius: 20px;
	border-radius: 20px;
	margin: 0 auto;
	padding: 15px;
	width: 400px;
}

</style>

<h2>Hauptmenü des Kurswahlsystems</h2><br>
<div class="classListing">
<h4 >Übersicht der Kurse</h4>
{foreach $classes as $class}
	{if $class.registrationEnabled}
		<a class="classListing"
			{if $class.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $class.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			href="index.php?section=Kuwasys|ChangeClass&ID={$class.ID}">{$class.label} --- {$class.status}</a><br>
	{else}		
		<p class="classListing"
			{if $class.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $class.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $class.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			href="index.php?section=Kuwasys|ChangeClass&ID={$class.ID}">{$class.label} --- {$class.status}</p><br>
	{/if}
	
{/foreach}
<br><br>
<form action="index.php?section=Kuwasys|classList" method="post">
	<input type="submit" value="Zur Kurswahlliste">
</form>
</div>
{include file='web/footer.tpl'}