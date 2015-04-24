React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
ButtonGroup = require 'react-bootstrap/lib/ButtonGroup'
AddInventoryBox = require './AddInventoryBox'

module.exports = React.createClass(

  getInitialState: ->
    return {
      showAddInventory: false
      newBarcodes: []
    }

  handleAddInventoryClicked: ->
    @setState showAddInventory: true

  handleSubmitAddInventoryClicked: ->
    toastr.error 'Kommt noch!'
    @setState newBarcodes: []

  handleCancelAddInventoryClicked: ->
    @setState showAddInventory: false

  handleNewBarcode: (barcode)->
    barcodes = @state.newBarcodes
    barcodes.push barcode
    @setState newBarcodes: barcodes

  handleNewBarcodeRemove: (barcodeIndex)->
    barcodes = @state.newBarcodes
    barcodes.splice barcodeIndex, 1
    @setState newBarcodes: barcodes

  render: ->
    <div>
      {
        if @state.showAddInventory
          <ButtonGroup justified>
            <ButtonGroup>
              <Button bsStyle='default'
                onClick={@handleCancelAddInventoryClicked}>
                <Icon name='trash-o' /> Hinzufügen abbrechen
              </Button>
            </ButtonGroup>
            <ButtonGroup>
              <Button bsStyle='primary'
                disabled={@state.newBarcodes.length is 0}
                onClick={@handleSubmitAddInventoryClicked}>
                <Icon name='upload' /> Hinzufügen
              </Button>
            </ButtonGroup>
          </ButtonGroup>
        else
          <ButtonGroup justified>
            <ButtonGroup>
              <Button bsStyle='default' onClick={@handleAddInventoryClicked}>
                Exemplare hinzufügen
              </Button>
            </ButtonGroup>
          </ButtonGroup>
      }
      <hr />
      {
        if @state.showAddInventory
          <AddInventoryBox barcodes={@state.newBarcodes}
            onNewBarcode={@handleNewBarcode}
            onNewBarcodeRemove={@handleNewBarcodeRemove} />
      }
    </div>
)