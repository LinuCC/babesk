{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	Den Schülern ihre Kurse zuweisen
</h2>

<fieldset class="smallContainer">
	<legend>{t}Actions{/t}</legend>
	<ul class="submodulelinkList">

		{if $tableExists}
		<li>
			<a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Overview">
				{t}Go to the existing Assignment-Process{/t}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>

<fieldset class="smallContainer">
	<legend>{t}Bulk-Actions{/t}</legend>
	<ul class="submodulelinkList">
		<li>
			<a id="resetAssignment" href="#">
				{t}Start a new Assignment-Process{/t}
			</a>
		</li>
		{if $tableExists}
		<li>
			<a id="applyAssignment" href="#">
				{t}Assign all Users to their Classes{/t}
			</a>
		</li>
		{/if}
	</ul>
</fieldset>


<div id="confirmReset" title="{t}Really start a new Assignment-Process?{/t}">
  <p>{t}If you have already started an Assignment-Process, the Data will be inevitably lost. Are you sure?{/t}</p>
</div>

<div id="confirmAssignment" title="Zuweisungen durchführen?">
  <p>Wenn sie die Zuweisungen durchführen, werden die in diesem Modul Temporär durchgeführten Veränderungen auf die Nutzter angewendet und die Nutzer sind dann offiziell in den hier zugewiesenen Kursen. Kleine Veränderungen können aber auch im Kurs-Modul nachher noch durchgeführt werden.</p>
</div>


<script src="../smarty/templates/administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/mainmenu.js">
</script>


{/block}
