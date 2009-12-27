var txSimpleShoutbox = {
	noUpdate: 0,
	url: 'index.php?eID=tx_simpleshoutbox_ajax',
	periodicalExecuter: null,
	listEl: null,
	messageEl: null,
	lastUid: 0,
	pageId: 1,

	init: function() {
		this.listEl = document.getElementById('tx-simpleshoutbox-list');
		this.messageEl = document.getElementById('tx-simpleshoutbox-pi1-form-message');
	},

	handleResponse: function(transport) {
		var tempLastUid = transport.responseXML.getElementsByTagName("lastuid")[0].firstChild.nodeValue;

		if(tempLastUid == txSimpleShoutbox.lastUid) { return false; }
		txSimpleShoutbox.lastUid = tempLastUid;

		var content = transport.responseXML.getElementsByTagName("messages")[0];
		var newIns = new Insertion.Top(txSimpleShoutbox.listEl, content.firstChild.nodeValue);
	},

	update: function() {
		if (txSimpleShoutbox.noUpdate == 1) { return false; }

		var d = new Date();

		var req = new Ajax.Request(txSimpleShoutbox.url, {
			method: 'get',
			parameters: {lastupdate: txSimpleShoutbox.lastUid, id: txSimpleShoutbox.pageId, update: 1, nocachequery: d.getTime()},
			onSuccess: function(transport) { txSimpleShoutbox.handleResponse(transport); }
		});
	},

	startPeriodicalUpdate: function(period) {
		this.periodicalExecuter = new PeriodicalExecuter(this.update,period);
	},

	sendForm: function() {
		var message = this.messageEl.value;
		this.messageEl.value = '';

		var d = new Date();
		this.noUpdate = 1;

		var req = new Ajax.Request(this.url, {
			method: 'post',
			parameters: {message: message, lastupdate: this.lastUid, id: this.pageId, nocachequery: d.getTime()},
			onSuccess: function(transport) {
				txSimpleShoutbox.handleResponse(transport);
				txSimpleShoutbox.periodicalExecuter.stop();
				txSimpleShoutbox.startPeriodicalUpdate(10);
				txSimpleShoutbox.noUpdate = 0;
			}
		});
	}
};
