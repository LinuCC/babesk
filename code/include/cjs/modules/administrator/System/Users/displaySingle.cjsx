React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Panel = require 'react-bootstrap/lib/Panel'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
NProgress = require 'nprogress'

Settings = require './lib/DisplaySingle/Settings'

App = React.createClass(

  getInitialState: ->
    return {
      selected: 'settings'
      formData: {
        user: {}
        groups: []
      }
    }

  componentDidMount: ->
    NProgress.start()
    $.getJSON(
      'index.php?module=administrator|System|Users'
      {id: window.userId, ajax: true}
    ) .done (res)=>
        @setState formData: res
        NProgress.done()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler beim Abrufen der Daten'
        NProgress.done()

  handleSelectedChange: (value)->
    if value isnt @state.selected
      NProgress.start()
      @setState selected: value
      NProgress.done()

  render: ->
    <div>
      <Row>
        <div className='user-header'>
          <div>
            <h4>Pascal Ernst</h4>
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
              <Icon name="cog" size="large" />
              Einstellungen
            </a>
          </Row>
        </div>
      </Row>
      {
        if @state.selected is 'overview'
          <h3>Später :) </h3>
        else if @state.selected is 'statistics'
          <h3>Später :) </h3>
        else if @state.selected is 'settings'
          <Settings {...@state.formData} />
        else
          <h3>Nichts ausgewählt...</h3>
      }
    </div>
)

React.render(
  <App userId={window.userId} />
  $('#entry')[0]
)