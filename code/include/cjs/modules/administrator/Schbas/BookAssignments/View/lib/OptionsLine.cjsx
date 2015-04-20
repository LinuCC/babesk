React = require 'react'
React.Bootstrap = require 'react-bootstrap'
OptionAddAssignmentLine = require './OptionAddAssignmentLine'
Button = React.Bootstrap.Button
DropdownButton = React.Bootstrap.DropdownButton
MenuItem = React.Bootstrap.MenuItem
Row = React.Bootstrap.Row
ListGroupItem = React.Bootstrap.ListGroupItem
ListGroup = React.Bootstrap.ListGroup

module.exports = React.createClass(
  getInitialState: ->
    return {
      showDialog: false
    }

  getDefaultProps: ->
    return {
      onAssignmentsChanged: -> {}
      handleChangeSchoolyear: -> {}
      schoolyears: []
    }

  handleAddAssignmentsClicked: ->
    @setState showDialog: not @state.showDialog
    console.log @state

  handleSchoolyearSelect: (schoolyearId)->
    @props.handleChangeSchoolyear schoolyearId

  render: ->
    selSchoolyear = $.grep(@props.schoolyears, (sy)-> return sy.active)
    if selSchoolyear[0]? then selSchoolyear = selSchoolyear[0]
    schoolyearBtnTitle = "Für Schuljahr #{selSchoolyear.name}"
    <ListGroup>
      <ListGroupItem>
        <Button bsStyle='primary' onClick={@handleAddAssignmentsClicked}>
          {
            if not @state.showDialog
              'Zuweisung hinzufügen...'
            else
              'Zuweisung abbrechen'
          }
        </Button>
        <DropdownButton bsStyle='default' title={schoolyearBtnTitle}
          className='pull-right' onSelect={@handleSchoolyearSelect}>
          {
            @props.schoolyears.map (schoolyear)=>
              if not schoolyear.active
                <MenuItem eventKey={schoolyear.id} key={schoolyear.id}>
                  {schoolyear.name}
                </MenuItem>
          }
        </DropdownButton>
      </ListGroupItem>
      {
        if @state.showDialog
          <OptionAddAssignmentLine schoolyear={selSchoolyear}
            onAssignmentsChanged={@props.onAssignmentsChanged} />
      }
    </ListGroup>
)