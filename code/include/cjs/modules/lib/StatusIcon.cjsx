React = require 'react'
classnames = require 'classnames'

StatusIcon = React.createClass(
  render: ->
    classes = classnames(
      'fa fa-3x fa-fw pull-left': true
      'fa-times-circle': @props.status == 'danger'
      'fa-exclamation-circle': @props.status == 'warning'
      'fa-info-circle': @props.status == 'info'
      'fa-check-circle': @props.status == 'success'
    )
    <i className={classes}></i>
)

exports.StatusIcon = StatusIcon