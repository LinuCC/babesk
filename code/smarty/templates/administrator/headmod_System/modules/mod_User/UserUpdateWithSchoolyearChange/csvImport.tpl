{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">{t}Import userdata with a csv-file{/t}</h2>

<p>{t}Please select the csv-file that contains the data of the users:{/t}</p>

<form class="simpleForm"
	action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|CsvImport" enctype="multipart/form-data" method="post">
	<div class="simpleForm">
		<label for="csvFile">
		</label>
		<input id="csvFile" type="file" name="csvFile">
	</div>
	<input type="submit" name="csvUploaded" value="{t}Upload{/t}">
</form>

{/block}