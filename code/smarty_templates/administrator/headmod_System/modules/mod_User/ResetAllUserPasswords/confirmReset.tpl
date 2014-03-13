{extends file=$inh_path}{block name=content}

<h2 class="moduleHeader">{t}Reset passwords of all users{/t}</h2>

<form action="index.php?module=administrator|System|User|ResetAllUserPasswords"
	method="post">
	<p>
		{t}Here you can reset the passwords of all users to the preset password.{/t}
		{if $presetPassword}
		{t}You will reset the passwords of all users (but the one which id is 1, usually the Superadmin) to the preset password. Thus this may be true for your password, too! If you can't log in after resetting the passwords, try the preset password.{/t}

		<div>
			<a href="http://localhost/babesk/code/administrator/index.php?module=administrator|System|PresetPassword">{t}Click here{/t}</a> {t}to change the preset password.{/t}
		</div>
		<fieldset class="blockyField">
			<legend>
				<b>{t}!!!Important!!!{/t}</b>
			</legend>
			{t}After the reset manually change the passwords of all accounts that have access to the Administrator-Section, else everyone can log themselfes into the Administrator-Section if they know the username!{/t}
		</fieldset>

		{else}
		{t}At the moment the preset password is not set. Please{/t}
		<a href="http://localhost/babesk/code/administrator/index.php?module=administrator|System|PresetPassword">{t}click here{/t}</a>
		{t}to set a preset password.{/t}
		{/if}
	</p>

	<input class="isolated" type="submit" name="resetConfirmed" value="{t}Yes, I want to reset the passwords!{/t}" />
</form>
{/block}