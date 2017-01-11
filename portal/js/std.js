/**
 * @file std.js
 * @fileOverview More Standard js Stuff
 * @author gemmans
 */
var gIgn = 0;
var gDiv = new Array();
var gCnt = 0;

var isIE = (navigator.appName.indexOf("Microsoft") != -1) ? 1 : 0;
var isIE4up = (navigator.appVersion.substring(0, 1) == "4") ? 1 : 0;
var isIE6 = (navigator.appVersion.indexOf("MSIE 6") != -1) ? 1 : 0;
var isIE7 = (navigator.appVersion.indexOf("MSIE 7") != -1) ? 1 : 0;
var isIE8 = (navigator.appVersion.indexOf("MSIE 8") != -1) ? 1 : 0;

var heightBuffer = 104;
/**
 * @function getElem
 * @author ahanslik
 * @param {string} aId
 */
function getElem(aId) {
	return (typeof aId == 'string') ? document.getElementById(aId) : aId;
}
/**
 * @function getVal
 * @author jwetherill
 * @param {string} aNode
 */
function getVal(aNode) {
	try {
		var lNode = getElem(aNode);
		return lNode.value;
	} catch(e) {}
}
/**
 * @functionsetVal
 * @author jwetherill
 * @param {string} aNode
 * @param {string} aVal
 */
function setVal(aNode, aVal) {
	try {
		var lNode = getElem(aNode);
		lNode.value = aVal;
	} catch(e) {}
}
/**
 * @function loadReport
 * @description Loads a Report via Ajax
 * @author jwetherill
 * @param {string} aLink
 * @param {string} aDiv
 * @param {string} aUrl
 * @param {array} aParams
 */
function loadReport(aLink, aDiv, aUrl, aParams){
	var lData = '';

	var lLink = jQuery("#"+aLink).text();
	jQuery(".title").html(lLink);
	jQuery(".doc span").removeClass("b");
	jQuery("#"+aLink).addClass("b");

	jQuery.ajax({
	  type: 'post',
      url: aUrl,
      async: false,
      data : aParams,
      success: function(aData) {
        jQuery(aDiv).html(aData);
      }
	});
}

function ajx(aUrl) {
	new Ajax.Request(aUrl);
}

function ajxAplFiles(aNam, aFrmId) {
	var lObj = document.getElementsByName(aNam);
	try {
		lEle = document.forms[aFrmId];
		lEle.listuserfiles.value = lObj[0].value;
	} catch(e) {
	}
}

function cpl(aDivId, aPersist) {
    try {
        aDivId = aDivId.replace('.', '\\.'); // in jQuery . in ids or names need to have \\
        jQuery('#' + aDivId).toggle(400);

		if (arguments.length > 1) {
			ajx.defer('index.php?act=ajx.tog&key=' + aPersist);
		}
	} catch(e) {}
}

function tabSel(aKey) {
	try {
		var lObj = getElem('tab' + actTab);
		tabLo(lObj, aKey);
	} catch(e) {}
	actTab = aKey;
}



function go(aUrl, aTest) {
	if (typeof(aTest) === 'undefined' && aTest == null) {
		location.href = aUrl;
	} else if (aTest.indexOf('tab') >= 0) {
		window.open(aUrl, '_blank');
	} else if (aTest.indexOf('win') >= 0) {
		window.open(aUrl, '_blank', 'dependent=no,hotkeys=yes,location=yes,menubar=yes,resizable=yes,scrollbars=yes,status=yes,toolbar=yes');
	}
}

function setAct(aNode,aAct) {
	try {
		lEle = getElem(aNode);
		lEle.form.act.value = aAct;
		lEle.form.submit();
	} catch(e) {
	}
}

function setActFormId(aId,aAct) {
	try {
		lEle = document.forms[aId];
		lEle.act.value = aAct;
		lEle.submit();
	} catch(e) {
	}
}

/**
 * @function setSubsUpdate
 * @description Set Flag 'subsupdate' active so after Update the sub jobs will be updated.
 * @author gemmans
 * @param {string} aNode Element for Updating
 * @param {string} aAct Value
 */
function setSubsUpdate(aNode,aAct) {
	try {
		lEle = getElem(aNode);
		lEle.form.subsupdate.value = aAct;
		lEle.form.submit();
	} catch(e) {
	}
}

function setStep(aNode,aStep,aCheckMandatoryByStatus,aStatus) {
	try {
		if (aCheckMandatoryByStatus == false) {
			if (checkMandatoryFieldsByJob(aStep)) {
				lEle = getElem(aNode);
				lEle.form.step.value = aStep;
				lEle.form.submit();
			}
		} else if (aCheckMandatoryByStatus == 'nostep') {
			alert('nostep');
			return false;
		} else {
			var concatFunctionName = "checkMandatoryFieldsByStatus" + aStatus;
			if (window[concatFunctionName]() && checkMandatoryFieldsByJob(aStep)) {
				lEle = getElem(aNode);
				lEle.form.step.value = aStep;
				lEle.form.submit();
			}
		}
	} catch(e) {
	}
}

function pop(aUrl) {
	var lWin = window.open(aUrl, '_blank', 'width=600,height=400,scrollbars=yes,status=yes');
}

function mem(aNode) {
	var lEl = $(aNode);
	if (lEl.checked) {
		while (true) {
			try {
				lEl = lEl.up('tr').up('tr').previous('tr').down('input');
				if (lEl && !lEl.checked) {
				    jQuery(lEl).hide('slow').show('slow');
					lEl.checked = true;
				}
			} catch (e) {
				return;
			}
		}
	} else {
		while (lEl) {
			try {
				lEl = lEl.up('tr').next('tr');
				if (lEl.hasClassName('togtr')) {
					lEl.getElementsBySelector('input').each(function(aEl) {
						if (aEl.checked) {
						    jQuery(lEl).hide('slow').show('slow');
							aEl.checked = false;
						}
					});
				}
			} catch (e) {
				return;
			}
		}
	}
}

function rigChk(aEle,aLvl,aStr) {
	var lEle = getElem(aEle);
	if (aEle.checked) {
		var lArr = aStr.split(',');
		for (i = 0; i < lArr.length; i++) {
			var lId = 'chk_' + lArr[i] + '_' + aLvl;
			var lNod =  getElem(lId);
			lNod.checked = true;
		}
	}
}

function rigSub(aEle,aLvl,aPid) {
	var lEle = getElem(aEle);
	if (!aEle.checked) {
		var lId = 'chk_' + aPid + '_' + aLvl;
		var lNod =  getElem(lId);
		lNod.checked = false;
	}
}

function getJobFrmVal(aName) {
	var idx = 'val[' + aName +']';
	return document.forms.jobFrm.elements[idx].value;
}

function setJobFrmVal(aName, aValue) {
	try {
		var idx = 'val[' + aName +']';
		document.forms.jobFrm.elements[idx].value = aValue;
	} catch(e) {}
}

function colChanged(aColPP,aIdx) {
	try {
		var val = getJobFrmVal('co' + aColPP + '_nr_' + aIdx);
		if (val == '') {
			resetCol(aColPP,aIdx);
		}
	} catch(e) {}
}

var GruMem = new Array();
/* BEISPIEL:
var GruMem["in14"] = new Array();
GruMem["in14"][56] = new Array();
GruMem["in14"][56][172] = "Huefken, Gerhard";
GruMem["in14"][56][157] = "Ludwig, Michael";
GruMem["in14"][169] = new Array();
GruMem["in14"][169][172] = "Huefken, Gerhard";
GruMem["in14"][171] = new Array();
GruMem["in14"][171][157] = "Ludwig, Michael";
 */
function ListChange(aSrc, aDst) {
	var lSrc = getElem(aSrc);
	var lDst = getElem(aDst);
	var lOldVal = lDst.value;

	try {
		var lGid = lSrc.value;
		var lOpt = new Option(' ', ''); // == leeres Option-Feld
		lDst.options.length = 0;
		lDst.options[lDst.options.length] = lOpt;

		var lArr = GruMem[aDst][lGid];
		var lDefaultSelected = false;
		var lSelected = false;
		for (var lKey in lArr) {
			/*
			 * The for in statement iterates over user-defined properties in addition to the array elements,
			 * so if you modify the arrays non-integer or non-positive properties (e.g. by adding a foo
			 * property to it or even by adding a method or property to Array.prototype), the for in statement
			 * will return the name of your user-defined properties in addition to the numeric indexes.
			 *
			 * isNaN Ermittelt, ob ein zu uebergebender Wert eine ungueltige Zahl ist (NaN = Not a Number).
			 * Gibt true zurueck, wenn der Wert keine Zahl ist, und false, wenn es eine Zahl ist.
			 */
			if ((!isNaN(lKey)) || (typeof lArr[lKey] == "string")) {
				var lNam = lArr[lKey];
				// Option('angezeigter Text in der Liste',
				// 'zu uebertragender Wert der Liste (optional)',
				// 'defaultSelected = true uebergeben, wenn der Eintrag der defaultmig
				//  vorselektierte Eintrag sein soll, sonst false (optional)',
				// 'selected = true uebergeben, wenn der Eintrag selektiert werden soll (optional)')
				var lOpt = new Option(lNam, lKey, lDefaultSelected, lSelected);
				lDst.options[lDst.options.length] = lOpt;
			}
		}
		lDst.value = lOldVal;
	} catch(e) {
	}
}

function grpChange(aSrc, aDst) {
	var lSrc = getElem(aSrc);
	var lDst = getElem(aDst);
	try {
		var lGid = lSrc.value;
		var lDstVal = jQuery("#"+aDst).prev("input[type='hidden']").attr("value");
		lDst.options.length = 0;
		var lOpt = new Option(' ', '');
		lDst.options[lDst.options.length] = lOpt;

		var lParams = {'gru': lGid};
	    new Ajax.Request('index.php?act=ajx.gselect', {
	        method : 'post',
	        parameters : lParams,
	        onSuccess : function(transport) {
	            var lRes = (transport.responseText).evalJSON();
	            var lFla = (lDstVal == '' && lRes.length == 1) ? true : false;
	            var lFla = false; // der erste Name wird selektiert
	    		for (var lKey in lRes) {
	    			if (lDstVal != '' && lKey == lDstVal){ //select if value is what is saved
	    				var lFla =  true;
	    			}
	    			var lNam = lRes[lKey];
	    			var lOpt = new Option(lNam, lKey, lFla, lFla);
	    			lDst.options[lDst.options.length] = lOpt;
	    			var lFla = false;
	    		}
	        }
	    });
	} catch(e) { }
}

function syncGrp(aSrc,aDst) {
	var lSrc = getElem(aSrc);
	var lDst = getElem(aDst);
	if (lSrc.value != lDst.value) {
		lDst.value = lSrc.value;
		lDst.onchange();
	}
}

function rig(aEl) {
	var lVal = (arguments.length > 1) ? arguments[1] : true;
	$(aEl).up('tr').getElementsBySelector('[type="checkbox"]').each(function(aNod){aNod.checked=lVal;});
}

function createArt(aPid, aLan) {
	var functionName = 'js.createArt';
	aLan = unescape(lan(functionName));

	var lIds = '';
	var lArr = $$('input').pluck('id').each(function (e) { if ($(e).checked && $(e).hasClassName('art') ) lIds+= e.substring(1,e.length)+',';});
	if (lIds.length == 0) {
		alert(aLan);
	} else {
		go('index.php?act=job-art.sub&pid='+aPid+'&sid='+ lIds.substring(0,lIds.length-1));
	}
}

function createMasterVariant(aPid, aLan) {
	var functionName = 'js.createMasterVariant';
	aLan = unescape(lan(functionName));

	var lIds = '';
	var lArr = $$('input').pluck('id').each(function (e) { if ($(e).checked && $(e).hasClassName('variant') ) lIds+= e.substring(1,e.length)+',';});
	if (lIds.length == 0) {
		alert(aLan);
	} else {
		go('index.php?act=job-pro-sub.assignmaster&jobid='+aPid+'&sid='+ lIds.substring(0,lIds.length-1));
	}
}

function defRole(self){
    var lDestId = jQuery("select[name='val[alias]']").attr("id");
    var lOldVal = jQuery("input[name='old[alias]']").val();

    var lParams = {'typ': self.value, 'val': lOldVal};
    new Ajax.Request('index.php?act=ajx.defineroles', {
        method : 'post',
        parameters : lParams,
        onSuccess : function(transport) {
            var lRes = (transport.responseText).evalJSON();
            var lDest = $(lDestId);
            lDest.length = 0;
            for ( var lKey in lRes) {
                var lRow = lRes[lKey];
                var lVal = lRow[0];
                var lSel = lRow[1];
                var lTmp = new Option(lVal, lKey, lSel, lSel);
                lDest.options[lDest.length] = lTmp;
            }
        }
    });
}

function addMsg(aTxt) {
	gCnt++;
	var lId = 'js' + gCnt;
	var lCnt = '<div class="box p2" id="'+lId+'" style="display:none">'+aTxt+'<br /></div><br />';
	$("pgMsgJs").insert(lCnt);
	jQuery('#' + lId).slideDown(200);
}

function isset(aJs) {
	if (null == aJs || '' == aJs) {
		return 0;
	} else {
		return 1;
	}
}

jQuery.fn.exists = function(fn) {
    var lTimer;
    var lElementID = this.selector;

    if (this.length > 0) {
        fn.call(this);
    } else {
    	lTimer = setInterval(function(){
            if (jQuery(lElementID).length > 0) {
                fn.call(jQuery(lElementID));
                clearInterval(lTimer);
            }
        }, 250);
    }
};

function validate(chgMsg, errMsg, Uid) {
    var illegalCharacters = /\W.-/;
    input = prompt(chgMsg, null);
    if (illegalCharacters.test(input) || input.length < 2) {
        alert(errMsg);
    } else {
        go("index.php?act=usr-opt.usr&id="+Uid+"&usr=" + input);
    }
};

function trim(str) {
    var str = str.replace(/^\s\s*/, ""),
            ws = /\s/,
            i = str.length;
    while (ws.test(str.charAt(--i)))
        ;
    return str.slice(0, i + 1);
};

function validateMail(errMsg) {
    Ausdruck = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    from = document.getElementsByName("val[email_from]")[0].value;
    from_trimmed = trim(from);
    replyto = document.getElementsByName("val[email_replyto]")[0].value;
    replyto_trimmed = trim(replyto);
    res = Ausdruck.exec(from);
    if (res === null && from_trimmed !== "") {
        alert(errMsg);
        return false;
    }
    res = Ausdruck.exec(replyto);
    if (res === null && replyto_trimmed !== "") {
        alert(errMsg);
        return false;
    }
    return true;
}
/**
 * @function checkAllStates
 * @author ahanslik
 * @param {array} states
 */
function checkAllStates(states) {
    var i, lobj, lsoll, statusarray;
    var statusarray = states.split(",");
    lnam = "val[webstatus][0]";
    lsoll = document.getElementsByName(lnam)[0];

    for (i = 0; i < statusarray.length; i++) {
        lnam = "statecheckbox" + statusarray[i];
        lobj = document.getElementById(lnam);
        if (typeof (lobj) === 'undefined')
            continue;
        lobj.checked = lsoll.checked;
    }
}
/**
 * @functionuncheckAllStates
 * @author ahanslik
 * @param {string} checkboxid
 * @param {array} states
 */
function uncheckAllStates(checkboxid, states) {
    var i, lobj, lsoll, stataus_array;
    lsoll = document.getElementById(checkboxid);
    if (lsoll.checked === false) {
        lnam = "val[webstatus][0]";
        lobj = document.getElementsByName(lnam)[0];
        lobj.checked = false;
    } else {
        var allchecked = true;
        var statusarray = states.split(",");
        for (i = 0; i < statusarray.length; i++) {
            lnam = "statecheckbox" + statusarray[i];
            lobj = document.getElementById(lnam);
            if (typeof (lobj) === 'undefined')
                continue;
            if (lobj.checked === true && allchecked === true) {
                allchecked = true;
            } else {
                allchecked = false;
            }
        }
        if (allchecked == true) {
            lnam = "val[webstatus][0]";
            lobj = document.getElementsByName(lnam)[0];
            lobj.checked = true;
        }
    }
}
/**
 * @function checkAllFlags
 * @author ahanslik
 * @param {array} flags
 */
function checkAllFlags(flags) {
    var i, lobj, lsoll, statusarray;
    lnam = "val[flags][0]";
    lsoll = document.getElementsByName(lnam)[0];
    var statusarray = flags.split(",");
    for (i = 0; i < statusarray.length; i++) {
        lnam = "flagcheckbox" + statusarray[i];
        lobj = document.getElementById(lnam);
        if (typeof (lobj) === undefined)
            continue;
        lobj.checked = lsoll.checked;
    }
    ;
}
/**
 * @function uncheckAllFlags
 * @author ahanslik
 * @param {string} checkboxid
 * @param {array} flags
 */
function uncheckAllFlags(checkboxid, flags) {
    var i, lobj, lsoll, stataus_array;
    lsoll = document.getElementById(checkboxid);
    if (lsoll.checked === false) {
        lnam = "val[flags][0]";
        lobj = document.getElementsByName(lnam)[0];
        lobj.checked = false;
    } else {
        var allchecked = true;
        var statusarray = flags.split(",");
        for (i = 0; i < statusarray.length; i++) {
            lnam = "flagcheckbox" + statusarray[i];
            lobj = document.getElementById(lnam);
            if (typeof (lobj) === undefined)
                continue;
            if (lobj.checked === true && allchecked === true) {
                allchecked = true;
            } else {
                allchecked = false;
            }
        }
        if (allchecked === true) {
            lnam = "val[flags][0]";
            lobj = document.getElementsByName(lnam)[0];
            lobj.checked = true;
        }
    }
}
/**
 * @function lan
 * @author pdohmen
 * @param {string} code Language Code
 * @description Gives you the requested string from the language Table if it exists
 */
function lan(code) {
  var rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
  code = code.replace(rtrim, '');
  if(typeof lang[code] !== 'undefined') {
    return lang[code];
  }
  return code;
}
