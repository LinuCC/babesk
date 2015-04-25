React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
ButtonGroup = require 'react-bootstrap/lib/ButtonGroup'
AddInventoryBox = require './AddInventoryBox'
SolveDuplicatesBox = require './SolveDuplicatesBox'

module.exports = React.createClass(

  getInitialState: ->
    return {
      showAddInventory: false
      newBarcodes: []
      askDuplicates: false
      askDuplicatesData: {}
    }

  # We need to check the barcodes that they have only one unique book
  # If not, we need to ask the user to what book the barcode should belong
  checkForUniqueBarcodes: (booksWithBarcodes)->
    duplicated = []
    unique = []
    # Input-Format: [
    #     { id: <bookId>, title: <bookTitle>, barcodes: [ <barcodeStr> ] }
    # ]
    # Output-Format duplicated-array: [
    #     { barcode: <barcodeStr>, books: [
    #         {id: <bookId>, title:<bookTitle>}
    #     ] }
    # ]
    booksWithBarcodes.map (book)->
      book.barcodes.map (barcode)->
        isDuplicated = false
        booksWithBarcodes.map (compareBook)->
          compareBook.barcodes.map (compareBarcode)->
            if book.id isnt compareBook.id and barcode is compareBarcode
              # Books are duplicated
              isDuplicated = true
              barcodeKey = lookupKeyOfObjectInArray(
                duplicated, 'barcode', compareBarcode
              )
              if barcodeKey isnt false
                # Barcode does exist in the duplicated-array
                bookKey = lookupKeyOfObjectInArray(
                  duplicated[barcodeKey]['books'], 'id', compareBook.id
                )
                if bookKey isnt false
                  # Book already exists for barcode in duplicated, do nothing
                else
                  duplicated[barcodeKey]['books'].push(compareBook)
              else
                # Barcode does not exist in the duplicated-array
                newBarcodeDuplication = {
                  barcode: compareBarcode
                  books: []
                }
                newBook = {id: book.id, title: book.title}
                newCompareBook = {id: compareBook.id, title: compareBook.title}
                newBarcodeDuplication.books.push newBook
                newBarcodeDuplication.books.push newCompareBook
                duplicated.push newBarcodeDuplication
        if not isDuplicated
          unique.push {
            barcode: barcode
            bookId: book.id
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
          @setState {askDuplicatesData: barcodes, askDuplicates: true}
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
            {
              if @state.askDuplicates
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
        if @state.showAddInventory and @state.askDuplicates
          <SolveDuplicatesBox uniqueBarcodes={@state.askDuplicatesData.unique}
            onSelectedBookChange={@handleDuplicateSelectedBookChange}
            barcodeGroups={@state.askDuplicatesData.duplicated} />
        else if @state.showAddInventory
          <AddInventoryBox barcodes={@state.newBarcodes}
            onNewBarcode={@handleNewBarcode}
            onNewBarcodeRemove={@handleNewBarcodeRemove} />
      }
    </div>
)