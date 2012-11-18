<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>{block name=title}Installation KuwaSys{/block}</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="../installation/components/Kuwasys/templates/installationStylesheet.css" type="text/css" />
</head>

<body>
	<div class="header">
	{block name='header'}
	{/block}
	</div>
	{if isset($noticeStr)}
	<div class="notice">
	{foreach $noticeStr as $notice}
	{$notice}
	{/foreach}
	</div>
	{/if}
	{if isset($errorStr)}
	<div class="error">
	{foreach $errorStr as $error}
	{$error}
	{/foreach}
	</div>
	{/if}
	
	<br><br>
	<div class="main">
	{block name="main"}
	{/block}
	</div>
</body>
</html>
