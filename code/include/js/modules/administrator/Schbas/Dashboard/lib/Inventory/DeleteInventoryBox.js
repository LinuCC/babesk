// Generated by Coffeescript
var Button, Icon, Input, React, classnames;

React = require('react');

Button = require('react-bootstrap/lib/Button');

Input = require('react-bootstrap/lib/Input');

Icon = require('lib/FontAwesomeIcon');

classnames = require('classnames');

module.exports = React.createClass({
  getInitialState: function() {
    return {
      deleteBarcodeValue: ''
    };
  },
  getDefaultProps: function() {
    return {
      barcodes: [],
      onDeleteBarcode: function() {
        return {};
      },
      onDeleteBarcodeRemove: function(barcodeIndex) {
        return console.log(barcodeIndex);
      }
    };
  },
  handleDeleteBarcode: function() {
    this.props.onDeleteBarcode(this.state.deleteBarcodeValue);
    return this.setState({
      deleteBarcodeValue: ''
    });
  },
  handleDeleteBarcodeKeyPress: function(event) {
    if (event.which === 13) {
      return this.handleDeleteBarcode();
    }
  },
  handleDeleteBarcodeValueChange: function(event) {
    return this.setState({
      deleteBarcodeValue: event.target.value
    });
  },
  deleteButton: function(barcodeIndex) {
    return React.createElement(Button, {
      "bsStyle": 'danger',
      "onClick": this.props.onDeleteBarcodeRemove.bind(null, barcodeIndex)
    }, React.createElement(Icon, {
      "name": 'trash-o',
      "fixedWidth": true
    }));
  },
  render: function() {
    var addButton;
    addButton = React.createElement(Button, {
      "bsStyle": 'success',
      "onClick": this.handleDeleteBarcode
    }, React.createElement(Icon, {
      "name": 'plus-square-o',
      "fixedWidth": true
    }));
    return React.createElement("div", null, this.props.barcodes.map((function(_this) {
      return function(barcode, index) {
        return React.createElement(Input, {
          "type": 'text',
          "key": index,
          "value": barcode,
          "readOnly": true,
          "buttonAfter": _this.deleteButton(index)
        });
      };
    })(this)), React.createElement(Input, {
      "type": 'text',
      "value": this.state.deleteBarcodeValue,
      "buttonAfter": addButton,
      "onKeyPress": this.handleDeleteBarcodeKeyPress,
      "onChange": this.handleDeleteBarcodeValueChange
    }));
  }
});
