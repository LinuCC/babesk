React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Input = require 'react-bootstrap/lib/Input'
Icon = require 'lib/FontAwesomeIcon'
classnames = require 'classnames'

module.exports = React.createClass(

  getInitialState: ->
    newBarcodeValue: ''

  getDefaultProps: ->
    return {
      barcodes: [],
      onNewBarcode: -> {}
      onNewBarcodeRemove: (barcodeIndex)-> console.log barcodeIndex
    }

  handleNewBarcode: ->
    @props.onNewBarcode @state.newBarcodeValue
    @setState newBarcodeValue: ''

  handleNewBarcodeKeyPress: (event)->
    # On Enter pressed
    if event.which is 13
      @handleNewBarcode()

  handleNewBarcodeValueChange: (event)->
    @setState newBarcodeValue: event.target.value

  deleteButton: (barcodeIndex)->
    <Button bsStyle='danger'
      onClick={@props.onNewBarcodeRemove.bind(null, barcodeIndex)}>
      <Icon name='trash-o' fixedWidth />
    </Button>

  render: ->
    addButton = (
      <Button bsStyle='success' onClick={@handleNewBarcode}>
        <Icon name='plus-square-o' fixedWidth />
      </Button>
    )
    <div>
      {
        @props.barcodes.map (barcode, index)=>
          <Input type='text' key={index} value={barcode} readOnly
            buttonAfter={@deleteButton(index)} />
      }
      <Input type='text' value={@state.newBarcodeValue} buttonAfter={addButton}
        onKeyPress={@handleNewBarcodeKeyPress}
        onChange={@handleNewBarcodeValueChange} />
    </div>
)