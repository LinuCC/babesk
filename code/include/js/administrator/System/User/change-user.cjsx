ClassMultiSelectOption = React.createClass(
  render: ->
    <option value={@props.classData.id} >
      {@props.classData.name}
    </option>
)

ClassMultiSelect = React.createClass(
  $multiselect: null,
  componentDidMount: ->
    that = @
    @$multiselect = $(@getDOMNode())
    @$multiselect.multiselect(
      onChange: (element, checked)->
        if not that.handleChange element, checked
          # Revert action if handleChange returned false
          oldStatus = if not checked then 'select' else 'deselect'
          that.$multiselect.multiselect oldStatus, element.val()
    );
  handleChange: (element, checked)->
    @props.onClassChange element, checked
  render: ->
    # React allows to define the selected options in the select-tag instead of
    # setting selected for each option
    <select className="kuwasys-classes-multiselect" multiple={true}
      value={@props.selectedClasses}>
      {
        @props.allClasses.map(
          (classData)->
            <ClassMultiSelectOption key={classData.id} classData={classData} />
          , @
        )
      }
    </select>
)

Line = React.createClass(
  handleClassChange: (element, checked)->
    @props.onClassChange element, checked
  render: ->
    <div className="row">
      <label className="col-sm-3 col-md-2 control-label">
        {@props.data.schoolyear.label}
      </label>
      <div className="col-sm-9 col-md-10">
        <ClassMultiSelect allClasses={@props.data.allClasses}
          selectedClasses={@props.data.selected}
          onClassChange={@handleClassChange} />
      </div>
    </div>
)

Box = React.createClass(
  getInitialState: ->
    # Every class can only be in one schoolyear, so we dont need to sort the
    # classes by schoolyear
    selectedClasses = []
    @props.data.map (schoolyearData) ->
      $.merge selectedClasses, schoolyearData.selected
    return {
      selectedClasses: selectedClasses
    }
  handleClassChange: (element, checked)->
    if checked
      # Update the state using the addon to allow React to properly re-render
      # the children. Directly changing state would lead to inconsistency.
      @state = React.addons.update(
        @state,
        selectedClasses:
          $push: [parseInt(element[0].value)]
      )
    else
      # Remove de-selected class from state
      pos = $.inArray parseInt(element[0].value), @state.selectedClasses
      if pos isnt -1
        @state = React.addons.update(
          @state,
          selectedClasses:
            $splice: [ [pos, 1] ]
        )
    return true
  render: ->
    <fieldset>
      <legend>Kuwasys Kurszuweisungen des Benutzers</legend>
      <div className="panel panel-default">
        <ul className="list-group form-horizontal">
          {this.props.data.map(
            (lineData)->
              className = React.addons.classSet(
                "list-group-item": true,
                "list-group-item-success": lineData.schoolyear.isActive
              )
              <li className={className}>
                <Line key={lineData.schoolyear.id} data={lineData}
                  onClassChange={@handleClassChange} />
              </li>
            , @)
          }
        </ul>
        <div className="panel-footer">
          <a href="#" className="btn btn-primary">
            Bestätigen
          </a>
          <div className="clearfix"></div>
        </div>
      </div>
    </fieldset>
)

DATA = [
  {
    schoolyear:
      id: 2
      label: "2015-2"
      isActive: false
    allClasses: [
      {id: 2, name: "Datt is Wacken"}
      {id: 5, name: "Datt is Schinken"}
      {id: 1, name: "Datt is Barsch"}
      {id: 10, name: "Datt is DÖNER!!!"}
    ]
    selected: [
      2, 5
    ]
  }
  {
    schoolyear:
      id: 3
      label: "2015-3"
      isActive: true
    allClasses: [
      {id: 2, name: "Datt is Wacken"}
      {id: 5, name: "Datt is Schinken"}
      {id: 1, name: "Datt is Barsch"}
      {id: 10, name: "Datt is DÖNER!!!"}
    ]
    selected: [
      1
    ]
  }
]

React.render(
  <Box data={DATA} />
  $('#additional-settings')[0]
)