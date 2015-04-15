React = require 'react'
BooksToUsersAssignments = require './lib/BooksToUsersAssignments'

renderBooksToUsersAssignments = ->
  React.render(
    <BooksToUsersAssignments />
    $('#view-panel > div.panel-body')[0]
  )

renderBooksToUsersAssignments()

