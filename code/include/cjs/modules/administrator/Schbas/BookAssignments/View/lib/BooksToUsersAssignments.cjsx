React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Table = React.Bootstrap.Table
Book = require './Book'

module.exports = React.createClass(

  getDefaultProps: ->
    return {
      books: []
      handleGradelevelOfBookAssignmentsDelete: -> {}
      handleGradeOfBookAssignmentsDelete: -> {}
      handleBookAssignmentsDelete: -> {}
    }

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
        @props.books.map (book)=>
          <Book key={book.id} data={book}
            handleGradelevelOfBookAssignmentsDelete={@props.handleGradelevelOfBookAssignmentsDelete}
            handleGradeOfBookAssignmentsDelete={@props.handleGradeOfBookAssignmentsDelete}
            handleBookAssignmentsDelete={@props.handleBookAssignmentsDelete}
            >
          </Book>
      }
    </Table>
)