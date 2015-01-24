$(document).ready ->

  updateStatuses = ->

    fetchStatuses = (hostId)->
      $.ajax
        type: 'POST'
        url: 'index.php?module=administrator|Elawa|Meetings|ChangeDisableds'
        data:
          hostId: hostId
        dataType: 'json'
        success: (data, statusText, jqXHR)->
          if jqXHR.status is 200
            console.log data
            insertStatuses data
          else if jqXHR.status is 204
            toastr.info 'Es sind keine Sprechzeiten für diesen Benutzer
             eingetragen', 'Keine Sprechzeiten'
        error: (jqXHR, textStatus, errorThrown)->
          toastr.error 'Ein Fehler ist beim holen der Daten aufgetreten.'
          console.log jqXHR

    insertStatuses = (data)->
      $tbody = $('table#meeting-statuses > tbody');
      $thead = $('table#meeting-statuses > thead');

      #Gets all Categories of the given data and puts them into an array
      getAllCategories = (data)->
        categories = []
        for index, val of data
          if $.inArray(val['category'], categories) is -1
            categories.push val['category']
        return categories

      setTableHeaders = (categories)->
        $headRow = $('<tr></tr>')
        $headRow.append "<th class='time'>Zeit</th>"
        $headRow.append "<th class='length'>Länge</th>"
        for index, category of categories
          $headRow.append "<th class='category' data-name='#{category}'>
            #{category}</th>
          "
        $thead.append $headRow

      #Each row has the time and length as attributes so that same entries
      #with different categories can be shown together
      fillTableBody = (categories, data)->
        for index, val of data
          $row = $tbody.children(
            "tr[data-time=\"#{val['time']}\"][data-length=\"#{val['length']}\"]"
          )
          if not $row.length
            $row = $(
              "<tr data-time='#{val['time']}' data-length='#{val['length']}'>
                <td class='time'>#{val['time']}</td>
                <td class='length'>#{val['length']}</td>
              </tr>"
            )
            for category in categories
              $row.append "<td class='category' data-name='#{category}'></td>"
            $tbody.append $row
          $toggle = $("<input type='checkbox' data-meeting-id='#{val['id']}'>")
          $toggle.prop 'checked', not val['isDisabled']
          $row.children "td[data-name='#{val['category']}']"
            .append $toggle

      updateSwitches = ->
        $switches = $(
          'table#meeting-statuses > tbody > tr > td.category > input'
        )
        $switches.bootstrapSwitch
          size: 'mini'
          onText: 'AN'
          onColor: 'primary'
          offText: 'PAUSE'
          offColor: 'danger'
        $switches.on 'switchChange.bootstrapSwitch', (event, status)->
          switchChanged $(this), event, status

      categories = getAllCategories(data)
      setTableHeaders(categories)
      fillTableBody(categories, data)
      updateSwitches()

    hostId = $('#hosts-select option:selected').attr 'value'
    $('table#meeting-statuses > thead').html('')
    $('table#meeting-statuses > tbody').html('')
    fetchStatuses(hostId)

  switchChanged = ($switch, event, status)->
    $.ajax
      type: 'POST'
      url: 'index.php?module=administrator|Elawa|Meetings|ChangeDisableds'
      data:
        meetingId: $switch.data('meetingId')
        isDisabled: not status
      success: (data, statusText, jqXHR)->
        console.log data
      error: (jqXHR, textStatus, errorThrown)->
        toastr.error 'Ein Fehler ist beim Verändern des Statuses aufgetreten.'
        $switch.bootstrapSwitch 'state', not status, true
        console.log jqXHR

  $('#hosts-select').multiselect
    maxHeight: 400
    enableFiltering: true
    enableCaseInsensitiveFiltering: true
    filterPlaceholder: 'Suche nach...'
    onChange: (element, checked)->
      updateStatuses()
    #Replace icons with our icon-set
    templates:
      filter: '<li class="multiselect-item filter"><div class="input-group">
        <span class="input-group-addon"><i class="icon icon-search">
        </i></span><input class="form-control multiselect-search" type="text">
        </div></li>'
      filterClearBtn: '<span class="input-group-btn">
        <button class="btn btn-default multiselect-clear-filter" type="button">
        <i class="icon icon-edit"></i></button></span>'

  updateStatuses()

