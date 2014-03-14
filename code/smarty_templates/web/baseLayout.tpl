<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		{if isset($redirection)}
			{*If this Var is set, redirect the user to another Website*}
			<meta HTTP-EQUIV="REFRESH" content="{$redirection.time};
			url=index.php?section={$redirection.target}" />
		{/if}

		{block name="style_include"}
		<!-- <link rel="stylesheet" href="{$path_smarty_tpl}/web/css/general.css" type="text/css" /> -->
		<link rel="stylesheet" href="{$path_css}/bootstrap-theme.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/bootstrap.css" type="text/css" />
		{/block}

		<link rel="shortcut icon" href="webicon.ico" />
		<title>{$title|default:'BaBeSK'}</title>
	</head>

	<body>
	<div class="navbar navbar-default">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">BaBeSK</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active">
						<a href="index.php">{t}Main menu{/t}</a>
					</li>
					<li>
						<a href="index.php?module=web|Settings">{t}Settings{/t}</a>
					</li>
					<li>
						<a href="index.php?module=web|Help">{t}Help{/t}</a>
					</li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					{if $babeskActivated && isset($credit)}
<!-- 						<li>
							<p>Guthaben: {$credit} Euro</p>
						</li>
 -->					{/if}
					<li>
						<a href="index.php?action=logout">{t}Logout{/t}</a>
					</li>
<!-- 					<li>
						<p>Name: {$username}</p>
					</li>
 -->				</ul>
			</div>
		</div>
	</div>


		<div id="header">
			<div id="top">
				<div id="top_left">
					<a id="account_settings" href="#">Kontoeinstellungen</a><br />
					<div id="account" style="display: none;">
						{if $babeskActivated}
						<a href="index.php?section=Babesk|Account">Karte sperren</a>
						{/if}
						<br>
						<a href="index.php?section=Settings">Daten &auml;ndern</a>
					</div>
				</div>

				<div id="top_right">
					{if $newmail}<a href="index.php?section=Messages"><img src="../smarty/templates/web/images/email.png"></a>{/if}<br />
					<a href="index.php?section=Babesk|Help">Hilfe</a><br />
					<a href="index.php?action=logout">Ausloggen</a>
				</div>
				<div id="heading">
					<a href="index.php"><h1>LeGeria Online</h1></a>
				</div>
			</div>
		</div>

		{include file="{$path_smarty_tpl}/web/module_selector.tpl" title='Modul Wählen'}
		<div id="main">
			<div id="content">
				<noscript>
					<p>
						<b>
							Ihr Browser hat JavaScript ausgestellt. Diese Seite funktioniert nur dann vollständig, wenn sie Javascript aktiviert haben!
						</b>
						<br />
						(Kurswahlen sind auch ohne Javascript möglich, allerdings wird die Seite nicht korrekt angezeigt)
						<br />
						Ein Anleitung finden sie
						<a href="http://www.enable-javascript.com/de/" target="_blank">
							hier
						</a>
						.
					</p>
					<hr />
				</noscript>
				{if isset($error) and count($error)}
					<p class="error">
						{foreach $error as $errorMsg}
							{$errorMsg}
						{/foreach}
					</p>
				{/if}
				{if isset($message) and count($message)}
					<p>
						{foreach $message as $msg}
							{$msg}
						{/foreach}
					</p>
				{/if}
				{if $status != ''}
					<h4>
						{$status}
					</h4>
				{/if}
				{*Allow content to be shown without having to change the code of the old-style smarty Templates (They worked by including the header beforehand and the footer afterwards)*}
				{block name="content"}
					{if isset($content) and $content != ''}
						{$content}
					{/if}
				{/block}
				<div class="buttons">
				{*A Link back*}
					<div class="button_back">
						{if isset($backlink)}
						<form action="{$backlink}" method="post">
							<input type="submit" value="{t}back{/t}" />
						</form>
						{/if}
					</div>
					<div class="linking_buttons">
						{foreach $buttonlinks as $button}
							<form action="{$button.link}" method="post">
								<input type="submit" value="{$button.name}" />
							</form>
						{/foreach}
					</div>
				</div>
				{if $last_login}
				<div>
					<p id="last_login">Letzter Login: {$last_login}</p>
				</div>
				{/if}
				<div id="background">

					{if isset($footerBackground)}
						<img src="{$footerBackground}" class="center" />
					{/if}

				</div>
			</div>
		</div>

		<div id="footer">
			<p>
				BaBeSK {$babesk_version}
			</p>
		</div>


		{block name="js_include"}
			<script type="text/javascript" src="{$path_js}/jquery.min.js"></script>
			<script type="text/javascript" src="../include/js/jquery.cookie.js"></script>
		{/block}

		{literal}
		<script type="text/javascript">

			jQuery.fn.outerHtml = function() {
				return jQuery('<div />').append(this.eq(0).clone()).html();
			};

			$('#account_settings').on('click', function(ev){$('#account').toggle()});

		</script>
		{/literal}
	</body>
</html>
