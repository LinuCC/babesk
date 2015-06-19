React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Input = require 'react-bootstrap/lib/Input'
Panel = require 'react-bootstrap/lib/Panel'
Table = require 'react-bootstrap/lib/Table'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
Cookies = require 'js-cookie'
NProgress = require 'nprogress'
Icon = require 'lib/FontAwesomeIcon'

module.exports = React.createClass(

  getInitialState: ->
    return {
      selectedSchoolyearId: false
    }

  getDefaultProps: ->
    return {
      schoolyears: []
      bookAssignments: []
      userId: false
    }

  componentWillReceiveProps: (newProps)->
    if newProps.schoolyears.length > 0
      if Cookies.get('bookAssignmentsSelectedSchoolyearId')?
        prevSelectedSy = parseInt(
          Cookies.get('bookAssignmentsSelectedSchoolyearId')
        )
        pos = lookupKeyOfObjectInArray(
          newProps.schoolyears, 'id', prevSelectedSy
        )
        if pos isnt false
          @setState selectedSchoolyearId: prevSelectedSy
      else
        activeSyArray = newProps.schoolyears.filter (s)-> s.active is true
        if activeSyArray.length
          @setState selectedSchoolyearId: activeSyArray[0].id
        else
          @setState selectedSchoolyearId: newProps.schoolyears[0].id

  handleSchoolyearSelectChange: (event, stuff)->
    schoolyearId = parseInt(event.target.value)
    @setState selectedSchoolyearId: schoolyearId
    Cookies.set 'bookAssignmentsSelectedSchoolyearId', schoolyearId

  handleBookAssignmentsGenerate: ->
    bootbox.confirm(
      "Wollen sie wirklich alle Buchzuweisungen neu generieren? Jetzige \
      Buchzuweisungen werden dabei geloescht. Die Zuweisungen werden nur fuer \
      das Schbas-Vorbeitungsschuljahr neu generiert.",
      (res)=>
        if res
          NProgress.start()
          $.ajax(
            method: 'POST'
            url: 'index.php?module=administrator|Schbas|BookAssignments|Generate'
            data:
              userId: @props.userId
          ) .done (res)=>
              NProgress.done()
              @props.refresh()
              toastr.success res
            .fail (jqxhr)->
              NProgress.done()
              toastr.error jqxhr.responseText, 'Fehler beim Erstellen der\
                Buchzuweisungen'
    )

  handleRemoveBookAssignment: (bookAssignmentId, event)->
    NProgress.start()
    $.ajax(
      method: 'POST'
      url: 'index.php?module=administrator|Schbas|BookAssignments|Delete'
      data:
        bookAssignmentId: bookAssignmentId
    ) .done (res)=>
        NProgress.done()
        @props.refresh()
      .fail (jqxhr)->
        NProgress.done()
        toastr.error jqxhr.responseText, 'Fehler beim loeschen der \
          Buchzuweisung'

  render: ->
    <Panel className='panel-dashboard' header={<h4>Buchzuweisungen</h4>}>
      <form className='form-horizontal'>
        <Col xs={2} md={6}>
          <Input type='select' label='Schuljahr' placeholder='---'
            labelClassName='col-xs-4' wrapperClassName='col-xs-8'
            onChange={@handleSchoolyearSelectChange}
            value={@state.selectedSchoolyearId} >
            {
              @props.schoolyears.map (schoolyear)->
                <option key={schoolyear.id} value={schoolyear.id}>
                  {schoolyear.label}
                </option>
            }
          </Input>
        </Col>
        <Col xs={2} md={6}>
          <Button bsStyle='default' className='pull-right'
            onClick={@handleBookAssignmentsGenerate}>
            Buchzuweisungen neu generieren
          </Button>
        </Col>
      </form>
      <Table striped bordered fill>
        <thead>
          <tr>
            <th>Buch</th>
            <th>Optionen</th>
          </tr>
        </thead>
        <tbody>
          {
            @props.bookAssignments.map (bookAssignment)=>
              if bookAssignment.schoolyear.id is @state.selectedSchoolyearId
                <tr key={bookAssignment.id}>
                  <td>{bookAssignment.book.title}</td>
                  <td>
                    <Button bsStyle='danger' bsSize='xsmall'
                      onClick={
                        @handleRemoveBookAssignment.bind(
                          null, bookAssignment.id
                        )
                      }>
                      <Icon name='trash-o' fixedWidth />
                    </Button>
                  </td>
                </tr>
          }
        </tbody>
      </Table>
    </Panel>

)