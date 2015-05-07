React = require 'react'
classnames = require 'classnames'

module.exports = React.createClass(

  getDefaultProps: ->
    return {
      size: false
      pullRight: false
      pullLeft: false
      name: false
      fixedWidth: false
      spin: false
    }

  render: ->
    classes = classnames(
      'fa': true
      'pull-left': @props.pullLeft
      'pull-right': @props.pullRight
      'fa-fw': @props.fixedWidth
      'fa-spin': @props.spin
    )
    if @props.size then classes += " fa-#{@props.size}"
    if @props.name then classes += " fa-#{@props.name}"
    <i className={classes}></i>
)