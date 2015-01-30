$(document).ready ->

  $table = $('table#selection-table')
  $table.find('tbody > tr > td.category-row input').hide()
  # $btns.on 'click', (event)->
  #   #"Unclick" other buttons
  #   $btns.filter('.btn-primary').each ->
  #     $(this).toggleClass 'btn-primary'
  #     $(this).toggleClass 'btn-success'
  #     $(this).find('span.status-text').text 'Frei'
  #   $this = $(this)
  #   $this.removeClass 'btn-success'
  #   $this.addClass 'btn-primary'
  #   $this.find('span.status-text').text 'Anmelden'
  #   $this.find('input.meetings').prop 'checked', true
  #   return false

  $('form#selection-form input#selection-submit').on 'click', (event)->
    event.preventDefault()
    if not $('form#selection-form input.meetings:checked').length
      bootbox.alert 'Bitte wählen sie eine Anmeldung aus, um sie bestätigen zu
        können'
      return
    bootbox.confirm(
      'Die Anmeldung ist bindend und kann nicht verändert werden.
      Sind sie sich sicher?',
      (res)->
        if res
          $('form#selection-form').submit()
    )

  #Very simple button-toggle, should work on all major Browsers without problem
  $('form#selection-form div.btn-toggle').on 'click', ->
    $div = $(this)
    $toggle = $div.find('input.meetings')
    # If user wants to disable toggle, dont toggle off and then on again
    if not $toggle.prop 'checked'
      disableAllToggles()
    toggleButtonAppearance($div)
    $toggle.prop 'checked', not $toggle.prop('checked')
    return false

  toggleButtonAppearance = ($btnGroupDiv)->
    if $btnGroupDiv.find('label.btn-primary').length
      $btnGroupDiv.find('label.btn').toggleClass 'btn-primary'
    if $btnGroupDiv.find('label.btn-danger').length
      $btnGroupDiv.find('label.btn').toggleClass 'btn-danger'
    if $btnGroupDiv.find('label.btn-success').length
      $btnGroupDiv.find('label.btn').toggleClass 'btn-success'
    if $btnGroupDiv.find('label.btn-info').length
      $btnGroupDiv.find('label.btn').toggleClass 'btn-info'
    $btnGroupDiv.find('label.btn').toggleClass 'btn-default'

  disableAllToggles = ()->
    $alreadyToggled = $(
        'form#selection-form div.btn-toggle input.meetings:checked'
      ).closest 'div.btn-toggle'
    toggleButtonAppearance $alreadyToggled
    $alreadyToggled.find('input.meetings').prop 'checked', false