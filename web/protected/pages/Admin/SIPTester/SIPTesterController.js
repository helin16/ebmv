/**
 * The page Js file
 */
var PageJs = new Class.create();
PageJs.prototype = Object.extend(new AdminPageJs(), {
	resultDivId: null
	
	,testSIP: function(btn) {
		var tmp = {};
		tmp.me = this;
		tmp.origBtnValue = $(btn).value;
		tmp.testData = tmp.me._collectData(btn);
		if(tmp.testData === null)
			return this;
		
		console.debug(tmp.testData);
		this.postAjax(tmp.me.getCallbackId('testSIP'), {'testdata': tmp.testData}, {
			'onLoading': function (sender, param) {
				$(btn).setValue('processing ...').disabled = true;
			},
			'onComplete': function (sender, param) {
				$(tmp.me.resultDivId).update('');
				try {
					tmp.result = tmp.me.getResp(param, false, true);
					console.debug(tmp.result);
				} catch(e) {
					$(tmp.me.resultDivId).update(e);
				}
				$(btn).setValue(tmp.origBtnValue).disabled = false;
			}
		});
		return this;
	}
	
	,_collectData: function(btn) {
		var tmp = {};
		tmp.data = {};
		tmp.hasErr = false;
		tmp.requestDiv = $(btn).up('.requestdiv');
		//clear all error msg
		tmp.requestDiv.getElementsBySelector('.hasError').each(function(item) {
			item.removeClassName('hasError');
		});
		
		//collect the data
		tmp.requestDiv.getElementsBySelector('[sip_request]').each(function(item) {
			tmp.value = $F(item);
			if(tmp.value.blank()) {
				tmp.hasErr = true;
				item.addClassName('hasError');
			}
			tmp.data[item.readAttribute('sip_request')] = tmp.value;
		});
		return tmp.hasErr === true ? null : tmp.data;
	}

});