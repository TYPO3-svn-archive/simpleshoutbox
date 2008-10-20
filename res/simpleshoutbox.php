<?php header('Content-type: application/javascript'); ?>
var txShoutBoxLastUpdate = <?php echo mktime(); ?>;
var txShoutBoxUpdater;
var txShoutBoxPeriodicalExecuter;

function txShoutBoxSendForm() {
	var url = 'index.php?eID=tx_simpleshoutbox_ajax';
	
	var message = document.getElementById('tx-simpleshoutbox-pi1-form-message').value;
	document.getElementById('tx-simpleshoutbox-pi1-form-message').value = '';

	var templastupdate = txShoutBoxLastUpdate;
	new Ajax.Request(url, {
		method: 'post',
		parameters: {message: message, conf: conf, lastupdate: templastupdate},
		onSuccess: function(transport) {
			var el = document.getElementById('tx-simpleshoutbox-list');
			new Insertion.Top(el, transport.responseText);
		}
	});
	txShoutBoxUpdateTime();
	txShoutBoxPeriodicalExecuter.stop();
	txShoutBoxStartPeriodicalUpdate(10);
}

function txShoutBoxUpdateTime() {
	var d = new Date();
	txShoutBoxLastUpdate = d.getTime();
	txShoutBoxLastUpdate = txShoutBoxLastUpdate / 1000;
}

function txShoutBoxUpdate() {
	var url = 'index.php?eID=tx_simpleshoutbox_ajax';
	
	var el = document.getElementById('tx-simpleshoutbox-list');
	txShoutBoxUpdater = new Ajax.Updater(el, url,
		{
			method: 'post',
			parameters: {update: 1, lastupdate: txShoutBoxLastUpdate, conf: conf},
			insertion: Insertion.Top,
			onComplete: txShoutBoxUpdateTime()
		}
	);	
}
function txShoutBoxStartPeriodicalUpdate(period) {
	txShoutBoxPeriodicalExecuter = new PeriodicalExecuter(txShoutBoxUpdate,period);
}

