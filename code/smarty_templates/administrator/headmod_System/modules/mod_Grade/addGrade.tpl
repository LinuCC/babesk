{extends file=$inh_path}{block name='content'}

<h2 class='module-header'>Eine Klasse hinzufügen</h2>

<form role="form" action="index.php?module=administrator|System|Grade|AddGrade"
	method="post">
	<div class="row">
		<div class="input-group form-group col-sm-6">
			<span class="input-group-addon">
				<span class="icon icon-counter"></span>
			</span>
			<input type="text" name="gradelevel" placeholder="Jahrgangsstufe"
				class="form-control">
		</div>
		<div class="input-group form-group col-sm-6">
			<span class="input-group-addon">
				<span class="icon icon-bookmark"></span>
			</span>
			<input type="text" name="gradelabel" placeholder="Label"
				class="form-control">
		</div>
	</div>
	<div class="row">
		<div class="input-group form-group col-sm-6">
		<span class="input-group-addon">
			<span class="icon icon-calendar"></span>
		</span>
		<select class="form-control" name='schooltype' size='1'>
			{foreach $schooltypes as $schooltype}
				<option value='{$schooltype.ID}'>
					{$schooltype.name}
				</option>
			{/foreach}
		</select>
		</div>
	</div>
	<a class="btn btn-default"
		href="index.php?module=administrator|System|Grade">
		Abbrechen
	</a>
	<button type="submit" class="btn btn-primary pull-right">
		Klasse hinzufügen
	</button>
</form>
{/block}
