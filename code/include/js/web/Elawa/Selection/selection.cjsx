$(document).ready ->

  $table = $('table#selection-table')
  $btns = $table.find(
    'tbody > tr > td.category-row label.meeting-status-button'
  )
  $table.find('tbody > tr > td.category-row input').hide()
  $btns.on 'click', (event)->
    #"Unclick" other buttons
    $btns.filter('.btn-primary').each ->
      $(this).toggleClass 'btn-primary'
      $(this).toggleClass 'btn-success'
      $(this).find('span.status-text').text 'Frei'
    $this = $(this)
    $this.removeClass 'btn-success'
    $this.addClass 'btn-primary'
    $this.find('span.status-text').text 'Anmelden'
    $this.find('input.meetings').prop 'checked', true
    return false
