{extends file=$inh_path} {block name="content"}

<h2 class='moduleHeader'>Details des Kurses "{$class.label}"</h2>

{literal}
<style type='text/css'  media='all'>
label {
	color: rgb(100,100,100);
	font-weight: bold;
	float:left;
}

.contentDiv {
	width: 400px;
	margin: 0 auto;
}

.valueDiv {
	float: right;
}
</style>
{/literal}

<div class="contentDiv">
	<div><label>ID:</label> <div class="valueDiv"> {$class.ID} </div></div><br>
	<div><label>Name:</label> <div class="valueDiv"> {$class.label} </div></div><br>
	<div><label>Maximale Registrierungen:</label> <div class="valueDiv"> {$class.maxRegistration} </div></div><br>
	<div><label>Aktiv:</label> <div class="valueDiv"> {if isset($class.sumStatus.active)}{$class.sumStatus.active}{else}---{/if}</div></div><br>
	<div><label>Wartend:</label> <div class="valueDiv"> {if isset($class.sumStatus.waiting)} {$class.sumStatus.waiting} {else}---{/if}</div></div><br>
	<div><label>Wunsch:</label> <div class="valueDiv"> {if isset($class.sumStatus.request)} {$class.sumStatus.request} {else}---{/if}</div></div><br>
	{if isset($class.users)}
	<div><label>Teilnehmer:</label>
		{foreach $class.users as $user}
			<a class="valueDiv"
			{if $user.status == 'active'} style="color: rgb(255, 50, 50);" 
			{else if $user.status == 'waiting'} style="color: rgb(50, 255, 50);" 
			{else if $user.status == 'request'} style="color: rgb(50, 50, 255);" {/if}
			 href="index.php?section=Kuwasys|Classes&action=changeLinkUserToClass&classId={$class.ID}&userId={$user.ID}">{$user.forename} {$user.name} -- {$user.status}</a><br>
		{/foreach}
	</div>
	{else}
	<div><label>Teilnehmer:</label> <div class="valueDiv"><b>keine Teilnehmer</b></div></div><br>
	{/if}
</div>

{/block}