{extends file=$inh_path}{block name='content'}

<h2 class='module-header'>Eine Klasse verändern</h2>

<form role="form" action='index.php?module=administrator|System|Grade|ChangeGrade&amp;ID={$grade.ID}'
	method="post">
	<div class="row">
		<div class="input-group form-group col-sm-6" data-toggle="tooltip"
				title="Jahrgangsstufe">
			<span class="input-group-addon">
				<span class="icon icon-counter"></span>
			</span>
			<input type="text" name="gradelevel" placeholder="Jahrgangsstufe"
				class="form-control" value="{$grade.gradelevel}">
		</div>
		<div class="input-group form-group col-sm-6" data-toggle="tooltip"
				title="Label">
			<span class="input-group-addon">
				<span class="icon icon-bookmark"></span>
			</span>
			<input type="text" name="gradelabel" placeholder="Label"
				class="form-control" value="{$grade.label}">
		</div>
	</div>
	<div class="row">
		<div class="input-group form-group col-sm-6" data-toggle="tooltip"
				title="Schultyp">
		<span class="input-group-addon">
			<span class="icon icon-calendar"></span>
		</span>
		<select class="form-control" name='schooltype' size='1'>
			<option value='0'> Kein Schultyp </option>
			{foreach $schooltypes as $schooltype}
				<option value='{$schooltype.ID}'
					{if $schooltype.ID == $grade.schooltypeId}
						selected='selected'
					{/if}>
					{$schooltype.name}
				</option>
			{/foreach}
		</select>
		</div>
	</div>
	<a class="btn btn-default"
		href="index.php?module=administrator|System|Grade|ShowGrades">
		Abbrechen
	</a>
	<button type="submit" class="btn btn-primary pull-right">
		Klasse ändern
	</button>
</form>

{/block}
