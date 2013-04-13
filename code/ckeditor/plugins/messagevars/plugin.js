CKEDITOR.plugins.add( 'messagevars',
		{
			init: function( editor )
			{
				editor.addCommand( 'forename',
					{
						exec : function( editor )
						{
								editor.insertHtml( "{vorname}" );
						}
					});
				editor.addCommand( 'name',
					{
						exec : function( editor )
						{
								editor.insertHtml( "{name}" );
						}
					});
				editor.addCommand( 'messageBarcode',
					{
						exec:function(editor)
						{
							editor.insertHtml("{barcode}");
						}
					});
				editor.ui.addButton( 'messageForename',
					{
						label: 'Platzhalter f&#252;r: Vorname',
						command: 'forename',
						icon: this.path + 'forename.png'
					});
				editor.ui.addButton( 'messageName',
					{
						label: 'Platzhalter f&#252;r: Name',
						command: 'name',
						icon: this.path + 'name.png'
					});
			}
		} );