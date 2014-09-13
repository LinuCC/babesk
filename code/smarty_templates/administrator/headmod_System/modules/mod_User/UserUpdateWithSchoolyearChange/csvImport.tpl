{extends file=$base_path}{block name="content"}

<h3 class="module-header">{t}Import userdata with a csv-file{/t}</h3>


<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|CsvImport" enctype="multipart/form-data" method="post">
	<div class="form-group">
			<label for="csvFile">
				{t}Please select the csv-file that contains the data of the users:{/t}
			</label>
			<input id="csvFile" type="file" name="csvFile">
	</div>
	<input type="submit" class="btn btn-default" name="csvUploaded"
		value="{t}Upload{/t}">
</form>

{/block}