// Generated by Coffeescript
var App, Button, Col, DateTimePicker, Icon, Input, Label, NProgress, OtherCostsBox, Panel, React, Row;

React = require('react');

Button = require('react-bootstrap/lib/Button');

Icon = require('lib/FontAwesomeIcon');

Panel = require('react-bootstrap/lib/Panel');

Input = require('react-bootstrap/lib/Input');

Row = require('react-bootstrap/lib/Row');

Col = require('react-bootstrap/lib/Col');

Label = require('react-bootstrap/lib/Label');

DateTimePicker = require('react-widgets/lib/DateTimePicker');

NProgress = require('nprogress');

App = React.createClass({
  getInitialState: function() {
    return {
      assistantsCost: 0,
      toolsCost: 0,
      otherCosts: [
        {
          amount: '',
          date: new Date(),
          recipient: ''
        }
      ]
    };
  },
  handleAddOtherCost: function() {
    var otherCosts;
    otherCosts = this.state.otherCosts;
    otherCosts.push({
      amount: '',
      date: new Date(),
      recipient: ''
    });
    return this.setState({
      otherCosts: otherCosts
    });
  },
  handleAssistantsCostChange: function(event) {
    return this.setState({
      assistantsCost: event.target.value
    });
  },
  handleToolsCostChange: function(event) {
    return this.setState({
      toolsCost: event.target.value
    });
  },
  handleOtherCostChange: function(index, key, value) {
    var otherCosts;
    otherCosts = this.state.otherCosts;
    otherCosts[index][key] = value;
    return this.setState({
      otherCosts: otherCosts
    });
  },
  handlePrint: function() {
    var params, state;
    state = $.extend(true, {}, this.state);
    state.otherCosts.map(function(otherCost, index) {
      return state.otherCosts[index].date = otherCost.date.toISOString().substr(0, 10);
    });
    params = $.param(state);
    params += '&action=pdf';
    return window.open("index.php?module=administrator|Statistics|SchbasStatistics&" + params, '_blank');
  },
  render: function() {
    var footer;
    footer = React.createElement("div", null, React.createElement(Button, {
      "bsStyle": 'primary',
      "onClick": this.handlePrint,
      "className": 'pull-right'
    }, "PDF erstellen"), React.createElement("div", {
      "className": 'clearfix'
    }));
    return React.createElement(Panel, {
      "className": 'panel-dashboard',
      "header": React.createElement("h4", null, "Schbas-Statistik PDF"),
      "footer": footer
    }, React.createElement("legend", null, "Zus\u00e4tzliche Angaben:"), React.createElement("form", {
      "className": 'form-horizontal'
    }, React.createElement(Input, {
      "type": 'text',
      "value": this.state.assistantsCost,
      "label": 'Kosten für Hilfskräfte',
      "labelClassName": 'col-xs-2',
      "wrapperClassName": 'col-xs-10',
      "onChange": this.handleAssistantsCostChange,
      "addonBefore": React.createElement(Icon, {
        "name": 'user',
        "fixedWidth": true
      })
    }), React.createElement(Input, {
      "type": 'text',
      "value": this.state.toolsCost,
      "label": 'Kosten für Hilfsmittel',
      "labelClassName": 'col-xs-2',
      "wrapperClassName": 'col-xs-10',
      "onChange": this.handleToolsCostChange,
      "addonBefore": React.createElement(Icon, {
        "name": 'wrench',
        "fixedWidth": true
      })
    }), this.state.otherCosts.map((function(_this) {
      return function(otherCost, index) {
        return React.createElement(OtherCostsBox, React.__spread({
          "key": index
        }, otherCost, {
          "onChange": _this.handleOtherCostChange.bind(null, index)
        }));
      };
    })(this)), React.createElement(Button, {
      "bsStyle": 'default',
      "onClick": this.handleAddOtherCost,
      "className": 'pull-right'
    }, "Sonstige Kosten-Feld hinzuf\u00fcgen...")));
  }
});

OtherCostsBox = React.createClass({
  getDefaultProps: function() {
    return {
      amount: '',
      date: null,
      recipient: '',
      onChange: function(key, value) {
        return {};
      }
    };
  },
  handleCostChange: function(event) {
    var cost;
    cost = event.target.value;
    return this.props.onChange('amount', cost);
  },
  handleRecipientChange: function(event) {
    var recipient;
    recipient = event.target.value;
    return this.props.onChange('recipient', recipient);
  },
  handleDateChange: function(date) {
    return this.props.onChange('date', date);
  },
  render: function() {
    return React.createElement("fieldset", null, React.createElement("legend", null, "Sonstige Kosten"), React.createElement(Input, {
      "type": 'text',
      "value": this.props.amount,
      "steps": '0.01',
      "label": 'Kosten',
      "labelClassName": 'col-xs-2',
      "wrapperClassName": 'col-xs-10',
      "onChange": this.handleCostChange,
      "addonBefore": React.createElement(Icon, {
        "name": 'money',
        "fixedWidth": true
      })
    }), React.createElement(Input, {
      "label": 'Datum',
      "labelClassName": 'col-xs-2',
      "wrapperClassName": 'col-xs-10'
    }, React.createElement(DateTimePicker, {
      "format": 'dd.MM.yyyy',
      "value": new Date(this.props.date),
      "time": false,
      "onChange": this.handleDateChange
    })), React.createElement(Input, {
      "type": 'text',
      "value": this.props.recipient,
      "label": 'Empfänger',
      "labelClassName": 'col-xs-2',
      "wrapperClassName": 'col-xs-10',
      "onChange": this.handleRecipientChange,
      "addonBefore": React.createElement(Icon, {
        "name": 'user',
        "fixedWidth": true
      })
    }));
  }
});

React.render(React.createElement(App, null), $('#entry')[0]);
