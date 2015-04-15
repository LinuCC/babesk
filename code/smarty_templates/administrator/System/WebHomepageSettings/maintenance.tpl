{extends file=$inh_path} {block name='content'}

<h3 class="module-header">Wartungsmodus</h3>

<form class="form-horizontal" action="index.php?section=System|WebHomepageSettings&amp;action=setmaintenance" method="post">
	<div class="form-group">
		<label for="maintenance" class="control-label col-sm-2">
			Wartungsmodus aktiv?
		</label>
		<div class="col-sm-10 checkbox">
			<input type="checkbox" id="maintenance" name="maintenance"
				{if $maintenance eq 1}checked{/if}>
		</div>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-default" value="Einstellung speichern">
	</div>

</form>

{/block}