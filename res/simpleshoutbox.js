var txSimpleShoutbox = {
	_noUpdate: 0,
	_url: 'index.php?eID=tx_simpleshoutbox_ajax',
	_periodicalExecuter: null,
	_listEl: document.getElementById('tx-simpleshoutbox-list'),
	_messageEl: document.getElementById('tx-simpleshoutbox-pi1-form-message'),
	lastUid: 0,

	_handleResponse: function(transport) {
		var tempLastUid = transport.responseXML.getElementsByTagName("lastuid")[0];
		this.lastUid = tempLastUid.firstChild.nodeValue;

		var content = transport.responseXML.getElementsByTagName("messages")[0];
		var newIns = new Insertion.Top(this._listEl, content.firstChild.nodeValue);
	},

	update: function() {
		if (this._noUpdate == 1) { return false; }

		var d = new Date();

		var req = new Ajax.Request(this._url, {
			method: 'post',
			parameters: {lastupdate: this.lastUid, update: 1, nocachequery: d.getTime()},
			onSuccess: this._handleResponse(transport)
		});
	},

	startPeriodicalUpdate: function(period) {
		this._periodicalExecuter = new PeriodicalExecuter(this.update,period);
	},

	sendForm: function() {
		var message = this._messageEl.value;
		this._messageEl.value = '';

		var d = new Date();
		this._noUpdate = 1;

		var req = new Ajax.Request(this._url, {
			method: 'post',
			parameters: {message: message, lastupdate: this.lastUid, nocachequery: d.getTime()},
			onSuccess: this._handleResponse(transport),
			onComplete: function(transport) { this._noUpdate = 0; }
		});
		this._periodicalExecuter.stop();
		this.startPeriodicalUpdate(10);
	}
};