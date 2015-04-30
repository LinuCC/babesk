React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Input = require 'react-bootstrap/lib/Input'
Panel = require 'react-bootstrap/lib/Panel'
Toggle = require 'react-toggle'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
SelectList = require 'react-widgets/lib/SelectList'

module.exports = React.createClass(

  getDefaultProps: ->
    return {
      user: {}
      groups: []
    }

  render: ->
    console.log @props
    personalTitle = <h4>Personendaten</h4>
    systemTitle = <h4>Systemdaten</h4>
    <Row>
      <Col md={12} lg={6}>
        <Panel className='panel-dashboard' header={personalTitle}>
          <form className='form-horizontal'>
            <Input type='text' value={@props.user.forename} label='Vorname'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='newspaper-o' fixedWidth/>} />
            <Input type='text' value={@props.user.surname} label='Nachname'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='newspaper-o' fixedWidth/>} />
            <Input type='text' value={@props.user.username}
              label='Benutzername'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='user' fixedWidth/>} />
            <Input type='text' value={@props.user.email}
              label='Emailadresse'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='envelope-o' fixedWidth/>} />
            <Input type='text' value={@props.user.telephone}
              label='Telefonnummer'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='phone' fixedWidth/>} />
            <Input type='text' value={@props.user.birthday}
              label='Geburtsdatum'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'
              addonBefore={<Icon name='calendar' fixedWidth/>} />
          </form>
        </Panel>
      </Col>
      <Col md={12} lg={6}>
        <Panel className='panel-dashboard' header={systemTitle}>
          <form className='form-horizontal'>
            <Input label='Konto gesperrt?'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'>
              <Toggle />
            </Input>
            <Input wrapperClassName='col-xs-offset-2 col-xs-10'>
              <Button bsStyle='default'>
                Passwort ändern
              </Button>
            </Input>
            <Input label='Benutzergruppen'
              labelClassName='col-xs-2' wrapperClassName='col-xs-10'>
              <SelectList data={@props.groups} valueField='id'
                value={@props.user.activeGroups}
                textField='name' multiple />
            </Input>
          </form>
        </Panel>
      </Col>
    </Row>
)