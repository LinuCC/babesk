React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Input = React.Bootstrap.Input
moment = require 'moment'

Deadlines = React.createClass(
  render: ->
    <Input type='text' label='Deadlines' placeholder='Kommt bald...' />
)

module.exports = Deadlines