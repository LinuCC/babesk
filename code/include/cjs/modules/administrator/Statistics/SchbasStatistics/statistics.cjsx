React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Panel = require 'react-bootstrap/lib/Panel'
Input = require 'react-bootstrap/lib/Input'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
Label = require 'react-bootstrap/lib/Label'
DateTimePicker = require 'react-widgets/lib/DateTimePicker'
NProgress = require 'nprogress'

App = React.createClass(

  getInitialState: ->
    return {
      assistantsCost: 0
      toolsCost: 0
      otherCosts: [
        {
          amount: ''
          date: new Date()
          recipient: ''
        }
      ]
    }

  handleAddOtherCost: ->
    otherCosts = @state.otherCosts
    otherCosts.push { amount: '', date: new Date(), recipient: '' }
    @setState otherCosts: otherCosts

  handleAssistantsCostChange: (event)->
    @setState assistantsCost: event.target.value

  handleToolsCostChange: (event)->
    @setState toolsCost: event.target.value

  handleOtherCostChange: (index, key, value)->
    otherCosts = @state.otherCosts
    otherCosts[index][key] = value
    @setState otherCosts: otherCosts

  handlePrint: ->
    # Clone the state so changes will not change it
    state = $.extend true, {}, @state
    state.otherCosts.map (otherCost, index)->
      state.otherCosts[index].date = otherCost.date.toISOString().substr(0, 10)
    params = $.param state
    params += '&action=pdf'
    window.open(
      "index.php?module=administrator|Statistics|SchbasStatistics&#{params}"
      '_blank'
    )

  render: ->
    footer = <div>
      <Button bsStyle='primary' onClick={@handlePrint}
        className='pull-right'>
          PDF erstellen
      </Button>
      <div className='clearfix' />
    </div>
    <Panel className='panel-dashboard' header={<h4>Schbas-Statistik PDF</h4>}
      footer={footer}>
      <legend>Zusätzliche Angaben:</legend>
      <form className='form-horizontal'>
        <Input type='text' value={@state.assistantsCost}
          label='Kosten für Hilfskräfte' labelClassName='col-xs-2'
          wrapperClassName='col-xs-10' onChange={@handleAssistantsCostChange}
          addonBefore={<Icon name='user' fixedWidth />} />
        <Input type='text' value={@state.toolsCost}
          label='Kosten für Hilfsmittel' labelClassName='col-xs-2'
          wrapperClassName='col-xs-10' onChange={@handleToolsCostChange}
          addonBefore={<Icon name='wrench' fixedWidth />} />
        {
          @state.otherCosts.map (otherCost, index)=>
            <OtherCostsBox key={index} {...otherCost}
              onChange={@handleOtherCostChange.bind(null, index)}/>
        }
        <Button bsStyle='default' onClick={@handleAddOtherCost}
          className='pull-right'>
          Sonstige Kosten-Feld hinzufügen...
        </Button>
      </form>
    </Panel>
)

OtherCostsBox = React.createClass(

  getDefaultProps: ->
    return {
      amount: '',
      date: null,
      recipient: ''
      onChange: (key, value)-> {}
    }

  handleCostChange: (event)->
    cost = event.target.value
    @props.onChange 'amount', cost

  handleRecipientChange: (event)->
    recipient = event.target.value
    @props.onChange 'recipient', recipient

  handleDateChange: (date)->
    @props.onChange 'date', date

  render: ->
    <fieldset>
      <legend>Sonstige Kosten</legend>
      <Input type='text' value={@props.amount} steps='0.01'
        label='Kosten' labelClassName='col-xs-2'
        wrapperClassName='col-xs-10' onChange={@handleCostChange}
        addonBefore={<Icon name='money' fixedWidth />} />
      <Input label='Datum' labelClassName='col-xs-2'
        wrapperClassName='col-xs-10'>
        <DateTimePicker format='dd.MM.yyyy'
          value={new Date(@props.date)}
          time={false} onChange={@handleDateChange} />
      </Input>
      <Input type='text' value={@props.recipient}
        label='Empfänger' labelClassName='col-xs-2'
        wrapperClassName='col-xs-10' onChange={@handleRecipientChange}
        addonBefore={<Icon name='user' fixedWidth />} />
    </fieldset>
)

React.render(
  <App />
  $('#entry')[0]
)