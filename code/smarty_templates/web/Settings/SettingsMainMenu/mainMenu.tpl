{extends file=$inh_path}{block name=content}


<div class="col-md-8 col-md-offset-2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				Einstellungen
			</div>
		</div>
		<div class="panel-body">
			<p>
				Hier kannst du deine Einstellungen verändern. Was willst du verändern?
			</p>
			<div class="row">
				<div class="col-md-4">
					<form action="index.php?section=Settings|ChangeEmail" method="post">
						<input class="btn btn-default" type="submit"
							value="Die Email-adresse verändern">
					</form>
				</div>
				<div class="col-md-4 text-center">
					<form action="index.php?section=Settings|SettingsChangePassword" method="post">
						<input class="btn btn-default" type="submit" value="Das Passwort verändern">
					</form>
				</div>
				<div class="col-md-4">
					<a href="index.php?module=web|Settings|Account&amp;lockAccount=confirm" class="btn btn-danger pull-right">
						Den Account sperren
					</a>
				</div>
			</div>
		</div>
	</div>
</div>


{/block}