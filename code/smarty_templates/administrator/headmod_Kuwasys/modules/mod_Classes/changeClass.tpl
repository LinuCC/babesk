{extends file=$inh_path} {block name=content}

<h3 class='module-header'>Kurs verändern</h3>

<form action="index.php?module=administrator|Kuwasys|Classes|ChangeClass&amp;ID={$class->getID()}" role="form" method="post">
	<fieldset>
		<legend>Kursdaten</legend>
		<div class="row">
			<div class="col-xs-12 col-md-6 form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-newspaper-o"></span>
					</span>
					<input type="text" name="label" class="form-control"
						value="{$class->getLabel()}" data-toggle="tooltip"
						title="Kurstitel">
				</div>
			</div>
			<div class="col-xs-12 col-md-6 form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-sliders fa-fw"></span>
					</span>
					<input type="text" name="maxRegistration" class="form-control"
						value="{$class->getMaxRegistration()}" data-toggle="tooltip"
						title="Maximale Registrierungen">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-clipboard fa-fw"></span>
					</span>
					<textarea name="description" class="form-control" maxlength="1024"
						rows="4" data-toggle="tooltip" title="Beschreibung"
						>{$class->getDescription()}</textarea>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6 form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-calendar fa-fw"></span>
					</span>
					<select name="schoolyearId" class="form-control"
						data-toggle="tooltip" title="Schuljahr">
						{foreach $schoolyears as $schoolyear}
							<option value="{$schoolyear->getID()}"
								{if $class->getSchoolyear() == $schoolyear} selected {/if}
							>
								{$schoolyear->getLabel()}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 form-group">
				<label for="allowRegistration">
					Registrierungen ermöglichen?
				</label>
				<input type="checkbox" id="allowRegistration"
					name="allowRegistration" data-on-text="Ja" data-off-text="Nein"
					data-on-color="warning" data-off-color="default" data-size="small"
					{if $class->getRegistrationEnabled()}checked="checked"{/if}>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-6 form-group">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-clock-o"></span>
					</span>
					<select id="category-select" class="form-control"
						multiple="multiple">
						{foreach $categories as $category}
							<option value="{$category->getID()}"
								{if $class->getCategories()->contains($category)} selected {/if}
							>
								{$category->getTranslatedName()}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-xs-12 col-md-6 form-group">
				<label for="isOptional">
					Ist Optional?
				</label>
				<input type="checkbox" id="isOptional"
					name="isOptional" data-on-text="Ja" data-off-text="Nein"
					data-on-color="info" data-off-color="default" data-size="small"
					{if $class->getIsOptional()}checked="checked"{/if}>
			</div>
		</div>
	</fieldset>
	<input type="submit" class="btn btn-primary" value="Kurs verändern">

</form>

{/block}

{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/bootstrap-switch.min.css" type="text/css" />
<link rel="stylesheet" href="{$path_css}/bootstrap-multiselect.css" type="text/css" />
{/block}

{block name=js_include append}

<script type="text/javascript" src="{$path_js}/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="{$path_js}/bootstrap-multiselect.min.js"></script>
<script type="text/javascript" src="{$path_js}/administrator/Kuwasys/Classes/change-class.js"></script>
{/block}