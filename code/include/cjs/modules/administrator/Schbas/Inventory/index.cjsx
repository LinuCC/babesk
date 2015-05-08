React = require 'react'
Button = require 'react-bootstrap/lib/Button'
Icon = require 'lib/FontAwesomeIcon'
Panel = require 'react-bootstrap/lib/Panel'
Row = require 'react-bootstrap/lib/Row'
Col = require 'react-bootstrap/lib/Col'
Table = require 'react-bootstrap/lib/Table'
Input = require 'react-bootstrap/lib/Input'
NProgress = require 'nprogress'
Paginator = require 'lib/Paginator'

IndexBox = React.createClass(

  getInitialState: ->
    return {
      activePage: 1
      entriesPerPage: 10
      pageCount: 1
      columns: ['id', 'barcode', 'lentUser']
      displayColumns: ['id', 'barcode', 'lentUser']
      filter: ''
      sort: ''
      data: []
    }

  componentDidMount: ->
    @updateData()

  updateData: ->
    NProgress.start()
    $.getJSON(
      'index.php?module=administrator|Schbas|Inventory&index&ajax'
      {
        filter: @state.filter
        sort: @state.sort
        activePage: @state.activePage
        entriesPerPage: @state.entriesPerPage
        displayColumns: @state.displayColumns
      }
    ) .done (res)=>
        stateTemp = @state
        stateTemp.data = res.data
        stateTemp.pageCount = parseInt(res.pageCount)
        @setState stateTemp
        NProgress.done()
      .fail (jqxhr)->
        toastr.error jqxhr.responseText, 'Fehler'
        NProgress.done()

  handleChangeActivePage: (pagenum)->
    @setState(activePage: pagenum, @updateData)

  handleSearch: ->
    console.log "res"
    @updateData()

  handleFilterChange: (event)->
    @setState filter: event.target.value

  render: ->
    searchButton = <Button onClick={@handleSearch} >
      <Icon name='search' />
    </Button>
    <Panel className='panel-dashboard' header={<h4>Inventar Übersicht</h4>}>
      <Row className='text-center'>
        <Col md={4}>
          <Input type='text' value={@state.filter}
            onChange={@handleFilterChange}
            buttonAfter={searchButton} />
        </Col>
        <Col md={4}>
          <Paginator maxPages={10} numPages={@state.pageCount}
            onClick={@handleChangeActivePage} />
        </Col>
        <Col md={4}>
        </Col>
      </Row>
      <Row>
        <Col xs={12}>
          <InventoryTable dataRows={@state.data}
            displayColumns={@state.displayColumns} />
        </Col>
      </Row>
    </Panel>
)

InventoryTable = React.createClass(

  getDefaultProps: ->
    return {
      displayColumns: ['id', 'barcode', 'lentUser']
      dataRows: []
    }

  render: ->
    <Table>
      <thead>
        <tr>
          {
            @props.displayColumns.map (column, index)->
              <th key={index}>{column}</th>
          }
        </tr>
      </thead>
      <tbody>
          {
            @props.dataRows.map (row)=>
              <tr key={row.id}>
                {
                  @props.displayColumns.map (column)=>
                    <td>
                      {
                        if row[column]?
                          if column is 'lentUser'
                            @renderLentUser row[column]
                          else
                            row[column]
                      }
                    </td>
                }
              </tr>
          }
      </tbody>
    </Table>

  renderLentUser: (data)->
    return (
      <a href="index.php?module=administrator|System|Users&id=#{data.id}">
        {data.username}
      </a>
    )
)

React.render(
  <IndexBox />
  $('#entry')[0]
)