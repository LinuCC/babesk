{extends file=$inh_path} {block name="content"}

<h2 class="moduleHeader">Erlauben der Kursregistrierungen</h2>

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
	<form class="tableForm" action="index.php?module=administrator|Kuwasys|Classes|GlobalClassRegistration&amp;toggleFormSend" method="post">
		<div>
			<label for="toggleGlobalClassregistration">
				Kursregistrierungen generell erlauben
			</label>
			<input id="toggleGlobalClassregistration" type="checkbox"
				name="toggleGlobalClassregistration"
				{if $enabled}checked="checked"{/if}>
		</div>
		<div title="Erlaubt die Kursregistrierungen bei jedem einzelnen Kurs">
			<label for="activateIndividualClassregistrations">
				Für alle Kurse in diesem Schuljahr die Kursregistrierungen aktivieren <br />(Überschreibt bei Aktivierung ihre Änderungen falls sie welche gemacht haben)
			</label>
			<input id="activateIndividualClassregistrations" type="checkbox"
				name="activateIndividualClassregistrations"
				{if $enabled}checked="checked"{/if}>
		</div>
		<input type="submit" value="Absenden">
	</form>
</fieldset>

{/block}
