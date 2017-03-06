function jopensimpaypalUpdate() {
	var retval1 = "";
	var iwCurrency = document.getElementById("jopensimpaypal_iwcurrencyname").value;
	var currencyRL = round2digits(document.getElementById("jopensimpaypal_rlcurrency").value);
	document.getElementById("jopensimpaypal_rlcurrency").value = currencyRL;
	var totalAmount = currencyRL;
	var exchangeRate = document.getElementById("jopensimpaypal_xchangerate").value;
	if(isNaN(currencyRL) || isNaN(exchangeRate)) {
		retval1 = "Error";
	} else {
		retval1 = Math.floor(currencyRL * exchangeRate);
	}
//	alert(retval);
	var updateDiv1 = document.getElementById("jOpenSimPayPalIW1");
	if(updateDiv1) updateDiv1.innerHTML = retval1;
	else alert("Error: "+retval1);
	var hasFee = document.getElementById("jopensimpaypal_hasfee").value;
	if(hasFee) {
		var feeType = document.getElementById("jopensimpaypal_transactionfeetype").value;
		if(feeType == "percent") {
			var percentVal = document.getElementById("jopensimpaypal_transactionfee").value;
			var transactionfee = round2digits(currencyRL / 100 * percentVal);
			document.getElementById("jOpenSimPayPalTransactionFee").innerHTML = transactionfee;
//			alert("hasFee (percent): "+hasFee);
		} else {
			var transactionfee = document.getElementById("jopensimpaypal_transactionfee").value;
		}
		var totalDiv = document.getElementById("jOpenSimPayPalRLtotal");
		totalAmount = (currencyRL * 1) + (transactionfee * 1);
		if(totalDiv) totalDiv.innerHTML = totalAmount;
		var updateDiv2 = document.getElementById("jOpenSimPayPalIW2");
		if(updateDiv2) updateDiv2.innerHTML = retval1;
	}
	document.getElementById("jopensimpaypal_amount_1").value = totalAmount;
	document.getElementById("jopensimpaypal_item").value = iwCurrency+" "+retval1;
}

function jopensimpaypalUpdate2() {
	var retval1 = "";
	var finalAmount = 0;
	var iwCurrencyName = document.getElementById("jopensimpaypal_iwcurrencyname").value;
	var currentBalance = document.getElementById("currentbalance").value * 1;
	var iwCurrency = document.getElementById("jopensimpaypal_iwcurrency").value * 1;
	if(iwCurrency > currentBalance) {
		errormsg = document.getElementById("jopensimpaypal_balanceerrormsg").value;
		alert(errormsg);
		document.getElementById("jopensimpaypal_iwcurrency").value = currentBalance;
		iwCurrency = currentBalance;
	}
	var currencyIW = Math.floor(iwCurrency);
	document.getElementById("jopensimpaypal_iwcurrency").value = currencyIW;
	var exchangeRate = document.getElementById("jopensimpaypal_xchangerate").value;
	if(isNaN(currencyIW) || isNaN(exchangeRate)) {
		retval1 = "Error";
	} else {
		currencyRL = round2digits(currencyIW / exchangeRate);
		finalAmount = retval1 = currencyRL;
	}
	var updateDiv1 = document.getElementById("jOpenSimPayPalRL1");
	if(updateDiv1) updateDiv1.innerHTML = retval1;
	else alert("Error: "+retval1);
	var hasFee = document.getElementById("jopensimpaypal_hasfee").value;
	if(hasFee) {
		var feeType = document.getElementById("jopensimpaypal_transactionfeetype").value;
		if(feeType == "percent") {
			var percentVal = document.getElementById("jopensimpaypal_transactionfee").value;
			var transactionfee = round2digits(currencyRL / 100 * percentVal);
			document.getElementById("jOpenSimPayPalTransactionFee").innerHTML = transactionfee;
//			alert("hasFee (percent): "+hasFee);
		} else {
			var transactionfee = document.getElementById("jopensimpaypal_transactionfee").value;
		}
		finalAmount = round2digits((currencyRL * 1) - (transactionfee * 1));
		var updateDiv2 = document.getElementById("jOpenSimPayPalRL2");
		if(updateDiv2) updateDiv2.innerHTML = finalAmount;
	}
	payout = document.getElementById("jopensimpaypal_payoutvalue");
	if(payout) payout.value = finalAmount;
}

function round2digits(currencyval) {
	return Math.round(currencyval*100)/100;
}

function checkPayPalForm() {
	var payment	= document.getElementById("jopensimpaypal_rlcurrency").value * 1;
	var minbuy	= document.getElementById("jopensimpaypal_minbuy").value * 1;
	var maxbuy	= document.getElementById("jopensimpaypal_maxbuy").value * 1;
	if(minbuy > 0 && minbuy > payment) {
		alert(document.getElementById("minbuymessage").value);
		return false;
	}
	if(maxbuy > 0 && maxbuy < payment) {
		alert(document.getElementById("maxbuymessage").value);
		return false;
	}
}

function checkPayOutForm() {
	var payoutValue = document.getElementById("jopensimpaypal_payoutvalue").value * 1;
	if(payoutValue <= 0) {
		errormsg = document.getElementById("jopensimpaypal_negativeamounterror").value;
		alert(errormsg);
		return false;
	}
	var minsell = document.getElementById("jopensimpaypal_minsell").value * 1;
	var maxsell = document.getElementById("jopensimpaypal_maxsell").value * 1;
	if(payoutValue < minsell) {
		errormsg = document.getElementById("jopensimpaypal_minsellmessage").value;
		alert(errormsg);
		return false;
	}
	if(payoutValue > maxsell) {
		errormsg = document.getElementById("jopensimpaypal_maxsellmessage").value;
		alert(errormsg);
		return false;
	}
	var userpaypal = document.getElementById("jopensimpaypal_paypaluser").value;
	if(validEmail(userpaypal) != true) {
		errormsg = document.getElementById("jopensimpaypal_paypalmessage").value;
		alert(errormsg);
		return false;
	}
	return true;
}

function validEmail(emailvalue) {
	var mailexp=/^.{1,}@.{3,}\..{2,4}$/;
	return mailexp.test(emailvalue);
}