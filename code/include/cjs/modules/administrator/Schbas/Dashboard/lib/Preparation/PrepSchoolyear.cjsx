React = require 'react'
React.Bootstrap = require 'react-bootstrap'
DropdownButton = React.Bootstrap.DropdownButton
Label = React.Bootstrap.Label
MenuItem = React.Bootstrap.MenuItem

PrepSchoolyear = React.createClass(
  handleSchoolyearChange: (schoolyearId)->
    @props.handleSchoolyearChange schoolyearId

  render: ->
    <DropdownButton bsStyle='default'
      title={'Für Schuljahr ' + @props.schoolyears.active.name}>
      {
        @props.schoolyears.alternatives.map((schoolyear)->
          boundSchoolyearChange = @handleSchoolyearChange.bind(
            @, schoolyear.id
          )
          <MenuItem eventKey={schoolyear.id} key={schoolyear.id}
            onClick={boundSchoolyearChange}>
            {schoolyear.name}&nbsp;&nbsp;
            {
              if schoolyear.entriesExist
                <Label bsStyle='warning'>Einträge existieren</Label>
            }
          </MenuItem>
        , @)
      }
    </DropdownButton>
)

module.exports = PrepSchoolyear