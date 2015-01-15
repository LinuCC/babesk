{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}Update users and change the schoolyear{/t}</h2>

<div class="panel panel-default">
	<div class="panel-body">
		Hier können sie das Schuljahr wechseln und dabei die Schülerdaten aktualisieren.<br>
		Der Prozess benötigt eine Datei mit den aktuellen Schülerdaten (Vorname, Nachname, Klasse, vielleicht noch weitere).<br>
		Der Aufbau der Datei ist <a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession">hier</a> beschrieben.
	</div>
</div>

<p class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<b>Existiert ein Aktualisierungsprozess</b> bereits, so wird er überschrieben.
</p>

<div class="clearfix">
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post">
		<input class="btn btn-warning pull-left" type="submit"
			value="Mit neuem Prozess starten" name="schoolyearSelect">
	</form>
	<form action="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange|NewSession" method="post" >
		<input type="submit" class="btn btn-default pull-left"
			value="Hilfe zur Datei" name="csvHelp" style="margin-left: 20px">
	</form>
	<a href="index.php?module=administrator|System|User|UserUpdateWithSchoolyearChange" class="btn btn-default pull-right">
		Abbrechen
	</a>
</div>
{/block}