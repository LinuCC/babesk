// Generated by Coffeescript
var Grade, MenuItem, React, SplitButton;

React = require('react');

React.Bootstrap = require('react-bootstrap');

SplitButton = React.Bootstrap.SplitButton;

MenuItem = React.Bootstrap.MenuItem;

Grade = require('./Grade');

module.exports = React.createClass({
  getDefaultProps: function() {
    return {
      handleGradeDelete: function(id, label) {
        return console.log(id);
      },
      handleGradelevelDelete: function(level) {
        return console.log(level);
      }
    };
  },
  handleDelete: function() {
    return this.props.handleGradelevelDelete(this.props.data.level);
  },
  handleGradeDelete: function(id, label) {
    return this.props.handleGradeDelete(id, this.props.data.level + label);
  },
  render: function() {
    var grade, title, usersAssigned, _i, _len, _ref;
    usersAssigned = 0;
    _ref = this.props.data.grades;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      grade = _ref[_i];
      usersAssigned += parseInt(grade.usersAssigned);
    }
    title = [];
    title.push(React.createElement("span", null, "Jahrgang ", this.props.data.level));
    title.push(React.createElement("i", null, "\u00a0(", usersAssigned, ")"));
    return React.createElement("tr", null, React.createElement("td", null, React.createElement(SplitButton, {
      "bsStyle": 'default',
      "title": title
    }, React.createElement(MenuItem, {
      "eventKey": 'delete',
      "onClick": this.handleDelete
    }, "Zuweisungen entfernen"))), React.createElement("td", {
      "className": 'grade-cell'
    }, React.createElement("ul", null, this.props.data.grades.map((function(_this) {
      return function(grade) {
        return React.createElement(Grade, {
          "key": grade.id,
          "gradelevel": _this.props.data.level,
          "data": grade,
          "handleGradeDelete": _this.handleGradeDelete
        });
      };
    })(this)))));
  }
});
