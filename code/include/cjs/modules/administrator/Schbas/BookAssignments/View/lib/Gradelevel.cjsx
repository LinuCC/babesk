React = require 'react'
React.Bootstrap = require 'react-bootstrap'
SplitButton = React.Bootstrap.SplitButton
MenuItem = React.Bootstrap.MenuItem
Grade = require './Grade'

module.exports = React.createClass(
  getDefaultProps: ->
    return {
      handleGradeDelete: (id, label)-> console.log id
      handleGradelevelDelete: (level)-> console.log level
    }

  handleDelete: ->
    @props.handleGradelevelDelete @props.data.level

  handleGradeDelete: (id, label)->
    @props.handleGradeDelete id, @props.data.level + label

  render: ->
    usersAssigned = 0
    for grade in @props.data.grades
      usersAssigned += parseInt grade.usersAssigned
    title = []
    title.push <span>{@props.data.level}</span>
    title.push <i className='user-count'>&nbsp;({usersAssigned})</i>
    <tr>
      <td className='gradelevel-cell'>
        <SplitButton bsStyle='default' title={title}>
          <MenuItem eventKey='delete' onClick={@handleDelete} key='delete'>
            Zuweisungen entfernen
          </MenuItem>
        </SplitButton>
      </td>
      <td className='grade-cell'>
        <ul>
          {
            @props.data.grades.map (grade)=>
              <Grade key={grade.id} gradelevel={@props.data.level} data={grade}
                handleGradeDelete={@handleGradeDelete} >
              </Grade>
          }
        </ul>
      </td>
    </tr>
)