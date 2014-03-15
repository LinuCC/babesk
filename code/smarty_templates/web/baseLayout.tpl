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
		<link rel="stylesheet" href="{$path_css}/main.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/iconfonts/headmods.css" type="text/css" />
		{/block}

		<link rel="shortcut icon" href="webicon.ico" />
		<title>{$title|default:'BaBeSK'}</title>
	</head>

	<body>
		<div id="navigation" class="navbar-inverse navbar-default">
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
						<li>
							<a href="index.php">{t}Home{/t}</a>
						</li>
						<li>
							<a href="index.php?module=web|Settings">
								<span class="icon-Settings"></span>
								{t}Settings{/t}
							</a>
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

		{block name="program_title"}
			<div class="container">
				<h2 class="program_title">
					Kurswahlsystem Kuwasys
				</h2>
			</div>
		{/block}

		{block name="module_selector"}
			<div id="module_selector" class="container">
				<a href="index.php?module=web|Babesk" class="col-md-2">
					<div> <!-- Correctly wrap with smaller devices with extra div -->
						<div class="icon-Babesk icon"></div>
						Essen bestellen
					</div>
				</a>
				<a href="index.php?module=web|Kuwasys" class="col-md-2">
					<div>
						<div class="icon-Kuwasys icon"></div>
						Kurswahlen
					</div>
				</a>
				<a href="index.php?module=web|Schbas" class="col-md-2">
					<div>
						<div class="icon-Schbas icon"></div>
						Schulbuchausleihe
					</div>
				</a>
				<a href="index.php?module=web|PVau" class="col-md-2">
					<div>
						<div class="icon-PVau icon"></div>
						Vertretungsplan
					</div>
				</a>
				<a href="index.php?module=web|Fits" class="col-md-2">
					<div>
						<div class="icon-Fits icon"></div>
						Internet-Führerschein
					</div>
				</a>
				<a href="index.php?module=web|Messages" class="col-md-2">
					<div>
						<div class="icon-Messages icon"></div>
						Nachrichten
					</div>
				</a>
			</div>
		{/block}

		<div id="content" class="container">
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
			{block name="content"}
			{/block}
		</div>


		<div id="footer">
			<div id="content_footer_conn"></div>
			<div class="container">
				<div class="col-md-2 col-md-offset-10">
					<div class="program_version">
						BaBeSK {$babesk_version}
					</div>
				</div>
			</div>
		</div>

		{literal}
		<script type="text/javascript">

			jQuery.fn.outerHtml = function() {
				return jQuery('<div />').append(this.eq(0).clone()).html();
			};

			$('#account_settings').on('click', function(ev){$('#account').toggle()});

		</script>
		{/literal}

		{block name="js_include"}
			<script type="text/javascript" src="{$path_js}/jquery.min.js"></script>
			<script type="text/javascript" src="../include/js/jquery.cookie.js"></script>
		{/block}

	</body>
</html>
