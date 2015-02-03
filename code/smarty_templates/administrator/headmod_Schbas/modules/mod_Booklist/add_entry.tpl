{extends file=$inh_path}{block name=content}

<h3 class="module-header">neues Buch hinzufügen</h3>

<form action="index.php?section=Schbas|Booklist&amp;action=4"
	class="form-horizontal" method="post">
	<div class="row">
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Fach</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-pencil"></span>
						</span>
						<input type="text" name="subject" class="form-control"
							maxlength="3">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Klasse</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-folder"></span>
						</span>
						<input type="text" name="class" class="form-control" maxlength="2">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Titel</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-book"></span>
						</span>
						<input type="text" name="title" class="form-control"
							maxlength="50">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Author</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-male"></span>
						</span>
						<input type="text" name="author" class="form-control"
							maxlength="30">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Verlag</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-newspaper-o"></span>
						</span>
						<input type="text" name="publisher" class="form-control"
							maxlength="30">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">ISBN</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-tags"></span>
						</span>
						<input type="text" name="isbn" class="form-control" maxlength="17">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Preis</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-money"></span>
						</span>
						<input type="text" name="price" class="form-control" maxlength="5">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="row form-group">
				<label class="col-sm-3 control-label">Bundle</label>
				<div class="col-sm-9">
					<div class="input-group">
						<span class="input-group-addon">
							<span class="fa fa-fw fa-bookmark"></span>
						</span>
						<input type="text" name="bundle" class="form-control"
							maxlength="1">
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
			<div class="row form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<input id="submit" type="submit" class="btn btn-primary"
						value="Hinzufügen">
				</div>
			</div>
		</div>
	</div>
</form>

{/block}