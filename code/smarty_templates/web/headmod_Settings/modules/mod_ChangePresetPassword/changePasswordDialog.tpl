{extends file=$inh_path}{block name=content}

<div align="center"><h3>Erst-Passwort verändern</h3></div> <br>

<p class="alert alert-info">
	Bitte gib ein neues Passwort für deinen Account ein.
</p>

<form class="form-horizontal"
	action="index.php?section=Settings|ChangePresetPassword&action=changePassword" method='POST'>
	<div class="form-group">
		<label for="password" class="control-label col-sm-2">neues Passwort</label>
		<div class="col-sm-10">
			<div class="input-group">
				<div class="input-group-addon">
					<span class="icon icon-lock"></span>
				</div>
				<input type='password' id="password" class="form-control"
					name='newPassword'>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="password-repeat" class="control-label col-sm-2">
			Passwort wiederholen
		</label>
		<div class="col-sm-10">
			<div class="input-group">
				<div class="input-group-addon">
					<span class="icon icon-lock"></span>
				</div>
				<input type='password' id="password-repeat" class="form-control"
					name='newPasswordRepeat'>
			</div>
		</div>
	</div>
	{if $onFirstLoginChangeEmail}
		<div class="form-group">
			<label for="email" class="control-label col-sm-2">
				Email-Adresse {if !$emailChangeForced}(erwünscht){/if}
			</label>
			<div class="col-sm-10">
				<div class="input-group">
					<div class="input-group-addon">
						<span class="icon icon-email"></span>
					</div>
					<input type='text' id="email" class="form-control" name='newEmail'
						value="{$userEmail}">
				</div>
			</div>
		</div>
	{/if}
	<input type='submit' class="btn btn-primary" value="Passwort ändern">
</form>

{/block}