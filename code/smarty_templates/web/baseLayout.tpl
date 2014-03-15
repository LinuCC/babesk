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
		<link rel="stylesheet" href="{$path_css}/bootstrap-theme.min.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/main.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/iconfonts/iconfonts.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/toastr.min.css" type="text/css" />
		{/block}

		<link rel="shortcut icon" href="webicon.ico" />
		<title>{$title|default:'BaBeSK'}</title>
	</head>

	<body>
		<div id="navigation" class="navbar-inverse navbar-default"
			role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php">BaBeSK</a>
				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li>
							<a href="index.php">{t}Home{/t}</a>
						</li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<form>
								<a class="btn btn-info navbar-btn"
								href="index.php?module=web|Help">
									{t}Help{/t}
								</a>
							</form>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								{$username} <b class="caret"></b>
							</a>
							<ul id="dropdown-user" class="dropdown-menu">
								{if $babeskActivated && isset($credit)}
									<li>
										<p class="navbar-text">
												<span class="highlighted">
													Guthaben: {$credit} Euro
												</span>
										</p>
									</li>
								{/if}
								<li>
									<a href="index.php?module=web|Settings">
										{t}Settings{/t}
										<span class="icon-Settings"></span>
									</a>
								</li>
								<li>
									<a href="index.php?action=logout">{t}Logout{/t}</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>

		{block name="program_title"}
			<div class="container">
				<h2 class="program_title">
					Schoolname or other nice title here...
				</h2>
			</div>
		{/block}

		{block name="module_selector"}
			<div id="module_selector" class="container">

				{$visibleModules = 0}
				{foreach $modules as $module}
					{if $module->isDisplayInMenuAllowed()}
						{$visibleModules = $visibleModules + 1}
					{/if}
				{/foreach}

				{if $visibleModules < 6}
					<div class="col-md-{6 - $visibleModules}"></div>
				{/if}
				{foreach $modules as $module}
					{if $module->isDisplayInMenuAllowed()}
						{*Center the Modules if they wouldnt span the whole page*}
						<a href="index.php?module=web|{$module->getName()}" class="col-md-2 col-sm-4 col-xs-6
						{if $activeHeadmodule == $module->getName()}active{/if}">
							<div> <!-- Correctly wrap with smaller devices with extra div -->
								<div class="icon-{$module->getName()} icon"></div>
									{$path = $moduleGenMan->modulePathGet($module)}
									{_g("modulepath_$path")}
							</div>
						</a>
					{/if}
				{/foreach}
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
				{if $error}
					<div class="col-md-8 col-md-offset-2 error-container">
						<h3><div class="icon-error icon"></div></h3>
						<p class="error_sorry">
							{t}Sorry! An error occured. We could not handle your request.{/t}
						</p>
						<div class="error_description">
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h3 class="panel-title">{t}Error-description:{/t}</h3>
								</div>
								<div class="panel-body">
									{if is_array($error)}
										{foreach $error as $msg}
											{$msg}
										{/foreach}
									{else}
										{$error}
									{/if}
								</div>
							</div>
						</div>
						<a class="btn btn-primary pull-right"
							href="{if $backlink}{$backlink}
								{else}javascript: history.go(-1){/if}">
							{t}back{/t}
						</a>
					</div>
				{/if}
			{/block}
		</div>

		{block name="footer"}
			<div id="footer">
				<div id="content_footer_conn">
					<div class="footer-heading"></div>
				</div>
				<div class="container footer-text">
					<div class="modules col-sm-4 col-xs-12">
							<div class="footer-heading">{t}Actions:{/t}</div>
							<p><a href="index.php?module=web|Babesk">Essen bestellen</a></p>
							<p><a href="index.php?module=web|Kuwasys">Kurswahlen</a></p>
							<p><a href="index.php?module=web|Schbas">Schulbuchausleihe</a></p>
							<p><a href="index.php?module=web|PVau">Vertretungsplan</a></p>
							<p><a href="index.php?module=web|Fits">Internet-Führerschein</a></p>
							<p><a href="index.php?module=web|Messages">Nachrichten</a></p>
							<p><a href="index.php?module=web|Settings">Einstellungen</a></p>
					</div>
					<div class="contact col-sm-4 col-xs-12">
						<div class="footer-heading">{t}Contact:{/t}</div>


						<!-- Remove following paragraphs and put your own information in -->
						<p>+++</p>
						<p>Insert your contact-information here</p>
						<p>+++</p>

					</div>
					<div class="col-sm-4 col-xs-12 right-col">
						<div class="footer-heading">{t}More:{/t}</div>
						<div class="help">
							<a class="btn btn-sm btn-info"
								href="index.php?module=web|Help"
							>{t}Help{/t}</a>
						</div>
						<div class="program_version">
							<p>
								BaBeSK {$babesk_version}<br />
								GNU aGPLv3.0 licensed
							</p>
							<p>You can reach us at
							<a href="http://sourceforge.net/projects/babesk/"
							target="_blank">
								SourceForge.
							</a>
							We also have a
							<a href="http://sourceforge.net/p/babesk/bugs" target="_blank">
								Bugtracker
							</a>
							for bugs and feature requests.
						</p>
						<p style="font-size: 10px; position:relative; top: 50px">
							Also, <a href="javascript: toastr.info('<div class=&quot;icon-darthvader icon&quot;></div>', 'A wild Vader appeared!')">spam.</a>
						</p>
						</div>
					</div>
				</div>
			</div>
		{/block}


		{block name="js_include"}
			<script type="text/javascript" src="{$path_js}/jquery.min.js"></script>
			<script type="text/javascript" src="{$path_js}/jquery.cookie.js"></script>
			<script type="text/javascript" src="{$path_js}/bootstrap.min.js"></script>
			<script type="text/javascript" src="{$path_js}/toastr.min.js"></script>

		{literal}
			<script type="text/javascript">

				jQuery.fn.outerHtml = function() {
					return jQuery('<div />').append(this.eq(0).clone()).html();
				};

				$('#account_settings').on('click', function(ev){$('#account').toggle()});

				toastr.options = {
				  "closeButton": false,
				  "debug": false,
				  "positionClass": "toast-top-right",
				  "onclick": null,
				  "showDuration": "300",
				  "hideDuration": "1000",
				  "timeOut": "0",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "fadeIn",
				  "hideMethod": "fadeOut"
				}

			</script>
		{/literal}

		{/block}

	</body>
</html>
