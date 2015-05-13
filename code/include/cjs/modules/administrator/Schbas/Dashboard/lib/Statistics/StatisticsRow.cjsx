React = require 'react'
Please = require 'pleasejs'
Panel = require 'react-bootstrap/lib/Panel'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
ChartJs = require 'chart.js'
DoughnutChart = require('react-chartjs').Doughnut
NProgress = require 'nprogress'


ChartJs.defaults.global.responsive = true

module.exports = React.createClass(

  getInitialState: ->
    return {
      gradelevelLendStatistics: []
      subjectLendStatistics: []
    }

  componentDidMount: ->
    NProgress.start()
    $.getJSON(
      'index.php?module=administrator|Schbas|Dashboard|Statistics'
    ) .done (res)=>
        console.log res
        @setState res
        NProgress.done()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler'
        NProgress.done()

  render: ->
    # Extra div fixes issue for chart.js, should be fixed in 1.1
    <Row>
      <Col smOffset={4} sm={4} mdOffset={3} md={3}>
        <Panel className='panel-chart' header='Ausleihen pro Jahrgang'>
          <div>
            <DoughnutStatsChart values={@state.gradelevelLendStatistics} />
          </div>
        </Panel>
      </Col>
      <Col sm={4} md={3}>
        <Panel className='panel-chart' header='Ausleihen pro Fach'>
          <div>
            <DoughnutStatsChart values={@state.subjectLendStatistics} />
          </div>
        </Panel>
      </Col>
    </Row>
)


DoughnutStatsChart = React.createClass(

  getDefaultProps: ->
    return {
      values: []
      header: ''
    }

  render: ->
    <div>
      {
        if @props.values.length
          @props.values.map (el)->
            el.color = Please.make_color saturation: 0.7
            return el
          <DoughnutChart data={@props.values} />
        else
          <p>---</p>
      }
    </div>
)