React = require 'react'
React.Bootstrap = require 'react-bootstrap'
Input = React.Bootstrap.Input
Col = React.Bootstrap.Col
Row = React.Bootstrap.Row
moment = require 'moment'
DateTimePicker = require 'react-widgets/lib/DateTimePicker'

Deadlines = React.createClass(

  handleTransferDeadlineChange: (dateObj, dateStr)->
    @props.onChange dateStr, 'schbasDeadlineTransfer'

  handleClaimDeadlineChange: (dateObj, dateStr)->
    @props.onChange dateStr, 'schbasDeadlineClaim'

  render: ->
    <div className='form-horizontal'>
      <Row>
        <Col lg={6}>
          <Input label='Geldtransfer' labelClassName='col-md-3'
            wrapperClassName='col-md-9'>
            <DateTimePicker format='yyyy-MM-dd'
              value={new Date(@props.deadlines.schbasDeadlineTransfer)}
              time={false} onChange={@handleTransferDeadlineChange} />
          </Input>
        </Col>
        <Col lg={6}>
          <Input label='Formularrückgabe' labelClassName='col-md-3' wrapperClassName='col-md-9'>
            <DateTimePicker format='yyyy-MM-dd'
              value={new Date(@props.deadlines.schbasDeadlineClaim)}
              time={false} onChange={@handleClaimDeadlineChange} />
          </Input>
        </Col>
      </Row>
    </div>
)

module.exports = Deadlines