React = require 'react'
React.Bootstrap = require 'react-bootstrap'
SplitButton = React.Bootstrap.SplitButton
MenuItem = React.Bootstrap.MenuItem

module.exports = React.createClass(
  getDefaultProps: ->
    return {
      handleGradeDelete: (id, label)-> console.log id
    }

  handleDelete: ->
    @props.handleGradeDelete @props.data.id, @props.data.label

  render: ->
    title = []
    title.push <span>{@props.gradelevel}{@props.data.label}</span>
    title.push <i className='user-count'>
        &nbsp;&nbsp;({@props.data.usersAssigned})
      </i>
    <li>
      <SplitButton bsStyle='default' title={title} pullRight>
        <MenuItem eventKey='delete' onClick={@handleDelete} key='delete'>
          Zuweisungen <br /> entfernen
        </MenuItem>
      </SplitButton>
    </li>
)