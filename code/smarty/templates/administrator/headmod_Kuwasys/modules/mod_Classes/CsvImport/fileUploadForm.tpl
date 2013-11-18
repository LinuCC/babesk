{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">{_g('Upload a file')}</h2>

<form action="index.php?module=administrator|Kuwasys|Classes|CsvImport|Review"
	method="post" enctype="multipart/form-data">

	<label for="csvFile">{_g('File:')}</label>
	<input type="file" name="csvFile" id="csvFile"><br />
	<input type="submit" value="{_g('Create Preview')}">
</form>

{/block}