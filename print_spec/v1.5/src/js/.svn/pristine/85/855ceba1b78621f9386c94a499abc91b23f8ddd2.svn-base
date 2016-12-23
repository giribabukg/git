Flow = {
    sliceObj : function(aObj, aStart, aEnd) {
        var lSlice = {};
        var lCounter = 0;
        for (var lKey in aObj) {
            if (lCounter >= aStart && lCounter < aEnd) {
                lSlice[lKey] = aObj[lKey];
            }

            lCounter++;
        }
        return lSlice;
    },
    setSelected : function(aDest, aValue) {
        $(aDest).writeAttribute('value', aValue);
    },

    cselect : function(aDest, aColumn, aPickList, aParents, aOldVal) {
        var lJqDest = jQuery('#' + aDest);
        var lOldVal = ('undefined' == typeof (aOldVal)) ? lJqDest.val() : aOldVal;

        var lParams = {};
        var lParents;
        try {
            if('[]' != aParents) {
                lParents = jQuery.parseJSON(aParents);
            }
        } catch(e) {
        }
        var lVal;
        $H(lParents).each(function(pair) {
            if(null != $(pair.value)) {
                lVal = $F(pair.value);
                if((lVal != '') && (lVal != ' ')) {
                    lParams['filter_' + pair.key] = lVal;
                }
            }
        });

        lParams['_column'] = aColumn;
        lParams['_picklist'] = aPickList;
        if(!(typeof (lOldVal) == 'undefined')) {
            lParams['_old'] = lOldVal;
        }
        jQuery.post('index.php?act=ajx.cselect', lParams, function(aData) {
        	lRes = aData;
                var lDest = $(aDest);
                lDest.length = 0;
            var lAuto = false;

            //var lLength = Object.keys(lRes).length;
            var lLength = lRes.length;
            var lSelectSingle = ((lLength == 2) && (lJqDest.attr('data-autoselect') == 1));
            for (var i = 0; i < lLength; i++) {
                var lRow = lRes[i];
                var lKey = lRow[0];
                var lVal = lRow[1];
                var lSel = lRow[2];
                if (lSelectSingle && lKey != '' && lKey != ' ') {
                	lSel = true;
                }
                if (lSel) {
                  lAuto = true;
                }
                    var lTmp = new Option(lVal, lKey, lSel, lSel);
                    lDest.options[lDest.length] = lTmp;
                }
            if (lAuto) {
            	if (lOldVal != lJqDest.val()) {
            	  lJqDest.change();
            }
            }
        }, 'json');

    },
    
    uselect : function(aFieldId, aGroup1, aGroup2, aNoPreselect) {
        var lParams = new Object();
        lParams['gru1'] = aGroup1;
        lParams['gru2'] = aGroup2;
        if(aNoPreselect) {
            lParams['nopre'] = 1;
        }
        new Ajax.Request('index.php?act=ajx.uselect', {
            method : 'post',
            parameters : lParams,
            onSuccess : function(transport) {
                var lRes = (transport.responseText).evalJSON();
                var lDest = $(aFieldId);
                lDest.length = 0;
                for(var lKey in lRes) {
                    var lRow = lRes[lKey];
                    var lVal = lRow[0];
                    var lSel = lRow[1];
                    var lTmp = new Option(lVal, lKey, lSel, lSel);
                    lDest.options[lDest.length] = lTmp;
                }
            }
        });

    },
    onRegionSelect : function(aValue, aCountryId, aLangId) {
        lParams = {
            value : aValue
        };
        new Ajax.Request('index.php?act=ajx.regionSelect', {
            method : 'post',
            parameters : lParams,
            onSuccess : function(transport) {
                var lRes = (transport.responseText).evalJSON();
                if(aCountryId && $(aCountryId)) {
                    $(aCountryId).setValue(lRes.country);
                }
                if(aLangId && $(aLangId)) {
                    $(aLangId).setValue(lRes.language);
                }

            }
        });
    },
    onCountrySelect : function(aValue, aLangId) {
        lParams = {
            value : aValue
        };
        new Ajax.Request('index.php?act=ajx.countrySelect', {
            method : 'post',
            parameters : lParams,
            onSuccess : function(transport) {
                lRes = (transport.responseText).evalJSON();
                if(aLangId && $(aLangId)) {
                    $(aLangId).setValue(lRes.language);
                }

            }
        });
    },
    fprSrcTip : function() {
        Flow.showFprTip("src");
    },
    fprDstTip : function() {
        Flow.showFprTip("dst");
    },
    showFprTip : function(aSelect) {
        var select = $(aSelect);
        var num = 0;
        var selectedItem;
        for(var i = 0; i < select.options.length; i++) {
            if(select.options[i].selected) {
                num++;
                selectedItem = select.options[i].value;
            }
        }
        if(1 == num) {
            var lDiv = $('pag_tip');
            lDiv.hide();
            new Ajax.Updater(lDiv, 'index.php', {
                parameters : {
                    act : 'ajx.fieldTip',
                    id : selectedItem
                },
                onSuccess : function(transport) {
                    if('' != transport.responseText) {
                        Element.clonePosition(lDiv, select, {
                            setHeight : false,
                            setWidth : false,
                            offsetLeft : $(select).getWidth()
                        });
                        lDiv.show();
                    }
                }
            });
        }
    },
    oprSrcTip : function() {
        Flow.showOprTip("src");
    },
    showOprTip : function(aSelect) {
        var select = $(aSelect);
        var num = 0;
        var selectedItem = null;
        for(var i = 0; i < select.options.length; i++) {
            if(select.options[i].selected) {
                num++;
                selectedItem = select.options[i].value;
            }
        }
        if(1 == num) {
            var lDiv = $('pag_tip');
            lDiv.hide();
            new Ajax.Updater(lDiv, 'index.php', {
                parameters : {
                    act : 'ajx.chkTip',
                    id : selectedItem
                },
                onSuccess : function(transport) {
                    if('' != transport.responseText) {
                        Element.clonePosition(lDiv, select, {
                            setHeight : false,
                            setWidth : false,
                            offsetLeft : $(select).getWidth()
                        });
                        lDiv.show();
                    }
                }
            });
        }
    },
    oprAll : function(aSrc) {
        var lSrc = $(aSrc);

        if(!lSrc)
            return;

        for(var i = 0; i < lSrc.options.length; i++) {
            lSrc.options[i].selected = true;
        }
    },
    oprUp : function(aId) {
        var select = $(aId);
        var num = select.options.length;
        var first = select.options[0].selected;

        if(num == 0)
            return;
        if(first)
            return;

        for(var i = 1; i < select.options.length; i++) {
            if(select.options[i].selected) {
                var lTxt = select.options[i - 1].text;
                var lVal = select.options[i - 1].value;
                var lSel = select.options[i - 1].selected;

                select.options[i - 1].text = select.options[i].text;
                select.options[i - 1].value = select.options[i].value;
                select.options[i - 1].selected = true;

                select.options[i].text = lTxt;
                select.options[i].value = lVal;
                select.options[i].selected = lSel;
            }
        }
    },
    oprDown : function(aId) {
        var select = $(aId);
        var num = $(aId).options.length;
        var last = $(aId).options[num - 1].selected;

        if(num == 0)
            return;
        if(last)
            return;

        for(var i = select.options.length - 1; i >= 0; i--) {
            if(select.options[i].selected) {
                var lTxt = select.options[i + 1].text;
                var lVal = select.options[i + 1].value;
                var lSel = select.options[i + 1].selected;

                select.options[i + 1].text = select.options[i].text;
                select.options[i + 1].value = select.options[i].value;
                select.options[i + 1].selected = true;

                select.options[i].text = lTxt;
                select.options[i].value = lVal;
                select.options[i].selected = lSel;
            }
        }
    },
    hideTip : function() {
        $('pag_tip').hide();
    },
    showTip : function() {
        if(0 == Ajax.activeRequestCount) {
            $('pag_tip').show();
        }
    },
    hisTip : function(aEl, aId, aName) {
        var lDiv = $('pag_tip');
        lDiv.hide();
        new Ajax.Updater(lDiv, 'index.php', {
            parameters : {
                act : 'ajx.hisTip',
                id : aId,
                name : aName
            },
            onSuccess : function() {
                Flow.showTip.defer();
            }
        }
        );
        Element.clonePosition(lDiv, aEl, {
            setHeight : false,
            setWidth : false,
            offsetTop : $(aEl).getHeight()
        });
    },
    stpTip : function(aEl, aId) {
        var lDiv = $('pag_tip');
        lDiv.hide();
        new Ajax.Updater(lDiv, 'index.php', {
            parameters : {
                act : 'ajx.stpTip',
                id : aId
            },
            onSuccess : function() {
                Flow.showTip.defer();
            }
        }
        );
        Element.clonePosition(lDiv, aEl, {
            setHeight : false,
            setWidth : false,
            offsetTop : $(aEl).getHeight()
        });
        lDiv.hide();
    },
    crpTip : function(aEl, aId, aOff) {
        var lDiv = $('pag_tip');
        lDiv.hide();
        new Ajax.Updater(lDiv, 'index.php', {
            parameters : {
                act : 'ajx.crpTip',
                id : aId
            },
            onSuccess : function() {
                Flow.showTip.defer();
            }
        });

        var lElement = jQuery(aEl);
        var lOffset = lElement.offset();
        var lLeft = lOffset.left;

        if (lLeft > lDiv.getWidth()) {
            var lOff = $(aEl).getWidth() - lDiv.getWidth() - 1;
            Element.clonePosition(lDiv, aEl, {
                setHeight : false,
                setWidth : false,
                offsetTop : $(aEl).getHeight(),
                offsetLeft : lOff
            });
        } else {
            Element.clonePosition(lDiv, aEl, {
                setHeight : false,
                setWidth : false,
                offsetTop : $(aEl).getHeight()
            });
        }
    },
    grpMemTip : function(aEl, aGid) {
        var lDiv = $('pag_tip');
        lDiv.hide();
        new Ajax.Updater(lDiv, 'index.php', {
            parameters : {
                act : 'ajx.grpMem',
                id : aGid
            },
            onSuccess : function() {
                Flow.showTip.defer();
            }
        }
        );
        Element.clonePosition(lDiv, aEl, {
            setHeight : false,
            setWidth : false,
            offsetTop : $(aEl).getHeight()
        });
    },
    usrDetTip : function(aEl, aUid) {
        var lDiv = $('pag_tip');
        lDiv.hide();
        new Ajax.Updater(lDiv, 'index.php', {
            parameters : {
                act : 'ajx.usrDet',
                id : aUid
            },
            onSuccess : function() {
                Flow.showTip.defer();
            }
        }
        );
        Element.clonePosition(lDiv, aEl, {
            setHeight : false,
            setWidth : false,
            offsetTop : $(aEl).getHeight()
        });
    },
    // deprecated???
    checkAll : function(aClass) {
        jQuery('.' + aClass).prop('checked', true);
    },
    // deprecated???
    uncheckAll : function(aClass) {
        jQuery('.' + aClass).prop('checked', false);
    },
    checkAllEx : function(aPattern) {
        jQuery('input[type="checkbox"][name*="' + aPattern + '"]').prop('checked', true);
    },
    uncheckAllEx : function(aPattern) {
        jQuery('input[type="checkbox"][name*="' + aPattern + '"]').prop('checked', false);
    },

    //Unused-> Kein richtiges Toggle -> Status des ersten Elem wird ausgewertet und alle anderen danach behandelt
//    togCheckAll : function(aClass) {
//        var lElem = $$('.' + aClass).first();
//        if (lElem.checked)
//            Flow.uncheckAll(aClass);
//        else
//            Flow.checkAll(aClass);
//
//    },
    togCheckAll : function(aClass) {
        jQuery('.' + aClass).each(function() {
            if(jQuery(this).prop('checked') === false) {
                jQuery(this).prop('checked', true);
            }
            else {
                jQuery(this).prop('checked', false);
            }
        });
    },
//    toggleLines : function(aClass) {
//        $$('.' + aClass).each(function(aTr) {
//            var lDis = aTr.style.display;
//            var lWasVisible = ('none' != lDis);
//            if (lWasVisible) {
//                aTr.style.display = 'none';
//            } else {
//                aTr.style.display = 'table-row';
//            }
//        });
//    },
    toggleLines : function(aClass) {
        jQuery('.' + aClass).each(function() {
            if(jQuery(this).css('display') === 'none') {
                jQuery(this).css('display', 'table-row');
            }
            else {
                jQuery(this).css('display', 'none');
            }
        });
    },
//    tog : function(aClass) {
//        $$('.' + aClass).each(function(aEl) {
//            var lDis = aEl.style.display;
//            var lWasVisible = (('none' != lDis) && ('' != lDis));
//            if (lWasVisible) {
//                aEl.style.display = 'none';
//            } else {
//                aEl.style.display = 'block';
//            }
//        });
//    },
    tog : function(aClass) {
        jQuery('.' + aClass).each(function() {
            if(jQuery(this).css('display') === 'none') {
                jQuery(this).css('display', 'block');
            }
            else {
                jQuery(this).css('display', 'none');
            }
        });
    },
    togTrOne : function(aId, aClass, aCallbackOnVisible) {
        var lEl = $(aId);
        var lDis = lEl.style.display;
        var lWasVisible = (('none' != lDis) && ('' != lDis));
        if(lWasVisible) {
            lEl.style.display = 'none';
        } else {
            $$('.' + aClass).each(function(aEl) {
                aEl.style.display = 'none';
            });
            lEl.style.display = 'table-row';
            if(aCallbackOnVisible) {
                aCallbackOnVisible(lEl);
            }
        }
    },
//    togTrTree : function(aElement, aClass) {
//        var lTr = $(aElement).up('tr');
//        if (lTr.hasClassName('collapsed')) {
//            $$('.' + aClass).each(function(aEl) {
//                aEl.style.display = 'table-row';
//                aEl.removeClassName('collapsed');
//            });
//            lTr.removeClassName('collapsed');
//        } else {
//            lTr.addClassName('collapsed');
//            $$('.' + aClass).each(function(aEl) {
//                aEl.style.display = 'none';
//                lTr.addClassName('collapsed');
//            });
//        }
//    },

    togTrTree : function(aElement, aClass) {
        var lTr = jQuery(aElement).closest('tr');
        if(lTr.hasClass('collapsed')) {
            jQuery('.' + aClass).each(function() {
                jQuery(this).css('display', 'table-row');
                jQuery(this).removeClass('collapsed');
            });
            lTr.removeClass('collapsed');
        }
        else {
            lTr.addClass('collapsed');
            jQuery('.' + aClass).each(function() {
                jQuery(this).css('display', 'none');
                jQuery(this).addClass('collapsed');
            });
        }
    },
//    expandAllTree : function(aElement, aClass) {
//        var lTbl = $(aElement).up('.tbl');
//        lTbl.getElementsBySelector('.' + aClass).each(function(aEl) {
//            aEl.style.display = 'table-row';
//            aEl.removeClassName('collapsed');
//        });
//    },
    expandAllTree : function(aElement, aClass) {
        var lTbl = jQuery(aElement).closest('.tbl');
        lTbl.find('.' + aClass).each(function() {
            jQuery(this).css('display', 'table-row');
            jQuery(this).removeClass('collapsed');
        });
    },
//    collapseAllTree: function (aElement, aClass, aExcept) {
//        var lTbl = $(aElement).up('.tbl');
//        lTbl.getElementsBySelector('.' + aClass).each(function (aEl) {
//            if (!aEl.hasClassName(aExcept)) {
//                aEl.style.display = 'none';
//                aEl.addClassName('collapsed');
//            }
//        });
//    }
    collapseAllTree : function(aElement, aClass, aExcept) {
        var lTbl = jQuery(aElement).closest('.tbl');
        lTbl.find('.' + aClass).each(function() {
            if(!jQuery(this).hasClass(aExcept)) {
                jQuery(this).css('display', 'none');
                jQuery(this).addClass('collapsed');
            }
        });
    },
    
    log: function (aText) { // you can pass any number of params, aText is just an example
    	if (console && console.log) {
    		Flow.log = function() { console.log.apply(console, arguments);};
    		console.log.apply(console, arguments);
    	} else {
    		Flow.log = function() {};
    }
    }

};



Flow.timesheet = {
    curDate : null,
    curAjax : null,
    showDialog : function(aSrc, aJobId) {
        var lDiv = jQuery('<div />');
        jQuery.get('index.php?act=job-' + aSrc + '.timedlg&jid=' + aJobId, function(data) {
            lDiv.html(data);
            jQuery(lDiv).dialog({
                title : 'Add time record',
                width : 750,
                height : 650,
                close : function(event, ui) {
                    lDiv.remove();
                }
            });
        });
    },
    onArtChanged : function() {
        var lVal = jQuery("select.field_art option:selected").val();
        if("4020" == lVal) {
            jQuery(".fie_menge").show();
            jQuery(".fie_hours").hide();
            jQuery(".fie_minutes").hide();
        } else {
            jQuery(".fie_menge").hide();
            jQuery(".fie_hours").show();
            jQuery(".fie_minutes").show();
        }
    },
    onSearchChange : function() {
        if(Flow.timesheet.curAjax) {
            try {
                Flow.timesheet.curAjax.abort();
            } catch(e) {
            }
        }
        jQuery('.field_jobid').fadeOut('fast');
        var lParam = {
            term : jQuery('.field_search').val(),
            jid : jQuery('.field_jobid').val()
        };
        Flow.timesheet.curAjax = jQuery.post('index.php?act=job-rep.timesearch', lParam, function(aResponse) {
            jQuery('.field_jobid').replaceWith(aResponse).fadeTo('fast', 0.7, function() {
                jQuery(this).fadeTo('fast', 1);
            });
            Flow.timesheet.curAjax = null;
        });
    },
    saveRecord : function(aElem, aSaveAsCopy) {
        var lForm = jQuery(aElem).closest('form');
        if(aSaveAsCopy) {
            jQuery('.field_id').val('');
        }
        var lParam = jQuery(lForm).serialize();

        $('pag_progress').show();
        jQuery.post('index.php?act=job-rep.timesave', lParam, function(aData) {
            jQuery('.time-list').html(aData);
            $('pag_progress').hide();
        });
    },
    resetForm : function() {
        jQuery(".field_hours").val("");
        jQuery(".field_id").val("");
        jQuery(".field_minutes").val("");
        jQuery(".field_comment").val("");
        jQuery('.ts-edit-btn').hide();
    },
    setDate : function(aDate, aDate2) {
        var lArr = aDate.split('-');
        var lDeDate = lArr[2] + '.' + lArr[1] + '.' + lArr[0];
        if('undefined' == typeof (aDate2)) {
            jQuery('.field_date').val(lDeDate);
        } else {
            aDate += ',' + aDate2;
        }
        $('pag_progress').show();
        jQuery('.time-list').load('index.php?act=job-rep.timelist&d=' + aDate);
        jQuery('.time-calendar').load('index.php?act=job-rep.timecal&d=' + aDate);
        Flow.timesheet.curDate = aDate;
        $('pag_progress').hide();
    },
    deleteRecord : function(aId, aJid) {
        if(!confirm('Are you sure?'))
            return;
        var lParam = {
            id : aId,
            jid : aJid
        };
        if(null !== Flow.timesheet.curDate) {
            lParam['date'] = Flow.timesheet.curDate;
        }
        $('pag_progress').show();
        jQuery.post('index.php?act=job-rep.timedel', lParam, function(aData) {
            jQuery('.time-list').html(aData);
            $('pag_progress').hide();
        });
    },
    editRecord : function(aCell) {
        var lTr = jQuery(aCell).closest('tr');
        var lKey = jQuery(lTr).attr('data-key');
        var lParam = {
            key : lKey
        };
        jQuery('.time-form').load('index.php?act=job-rep.timeedit', lParam);
    }

};

Flow.uteam = function() {
    this.parents = new Object();
    this.observers = new Object;

    var that = this;
    /*
     Event.observe(window, 'load', function() {
     that.register();
     });
     */
    jQuery(window).load(function() {
        that.register();
    });

    this.addChild = function(aParent, aField, aGroup) {
        if(!this.parents[aParent]) {
            this.addParent(aParent);
        }
        this.observers[aParent][aField] = aGroup;
    };

    this.addParent = function(aParent) {
        this.parents[aParent] = new Object();
        this.observers[aParent] = new Object();
        var that = this;
        new Field.EventObserver(aParent, function(field, value) {
            that.changed(field, value);
        });
    };

    this.register = function() {
        for(var lIndex in this.parents) {
            this.changed($(lIndex), $F(lIndex));
        }
    };

    this.changed = function(aField, aValue) {
        var lObs = this.observers[aField.id];
        var lParams = new Object();
        lParams['team'] = aValue;
        for(var field in lObs) {
            lParams['upd[' + field + ']'] = lObs[field];

        }
        var that = this;
        new Ajax.Request('index.php?act=ajx.uselect2', {
            method : 'post',
            parameters : lParams,
            onSuccess : function(transport) {
                var lRes = jQuery.parseJSON(transport.responseText);

                var lOArr = [];
                jQuery.each(lRes, function(lOId, lOObj) {
                    var lIArr = [];

                    jQuery.each(lOObj, function(lIId, lIObj) {
                        // lIArr[lIId] = lIObj;
                        lIArr.push({
                            key : lIId,
                            value : lIObj
                        });
                    });

                    lIArr.sort(function(p, q) {
                        if(p.value > q.value) {
                            return 1;
                        }
                        if(p.value < q.value) {
                            return -1;
                        }
                        return 0;
                    });

                    lOArr[lOId] = lIArr;
                });

                //var lRes = (transport.responseText).evalJSON();
                lRes = lOArr;

                for(var field in lObs) {
                    var lMem = (lRes[field]) ? lRes[field] : null;
                    that.doassign(field, lMem);
                }

            }
        });

    };

    this.doassign = function(aField, aMembers) {
        var lDest = $(aField);
        var lOld = $F(aField);
        lDest.length = 0;
        var lTmp = new Option('\xa0', '');
        lDest.options[lDest.length] = lTmp;

        var lNum = 0;
        for(var lKey in aMembers) {
            if(isNaN(lKey))
                break;
            lNum++;
        }
        for(var lKey in aMembers) {
            if(isNaN(lKey))
                break;

            var lId = aMembers[lKey].key;
            var lName = aMembers[lKey].value;

            var lSel = ((lNum == 1) || (lOld == lId)); // lId formerly know as lKey
            lTmp = new Option(lName, lId, lSel, lSel); // lId formerly know as lKey
            lDest.options[lDest.length] = lTmp;
        }
    };
};
Flow.uteams = new Flow.uteam();

Flow.apl = {
    getOverview : function(aEventId, aJid, aPrefix, aContainer) {
//        new Ajax.Updater(aContainer, 'index.php', {
//            parameters : {
//                act : 'ajx.apl',
//                event_id : aEventId,
//                jid : aJid,
//                prefix : aPrefix
//            }
//        });

        jQuery('#' + aContainer).load('index.php', {
            act : 'ajx.apl',
            event_id : aEventId,
            jid : aJid,
            prefix : aPrefix
        }
        );
    },
    updateOverview : function(aJid, aPrefix, aContainer) {
        new Ajax.Updater(aContainer, 'index.php', {
            parameters : {
                act : 'ajx.updateapl',
                jid : aJid,
                prefix : aPrefix
            }
        });
    },
    showAddRevisorDlg : function(aMod, aJid, aPrefix, aDiv, aDesc, aCanDeselect) {
        aDesc = (typeof aDesc == 'undefined') ? true : aDesc;
        var lDiv = jQuery('#apl_add_dlg');
        var lParam = {
            mod : aMod,
            jid : aJid,
            prefix : aPrefix,
            desc : aDesc,
            canDeselect : aCanDeselect
        };
        jQuery.post('index.php?act=' + aMod + '.getrevdlg', lParam, function(
                aData) {
            lDiv.html(aData);
            jQuery(lDiv).dialog({
                title : 'Add Revisor to ' + aPrefix,
                modal : true,
                width : 600,
                buttons : {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var lParams = {
                            act : 'ajx.addapluser',
                            jid : aJid,
                            mod : aMod,
                            prefix : aPrefix,
                            pos : jQuery('#apl_pos').val(),
                            days : jQuery('#apl_days').val(),
                            src : jQuery('#apl_src').val(),
                            canDeselect : aCanDeselect
                        };
                        jQuery.post('index.php', lParams, function(aHtml) {
                            jQuery('#' + aDiv).html(aHtml);
                        });
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    },
    showAddReaderDlg : function(aMod, aDiv) {
        jQuery('<div>').dialog({
            title : 'Add Reader',
            buttons : {
                Okay : function() {
                    jQuery(this).dialog('close');
                },
                Cancel : function() {
                    jQuery(this).dialog('close');
                }
            }
        });
    },
    deleteAction : function(aElem, aMod, aJid, aPrefix, aHash) {
        var lConfirm = confirm('Really delete?');
        if(!lConfirm)
            return;
        var lDiv = jQuery(aElem).closest('.bc-apl');
        var lReq = {
            prefix : aPrefix,
            jid : aJid,
            hash : aHash,
            div : jQuery(lDiv).attr('id')
        };
        jQuery.post('index.php?act=' + aMod + '.delaction', lReq, function(aData) {
            jQuery(lDiv).html(aData);
        });
    },
    selectFunction : function(aElem) {
        lElem = jQuery(aElem);
        var lSelect = jQuery('#apl_src');
        jQuery.get('index.php?act=ajx.getstruc&func=' + lElem.val(), function(
                aData) {
            jQuery('option', lSelect).remove();
            var lOptions = (lSelect.prop) ? lSelect.prop('options') : lSelect
                    .attr('options');
            var lUsers = aData.usr;
            var lGroups = aData.gru;

            jQuery.each(lUsers, function(val, text) {
                lOptions[lOptions.length] = new Option(text, val);
            });
            jQuery.each(lGroups, function(val, text) {
                lOptions[lOptions.length] = new Option(text, val);
            });
        }, 'json');
    },
    addRevisorDialog : function(aSrc, aJid, aUrl, aShowForward, aShowExpand) {
        var lDiv = jQuery('<div />');
        var lParam = {
            pos : 1,
            jid : aJid
        };
        var lButtons = {
            Add : function() {
                jQuery(this).dialog('close');
                var lParams = {
                    act : 'job-apl-page.addapluser',
                    jid : aJid,
                    src : aSrc,
                    method : 'add',
                    prefix : jQuery('#into_apl_prefix').val(),
                    aplpos : jQuery('#apl_pos').val(),
                    usr : jQuery('#apl_src').val()
                };
                jQuery.post('index.php', lParams, function(aHtml) {
                    if(typeof (aUrl) != 'undefined') {
                        go(aUrl);
                    }
                });
            }
        };

        if(aShowForward) {
            lButtons['Forward'] = function() {
                var lHtml = 'The selected user/group will participate and replace you in the workflow.\n\nYou will not be able to approve anymore. Your decision is not reversible! Do you wish to proceed?';
                var lParams = {
                    act : 'job-apl-page.addapluser',
                    jid : aJid,
                    src : aSrc,
                    method : 'forward',
                    prefix : jQuery('#into_apl_prefix').val(),
                    usr : jQuery('#apl_src').val()
                };

                if(confirm(lHtml)) {
                    jQuery.post('index.php', lParams, function(aHtml) {
                        if(typeof (aUrl) != 'undefined') {
                            go(aUrl);
                        }
                    });
                    jQuery(this).dialog('close');
                }
            };
        }

        if(aShowExpand) {
            lButtons['Expand'] = function() {
                jQuery(this).dialog('close');
                var lParams = {
                    act : 'job-apl-page.addapluser',
                    jid : aJid,
                    src : aSrc,
                    method : 'expand',
                    prefix : jQuery('#into_apl_prefix').val(),
                    usr : jQuery('#apl_src').val()
                };
                jQuery.post('index.php', lParams, function(aHtml) {
                    if(typeof (aUrl) != 'undefined') {
                        go(aUrl);
                    }
                });
            };
        }

        lButtons['Cancel'] = function() {
            jQuery(this).dialog('destroy').remove();
        };

        jQuery.post('index.php?act=job-' + aSrc + '.getrevdlg', lParam,
                function(aData) {
                    lDiv.html(aData);
                    jQuery(lDiv).dialog({
                        title : 'Add Revisor',
                        modal : true,
                        width : 350,
                        buttons : lButtons
                    });
                }
        );
    },
    intouchSelectFunction : function(aElem) {
        lElem = jQuery(aElem);
        var lSelectUsr = jQuery('#apl_src');
        var lSelectGrp = jQuery('#apl_src_grps');
        var lSelectFil = jQuery('#filter_grps');

        var lJid = jQuery("input[name='jobid']").val();
        var lSrc = jQuery("input[name='act']").val().split("-")[1];

        jQuery.get('index.php?act=ajx.getstruc&func=' + lElem.val() + '&jid=' + lJid + '&src=' + lSrc, function(aData) {
            jQuery('option', lSelectUsr).remove();
            jQuery('option', lSelectGrp).remove();
            jQuery('option', lSelectFil).remove();

            var lOptionsUsr = (lSelectUsr.prop) ? lSelectUsr.prop('options') : lSelectUsr.attr('options');
            var lOptionsGrp = (lSelectGrp.prop) ? lSelectGrp.prop('options') : lSelectGrp.attr('options');
            var lOptionsFil = (lSelectFil.prop) ? lSelectFil.prop('options') : lSelectFil.attr('options');

            var lUsers = aData.usr;
            var lGroups = aData.gru;

            jQuery.each(lGroups, function(val, text) {
                lOptionsFil[lOptionsFil.length] = new Option(text, val);
            });
            jQuery.each(lUsers, function(val, text) {
                lOptionsUsr[lOptionsUsr.length] = new Option(text, val);
            });
            jQuery.each(lGroups, function(val, text) {
                lOptionsGrp[lOptionsGrp.length] = new Option(text, val);
            });
        }, 'json');
    },
    intouchSelectUsers : function(aElem) {
        lElem = jQuery(aElem);
        var lSelectUsr = jQuery('#apl_src');

        jQuery.get('index.php?act=ajx.getusrs&func=' + lElem.val(), function(aData) {
            jQuery('option', lSelectUsr).remove();

            var lOptionsUsr = (lSelectUsr.prop) ? lSelectUsr.prop('options') : lSelectUsr.attr('options');
            var lUsers = aData.usr;

            jQuery.each(lUsers, function(val, text) {
                lOptionsUsr[lOptionsUsr.length] = new Option(text, val);
            });
        }, 'json');
    },
    intouchShowAddRevisorDlg : function(aMod, aJid, aPrefix, aDiv, aDesc) {
        aDesc = (typeof aDesc == 'undefined') ? true : aDesc;
        var lDiv = jQuery('#apl_add_dlg');
        var lParam = {
            mod : aMod,
            jid : aJid,
            prefix : aPrefix,
            desc : aDesc
        };
        jQuery.post('index.php?act=' + aMod + '.getrevdlg', lParam, function(aData) {
            lDiv.html(aData);
            jQuery(lDiv).dialog({
                title : 'Add Revisor',
                modal : true,
                width : 340,
                buttons : {
                    Okay : function() {
                        var lUsrs = jQuery('#apl_src').val();
                        var lGrps = jQuery('#apl_src_grps').val();
                        if(lUsrs != null || lGrps != null) {
                            jQuery("#pag_progress").show();
                            if(lUsrs !== null && lGrps !== null) {
                                var lUsrGrps = jQuery.merge(lUsrs, lGrps);
                            } else {
                                if(lUsrs != null)
                                    var lUsrGrps = lUsrs;
                                if(lGrps != null)
                                    var lUsrGrps = lGrps;
                            }

                            var lParams = {
                                act : 'ajx.addapluser',
                                jid : aJid,
                                prefix : aPrefix,
                                pos : jQuery('#apl_pos').val(),
                                days : jQuery('#apl_days').val(),
                                src : lUsrGrps
                            };
                            jQuery.post('index.php', lParams, function(aHtml) {
                                jQuery('#' + aDiv).html(aHtml);
                                jQuery("#pag_progress").hide();
                            });
                        }
                        jQuery(this).dialog('close');
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    },
    intouchAddRevisorDialog : function(aSrc, aJid, aUrl, aShowForward, aShowExpand) {
        var lDiv = jQuery('<div />');
        var lParam = {
            pos : 1,
            jid : aJid
        };
        var lButtons = {
            Add : function() {
                var lUsrs = jQuery('#apl_src').val();
                var lGrps = jQuery('#apl_src_grps').val();
                if(lUsrs != null || lGrps != null) {
                    jQuery("#pag_progress").show();
                    if(lUsrs !== null && lGrps !== null) {
                        var lUsrGrps = jQuery.merge(lUsrs, lGrps);
                    } else {
                        if(lUsrs != null)
                            var lUsrGrps = lUsrs;
                        if(lGrps != null)
                            var lUsrGrps = lGrps;
                    }

                    var lParams = {
                        act : 'job-apl-page.addapluser',
                        jid : aJid,
                        src : aSrc,
                        method : 'add',
                        prefix : jQuery('#into_apl_prefix').val(),
                        aplpos : jQuery('#apl_pos').val(),
                        usr : lUsrGrps
                    };
                    jQuery.post('index.php', lParams, function(aHtml) {
                        jQuery("#pag_progress").hide();
                        if(typeof (aUrl) != 'undefined') {
                            go(aUrl);
                        }
                    });
                }
                jQuery(this).dialog('destroy').remove();
            }
        };

        lButtons['Cancel'] = function() {
            jQuery(this).dialog('destroy').remove();
        };

        jQuery.post('index.php?act=job-' + aSrc + '.getrevdlg', lParam,
                function(aData) {
                    lDiv.html(aData);
                    jQuery(lDiv).dialog({
                        title : 'Add Revisor',
                        modal : true,
                        width : 340,
                        buttons : lButtons
                    });
                }
        );
    },
    reloadAplOverView : function() {
        var lIcoDiv = jQuery('.apl-commit');
        var lParams = {};
        lParams['src'] = lIcoDiv.attr('data-src');
        lParams['jobid'] = lIcoDiv.attr('data-jid');
        lParams['act'] = 'job-apl2.reloadcommit';

        lIcoDiv.load('index.php', lParams);
    },
    setStatus : function(aId, aFlag, aFlagName, aSubId) {
        jQuery('#apl-comment').val('');
        jQuery.post('index.php?act=ajx.prf&key=apl.sid&val=' + aSubId);
        if (aFlagName == 'Finished') {
            jQuery('#apl-upload').show();
        } else {
        	jQuery('#apl-upload').hide();
        }
        jQuery('#apl-dlg').dialog({
            modal : true,
            title : aFlagName,
            width : '450px',
            resizable : false,
            buttons : {
                Okay : function() {
                    jQuery('#apl-dlg').dialog('close');
                    var lParams = {
                        id : aId,
                        flag : aFlag,
                        comment : jQuery('#apl-comment').val()
                    };
                    jQuery.post('index.php?act=job-apl2.set', lParams,
                            function(aResponse) {
                                jQuery('.sid' + aSubId).replaceWith(aResponse['content']);
                                jQuery('.sid' + aSubId).closest('.bc-prefix-cont').prev('.bc-prefix-row').find('.my-apl-icon').html(aResponse['icons']);
                                Flow.apl.reloadAplOverView();
                            }, 'json'
                            );
                },
                'Cancel' : function() {
                    jQuery('#apl-dlg').dialog('close');
                }
            }
        });
    },
    resetStatus : function(aId, aSubId) {
        jQuery.post('index.php?act=job-apl2.reset&id=' + aId,
                function(aResponse) {
                    jQuery('.sid' + aSubId).replaceWith(aResponse['content']);
                    jQuery('.sid' + aSubId).closest('.bc-prefix-cont').prev('.bc-prefix-row').find('.my-apl-icon').html(aResponse['icons']);
                    Flow.apl.reloadAplOverView();
                }, 'json'
                );

    },
    restartSubLoop : function(aId, aSubId, aAll) {
        var icons = jQuery('.sid' + aSubId).closest('.bc-prefix-cont').prev('.bc-prefix-row').find('.my-apl-icon');
        jQuery('#apl-comment').val('');
        jQuery('#apl-dlg').dialog({
            modal : true,
            title : 'Restart',
            width : '450px',
            resizable : false,
            buttons : {
                Okay : function() {
                    jQuery('#apl-dlg').dialog('close');
                    var lParams = {
                        id : aId,
                        sid : aSubId,
                        comment : jQuery('#apl-comment').val(),
                        all : aAll
                    };
                    jQuery.post('index.php?act=job-apl2.restart', lParams,
                            function(aResponse) {
                                jQuery('.sid' + aSubId).replaceWith(aResponse['content']);
                                icons.html(aResponse['icons']);
                                Flow.apl.reloadAplOverView();
                            }, 'json'
                            );
                },
                'Cancel' : function() {
                    jQuery('#apl-dlg').dialog('close');
                }
            }
        });
    },
    expandParentsOf : function(aSelector) {
        jQuery(aSelector).each(function() {
            Flow.apl.expandParents(this)
        });
    },
    expandParents : function(aElem) {
        var cur = jQuery(aElem);
        var old = cur;
        if(typeof (cur) == "undefined") {
            cur = null;
        }
        while(cur) {
            cur.show();
            cur = jQuery(cur).parent();
            if(typeof (cur) == "undefined") {
                cur = null;
                break;
            }
            if(cur == old) {
                cur = null;
                break;
            }
            if(cur.hasClass('bc-apl')) {
                cur = null; // exit loop
                break;
            }
            old = cur;
        }
    },
    collapse : function(aSelector) {
        jQuery(aSelector).hide();
    }

};

Flow.wec = {
    thumb : function(aJobId, aWecDocId, aWecVerId, aName, aLnk, aId) {
//    var lLnk = Flow.Std.html_entity_decode(aLnk);
//    var lName = jQuery("td[id=\"" + aWecDocId + "\"]");
//    var lLink = jQuery("img[id=\"" + aId + "\"]");

//    var lInterval = setInterval(function() {
//      var lThumbExists = jQuery.get("index.php?act=utl-wec.thumbexists&doc=" + aWecDocId + "&ver=" + aWecVerId + "&jobid=" + aJobId, function() {})
//        .done(function(data) {
//          if (data == 1) {
        jQuery("img[id=\"" + aId + "\"]").attr("src", "index.php?act=utl-wec.thumb&doc=" + aWecDocId + "&ver=" + aWecVerId + "&jobid=" + aJobId);
//            lName.html(lLnk + aName + "</a>");
//            lLink.wrap(lLnk + "</a>");
//            clearInterval(lInterval);
//          }
//      });
//    }, 2000);
    }
};

Flow.thumbnail = {
    setupReload : function(aImg, aJid) {
        var lUrl = 'index.php?act=ajx.getthumbnail&jid=' + aJid;
        setTimeout(this.reload.bind(this, aImg, lUrl), 5000);
    },
    reload : function(aImg, aUrl) {
        jQuery.get(aUrl, function(aData) {
            if(aData == 'na') {
                setTimeout(Flow.thumbnail.reload.bind(this, aImg, aUrl), 5000);
            } else {
                $(aImg).src = aData;
            }
        });
    },
    update : function(aParams) {
        jQuery.ajax({
            type : 'post',
            url : 'index.php?act=ajx.updatethumbnail',
            data : aParams
        }).done(function(data) {
            var msg = jQuery.parseJSON(data);

            var lErr = msg['err'];
            var lMsg = msg['msg'];
            var lImg = msg['img'];
            var lThb = msg['thb'];

            if (lErr) {
                alert(lErr);
            }

            if (lMsg) {
                alert(lMsg);
            }

            if (lImg) {
                jQuery('a[id="a' + aParams.jobid + '"]').attr('data-tooltip-body', "<img src='" + lImg + "' border'0p' width='300' height='300px' alt='' />");
            }

            if (lThb) {
                jQuery('img[id="img' + aParams.jobid + '"]').attr('src', lThb);
            }
        });
    }
};
    
Flow.event = {
    onFunctionChange : function(aElem) {
        lElem = jQuery(aElem);
        var lSelect = jQuery('.bc-group');
        var lOld = lSelect.val();

        var lUrl = 'index.php?act=ajx.getfunctiongroups&func=' + lElem.val();
        var lEve = lElem.attr('data-eve');
        if(lEve)
            lUrl += '&eve=' + lEve;
        jQuery.get(lUrl, function(aData) { 
          //Sort
          var DataArr=[];
          //Generate Array from object
          for(var key in aData) {
            if(aData.hasOwnProperty(key)) {
              DataArr.push([key, aData[key]]);
            }
          }
          //Sort with custom Compare Function
          DataArr.sort(function(a, b) {
            var x=a[1].toLowerCase(),
            y=b[1].toLowerCase();
            return x<y ? -1 : x>y ? 1 : 0;
          });
          //Remove old Options from Select
          jQuery('option', lSelect).remove();
          var lOptions = (lSelect.prop) ? lSelect.prop('options') : lSelect
            .attr('options');
          //Set new Options for Select
          jQuery.each(DataArr, function(i, val) {
            lOptions[lOptions.length] = new Option(val[1], val[0]);
          });
          lSelect.val(lOld);
        }, 'json');
    }
};

Flow.page = {
    feedbackDialog : function() {
        var lDiv = jQuery('<div />');
        jQuery.get('index.php?act=hom-wel.feedback', function(data) {
            lDiv.html(data);
            jQuery(lDiv).dialog({
                title : 'Feedback',
                modal : true,
                width : 600,
                buttons : {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var lParams = {
                            act : 'hom-wel.sfeedback',
                            msg : jQuery('#feedback_msg').val()
                        };
                        jQuery.post('index.php', lParams, function(aResponse) {
                            alert(aResponse);
                        });
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
        jQuery.update();
    }
};

Flow.user = {
    pasteMem : function(aAdd) {
        var all = '';
        jQuery('.cb:checked').each(function() {
            all += jQuery(this).val() + ',';
        });
        all = all.substr(0, all.length - 1);
        var url = 'index.php?act=usr.pastemem&ids=' + all;
        if(aAdd) {
            url += '&add=1';
        }
        location.href = url;
    }
};

Flow.groups = {
    showMembers : function(aGroupId, aContainer) {
        jQuery('#' + aContainer).load(
                'index.php?act=ajx.getmembers&gid=' + aGroupId);
    },
    showMembersOnce : function(aGroupId, aContainer) {
        var lContainer = jQuery('#' + aContainer);
        if(lContainer.hasClass('loaded'))
            return;
        Flow.groups.showMembers(aGroupId, aContainer);
        lContainer.addClass('loaded');
    },
    memberDialog : function(aGroupId, aContainer) {
        var lDiv = jQuery('<div />');
        jQuery.get('index.php?act=structuregroups.mem&gid=' + aGroupId, function(data) {
            lDiv.html(data);
            jQuery(lDiv).dialog({
                title : 'Members',
                modal : true,
                width : 700,
                buttons : {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var members = [];
                        jQuery('#dst option').each(function() {
                            members.push(jQuery(this).val());
                        });
                        var lParams = {
                            act : 'structuregroups.smem',
                            gid : aGroupId,
                            old : jQuery('#old_mem').val(),
                            mem : members
                        };
                        jQuery(lDiv).remove();
                        jQuery.post('index.php', lParams, function(aResponse) {
                            jQuery('#' + aContainer).html(aResponse['mem']);
                            jQuery('.app-gru' + aGroupId).html(aResponse['count']);
                        }, 'json');
                    },
                    Cancel : function() {
                        jQuery(lDiv).remove();
                        jQuery(this).dialog('close');
                    }
                }
            });
        });


    }

};

Flow.formula = function() {
    var lSeparator = ',';
    var lMinRows = 1;
    var lMaxRows = 30;

    var that = this;

    this.add = function(aInpVal) {
        var lTrLength = jQuery('#formula_tbody > tr').length;

        if(lTrLength < lMaxRows) {
            jQuery('#formula_tbody').append(
                    '<tr id="' + (lTrLength + 1) + '">' +
                    '<td style="width: 5%; padding: 2px;" class="ac">' +
                    '<span class="app-version w30">' + (lTrLength + 1) + '.</span>' +
                    '</td>' +
                    '<td style="width: 90%; padding: 2px;">' +
                    '<input type="text" class="inp w100p">' +
                    '</td>' +
                    '<td style="width: 5%; padding: 2px; text-align: center;">' +
                    Flow.Std.imgFinder("img/ico/16/ml-8.gif", {
                        "alt" : "Remove",
                        "id" : "formula_remove_" + (lTrLength + 1),
                        "onClick" : "Flow.formu.remove(this);"
                    }) +
                    '</td>' +
                    '</tr>'
                    );

            if(aInpVal) {
                jQuery('#formula_tbody tr[id="' + (lTrLength + 1) + '"] input').val(aInpVal);
            }
        }
    };

    this.remove = function(aElem) {
        var lTrLength = jQuery('#formula_tbody > tr').length;

        if(lTrLength > lMinRows) {
            var lId = jQuery('#' + aElem.id).closest('tr').attr('id');

            jQuery('#formula_tbody tr[id="' + lId + '"]').remove();

            jQuery('#formula_tbody').find('tr').each(function(aIndex) {
                jQuery(this).prop('id', (aIndex + 1));
                jQuery(this).find('span').text((aIndex + 1) + '.');
                jQuery(this).find('img').prop('id', 'formula_remove_' + (aIndex + 1));
            });

        }
    };

    this.stick = function() {
        var lRes = '';
        var lValid = true;

        jQuery('#formula_tbody').find('input').each(function() {
            var lFormuNo = jQuery(this).parent().parent().attr("id");
            var lInpVal = jQuery(this).val();
            if(lInpVal != undefined && lInpVal != '' && lValid == true) {
                //replace \ with /
                lInpVal = lInpVal.replace("/", "\\");

                //check lInpVal with regexp
                /*var lRegArra = {"^F{1}":["0","1"],
                 "^F{1}[A-Z]{2}":["1","3"],
                 "^F{1}[A-Z]{2}[0-9]{2}":["3","5"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}":["5","7"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}":["7","11"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}[\\\\]{1}":["11","12"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}[\\\\]{1}[0-9]{2}":["12","14"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}[\\\\]{1}[0-9]{2}\s*":["14","16"],
                 "^F{1}[A-Z]{2}[0-9]{2}[A-Z]{2}[0-9]{4}[\\\\]{1}[0-9]{2}\s*\w*":["16","0"]};*/
                var lRegArra = {
                    "^F{1}" : ["0", "1", "First character has to be 'F'"],
                    "^F{1}[A-Z0-9]{10}" : ["1", "11", "First word has to be 10 characters long"],
                    "^F{1}[A-Z0-9]{10}[\\\\]{1}" : ["11", "12", "The characters above needs to be a backslash"],
                    "^F{1}[A-Z0-9]{10}[\\\\]{1}[0-9]{2}" : ["12", "14", "There needs to be two digits after the backslash"]
                };

                for(lReg in lRegArra) {
                    var lStrPos = lRegArra[lReg];
                    var lStart = lStrPos[0];
                    var lFinish = lStrPos[1];
                    var lReason = lStrPos[2];

                    var lBegin = lInpVal.substr(0, lStart);
                    var lEnd = lInpVal.substr(lFinish, lInpVal.length);

                    var lFormuRegex = new RegExp(lReg);
                    var lTest = lFormuRegex.test(lInpVal);

                    if(lInpVal.length !== 14) {
                        lTest = false;
                        lFinish = "0";
                        lEnd = "";
                        lReason = "Formula needs to be 14 characters long";
                    }

                    if(!lTest) {
                        var lError = lBegin + "<span class='red'>";
                        lError += (lFinish !== "0" ? lInpVal.substring(lStart, lFinish) : lInpVal.substring(lStart));
                        lError += "</span>" + lEnd;
                        var lHtm = "<i>Formula number " + lFormuNo + " does not comply.</i><br/><br/>" + lError + "<br/><br/><b>Reason:</b> " + lReason;
                        jQuery('<div>' + lHtm + '</div>').dialog({
                            title : 'Formula Code Incorrect!',
                            buttons : [{
                                    text : "OK",
                                    click : function() {
                                        jQuery(this).dialog('destroy');
                                    }
                                }],
                            height : 'auto',
                            width : 'auto',
                            modal : true
                        });
                        jQuery(this).attr("style", "border:solid red 1px;");
                        lValid = false;
                        break;
                    } else {
                        jQuery(this).removeAttr("style");
                    }
                }

                lRes += lInpVal + lSeparator;
            }
        });

        var regex = new RegExp(lSeparator + "+$");
        lRes = lRes.replace(regex, '');

        jQuery('input[name="val[formula_nr]"]').val(lRes);

        return lValid;
    };

    this.unstick = function() {
        var lRes = jQuery('input[name="val[formula_nr]"]').val();

        if(lRes.length > 0) {
            jQuery('#formula_tbody > tr').remove();

            lRes = lRes.split(lSeparator);
            lRes.each(function(aIndex) {
                that.add(aIndex);
            });
        } else {
            that.add();
        }
    };

    this.disable = function() {
        if(jQuery('input[name="val[formula_nr]"]').prop('disabled')) {
            jQuery('#formula_thead img').hide();
            jQuery('#formula_tbody img').hide();
            jQuery('#formula_tbody input').prop('disabled', true);
        }
    };

    this.check = function() {
        var lFormu = this.stick();

        return (lFormu === true ? checkMandatoryFieldsByJob() : lFormu);
    };
};
Flow.formu = new Flow.formula();

Flow.mailform = function() {
    var lMinRows = 3;
    var lMaxRows = 20;

    this.add = function() {
        var lTrLengthAll = jQuery('#table_form tr[id]').length;
        var lTrLengthTrForm = jQuery('#table_form tr[id="tr_form"]').length;
        var lTrLength = lTrLengthAll - lTrLengthTrForm;

        if(lTrLength < lMaxRows) {
            jQuery('#table_form tr[id="subject"]:first').before(
                    this.getLineHtml(lTrLength + 1)
                    );
        }
        Flow.autocomplete.init();
    };

    this.remove = function(aElem) {
        var lTrLengthAll = jQuery('#table_form tr[id]').length;
        var lTrLengthTrForm = jQuery('#table_form tr[id="tr_form"]').length;
        var lTrLength = lTrLengthAll - lTrLengthTrForm;

        if(lTrLength > lMinRows) {
            var lId = jQuery('#' + aElem.id).closest('#table_form > tbody > tr').attr('id');
            jQuery('#table_form tr[id="' + lId + '"]').remove();
        }
    };

    this.load = function() {
        var i;

        for(i = 0; i < lMinRows; i++) {
            jQuery('#table_form tr[id="subject"]:first').before(
                    this.getLineHtml(i)
                    );
        }
        Flow.autocomplete.init();
    };

    // possible mandator override, so leave this function as is
    this.getCaption = function(aIndex) {
        return '';
    };

    this.getLineHtml = function(aIndex) {
        var src, alt, id, onclick;
        if(aIndex == 0) {
            src = 'img/ico/16/plus.gif';
            alt = 'Add';
            id = 'add';
            onclick = 'Flow.mail.add();';
        } else {
            src = 'img/ico/16/ml-8.gif';
            alt = 'Remove';
            id = 'remove';
            onclick = 'Flow.mail.remove(this);';
        }
        var caption = this.getCaption(aIndex);
        var lRet =
                '<tr id="' + aIndex + '">' +
                '<td style="width: 5%; vertical-align: center;">' + caption + '</td>' +
                '<td style="width: 95%; padding: 0px;">' +
                '<table cellpadding="2" cellspacing="0" border="0" width="100%">' +
                '<tbody>' +
                '<tr>' +
                '<td style="width: 95%;">' +
                '<input type="hidden" name="old[inpMailAddr' + aIndex + ']" value>' +
                '<input id="inpMailAddr' + aIndex + '" data-change="autocomplete" data-source="ajx.usremail" name="val[inpMailAddr' + aIndex + ']" type="text" class="inp w100p">' +
                '</td>' +
                '<td style="width: 5%; text-align: center;">' +
                '<img src="' + src + '" alt="' + alt + '" id="' + id + '" onClick="' + onclick + '">' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</td>' +
                '</tr>';
        return lRet;
    };
};
Flow.mail = new Flow.mailform();

Flow.dalim = {
    compare : function(aSrc, aJid) {
        var sel = new Array();
        jQuery('.beh-dalim-comp:checked').each(function() {
            sel.push(jQuery(this).val());
        });

        var htm = '<form action="index.php" method="post">' +
                '<input type="hidden" name="act" value="utl-dalim.multi" />' +
                '<input type="hidden" name="src" value="' + aSrc + '" />' +
                '<input type="hidden" name="jid" value="' + aJid + '" />';

        jQuery('.beh-dalim-comp:checked').each(function() {
            htm += '<input type="hidden" name="docs[]" value="' + jQuery(this).val() + '" />';
        });
        htm += '</form>';
        form = jQuery(htm);

        jQuery('body').append(form);

        form.submit();        
    }
};

Flow.category = function() {
    this.change = function(aStd) {
        var lAge = aStd['age']; // active (job) or archived (arc) job
        var lSrc = aStd['src']; // job source/type: art, rep, etc.
        var lJId = aStd['jid']; // job id
        var lSub = aStd['sub']; // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
        var lCat = aStd['cat']; // category
        var lFil = aStd['fil']; // filename
        var lOld = aStd['old']; // old category
        var lNew = aStd['new']; // new category
        var lDiv = aStd['div']; // div id

        var lSelect = jQuery('<select style="width:100%;height:100%;">').appendTo('p[id="select"]');
        
        var DataArr=[];
          //Generate Array from object
          for(var key in lCat) {
            if(lCat.hasOwnProperty(key)) {
              DataArr.push([key, lCat[key]]);
            }
          }
          //Sort with custom Compare Function
          DataArr.sort(function(a, b) {
            var x=a[1].toLowerCase(),
            y=b[1].toLowerCase();
            return x<y ? -1 : x>y ? 1 : 0;
          });
        
        jQuery.each(DataArr, function(i, val) {
            lSelect.append(jQuery("<option>").attr('value', val[0]).text(val[1]));
        });
        // set old category when creating dialog box
        jQuery().ready(function() {
            jQuery('select').val(lOld);
        });

        // set new category when changing select box
        jQuery('select').bind('change', function() {
            lNew = jQuery("option:selected", this).val();
        });

        jQuery("#category-change").dialog({
            buttons : {
                Ok : function(aEvent) {
                    aEvent.preventDefault();

                    lNew = jQuery("option:selected", this).val();

                    jQuery.ajax({
                        type : 'post',
                        url : 'index.php?act=job-fil.changecategory',
                        data : {
                            src : lSrc,
                            jid : lJId,
                            sub : lSub,
                            fil : lFil,
                            div : lDiv,
                            'new' : lNew
                        }
                    }).fail(function() {
                        alert('File category could not be changed!');
                    });

                    jQuery('p[id="select"]').empty();
                    jQuery(this).dialog("close");

                    var lParams = {
                      'act': 'job-'+lSrc+'-fil.get',
                      'src': lSrc,
                      'jid': lJId,
                      'sub': lSub,
                      'div': lDiv,
                      'age': lAge
                    };
                    Flow.Std.ajxUpd(lParams);
                },
                Cancel : function(aEvent) {
                    aEvent.preventDefault();

                    jQuery('p[id="select"]').empty();
                    jQuery(this).dialog("close");
                }
            },
            close : function(aEvent, aUI) {
                aEvent.preventDefault();

                jQuery('p[id="select"]').empty();
                jQuery(this).dialog("close");
            },
            modal : true,
            resizable : false
        });
    };
};
Flow.cat = new Flow.category();

Flow.IEversion = function() {
    this.detect = function() {
        var lVersion = -1;
        var lUserAgent = window.navigator.userAgent;
        var lMSIE = lUserAgent.indexOf('MSIE ');
        var lTrident = lUserAgent.indexOf('Trident/');

        if(lMSIE > 0) {
            // IE <= 10
            lVersion = parseInt(lUserAgent.substring(lMSIE + 5, lUserAgent.indexOf('.', lMSIE)), 10);
        } else if(lTrident > 0) {
            // IE > 10
            var lRevision = lUserAgent.indexOf('rv:');
            lVersion = parseInt(lUserAgent.substring(lRevision + 3, lUserAgent.indexOf('.', lRevision)), 10);
        }

        return lVersion;
    };
};
Flow.IEver = new Flow.IEversion();

Flow.MsgBox = {
    search : function() {
        var searchValue = jQuery('#pag-msg-search').val().toLowerCase();
        jQuery('.ml-all').each(function() {
            var text = jQuery(this).find('.td2').text().toLowerCase();
            var isTermFound = (text.indexOf(searchValue) > -1);
            if(isTermFound) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });
    },
    tog : function(aBtn, aClass) {
        if(jQuery(aBtn).hasClass('b')) {
            jQuery('.' + aClass).addClass(aClass + '-hide');
        } else {
            jQuery('.' + aClass).removeClass(aClass + '-hide');
        }
        jQuery(aBtn).toggleClass('b');
    }

};

Flow.job = {
    productions_data : function(aSrc, aJobId) {
        jQuery.ajax({
            type : 'post',
            url : 'index.php?act=job-' + aSrc + '.productions_data',
            data : {
                src : aSrc,
                jobid : aJobId
            }
        }).success(function() {
            alert('File(s) created!');
        }).fail(function() {
            alert('File(s) could not be created!');
        });
    },
    
    validate : function(aForm, aSuccessCallback) {
      var oldact = jQuery('input[name=act]').val();
      jQuery('input[name=act]').val('ajx.svalid');
      lParams = jQuery(aForm).serialize();
      jQuery('input[name=act]').val(oldact);
      jQuery.post('index.php', lParams, function(aResponse) {
        jQuery('.red').removeClass('red');
        var stat = aResponse['status'];
        if ('ok' == stat) {
          if (aSuccessCallback && aSuccessCallback instanceof Function) {
            aSuccessCallback.call();
          }
        } else {
          var errors = aResponse['errors'];
          var txt = '';
          for (var alias in errors) {
            jQuery('.field_' + alias).addClass('red');
            txt += errors[alias]+"\n";
          }
          alert(txt);
        }
      }, 'json');
      return false;
    }
};

Flow.diff = {
    search : function(aElem) {
        var text, isTermFound;
        var searchValue = jQuery(aElem).val().toLowerCase();
        if('' == searchValue) {
            jQuery(aElem).closest('table .tbl').children('tbody').find('tr').each(function() {
                jQuery(this).show();
            });
            jQuery('#app-alljob').hide();
            return;
        } else {
            jQuery('#app-alljob').show();
        }
        jQuery(aElem).closest('table .tbl').children('tbody').find('tr').each(function() {
            //jQuery(this).addClass('cy');
            isTermFound = false;
            jQuery(this).find('td').each(function() {
                text = jQuery(this).text().toLowerCase();
                if(text.indexOf(searchValue) > -1) {
                    isTermFound = true;
                }
            });
            if(isTermFound) {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });
    },
    togCheck : function(aSelector) {
        var first = jQuery(aSelector).first();
        var flag = !(first.prop('checked'));
        jQuery(aSelector).each(function() {
            jQuery(this).prop('checked', flag);
        });
    },
    togSame : function(aElem) {
        var rows = jQuery(aElem).closest('table.tbl').find('tr.hi');
        jQuery(rows).each(function() {
            if(!(jQuery(this).has('.cy').length)) {
                jQuery(this).toggle();
            }
        });
    },
    loadJob : function(aSrc, aJid, aXid) {
        var params = {
            src : aSrc,
            jid : aJid,
            xid : aXid,
            act : 'xchange.selectjob'
        };
        jQuery('#app-diff').html('<img src="img/pag/ajx.gif" />');
        jQuery('#app-diff').load('index.php', params);
    }
};

Flow.Calendar = {
    /*
     * Initialise Flow.Calendar
     * 
     * @method init
     * @requires {String} aId
     * @requires {String} aLan
     */
    init : function() {
        var that = this;

        var lJId = jQuery('.datepicker');

        var lMonthsButtons = function(aInput, aInst) {
            var lId = aInst.id;

            setTimeout(function() {
                var lButtonPane = lJId.datepicker("widget").find(".ui-datepicker-buttonpane");
                var lButtons = jQuery(
                        '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all m3" type="button">3 Mo.</button>'
                        + '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all m6" type="button">6 Mo.</button>'
                        + '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all m9" type="button">9 Mo.</button>'
                        + '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all m12" type="button">12 Mo.</button>'
                        );

                lButtons.appendTo(lButtonPane);

                // 3 months
                jQuery('.m3').bind('click', function() {
                    that.save(3);
                    lJId.datepicker("option", "numberOfMonths", 3);
                    jQuery('label[for="' + lId + '"] img').trigger("click");
                });

                // 6 months
                jQuery('.m6').bind('click', function() {
                    that.save(6);
                    lJId.datepicker("option", "numberOfMonths", [2, 3]);
                    jQuery('label[for="' + lId + '"] img').trigger("click");
                });

                // 9 months
                jQuery('.m9').bind('click', function() {
                    that.save(9);
                    lJId.datepicker("option", "numberOfMonths", [3, 3]);
                    jQuery('label[for="' + lId + '"] img').trigger("click");
                });

                // 12 months
                jQuery('.m12').bind('click', function() {
                    that.save(12);
                    lJId.datepicker("option", "numberOfMonths", [3, 4]);
                    jQuery('label[for="' + lId + '"] img').trigger("click");
                });
            }, 1);

            var lDatePicker = lJId.datepicker("widget");
            lDatePicker.css('margin-top', -aInput.offsetHeight);
            lDatePicker.css('margin-left', aInput.offsetWidth);

            lDatePicker.draggable();
        };

        var lJIdId = lJId.attr('id');
        var lFormat = jQuery('#' + lJIdId + '_format').val();

        lJId.datepicker({
            showOn : 'button', // can be either >focus<, >button< or >both<
            buttonImage : 'img/ico/16/cal.gif',
            buttonImageOnly : true,
            buttonText : '',
            showButtonPanel : true,
            beforeShow : lMonthsButtons,
            onChangeMonthYear : lMonthsButtons,
            dateFormat : lFormat,
            firstDay : 1,
            showWeek : true
        });

        var lNumberOfMonths = jQuery('#' + lJIdId + '_months').val();

        this.refresh(lNumberOfMonths);
    },
    /*
     * Save number of months to database
     * 
     * @method save
     * @requires {Integer} aNumberOfMonths
     */
    save : function(aNumberOfMonths) {
        var lNumberOfMonths = aNumberOfMonths; // to do (?): allow for 3, 6, 9 and 12 only

        jQuery.ajax({
            type : "POST",
            url : "index.php?act=utl-date.save",
            data : {
                months : lNumberOfMonths
            }
        }).done(function(aData, aTextStatus, aJQXHR) {
        }).fail(function(aData, aTextStatus, aJQXHR) {
        }).always(function(aData, aTextStatus, aJQXHR) {
        });
    },
    /*
     * Load number of months from database
     * 
     * @method load
     * @return {Integer} Returns integer on success
     */
    load : function() {
        var lResult = 3;

        jQuery.ajax({
            type : "POST",
            url : "index.php?act=utl-date.load"
        }).done(function(aData, aTextStatus, aJQXHR) {
            var lData = aData; // to do (?): allow for 3, 6, 9 and 12 only

            lResult = lData;
        }).fail(function(aData, aTextStatus, aJQXHR) {
        }).always(function(aData, aTextStatus, aJQXHR) {
        });

        return lResult;
    },
    refresh : function(aNumberOfMonths) {
        var lNumberOfMonths = aNumberOfMonths;
        lNumberOfMonths = parseInt(lNumberOfMonths);
        var lNumberOfMonthsFormatted;

        var lJId = jQuery('.datepicker');

        switch(lNumberOfMonths) {
            case 3:
                lNumberOfMonthsFormatted = 3;
                break;
            case 6:
                lNumberOfMonthsFormatted = [2, 3];
                break;
            case 9:
                lNumberOfMonthsFormatted = [3, 3];
                break;
            case 12:
                lNumberOfMonthsFormatted = [3, 4];
                break;
            default:
                lNumberOfMonthsFormatted = 3;
        }

        lJId.datepicker("option", "numberOfMonths", lNumberOfMonthsFormatted);
    }
};

Flow.File = function() {
    var gThat = this;

    var gParams = [];
    var gTxt = [];

    this.init = function(aParams) {
        var lAge = aParams['age']; // job, arc
        var lSrc = aParams['src']; // adm, art, com, mis, rep, sec, etc. 
        var lJId = aParams['jid']; // jobid
        var lSub = aParams['sub']; // dalim, doc, dms, pdf, wec, etc.
        var lFil = aParams['fil']; // Filename
        var lDiv = aParams['div']; // division
        var lTd = aParams['td']; // division

        jQuery('#' + lTd + '_txtdiv').click(function(aEvent) {
            var lTxt = aEvent.target.innerText;
            gTxt[lTd] = lTxt;
            gParams = JSON.stringify(aParams);

            jQuery('#' + lTd + '_innerdiv').html(
                    '<table class=\'w100p h100p\'>' +
                    '<tr>' +
                    '<td class=\'w100p h100p p4\'>' +
                    '<textarea class=\'w100p h100p resv\' id=\'' + lTd + '_txt\' >' +
                    lTxt +
                    '</textarea>' +
                    '</td>' +
                    '<td class=\'w16 h100p p4\'>' +
                    '<table>' +
                    '<tr>' +
                    '<td>' +
                    '<a class=\'nav\' id=\'ok\' onclick=\'Flow.File.ok(' + gParams + ');\'>' +
                    '<img src=\'cust/img/ico/16/ok.gif\' alt>' +
                    '</a>' +
                    '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>' +
                    '<a class=\'nav\' id=\'cancel\' onclick=\'Flow.File.cancel(' + gParams + ');\'>' +
                    '<img src=\'cust/img/ico/16/cancel.gif\' alt>' +
                    '</a>' +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</td>' +
                    '</tr>' +
                    '</table>'
                    );
        });
    };

    this.ok = function(aParams) {
        var lSrc = aParams['src']; // adm, art, com, mis, rep, sec, etc. 
        var lJId = aParams['jid']; // jobid
        var lSub = aParams['sub']; // dalim, doc, dms, pdf, wec, etc.
        var lFil = aParams['fil']; // Filename
        var lTd = aParams['td']; // td
        var lParams = JSON.stringify(aParams);

        var lTxt = jQuery('#' + lTd + '_txt').val(); // txt

        jQuery.ajax({
            type : 'post',
            async : false,
            url : 'index.php?act=job-fil.filelinkuploadtxt',
            data : {
                src : lSrc,
                jid : lJId,
                sub : lSub,
                fil : lFil,
                txt : lTxt
            }
        }).done(function() {
            jQuery('#' + lTd + '_innerdiv').html(
                    '<div class=\'w100p h100p p4\' id=\'' + lTd + '_txtdiv\'>' +
                    '<script type="text/javascript">' +
                    'jQuery(function() {' +
                    'Flow.File.init(' + lParams + ');' +
                    '})' +
                    '</script>' +
                    lTxt +
                    '</div>'
                    );
        });
    };

    this.cancel = function(aParams) {
        var lTd = aParams['td']; // td
        var lParams = JSON.stringify(aParams);

        jQuery('#' + lTd + '_innerdiv').html(
                '<div class=\'w100p h100p p4\' id=\'' + lTd + '_txtdiv\'>' +
                '<script type="text/javascript">' +
                'jQuery(function() {' +
                'Flow.File.init(' + lParams + ');' +
                '})' +
                '</script>' +
                gTxt[lTd] +
                '</div>'
                );
    };
};
Flow.File = new Flow.File();

jQuery(function() {
    if(jQuery('.datepicker').length) {
        Flow.Calendar.init();
    }
});

Flow.Std = {
    // **************************************************
    // FPR - Fields preference
    // **************************************************
    fprSel : function(aSrc, aDest) {
        var lSrc = getElem(aSrc);
        var lDst = getElem(aDest);
        for(var i = 0; i < lSrc.options.length; i++) {
            if(lSrc.options[i].selected) {
                var lTmp = new Option(lSrc.options[i].text, lSrc.options[i].value, false, true);
                lDst.options[lDst.length] = lTmp;
            }
        }
        i = 0;
        while(i < lSrc.options.length) {
            if(lSrc.options[i].selected) {
                lSrc.options[i] = null;
            } else {
                i++;
            }
        }
    },
    fprAll : function(aDst) {
        var lDst = getElem(aDst);
        if(!lDst)
            return;
        for(i = 0; i < lDst.options.length; i++) {
            lDst.options[i].selected = true;
        }
    },
    fprUp : function(aId) {
        var lObj = getElem(aId);
        if(lObj.options.length == 0)
            return;
        if(lObj.options[0].selected)
            return;
        for(i = 1; i < lObj.options.length; i++) {
            if(lObj.options[i].selected) {
                var lTxt = lObj.options[i - 1].text;
                var lVal = lObj.options[i - 1].value;
                var lSel = lObj.options[i - 1].selected;
                lObj.options[i - 1].text = lObj.options[i].text;
                lObj.options[i - 1].value = lObj.options[i].value;
                lObj.options[i - 1].selected = true;
                lObj.options[i].text = lTxt;
                lObj.options[i].value = lVal;
                lObj.options[i].selected = lSel;
            }
        }
    },
    fprDn : function(aId) {
        var lObj = getElem(aId);
        if(lObj.options.length == 0)
            return;
        if(lObj.options[lObj.options.length - 1].selected)
            return;
        for(i = lObj.options.length - 1; i >= 0; i--) {
            if(lObj.options[i].selected) {
                var lTxt = lObj.options[i + 1].text;
                var lVal = lObj.options[i + 1].value;
                var lSel = lObj.options[i + 1].selected;

                lObj.options[i + 1].text = lObj.options[i].text;
                lObj.options[i + 1].value = lObj.options[i].value;
                lObj.options[i + 1].selected = true;

                lObj.options[i].text = lTxt;
                lObj.options[i].value = lVal;
                lObj.options[i].selected = lSel;
            }
        }
    },
    // **************************************************
    // CNF (see ajx.lang)
    // **************************************************

    cnfCpy : function(aUrl) {
        var functionName = 'js.cnfCpy';
        aLan = unescape(lan(functionName));
        if(confirm(aLan)) {
            location.href = aUrl;
        }
    },
    cnfDel : function(aUrl) {
        var functionName = 'js.cnfDel';
        aLan = unescape(lan(functionName));
        if (confirm(aLan)) {
            location.href = aUrl;
        }
    },
    cnfDelGeneralFiles : function(aUncodedURL, aCodedURL) {
        aCodedURL = encodeURIComponent(aCodedURL);
        var functionName = 'js.cnfDel';
        aLan = unescape(lan(functionName));
        if (confirm(aLan)) {
            location.href = aUncodedURL + aCodedURL;
        }
    },
    cnfUnassign : function(aUrl) {
        var functionName = 'js.cnfUnassign';
        aLan = unescape(lan(functionName));
        if(confirm(aLan)) {
            location.href = aUrl;
        }
    },
    cnfMAssOut : function(aUrl) {
        var functionName = 'js.cnfMAssOut';
        aLan = unescape(lan(functionName));
        if(confirm(aLan)) {
            location.href = aUrl;
        }
    },
    cnfVAssOut : function(aUrl) {
        var functionName = 'js.cnfVAssOut';
        aLan = unescape(lan(functionName));
        if(confirm(aLan)) {
            location.href = aUrl;
        }
    },
    cnfUnDel : function(aUrl) {
        var functionName = 'js.cnfUnDel';
        aLan = unescape(lan(functionName));
        if(confirm(aLan)) {
            location.href = aUrl;
        }
    },
    // **************************************************
    // JOB
    // **************************************************

    jobDelFile : function(aDiv, aSrc, aJid, aSub, aFile, aLan) {
        var functionName = 'js.jobDelFile';
        aLan = unescape(lan(functionName));

        if(confirm(aLan)) {
            this.ajxImg(aDiv, "Processing...");
            new Ajax.Updater(aDiv, 'index.php', {
                evalScripts : true,
                parameters :
                        {
                            act : 'job-fil.del',
                            div : aDiv,
                            src : aSrc,
                            jid : aJid,
                            sub : aSub,
                            name : aFile
                        }
            });
        }
    },
    jobWecUploadFile : function(aDiv, aSrc, aJid, aSub, aFile, aLan) {
        var functionName = 'js.jobWecUploadFile';
        aLan = unescape(lan(functionName));

        if(confirm(aLan)) {
            this.ajxImg(aDiv, "Processing...");
            new Ajax.Updater(aDiv, 'index.php', {
                evalScripts : true,
                parameters :
                        {
                            act : 'job-fil.wecupl',
                            div : aDiv,
                            src : aSrc,
                            jid : aJid,
                            sub : aSub,
                            name : aFile
                        }
            });
        }
    },
    // **************************************************
    // checkForSomething
    // **************************************************

    checkForPath : function(aValue) {
        var toBeChecked = document.getElementsByName(aValue).item(0).value;

        // entferne alle Leerzeichen am Anfang der Zeichenkette 
        // entferne alle Leerzeichen am Ende der Zeichenkette
        // aendere alle \ in / um
        // aendere alle // in / um
        toBeChecked = toBeChecked.replace(/^\s+/, "").replace(/\s+$/, "").replace(/\\+/g, "/").replace(/\/\/+/g, "/");

        // haege ein / oder ein // am Anfang der Zeichenkette hinzu
        if(toBeChecked.substr(0, 2) != "//" && toBeChecked.length >= 1) {
            toBeChecked = "/" + toBeChecked;

            if(toBeChecked.substr(0, 2) != "//" && toBeChecked.length >= 1) {
                toBeChecked = "/" + toBeChecked;
            }
        }

        // haege ein / am Ende der Zeichenkette hinzu
        if(toBeChecked.substr(toBeChecked.length - 1, 1) != "/" && toBeChecked.length >= 1) {
            toBeChecked = toBeChecked + "/";
        }

        document.getElementsByName(aValue).item(0).value = toBeChecked;
    },
    checkForSubfolder : function(aValue) {
        var toBeChecked = document.getElementsByName(aValue).item(0).value;

        // entferne alle Leerzeichen am Anfang der Zeichenkette 
        // entferne alle Leerzeichen am Ende der Zeichenkette
        // aendere alle \ in / um
        // entferne / am Anfang der Zeichenkette
        // aendere alle // in / um
        toBeChecked = toBeChecked.replace(/^\s+/, "").replace(/\s+$/, "").replace(/\\+/g, "/").replace(/^\/+/, "").replace(/\/\/+/g, "/");

        // haege / am Ende der Zeichenkette hinzu
        if(toBeChecked.substr(toBeChecked.length - 1, 1) != "/" && toBeChecked.length >= 1) {
            toBeChecked = toBeChecked + "/";
        }

        document.getElementsByName(aValue).item(0).value = toBeChecked;
    },
    checkForName : function(aValue) {
        var toBeChecked = document.getElementsByName(aValue).item(0).value;

        // entferne alle Leerzeichen am Anfang der Zeichenkette 
        // entferne alle Leerzeichen am Ende der Zeichenkette
        // aendere alle \ in / um
        // entferne / am Anfang der Zeichenkette
        // entferne / am Ende der Zeichenkette
        // aendere alle // in / um
        toBeChecked = toBeChecked.replace(/^\s+/, "").replace(/\s+$/, "").replace(/\\+/g, "/").replace(/^\/+/, "").replace(/\/+$/, "").replace(/\/\/+/g, "/");

        document.getElementsByName(aValue).item(0).value = toBeChecked;
    },
    checkForNoImg : function(aValue) {
        var toBeChecked = document.getElementsByName(aValue).item(0).value;

        // entferne alle Leerzeichen am Anfang der Zeichenkette 
        // entferne alle Leerzeichen am Ende der Zeichenkette
        // aendere alle \ in / um
        // entferne / am Ende der Zeichenkette
        // aendere alle // in / um
        toBeChecked = toBeChecked.replace(/^\s+/, "").replace(/\s+$/, "").replace(/\\+/g, "/").replace(/\/+$/, "").replace(/\/\/+/g, "/");

        // haege ein / oder ein // am Anfang der Zeichenkette hinzu
        if(toBeChecked.substr(0, 2) != "//" && toBeChecked.length >= 1) {
            toBeChecked = "/" + toBeChecked;
            if(toBeChecked.substr(0, 2) != "//" && toBeChecked.length >= 1) {
                toBeChecked = "/" + toBeChecked;
            }
        }

        document.getElementsByName(aValue).item(0).value = toBeChecked;
    },
    // **************************************************
    // GENERAL
    // **************************************************

    doInit : function() {
        //Ajax Event Handler
        jQuery(document).ajaxSend(function() {
            jQuery('#pag_ajx').attr("src", "img/pag/ajx2.gif");
        });
        jQuery(document).ajaxSuccess(function() {
            jQuery('#pag_ajx').attr("src", "img/d.gif");
            jQuery('#pag_ajx').attr("title", "");
        });
        jQuery(document).ajaxError(function() {
            jQuery('#pag_ajx').attr("src", "img/ico/16/ml-4.gif");
            jQuery('#pag_ajx').attr("title", "Ajax Error");
        });
    },
    //Deprecated
    pageInit : function() {
        Ajax.Responders.register({
            onCreate : function() {
                $('pag_ajx').src = 'img/pag/ajx2.gif';
            },
            onComplete : function() {
                if(0 == Ajax.activeRequestCount)
                    $('pag_ajx').src = 'img/d.gif';
            }
        });
    },
    getElem : function(aId) {
        return (typeof aId == 'string') ? document.getElementById(aId) : aId;
    },
    // **************************************************
    // TOGGLE
    // **************************************************

    tog : function(aId) {
        try {
            var lDiv = this.getElem(aId);
            var lNew = 'none';
            var lOld = lDiv.style.display;
            if(lOld == '')
                lNew = 'block';
            if(lOld == 'none')
                lNew = 'block';
            lDiv.style.display = lNew;
        } catch(e) {
            alert(e);
        }
    },
    togTr : function(aId) {
        if(isIE4up) {
            this.tog(aId);
            return;
        }
        try {
            var lDiv = this.getElem(aId);
            var lNew = 'none';
            var lOld = lDiv.style.display;
            if(lOld == '')
                lNew = 'table-row';
            if(lOld == 'none')
                lNew = 'table-row';
            lDiv.style.display = lNew;
        } catch(e) {
            alert(e);
        }
    },
    togWecSingle : function(aId, aDocId) {
        var lDis = $(aId).getStyle('display');
        if(('' == lDis) || ('none' == lDis)) {
            this.hideAllTr(aId);
            this.togTr(aId);
            lImg = $(aId).down("img");
            if(!lImg.hasClassName("loaded")) {
                lImg.addClassName("loaded");
                lImg.src = 'index.php?act=utl-wec.thumb&doc=' + aDocId;
            }
        } else {
            this.togTr(aId);
        }
    },
    togWec : function(aId, aDocId, aJobId, aVerId) {
        this.togTr(aId);
        lImg = $(aId).down("img");
        if(!lImg.hasClassName("loaded")) {
            lImg.addClassName("loaded");
            lImg.src = 'index.php?act=utl-wec.thumb&doc=' + aDocId + '&jobid=' + aJobId + '&ver=' + aVerId;
        }
    },
    togBits : function(aId, aBit) {
        var lObj = this.getElem(aId);
        var lVal = parseInt(lObj.value);
        lVal = lVal ^ aBit;
        lObj.value = lVal;
    },
    togCheckbox : function(aId, aVal) {
        var lObj = this.getElem(aId);
        var lVal = lObj.value;
        lVal = lVal.split(",");
        var lInArr = jQuery.inArray(aVal, lVal);
        
        if(lInArr > -1) {
        	lVal.splice(lInArr, 1);
        } else {
        	lVal.push(aVal);
        }
        lObj.value = lVal.join(",");
    },
    togByClass : function(aClass) {
        var lElems = jQuery('.' + aClass);
        var lFirst = jQuery(lElems).first();
        if(lFirst.hasClass('dn')) {
            jQuery(lElems).removeClass('dn');
        } else {
            jQuery(lElems).addClass('dn');
        }
    },
    // deprecated?
    togChildTr : function(aPar, aStopClass) {
        var lEl = $(aPar);
        while(lEl = lEl.next('tr')) {
            if(lEl.hasClassName(aStopClass))
                break;
            this.togTr(lEl);
        }
    },
    togCpl : function(aDivId, aImgId, aLnkId, aPersist) {
        try {
            var lDiv = jQuery('#' + aDivId);
            var lImg = jQuery('#' + aImgId);
            var lLnk = jQuery('#' + aLnkId);
            if (!lDiv.is(':visible')) {
                lDiv.show();
                if (arguments.length > 3) {
                    jQuery.post('index.php?act=ajx.prf&key=' + aPersist);
                }
                //lImg.attr('src', Flow.Std.imgFinder('img/ico/16/cpl-collapse.gif', {}, false));
                lImg.attr('class', 'ico-w16 ico-w16-cpl-collapse');
                lImg.attr('title', lan("lib.collapse"));
            } else {
            	lDiv.hide();
                if (arguments.length > 3) {
                    jQuery.post('index.php?act=ajx.prf&val=1&key=' + aPersist);
                }
                //lImg.attr('src', Flow.Std.imgFinder('img/ico/16/cpl-expand.gif', {}, false));
                lImg.attr('class', 'ico-w16 ico-w16-cpl-expand');
                lImg.attr('title', lan("lib.expand"));
            }
            lLnk.blur();
        } catch(e) {
        }
    },
    // deprecated !!!
    togColFrm : function(aVal) {
        if(aVal > 1) {
            $('dco2').show();
        } else {
            $('dco2').hide();
        }
        if(aVal > 2) {
            $('dco3').show();
        } else {
            $('dco3').hide();
        }
    },
    togAllChildTr : function(aPar, aMatchClass, aStopClass) {
        var lEl = $(aPar);
        var lFirst = true;
        while(lEl = lEl.next('tr')) {
            if(lEl.hasClassName(aStopClass))
                break;
            if(lFirst) {
                lFla = this.isVisible(lEl);
                lFirst = false;
            }
            if(lFla) {
                this.hide(lEl);
            } else {
                if(lEl.hasClassName(aMatchClass)) {
                    this.showTr(lEl);
                }
            }
        }
    },
    hideAllTr : function(aPar) {
        $$('tr.togtr').each(function(aTr) {
            Flow.Std.hide(aTr);
        });
    },
    showAllTr : function(aPar) {
        $$('tr.togtr').each(function(aTr) {
            Flow.Std.showTr(aTr);
        });
    },
    isVisible : function(aId) {
        try {
            var lVis = this.getElem(aId).style.display;
            return !((lVis == '') || (lVis == 'none'));
        } catch(e) {
            return false;
        }
    },
    // deprecated
    hide : function(aId) {
        try {
            var lObj = getElem(aId);

            if(isIE8 || isIE7 || isIE6) {
                var lObjF = getElem('myIFrame');
                lObj.style.display = 'none';
                return;
            }

            if(isIE4up) {
                var lObjF = getElem('myIFrame');
                lObjF.style.display = 'none';
                return;
            }

            lObj.style.display = 'none';
        } catch(e) {
        }
    },
    // deprecated
    show : function(aId) {
        try {
            var lObj = getElem(aId);

            if(isIE8 || isIE7 || isIE6) {
                var lObjF = getElem('myIFrame');
                lObj.style.display = 'block';
                return;
            }

            if(isIE4up) {
                var lObjF = getElem('myIFrame');
                lObjF.style.position = 'absolute';
                lObjF.style.display = 'block';
                return;
            }

            lObj.style.display = 'block';
        } catch(e) {
        }
    },
    popMain : function(aId, aLnk, aSub) {
        this.hideMenus();
        gIgn = 1;
        if(document.getElementById("submenu_inner") === null) {
            this.popMen(aId, aLnk);
        } else {
            this.popMenWave8(aId, aLnk, aSub);
        }
    },
    hideMenus : function() {
        if(gIgn) {
            gIgn = 0;
            return;
        }

        var lDiv;
        for(var lIdx in gDiv) {
            lDiv = gDiv[lIdx];
            if($(lDiv)) {
                this.hide(lDiv);
            }
        }

        if($('pag_tip')) {
            Flow.hideTip();
        }
        gDiv = new Array();
    },
    popMen : function(aId, aLnk) {
        this.show(aId);

        if(isIE4up) {
            var offsetTop = document.getElementById(aId).parentElement.offsetTop;
            if(offsetTop == 0)
                var offsetTop = document.getElementById(aId).offsetTop;
        } else {
            var offsetTop = document.getElementById(aId).offsetTop;
        }

        if(isIE4up) {
            var offsetLeft = document.getElementById(aId).parentElement.offsetLeft;
            if(offsetLeft == 0)
                var offsetLeft = document.getElementById(aId).offsetLeft;
        } else {
            var offsetLeft = document.getElementById(aId).offsetLeft;
        }

        var offsetWidth = document.getElementById(aId).offsetWidth;
        var offsetHeight = document.getElementById(aId).offsetHeight;

        if(window.pageXOffset + screen.width - offsetLeft < offsetWidth) {
            document.getElementById(aId).style.left = (window.pageXOffset + screen.width - offsetWidth) + 'px';
            offsetLeft = window.pageXOffset + screen.width - offsetWidth;
        }

        if(window.pageYOffset + screen.height - offsetTop < offsetHeight) {
            document.getElementById(aId).style.top = (window.pageYOffset + screen.height - offsetHeight) + 'px';
            offsetTop = window.pageYOffset + screen.height - offsetHeight;
        }

        if(isIE4up) {
            document.getElementById('myIFrame').style.top = offsetTop;
            document.getElementById('myIFrame').style.left = offsetLeft;
            document.getElementById('myIFrame').style.width = offsetWidth;
            document.getElementById('myIFrame').style.height = offsetHeight;
        }

        gDiv.push(aId);
        if(typeof aLnk != 'undefined') {
            try {
                var lLnk = getElem(aLnk);
                lLnk.blur();
            } catch(e) {
            }
        }
    },
    popMenWave8 : function(aId, aLnk, aSub) {
        var aContent = document.getElementById(aId).innerHTML;
        document.getElementById("submenu_inner").innerHTML = aContent;

        var lAct = jQuery("#page_act").val();
        jQuery(".mmHi").removeClass("mmHi").removeClass(lAct).addClass("mmLo");
        jQuery("#" + aSub).parent().removeClass("mmLo").addClass("mmHi").addClass(lAct);
    },
    togfRdOnly : function(aBtn, aTar, aTarCancel) {
        var idxLck = 'lck[' + aBtn + ']';
        var idxBtn = 'btn[' + aBtn + ']';
        var idxVal = 'val[' + aBtn + ']';
        var idxTar = 'val[' + aTar + ']';
        var idxaTarCancel = 'val[' + aTarCancel + ']';

        var lLck = document.getElementsByName(idxLck);
        var lBtn = document.getElementsByName(idxBtn);
        var lTar = document.getElementsByName(idxTar);
        var lVal = document.getElementsByName(idxVal);
        var lTarCC = document.getElementsByName(idxaTarCancel);

        lTar[0].readOnly = !(lTar[0].readOnly);
        if(lTar[0].readOnly) {
            lTar[0].blur();
            lBtn[0].style.display = '';
            lLck[0].style.display = 'none';
            lVal[0].value = '0';
            lTar[0].value = lTarCC[0].value;
        } else {
            lTar[0].focus();
            lBtn[0].style.display = 'none';
            lLck[0].style.display = '';
            lVal[0].value = '1';
        }

        return true;
    },
    // **************************************************
    // IMG
    // **************************************************

    imgFinder : function(aSrc, aAttr, aHtm) {
        aHtm = (typeof aHtm == 'undefined') ? true : aHtm;
        aAttr = (typeof aAttr == 'undefined') ? {} : aAttr;

        var lData = '';
        jQuery.ajax({
            type : 'post',
            url : 'index.php?act=ajx.getimage',
            async : false,
            data : {
                src : aSrc,
                attr : aAttr,
                htm : aHtm
            },
            success : function(aData) {
                lData = aData;
            }
        });

        return lData;
    },
    // TODO: is '#' really needed?
    ajxImg : function(aDiv) {
        jQuery('#' + aDiv).html('<div style="text-align:center; position:relative; top:50px">' + this.imgFinder("img/pag/ajx.gif", {
            'alt' : 'Loading...',
            'class' : 'p16 ac'
        }) + '<br />Loading...</div>');
    },
    // deprecated
    hiImg : function(aId) {
        try {
            var lImg = this.getElem(aId);
            var lOld = lImg.src;
            lImg.src = lOld.substr(0, lOld.length - 6) + 'hi.gif';
        } catch(e) {
        }

    },
    // deprecated
    loImg : function(aId) {
        try {
            var lImg = this.getElem(aId);
            var lOld = lImg.src;
            lImg.src = lOld.substr(0, lOld.length - 6) + 'lo.gif';
        } catch(e) {
        }
    },

    // **************************************************
    // AJAX
    // **************************************************
    ajxUpd : function(aParams) {
        if (aParams.loading_screen) {
            Flow.Std.ajxImg(aParams.div);
        }

        jQuery.ajax({
            url: 'index.php',
            data: aParams,
            cache: false,
            success: function (lResult, lStatus, lXHR) {
                jQuery('#' + aParams.div).html(lResult);
            }
        });
    },

    // TODO equals ajxUpd!!!
    ajxFil : function(aParams) {
        if (aParams.loading_screen) {
            Flow.Std.ajxImg(aParams.div);
        }

        jQuery.ajax({
            url: 'index.php',
            data: aParams,
            success: function (lResult, lStatus, lXHR) {
                jQuery('#' + aParams.div).html(lResult);
            }
        });
    },
    
    ajxAplPageFil : function(aDiv, aSrc, aJid, aUrl, aFlag, aSub, aFile, aFrmId) {
        var lObj = document.getElementsByName("userfiles");

        if(arguments.length > 7) {
            this.ajxUpd({act: aUrl, div: aDiv, src: aSrc, jid: aJid, typ: aFlag, sub: aSub, fid: aFrmId});

            lObj[0].value = lObj[0].value + "\n" + aFile;
            lObj[0].rows = lObj[0].rows + 1;
            try {
                lEle = document.forms[aFrmId];
                lEle.listuserfiles.value = lObj[0].value;
            } catch(e) {
            }
        } else {
            this.ajxUpd({act: aUrl, div: aDiv, src: aSrc, jid: aJid, typ: aFlag, sub: aSub});
        }
    },

    addBm : function(aDiv, aSrc, aJid, aKey) {
        new Ajax.Updater(aDiv, 'index.php', {
            parameters : {
                act : 'ajx.addbm',
                src : aSrc,
                jid : aJid,
                key : aKey
            }
        });
    },
    removeBm : function(aDiv, aSrc, aJid, aKey) {
        new Ajax.Updater(aDiv, 'index.php', {
            parameters : {
                act : 'ajx.removebm',
                src : aSrc,
                jid : aJid,
                key : aKey
            }
        });
    },
    // **************************************************
    // REPORT
    // **************************************************

    saveReport : function(aSrc, aKey, aLan) {
        var functionName = 'js.saveReport';
        aLan = unescape(lan(functionName));
        var lRes = prompt(aLan, 'Report');
        if(lRes) {
            Flow.Std.ajxUpd('rep_tree', {
                act : 'rep.save',
                src : aSrc,
                key : aKey,
                name : lRes
            },
            true);
        }
    },
    // **************************************************
    // VALIDATORS
    // **************************************************

    // deprecated?
    is_int : function(input) {
        return !isNaN(input) && parseInt(input) == input;
    },
    chkInt : function(aEl, aImg) {
        var lVal = aEl.value;

        var intRegex = /^-?\d*\.?\d*$/;
        if(intRegex.test(lVal)) {
            $(aImg).src = 'img/ico/16/ok.gif';
        } else {
            $(aImg).src = 'img/ico/16/ml-4.gif';
        }
    },
    chkEan : function(aEl, aImg) {
        var lVal = aEl.value.replace(/ /g, '');

        if(this.checkModulo(lVal, 8) || this.checkModulo(lVal, 13)) {
            $(aImg).src = 'img/ico/16/ok.gif';
        } else {
            $(aImg).src = 'img/ico/16/ml-4.gif';
        }
    },
    checkModulo : function(aText, aDigits) {
        if(aText.search(/[^0-9]/) != -1)
            return false;
        if(aText.length != aDigits)
            return false;

        var sum = 0;
        var fak = ((aDigits % 2) == 0) ? 3 : 1;
        var num;
        for(var i = 0; i < aDigits - 1; i++) {
            num = parseInt(aText.charAt(i));
            sum = sum + num * fak;
            fak = (fak == 1) ? 3 : 1;
        }
        sum = 10 - (sum % 10);
        if(sum == 10)
            sum = 0;
        if(parseInt(aText.charAt(aDigits - 1)) != sum) {
            return false;
        } else {
            return true;
        }
    },
    // deprecated
    integertest : function(aAllintegers, aFieldvalue, aFieldid) {
        var lCorrection = false;
        var lx = "";
        var lComma = false;
        var lAllintegers = "," + aAllintegers + ",";
        if(lAllintegers.indexOf(',' + aFieldvalue + ',') == -1) {
            lCorrection = true;
        }
        if(lCorrection) {
            document.getElementById(aFieldid).value = '';
        }
        return !lCorrection;
    },
    currencytest : function(aFieldvalue, aFieldid) {
        var lAllchars = "0123456789.,";
        var lCorrection = false;
        var lx = "";
        var lComma = false;
        for(var i = 0; i < aFieldvalue.length; i++) {
            var lchar = aFieldvalue.charAt(i);
            if(lAllchars.indexOf(lchar) == -1) {
                lCorrection = true;
            } else {
                if(lchar == "." || lchar == ",") {
                    if(lComma) {
                        lCorrection = true;
                    } else {
                        lComma = true;
                        if(lchar == ".") {
                            lCorrection = true;
                        }
                        lx = lx.concat(",");
                    }
                } else {
                    lx = lx.concat(lchar);
                }
            }
        }
        if(lCorrection) {
            document.getElementById(aFieldid).value = lx;
        }
        return !lCorrection;
    },
    // **************************************************
    // OTHERS
    // **************************************************

    tMce : function(aEl) {
        var lHeight = jQuery("#" + aEl).attr("data-height");
        var lHeight = (typeof lHeight == 'undefined') ? 1000 : lHeight;

        var lBtns = jQuery("#" + aEl).attr("data-btns");
        var lBtns = (typeof lBtns == 'undefined') ? "print,|,cut,copy,paste,|,undo,redo,|,fontsizeselect,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent" : lBtns;

        tinyMCE.init({
            mode : "exact",
            elements : aEl,
            theme : "advanced",
            width : "100%",
            height : lHeight,
            plugins : "safari,print,pagebreak,style,layer,save,preview,searchreplace,print,paste,directionality,noneditable,visualchars,nonbreaking,inlinepopups",
            theme_advanced_buttons1 : lBtns,
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_buttons4 : "",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_path : false,
            theme_advanced_resize_horizontal : false,
            theme_advanced_resizing : true,
            setup : function(ed) {
                // Register example button
            }
        });
    },
    pagSel : function(aKey) {
        this.hide('pag' + actTab);
        tabSel(aKey);
        try {
            document.forms.jobFrm.page.value = aKey;
        } catch(e) {
        }
        this.show('pag' + aKey);
    },
    pageY : function(elem) {
        return elem.offsetParent ? (elem.offsetTop + this.pageY(elem.offsetParent)) : elem.offsetTop;
    },
    doResizeIframe : function(elem) {
        var height = (typeof window.innerHeight == 'undefined') ? document.documentElement.clientHeight : window.innerHeight;
        height -= this.pageY(document.getElementById(elem)) + heightBuffer;
        height = (height < 0) ? 0 : height;
        if(height < 600)
            height = 600;
        document.getElementById(elem).style.height = height + 'px';
    },
    // **************************************************
    // LISTS
    // **************************************************

    // DEPRECATED
    pickOld : function(aId, aDom, aLis, aTbl) {
        var lObj = getElem(aId);
        var lUrl;
        var lWin;
        if('htb' == aTbl) {
            lUrl = 'index.php?act=utl-htb&dom=' + aDom + '&lis=' + aLis + '&sel=' + lObj.value;
        } else {
            lUrl = 'index.php?act=utl-pck&dom=' + aDom + '&lis=' + aLis + '&sel=' + lObj.value;
        }
        lWin = window.open(lUrl, '_blank', 'left=100,top=100,width=510,height=500,scrollbars=yes');
        lWin.obj = lObj;
        window.obj = lObj;
    },
    pick : function(aId, aDom, aLis, aParent) {
        var lObj = getElem(aId);
        var lUrl = 'index.php?act=utl-ctr&dom=' + aDom + '&lis=' + aLis + '&sel=' + lObj.value;
        if(aParent) {
            lUrl += '&parent=' + escape(aParent);
        }
        var lWin = window.open(lUrl, '_blank', 'left=100,top=100,width=510,height=500,scrollbars=yes');
        lWin.obj = lObj;
        window.obj = lObj;
    },
    // DEPRECATED
    gPick : function(aUrl, aForm, aElement, aWidth, aHeight, aAppend) {
        window.obj = window.document.forms[aForm].elements[aElement];
        window.doAppend = (aAppend == 'Y');
        win = window.open(aUrl, '_blank', 'left=100,top=100,width=' + aWidth + ',height=' + aHeight + ',scrollbars=yes');
        win.opener = self;
    },
    newPick : function(aUrl, aForm, aWidth, aHeight) {
        window.frm = window.document.forms[aForm];
        win = window.open(aUrl, '_blank', 'left=100,top=100,width=' + aWidth + ',height=' + aHeight + ',scrollbars=yes');
        win.opener = self;
    },
    paramPick : function(aId, aDom) {
        var lObj = getElem(aId);
        var lUrl = 'index.php?act=utl-par&sel=' + lObj.value;
        var lWin = window.open(lUrl, '_blank', 'width=420,height=450,scrollbars=yes');
        lWin.obj = lObj;
        window.obj = lObj;
    },
    jobfieldparamPick : function(aId, aDom) {
        var lObj = getElem(aId);
        var lUrl = 'index.php?act=utl-jfp&sel=' + lObj.value;
        var lWin = window.open(lUrl, '_blank', 'width=420,height=450,scrollbars=yes');
        lWin.obj = lObj;
        window.obj = lObj;
    },
    pckCtr : function(aId, aCtr, aSort) {
        aSort = (typeof aSort == 'undefined') ? true : aSort;

        var lObj = $(aId);
        if(lObj.hasClassName('pckNrm')) {
            $$('td[data-val="' + aCtr + '"]').each(function(aEl) {
                aEl.removeClassName('pckNrm');
                aEl.addClassName('pckAct');
            });
            gCtr[gCtr.length] = aCtr;
            if(aSort == true)
                gCtr.sort();
            var lRes = gCtr.join(',');
        } else {
            $$('td[data-val="' + aCtr + '"]').each(function(aEl) {
                aEl.removeClassName('pckAct');
                aEl.addClassName('pckNrm');
            });
            var lArr = new Array();
            for(var i = 0; i < gCtr.length; i++) {
                if(gCtr[i] != aCtr) {
                    lArr.push(gCtr[i]);
                }
            }
            if(aSort == true)
                lArr.sort();
            gCtr = lArr;
            var lRes = gCtr.join(',');
        }
        var lInp = getElem('lRes');
        lInp.value = lRes;
    },
    pckOkay : function() {
        try {
            var lRes = getElem('lRes');
            window.opener.obj.value = lRes.value;
            if(window.opener.obj) {
                $(window.opener.obj).fire('flow:countrychanged');
                window.opener.obj.onchange();
                window.opener.obj.blur();
            }
        } catch(e) {
        }
        window.close();
    },
    /** Liste checken: ist eine option gewaehlt?  */
    ListCheck : function(aLan) {
        var functionName = 'js.ListCheck';
        aLan = unescape(lan(functionName));

        var elements;

        if(document.all)
            elements = document.all;
        else if(document.getElementsByTagName && !document.all)
            elements = document.getElementsByTagName("*");

        for(i = 0; i < elements.length; i++) {
            if(elements[i].checked) {
                if(isset(document.getElementById('sel' + elements[i].id))) {
                    if(document.getElementById('sel' + elements[i].id).selectedIndex == 0) {
                        alert(aLan + document.getElementById(elements[i].id).value);
                        return false;
                    }
                }
            }
        }

        return true;
    },
    delChoice : function(aId) {
        new Ajax.Request('index.php?act=ajx.delchoice&id=' + aId);
    },
    // **************************************************
    // CALENDER
    // **************************************************

    // DEPRECATED
    cal : function(aId) {
        var lObj = getElem(aId);
        var lUrl = 'index.php?act=utl-date';
        var lWin = window.open(lUrl, '_blank', 'left=100,top=100,width=880,height=400,scrollbars=no');
        lWin.obj = lObj;
        window.obj = lObj;
    },
    selCal : function(aVal) {
        try {
            window.opener.obj.value = aVal;
        } catch(e) {
        }
        window.close();
    },
    // **************************************************
    // CLASSES
    // **************************************************

    showByClass : function(aClass) {
        var lElems = jQuery('.' + aClass);
        jQuery('.' + aClass).removeClass('dn');
    },
    hideByClass : function(aClass) {
        var lElems = jQuery('.' + aClass);
        jQuery('.' + aClass).addClass('dn');
    },
    // deprecated
    getCls : function(aNode) {
        var lNode = this.getElem(aNode);
        if(lNode.className) {
            return lNode.className;
        } else {
            return lNode.getAttribute('class');
        }
    },
    setCls : function(aNode, aClass) {
        var lNode = this.getElem(aNode);
        lNode.setAttribute('class', aClass);
        lNode.className = aClass;
    },
    tabHi : function(aId, aKey) {
        try {
            var lObj = this.getElem(aId);
            this.setCls(lObj, 'tabOver');
        } catch(e) {
        }
    },
    tabLo : function(aId, aKey) {
        try {
            var lObj = this.getElem(aId);
            var lCls = (aKey == actTab) ? 'tabAct' : 'tabNorm';
            this.setCls(lObj, lCls);
        } catch(e) {
        }
    },
    // **************************************************
    // 3rd PARTY TOOLS
    // **************************************************

    get_html_translation_table : function(table, quote_style) {
        //  discuss at: http://phpjs.org/functions/get_html_translation_table/
        // original by: Philip Peterson
        //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // bugfixed by: noname
        // bugfixed by: Alex
        // bugfixed by: Marco
        // bugfixed by: madipta
        // bugfixed by: Brett Zamir (http://brett-zamir.me)
        // bugfixed by: T.Wild
        // improved by: KELAN
        // improved by: Brett Zamir (http://brett-zamir.me)
        //    input by: Frank Forte
        //    input by: Ratheous
        //        note: It has been decided that we're not going to add global
        //        note: dependencies to php.js, meaning the constants are not
        //        note: real constants, but strings instead. Integers are also supported if someone
        //        note: chooses to create the constants themselves.
        //   example 1: get_html_translation_table('HTML_SPECIALCHARS');
        //   returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

        var entities = {},
                hash_map = {},
                decimal;
        var constMappingTable = {},
                constMappingQuoteStyle = {};
        var useTable = {},
                useQuoteStyle = {};

        // Translate arguments
        constMappingTable[0] = 'HTML_SPECIALCHARS';
        constMappingTable[1] = 'HTML_ENTITIES';
        constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
        constMappingQuoteStyle[2] = 'ENT_COMPAT';
        constMappingQuoteStyle[3] = 'ENT_QUOTES';

        useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
        useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() :
                'ENT_COMPAT';

        if(useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
            throw new Error('Table: ' + useTable + ' not supported');
            // return false;
        }

        entities['38'] = '&amp;';
        if(useTable === 'HTML_ENTITIES') {
            entities['160'] = '&nbsp;';
            entities['161'] = '&iexcl;';
            entities['162'] = '&cent;';
            entities['163'] = '&pound;';
            entities['164'] = '&curren;';
            entities['165'] = '&yen;';
            entities['166'] = '&brvbar;';
            entities['167'] = '&sect;';
            entities['168'] = '&uml;';
            entities['169'] = '&copy;';
            entities['170'] = '&ordf;';
            entities['171'] = '&laquo;';
            entities['172'] = '&not;';
            entities['173'] = '&shy;';
            entities['174'] = '&reg;';
            entities['175'] = '&macr;';
            entities['176'] = '&deg;';
            entities['177'] = '&plusmn;';
            entities['178'] = '&sup2;';
            entities['179'] = '&sup3;';
            entities['180'] = '&acute;';
            entities['181'] = '&micro;';
            entities['182'] = '&para;';
            entities['183'] = '&middot;';
            entities['184'] = '&cedil;';
            entities['185'] = '&sup1;';
            entities['186'] = '&ordm;';
            entities['187'] = '&raquo;';
            entities['188'] = '&frac14;';
            entities['189'] = '&frac12;';
            entities['190'] = '&frac34;';
            entities['191'] = '&iquest;';
            entities['192'] = '&Agrave;';
            entities['193'] = '&Aacute;';
            entities['194'] = '&Acirc;';
            entities['195'] = '&Atilde;';
            entities['196'] = '&Auml;';
            entities['197'] = '&Aring;';
            entities['198'] = '&AElig;';
            entities['199'] = '&Ccedil;';
            entities['200'] = '&Egrave;';
            entities['201'] = '&Eacute;';
            entities['202'] = '&Ecirc;';
            entities['203'] = '&Euml;';
            entities['204'] = '&Igrave;';
            entities['205'] = '&Iacute;';
            entities['206'] = '&Icirc;';
            entities['207'] = '&Iuml;';
            entities['208'] = '&ETH;';
            entities['209'] = '&Ntilde;';
            entities['210'] = '&Ograve;';
            entities['211'] = '&Oacute;';
            entities['212'] = '&Ocirc;';
            entities['213'] = '&Otilde;';
            entities['214'] = '&Ouml;';
            entities['215'] = '&times;';
            entities['216'] = '&Oslash;';
            entities['217'] = '&Ugrave;';
            entities['218'] = '&Uacute;';
            entities['219'] = '&Ucirc;';
            entities['220'] = '&Uuml;';
            entities['221'] = '&Yacute;';
            entities['222'] = '&THORN;';
            entities['223'] = '&szlig;';
            entities['224'] = '&agrave;';
            entities['225'] = '&aacute;';
            entities['226'] = '&acirc;';
            entities['227'] = '&atilde;';
            entities['228'] = '&auml;';
            entities['229'] = '&aring;';
            entities['230'] = '&aelig;';
            entities['231'] = '&ccedil;';
            entities['232'] = '&egrave;';
            entities['233'] = '&eacute;';
            entities['234'] = '&ecirc;';
            entities['235'] = '&euml;';
            entities['236'] = '&igrave;';
            entities['237'] = '&iacute;';
            entities['238'] = '&icirc;';
            entities['239'] = '&iuml;';
            entities['240'] = '&eth;';
            entities['241'] = '&ntilde;';
            entities['242'] = '&ograve;';
            entities['243'] = '&oacute;';
            entities['244'] = '&ocirc;';
            entities['245'] = '&otilde;';
            entities['246'] = '&ouml;';
            entities['247'] = '&divide;';
            entities['248'] = '&oslash;';
            entities['249'] = '&ugrave;';
            entities['250'] = '&uacute;';
            entities['251'] = '&ucirc;';
            entities['252'] = '&uuml;';
            entities['253'] = '&yacute;';
            entities['254'] = '&thorn;';
            entities['255'] = '&yuml;';
        }

        if(useQuoteStyle !== 'ENT_NOQUOTES') {
            entities['34'] = '&quot;';
        }
        if(useQuoteStyle === 'ENT_QUOTES') {
            entities['39'] = '&#39;';
        }
        entities['60'] = '&lt;';
        entities['62'] = '&gt;';

        // ascii decimals to real symbols
        for(decimal in entities) {
            if(entities.hasOwnProperty(decimal)) {
                hash_map[String.fromCharCode(decimal)] = entities[decimal];
            }
        }

        return hash_map;
    },
    html_entity_decode : function(string, quote_style) {
        //  discuss at: http://phpjs.org/functions/html_entity_decode/
        // original by: john (http://www.jd-tech.net)
        //    input by: ger
        //    input by: Ratheous
        //    input by: Nick Kolosov (http://sammy.ru)
        // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // improved by: marc andreu
        //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        //  revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // bugfixed by: Onno Marsman
        // bugfixed by: Brett Zamir (http://brett-zamir.me)
        // bugfixed by: Fox
        //  depends on: get_html_translation_table
        //   example 1: html_entity_decode('Kevin &amp; van Zonneveld');
        //   returns 1: 'Kevin & van Zonneveld'
        //   example 2: html_entity_decode('&amp;lt;');
        //   returns 2: '&lt;'

        var hash_map = {},
                symbol = '',
                tmp_str = '',
                entity = '';
        tmp_str = string.toString();

        if(false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
            return false;
        }

        // fix &amp; problem
        // http://phpjs.org/functions/get_html_translation_table:416#comment_97660
        delete(hash_map['&']);
        hash_map['&'] = '&amp;';

        for(symbol in hash_map) {
            entity = hash_map[symbol];
            tmp_str = tmp_str.split(entity)
                    .join(symbol);
        }
        tmp_str = tmp_str.split('&#039;')
                .join("'");

        return tmp_str;
    },
    toggleSelection : function() {
        jQuery('input[name="job"]').trigger('click');
    },
    xg : function(aId, aSrc) {
        lNode = this.getElem(aId);
        lNode.src = aSrc;
    },
    // deprecated
    hiTd : function(aId) {
        try {
            var lObj = this.getElem(aId);
            lObj.style.backgroundColor = '#dddddd';
        } catch(e) {
        }
    },
    // deprecated
    loTd : function(aId) {
        try {
            var lObj = this.getElem(aId);
            lObj.style.backgroundColor = '';
        } catch(e) {
        }
    },
    calc : function(aEl, aArtNr, aPpu) {
        try {
            var lVal = aEl.value;
            lVal = parseFloat(lVal.replace(/,/, '.'));
            if(isNaN(lVal)) {
                // alert?
                lVal = 0;
            }
            aEl.value = lVal.toFixed(2);
            var lRet = lVal * aPpu;

            $('p' + aArtNr).update(lRet.toFixed(2));

            var lSum = 0;
            $$('.prc').each(function(aInp) {
                lVar = parseFloat(aInp.innerHTML);
                lSum += lVar;
            });
            $('sum_prc').update(lSum.toFixed(2));
        } catch(e) {
            alert(e);
        }
    },
    showTr : function(aTr) {
        var isIE4up = (navigator.appVersion.substring(0, 1) == "4") ? 1 : 0;

        var lTr = $(aTr);
        if(isIE4up) {
            lTr.style.display = 'block';
        } else {
            lTr.style.display = 'table-row';
        }
    },
    modalClose : function(aVal) {
        try {
            window.opener.obj.value = aVal;
            window.opener.obj.blur();
        } catch(e) {
        }
        window.close();
    }
};

Flow.editmultiplejobs = function() {
    this.init = function() {
      jQuery('button[onclick="Flow.multiplejobs.run();"]').prop('disabled', true);
    };
    this.run = function() {
        var getFields = JSON.parse(jQuery('input[name="fields"]').val());
        var getJobIds = {};
        jQuery('input[name="job"]:checkbox:checked').map(function() {
            lSrc = jQuery(this).data('src');
            lJobID = jQuery(this).data('jobid');
            getJobIds[lJobID] = lSrc;
        }).get();
        var getValues = {};

        var lMultipleEdit = jQuery('#multipleedit');
        var lMinHeight = 102;
        var lMinWidth = 640;
        var lMaxHeight = jQuery(window).height() - 32;
        var lMaxWidth = jQuery(window).width() - 32;
        var lHeight = 'auto';
        var lWidth = 'auto';

        lMultipleEdit.dialog({
            minHeight : lMinHeight,
            minWidth : lMinWidth,
            maxHeight : lMaxHeight,
            maxWidth : lMaxWidth,
            height : lHeight,
            width : lWidth,
            draggable : true,
            buttons : [
                {
                    text : 'Ok',
                    id : 'dialog_ok',
                    click : function(aEvent) {
                        jQuery('div[id="multipleedit"] input[name="jobids"]').val(JSON.stringify(getJobIds));

                        jQuery.each(getFields, function(lKey, lValue) {
                            var getValue = jQuery('div[id="multipleedit"]').find('[name="val[' + lValue + ']"]').val();
                            if (getValue) {
                                getValues[lValue] = getValue;
                            }
                        });

                        jQuery('div[id="multipleedit"] input[name="values"]').val(JSON.stringify(getValues));

                        var lAct = jQuery('div[id="multipleedit"] input[name="act"]').val();
                        var lMID = jQuery('div[id="multipleedit"] input[name="mid"]').val();
                        var lSrc = jQuery('div[id="multipleedit"] input[name="src"]').val();
                        var lFields = jQuery('div[id="multipleedit"] input[name="fields"]').val();
                        var lJobIDs = jQuery('div[id="multipleedit"] input[name="jobids"]').val();
                        var lValues = jQuery('div[id="multipleedit"] input[name="values"]').val();

                        var lJobIds = getJobIds;
                        var lJobIdsLength = Object.keys(lJobIds).length;
                        var lJobIdCurrent = 0;

                        var lPercentage = 0;

                        jQuery('div[id="multipleedit"]').empty();
                        var lProgress = jQuery('div[id="multipleedit_progress"]').html();
                        jQuery('div[id="multipleedit"]').html(lProgress);

                        var lProgressValue = jQuery('div[id="multipleedit_progress_value"]');
                        var lProgressText = jQuery('div[id="multipleedit_progress_text"]');

                        var lMessagesValue = jQuery('div[id="multipleedit_messages_value"]');
                        var lMessagesText = jQuery('div[id="multipleedit_messages_text"]');

                        if (lProgressValue.progressbar()) {
                            lProgressValue.progressbar('destroy');
                        }

                        lProgressValue.progressbar({
                            create: function() {
                                var lJob = (lJobIdsLength == 1) ? lan('lib.job') : lan('lib.jobs');
                                lProgressText.html('<p>0 ' + lan('lib.of') + ' ' + lJobIdsLength + ' ' + lJob + '</p>');
                            }
                        });

                        lProgressValue.addClass('progress_value');
                        lProgressText.addClass('progress_text');

                        lMessagesValue.addClass('progress_value');
//                        lMessagesText.addClass('progress_text');

                        var lLoop = function(lJobIdCurrent) {
                            lPercentage = parseInt(((lJobIdCurrent + 1) * 100) / lJobIdsLength, 10);

                            if (lJobIdCurrent < lJobIdsLength) {
                                setTimeout(function () {
                                    var lJobIdsSlice = Flow.sliceObj(lJobIds, lJobIdCurrent, lJobIdCurrent + 1);
                                    var lKey = Object.keys(lJobIdsSlice)[0];
                                    var lValue = lJobIdsSlice[lKey];

                                    var lParams = {
                                        act: lAct,
                                        mid: lMID,
                                        src: lSrc,
                                        fields: lFields,
                                        jobids: JSON.stringify(lJobIdsSlice),
                                        values: lValues
                                    };

                                    jQuery.ajax({
                                        url: 'index.php',
                                        data: lParams,
                                        async: false,
                                        success: function(data, textStatus, jqXHR) {
                                            var lMsgTxtHTML = lMessagesText.html();
                                            lMessagesText.html("Job <b>" + lKey + "</b> of type <b>" + lan('job-' + lValue + '.menu') + "</b> <b><span style=\"color: green;\">successfully updated</span></b>!<br>" + lMsgTxtHTML);
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            var lMsgTxtHTML = lMessagesText.html();
                                            lMessagesText.html("Job <b>" + lKey + "</b> of type <b>" + lan('job-' + lValue + '.menu') + "</b> <b><span style=\"color: red;\">not updated</span></b>!<br>" + lMsgTxtHTML);
                                        }
                                    });

                                    lProgressValue.progressbar({
                                        change: function(aEvent, aUI) {
                                            var lJob = (lJobIdsLength == 1) ? lan('lib.job') : lan('lib.jobs');
                                            lProgressText.html('<p>' + (lJobIdCurrent + 1) + ' ' + lan('lib.of') + ' ' + lJobIdsLength + ' ' + lJob + '</p>');
                                        }
                                    });

                                    lProgressValue.progressbar("option", "value", lPercentage);

                                    lLoop(lJobIdCurrent + 1);
                                }, 20);
                            } else {
                                location.href = "index.php?act=job-" + lSrc;

                                jQuery(this).dialog("close");
                            }
                        }

                        lLoop(0);
                    }
                }, {
                    text : 'Cancel',
                    id : 'dialog_cancel',
                    click : function(aEvent) {
                        aEvent.preventDefault();

                        location.href = "index.php?act=job-" + lSrc;

                        jQuery(this).dialog("close");
                    }
                }],
            modal : true,
            open : function(aEvent) {
                jQuery(this).dialog("option", "width", 640);
                jQuery('div[id="multipleedit"]').find('*[class*="field_"]').css('width', '100%');
            },
            close : function(aEvent) {
                aEvent.preventDefault();

                location.href = "index.php?act=job-" + lSrc;

                jQuery(this).dialog("close");
            }
        });
    };

    this.initJobFields = function() {
        jQuery.ajax({
            url: 'index.php?act=job-multi-ord.getjobfieldsorderbysystem',
            data: {},
            success: function(data, textStatus, jqXHR) {
                if (typeof(data) !== "undefined" && data !== null && data !== '') {
                    jQuery('#job-multi-ord').html(data);
                    jQuery("#job-multi-ord > ul").sortable();
                    jQuery("#job-multi-ord > ul").disableSelection();
                } else {
                    Flow.multiplejobs.loadJobFields();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                jQuery('#job-multi-ord').text(lan("job.multiple-edit.ord.err"));
            }
        });
    };

    this.loadJobFields = function() {
        jQuery.ajax({
            url: 'index.php?act=job-multi-ord.getjobfieldsorderbyalphabet',
            data: {},
            success: function(data, textStatus, jqXHR) {
                jQuery('#job-multi-ord').html(data);
                jQuery("#job-multi-ord > ul").sortable();
                jQuery("#job-multi-ord > ul").disableSelection();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                jQuery('#job-multi-ord').text(lan("job.multiple-edit.ord.err"));
            }
        });
    };

    this.saveJobFields = function() {
        var listCSV = "";
        jQuery("#job-multi-ord li span").each(function() {
            if (listCSV === "") {
                listCSV = jQuery(this).attr("data-value");
            } else {
                listCSV += "," + jQuery(this).attr("data-value");
            }
        });
        jQuery("#job-multi-ord-val").val(listCSV);
    };
};
Flow.multiplejobs = new Flow.editmultiplejobs();

jQuery(document).tooltip({
    items: '[data-toggle="tooltip"]',
    content: function () {
        var lHead = jQuery(this).attr("data-tooltip-head");
        var lBody = jQuery(this).attr("data-tooltip-body");
        
        var lRet = (lHead == "" || lHead == undefined) ? "" : "<div class='cap b'>"+lHead+"</div>";
        lRet+= "<p>"+lBody+"</p>";
        
        return lRet;
    }
});

Flow.autocomplete = {
    init:  function() {
            jQuery("[data-change=autocomplete]").each(function() {
            var lBody = jQuery(this).attr("data-autocomplete-body");
            var lAppStr = "";
            if(lBody !== undefined && lBody.length > 0) {
                lAppStr = lBody;
            }
            else {
                lAppStr = "item.label";
            }
            
            jQuery(this).autocomplete({
            source: "index.php?act="+jQuery(this).attr("data-source"),
            minLength: 2

        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            
            return jQuery("<li></li>")
            .data("item.autocomplete", item)
            .append(eval(lAppStr))
            .appendTo(ul);
        }
    })
    }
};

jQuery(function(){
    Flow.autocomplete.init();
    Flow.multiplejobs.init();

    jQuery('input[name="job"]').on('click', function() {
        var lCheckedCheckboxes = jQuery('input[name="job"]:checked').length;
        if (lCheckedCheckboxes > 0) {
            jQuery('button[onclick="Flow.multiplejobs.run();"]').prop('disabled', false);
        } else {
            jQuery('button[onclick="Flow.multiplejobs.run();"]').prop('disabled', true);
        }
  });
});


Flow.pixelboxx = {
        
    showDetails : function (aDoi, aDiv, aSrc, aJid, aSub, aAge) {
        var lPar = {act: 'job-fil.pboxdetails', doi: aDoi, div: aDiv, src: aSrc, jid: aJid, sub: aSub, age: aAge};
        jQuery('#'+aDiv).load('index.php', lPar);
    },
    
    deleteFile : function (aDoi, aDiv, aSrc, aJid, aSub, aAge) {
        if (!confirm('Are you sure?')) return;
        var lPar = {act: 'job-fil.pboxdel', doi: aDoi, div: aDiv, src: aSrc, jid: aJid, sub: aSub, age: aAge};
        jQuery('#'+aDiv).load('index.php', lPar);
    },
    
    addToCart : function (aEnc, aDoi, aDiv, aSrc, aJid, aSub, aAge) {
        var lPar = {act: 'job-fil.pboxaddcart', enc: aEnc, doi: aDoi, div: aDiv, src: aSrc, jid: aJid, sub: aSub, age: aAge};
        jQuery('#'+aDiv).load('index.php', lPar);
    },
    
    removeFromCart : function (aDoi, aDiv, aSrc, aJid, aSub, aAge) {
        var lPar = {act: 'job-fil.pboxremovecart', doi: aDoi, div: aDiv, src: aSrc, jid: aJid, sub: aSub, age: aAge};
        jQuery('#'+aDiv).load('index.php', lPar);
    },    
    
    toggleDetails : function(aDiv) {
        jQuery('.pbox-hide-details').toggle();
    }
};



Flow.FieldMap = 
{
    showSub : function (aId) {
        var row = jQuery('#m' + aId);
        if (row.is(':visible')) {
            row.hide();
            return;
        }
        jQuery('.fiemap-tr').hide();
        row.show();
        
        if (!row.hasClass('loaded')) {
            Flow.FieldMap.loadSub(aId);
            row.addClass('loaded');
        }
    },
    
    loadSub : function (aId) {
        jQuery('#m' + aId + 'r').load('index.php?act=fie-map.sub&id=' +aId);
    },
    
    deleteItem : function (aMapId, aId) {
        jQuery('#m' + aMapId + 'r').load('index.php?act=fie-map.delitem&fid=' +aId +'&mid='+aMapId);
    },
    
    newItem : function(aMapId) {
        var div = jQuery('<div />');
        jQuery(div).load('index.php?act=fie-map.newitem', function() {
            jQuery(div).dialog({
                title : 'Add New Field',
                width : '450px',
                resizable: false,
                buttons: {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var lVal = {};
                        lVal['alias'] = jQuery('.field_alias', div).val();
                        lVal['native'] = jQuery('.field_native', div).val();
                        lVal['default_value'] = jQuery('.field_default_value', div).val();
                        var lParams = {mid : aMapId, val : lVal}; 
                        jQuery('#m' + aMapId + 'r').load('index.php?act=fie-map.snewitem', lParams);
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    },
    
    editItem : function(aMapId, aId) {
        var div = jQuery('<div />');
        jQuery(div).load('index.php?act=fie-map.edititem&fid='+aId, function() {
            jQuery(div).dialog({
                title : 'Edit Field',
                width : '450px',
                resizable: false,
                buttons: {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var lVal = {};
                        lVal['alias'] = jQuery('.field_alias', div).val();
                        lVal['native'] = jQuery('.field_native', div).val();
                        lVal['default_value'] = jQuery('.field_default_value', div).val();
                        var lParams = {mid : aMapId, fid: aId, val : lVal}; 
                        jQuery('#m' + aMapId + 'r').load('index.php?act=fie-map.sedititem', lParams);
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    },
    
    
    addJobFields : function(aMapId) {
        var div = jQuery('<div />');
        jQuery(div).load('index.php?act=fie-map.addjobfields&mid='+aMapId, function() {
            jQuery(div).dialog({
                title : 'Add Job Fields',
                width : '450px',
                resizable: false,
                buttons: {
                    Okay : function() {
                        jQuery(this).dialog('close');
                        var lVal = jQuery('select', div).val();
                        var lParams = {mid : aMapId, val : lVal}; 
                        jQuery('#m' + aMapId + 'r').load('index.php?act=fie-map.saddjobfields', lParams);
                    },
                    Cancel : function() {
                        jQuery(this).dialog('close');
                    }
                }
            });
        });
    }
};

Flow.JobForm = {
	setActionStep : function (aElement, aSrc, aStepId) {
		var lForm = aElement.form; 
		jQuery('input[name="step"]', lForm).val(aStepId);
		jQuery('input[name="act"]', lForm).val('job-'+aSrc+'.stepindependent');
		lForm.submit();
	}
	
		
};