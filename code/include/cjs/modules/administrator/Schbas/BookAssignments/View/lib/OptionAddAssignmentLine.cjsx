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
      selectedBook:
        value: 0
        label: ''
      selectedType: 'user'
      selectedValue:
        value: 0
        label: ''
    }

  getDefaultProps: ->
    return {
      schoolyear: {}
      onAssignmentsChanged: -> {}
    }

  handleSubmit: ->
    if not @state.selectedBook.value
      toastr.error 'Bitte wählen sie ein Buch aus'
      return
    if not @state.selectedValue.value
      toastr.error 'Bitte wählen sie einen Eintrag bei Benutzer/Klasse/\
        Klassenstufe aus'
      return
    if not @props.schoolyear.id
      toastr.error 'Kein Schuljahr ausgewählt'
      return
    $.post(
      'index.php?module=administrator|Schbas|BookAssignments|Add', {
        bookId: @state.selectedBook.value
        entityType: @state.selectedType
        entityId: @state.selectedValue.value
        schoolyearId: @props.schoolyear.id
    }).done (data)=>
        toastr.success data, 'Erfolgreich'
        @props.onAssignmentsChanged()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler beim Hinzufügen'

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

  searchGrades: (input, callback)->
    setTimeout( =>
      if not input.length then return
      $.getJSON(
        'index.php?module=administrator|System|Grades|Search'
        {gradename: input}
      ).done (data)=>
          selectData = @replaceKeys data, 'gradename', 'id'
          callback(null,
            options: selectData
          )
        .fail (jqxhr)->
          toastr.error jqxhr.responseText, 'Fehler beim Klassen-suchen'
    , 500)

  searchGradelevels: (input, callback)->
    setTimeout( =>
      if not input.length then return
      $.getJSON(
        'index.php?module=administrator|System|Gradelevels|Search'
        {gradelevel: input}
      ).done (data)=>
          selectData = data.map (entry)->
            entry.label = entry.gradelevel.toString()
            entry.value = entry.gradelevel
            delete entry.gradelevel
            return entry
          callback(null,
            options: selectData
          )
        .fail (jqxhr)->
          toastr.error jqxhr.responseText, 'Fehler beim Klassenstufen-suchen'
    , 500)

  handleTypeSelect: (event)->
    # The value-select gets changed, reset the value-data too
    @setState(
      selectedType: event.target.value
      selectedValue:
        id: 0
        label: ''
    )

  handleBookSelect: (bookVal, bookData)->
    data = bookData[0] # We dont have multiselection
    @setState selectedBook: data

  handleEntityValueSelect: (value, entityData)->
    data = entityData[0]
    @setState selectedValue: data

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
          <ExtendedSelect key={1} asyncOptions={@searchBooks} autoload={false}
            name='add-assignment-book-search' value={@state.selectedBook.label}
            onChange={@handleBookSelect} />
        </Input>
        <Input label={typeLabel} labelClassName='col-sm-2'
          wrapperClassName='col-sm-10' onChange={@handleTypeSelect}>
          {
            if @state.selectedType is 'grade'
              <ExtendedSelect key={2} asyncOptions={@searchGrades} autoload={false}
                name='add-assignment-grade-search'
                value={@state.selectedValue.label}
                onChange={@handleEntityValueSelect} />
            else if @state.selectedType is 'gradelevel'
              <ExtendedSelect key={3} asyncOptions={@searchGradelevels}
                autoload={false} name='add-assignment-gradelevel-search'
                value={@state.selectedValue.label}
                onChange={@handleEntityValueSelect} />
            else if @state.selectedType is 'user'
              <ExtendedSelect key={4} asyncOptions={@searchUsers} autoload={false}
                name='add-assignment-users-search'
                value={@state.selectedValue.label}
                onChange={@handleEntityValueSelect} />
          }
        </Input>
        <Button bsStyle='primary' className='pull-right'
          onClick={@handleSubmit}>
          Buch hinzufügen
        </Button>
        <div className='clearfix'></div>
      </form>
    </ListGroupItem>
)