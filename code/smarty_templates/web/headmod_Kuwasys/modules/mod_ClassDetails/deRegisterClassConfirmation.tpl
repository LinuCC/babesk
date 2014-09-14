{extends file=$inh_path}{block name=content}

<div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-xs-12">
	<div class="panel panel-danger">
		<div class="panel-heading">
			<div class="panel-title">
				Wollen sie sich wirklich vom Kurs {$class.label} abmelden?
			</div>
		</div>
		<div class="panel-body">
		<form action="index.php?section=Kuwasys|ClassDetails&amp;action=deRegisterClass&amp;classId={$class.ID}&amp;categoryId={$class.categoryId}" method="post">
			<input class="btn btn-danger" type="submit" name="yes" value="Ja, ich möchte mich vom Kurs abmelden">
			<a href="index.php?module=web|Kuwasys" class="btn btn-primary pull-right"
				>
				Nein, ich möchte mich nicht vom Kurs abmelden
				</a>
		</form>
		</div>
	</div>
</div>

{/block}