React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Panel = require 'react-bootstrap/lib/Panel'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
Label = require 'react-bootstrap/lib/Label'
NProgress = require 'nprogress'

Settings = require './lib/DisplaySingle/Settings'

confirmExit = ->
  return 'Wollen sie die Seite wirklich verlassen? Sie haben Veränderungen\
    gemacht, die noch nicht gespeichert wurden'

noConfirmExit = ->
  return false

App = React.createClass(

  getInitialState: ->
    return {
      selected: 'settings'
      settingsChanged: false
      # Store the data for the settings-formular here so that non-committed
      # changes to the user dont get displayed in overview
      formData: {
        user: {}
        groups: []
      }
      user: {}
    }

  componentDidMount: ->
    @updateData()

  updateData: ->
    NProgress.start()
    $.getJSON(
      'index.php?module=administrator|System|Users'
      {id: window.userId, ajax: true}
    ) .done (res)=>
        state = @state
        state.formData = res
        # Clone the same data so that settings-changes dont affect the overview
        state.user = $.extend(true, {}, res.user)
        @setState state
        NProgress.done()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler beim Abrufen der Daten'
        NProgress.done()

  patchData: (data)->
    data['patch'] = true
    NProgress.start()
    $.ajax(
      method: 'POST'
      url: 'index.php?module=administrator|System|Users'
      data: data
    ) .done (res)=>
        @updateData()
        NProgress.done()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler beim Hochladen der Daten'
        NProgress.done()

  handleSelectedChange: (value)->
    if value isnt @state.selected
      NProgress.start()
      @setState selected: value
      NProgress.done()

  handleUserChange: (dataName, data)->
    state = @state
    if not state.settingsChanged
      state.settingsChanged = true
      window.onbeforeunload = confirmExit
    state['formData']['user'][dataName] = data
    console.log state
    @setState state
    # dataToPatch = {}
    # dataToPatch[dataName] = data
    # dataToPatch['userId'] = @state.formData.user.id
    # @patchData(dataToPatch)

  render: ->
    <div>
      <Row>
        <div className='user-header'>
          <div>
            <h4>
              Pascal Ernst
              {
                if @state.user.locked
                  <Label bsStyle='danger'>
                    gesperrt
                  </Label>
              }
            </h4>
          </div>
          <Row className='tabs'>
            <a href='#' onClick={@handleSelectedChange.bind(null, 'overview')}>
              <Icon name="eye" size="large" />
              Übersicht
            </a>
            <a href='#'
              onClick={@handleSelectedChange.bind(null, 'statistics')}>
              <Icon name="bar-chart" size="large" />
              Statistiken
            </a>
            <a href='#' onClick={@handleSelectedChange.bind(null, 'settings')}>
              <Icon name="cog" size="large" spin={@state.settingsChanged} />
              Einstellungen
            </a>
          </Row>
          <Row className='submenu'>
            {
              if @state.selected is 'settings'
                if not @state.settingsChanged
                  <p>
                    <Icon name="cog" size="large" />
                    Einstellungen
                  </p>
                else
                  <span>
                    <a href='#' className='bg-danger'>
                      <Icon name="trash-o" size="large" />
                      abbrechen
                    </a>
                    <a href='#' className='bg-info'>
                      <Icon name="upload" size="large" />
                      Änderungen speichern
                    </a>
                  </span>
            }
          </Row>
        </div>
      </Row>
      {
        if @state.selected is 'overview'
          <h3>Später :) </h3>
        else if @state.selected is 'statistics'
          <h3>Später :) </h3>
        else if @state.selected is 'settings'
          <Settings {...@state.formData} onUserChange={@handleUserChange}
            settingsChanged={@state.settingsChanged} />
        else
          <h3>Nichts ausgewählt...</h3>
      }
    </div>
)

React.render(
  <App userId={window.userId} />
  $('#entry')[0]
)