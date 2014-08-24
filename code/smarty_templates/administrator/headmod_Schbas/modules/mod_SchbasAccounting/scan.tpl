{extends file=$checkoutParent}{block name=content}


<h3 class="module-header">Antrag erfassen</h3>


	<div class="form-horizontal" role="form">
		<div class="form-group">
			<label for="barcodeInput" class="col-sm-2 control-label">Barcode</label>
			<div class="col-sm-10">
				<input class="form-control" id="barcodeInput" type="text" autofocus />
				<small>Enter drücken, wenn Barcode eingescannt ist.</small>
			</div>
		</div>
	</div>

{/block}

{block name=js_include append}

<script type="text/javascript" src="{$path_js}/administrator/Schbas/Accounting/scan.js"></script>

{/block}