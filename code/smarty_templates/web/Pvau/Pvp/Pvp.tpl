{extends file=$inh_path}{block name=content}

<div align="center"><h3>Pers&ouml;nlicher Vertretungsplan*</h3></div>
<br>
{$planheute}<br>
{$planmorgen}<br>
<div class="panel panel-primary">
	<div class="panel-body">
		<form class="form-inline" action="index.php?section=PVau|Pvp"
			method="post">
				<label for="search">Suchfilter:</label>
				<div class="form-group">
					<div class="input-group">
						<div class="input-group-addon">
							<span class="fa fa-search fa-fw"></span>
						</div>
						<input id="search" class="form-control" type="text" name="search"
							value="{$searchterm}" />
					</div>
				</div>
				<input class="btn btn-primary" type="submit" value="Best&auml;tigen" />
		</form>
	</div>
	<div class="panel-footer">
		<p>*ohne Gew&auml;hr!</p>
		<p>
			Es k&ouml;nnen Lehrerk&uuml;rzel sowie Klassen- und Kursbezeichnungen eingegeben werden, nach denen gesucht werden soll. Gro&szlig;- und Kleinschreibung ist zu beachten! Mehrere Suchbegriffe k&ouml;nnen durch Leerzeichen getrennt eingegeben werden.
		</p>
	</div>
</div>
{/block}


{block name=js_include append}

<script type="text/javascript">

$(document).ready(function() {
	//Make the table better to read
	$('table').addClass('table table-responsive table-striped');
	$('td').attr('align', 'left');
});

</script>

{/block}