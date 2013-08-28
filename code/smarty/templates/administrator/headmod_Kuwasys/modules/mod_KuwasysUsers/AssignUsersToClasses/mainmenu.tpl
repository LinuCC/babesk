{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	Den Schülern ihre Kurse zuweisen
</h2>

<fieldset class="smallContainer">
	<legend>{_g('Actions')}</legend>
	<ul class="submodulelinkList">

		{if $tableExists}
		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Overview">
				{_g('Go to the existing Assignment-Process')}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>{_g('Bulk-Actions')}</legend>
	<ul class="submodulelinkList">
		<li>
			<a id="resetAssignment" href="#">
				{_g('Start a new Assignment-Process')}
			</a>
		</li>
		{if $tableExists}
		<li>
			<a id="applyAssignment" href="#">
				{_g('Assign all Users to their Classes')}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>


<div id="confirmReset" title="{_g('Really start a new Assignment-Process?')}">
  <p>{_g('If you have already started an Assignment-Process, the Data will be inevitably lost. Are you sure?')}</p>
</div>

<div id="confirmAssignment" title="Zuweisungen durchführen?">
  <p>Wenn sie die Zuweisungen durchführen, werden die in diesem Modul Temporär durchgeführten Veränderungen auf die Nutzter angewendet und die Nutzer sind dann offiziell in den hier zugewiesenen Kursen. Kleine Veränderungen können aber auch im Kurs-Modul nachher noch durchgeführt werden.</p>
</div>


<script src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/mainmenu.js">
</script>


{/block}
