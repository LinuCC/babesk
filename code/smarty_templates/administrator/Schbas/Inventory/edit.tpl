{extends file=$inh_path}{block name=content}

{$id = $inventory->getId()}
{if $inventory->getLending() && $inventory->getLending()->first()->getUser()}
	{$user = $inventory->getLending()->first()->getUser()}
	{$userFullName = $user->getForename()|cat:" "|cat:$user->getName()}
{else}
	{$user = false}
	{$userFullName = ''}
{/if}
{$book = $inventory->getBook()}

<h3 class='module-header'>Inventar ändern</h3>

<form role='form' method='post' class='form-horizontal'
	action='index.php?module=administrator|Schbas|Inventory&amp;id={$id}'>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">#</label>
			<div class="col-sm-10">
				<p class="form-control-static">{$id}</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">Buchtitel</label>
			<div class="col-sm-10">
				<p class="form-control-static">
					{if $book}{$book->getTitle()}{else}---{/if}
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">ISBN</label>
			<div class="col-sm-10">
				<p class="form-control-static">
					{if $book}{$book->getIsbn()}{else}---{/if}
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">Fach</label>
			<div class="col-sm-10">
				<p class="form-control-static">
					{if $book->getSubject()}
						{$book->getSubject()->getName()}
					{else}
						---
					{/if}
				</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">Verliehen an</label>
			<div class="col-sm-10">
				<p class="form-control-static">{$userFullName}</p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-2 control-label">Kaufsjahr</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-calendar"></span>
					</span>
					<input name="year-of-purchase" id="year-of-purchase"
						class="form-control" type="text" placeholder="Kaufsjahr"
						value="{$inventory->getYearOfPurchase()}" data-toggle="tooltip"
						title="Kaufsjahr" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Exemplarnummer</label>
			<div class="col-sm-10">
				<div class="input-group">
					<span class="input-group-addon">
						<span class="fa fa-list-ol"></span>
					</span>
					<input name="exemplar" id="exemplar" class="form-control" type="text"
						placeholder="Exemplarnummer" data-toggle="tooltip" required
						value="{$inventory->getExemplar()}" title="Exemplarnummer" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<input type="submit" class="btn btn-primary "
					value="Ändern" />
			</div>
		</div>
	</div>
</form>


{/block}