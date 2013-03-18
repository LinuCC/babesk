CKEDITOR.plugins.add( 'contractvars',
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
		        editor.ui.addButton( 'contractforename',
		        {
		            label: 'Platzhalter f&#252;r: Vorname',
		            command: 'forename',
		            icon: this.path + 'forename.png'
		        } );
		        editor.ui.addButton( 'contractname',
				        {
				            label: 'Platzhalter f&#252;r: Name',
				            command: 'name',
				            icon: this.path + 'name.png'
				        } );
		    }
		} ); 