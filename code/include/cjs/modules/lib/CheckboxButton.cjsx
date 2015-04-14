React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Button = React.Bootstrap.Button
classnames = require 'classnames'

CheckboxButton = React.createClass(
  getInitialState: ->
    return {
      isChecked: false
    }
  getDefaultProps: ->
    return {
      offText: ''
      onText: ''
      offStyle: 'default'
      onStyle: 'primary'
      onClick: -> true
    }
  onClick: ->
    if @props.onClick()
      @setState(isChecked: not @state.isChecked)
  render: ->
    bsStyle = if @state.isChecked then @props.onStyle else @props.offStyle
    iconClasses = classnames(
      'fa fa-fw': true
      'fa-square-o': not @state.isChecked
      'fa-check-square-o': @state.isChecked
    )
    <Button bsStyle={bsStyle} onClick={@onClick}>
      <i className={iconClasses}></i>&nbsp;
      {if @state.isChecked then @props.onText else @props.offText}
      {@props.children}
    </Button>
)

module.exports = CheckboxButton