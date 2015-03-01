{extends file=$inh_path}{block name=content}

<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Modulauswahl</h3>
		</div>
		<div class="panel-body">
			<p>
				Hallo! Starte, indem du ein Modul aus der oberen Leiste auswähltst.
			</p>
		</div>
	</div>
</div>


{if $birthday != $smarty.now|date_format:"%m-%d"}
<div class="text-center">
	<img src="../include/res/images/birthday.jpg" /><br>
	<span>Fotograf: Will Clayton</span>
	<span>Lizenz: CC BY 2.0</span>
</div>
{/if}
{/block}