React = require 'react'
React.Bootstrap = require 'react-bootstrap'
ListGroupItem = React.Bootstrap.ListGroupItem
Row = React.Bootstrap.Row
Col = React.Bootstrap.Col
Button = React.Bootstrap.Button
Input = React.Bootstrap.Input
ButtonGroup = React.Bootstrap.ButtonGroup
DropdownButton = React.Bootstrap.DropdownButton
MenuItem = React.Bootstrap.MenuItem
ExtendedSelect = require 'react-select'

module.exports = React.createClass(

  getInitialState: ->
    return {
      selectedBook: 0,
      selectedType: 'user',
      selectedValue: 0
    }

  getDefaultProps: ->
    return {
      schoolyear: {}
    }

  replaceKeys: (data, keyLabelName, keyValueName)->
    # Replace the keys with label and value for the select-component
    # Useful when the Rest-Api returns some other values
    return data.map (entry)->
      entry.label = entry[keyLabelName]
      entry.value = entry[keyValueName]
      delete entry[keyLabelName]
      delete entry[keyValueName]
      return entry

  searchBooks: (input, callback)->
    setTimeout( =>
      if not input.length then return
      $.getJSON(
        'index.php?module=administrator|Schbas|Books|Search'
        {title: input}
      ).done (data)=>
          selectData = @replaceKeys data, 'title', 'id'
          callback(null,
            options: selectData
          )
        .fail (jqxhr)->
          toastr.error jqxhr.responseText, 'Fehler beim Buch-suchen'
    , 500)

  searchUsers: (input, callback)->
    setTimeout( =>
      if not input.length then return
      if not @props.schoolyear.id?
        toastr.error 'Kein Schuljahr ausgewählt'
      $.getJSON(
        'index.php?module=administrator|System|Users|Search'
        {username: input, schoolyearId: @props.schoolyear.id}
      ).done (data)=>
          selectData = @replaceKeys data, 'username', 'id'
          callback(null,
            options: selectData
          )
        .fail (jqxhr)->
          toastr.error jqxhr.responseText, 'Fehler beim User-suchen'
    , 500)

  handleTypeSelect: (event)->
    @setState selectedType: event.target.value
    console.log event.target.value

  render: ->
    typeLabel = ''
    if @state.selectedType == 'user' then typeLabel = 'Benutzer'
    if @state.selectedType == 'grade' then typeLabel = 'Klasse'
    if @state.selectedType == 'gradelevel' then typeLabel = 'Klassenstufe'
    <ListGroupItem>
      <form className='form-horizontal'>
        <Input type='select' label='Hinzufügen zu' labelClassName='col-sm-2'
          wrapperClassName='col-sm-10' onChange={@handleTypeSelect}
          value={@state.selectedType}>
          <option value='user' key='user' eventKey='user'>Benutzer</option>
          <option value='grade' key='grade' eventKey='grade'>Klasse</option>
          <option value='gradelevel' key='gradelevel' eventKey='gradelevel'>
            Klassenstufe
          </option>
        </Input>
        <Input label='Buch' labelClassName='col-sm-2'
          wrapperClassName='col-sm-10'>
          <ExtendedSelect asyncOptions={@searchBooks} autoload={false}
            name='add-assignment-book-search' />
        </Input>
        <Input label={typeLabel} labelClassName='col-sm-2'
          wrapperClassName='col-sm-10' onChange={@handleTypeSelect}>
          {
            if @state.selectedType is 'grade'
              <ExtendedSelect asyncOptions={@searchBooks} autoload={false}
                name='add-assignment-grade-search' />
            else if @state.selectedType is 'gradelevel'
              <ExtendedSelect asyncOptions={@searchBooks} autoload={false}
                name='add-assignment-gradelevel-search' />
            else if @state.selectedType is 'user'
              <ExtendedSelect asyncOptions={@searchUsers} autoload={false}
                name='add-assignment-users-search' />
          }
        </Input>
        <Button bsStyle='primary' className='pull-right' >
          Buch hinzufügen
        </Button>
        <div className='clearfix'></div>
      </form>
    </ListGroupItem>
)