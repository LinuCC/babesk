{extends file=$inh_path}{block name=content}

<div class="row">
	<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Modulauswahl</h3>
			</div>
			<div class="panel-body">
				<p>
					Hallo! Starte, indem du ein Modul aus der oberen Leiste auswählst.
				</p>
			</div>
		</div>
	</div>
</div>


{if $birthday == $smarty.now|date_format:"%m-%d"}
<div class="row">
	<div class="col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4 text-center">
		<div class="thumbnail">
			<img src="../include/res/images/birthday.jpg" />
			<div class="caption">
				<span>Fotograf: Will Clayton</span>
				<span>Lizenz: CC BY 2.0</span>
			</div>
		</div>
	</div>
</div>
{/if}
{/block}