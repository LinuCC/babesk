React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Button = React.Bootstrap.Button
classnames = require 'classnames'

CheckboxButton = React.createClass(
  getDefaultProps: ->
    return {
      offText: ''
      onText: ''
      offStyle: 'default'
      onStyle: 'primary'
      onClick: -> true
    }
  onClick: ->
    @props.onChange(not @props.checked)
  render: ->
    bsStyle = if @props.checked then @props.onStyle else @props.offStyle
    iconClasses = classnames(
      'fa fa-fw': true
      'fa-square-o': not @props.checked
      'fa-check-square-o': @props.checked
    )
    <Button bsStyle={bsStyle} onClick={@onClick} active={@props.checked}>
      <i className={iconClasses}></i>&nbsp;
      {if @props.checked then @props.onText else @props.offText}
      {@props.children}
    </Button>
)

module.exports = CheckboxButton