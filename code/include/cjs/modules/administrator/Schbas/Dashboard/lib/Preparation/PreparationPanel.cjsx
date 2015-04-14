React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Grid = React.Bootstrap.Grid
Row = React.Bootstrap.Row
Col = React.Bootstrap.Col
ButtonGroup = React.Bootstrap.ButtonGroup
ButtonToolbar = React.Bootstrap.ButtonToolbar
PrepSchoolyear = require './PrepSchoolyear'
AssignmentController = require './AssignmentController'
SchbasClaimStatus = require './SchbasClaimStatus'

PreparationPanel = React.createClass(
  getInitialState: ->
    return {
      prepSchoolyear:
        active:
          id: 7
          name: "14/15"
          entriesExist: true
        alternatives: [
          {
            id: 4
            name: "15/16"
            entriesExist: false
          }
          {
            id: 5
            name: "12/13"
            entriesExist: true
          }
          {
            id: 6
            name: "13/14"
            entriesExist: true
          }
        ]
    }

  handleSchoolyearChange: (schoolyearId)->
    toastr.error 'Wechseln des Schuljahres ist noch nicht implementiert'
    console.log schoolyearId

  handleEditAssignments: ->
    toastr.error 'Editieren ist noch nicht implementiert'
    # window.location = 'index.php?module=administrator|Schbas|BookAssignments|\
      # Edit'

  handleGenerateAssignments: ->
    window.location = 'index.php?module=administrator|Schbas|BookAssignments|\
      Generate'

  handleDeleteAssignments: ->
    toastr.error 'Löschen ist noch nicht implementiert'
    return

  render: ->
    # Single buttons need to be wrapped with their own ButtonGroup
    <ButtonGroup justified>
      <PrepSchoolyear schoolyears={@state.prepSchoolyear}
        handleSchoolyearChange={@handleSchoolyearChange}>
      </PrepSchoolyear>
      <ButtonGroup>
        <SchbasClaimStatus />
      </ButtonGroup>
      <AssignmentController prepSchoolyear={@state.prepSchoolyear.active}
        handleEdit={@handleEditAssignments}
        handleGenerate={@handleGenerateAssignments}
        handleDelete={@handleDeleteAssignments}>
      </AssignmentController>
    </ButtonGroup>
)


module.exports = PreparationPanel