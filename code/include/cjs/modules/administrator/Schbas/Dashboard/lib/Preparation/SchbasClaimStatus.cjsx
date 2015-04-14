React = require 'react'
CheckboxButton = require 'lib/CheckboxButton.js'

SchbasClaimStatus = React.createClass(
  render: ->
    <CheckboxButton onText='Rückmeldeformular aktiv'
      offText='Rückmeldeformular deaktiv' onStyle='primary'
      offStyle='warning'>
    </CheckboxButton>
)

module.exports = SchbasClaimStatus