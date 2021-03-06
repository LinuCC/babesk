// Generated by Coffeescript
var AssignmentController, ButtonGroup, Deadlines, PrepSchoolyear, PreparationPanel, React, SchbasClaimStatus;

React = require('react');

React.Bootstrap = require('react-bootstrap');

ButtonGroup = React.Bootstrap.ButtonGroup;

PrepSchoolyear = require('./PrepSchoolyear');

AssignmentController = require('./AssignmentController');

SchbasClaimStatus = require('./SchbasClaimStatus');

Deadlines = require('./Deadlines');

PreparationPanel = React.createClass({
  getInitialState: function() {
    return {
      prepSchoolyear: {
        active: {
          id: 0,
          name: "???",
          entriesExist: false
        },
        alternatives: []
      },
      schbasClaimStatus: false,
      deadlines: {
        schbasDeadlineTransfer: new Date('1999-01-01'),
        schbasDeadlineClaim: new Date('1999-01-01')
      }
    };
  },
  componentDidMount: function() {
    return $.getJSON('index.php?module=administrator|Schbas|Dashboard|Preparation').done((function(_this) {
      return function(res) {
        if (_this.isMounted()) {
          return _this.setState(res);
        }
      };
    })(this)).fail(function(jqxhr) {
      return toastr.error(jqxhr.responseText, 'Fehler beim Abrufen der Daten');
    });
  },
  handleSchoolyearChange: function(schoolyearId) {
    return bootbox.confirm('Wollen sie das Vorbereitungs-Schuljahr wirklich wechseln?', (function(_this) {
      return function(res) {
        if (res) {
          return $.get('index.php?module=administrator|Schbas|Dashboard|Preparation|Schoolyear', {
            schoolyearId: schoolyearId,
            action: 'change'
          }).done(function(res) {
            var newActive, oldActive, prepSy;
            prepSy = _this.state.prepSchoolyear;
            oldActive = prepSy.active;
            newActive = prepSy.alternatives.filter(function(sy) {
              return sy.id === schoolyearId;
            });
            if (newActive.length === 1) {
              prepSy.active = newActive[0];
            } else {
              toastr.error('Fehler beim Wechseln des Schuljahres');
              return false;
            }
            prepSy.alternatives.push(oldActive);
            prepSy.alternatives = prepSy.alternatives.filter(function(sy) {
              return sy.id !== schoolyearId;
            });
            return _this.setState({
              prepSchoolyear: prepSy
            });
          }).fail(function(jqxhr) {
            return toastr.error(jqxhr.responseText, 'Fehler');
          });
        }
      };
    })(this));
  },
  handleEditAssignments: function() {
    return window.location = 'index.php?module=administrator|Schbas|BookAssignments|View';
  },
  handleGenerateAssignments: function() {
    return window.location = 'index.php?module=administrator|Schbas|BookAssignments|Generate';
  },
  handleDeleteAssignments: function() {
    return bootbox.confirm("Wollen sie die Buchzuweisungen für das Jahr " + this.state.prepSchoolyear.active.name + " wirklich löschen?", (function(_this) {
      return function(res) {
        if (res) {
          return bootbox.confirm('Wirklich wirklich??', function(res) {
            if (res) {
              return $.get('index.php?module=administrator|Schbas|BookAssignments|Delete', {
                schoolyearId: _this.state.prepSchoolyear.active.id
              }).done(function(res) {
                _this.setState(function(prevState, props) {
                  return prevState.prepSchoolyear.active.entriesExist = false;
                });
                return toastr.success(res, 'Erfolgreich');
              }).fail(function(jqxhr) {
                return toastr.error(jqxhr.responseText, 'Fehler');
              });
            }
          });
        }
      };
    })(this));
  },
  handleSchbasClaimStatusChanged: function(status) {
    return bootbox.confirm('Wollen sie den Rückmeldeformular-Status wirklich verändern?', (function(_this) {
      return function(res) {
        if (res) {
          return $.get('index.php?module=administrator|Schbas|Dashboard|Preparation|SchbasClaimStatus', {
            newStatus: status
          }).done(function(res) {
            return _this.setState({
              schbasClaimStatus: status
            });
          }).fail(function(jqxhr) {
            return toastr.error(jqxhr.responseText, 'Fehler');
          });
        }
      };
    })(this));
  },
  handleDeadlineChanged: function(deadlineVal, type) {
    return $.get('index.php?module=administrator|System|GlobalSettings|Change', {
      name: type,
      value: deadlineVal
    }).done((function(_this) {
      return function(res) {
        var deadlines;
        deadlines = _this.state.deadlines;
        deadlines[type] = deadlineVal;
        _this.setState({
          deadlines: deadlines
        });
        console.log(_this.state);
        return toastr.success(res, 'Erfolgreich');
      };
    })(this)).fail(function(jqxhr) {
      return toastr.error(jqxhr.responseText, 'Fehler beim Ändern der Deadline');
    });
  },
  render: function() {
    return React.createElement("div", null, ((function() {

      /*Single buttons need to be wrapped with their own ButtonGroup */
    })()), React.createElement(ButtonGroup, {
      "justified": true
    }, React.createElement(PrepSchoolyear, {
      "schoolyears": this.state.prepSchoolyear,
      "handleSchoolyearChange": this.handleSchoolyearChange
    }), React.createElement(AssignmentController, {
      "prepSchoolyear": this.state.prepSchoolyear.active,
      "handleEdit": this.handleEditAssignments,
      "handleGenerate": this.handleGenerateAssignments,
      "handleDelete": this.handleDeleteAssignments
    }), React.createElement(ButtonGroup, null, React.createElement(SchbasClaimStatus, {
      "status": this.state.schbasClaimStatus,
      "handleStatusChanged": this.handleSchbasClaimStatusChanged
    }))), React.createElement("hr", null), React.createElement("legend", null, "Deadlines"), React.createElement(Deadlines, {
      "deadlines": this.state.deadlines,
      "onChange": this.handleDeadlineChanged
    }));
  }
});

module.exports = PreparationPanel;
