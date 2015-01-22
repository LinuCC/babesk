$(document).ready ->

  changeSelections = ($btnSwitch, state) ->
    alert 'ToDo'

  displaySelectHostGroup = ->
    hosts = ["abc", "cde", "zdf"]
    form = "<div class='row'>
              <div class='col-md-4'>
                <label for='host-group-select'>Gruppe auswählen:</label>
              </div>
              <div class='col-md-8'>
                <select id='host-group-select' name='host-group-select'>
                </select>
              </div>
            </div>"
    bootbox.dialog(
      title: "Ändern der Gruppe der Lehrer",
      message: form
      "buttons":
          success:
            label: "Gruppe ändern"
            className: "btn-success"
            callback: ->
              alert 'ToDo'
    )

  $selectionsSwitch = $('#enable-selections')
  $selectionsSwitch.bootstrapSwitch()

  $selectionsSwitch.on 'switchChange.bootstrapSwitch', (event, state)->
    stateText = if state then "aktivieren" else "deaktivieren"
    $btnSwitch = $(this)
    bootbox.confirm(
      "Wollen sie die Wahlen für die Schüler wirklich #{stateText}?",
      (res)->
        if res
          changeSelections $btnSwitch, state
        else
          $btnSwitch.bootstrapSwitch 'state', not state, true
    )

  $('a#select-host-group-button').on 'click', (event)->
    displaySelectHostGroup()
