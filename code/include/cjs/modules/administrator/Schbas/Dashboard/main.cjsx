React = require 'react'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
Panel = require 'react-bootstrap/lib/Panel'

PreparationPanel = require './lib/Preparation/PreparationPanel.js'
InventoryPanel = require './lib/Inventory/InventoryPanel'
StatisticsRow = require './lib/Statistics/StatisticsRow'

DashboardBox = React.createClass(
  render: ->
    <div>
      <StatisticsRow />
      <Row>
        <Col sm={12}>
          <Panel className='panel-dashboard' header={<h4>Schbas</h4>}>
            Kommt vielleicht später :)
          </Panel>
        </Col>
        <Col sm={12} lg={6}>
          <Panel className='panel-dashboard' header={<h4>Bücher</h4>}>
            Kommt vielleicht später :)
          </Panel>
        </Col>
        <Col sm={12} lg={6}>
          <Panel className='panel-dashboard' header={<h4>Exemplare</h4>}>
            <InventoryPanel />
          </Panel>
        </Col>
        <Col sm={12}>
          <Panel className='panel-dashboard' header={<h4>Vorbereitungen</h4>}>
            <PreparationPanel />
          </Panel>
        </Col>
      </Row>
    </div>
)



React.render(
  <DashboardBox />
  $('#react')[0]
)
