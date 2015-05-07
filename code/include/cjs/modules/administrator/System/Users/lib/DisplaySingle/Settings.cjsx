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
      settingsChanged: false
      onUserChange: (key, value)-> console.log [key, value]
    }

  handleGroupChange: (selectedGroups)->
    groups = selectedGroups.map (group)-> return group.id
    # Void arrays will be wrongfully removed by PHP
    if groups.length is 0 then groups = false
    @props.onUserChange 'activeGroups', groups

  handlePersonalDataChange: (name, event)->
    @props.onUserChange name, event.target.value

  handleAccountLockedChange: (event)->
    @props.onUserChange 'locked', event.target.checked

  render: ->
    personalTitle = <h4>Personendaten</h4>
    systemTitle = <h4>Systemdaten</h4>
    <div>
      <Row>
        <Col md={12} lg={6}>
          <Panel className='panel-dashboard' header={personalTitle}>
            <form className='form-horizontal'>
              <Input type='text' value={@props.user.forename} label='Vorname'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='newspaper-o' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'forename')} />
              <Input type='text' value={@props.user.surname} label='Nachname'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='newspaper-o' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'surname')} />
              <Input type='text' value={@props.user.username}
                label='Benutzername'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='user' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'username')} />
              <Input type='text' value={@props.user.email}
                label='Emailadresse'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='envelope-o' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'email')} />
              <Input type='text' value={@props.user.telephone}
                label='Telefonnummer'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='phone' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'telephone')} />
              <Input type='text' value={@props.user.birthday}
                label='Geburtsdatum'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'
                addonBefore={<Icon name='calendar' fixedWidth/>}
                onChange={@handlePersonalDataChange.bind(null, 'birthday')} />
            </form>
          </Panel>
        </Col>
        <Col md={12} lg={6}>
          <Panel className='panel-dashboard' header={systemTitle}>
            <form className='form-horizontal'>
              <Input label='Konto gesperrt?' onChange={@handleGroupChange}
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'>
                <Toggle checked={@props.user.locked} onChange={@handleAccountLockedChange} />
              </Input>
              <Input wrapperClassName='col-xs-offset-2 col-xs-10'>
                <Button bsStyle='default'>
                  Passwort ändern
                </Button>
              </Input>
              <Input label='Benutzergruppen'
                labelClassName='col-xs-2' wrapperClassName='col-xs-10'>
                <SelectList data={@props.groups} valueField='id'
                  value={@props.user.activeGroups} textField='name' multiple
                  onChange={@handleGroupChange} />
              </Input>
            </form>
          </Panel>
        </Col>
      </Row>
    </div>
)