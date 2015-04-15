React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Button = React.Bootstrap.Button
Gradelevel = require './Gradelevel'

module.exports = React.createClass(

  getDefaultProps: ->
    return {
      handleGradeOfBookAssignmentsDelete: (book, gradeId, gradeName)->
        console.log gradeId
      handleGradelevelOfBookAssignmentsDelete: (book, level)->
        console.log level
      handleBookAssignmentsDelete: (book)-> console.log book
    }

  handleGradelevelDelete: (gradelevel)->
    console.log @props
    @props.handleGradelevelOfBookAssignmentsDelete @props.data, gradelevel

  handleGradeDelete: (gradeId, gradeName)->
    @props.handleGradeOfBookAssignmentsDelete(
      @props.data, gradeId, gradeName
    )

  handleBookDelete: ->
    @props.handleBookAssignmentsDelete @props.data

  render: ->
    rowCount = 1
    for gradelevel in @props.data.gradelevels
      rowCount += 1
    <tbody>
      <tr>
        <td rowSpan={rowCount} className='book-column'>
          <b>{@props.data.name}</b>
          <Button bsStyle='danger' className='pull-right'
            onClick={@handleBookDelete}>
            Zuweisungen löschen
          </Button>
        </td>
      </tr>
      {
        @props.data.gradelevels.map (gradelevel)=>
          <Gradelevel key={gradelevel.level} data={gradelevel}
            handleGradelevelDelete={@handleGradelevelDelete}
            handleGradeDelete={@handleGradeDelete}>
          </Gradelevel>
      }
    </tbody>
)