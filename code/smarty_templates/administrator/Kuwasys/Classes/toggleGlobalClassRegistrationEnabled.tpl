{extends file=$inh_path} {block name="content"}

<h2 class="module-header">Erlauben der Kursregistrierungen</h2>

<fieldset class="smallContainer">
	<legend>Beschreibung</legend>
	<span class="functionalityDescription">
		<p>
			Hier können sie die Kursregistrierungen aktivieren / deaktivieren.
		</p>
		<p>
			Kurse müssen, damit Benutzer sich dafür registrieren können, auf zwei Weisen aktiviert werden:
		</p>
		<p>
			Es gibt für jeden einzelnen Kurs einen Schalter, mit dem man verhindern kann, dass Nutzer sich für einzelne Kurse registrieren können.
		</p>
		<p>
			Weiterhin gibt es einen globalen Schalter, der generell Kursregistrierungen erlaubt / nicht erlaubt.
		</p>
	</span>
</fieldset>

<fieldset class="smallContainer">
	<legend>Formular</legend>
	<form class="form-horizontal" role="form" method="post"
		action="index.php?module=administrator|Kuwasys|Classes|GlobalClassRegistration&amp;toggleFormSend">
		<div class="form-group">
			<label for="toggleGlobalClassregistration" class="col-sm-4">
				Kursregistrierungen generell erlauben
			</label>
			<input id="toggleGlobalClassregistration" type="checkbox"
				name="toggleGlobalClassregistration" data-on-text="Ja"
				data-off-text="Nein" data-on-color="success"
				data-off-color="danger" {if $enabled}checked="checked"{/if}>
		</div>
		<div class="form-group" title="Erlaubt die Kursregistrierungen bei jedem einzelnen Kurs">
			<label for="activateIndividualClassregistrations" class="col-sm-4">
				Für alle Kurse in diesem Schuljahr die Kursregistrierungen aktivieren <br />(Überschreibt bei Aktivierung ihre Änderungen falls sie welche gemacht haben)
			</label>
			<input id="activateIndividualClassregistrations" type="checkbox"
				name="activateIndividualClassregistrations" data-on-text="Ja"
				data-off-text="Nein" data-on-color="warning"
				data-off-color="default">
		</div>
		<input class="btn btn-primary" type="submit" value="Absenden">
	</form>
</fieldset>

{/block}


{block name=style_include append}
<link rel="stylesheet" href="{$path_css}/bootstrap-switch.min.css" type="text/css" />
{/block}


{block name=js_include append}
<script type="text/javascript" src="{$path_js}/vendor/bootstrap-switch.min.js">
</script>

<script>

$('#toggleGlobalClassregistration').bootstrapSwitch();
$('#activateIndividualClassregistrations').bootstrapSwitch();

</script>
{/block}