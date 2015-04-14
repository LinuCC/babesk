React = require 'react'
React.Bootstrap = require 'react-bootstrap'
DropdownButton = React.Bootstrap.DropdownButton
MenuItem = React.Bootstrap.MenuItem

AssignmentController = React.createClass(

  render: ->
    if @props.prepSchoolyear.entriesExist
      style = 'info'
      title = 'Zuweisungen existieren'
    else
      style = 'danger'
      title = 'Noch keine Zuweisungen'
    <DropdownButton bsStyle={style} title={title} pullRight>
      <MenuItem eventKey='edit' key='edit' onClick={@props.handleEdit}>
        Buchzuweisungen bearbeiten
      </MenuItem>
      <MenuItem divider />
      <MenuItem eventKey='generate' key='generate'
        onClick={@props.handleGenerate}>
        Buchzuweisungen automatisch generieren
      </MenuItem>
      {
        if @props.prepSchoolyear.entriesExist
          <MenuItem eventKey='delete' key='delete'
            onClick={@props.handleDelete}>
            Buchzuweisungen löschen
          </MenuItem>
      }
    </DropdownButton>
)

module.exports = AssignmentController