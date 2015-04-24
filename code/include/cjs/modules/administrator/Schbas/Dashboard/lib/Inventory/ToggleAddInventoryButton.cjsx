React = require 'react'
Button = require 'react-bootstrap/lib/Button'
classnames = require 'classnames'

module.exports = React.createClass(

  getDefaultProps: ->
    return {
      toggled: false
      onClick: -> {}
    }

  render: ->
    style = classnames(
      default: not @props.toggled
      danger: @props.toggled
    )
    <Button bsStyle={style} onClick={@props.onClick}>
      {
        if not @props.toggled
          'Exemplare hinzufügen'
        else
          'Hinzufügen abbrechen'
      }
    </Button>

)