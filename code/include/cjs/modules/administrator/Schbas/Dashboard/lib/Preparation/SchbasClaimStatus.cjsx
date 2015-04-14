React = require 'react'
CheckboxButton = require 'lib/CheckboxButton.js'

SchbasClaimStatus = React.createClass(
  handleStatusChanged: (checked)->
    @props.handleStatusChanged(checked)

  render: ->
    <CheckboxButton onText='Rückmeldeformular aktiv' checked={@props.status}
      offText='Rückmeldeformular deaktiv' onStyle='primary'
      offStyle='warning' onChange={@handleStatusChanged}>
    </CheckboxButton>
)

module.exports = SchbasClaimStatus