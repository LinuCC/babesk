React = require 'react'
PreparationPanel = require './lib/Preparation/PreparationPanel.js'

    $.ajax(
      type: 'POST'
      url: 'index.php?module=administrator|Schbas|Dashboard|Preparation'
      dataType: 'json'

    )
renderPrepararationPanel = ->
  React.render(
    <PreparationPanel />
    $('#preparation-panel > div.panel-body')[0]
  )

renderPrepararationPanel()