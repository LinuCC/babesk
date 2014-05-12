<fieldset>
	<legend>Religion verändern (ersetzt vorherige Religion(-en))</legend>
	<form method="post" action="index.php?module=administrator|System|User|DisplayAll|Multiselection|ActionExecute">
		<input type="hidden" name="actionName" value="UserReplaceReligion">
		<input type="text" name="username" value="Hier reli und so" data-enter-as-click="#action-user-replace-religion-submit">
		<button id="action-user-replace-religion-submit" type="button"
			class="btn btn-danger multiselection-action-submit">Verändern</button>
	</form>
</fieldset>