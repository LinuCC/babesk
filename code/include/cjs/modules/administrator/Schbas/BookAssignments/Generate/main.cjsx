React = require 'react'
React.Bootstrap = require 'react-bootstrap'
AssignmentBox = require('./lib/AssignmentBox.js').AssignmentBox

DATA = {
  assignmentsForSchoolyearExist: false,
  bookAssignmentsForGrades: [
    {
      gradelevel: 7,
      books: [
        {
          name: "Französisch 7",
          link: "index.php?module=administrator|Schbas|ShowBook&id=234"
        }
      ]
    },
    {
      gradelevel: 8,
      books: [
        {
          name: "Französisch 8",
          link: "index.php?module=administrator|Schbas|ShowBook&id=134"
        },
        {
          name: "Latein 8",
          link: "index.php?module=administrator|Schbas|ShowBook&id=244"
        },
        {
          name: "Chemie 8/9",
          link: "index.php?module=administrator|Schbas|ShowBook&id=256"
        },
      ]
    },
    {
      gradelevel: 9,
      books: [
        {
          name: "Französisch 8",
          link: "index.php?module=administrator|Schbas|ShowBook&id=134"
        },
        {
          name: "Latein 8",
          link: "index.php?module=administrator|Schbas|ShowBook&id=244"
        },
        {
          name: "Chemie 8/9",
          link: "index.php?module=administrator|Schbas|ShowBook&id=256"
        },
      ]
    }
  ]
}

$.ajax
  type: 'GET'
  url: 'index.php?module=administrator|Schbas|BookAssignments|Generate'
  data: 'general-information'
  dataType: 'json'
  success: (data, statusText, jqXHR)->
    DATA = data
    React.render(
      <AssignmentBox data={DATA} />
      $('#entry')[0]
    )
  error: (jqXHR, statusText, errorThrown)->
    toastr.error 'Konnte die Daten nicht abrufen'