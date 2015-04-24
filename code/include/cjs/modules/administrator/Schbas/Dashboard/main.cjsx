React = require 'react'
PreparationPanel = require './lib/Preparation/PreparationPanel.js'
InventoryPanel = require './lib/Inventory/InventoryPanel'

renderInventoryPanel = ->
  React.render(
    <InventoryPanel />
    $('#inventory-panel > div.panel-body')[0]
  )

renderPreparationPanel = ->
  React.render(
    <PreparationPanel />
    $('#preparation-panel > div.panel-body')[0]
  )

renderInventoryPanel()
renderPreparationPanel()