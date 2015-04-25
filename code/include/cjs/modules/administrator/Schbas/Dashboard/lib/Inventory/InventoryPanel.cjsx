React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
ButtonGroup = require 'react-bootstrap/lib/ButtonGroup'
AddInventoryBox = require './AddInventoryBox'
DeleteInventoryBox = require './DeleteInventoryBox'
SolveDuplicatesBox = require './SolveDuplicatesBox'

module.exports = React.createClass(

  getInitialState: ->
    return {
      showAddInventory: false
      showEditDuplicates: false
      showDeleteInventory: false
      newBarcodes: []
      deleteBarcodes: []
      askDuplicatesData: {}
    }

  handleAddInventoryClicked: ->
    @setState showAddInventory: true

  handleSubmitAddInventoryClicked: ->
    $.getJSON(
      'index.php?module=administrator|Schbas|Inventory'
      {getBooksForBarcodes: true, barcodes: @state.newBarcodes}
    ).done (barcodes)=>
        if barcodes.duplicated.length > 0
          # Non-unique barcodes exist, ask the user first
          barcodes.duplicated = barcodes.duplicated.map (barcodeGroup)->
            barcodeGroup.assignedBookId = false
            return barcodeGroup
          @setState {askDuplicatesData: barcodes, showEditDuplicates: true}
        else
          @uploadBarcodes barcodes.unique
    .fail (jqxhr)->
      toastr.error jqxhr.responseText, 'Fehler'

  handleSubmitAddDuplicatedInventoryClicked: ->
    barcodesToUpload = @state.askDuplicatesData.unique
    @state.askDuplicatesData.duplicated.forEach (group)->
      group.barcodes.forEach (barcode)->
        barcodesToUpload.push {barcode: barcode, bookId: group.assignedBookId}
    @uploadBarcodes barcodesToUpload

  uploadBarcodes: (barcodes)->
    $.post(
      'index.php?module=administrator|Schbas|Inventory|Add'
      {barcodesWithBookIds: barcodes}
    ) .done (res)=>
        toastr.success res, 'Erfolgreich'
        @setState {newBarcodes: [], showAddInventory: false}
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Ein Fehler ist aufgetreten'

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

  handleDuplicateSelectedBookChange: (groupKey, bookId)->
    askDuplicatesDataTmp = @state.askDuplicatesData
    askDuplicatesDataTmp['duplicated'][groupKey]['assignedBookId'] = bookId
    @setState askDuplicatesData: askDuplicatesDataTmp

  handleDeleteInventoryClicked: ->
    @setState showDeleteInventory: true

  handleCancelDeleteInventoryClicked: ->
    @setState showDeleteInventory: false

  handleSubmitDeleteInventoryClicked: ->
    bootbox.confirm(
      'Wollen sie die Exemplare wirklich löschen?'
      (res)=>
        if res
          $.post(
            'index.php?module=administrator|Schbas|Inventory|Delete'
            {barcodes: @state.deleteBarcodes}
          ) .done (res)=>
              toastr.success res, 'Erfolgreich gelöscht'
              @setState deleteBarcodes: []
            .fail (jqxhr)->
              toastr.error jqxhr.responseText, 'Fehler beim Löschen'
    )

  handleDeleteBarcode: (barcode)->
    barcodes = @state.deleteBarcodes
    barcodes.push barcode
    @setState deleteBarcodes: barcodes

  handleDeleteBarcodeRemove: (barcodeIndex)->
    barcodes = @state.deleteBarcodes
    barcodes.splice barcodeIndex, 1
    @setState deleteBarcodes: barcodes

  render: ->
    <div>
      {
        if @state.showAddInventory
          <ButtonGroup justified>
            <ButtonGroup>
              <Button bsStyle='default'
                onClick={@handleCancelAddInventoryClicked}>
                <Icon name='times' /> Hinzufügen abbrechen
              </Button>
            </ButtonGroup>
            {
              if @state.showEditDuplicates
                <ButtonGroup>
                  <Button bsStyle='primary'
                    disabled={
                      not @state.askDuplicatesData.duplicated.every (group) ->
                        return group.assignedBookId isnt false
                    }
                    onClick={@handleSubmitAddDuplicatedInventoryClicked}>
                    <Icon name='upload' /> Hinzufügen
                  </Button>
                </ButtonGroup>
              else
                <ButtonGroup>
                  <Button bsStyle='primary'
                    disabled={@state.newBarcodes.length is 0}
                    onClick={@handleSubmitAddInventoryClicked}>
                    <Icon name='upload' /> Hinzufügen
                  </Button>
                </ButtonGroup>
            }
          </ButtonGroup>
        else if @state.showDeleteInventory
          <ButtonGroup justified>
            <ButtonGroup>
              <Button bsStyle='default'
                onClick={@handleCancelDeleteInventoryClicked}>
                <Icon name='times' /> Löschen abbrechen
              </Button>
            </ButtonGroup>
            <ButtonGroup>
              <Button bsStyle='danger'
                disabled={@state.deleteBarcodes.length is 0}
                onClick={@handleSubmitDeleteInventoryClicked}>
                <Icon name='trash-o' /> Löschen
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
            <ButtonGroup>
              <Button bsStyle='default'
                onClick={@handleDeleteInventoryClicked}>
                Exemplare löschen
              </Button>
            </ButtonGroup>
          </ButtonGroup>
      }
      <hr />
      {
        if @state.showAddInventory and @state.showEditDuplicates
          <SolveDuplicatesBox uniqueBarcodes={@state.askDuplicatesData.unique}
            onSelectedBookChange={@handleDuplicateSelectedBookChange}
            barcodeGroups={@state.askDuplicatesData.duplicated} />
        else if @state.showAddInventory
          <AddInventoryBox barcodes={@state.newBarcodes}
            onNewBarcode={@handleNewBarcode}
            onNewBarcodeRemove={@handleNewBarcodeRemove} />
        else if @state.showDeleteInventory
          <DeleteInventoryBox barcodes={@state.deleteBarcodes}
            onDeleteBarcode={@handleDeleteBarcode}
            onDeleteBarcodeRemove={@handleDeleteBarcodeRemove} />
      }
    </div>
)