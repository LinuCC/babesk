React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Input = React.Bootstrap.Input
Table = React.Bootstrap.Table
Button = React.Bootstrap.Button
classnames = require 'classnames'
StatusIcon = require('lib/StatusIcon.js').StatusIcon

AssignmentBox = React.createClass(
  getInitialState: ->
    return {
      existingAssignmentsAction: 'add-to-existing'
      addGradelevelToUsers: true
    }

  handleSubmit: (event)->
    $.ajax
      type: 'POST'
      url: 'index.php?module=administrator|Schbas|BookAssignments|Generate'
      data: @state
      dataType: 'json'
      success: (data, statusText, jqXHR)->
        toastr.success 'Die Zuweisungen wurden erfolgreich generiert.'
      error: (jqXHR, statusText, errorThrown)->
        toastr.error 'Ein Fehler ist bei der Bearbeitung aufgetreten.'
        console.log jqXHR

  handleExistingAssignmentsAction: (event)->
    @setState(existingAssignmentsAction: event.target.value)

  handleAddGradelevelChange: (event)->
    @setState(addGradelevelToUsers: event.target.checked)

  render: ->
    <div className="panel panel-default">
      <div className="panel-heading">
        <h3 className="panel-title">
          Automatische Zuweisungen der Buchausleihen
        </h3>
      </div>
      <ul className="list-group checklist">
        <AssignmentSchoolyearsLine
          assignmentsExist={@props.data.assignmentsForSchoolyearExist}
          handleExistingAssignmentsAction={@handleExistingAssignmentsAction}
          selectedValue={@state.existingAssignmentsAction} />
        <AddGradelevelLine isChecked={@state.addGradelevelToUsers}
          handleAddGradelevelChange={@handleAddGradelevelChange} />
      </ul>
      <div className="panel-heading">
        <i className="fa fa-list fa-fw fa-lg"></i>&nbsp;&nbsp;
        Vorschau der Zuweisungen der Bücher zu Schülern bestimmter
        Klassenstufen
      </div>
      <AssignmentsTable entries={@props.data.bookAssignmentsForGrades} />
      <div className='panel-footer'>
        <ActionFooter handleSubmit={@handleSubmit} />
      </div>
    </div>
)

AssignmentSchoolyearsLine = React.createClass(
  render: ->
    classes = classnames(
      'list-group-item': true
      'list-group-item-success': not @props.assignmentsExist
      'list-group-item-warning': @props.assignmentsExist
    )
    iconStatus = if @props.assignmentsExist then 'warning' else 'success'
    <li className={classes}>
      <StatusIcon status={iconStatus} />
      {
        if @props.assignmentsExist
          <div className="form-inline">
            <p>
              Zuweisungen für dieses Schuljahr sind bereits vorhanden.
              Was soll gemacht werden?
            </p>
            <Input type="select" standalone value={@props.selectedValue}
              onChange={@props.handleExistingAssignmentsAction}>
              <option value='delete-existing'>
                Lösche existierende Zuweisungen für dieses Schuljahr.
              </option>
              <option value='add-to-existing'>
                Füge die automatisch generierten Zuweisungen hinzu.
              </option>
            </Input>
          </div>
        else
          <p>Keine Zuweisungen für dieses Schuljahr vorhanden.</p>
      }
      <div className="clearfix"></div>
    </li>
)

AddGradelevelLine = React.createClass(
  render: ->
    <li className='list-group-item'>
      <i className="fa fa-3x fa-fw pull-left fa-question-circle"></i>
      <p>
        Steigen die Schüler währenddessen normalerweise eine Klassenstufe auf?
        <Input type="checkbox" label="Ja, eine Stufe" standalone
        onChange={@props.handleAddGradelevelChange} checked={@props.isChecked}>
        </Input>
      </p>
      <div className="clearfix"></div>
    </li>
)

AssignmentsTable = React.createClass(
  render: ->
    <Table bordered>
      <thead>
        <tr>
          <th>Klassenstufe</th>
          <th>Bücher</th>
        </tr>
      </thead>
      <tbody>
        {
          @props.entries.map(
            (entry)->
              <AssignmentsTableGradeEntry gradelevel={entry.gradelevel}
                books={entry.books} />
            , @
          )
        }
      </tbody>
    </Table>
)

AssignmentsTableGradeEntry = React.createClass(
  render: ->
    # Gradelevel-column spans multiple rows, only needs to be defined for the
    # first row
    [firstBook, restBooks...] = @props.books
    if not restBooks? then restBooks = []
    # React needs a single point of entrance for the DOM, so we just use
    # tbody to group the rows of a gradelevel together
    <tbody>
      <tr>
        <td rowSpan={@props.books.length}>{@props.gradelevel}</td>
        <td><a href={firstBook.link}>{firstBook.name}</a></td>
      </tr>
      {
        restBooks.map(
          (book)->
            <tr>
              <td><a href={book.link}>{book.name}</a></td>
            </tr>
          , @
        )
      }
    </tbody>
)

ActionFooter = React.createClass(
  render: ->
    <div>
      <Button className='pull-right' bsStyle='primary'
        onClick={@props.handleSubmit}>
        Zuweisungen generieren
      </Button>
      <Button className='pull-left' bsStyle='default'
        href='index.php?module=administrator|Schbas|BookAssignments'>
        Abbrechen
      </Button>
      <div className='clearfix'></div>
    </div>
)

exports.AssignmentBox = AssignmentBox