{extends file=$inh_path}


{block name=popup_dialogs}

<div classId="{$class->getID()}" {*data-category-id="{$class.categoryId}"*}
	id="add-user-modal" class="modal fade" tabindex="-1" role="dialog"
	aria-hidden="true" >
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
						title="Status">
						<span class="input-group-addon">
							<span class="icon icon-businesscard"></span>
						</span>
						<select name="status" class="form-control">
							{foreach $statuses as $status}
							<option value="{$status.ID}" >
								{$status.translatedName}
							</option>
							{/foreach}
						</select>
					</div>
					<div class="form-group input-group" data-toggle="tooltip"
						title="Kategorie">
						<span class="input-group-addon">
							<span class="icon icon-clock"></span>
						</span>
						<select name="category" class="form-control">
							{foreach $class->getCategories() as $category}
								<option value="{$category->getID()}">
									{$category->getTranslatedName()}
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

<div classId="{$class->getID()}" id="change-user-modal" class="modal fade"
	tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-title">
					<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">
						&times;
					</button>
					Kursregistrierung für Benutzer <span class="username"></span> ändern
				</div>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group input-group" data-toggle="tooltip"
						title="Status des Benutzers">
						<span class="input-group-addon">
							<span class="icon icon-businesscard"></span>
						</span>
						<select name="status" class="form-control">
							{foreach $statuses as $status}
								<option value="{$status.ID}" >
									{$status.translatedName}
								</option>
							{/foreach}
						</select>
					</div>
						<label>In einen anderen Kurs verschieben?</label>
						<input type="checkbox" id="change-user-move-class-switch"
						name="change-user-move-class-switch"
						data-on-text="Ja" data-off-text="Nein"
						data-on-color="info" data-off-color="default"/>
					<div id="change-user-move-class-selector-container" hidden="true">
						<div class="form-group input-group" data-toggle="tooltip" title="Kurs zu dem der Benutzer verschoben werden soll">
							<span class="input-group-addon">
								<span class="icon icon-listelements"></span>
							</span>
							<select name="classes" class="form-control">
							</select>
						</div>
						<a class="btn btn-info" data-toggle="popover" href="#a"
							title="Schüler in einen Kurs an einem anderen Tag verschieben"
							data-content="Um den Schüler in einen Kurs zu verschieben, der an einem anderen Tag liegt, melden sie den Schüler in diesem Kurs ab und fügen sie den Schüler dem anderen Kurs hinzu.">
							Kurse an anderen Tagen
						</a>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					Abbrechen
				</button>
				<button id="change-user-submit" type="button" class="btn btn-primary">
					Kursregistrierung verändern
				</button>
			</div>
		</div>
	</div>
</div>

{/block}


{block name="filling_content"}

<h2 class='module-header'>Details des Kurses "{$class->getLabel()}"</h2>

<div class="container">
	<div class="col-lg-8 col-lg-offset-2">
		<table class="table table-responsive">
			<tr>
				<th>ID:</th>
				<td>{$class->getID()}</td>
			</tr>
			<tr>
				<th>Name:</th>
				<td>{$class->getLabel()}</td>
			</tr>
			<tr>
				<th>Beschreibung:</th>
				<td>{$class->getDescription()}</td>
			</tr>
			<tr class="class-schoolyear"
				schoolyearid="{$class->getSchoolyear()->getID()}">
				<th>Schuljahr:</th>
				<td>{$class->getSchoolyear()->getLabel()}</td>
			</tr>
			<tr>
				<th>Maximale Registrierungen:</th>
				<td>{$class->getMaxRegistration()}</td>
			</tr>
			<tr>
				<th>Aktiv:</th>
				<td>
					{$activeUserChoicesCount = 0}
					{foreach $class->getUsersInClassesAndCategories() as $userChoice}
						{if $userChoice->getStatus()->getName() == "active"}
							{$activeUserChoicesCount = $activeUserChoicesCount + 1}
						{/if}
					{/foreach}
					{if $activeUserChoicesCount}{$activeUserChoicesCount}{else}---{/if}
				</td>
			</tr>
			<tr>
				<th>Wartend:</th>
				<td>
					{$waitingUserChoicesCount = 0}
					{foreach $class->getUsersInClassesAndCategories() as $userChoice}
						{if $userChoice->getStatus()->getName() == "waiting"}
							{$waitingUserChoicesCount = $waitingUserChoicesCount + 1}
						{/if}
					{/foreach}
					{if $waitingUserChoicesCount}{$waitingUserChoicesCount}{else}---{/if}
				</td>
			</tr>
			<tr>
				<th>Wunsch:</th>
				<td>
					{$requestingUserChoicesCount = 0}
					{foreach $class->getUsersInClassesAndCategories() as $userChoice}
						{if $userChoice->getStatus()->getName() == "request1" ||
								$userChoice->getStatus()->getName() == "request2"}
							{$requestingUserChoicesCount = $requestingUserChoicesCount + 1}
						{/if}
					{/foreach}
					{if $requestingUserChoicesCount}
						{$requestingUserChoicesCount}
					{else}
						---
					{/if}
				</td>
			</tr>
			<tr>
				<th>Schüler-Registrierungen erlaubt:</th>
				<td>
					{if $class->getRegistrationEnabled()}
						<b>Ja</b>
					{else}
						<b>Nein</b>
					{/if}
				</td>
			</tr>
			<tr>
				<th>Ist Optional:</th>
				<td>{if $class->getIsOptional()}Ja{else}Nein{/if}
				</td>
			</tr>
			{* TODO: categoryid not useful anymore since there possibly are multiple categories (its just for not many classes) *}
			<tr class="class-category" categoryid="{$class->getCategories()->first()->getID()}">
				<th>Veranstaltungstag:</th>
				<td>
					{foreach from=$class->getCategories() item=category name=catLoop}
						{$category->getTranslatedName()}{if $smarty.foreach.catLoop.last != true}, {/if}
					{/foreach}
				</td>
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
			<th>Klasse</th>
			<th>Email-Adresse</th>
			<th>Telefonnummer</th>
			<th>Kategorie</th>
			<th>Art der Kurswahl</th>
			<th>Kurse desselben Tages</th>
			<th>Optionen</th>
		</tr>
	</thead>
	<tbody>
		{if isset($users) && count($users)}
		{foreach $users as $user}

			{* The first link has to be written in the same row for better
			 * user-experience
			 *}
			{$userClassLink = $user->getUsersInClassesAndCategories()->first()}
			{$rowsOfSamePerson = count($user->getUsersInClassesAndCategories())}

			<tr joinId="{$userClassLink->getID()}" >
				<td class="username" rowspan="{$rowsOfSamePerson}">
				<!-- Link to UserDetails -->
				<a href="index.php?module=administrator|System|User|DisplayChange&amp;ID={$user->getID()}">
					{$user->getForename()} {$user->getName()}
				</a>
				</td>
				{$grade = $user->getUsersInGradesAndSchoolyears()->first()->getGrade()}
				<td rowspan="{$rowsOfSamePerson}">
				{if $grade}
					{$grade->getGradelevel()} {$grade->getLabel()}
				{else}
					---
				{/if}
				</td>
				<td rowspan="{$rowsOfSamePerson}">{$user->getEmail()}</td>
				<td rowspan="{$rowsOfSamePerson}">{$user->getTelephone()}</td>
				{* Following is the first row of the possible multi-row data.
				 * The user can vote for multiple days at the same time, so we need
				 * to differ those votes.
				 *}
				<td>
					{$userClassLink->getCategory()->getTranslatedName()}
				</td>
				{$status = $userClassLink->getStatus()}
				<td class="user-status" statusid="{$status->getID()}" >
					{if $status->getTranslatedName()}
						{$status->getTranslatedName()}
					{else}Fehler!{/if}
				</td>
				<td>
					{*
					<ul class="other-classes-container">
						{foreach $user->classesOfSameDay as $cKey => $otherClass}
							<li>
								<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID={$otherClass.ID}">
									{$otherClass.label}
								</a>
							</li>
						{/foreach}
					</ul>
					*}
					disabled
				</td>
				<td class="user-actions">
					<div class="btn-group">
						<button class="btn btn-default btn-xs change-user"
						joinId="{$userClassLink->getID()}">
							ändern
						</button>
						<button class="btn btn-danger btn-xs unregister-user"
						joinId="{$userClassLink->getID()}">
							Abmelden
						</button>
					</div>
				</td>
			</tr>
			{if count($user->getUsersInClassesAndCategories())}
				{foreach from=$user->getUsersInClassesAndCategories()
					item=userClassLink name=linkLoop}
					{if $smarty.foreach.linkLoop.first}{continue}{/if}

					<tr joinId="{$userClassLink->getID()}">
						<td>
							{$userClassLink->getCategory()->getTranslatedName()}
						</td>
						{$status = $userClassLink->getStatus()}
						<td class="user-status" statusid="{$status->getID()}">
							{if $status->getTranslatedName()}
								{$status->getTranslatedName()}
							{else}Fehler!{/if}
						</td>
						<td>
							{*
							<ul class="other-classes-container">
								{foreach $user->classesOfSameDay as $cKey => $otherClass}
									<li>
										<a href="index.php?module=administrator|Kuwasys|Classes|DisplayClassDetails&amp;ID={$otherClass.ID}">
											{$otherClass.label}
										</a>
									</li>
								{/foreach}
							</ul>
							*}
							disabled
						</td>
						<td class="user-actions">
							<div class="btn-group">
								<button class="btn btn-default btn-xs change-user"
								joinId="{$userClassLink->getID()}">
									ändern
								</button>
								<button class="btn btn-danger btn-xs unregister-user"
								joinId="{$userClassLink->getID()}">
									Abmelden
								</button>
							</div>
						</td>
					</tr>
				{/foreach}
			{/if}
		{/foreach}
		{/if}
	</tbody>
</table>
{*
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
		<tr joinId="{$user.jointId}" >
			{$rowsOfSamePerson = 1}
			<td class="username" rowspan="{$rowsOfSamePerson}">
			<!-- Link to UserDetails -->
			<a href="index.php?module=administrator|System|User|DisplayChange&amp;ID={$user.ID}">
				{$user.forename} {$user.name}
			</a>
			</td>
			<td class="user-status" rowspan="{$rowsOfSamePerson}"
				statusid="{$user.statusId}" >
				{if $user.statusTranslated}
					{$user.statusTranslated}
				{else}Fehler!{/if}
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
			<td class="user-actions" rowspan="{$rowsOfSamePerson}">
				<div class="btn-group">
					<button class="btn btn-default btn-xs change-user"
					joinId="{$user.jointId}">
						ändern
					</button>
					<button class="btn btn-danger btn-xs unregister-user"
					joinId="{$user.jointId}">
						Abmelden
					</button>
				</div>
			</td>
		</tr>
		{/foreach}
		{/if}
	</tbody>
</table>
*}

{/block}


{block name=style_include append}

<link rel="stylesheet" href="{$path_css}/administrator/Kuwasys/Classes/display-class-details.css" type="text/css" />
<link rel="stylesheet" href="{$path_js}/jquery-ui-smoothness.css" type="text/css" />
<link rel="stylesheet" href="{$path_css}/bootstrap-switch.min.css" type="text/css" />

{/block}


{block name=js_include append}

<script type="text/javascript">
	var statuses = {json_encode($statuses)};
</script>

<script type="text/javascript" src="{$path_js}/jquery-ui-1.10.4.only-autocomplete.min.js"></script>
<script type="text/javascript" src="{$path_js}/bootbox.min.js"></script>
<script type="text/javascript" src="{$path_js}/administrator/Kuwasys/Classes/display-class-details.js"></script>
<script type="text/javascript" src="{$path_js}/bootstrap-switch.min.js"></script>

{/block}