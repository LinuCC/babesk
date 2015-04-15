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
    title.push <b>{@props.gradelevel}{@props.data.label}</b>
    title.push <i>&nbsp;&nbsp;({@props.data.usersAssigned})</i>
    <li>
      <SplitButton bsStyle='default' title={title} pullRight>
        <MenuItem eventKey='delete' onClick={@handleDelete}>
          Zuweisungen <br /> entfernen
        </MenuItem>
      </SplitButton>
    </li>
)