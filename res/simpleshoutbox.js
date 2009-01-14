var txShoutBoxUpdater;
var txShoutBoxPeriodicalExecuter;

function txShoutBoxSendForm() {
	var url = 'index.php?eID=tx_simpleshoutbox_ajax';

	var message = document.getElementById('tx-simpleshoutbox-pi1-form-message').value;
	document.getElementById('tx-simpleshoutbox-pi1-form-message').value = '';

	var d = new Date();

	new Ajax.Request(url, {
		method: 'post',
		parameters: {message: message, lastupdate: txShoutBoxLastUid, nocachequery: d.getTime()},
		onSuccess: function(transport) {
			var el = document.getElementById('tx-simpleshoutbox-list');

			txShoutBoxLastUid = transport.responseXML.getElementsByTagName("lastuid")[0];
			txShoutBoxLastUid = txShoutBoxLastUid.firstChild.nodeValue;

			var content = transport.responseXML.getElementsByTagName("messages")[0];
			new Insertion.Top(el, content.firstChild.nodeValue);
		}
	});
	txShoutBoxPeriodicalExecuter.stop();
	txShoutBoxStartPeriodicalUpdate(10);
}

function txShoutBoxUpdate() {
	var url = 'index.php?eID=tx_simpleshoutbox_ajax';

	var d = new Date();

	new Ajax.Request(url, {
		method: 'post',
		parameters: {lastupdate: txShoutBoxLastUid, update: 1, nocachequery: d.getTime()},
		onSuccess: function(transport) {
			var el = document.getElementById('tx-simpleshoutbox-list');

			txShoutBoxLastUid = transport.responseXML.getElementsByTagName("lastuid")[0];
			txShoutBoxLastUid = txShoutBoxLastUid.firstChild.nodeValue;

			var content = transport.responseXML.getElementsByTagName("messages")[0];
			new Insertion.Top(el, content.firstChild.nodeValue);
		}
	});
}
function txShoutBoxStartPeriodicalUpdate(period) {
	txShoutBoxPeriodicalExecuter = new PeriodicalExecuter(txShoutBoxUpdate,period);
}

