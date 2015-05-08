React = require 'react'

range = (start, stop)->
  if arguments.length <= 1
    stop = start or 0
    start = 0
  length= Math.max(stop - start, 0)
  idx = 0
  arr = new Array length
  while idx < length
    arr[idx++] = start
    start += 1
  return arr

module.exports = React.createClass(

  propTypes:
    numPages: React.PropTypes.number.isRequired
    maxPages: React.PropTypes.number
    onClick: React.PropTypes.func

  getDefaultProps: ->
    return {
      maxPages: 3
    }

  getInitialState: ->
    return {
      page: 1
    }

  onClick: (n)->
    if n > @props.numPages or n < 1
      return
    if @props.onClick then @props.onClick n
    @setState page: n

  getDisplayCount: ->
    if @props.numPages > @props.maxPages
      return @props.maxPages
    return @props.numPages

  getPageRange: ->
    displayCount = @getDisplayCount()
    page = @state.page
    idx = (page - 1) % displayCount
    start = page - idx
    remaining = @props.numPages - page
    if page > displayCount and remaining < displayCount
      start = @props.numPages - displayCount + 1
    return range start, start + displayCount

  preventDefault: (e)->
    e.preventDefault()

  render: ->
    page = @state.page
    prevClassName = if page is 1 then 'disabled' else ''
    nextClassName = if page >= @props.numPages then 'disabled' else ''
    <ul className='pagination'>
      <li className={prevClassName} onClick={@onClick.bind(null, page - 1)}>
        <a href='#' onClick={@preventDefault}>
          <i className='fa fa-chevron-left' />
        </a>
      </li>
      {@getPageRange().map @renderPage}
      <li className={nextClassName} onClick={@onClick.bind(null, page + 1)}>
        <a href='#' onClick={@preventDefault}>
          <i className='fa fa-chevron-right' />
        </a>
      </li>
    </ul>

  renderPage: (n, i)->
    cls = if @state.page is n then 'active' else ''
    return (
      <li key={i} className={cls} onClick={@onClick.bind(null, n)}>
        <a href='#' onClick={@preventDefault}>{n}</a>
      </li>
    )
)