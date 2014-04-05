{extends file=$inh_path}


{block name=popup_dialogs}

<div classId="{$class.ID}" id="add-user-modal" class="modal fade" tabindex="-1"
	role="dialog" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
						&times;
					</button>
					{t}Please select the User and the Status of the Assignment{/t}
				</div>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group input-group" data-toggle="tooltip"
						title="Benutzername">
						<span class="input-group-addon">
							<span class="icon icon-user"></span>
						</span>
						<input type="text" name="username" id="username"
							class="form-control" placeholder="Benutzername suchen..." />
					</div>
					<div class="form-group input-group" data-toggle="tooltip"
						title="Benutzername">
						<span class="input-group-addon">
							<span class="icon icon-businesscard"></span>
						</span>
						<select name="status" id="status" class="form-control">
							{foreach $statuses as $status}
							<option value="{$status.ID}" >
								{$status.translatedName}
							</option>
							{/foreach}
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					{t}Cancel{/t}
				</button>
				<button id="add-user-submit" type="button" class="btn btn-primary">
					{t}Add the user to the class{/t}
				</button>
			</div>
		</div>
	</div>
</div>

<!-- <div id="addUserDialog" title="{t}Assign a User to this Class{/t}">
	<p>{t}Please select the User and the Status of the Assignment{/t}</p>
<form>
	<fieldset>
	<label for="username">{t}Username{/t}</label>
	<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" />
	<label for="status">{t}Status{/t}</label>
		<select name="status" id="status">
			{foreach $statuses as $status}
			<option value="{$status.ID}" >
				{$status.translatedName}
			</option>
			{/foreach}
		</select>
	</fieldset>
	</form>
</div> -->

{/block}


{block name="filling_content"}

<h2 class='moduleHeader'>Details des Kurses "{$class.label}"</h2>

<div class="container">
	<div class="col-lg-8 col-lg-offset-2">
		<table class="table table-responsive">
			<tr>
				<th>ID:</th>
				<td>{$class.ID}</td>
			</tr>
			<tr>
				<th>Name:</th>
				<td>{$class.label}</td>
			</tr>
			<tr>
				<th>Beschreibung:</th>
				<td>{$class.description}</td>
			</tr>
			<tr>
				<th>Maximale Registrierungen:</th>
				<td>{$class.maxRegistration}</td>
			</tr>
			<tr>
				<th>Aktiv:</th>
				<td>{if
					isset($class.activeCount)}{$class.activeCount}{else}---{/if}</td>
			</tr>
			<tr>
				<th>Wartend:</th>
				<td>{if isset($class.waitingCount)} {$class.waitingCount}
					{else}---{/if}</td>
			</tr>
			<tr>
				<th>Wunsch:</th>
				<td>{if (isset($class.request1Count) || isset($class.request2Count))} {$class.request1Count + $class.request2Count}
					{else}---{/if}</td>
			</tr>
			<tr>
				<th>Schüler-Registrierungen erlaubt:</th>
				<td>{if $class.registrationEnabled}<b>Ja</b>{else}<b>Nein</b>{/if}
				</td>
			</tr>
			<tr>
				<th>Veranstaltungstag:</th>
				<td>{if
					$class.unitTranslatedName}{$class.unitTranslatedName}{else}---{/if}</td>
			</tr>
		</table>
	</div>
</div>

<h3>Teilnehmer</h3>

<button class="btn btn-info pull-right" data-toggle="modal" data-target="#add-user-modal">
	{t}Assign a User to this Class{/t}
</button>

<table class="table table-striped table-responsive">
	<thead>
		<tr>
			<th>Name</th>
			<th>Art der Kurswahl</th>
			<th>Klasse</th>
			<th>Email-Adresse</th>
			<th>Telefonnummer</th>
			<th>Kurse desselben Tages</th>
		</tr>
	</thead>
	<tbody>
		{if isset($users) && count($users)} {foreach $users as $user}
		<tr>
			{$rowsOfSamePerson = 1}
			<td rowspan="{$rowsOfSamePerson}">
			<!-- Link to UserDetails -->
			<a href="index.php?section=Kuwasys|Users&action=showUserDetails&ID={$user.ID}">
				{$user.forename} {$user.name}
			</a>
			</td>
			<td rowspan="{$rowsOfSamePerson}">
				<!-- Link to "move user to another Class" -->
				<a href="index.php?section=Kuwasys|Users&amp;action=moveUserByClass&amp;classIdOld={$class.ID}&amp;userId={$user.ID}">
					{if $user.statusTranslated}
						{$user.statusTranslated}
					{else}Fehler!{/if}
				</a>
			</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.gradename}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.email}</td>
			<td rowspan="{$rowsOfSamePerson}">{$user.telephone}</td>
			<td>
				<ul class="other-classes-container">
					{foreach $user.classesOfSameDay as $cKey => $otherClass}
						<li>
							<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID={$otherClass.ID}">
								{$otherClass.label}
							</a>
						</li>
					{/foreach}
				</ul>
			</td>
			<td rowspan="{$rowsOfSamePerson}">
			<a class="btn btn-default btn-xs unregister-user"
			joinId="{$user.jointId}" href=>Abmelden</a></td>
		</tr>
		{/foreach}
		{/if}
	</tbody>
</table>


{/block}


{block name=style_include append}

<link rel="stylesheet" href="{$path_css}/administrator/Kuwasys/Classes/display-class-details.css" type="text/css" />
<link rel="stylesheet" href="{$path_js}/jquery-ui-smoothness.css" type="text/css" />

{/block}


{block name=js_include append}

<script type="text/javascript" src="{$path_js}/jquery-ui-1.10.4.only-autocomplete.min.js"></script>
<script type="text/javascript" src="{$path_js}/administrator/Kuwasys/Classes/display-class-details.js"></script>

{/block}