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
		<link rel="stylesheet" href="{$path_css}/bootstrap-theme.min.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/bootstrap.min.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/web.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/font-awesome.min.css" type="text/css" />
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
						{block name="nav_home"}
							<li>
								<a href="index.php">{t}Home{/t}</a>
							</li>
						{/block}
					</ul>
					<ul class="nav navbar-nav navbar-right">
						{block name="nav_help_button"}
							<li>
								<form>
									<a class="btn btn-info navbar-btn"
									href="index.php?module=web|Help">
										{t}Help{/t}
									</a>
								</form>
							</li>
						{/block}
						{block name="nav_user_dropdown"}
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
											<span class="fa fa-cog"></span>
										</a>
									</li>
									<li>
										<a href="index.php?action=logout">{t}Logout{/t}</a>
									</li>
								</ul>
							</li>
						{/block}
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

				{foreach $modules as $module}
					<a href="index.php?module=web|{$module->getName()}"
					class="col-md-2 col-xs-6
						{*Center the Modules if they wouldnt span the whole page*}
						{if $module@first}
							col-md-offset-{6 - $visibleModules}
						{else}
							col-md-offset-0
						{/if}
						{if $activeHeadmodule == $module->getName()}active{/if}">
						<div> <!-- Correctly wrap with smaller devices with extra div -->
							<div>
								<i class="fa fa-{$module->getName()}"></i>
							</div>
								{$path = $moduleGenMan->modulePathGet($module)}
								{_g("modulepath_$path")}
						</div>
					</a>
					{if $module@iteration is even}
						{*Clearfix when text under the icons begin to wrap*}
						<div class="clearfix visible-xs"></div>
					{/if}
				{/foreach}
			</div>
		{/block}

		<div id="content" class="container">
			<noscript>
				<div class="panel panel-danger">
					<div class="panel-heading">
						<div class="panel-title">
							Javascript ist deaktiviert
						</div>
					</div>
					<div class="panel-body">
							Ihr Browser hat JavaScript ausgestellt. Diese Seite funktioniert nur dann, wenn sie Javascript aktiviert haben!
						<a class="btn btn-primary pull-right" href="http://www.enable-javascript.com/de/" target="_blank">
							Aktivierungsanleitung
						</a>
					</div>
				</div>
			</noscript>
			{block name="content"}
				{if $error}
					<div class="col-md-8 col-md-offset-2 error-container">
						<div class="panel panel-danger">
							<div class="panel-heading">
								<div class="panel-title">
									<h3 class="icon-container col-xs-2 col-sm-1">
										<span class="fa fa-exclamation-triangle"></span>
									</h3>
									<span class="col-xs-10 col-sm-11">
										{t}Sorry! An error occured. We could not handle your request.{/t}
									</span>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="panel-body">
								<div>
									{if is_array($error)}
										{foreach $error as $msg}
											{$msg}
										{/foreach}
									{else}
										{$error}
									{/if}
								</div>
							</div>

							{if $backlink}
								<a class="btn btn-primary pull-right"
									href="{$backlink}">
									{t}back{/t}
								</a>
							{/if}
						</div>
					</div>
				{/if}
				{if $message}
					<div class="col-md-8 col-md-offset-2 message-container">
						<div class="panel panel-info">
							<div class="panel-heading">
								<div class="panel-title">
									<h3 class="icon-container col-xs-2 col-sm-1">
										<span class="fa fa-info-circle"></span>
									</h3>
									<span class="col-xs-10 col-sm-11">
										{t}Information{/t}
									</span>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="panel-body">
									{if is_array($message)}
										{foreach $message as $msg}
											<div>{$msg}</div>
										{/foreach}
									{else}
										{$message}
									{/if}
							</div>

							{if $backlink}
								<a class="btn btn-primary pull-right"
									href="{$backlink}">
									{t}back{/t}
								</a>
							{/if}
						</div>
					</div>
				{/if}
				{if $success}
					<div class="col-md-8 col-md-offset-2 success-container">
						<div class="panel panel-success">
							<div class="panel-heading">
								<div class="panel-title">
									<h3 class="icon-container col-xs-2 col-sm-1">
										<span class="fa fa-check"></span>
									</h3>
									<span class="col-xs-10 col-sm-11">
										{t}Success!{/t}
									</span>
									<div class="clearfix"></div>
								</div>
							</div>
							<div class="panel-body">
								{if is_array($success)}
									{foreach $success as $msg}
										<div>{$msg}</div>
									{/foreach}
								{else}
									{$success}
								{/if}
							</div>

							{if $backlink}
								<a class="btn btn-primary pull-right"
									href="{$backlink}">
									{t}back{/t}
								</a>
							{/if}
						</div>
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
						{block name="footer_actions"}
							<div class="footer-heading">{t}Actions:{/t}</div>
							<p><a href="index.php?module=web|Babesk">Essen bestellen</a></p>
							<p><a href="index.php?module=web|Kuwasys">Kurswahlen</a></p>
							<p><a href="index.php?module=web|Schbas">Schulbuchausleihe</a></p>
							<p><a href="index.php?module=web|PVau">Vertretungsplan</a></p>
							<p><a href="index.php?module=web|Fits">Internet-Führerschein</a></p>
							<p><a href="index.php?module=web|Messages">Nachrichten</a></p>
							<p><a href="index.php?module=web|Settings">Einstellungen</a></p>
						{/block}
					</div>
					<div class="contact col-sm-4 col-xs-12">
						<div class="footer-heading">{t}Contact:{/t}</div>


						<!-- Remove following paragraphs and put your own information in -->
						<p>+++</p>
						<p>Insert your contact-information here</p>
						<p>+++</p>

					</div>
					<div class="col-sm-4 col-xs-12 right-col">
						<div class="footer-heading">
							{t}More:{/t}
						</div>
						{block name="footer_help_button"}
							<div class="help">
								<a class="btn btn-sm btn-info"
									href="index.php?module=web|Help"
								>{t}Help{/t}</a>
							</div>
						{/block}
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
							Also, <a href="#" onclick="javascript: toastr.info('<div class=&quot;fa fa-gamepad&quot; style=&quot;font-size:48px&quot;></div>', 'A wild Gamepad appeared!')">spam.</a>
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
			<script type="text/javascript" src="{$path_js}/json2.min.js"></script>
			<script type="text/javascript" src="{$path_js}/custom-base.js"></script>
		{literal}
			<script type="text/javascript">

				jQuery.fn.outerHtml = function() {
					return jQuery('<div />').append(this.eq(0).clone()).html();
				};

				toastr.options = {
				  "closeButton": true,
				  "debug": false,
				  "positionClass": "toast-top-center",
				  "onclick": null,
				  "showDuration": "300",
				  "hideDuration": "1000",
				  "timeOut": "7500",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "fadeIn",
				  "hideMethod": "fadeOut"
				}

				document.cookie="testcookie";
				var cookieEnabled = (
					document.cookie.indexOf("testcookie")!=-1
				);
				if(!cookieEnabled) {
					toastr['error']('Cookies sind nicht aktiviert! Diese Website benötigt Cookies um zu funktionieren.', 'Cookies');
				}

			</script>
		{/literal}

		{/block}

	</body>
</html>
