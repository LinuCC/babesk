{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Wartungsmodus</h3>

<form action="index.php?section=System|WebHomepageSettings&amp;action=setmaintenance" method="post">

	Wartungsmodus aktiv?<input type="checkbox" name="maintenance" name="maintenance"  {if $maintenance eq 1}checked{/if}><br/>
	<input type="submit" value="Einstellung speichern">

</form>

{/block}