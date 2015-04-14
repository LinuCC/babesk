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
Deadlines = require './Deadlines'

PreparationPanel = React.createClass(
  getInitialState: ->
    return {
      prepSchoolyear:
        active:
          id: 0
          name: "???"
          entriesExist: false
        alternatives: []
      schbasClaimStatus: false
    }

  componentDidMount: ->
    $.getJSON 'index.php?module=administrator|Schbas|Dashboard|Preparation'
      .done (res)=> if @isMounted() then @setState(res)
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler beim Abrufen der Daten'

  handleSchoolyearChange: (schoolyearId)->
    bootbox.confirm(
      'Wollen sie das Vorbereitungs-Schuljahr wirklich wechseln?'
      (res)=>
        if res
          $.get(
            'index.php?module=administrator|Schbas|Dashboard|Preparation|\
            Schoolyear',
            {schoolyearId: schoolyearId, action: 'change'}
          ).done (res)=>
              # put the active into alternatives and the alternative with the
              # schoolyearId into active
              prepSy = @state.prepSchoolyear
              oldActive = prepSy.active
              newActive = prepSy.alternatives.filter (sy)->
                sy.id is schoolyearId
              if newActive.length is 1
                prepSy.active = newActive[0]
              else
                toastr.error 'Fehler beim Wechseln des Schuljahres'
                return false
              prepSy.alternatives.push oldActive
              # Remove the now active alternative from alternatives
              prepSy.alternatives = prepSy.alternatives.filter(
                (sy)-> sy.id isnt schoolyearId
              )
              @setState(prepSchoolyear: prepSy)
            .fail (jqxhr)->
              toastr.error jqxhr.responseText, 'Fehler'
    )

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

  handleSchbasClaimStatusChanged: (status)->
    bootbox.confirm(
      'Wollen sie den Rückmeldeformular-Status wirklich verändern?'
      (res)=>
        if res then @setState(schbasClaimStatus: status)
        toastr.error 'Leider noch nicht implementiert'
    )

  render: ->
    <div>
      {###Single buttons need to be wrapped with their own ButtonGroup###}
      <ButtonGroup justified>
        <PrepSchoolyear schoolyears={@state.prepSchoolyear}
          handleSchoolyearChange={@handleSchoolyearChange}>
        </PrepSchoolyear>
        <AssignmentController prepSchoolyear={@state.prepSchoolyear.active}
          handleEdit={@handleEditAssignments}
          handleGenerate={@handleGenerateAssignments}
          handleDelete={@handleDeleteAssignments}>
        </AssignmentController>
        <ButtonGroup>
          <SchbasClaimStatus status={@state.schbasClaimStatus}
            handleStatusChanged={@handleSchbasClaimStatusChanged} />
        </ButtonGroup>
      </ButtonGroup>
      <hr />
      <Deadlines />
    </div>
)


module.exports = PreparationPanel