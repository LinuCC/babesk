React = require 'react'
PreparationPanel = require './lib/Preparation/PreparationPanel.js'

renderPrepararationPanel = ->
  React.render(
    <PreparationPanel />
    $('#preparation-panel > div.panel-body')[0]
  )

renderPrepararationPanel()