// Generated by Coffeescript
var Button, CheckboxButton, React, classnames;

React = require('react');

React.Bootstrap = require('react-bootstrap');

Button = React.Bootstrap.Button;

classnames = require('classnames');

CheckboxButton = React.createClass({
  getDefaultProps: function() {
    return {
      offText: '',
      onText: '',
      offStyle: 'default',
      onStyle: 'primary',
      onClick: function() {
        return true;
      }
    };
  },
  onClick: function() {
    return this.props.onChange(!this.props.checked);
  },
  render: function() {
    var bsStyle, iconClasses;
    bsStyle = this.props.checked ? this.props.onStyle : this.props.offStyle;
    iconClasses = classnames({
      'fa fa-fw': true,
      'fa-square-o': !this.props.checked,
      'fa-check-square-o': this.props.checked
    });
    return React.createElement(Button, {
      "bsStyle": bsStyle,
      "onClick": this.onClick,
      "active": this.props.checked
    }, React.createElement("i", {
      "className": iconClasses
    }), "\u00a0", (this.props.checked ? this.props.onText : this.props.offText), this.props.children);
  }
});

module.exports = CheckboxButton;
