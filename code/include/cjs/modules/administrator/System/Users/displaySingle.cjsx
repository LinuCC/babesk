React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Panel = require 'react-bootstrap/lib/Panel'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'

App = React.createClass(

  getInitialState: ->
    return {

    }

  render: ->
    <div>
      <Row>
        <div className='user-header'>
          <h3>Benutzer "Pascal Ernst"</h3>
          <Row className='tabs'>
            <a href='#'className="text-center">
              <Icon name="eye" size="large" />
              Übersicht
            </a>
            <a href='#'>
              <Icon name="bar-chart" size="large" />
              Statistiken
            </a>
            <a href='#'>
              <Icon name="cog" size="large" />
              Einstellungen
            </a>
          </Row>
        </div>
      </Row>
      <Row>
      </Row>
    </div>
)

React.render(
  <App />
  $('#entry')[0]
)