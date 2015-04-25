React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Input = require 'react-bootstrap/lib/Input'
Icon = require 'lib/FontAwesomeIcon'
classnames = require 'classnames'

module.exports = React.createClass(

  getInitialState: ->
    deleteBarcodeValue: ''

  getDefaultProps: ->
    return {
      barcodes: [],
      onDeleteBarcode: -> {}
      onDeleteBarcodeRemove: (barcodeIndex)-> console.log barcodeIndex
    }

  handleDeleteBarcode: ->
    @props.onDeleteBarcode @state.deleteBarcodeValue
    @setState deleteBarcodeValue: ''

  handleDeleteBarcodeKeyPress: (event)->
    # On Enter pressed
    if event.which is 13
      @handleDeleteBarcode()

  handleDeleteBarcodeValueChange: (event)->
    @setState deleteBarcodeValue: event.target.value

  deleteButton: (barcodeIndex)->
    <Button bsStyle='danger'
      onClick={@props.onDeleteBarcodeRemove.bind(null, barcodeIndex)}>
      <Icon name='trash-o' fixedWidth />
    </Button>

  render: ->
    addButton = (
      <Button bsStyle='success' onClick={@handleDeleteBarcode}>
        <Icon name='plus-square-o' fixedWidth />
      </Button>
    )
    <div>
      {
        @props.barcodes.map (barcode, index)=>
          <Input type='text' key={index} value={barcode} readOnly
            buttonAfter={@deleteButton(index)} />
      }
      <Input type='text' value={@state.deleteBarcodeValue} buttonAfter={addButton}
        onKeyPress={@handleDeleteBarcodeKeyPress}
        onChange={@handleDeleteBarcodeValueChange} />
    </div>
)