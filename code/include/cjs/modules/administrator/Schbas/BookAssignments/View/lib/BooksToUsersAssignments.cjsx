React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Table = React.Bootstrap.Table
Book = require './Book'

module.exports = React.createClass(
  getInitialState: ->
    return {
      schoolyears: []
      books: []
    }

  componentDidMount: ->
    @updateTable()

  updateTable: ->
    $.getJSON(
      'index.php?module=administrator|Schbas|BookAssignments|View',
      {'jsonData': true, 'schoolyearId': 5}
    ).done (res)=> if @isMounted() then @setState res
      .fail (jqxhr)-> toastr.error jqxhr.responseText, 'Fehler'

  deleteAssignments: (data)->
    $.get(
      'index.php?module=administrator|Schbas|BookAssignments|View|Delete'
      data
    ).done (res)=>
        toastr.success res, 'Erfolgreich gelöscht'
        @updateTable()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler'

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
    <Table bordered>
      <thead>
        <tr>
          <th>Buch</th>
          <th>Jahrgang</th>
          <th>Klasse</th>
        </tr>
      </thead>
      {
        @state.books.map (book)=>
          <Book key={book.id} data={book}
            handleGradelevelOfBookAssignmentsDelete={@handleGradelevelOfBookAssignmentsDelete}
            handleGradeOfBookAssignmentsDelete={@handleGradeOfBookAssignmentsDelete}
            handleBookAssignmentsDelete={@handleBookAssignmentsDelete}
            >
          </Book>
      }
    </Table>
)