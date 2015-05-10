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
Select = require 'react-select'

IndexBox = React.createClass(

  getInitialState: ->
    return {
      activePage: 1
      entriesPerPage: 10
      pageCount: 1
      columns: [
        'id', 'barcode', 'lentUser', 'bookTitle', 'bookIsbn', 'subjectName',
        'bookAuthor'
      ]
      columnTranslations:
        id: 'ID'
        barcode: 'Barcode'
        lentUser: 'Verliehen an'
        bookTitle: 'Buchtitel'
        bookAuthor: 'Buchauthor'
        bookIsbn: 'ISBN'
        subjectName: 'Fach'
      displayColumns: ['barcode', 'lentUser']
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
    @updateData()

  handleFilterChange: (event)->
    @setState filter: event.target.value

  handleFilterKeyDown: (event)->
    if event.key is 'Enter'
      @handleSearch()

  handleSelectedColumnsChange: (values)->
    if values.indexOf('barcode') < 0
      values.unshift 'barcode'
    @setState displayColumns: values

  render: ->
    searchButton = <Button onClick={@handleSearch} >
      <Icon name='search' />
    </Button>
    <Panel className='panel-dashboard' header={<h4>Inventar Übersicht</h4>}>
      <Row className='text-center'>
        <Col md={4}>
          <Input type='text' value={@state.filter}
            onChange={@handleFilterChange} onKeyDown={@handleFilterKeyDown}
            buttonAfter={searchButton} />
        </Col>
        <Col md={4}>
          <Paginator maxPages={10} numPages={@state.pageCount}
            onClick={@handleChangeActivePage} />
        </Col>
        <Col md={4}>
          <ColumnDisplaySelect columnTranslations={@state.columnTranslations}
            columns={@state.columns} onChange={@handleSelectedColumnsChange}
            displayColumns={@state.displayColumns} />
        </Col>
      </Row>
      <Row>
        <Col xs={12}>
          <InventoryTable columnTranslations={@state.columnTranslations}
            dataRows={@state.data} displayColumns={@state.displayColumns} />
        </Col>
      </Row>
    </Panel>
)

ColumnDisplaySelect = React.createClass(

  propTypes:
    columns: React.PropTypes.array,
    columnTranslations: React.PropTypes.array,
    onChange: React.PropTypes.func

  getDefaultProps: ->
    columns: []
    columnTranslations: []
    onChange: (values)-> console.log values

  handleChange: (value)->
    values = value.split ','
    # Remove void string from array in case it exists
    if values.indexOf('') > -1
      pos = values.indexOf('')
      values.splice pos, pos + 1
    @props.onChange values

  render: ->
    possibleCols = @props.columns.map (col)=>
      if @props.columnTranslations[col]?
        return {label: @props.columnTranslations[col], value: col}
      else
        return {label: col, value: col}
    <Select multi={true} placeholder='Spaltenanzeige' options={possibleCols}
      onChange={@handleChange} value={@props.displayColumns} />
)

InventoryTable = React.createClass(

  getDefaultProps: ->
    return {
      displayColumns: ['id', 'barcode', 'lentUser']
      columnTranslations: {}
      dataRows: []
    }

  render: ->
    <Table>
      <thead>
        <tr>
          {
            @props.displayColumns.map (column, index)=>
              if @props.columnTranslations[column]?
                columnName = @props.columnTranslations[column]
              else
                columnName = column
              <th key={index}>{columnName}</th>
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