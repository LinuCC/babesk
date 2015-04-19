React = require 'react'
React.Bootstrap = require 'react-bootstrap'
BooksToUsersAssignments = require './lib/BooksToUsersAssignments'
OptionsLine = require './lib/OptionsLine'
Panel = React.Bootstrap.Panel

AssignmentsBox = React.createClass(
  getInitialState: ->
    return {
      schoolyears: []
      books: []
    }

  componentDidMount: ->
    @updateData()

  updateData: ->
    activeSy = $.grep @state.schoolyears, (sy)-> return sy.active
    if activeSy[0]? then activeSy = activeSy[0]
    $.getJSON(
      'index.php?module=administrator|Schbas|BookAssignments|View',
      {'jsonData': true, 'schoolyearId': activeSy.id}
    ).done (res)=> if @isMounted() then @setState res
      .fail (jqxhr)-> toastr.error jqxhr.responseText, 'Fehler'

  deleteAssignments: (data)->
    $.get(
      'index.php?module=administrator|Schbas|BookAssignments|View|Delete'
      data
    ).done (res)=>
        toastr.success res, 'Erfolgreich gelöscht'
        @updateData()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler'

  handleChangeSchoolyear: (schoolyearId)->
    newSchoolyears = @state.schoolyears
    activeSyIndex = newSchoolyears.map((e)-> return e.active).indexOf(true);
    newActiveIndex = newSchoolyears.map((e)->
        return e.id == schoolyearId
      ).indexOf(true);
    newSchoolyears[activeSyIndex]['active'] = false
    newSchoolyears[newActiveIndex]['active'] = true
    @setState schoolyears: newSchoolyears
    @updateData()


  handleGradelevelOfBookAssignmentsDelete: (book, gradelevel)->
    that = this
    bootbox.confirm(
      "Wollen sie alle Buchzuweisungen des Buchs #{book.name} für den \
      Jahrgang #{gradelevel} wirklich löschen?",
      (res)->
        if res
          data = {
            deleteEntity: 'gradelevel', entityId: gradelevel, bookId: book.id
          }
          that.deleteAssignments data
    )

  handleGradeOfBookAssignmentsDelete: (book, gradeId, gradeName)->
    that = this
    bootbox.confirm(
      "Wollen sie alle Buchzuweisungen des Buchs #{book.name} für die \
      Klasse #{gradeName} wirklich löschen?",
      (res)->
        if res
          data = {
            deleteEntity: 'grade', entityId: gradeId, bookId: book.id
          }
          that.deleteAssignments data
    )


  handleBookAssignmentsDelete: (book, gradelevel)->
    that = this
    bootbox.confirm(
      "Wollen sie alle Buchzuweisungen des Buchs #{book.name} wirklich \
      löschen?",
      (res)->
        if res
          data = {
            deleteEntity: 'book', entityId: book.id, bookId: book.id
          }
          that.deleteAssignments data
    )


  render: ->
    title = <h4>Zuweisungen der Bücher an Nutzer</h4>
    <Panel className='panel-dashboard' header={title}>
      <OptionsLine schoolyears={@state.schoolyears}
        handleChangeSchoolyear={@handleChangeSchoolyear}/>
      <BooksToUsersAssignments books={@state.books}
        handleGradelevelOfBookAssignmentsDelete={@handleGradelevelOfBookAssignmentsDelete}
        handleGradeOfBookAssignmentsDelete={@handleGradeOfBookAssignmentsDelete}
        handleBookAssignmentsDelete={@handleBookAssignmentsDelete}
      />
    </Panel>
)

renderBooksToUsersAssignments = ->
  React.render(
    <AssignmentsBox />
    $('#view-entry')[0]
  )

renderBooksToUsersAssignments()