{extends file=$inh_path}{block name="content"}

<h2 class="module-header">
	{t}Print Recharge Balance{/t}
</h2>

<form action="index.php?module=administrator|Babesk|Recharge|PrintRechargeBalance" method="post">
	<div class="form-group">
		<label for="interval">{t}Print Recharge Balance{/t}</label>
		<select name="interval" id="interval" class="form-control">
			{foreach $intervals as $intervalId => $intervalname}
				<option value="{$intervalId}">{$intervalname}</option>
			{/foreach}
		</select>
	</div>

	<div class="form-group">
		<label for="date">{t}At:{/t}</label>
		<input type="text" id="date" class="form-control" name="date" size="10"
			data-provide="datepicker" data-date-format="dd.mm.yyyy"
			data-date-language="de" value="{date('d.m.Y')}" />
	</div>
	<input type="submit" class="btn btn-primary" value="{t}Download Pdf{/t}" />

</form>

{/block}

{block name=style_include append}

<link rel="stylesheet" type="text/css" href="{$path_css}/datepicker3.css">

{/block}

{block name=js_include append}

<script type="text/javascript" src="{$path_js}/vendor/datepicker/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">

</script>

{/block}
