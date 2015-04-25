React = require 'react'
Alert = require 'react-bootstrap/lib/Alert'
Well = require 'react-bootstrap/lib/Well'
Input = require 'react-bootstrap/lib/Input'
ListGroup = require 'react-bootstrap/lib/ListGroup'
ListGroupItem = require 'react-bootstrap/lib/ListGroupItem'


module.exports = React.createClass(

  getDefaultProps: ->
    return {
      barcodeGroups: []
      uniqueBarcodes: []
      onSelectedBookChange: (groupKey, bookId)-> {}
    }

  handleBookSelectChange: (groupKey, event)->
    value = false
    value = event.target.value unless event.target.value is 'placeholder'
    @props.onSelectedBookChange groupKey, value

  render: ->
    hasForAllGroupsSelected = @props.barcodeGroups.every (group)->
      return group.assignedBookId isnt false
    <div>
      {
        if not hasForAllGroupsSelected
          <Alert bsStyle='info'>
            <strong>Mehrdeutige Barcodes!</strong>
            <p>
              Bitte wählen sie im folgenden für die jeweiligen Barcodes der
              neuen Exemplare das korrekte Buch aus.
            </p>
          </Alert>
        else
          <Alert bsStyle='success'>
            <strong>Bücher ausgewählt</strong>
            <p>
              Sie können nun die neuen Exemplare hinzufügen.
            </p>
          </Alert>
      }
      {
        @props.barcodeGroups.map (group, index)=>
          <Well key={index}>
            <Input type='select' label='Buch'
              onChange={@handleBookSelectChange.bind(null, index)}
              placeholder='placeholder'>
              <option value='placeholder'>Bitte auswählen...</option>
              {
                group.books.map (book)->
                  <option value={book.id} key={book.id}>{book.title}</option>
              }
            </Input>
            <Input label='zugehörige Barcodes'>
              <ListGroup>
                {
                  group.barcodes.map (barcode)->
                    <ListGroupItem key={barcode}>
                      {barcode}
                    </ListGroupItem>
                }
              </ListGroup>
            </Input>
          </Well>
      }
      <Well>
        <Input label='Eindeutige Barcodes'>
          <ListGroup>
            {
              @props.uniqueBarcodes.map (barcodeObj, index)->
                <ListGroupItem key={index} bsStyle='success'>
                  {barcodeObj.barcode}
                </ListGroupItem>
            }
          </ListGroup>
        </Input>
      </Well>
    </div>

)