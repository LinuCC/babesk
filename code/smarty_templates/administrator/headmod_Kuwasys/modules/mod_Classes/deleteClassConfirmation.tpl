{extends file=$inh_path} {block name="content"}

<h2 class="module-header">
	{t}Class-Deletion{/t}
</h2>

<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<div class="panel-title">
				<h3 class="icon-container col-xs-2 col-sm-1">
					<span class="icon-error icon"></span>
				</h3>
				<span class="col-xs-10 col-sm-11">
					{t}Really delete the class?{/t}
				</span>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="panel-body">
			{t class=$class.label}Do you really want to delete the Class "%1"? All its data is inevitably lost when doing so!{/t}
		</div>
		<div class="panel-footer">
		<form action="index.php?module=administrator|Kuwasys|Classes|DeleteClass&amp;ID={$class.ID}" method="post">
			<input class="btn btn-danger" type="submit" value="{t}Yes{/t}" name="confirmed" />
			<input type="submit" class="btn btn-primary pull-right"
				value="{t}No, I dont want to delete the Class{/t}"
				name="declined" />
		</form>
		</div>
	</div>
</div>


{/block}
