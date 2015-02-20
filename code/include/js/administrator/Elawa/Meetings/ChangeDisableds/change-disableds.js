// Generated by Coffeescript
$(document).ready(function() {
  var switchChanged, updateStatuses;
  updateStatuses = function() {
    var fetchStatuses, hostId, insertStatuses;
    fetchStatuses = function(hostId) {
      return $.ajax({
        type: 'POST',
        url: 'index.php?module=administrator|Elawa|Meetings|ChangeDisableds',
        data: {
          hostId: hostId
        },
        dataType: 'json',
        success: function(data, statusText, jqXHR) {
          if (jqXHR.status === 200) {
            console.log(data);
            return insertStatuses(data);
          } else if (jqXHR.status === 204) {
            return toastr.info('Es sind keine Sprechzeiten für diesen Benutzer eingetragen', 'Keine Sprechzeiten');
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          toastr.error('Ein Fehler ist beim holen der Daten aufgetreten.');
          return console.log(jqXHR);
        }
      });
    };
    insertStatuses = function(data) {
      var $tbody, $thead, categories, fillTableBody, getAllCategories, setTableHeaders, updateSwitches;
      $tbody = $('table#meeting-statuses > tbody');
      $thead = $('table#meeting-statuses > thead');
      getAllCategories = function(data) {
        var categories, index, val;
        categories = [];
        for (index in data) {
          val = data[index];
          if ($.inArray(val['category'], categories) === -1) {
            categories.push(val['category']);
          }
        }
        return categories;
      };
      setTableHeaders = function(categories) {
        var $globalSwitches, $headRow, category, index;
        $headRow = $('<tr></tr>');
        $headRow.append("<th class='time'>Zeit</th>");
        $headRow.append("<th class='length'>Länge</th>");
        for (index in categories) {
          category = categories[index];
          $headRow.append("<th class='category' data-name='" + category + "'> " + category + " <input type='checkbox' class='global-category-switch' data-category='" + category + "' checked> </th>");
        }
        $thead.append($headRow);
        $globalSwitches = $('table#meeting-statuses > thead > tr > th.category > input.global-category-switch');
        $globalSwitches.bootstrapSwitch({
          size: 'mini',
          onText: 'AN',
          onColor: 'primary',
          offText: 'PAUSE',
          offColor: 'danger'
        });
        return $globalSwitches.on('switchChange.bootstrapSwitch', function(event, status) {
          var $globalSwitch, $switches;
          $globalSwitch = $(this);
          category = $globalSwitch.data('category');
          $switches = $("table#meeting-statuses > tbody > tr > td.category[data-name='" + category + "'] input");
          return $switches.bootstrapSwitch('state', status, false);
        });
      };
      fillTableBody = function(categories, data) {
        var $row, $toggle, category, index, val, _i, _len, _results;
        _results = [];
        for (index in data) {
          val = data[index];
          $row = $tbody.children("tr[data-time=\"" + val['time'] + "\"][data-length=\"" + val['length'] + "\"]");
          if (!$row.length) {
            $row = $("<tr data-time='" + val['time'] + "' data-length='" + val['length'] + "'> <td class='time'>" + val['time'] + "</td> <td class='length'>" + val['length'] + "</td> </tr>");
            for (_i = 0, _len = categories.length; _i < _len; _i++) {
              category = categories[_i];
              $row.append("<td class='category' data-name='" + category + "'></td>");
            }
            $tbody.append($row);
          }
          $toggle = $("<input type='checkbox' data-meeting-id='" + val['id'] + "'>");
          $toggle.prop('checked', !val['isDisabled']);
          _results.push($row.children("td[data-name='" + val['category'] + "']").append($toggle));
        }
        return _results;
      };
      updateSwitches = function() {
        var $switches;
        $switches = $('table#meeting-statuses > tbody > tr > td.category > input');
        $switches.bootstrapSwitch({
          size: 'mini',
          onText: 'AN',
          onColor: 'primary',
          offText: 'PAUSE',
          offColor: 'danger'
        });
        return $switches.on('switchChange.bootstrapSwitch', function(event, status) {
          return switchChanged($(this), event, status);
        });
      };
      categories = getAllCategories(data);
      setTableHeaders(categories);
      fillTableBody(categories, data);
      return updateSwitches();
    };
    hostId = $('#hosts-select option:selected').attr('value');
    $('table#meeting-statuses > thead').html('');
    $('table#meeting-statuses > tbody').html('');
    return fetchStatuses(hostId);
  };
  switchChanged = function($switch, event, status) {
    return $.ajax({
      type: 'POST',
      url: 'index.php?module=administrator|Elawa|Meetings|ChangeDisableds',
      data: {
        meetingId: $switch.data('meetingId'),
        isDisabled: !status
      },
      success: function(data, statusText, jqXHR) {
        return console.log(data);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        toastr.error('Ein Fehler ist beim Verändern des Statuses aufgetreten.');
        $switch.bootstrapSwitch('state', !status, true);
        return console.log(jqXHR);
      }
    });
  };
  $('#hosts-select').multiselect({
    maxHeight: 400,
    enableFiltering: true,
    enableCaseInsensitiveFiltering: true,
    filterPlaceholder: 'Suche nach...',
    onChange: function(element, checked) {
      return updateStatuses();
    },
    templates: {
      filter: '<li class="multiselect-item filter"><div class="input-group"> <span class="input-group-addon"><i class="fa fa-search fa-fw"> </i></span><input class="form-control multiselect-search" type="text"> </div></li>',
      filterClearBtn: '<span class="input-group-btn"> <button class="btn btn-default multiselect-clear-filter" type="button"> <i class="fa fa-pencil"></i></button></span>'
    }
  });
  return updateStatuses();
});
