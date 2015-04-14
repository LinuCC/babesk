React = require 'react'
React.Bootstrap = require 'react-bootstrap'
AssignmentBox = require('./lib/AssignmentBox.js')

renderAssignmentBox = (overviewData)->
  React.render(
    <AssignmentBox data={overviewData} />
    $('#entry')[0]
  )

$.ajax
  type: 'GET'
  url: 'index.php?module=administrator|Schbas|BookAssignments|Generate'
  data: 'overview-infos'
  dataType: 'json'
  success: (data, statusText, jqXHR)->
    if data.value? and data.value is 'error'
      toastr.error 'Konnte die Daten nicht abrufen'
      return
    renderAssignmentBox(data)

  error: (jqXHR, statusText, errorThrown)->
    console.log jqXHR
    toastr.error 'Konnte die Daten nicht abrufen'