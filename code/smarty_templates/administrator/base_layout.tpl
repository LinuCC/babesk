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
		<link rel="stylesheet" href="{$path_css}/administrator.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/iconfonts/iconfonts.css" type="text/css" />
		<link rel="stylesheet" href="{$path_css}/toastr.min.css" type="text/css" />
		{/block}

		<link rel="shortcut icon" href="webicon.ico" />
		<title>{$title|default:'BaBeSK'}</title>
	</head>

	<body>
		{*-----------------------------------------------------
		 * The top-navigation
		 *}
		<div id="navigation" class="navbar-inverse navbar-default navbar-fixed-top"
			role="navigation">
			<div class="">
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
											<span class="icon-Settings"></span>
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

		<div class="container-fluid">
			<div class="row">
				{*-----------------------------------------------------
				 * The sidebar-navigation
				 *}
				<div id="sidebar-module-selection" class="col-sm-3 col-md-2 sidebar">
					<ul id="sidebar-base-nav" class="nav">
						<li>
							{foreach $headmodules as $headmodule}
								{$hModulepath = $moduleGenMan->modulePathGet($headmodule)}
								<li>
									<a href="#" data-toggle="collapse" data-target="#sidebar-module-{str_replace('/', '_', $hModulepath)}" class="collapsed sidebar-folder">
										<span>{_g('modulepath_'|cat:$hModulepath)}</span>
										<span class="toggle-icon icon icon-plus pull-right"></span>
									</a>
									<ul id="sidebar-module-{str_replace('/', '_', $hModulepath)}"
									class="nav collapse">
										{foreach $headmodule->getChilds() as $module}
											{$modulepath = $moduleGenMan->modulePathGet($module)}
											<li>
												<a href="index.php?module=administrator|{$headmodule->getName()}|{$module->getName()}">
													<span>{_g('modulepath_'|cat:$modulepath)}</span>
												</a>
											</li>
										{/foreach}
									</ul>
								</li>
							{/foreach}
						</li>
						<li>
							<a href="#" data-toggle="collapse" data-target="#headmod-submenu-1" class="collapsed sidebar-folder">
								<span>Custom folder</span>
								<span class="toggle-icon icon icon-plus pull-right"></span>
							</a>
							<ul id="headmod-submenu-1" class="nav collapse">
								<li>
									<a href="#">Custom Link</a>
								</li>
								<li>
									<a href="#" data-toggle="collapse" data-target="#headmod-submenu-3" class="collapsed sidebar-folder">
										<span>Custom nested folder</span>
										<span class="toggle-icon icon icon-plus pull-right"></span>
									</a>
									<ul id="headmod-submenu-3" class="nav collapse">
										<li>
											<a href="#">Custom Link</a>
										</li>
										<li>
											<a href="#">Another Link that could do something...</a>
										</li>
									</ul>
								</li>
								<li class="spacer"></li>
								<li>
									<a href="#">Hey, there is a spacer over me!</a>
								</li>

							</ul>
						</li>
					</ul>
				</div>
				<div id="main_wrapper" class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2">

					{*-----------------------------------------------------
					 * Breadcrumb displaying which module we use right now
					 *}
					{block name="module_breadcrumb"}
						<ul class="module_breadcrumb breadcrumb">
							<li>administrator</li>
							{$level = 2}
							{while $moduleExecCommand->moduleAtLevelGet($level)}
							<li>
								{if $level > 2}
								<a href="index.php?module={$moduleExecCommand->pathGet('|', $level + 1)}">
								{/if}
									{$moduleExecCommand->moduleAtLevelGet($level)}
								{if $level > 2}
								</a>
								{/if}
							</li>
								{$level = $level + 1}
							{/while}
						</ul>
					{/block}
					<div id="content">
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
						{block name="filling_content"}
							<div class="container">
								{if $_userErrorOutput}
									<div class="col-md-8 col-md-offset-2 error-container">
										<div class="panel panel-danger">
											<div class="panel-heading">
												<div class="panel-title">
													<h3 class="icon-container col-xs-2 col-sm-1">
														<span class="icon-error icon"></span>
													</h3>
													<span class="col-xs-10 col-sm-11">
														{t}Sorry! An error occured. We could not handle your request.{/t}
													</span>
													<div class="clearfix"></div>
												</div>
											</div>
											<div class="panel-body">
												<div>
													{if is_array($_userErrorOutput)}
														{foreach $_userErrorOutput as $msg}
															{$msg}
														{/foreach}
													{else}
														{$error}
													{/if}
												</div>
											</div>

											<a class="btn btn-primary pull-right"
												href="{if $backlink}{$backlink}
													{else}javascript: history.go(-1){/if}">
												{t}back{/t}
											</a>
										</div>
									</div>
								{/if}
								{if $message}
									<div class="col-md-8 col-md-offset-2 message-container">
										<div class="panel panel-info">
											<div class="panel-heading">
												<div class="panel-title">
													<h3 class="icon-container col-xs-2 col-sm-1">
														<span class="icon icon-info"></span>
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

											<a class="btn btn-primary pull-right"
												href="{if $backlink}{$backlink}
													{else}javascript: history.go(-1){/if}">
												{t}back{/t}
											</a>
										</div>
									</div>
								{/if}
								{if $success}
									<div class="col-md-8 col-md-offset-2 success-container">
										<div class="panel panel-success">
											<div class="panel-heading">
												<div class="panel-title">
													<h3 class="icon-container col-xs-2 col-sm-1">
														<span class="icon icon-success"></span>
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

											<a class="btn btn-primary pull-right"
												href="{if $backlink}{$backlink}
													{else}javascript: history.go(-1){/if}">
												{t}back{/t}
											</a>
										</div>
									</div>
								{/if}
							{block name="content"}
							{/block}
							</div>
						{/block}
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>

		{block name="js_include"}
			<script type="text/javascript" src="{$path_js}/jquery.min.js"></script>
			<script type="text/javascript" src="{$path_js}/jquery.cookie.js"></script>
			<script type="text/javascript" src="{$path_js}/bootstrap.min.js"></script>
			<script type="text/javascript" src="{$path_js}/toastr.min.js"></script>
			<script type="text/javascript" src="{$path_js}/administrator/main.js"></script>

		{literal}
			<script type="text/javascript">

				jQuery.fn.outerHtml = function() {
					return jQuery('<div />').append(this.eq(0).clone()).html();
				};

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
