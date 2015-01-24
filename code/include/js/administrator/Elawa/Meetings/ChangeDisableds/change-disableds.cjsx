$(document).ready ->

  updateStatuses = ()->
    fetchStatuses = (hostId)->
      $.ajax
        type: 'POST'
        url: 'index.php?module=administrator|Elawa|Meetings|ChangeDisableds'
        data:
          hostId: hostId
        success: (data, statusText, jqXHR)->
          console.log data
        error: (jqXHR, textStatus, errorThrown)->
          toastr.error 'Ein Fehler ist beim holen der Daten aufgetreten.'
          console.log jqXHR
    hostId = $('#hosts-select option:selected').attr 'value'
    fetchStatuses hostId


  $('#hosts-select').multiselect
    maxHeight: 400
    enableFiltering: true
    enableCaseInsensitiveFiltering: true
    filterPlaceholder: 'Suche nach...'
    #Replace icons with our icon-set
    templates:
      filter: '<li class="multiselect-item filter"><div class="input-group">
        <span class="input-group-addon"><i class="icon icon-search">
        </i></span><input class="form-control multiselect-search" type="text">
        </div></li>'
      filterClearBtn: '<span class="input-group-btn">
        <button class="btn btn-default multiselect-clear-filter" type="button">
        <i class="icon icon-edit"></i></button></span>'
    onChange: (element, checked)->
      updateStatuses()

  updateStatuses()

