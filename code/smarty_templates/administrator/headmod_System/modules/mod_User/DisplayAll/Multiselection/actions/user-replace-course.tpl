{$courselist = $doctrine->getRepository('\\Babesk\\ORM\\SystemGlobalSettings')->findOneByName('course')}
{if !empty($courselist)}
	{$courses = explode('|', $courselist->getValue())}
{/if}

<fieldset>
	<legend>Kurse verändern (ersetzt vorherige Kurse)</legend>
		<div class="multiselection-action-view">
			<input type="hidden" name="actionName" value="UserReplaceCourse">
			<div class="form-group col-sm-10 row">
				<div class="col-sm-12">
					<div class="input-group btn-group">
						<span class="input-group-addon">
							<span class="icon icon-clipboard"></span>
						</span>
						<select name="courses" class="multiselect"
						data-toggle="tooltip" title="Kurs auswählen" multiple="multiple">
							{foreach $courses as $course}
								<option value="{$course}">{$course}</option>
							{/foreach}
						</select>
					</div>
				</div>
		</div>
				<div class="form-group pull-right">
					<button id="action-user-replace-course-submit" type="button"
						class="btn btn-warning multiselection-action-submit"
						data-toggle="tooltip" title="Kurse verändern">
							<span class="icon icon-edit"></span>
					</button>
				</div>
	</div>
</fieldset>

<script type="text/javascript">
	$('.multiselect').multiselect({
		buttonContainer: '<div class="btn-group" />',
		buttonWidth: '100%'
	});
</script>