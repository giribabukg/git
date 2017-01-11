//document.domain = "matthewsbrandsolutions.co.uk";
var frameWindow, editor;

(function($){
    $.fn.modalSteps = function(options){
        var $modal = this;

        var settings = $.extend({
            btnCancelHtml: lang['lib.cancel'],
            btnPreviousHtml: lang['apl.phrase.window.previous'],
            btnNextHtml: lang['apl.phrase.window.next'],
            btnLastStepHtml: lang['lib.complete'],
            disableNextButton: false,
            completeCallback: function(){ },
            callbacks: { }
        }, options);


        var validCallbacks = function(){
            var everyStepCallback = settings.callbacks['*'];

            if (everyStepCallback !== undefined && typeof(everyStepCallback) !== 'function'){
                throw 'everyStepCallback is not a function! I need a function';
            }

            if (typeof(settings.completeCallback) !== 'function') {
                throw 'completeCallback is not a function! I need a function';
            }

            for(var step in settings.callbacks){
                if (settings.callbacks.hasOwnProperty(step)){
                    var callback = settings.callbacks[step];

                    if (step !== '*' && callback !== undefined && typeof(callback) !== 'function'){
                        throw 'Step ' + step + ' callback must be a function';
                    }
                }
            }
        };

        var executeCallback = function(callback){
            if (callback !== undefined && typeof(callback) === 'function'){
                callback();
                return true;
            }
            return false;
        };

        $modal.on('show.bs.modal', function(){
                var $modalFooter = $modal.find('.modal-footer'),
                    $btnCancel = $modalFooter.find('.js-btn-step[data-orientation=cancel]'),
                    $btnPrevious = $modalFooter.find('.js-btn-step[data-orientation=previous]'),
                    $btnNext = $modalFooter.find('.js-btn-step[data-orientation=next]'),
                    everyStepCallback = settings.callbacks['*'],
                    stepCallback = settings.callbacks['1'],
                    actualStep,
                    $actualStep,
                    titleStep,
                    $titleStepSpan,
                    nextStep;

                if (settings.disableNextButton){
                    $btnNext.attr('disabled', 'disabled');
                }
                $btnPrevious.attr('disabled', 'disabled');

                validCallbacks();
                executeCallback(everyStepCallback);
                executeCallback(stepCallback);

                // Setting buttons
                $btnCancel.html(settings.btnCancelHtml);
                $btnPrevious.html(settings.btnPreviousHtml);
                $btnNext.html(settings.btnNextHtml);

                $actualStep = $('<input>').attr({
                    'type': 'hidden',
                    'id': 'actual-step',
                    'value': '1',
                });

                $modal.find('#actual-step').remove();
                $modal.append($actualStep);

                actualStep = 1;
                nextStep = actualStep + 1;

                $modal.find('[data-step=' + actualStep + ']').removeClass('hide');

                titleStep = $modal.find('[data-step=' + actualStep + ']').data('title');
                $titleStepSpan = $('<span>').addClass('label label-success').html(actualStep);

                $modal.find('.js-title-step').append($titleStepSpan).append(' ' + titleStep);
                
                var steps = $modal.find('div[data-step]').length;
                if (actualStep === steps){
                    $btnNext.attr('data-step', 'complete').html(settings.btnLastStepHtml);
                } else {
                    $btnNext.attr('data-step', nextStep).html(settings.btnNextHtml);
                }
            }).on('hidden.bs.modal', function(){
                var $actualStep = $modal.find('#actual-step'),
                    $btnNext = $modal.find('.js-btn-step[data-orientation=next]');

                $modal.find('[data-step]').not($modal.find('.js-btn-step')).addClass('hide');
                $actualStep.not($modal.find('.js-btn-step')).remove();
                $btnNext.attr('data-step', 1).html(settings.btnNextHtml);
                $modal.find('.js-title-step').html('');
            });

        $modal.find('.js-btn-step').on('click', function(){
            var $btn = $(this),
                $actualStep = $modal.find('#actual-step'),
                $btnPrevious = $modal.find('.js-btn-step[data-orientation=previous]'),
                $btnNext = $modal.find('.js-btn-step[data-orientation=next]'),
                $title = $modal.find('.js-title-step'),
                orientation = $btn.data('orientation'),
                actualStep = parseInt($actualStep.val()),
                everyStepCallback = settings.callbacks['*'],
                steps,
                nextStep,
                $nextStep,
                newTitle;

            steps = $modal.find('div[data-step]').length;

            // Callback on Complete
            if ($btn.attr('data-step') === 'complete'){
                settings.completeCallback();
                $modal.modal('hide');

                return;
            }

            // Check the orientation to make logical operations with actualStep/nextStep
            if (orientation === 'next'){
                nextStep = actualStep + 1;

                $btnPrevious.attr('data-step', actualStep);
                $actualStep.val(nextStep);
            } else if (orientation === 'previous'){
                nextStep = actualStep - 1;

                $btnNext.attr('data-step', actualStep);
                $btnPrevious.attr('data-step', nextStep - 1);

                $actualStep.val(actualStep - 1);
            } else {
                $modal.modal('hide');
                return;
            }

            if (parseInt($actualStep.val()) === steps){
                $btnNext.attr('data-step', 'complete').html(settings.btnLastStepHtml);
            } else {
                $btnNext.attr('data-step', nextStep).html(settings.btnNextHtml);
            }

            if (settings.disableNextButton){
                $btnNext.attr('disabled', 'disabled');
            }

            // Hide and Show steps
            $modal.find('[data-step=' + actualStep + ']').not($modal.find('.js-btn-step')).addClass('hide');
            $modal.find('[data-step=' + nextStep + ']').not($modal.find('.js-btn-step')).removeClass('hide');

            // Just a check for the class of previous button
            if (parseInt($btnPrevious.attr('data-step')) > 0 ){
                $btnPrevious.removeAttr('disabled');
            } else {
                $btnPrevious.attr('disabled', 'disabled');
            }

            if (orientation === 'previous'){
                $btnNext.removeAttr('disabled');
            }

            // Get the next step
            $nextStep = $modal.find('[data-step=' + nextStep + ']');

            // Verify if we need to unlock continue btn of the next step
            if ($nextStep.attr('data-unlock-continue')){
                $btnNext.removeAttr('disabled');
            }

            // Set the title of step
            newTitle = $nextStep.attr('data-title');
            var $titleStepSpan = $('<span>').addClass('label label-success').html(nextStep);
            $title.html($titleStepSpan).append(' ' + newTitle);

            var stepCallback = settings.callbacks[$actualStep.val()];
            executeCallback(everyStepCallback);
            executeCallback(stepCallback);
        });

        return this;
    };
}(jQuery));

jQuery(document).ready( function(){
	jQuery('#add_master').modalSteps({
		btnPreviousHtml: lang['apl.phrase.window.tempsel'],
		completeCallback: function(){ Flow.cmsContent.setJobForm(); },
        callbacks: { 2 : function() { Flow.cmsContent.prepareAddDlg(); } }
	});
	
	jQuery("#add_master .js-btn-step[data-orientation=previous]").click( function() {
		var lOld = Flow.cmsContent.tempSave();
	});

	jQuery('#approve_review').modalSteps({
        btnLastStepHtml: lang['apl.phrase.complete.approval'],
        disableNextButton: true,
		completeCallback: function(){ Flow.cmsApl.complete(); }
	});

	jQuery('#add_translation').modalSteps({
        btnLastStepHtml: lang['lib.complete'],
        disableNextButton: true,
		completeCallback: function(){ Flow.cmsAdd.complete(); }
	});
	
	if(jQuery("#productChange").length > 0){
		jQuery("#productChange").modal({ show: false });
		jQuery("#change_product").click( function() {
			var lParams = { 
        		act : 'job-cms.updatejob',
	            job : JSON.stringify(lJobData),
                jobid: jQuery("input[name='jobid']").val(),
                src: jQuery("input[name='src']").val()
            };
        	jQuery.post('index.php', lParams, function(aData) {
        		//alert user with updated notification
        		var lData = lang['phrase.product.update.desc']+' '+lParams['code'];
        		jQuery("#productChange").modal('hide');
        		location.reload();
        	});
		});
	}

	if(jQuery("#productUpdate").length > 0){
		jQuery("#productUpdate").modal({ show: false });
		jQuery("#update_product_yes").click( function() {
	    	jQuery("#referenceUpdate").val("yes");
	    	jQuery("#save").click();
		});
		jQuery("#update_product_no").click( function() {
	    	jQuery("#referenceUpdate").val("no");
	    	jQuery("#save").click();
		});
	}
	
	if(jQuery("#deleteItem").length > 0){
		jQuery("#deleteItem").modal({ show: false });
	}
	
	jQuery('#saveNewTemplate').click( function() {
    	//dispay name field and save button
		jQuery('.result').html('');
		jQuery(".question").removeClass("hide");
    	jQuery(".save_template").slideToggle('slow');
	});
	jQuery('#saveName').click( function() {
    	var lCombos = Flow.cmsTemplate.getCombos();
		var lName = jQuery("#template_name");
    	if(Object.keys(lCombos).length > 0 && lName.val() != ''){
    		lName.removeAttr("style");
    		Flow.cmsTemplate.save(lName.val(), lCombos, 'new'); //save template and set items on page
    	} else {
    		lName.attr("style", "border: 1px solid #FF0000;");
    	}
	});
	
	jQuery('#updateTemplate').click( function() {
    	var lCombos = Flow.cmsTemplate.getCombos();
    	if(Object.keys(lCombos).length > 0){
    		jQuery('.result').html('');
        	jQuery(".save_template").slideToggle('slow');
        	
        	var lName = jQuery("#templates").val();
        	if(lName != '') Flow.cmsTemplate.save(lName, lCombos, 'update'); //save template and set items on page
    	}
	});
	
	jQuery('#addMasterAreaList, #addAreaList, #arAreaList').on('shown.bs.collapse', function (e) {
		var lLength = jQuery(".collapse.in").find("ul li.active").length;
		if(lLength < 1) {
			jQuery(".collapse.in").find("ul li:first").click();
		}
	});
});

Flow.cmsTemplate = {	
    select : function() { //when selecting a template gather data from db and auto fill dialog
    	var lTemplate = jQuery("#templates").val();
    	
    	if(lTemplate != ''){
    		jQuery('#saveNewTemplate').removeAttr("disabled").removeClass("disabled");
	        jQuery('#updateTemplate').removeAttr("disabled").removeClass("disabled");
	        
	        var lDisabled = jQuery('select[name="cat"]').is('disabled');
	        if(lDisabled) {
	        	jQuery('select[name="cat"], select[name="layout"], input[name="amount"]').removeAttr("disabled");
	        }
	        
    		var lCombo = [];
    		//retrieve all combos for template name
    		var lParams = {
    				act : 'job-cms.gettemplate',
    				name: lTemplate,
    	            jobid: jQuery("input[name='jobid']").val(),
    	            src: jQuery("input[name='src']").val()
    		};
    		jQuery.post('index.php', lParams, function(aData) {
    			lCombo = aData;
    			jQuery(".content_item").not(":last").remove();
    			var lItem = jQuery(".content_item:first");
    			var lLength = lItem.find("select[name='cat']").attr("id").replace(/[^0-9]/gi, '');

    	    	var lNewItem = lItem.prop('outerHTML');
    	    	var lFields = new Array("category","layout","amount");
    	    	for(var lI=0; lI < lFields.length; lI++) {
    	    		var lField = lFields[lI];
    	    		var lReg = new RegExp(lField+lLength, "g");
    	    		lNewItem = lNewItem.replace(lReg, lField+"1");
    	    	}
    	    	lItem.replaceWith(lNewItem);
    	    	
    			//add to item dialog
    			for(var i=0; i < Object.keys(lCombo).length; i++){
    				var lCategory = lCombo[i]['category'];
    				var lLayout = lCombo[i]['layout'];
    				var lAmount = lCombo[i]['amount'];
    				
    				var lRow = jQuery(".removeBtn:last").parent().parent();
    				lRow.find(".cms_cat").val(lCategory);

    				var lLayoutSel = lRow.find(".cms_lay");
		 			lLayoutSel.find('option[value!=""]').remove();
		        	for(lKey in lLayoutOptions) {
		        	  var lOption = jQuery("<option></option>").attr("value", lKey).text(lLayoutOptions[lKey]);
		        	  lLayoutSel.append( lOption );
		        	}
		 			lLayoutSel.find('option[value!=""][value!="'+lLayout+'"]').remove();
	    			lLayoutSel.val(lLayout).change();

    				lRow.find(".cms_amt").val(lAmount);
    			}

    	        if(lDisabled) {
    	        	jQuery('select[name="cat"], select[name="layout"], input[name="amount"]').attr("disabled", true);
    	        }
    		}, 'json');
    	} else {
	        jQuery('#saveNewTemplate').attr("disabled", true).addClass("disabled");
	        jQuery('#updateTemplate').attr("disabled", true).addClass("disabled");
	        
	        var lDisabled = jQuery('select[name="cat"]').is('disabled');
	        if(lDisabled) {
	        	jQuery('select[name="cat"], select[name="layout"], input[name="amount"]').removeAttr("disabled");
	        }
	        
	        jQuery(".content_item").not(":last").remove();
    		var lItem = jQuery(".content_item:first");
    		var lLength = lItem.find("select[name='cat']").attr("id").replace(/[^0-9]/gi, '');

    	    var lNewItem = lItem.prop('outerHTML');
    	    var lFields = new Array("category","layout","amount");
    	    for(var lI=0; lI < lFields.length; lI++) {
    	    	var lField = lFields[lI];
    	    	var lReg = new RegExp(lField+lLength, "g");
    	    	lNewItem = lNewItem.replace(lReg, lField+"1");
    	    }
    	    lItem.replaceWith(lNewItem);
			
			var lRow = jQuery(".removeBtn:last").parent().parent();
			lRow.find(".cms_cat").val('');
			lRow.find(".cms_amt").val(1);
			lRow.find(".cms_lay").val('');
			
			//select what is on form
			jQuery("#job_content div.section").each( function() {
				var lSection = jQuery(this);
				var lCategory = lSection.attr("id");
				var lLayout = lSection.find("input[name$='_layout]']").val();
				var lAmount = lSection.find(".item").length;
				
				var lRow = jQuery(".removeBtn:last").parent().parent();
				lRow.find(".cms_cat").val(lCategory);

				var lLayoutSel = lRow.find(".cms_lay");
	 			lLayoutSel.find('option[value!=""]').remove();
	        	for(lKey in lLayoutOptions) {
	        	  var lOption = jQuery("<option></option>").attr("value", lKey).text(lLayoutOptions[lKey]);
	        	  lLayoutSel.append( lOption );
	        	}
	 			lLayoutSel.find('option[value!=""][value!="'+lLayout+'"]').remove();
    			lLayoutSel.val(lLayout).change();
				
				lRow.find(".cms_amt").val(lAmount);
			});
			
    	    if(lDisabled) {
    	      jQuery('select[name="cat"], select[name="layout"], input[name="amount"]').attr("disabled", true);
    	    }
    	}
    },
    addItem : function(aThis) { //add new row to item dialog
    	var lSelf = jQuery(aThis);
    	var lLayoutId = jQuery(".content_item:last").find(".cms_lay").attr("id");
    	var lLength = parseInt( lLayoutId.match(/\d+/) );
    	
    	if(lSelf.attr("id") == "layout"+lLength || jQuery("#layout"+lLength).length == 0) {
	    	var lItem = lSelf.closest(".content_item");
	    	var lNewItem = lItem.clone();
	
	    	var lNewItem = lNewItem.prop('outerHTML');
	
	    	var lFields = new Array("category","layout","amount");
	    	for(var lI=0; lI < lFields.length; lI++) {
	    		var lField = lFields[lI];
	    		var lReg = new RegExp(lField+lLength, "g");
	    		lNewItem = lNewItem.replace(lReg, lField+(lLength+1));
	    	}
	    	
	    	lNewItem = jQuery(lNewItem);
	    	
	    	lNewItem.find("option").removeAttr("selected");
	    	lNewItem.insertAfter(lItem);
	
			jQuery('#saveNewTemplate').removeAttr("disabled").removeClass("disabled");
			var lTemplate = jQuery("#templates").val();
	    	if(lTemplate != ''){
	    		jQuery('#updateTemplate').removeAttr("disabled").removeClass("disabled");
	    	}
    	}
    },
    removeItem : function(aThis) { //remove row from item dialog
    	var lItem = jQuery(aThis).closest(".content_item");
    	lItem.remove();
    },
    getCombos : function() { //gather items from item dialog to use
    	var lItems = [];
    	jQuery(".content_item").each( function() {
    		var lSelf = jQuery(this);
    		
    		var lCategory = lSelf.find('.cms_cat').val();
            var lLayout = lSelf.find('.cms_lay').val();
            var lAmount = parseInt( lSelf.find('.cms_amt').val() );
            
            if(lCategory != '' && lLayout != '' && lAmount > 0){
            	lItems.push({'category': lCategory, 'layout': lLayout, 'amount': lAmount});
            }
    	});
    	
    	return lItems;
    },
    getLayouts : function(aLayout, aCategory) {
        var lLayout = jQuery('#' + aLayout);
        var lCategory = jQuery('#' + aCategory).val();
        
        var lRes = lCategories[lCategory]['layouts'];
        lRes = lRes.split(',');
        lRes.unshift('');
        
        var lAuto = false;
        var lLength = lRes.length;
        var lSelectSingle = (lLength == 2);
        lLayout.find("option").remove();
        for (var i = 0; i < lLength; i++) {
            var lRow = lRes[i];
            var lKey = lRow;
            var lVal = (lRow == '') ? ' ' : lLayoutOptions[ lRow ];
            var lSel = false;
            if (lSelectSingle && lKey != '' && lKey != ' ') {
            	lSel = true;
            }
            if (lSel) {
              lAuto = true;
            }
            var lTmp = new Option(lVal, lKey, lSel, lSel);
            lLayout.append(lTmp);
        }
        if (lAuto) {
          lLayout.change();
        }

    },
    save : function(aName, aItems, aTyp) { //saving item dialog selection as a template
    	var lParams = {
            act : 'job-cms.settemplate',
            name: aName,
            data: JSON.stringify(aItems),
            method: aTyp,
            src: jQuery("input[name='src']").val()
        };
    	var lMsgTyp = (aTyp == 'new') ? lang['lib.created'] : lang['lib.updated'];
        jQuery.post('index.php', lParams, function(aData) {
    		var lData = '<p>'+lang['lib.template']+': '+aName+' '+lang['lib.hasbeen']+' '+lMsgTyp+' '+lang['lib.successfully']+'</p>';
    		jQuery('.result').html(lData);

    		jQuery(".question").addClass("hide");
    		setTimeout( function() {
	    		var lTemplates = jQuery("#templates");
	        	if(aTyp == 'new') {
	        		var lOption = jQuery("<option></option>").attr("value",aName).text(aName);
	        		lTemplates.append( lOption );
	        	}
	        	lTemplates.val(aName).change();
	        	jQuery(".save_template").slideUp("slow");
	    		jQuery('.result').html('');
			}, 2000);
        });
    }
};

Flow.cmsForm = {
	showTranslation : function(aSelf) {
    	var lSelf = jQuery("#"+aSelf);
    	jQuery("*[class^='translation_']").addClass("dn");
    	
    	var lChecked = jQuery("input:checkbox[id^='trans_']:checked").length;
    	if(lChecked > 3) {
    		lSelf.removeAttr("checked");
    	}

		jQuery("input:checkbox[id^='trans_']").each( function() {
    		var lChkBox = jQuery(this);
    		if(lChkBox.is(":checked")){
    			var lLang = lChkBox.val();
    	    	jQuery(".translation_"+lLang).removeClass("dn");
    		}
    	});
    },
    setItems : function(aCombos, aData, aAdd) { //adding item into general layout (Combos/Search Content)
    	var lData = (typeof aData != 'undefined') ? aData : [];
    	var lAdditional = (typeof aAdd != 'undefined') ? true : false;
    	
    	var lContainer = jQuery('#job_content');
    	var lContainerHtml = lContainer.html();
    	var lSize = lContainer.find(".item").length;

    	var lCombos = new Array();
    	if(lAdditional == false) {
	    	//restructure combos as to what is there
	    	jQuery.each(aCombos, function (lI, lCombo) {
	    		if(lCombo !== undefined) {
		    		var lAmount = lCombo['amount'];
		    		var lCatSize = lContainer.find("#"+lCombo['category']).find(".item").length;
		    		if(lCatSize > 0) {
			    		var lDiff = lAmount - lCatSize;
			    		if(lDiff > 0) {
			    			lCombo['amount'] = lDiff;
			    			lCombos.push(lCombo);
			    		}
		    		} else {
		    			lCombos.push(lCombo);
		    		}
	    		}
	    	});
	    } else {
	    	lCombos = aCombos;
	    }

    	if(Object.keys(lCombos).length > 0) {
	        var lParams = {
	            act : 'job-cms.getitems',
	            combo: JSON.stringify(lCombos),
	            data: JSON.stringify(lData),
	            size: lSize,
	            jobid: jQuery("input[name='jobid']").val(),
	            src: jQuery("input[name='src']").val(),
	            job : JSON.stringify(lJobData)
	        };
	    	
	        jQuery.post('index.php', lParams, function(aData) {
	        	for(lItem in aData){ //category
	        		for(var i=0; i < aData[lItem].length; i++){ //cycle through each category
	        			var lPresent = false;
	        			var lHtml = aData[lItem][i];
	        			var lCheck = jQuery(lHtml).find("input[name$='ma_cid]']").val();
	        			jQuery(lContainerHtml).find("input[name$='ma_cid]']").each( function() {
	        				if(jQuery(this).val() == lCheck && parseInt(lCheck) > 0){
	        					lPresent = true;
	        				}
	        			});
	        			
	        			if(!lPresent) {
			        		if(jQuery("#"+lItem).length > 0) {
			        			lContainer.find("#"+lItem).append(lHtml);
			        		} else {
			        			lHtml = '<div class="th1 fl w100p">' + lItem + '</div>' + lHtml;
			        			lHtml = jQuery('<div>', {id: lItem, 'class': "section"}).append(lHtml);
			        			lContainer.append(lHtml);
			        		}
	        			}
	        		}
	        	}
	        	Flow.cmsForm.showTranslation();
	        }, 'json');
    	}
    },
    getAmount : function(aCat, aAmt) {
    	var lCategory = jQuery("#"+aCat).val();
    	var lSize = jQuery('#job_content').find("#"+lCategory).find(".item").length;
    	lSize = (lSize > 0) ? lSize : 1;
    	jQuery("#"+aAmt).val(lSize);
    },
    checkContent : function(aId) {
    	var lItem = jQuery("*[name='val["+aId+"]']:first");
    	var lNew = lItem.val();
    	var lOld = jQuery("*[name='old["+aId+"]']").val();

    	if(lNew != lOld) {
    		if(lNew != "" && lOld != "") {
    			lItem.addClass("phrase_update");
    		} else if (lNew != "" && lOld == "") {
    			lItem.addClass("phrase_new");
    		}
    	}
    },
    removeContent : function(aThis) {
    	var lHtml = '<p>'+lang['phrase.contentremoval']+':<br/><br/><b>';
    	lHtml+= jQuery(aThis).parent().next('div').text();
    	lHtml+= '</b>.<br/><br/>'+lang['phrase.contentrem.question']+'</p>';
		
        jQuery(".contentRemoval").html(lHtml);
        jQuery("#deleteItem").modal('show');

		jQuery("#delete_item").click( function() {
	    	var lItem = jQuery(aThis).closest(".item");
	    	var lGroupRemoved = lItem.find("input[name$='_group]']").val();
	    	jQuery(aThis).closest(".section").find("input[name$='_group]']").each( function() {
	    		var lGroup = jQuery(this).val();
	    		if(parseInt(lGroup) > parseInt(lGroupRemoved)) {
	    		  lGroup = parseInt(lGroup) -1;
	    		}
	    		
	    		jQuery(this).val(lGroup);
	    	});
	    	lItem.remove();
	        jQuery("#deleteItem").modal('hide');
	    	jQuery("#save").click();
		});
    }
};

Flow.cmsSearch = {
	results : function() { //get search results
    	var lMeta = {};
    	jQuery("input:checkbox[name^='meta']:checked").each( function() {
    		var lId = jQuery(this).prop("id");
    		var lValue = jQuery(this).prop("value");
    		
    		lMeta[lId] = lValue;
    	});

    	var lChosen = [];
    	jQuery(".content_search").each( function() {
    		var lVal = jQuery(this).val();
    		
    		if(lVal !== '') {
    			lChosen.push( lVal );
    		}
    	});
    	
    	var lCategory = '';
    	jQuery.each(lCategories, function(lKey, lVal) {
    		var lCatTitle = jQuery(".category_title").html().replace(/\d+/g, '');
    		lCatTitle = jQuery.trim(lCatTitle);
    		if(lVal['name'] == lCatTitle){
    			lCategory = lKey;
    		}
    	});
    	
    	var lParams = {
            act : 'job-cms.search',
            content: jQuery(".content_section:not(.dn) .content_search:focus").val(),
            chosen : lChosen,
            meta: lMeta,
            language: 'MA',
            id: lCategory,
            src: jQuery("input[name='src']").val()
        };

        jQuery.post('index.php', lParams, function(aData) {
        	var lResults = jQuery("#cms_sugg");
        	lResults.html("");
        	
        	for(var i=0; i < aData.length; i++){
        		var lSugg = aData[i];

        		var lSuggestion = jQuery('<div>', { 'id': lSugg['content_id'], 'class': "p4 m5 suggestion "+lSugg['language'] });
        		
        		var lVersion = jQuery('<span>', {'class': "m5 version"}).text(lSugg['version']);
        		//lVersion.appendTo(lSuggestion);
        		
        		var lContent = jQuery('<span>', {'class': "content"}).html(lSugg['content']);
        		lContent.appendTo(lSuggestion);
        		
        		var lLanguage = lSugg['language'].charAt(0).toUpperCase() + lSugg['language'].substr(1);
        		var lTooltip = '<b>Category:</b> '+lSugg['categories']+'<br/><b>Language:</b> '+lLanguage+'<br/><b>Version:</b> '+lSugg['version'];
        		lTooltip+= '<br/><b>Status:</b> '+lSugg['status'];
        		var lInfo = jQuery('<span>', {'class': "info fr", 'data-toggle': 'tooltip', 'data-tooltip-head' : '', 'data-tooltip-body': lTooltip}).html("<img src='img/wave8/ico/16/ml-1.gif' alt='' />");
        		//lInfo.appendTo(lSuggestion);
        		
        		for(lKey in lSugg){
        			lSuggestion.data(lKey, lSugg[lKey]);
        		}
        		lResults.append(lSuggestion);

				jQuery('.suggestion').draggable({
				  	helper: "clone",
				  	snap: true,
				  	iframeFix: true
				});

				jQuery('.content_search, .mceIframeContainer iframe').droppable({
				  	snap: true,
	                iframeFix: true,
				    drop: function (event, ui) {
						var lData = jQuery(ui.draggable).data();
						lData.status = 'draft';
						
						var lSelf = jQuery(this).closest(".content");

						//replace existing content in lContentData with new content chosen
						if(lSelf.find("*[name^='val[']").val() !== '') {
							var lParams = { 'parent_id' : parseInt( lSelf.find("input[name$='_pid]']").val() ) };
							var lExists = Flow.cmsContent.searchContent(lContentData, lParams);
				    		if(lExists.length > 0){
					    		var lTempData = [];
				    			jQuery.each(lContentData, function(lIdx, lObj){
				    				if(jQuery.inArray(lIdx, lExists) < 0) {
				    					lTempData.push(lObj);
				    				}
				    			});
				    			lContentData = lTempData;
				    		}
				    		var lObject = {
				    		  'category': lData.categories,
				    		  'content': lData.content,
				    		  'content_id': lData.content_id,
				    		  'group': lSelf.find("input[name$='_group]']").val(),
				    		  'language': 'MA',
				    		  'layout': lSelf.find("input[name$='_layout]']").val(),
				    		  'metadata': lData.metadata,
				    		  'parent_id': lData.parent_id,
				    		  'position': '',
				    		  'status': lData.status,
				    		  'type': lSelf.find("input[name$='_type]']").val(),
				    		  'version': lData.version	
				    		};
				    		lContentData.push( lObject );
						}
						
						//place new content in form
						lSelf.find("input[name$='_cid]']").val(lData.content_id);
						lSelf.find("input[name$='_pid]']").val(lData.parent_id);
						lSelf.find("input[name$='_ver]']").val(lData.version);
						lSelf.find("input[name$='_status]']").val(lData.status);
						lSelf.find("input[name^='old[']").val(lData.content);
						lSelf.find("*[name^='val[']").val(lData.content).attr("value", lData.content).removeClass("no_content");
						jQuery(".status_class").removeClass("content_approved content_draft").addClass("content_"+lData.status);
						jQuery(".status_title").html(lData.status.toUpperCase());

						var iframe = lSelf.find("iframe[id$='_ifr']");
						iframe.ready(function() {
						    iframe.contents().find("body").html(lData.content);
						});
				    }
				});
        	}
        }, 'json');
    },
    metaCheck : function (aSelf) {
    	var lSelf = jQuery("#"+aSelf);
    	if(lSelf.is(":checked")) {
    		lSelf.prevAll("input:checkbox[name^='meta']").each( function() {
	    		var lChkBox = jQuery(this);
	    		if(!lChkBox.is(":checked")){
	    			lChkBox.prop('checked', 'checked');
	    		}
	    	});
    	}
    	Flow.cmsSearch.results();
    }
};

Flow.cmsContent = {
	text : [],
	prepareAddDlg : function() {
		var lCombos = Flow.cmsTemplate.getCombos();
		
		Flow.cmsContent.prepareNav(lCombos, '#addMasterAreaList', 1);
		Flow.cmsContent.prepareAdd(lCombos, 1);
	},
	prepareNav : function(aCombos, aArea, aSecNum) {
		var lNavigation = '';
		var lSecNum = 1;
		var lActiveNum = aSecNum;
		
		//prepare left-hand side menu navigation
		var lCombos = {};
		for(var i=0; i < Object.keys(aCombos).length; i++){
			var lCategory = aCombos[i]['category'];
			var lAmount = aCombos[i]['amount'];
			
			if(lCombos[lCategory] === undefined ) {
				lCombos[lCategory] = 0;
			}
			lCombos[lCategory] = lCombos[lCategory] + parseInt(lAmount);
		}

		for(lCategory in lCombos){
			var lAmount = lCombos[lCategory];
			var lLabel = (lCategories[lCategory] == undefined) ? lCategory : lCategories[lCategory]['name'];
			var lTooltip = (lCategories[lCategory] == undefined) ? '' : lCategories[lCategory]['tooltip'];
			
			lNavigation += '<div class="panel panel-default">';

		    lNavigation += '<div class="panel-heading">';
		    lNavigation += '<h4 class="panel-title">';
		    lNavigation += '<a data-toggle="collapse" data-parent="'+aArea+'" href="#collapse'+lSecNum+'">';
			lNavigation += (lTooltip != '') ? "<span data-toggle='tooltip' data-tooltip-head ='"+lLabel+"' data-tooltip-body='"+jQuery("<div>"+lTooltip+"</div>").html()+"'>" : '';
		    lNavigation += lLabel;
		    lNavigation += (lTooltip != '') ? '</span>' : '';
		    lNavigation += ' <span class="badge pull-right">'+lAmount+'</span></a>';
		    lNavigation += '</h4>';
		    lNavigation += '</div>';
			  
			var lClass = (lSecNum == lActiveNum) ? ' in' : '';
		    lNavigation += '<div id="collapse'+lSecNum+'" class="panel-collapse collapse'+lClass+'"><ul class="list-group">';
		    	
			//get layout for content input
			for(var j=0; j < lAmount; j++){
				var lActive = (lSecNum == lActiveNum) ? ' active' : '';
				var lItem = (lLabel.indexOf( lang['lib.summary'] ) < 0) ? lLabel+' '+(j+1) : lLabel;
				lNavigation += '<li class="section'+lSecNum+lActive+' list-group-item">'+lItem+'</li>';
				lSecNum++;
			}
			lNavigation += '</ul></div></div>';
		}
		jQuery(aArea).html(lNavigation);
		
		jQuery(aArea+' li[class^="section"]').click( function() {
			var lClass = jQuery(this).attr('class').split(" ");
			var lSection = lClass[0];
			
			Flow.cmsContent.show(null, aArea, lSection);
		});
	},
	prepareAdd : function(aCombos, aActiveNum) {
		//getsection html
		var lParams = { 
			act : 'job-cms.contentsections',
			combos: JSON.stringify(aCombos),
			content: JSON.stringify(lContentData),
			job: JSON.stringify(lJobData),
			active: aActiveNum,
			src: jQuery("input[name='src']").val()
		};
        jQuery.post('index.php', lParams, function(aData) {
            jQuery("#contAreas").html(aData);
            
            var lElm = jQuery(".content_section:not(.dn)");
			var lCategory = lElm.attr("data-category").split(" ");
            jQuery(".category_title").html(lCategories[lCategory[0]]['name']+' '+lCategory[1]);
            var lStatus = lElm.find("input[name$='_status]']").val();
            if(lStatus !== undefined) {
            	jQuery(".status_title").html(lStatus.toUpperCase());
            	jQuery(".status_class").attr("class", "card-header status_class content_"+lStatus);
            }

            if(lElm.prev('.content_section').length  < 1){ jQuery(".contprev").addClass("disabled"); } else { jQuery(".contprev").removeClass("disabled"); }
    		if(lElm.next('.content_section').length  < 1){ jQuery(".contnext").addClass("disabled"); } else { jQuery(".contnext").removeClass("disabled"); }

            jQuery(".content_search").keyup( function() { Flow.cmsSearch.results(); });
            
    		Flow.cmsSearch.results();
        }, 'json');
	},
	notneeded : function() {
		var lElm = jQuery(".content_section:not(.dn)");
		
		var lInput = lElm.find(".content_search");
		var lLayout = lInput.siblings("input[name$='_layout]']").val();
		var lVal = (lLayout.indexOf("nutri") > -1) ? 'N/A' : lang['lib.no-content-needed']+" "+jQuery(".category_title").html();
		
		var lId = lInput.attr("id");
		var lEditor = lInput.attr('data-btns');
		if (typeof lEditor !== 'undefined' && lEditor !== false) {
			tinyMCE.get(lId).setContent( lVal );
		} else {
			lInput.val( lVal );
		}
	},
	record : function(aThis, aField) {
		var lElm = jQuery(aThis);
		if(lElm.val() == 'ALL') {
		  var lActive = lElm.is(":checked");
		  lElm.parent('div').find("input[name^='"+aField+"']").each( function() {
				var lChk = jQuery(this);
				if(lActive) {
					lChk.prop("checked", true);
				} else {
					lChk.removeAttr("checked");
				}
			});
		}

		var lVal = [];
		lElm.parent('div').find("input[name^='"+aField+"']").each( function() {
			var lChk = jQuery(this);
			if(lChk.is(":checked")) {
				lVal.push( lChk.val() );
			}
		});
		
		lElm.closest("div.content").find("input[name$='_"+aField+"]']").val( lVal.join(" ") );
	},
	show : function(aType, aArea, lSection) {
		var lShown = jQuery(".content_section:not(.dn)");
		var lId = lShown.attr("id");
		if(lId == lSection) return;
		
		var lElm = jQuery("<div></div>");
		if(aType == null) {
			lElm = jQuery("#"+lSection);
			if(lElm.length  > 0){
				var lCategory = lElm.attr("data-category").split(" ");
	            jQuery(".category_title").html(lCategories[lCategory[0]]['name']+' '+lCategory[1]);

	            var lStatus = lElm.find("input[name$='_status]']").val();
	            if(lStatus !== undefined) {
	            	jQuery(".status_title").html(lStatus.toUpperCase());
	            	jQuery(".status_class").attr("class", "card-header status_class content_"+lStatus);
	            }
	            
				jQuery(aArea+" .list-group-item").removeClass("active");
				var lMenuItem = jQuery("."+lSection);
				lMenuItem.addClass("active");
				
				lElm.removeClass('dn');		
				lShown.addClass("dn");

				Flow.cmsContent.size(lCategory[0], lElm);
			}
		} else {
			if(jQuery(".cont"+aType).hasClass("disabled") == false) {
				lShown = (lSection !== undefined) ? jQuery("#"+lSection) : lShown;
				lElm = (aType == 'prev') ? lShown.prev('.content_section') : lShown.next('.content_section');
				if(lElm.length  > 0){
					var lId = lElm.attr("id");
					if(jQuery("."+lId).attr("style") !== undefined) {
						Flow.cmsContent.show(aType, aArea, lId);
					} else {
						var lCategory = lElm.attr("data-category").split(" ");
			            jQuery(".category_title").html(lCategories[lCategory[0]]['name']+' '+lCategory[1]);
	
			            var lStatus = lElm.find("input[name$='_status]']").val();
			            if(lStatus !== undefined) {
			            	jQuery(".status_title").html(lStatus.toUpperCase());
			            	jQuery(".status_class").attr("class", "card-header status_class content_"+lStatus);
			            }
		
						jQuery(aArea+" .list-group-item").removeClass("active");
						var lId = lElm.attr("id");
						var lMenuItem = jQuery("."+lId);
						lMenuItem.addClass("active");
						if(lMenuItem.closest(".collapse").hasClass("in") !== true){
							lMenuItem.closest(".panel").find("a[data-toggle='collapse']").click();
						}
						
						lElm.removeClass('dn');		
						lShown.addClass("dn");
						
						Flow.cmsContent.size(lCategory[0], lElm);
					}
				}
			} else {
				lElm = lShown;
			}
		}
		var lTask = jQuery('#task').val();
		if(lTask == undefined) {
			Flow.cmsSearch.results();
		}

		if(lElm.prev('.content_section').length  < 1){ jQuery(".contprev").addClass("disabled"); } else { jQuery(".contprev").removeClass("disabled"); }
		if(lElm.next('.content_section').length  < 1){ jQuery(".contnext").addClass("disabled"); } else { jQuery(".contnext").removeClass("disabled"); }
	},
	size : function(lCategory, lElm) {
		if(lCategory == lang['phrase.nutrifacts']) {
			var lSection = lElm.attr("data-category").split(" ");
			var lNum = lSection[1];
			
			jQuery("#search_area").hide();
			jQuery("#content_area").addClass('full-width');
			
			var lNutriFactNum = 1;
			jQuery("#add_master *[name^='val[NutritionFacts_']").each( function() {
				var lVal = jQuery(this).val();

				if(lNum == lNutriFactNum) {
					if(lVal !== undefined && lVal !== '') {
						lElm.find("input[name^='val[per100_']").val(lVal).removeClass("no_content");
					}
				}
				lNutriFactNum++;
			});
			
			var lNutriPortionNum = 1;
			jQuery("#add_master *[name^='val[NutritionPortion_']").each( function() {
				var lVal = jQuery(this).val();
				
				if(lNum == lNutriPortionNum) {
					if(lVal !== undefined && lVal !== '') {
						lElm.find("input[name^='val[perserving_']").val(lVal).removeClass("no_content");
					}
				}
				lNutriPortionNum++;
			});
		} else {
			jQuery("#search_area").show();
			jQuery("#content_area").removeClass('full-width');
		}
	},
    tempSave : function() {
    	var lOld = [];
    	var lCats = [];
    	var lTemp = [];

    	//get all content from sections (val[] & old[])
    	jQuery(".content_section .content_search").each( function() {
    		var lCont = jQuery(this);
    		var lId = lCont.attr("id");
    		var lDetails = lId.split("_");
    		
    		var lParams = {
    	        "content_id": lCont.siblings("input[name$='_cid]']").val(),
    	        "parent_id": parseInt( lCont.siblings("input[name$='_pid]']").val() ),
    	        "category": lDetails[0],
    	        "language": lDetails[2].toUpperCase(),
    	        "version": lCont.siblings("input[name$='_ver]']").val(),
    	        "group": lCont.siblings("input[name$='_group]']").val(),
    	        "ntn": lCont.siblings("input[name$='_ntn]']").val(),
    	        "packtypes": lCont.siblings("input[name$='_packtypes]']").val(),
    	        "type": lCont.siblings("input[name$='_type]']").val(),
    	        "layout": lCont.siblings("input[name$='_layout]']").val()
    	    };
    		if(lParams['layout'].indexOf('nutri') > -1) {
    			//category to metadata
    			lParams['metadata'] = lParams['category'];
    			lParams['category'] = 'Nutrition';
    		}
    		
    		var lExists = Flow.cmsContent.searchContent(lContentData, lParams);
    		if(lExists.length > 0){
    			lOld.push( { key: lId, val: lCont.siblings("input[name^='old[']").val() } );
    		}
    		
			if(lParams['layout'] == 'rich'){
				var lTinyCont = jQuery( tinyMCE.get(lId).getContent() ).unwrap();
				lParams.content = jQuery("<div />").append(lTinyCont.clone()).html();
			} else {
				lParams.content = lCont.val();
			}
			lParams.position = null;
			if(lParams['layout'].indexOf('nutri') < 0) {
				lParams.metadata = [ lCont.siblings("input[name$='_meta]']").val() ];
			}
			lParams.status = lCont.siblings("input[name$='_status]']").val();
			
			if(lParams.content == '' && jQuery("#noContent").length > 0) {
				var lCategory = lCont.closest(".content_section").attr("data-category").split(" ");
	            var lCat = lCategories[lCategory[0]]['name']+' '+lCategory[1];

	            lParams.content = (lParams['layout'].indexOf("nutri") > -1) ? 'N/A' : lang['lib.no-content-needed.notentered']+" "+lCat;
			}

			//add into lContentData
			if(lParams.content !== ''){
				lTemp.push(lParams);
			}

    		if(jQuery.inArray(lParams['category'], lCats) < 0){
    			lCats.push( lParams['category'] );
    		}
    	});
    	lContentData = lTemp;

    	// remove any content which is now not needed
    	var lTmpContent = [];
    	for(var i=0; i < Object.keys(lContentData).length; i++) {
    		var lCategory = lContentData[i]['category'];

    		if(jQuery.inArray(lCategory, lCats) > -1){
    		  lTmpContent.push( lContentData[i] );
    		}
    	}
    	lContentData = lTmpContent;
    	
    	return lOld;
	},
    setJobForm : function() {
    	var lOld = Flow.cmsContent.tempSave();
    	
    	//get form html with new content
    	var lParams = {
    	  act : 'job-cms.getjobform',
          jobid: jQuery("input[name='jobid']").val(),
          src: jQuery("input[name='src']").val(),
          job : JSON.stringify(lJobData),
          content : JSON.stringify(lContentData)
        };
        jQuery.post('index.php', lParams, function(aData) {
        	jQuery("#job_content").replaceWith(aData);
        	//set old values
        	jQuery.each(lOld, function(lKey, lObj) {
        		jQuery("input[name='old["+lObj.key+"]'").val(lObj.val);
        	});
        	
        	jQuery("#contAreas, #cms_sugg").html(""); //Remmove sections from content dialog
        	jQuery("#save").click(); //submit form when completing content dialog
        }, 'json');
    },
	duplicateItem : function(aId) { //duplicate item in multi/ingred layouts
    	var lSelf = jQuery("#contAreas #"+aId);
    	
    	if(lSelf.val() !== "") {
	        var lContent = lSelf.closest(".content");
	        var lItem = lContent.parent(".item");
	        var lClone = lContent.html();
	        
	    	var lId = aId.substr(0, aId.length-3);
	        var lCategory = lId.replace(/\d+/g, '');
	        
	        var lContainer = jQuery('#contAreas');
	    	var lSize = lContainer.find(".content").length;
	    	
			var lCheck = lCategory+(lSize+1)+"_ma";
	    	if(lSelf.attr("id") == lCheck || jQuery("#"+lCheck).length == 0) {
	    		var lNewClass = lCategory + (lSize + 1);
		    	var lPattern = new RegExp(lId, 'g');
		    	lClone = '<div class="content fl w100p">' + lClone.replace(lPattern, lNewClass) + '</div>';
		    	
		    	//remove values
		    	lClone = jQuery(lClone);
		    	lClone.find("input[type='text']").val("");
		    	lClone.find("input[name$='_cid]']").val("0");
		    	lClone.find("input[name$='_pid]']").val("0");
		    	lClone.find("input[name$='_ver]']").val("1");
		    	lClone.find("input[name^='old[']").val("");
		    	lClone.find("input[name^='val[']").val("").attr("value", "");
		    	lClone.find("input[name$='_ma]']").removeAttr("readonly");
		
		    	//check if next content isn't blank
		    	var lNextVal = lContent.next('.content').find(".content_search").val();
		    	if(typeof(lNextVal) == 'undefined'){
		    		lClone.insertAfter(lContent);
		            jQuery(".content_search").keyup( function() { Flow.cmsSearch.results(); });
		    	}
	    	}
    	}
    },
    searchDictionary : function(aId) {
    	var lElm = jQuery("#"+aId);
    	if( lElm.data('ui-autocomplete') != undefined){
    		lElm.autocomplete("destroy");
    	}
    	var lParts = aId.split("_");
    	var lCategory = lParts[0];
    	var lNum = lParts[1];
    	var lLanguage = lParts[2];
    	
    	var lSuggs = [];
    	lElm.autocomplete({
            source: function(req, add){
            	var lChosen = [];
            	jQuery(".content_search").each( function() {
            		var lVal = jQuery(this).val();
            		
            		if(lVal != '') {
            			lChosen.push( lVal );
            		}
            	});
            	
            	var lParams = { 
            		act : 'job-cms.dictionary',
            		category: lCategory, 
            		lang: lLanguage, 
            		val: lElm.val(),
            		chosen: lChosen,
	                src: jQuery("input[name='src']").val()
            	};
                jQuery.post('index.php', lParams, function(aData) {
                    lSuggs = [];
                	jQuery.each(aData, function(i, aVal){                              
                		lSuggs.push( { 
                			id: aVal.content_id, 
                			pid: aVal.parent_id, 
                			label: aVal.content, 
                			value: aVal.content, 
                			langvar: aVal.language, 
                			version: aVal.version 
                		} );
                    });
                	add(lSuggs);
                }, 'json');
            },
            minLength: 2,
            select: function(event, ui) {
            	event.preventDefault();
            	
            	var lParams = { 
            		act : 'job-cms.translations', 
            		id: ui.item.id, 
                    jobid: jQuery("input[name='jobid']").val(),
                    src: jQuery("input[name='src']").val(),
                    job : JSON.stringify(lJobData)
                };
                jQuery.post('index.php', lParams, function(aData) {
                	jQuery.each(aData, function(i, aVal){
                		i = i.toLowerCase(); 
                		var lId = lCategory+"_"+lNum+"_"+i;
                		
                        //grab id and present language in consolidated area
                    	jQuery("input[name='content["+lId+"_cid]']").val(aVal.content_id); //cid
                    	jQuery("input[name='content["+lId+"_pid]']").val(aVal.parent_id);//pid
                    	jQuery("input[name='content["+lId+"_ver]']").val(aVal.version);//ver
                    	
                    	//in array and consolidated item needs to look in there
                    	Flow.cmsContent.text[lId] = aVal.content;

                		//replace text without tags
                    	var lNewVal = Flow.cmsContent.text[lId].replace(/(<([^>]+)>)/ig,"");
                    	jQuery("#"+lId).val(lNewVal);
                    	
                    	Flow.cmsContent.duplicateItem(lId);
                	});
                }, 'json');
            },
            open: function(event, ui) {
            	jQuery(".ui-autocomplete").css("z-index", 1000);
            }
       });
    },
    searchContent : function(aArr, aParams) {
    	var lRes = [];

	    jQuery.each(aArr, function(lKey, lVal) {
	      var lVerdict = true;
	      jQuery.each(aParams, function (lParamKey, lParamVal) {
	        if (lVal[lParamKey] == undefined || lVal[lParamKey] != lParamVal){
	          lVerdict = false;
	          return false;
	        }
	      });

	      if(lVerdict) {
	    	lRes.push(lKey);
	      }
	    });

	    return lRes;
    }
};

Flow.cmsApl = {
	states : [],
	prepareDialog : function(aTask, aPart, aPrefix, aStateId, aSubloopId, aCategoryJson, aJid, aSrc) {
	    var lLangs = (aPart == 'master') ? 'MA' : aPrefix;
		//get all content/master data based on categories for each country
		var lParams = { 
    		act : 'job-cms-apl.getdata',
    		categories: aCategoryJson,
    		language: lLangs,
    		stateid: aStateId,
    		subloop: aSubloopId,
    		task: aTask,
            jobid: aJid,
            src: aSrc
    	};
        jQuery.post('index.php', lParams, function(aData) {
            var lCombos = aData['combos'];
            if(aTask.indexOf('add') < 0) {
            	lCombos.push({'category': lang['lib.summary'], 'layout': 'memo', 'amount': 1}); //build summary
            }
            var lArea = (aTask.indexOf('approve') > -1 || aTask.indexOf('check') > -1) ? '#arAreaList' : '#addAreaList';
            Flow.cmsContent.prepareNav(lCombos, lArea, 1);
            
            Flow.cmsApl.states = aData['data'];
            Flow.cmsApl.prepareSections(aTask, aData['language'], lCombos, Flow.cmsApl.states, aSrc, 1);
            
            jQuery("#state_id").val(aStateId);
            jQuery("#sub_loop_id").val(aSubloopId);
            jQuery("#jobid").val(aJid);
            jQuery("#task").val(aTask);
            
            var lId = (aTask.indexOf('approve') > -1 || aTask.indexOf('check') > -1) ? '#approveReviewDialogBtn' : '#addDialogBtn';
            jQuery(lId).click();
        }, 'json');
	},
	prepareSections : function(aTask, aLang, aCombos, aData, aSrc, aActiveNum) {
		//getsection html
		var lParams = { 
			act : 'job-cms-apl.sections', 
			combos: JSON.stringify(aCombos), 
			content: JSON.stringify(aData),
			language: JSON.stringify(aLang),
			task: aTask,
			src: aSrc,
			active: aActiveNum
		};
        jQuery.post('index.php', lParams, function(aData) {
            var lClass = (aTask.indexOf('approve') > -1 || aTask.indexOf('check') > -1) ? '#approveReviewContArea' : '#addContArea';
            jQuery(lClass).html(aData);
            
            var lElm = jQuery(".content_section:not(.dn)");
			var lCategory = lElm.attr("data-category").split(" ");
            jQuery(".category_title").html(lCategories[lCategory[0]]['name']+' '+lCategory[1]);

            if(lElm.prev('.content_section').length  < 1){ jQuery(".contprev").addClass("disabled"); } else { jQuery(".contprev").removeClass("disabled"); }
    		if(lElm.next('.content_section').length  < 1){ jQuery(".contnext").addClass("disabled"); } else { jQuery(".contnext").removeClass("disabled"); }
    		
    		var lArea = (aTask.indexOf('approve') > -1 || aTask.indexOf('check') > -1) ? '#arAreaList' : '#addAreaList';
    		jQuery(lArea+' li[class^="section"]').click( function() {
    			var lClass = jQuery(this).attr('class').split(" ");
    			var lSection = lClass[0];
    			
    			Flow.cmsContent.show(null, lArea, lSection);
    		});
    		
    		jQuery(lClass).scroll(function(){
    		  var lAplContainer = jQuery(lClass).find(".content_section:visible .approvalBtnContainer");
    		  if(lAplContainer.length > 0) {
	    		  var lTop = (jQuery(lClass).scrollTop() > 0) ? '-'+jQuery(lClass).scrollTop() : jQuery(lClass).scrollTop();
	    		  lAplContainer.css("bottom", lTop+"px");
    		  }
	    	});
    		
    		if(aTask.indexOf('approve') > -1){
    			Flow.cmsApl.buildSummary();
    		}
    		
    		var lApproved = false;
    		jQuery(".content_section").each( function() {
    			var lSelf = jQuery(this);
    			var lId = lSelf.attr("id");
    			var lStatus = lSelf.attr("data-status");
    			
    			var lSpan = '';
    			if(lStatus == 'approved') {
    				lSpan = "<span class='badge badge-success pull-right'>&nbsp;</span>";
    				lApproved = true;
    			} else if(lStatus == 'draft') {
    				lSpan = "<span class='badge badge-warning pull-right'>&nbsp;</span>";
    			} else if(lStatus == 'incorrect') {
    				lSpan = "<span class='badge badge-danger pull-right'>&nbsp;</span>";
    			}
    			
    			jQuery("."+lId).find("span.badge").remove();
    			jQuery("."+lId).append(lSpan);
    			if(lStatus == 'approved') {
    			  jQuery("."+lId).removeClass("approved draft incorrect").addClass(lStatus);
    			}
    		});
    		
    		if(lApproved) {
    			jQuery("button.contall").click();
    		} else {
    			jQuery("button.contall").hide();
    		}
    	}, 'json');
	},
	showAll : function() {
		var lApproved = jQuery("li[class^='section'][class~='approved']");
		var lToggle = jQuery("button.contall");
		
		if(lApproved !== undefined) {
			//hide/show all approved content
			if(lApproved.attr("style") == undefined) {
				lApproved.attr("style", "display:none;");
				lToggle.html( lang['apl.phrase.window.showall']);
			} else {
				lApproved.removeAttr("style");
				lToggle.html( lang['apl.phrase.window.hideall']);
			}
			
			//realign badges navigation
			jQuery(".list-group").each( function() {
				var lAmount = jQuery(this).find("li:not([style])").length;
				jQuery(this).closest(".panel").find(".panel-title span.badge").html(lAmount);
			});
			
			//if approved is shown then move onto non-approved content
			var lActive = jQuery("li.list-group-item.active");
			while(lActive.attr("style") !== undefined) {
				lActive.removeClass("active");
				var lCurrent = lActive.attr("class").split(' ')[0];
				var lNum = parseInt(lCurrent.match(/\d+/)) + 1;
				var lSection = jQuery("#section"+lNum); //next section to show
				
				if(lSection.length < 1) break; //if next section doesn't exist
				jQuery(".contnext").click();
				lActive = jQuery("li.list-group-item.active");
			}
		}
	},
	showBtn : function(aArgs) {
		var lSuggBox = jQuery(".content_section:visible");
		
		var lFind = [];
		var lTypes = aArgs.toString().split(",");
		for(var i=0; i < lTypes.length; i++){
			lFind.push(".approval_buttons.btn-success."+lTypes[i]);
		}
		
		var lSelf = lSuggBox.find("*[id^='suggestion']");
		var lEditor = lSelf.attr('data-btns');
		if (typeof lEditor !== 'undefined' && lEditor !== false) {
			var lTinyCont = jQuery( tinyMCE.get(lSelf.attr("id")).getContent() ).unwrap();
			var lSuggestion = jQuery("<div />").append(lTinyCont.clone()).text();
		} else {
			var lSuggestion = lSelf.val();
		}
		
		if(lSuggestion == ''){
			lSuggBox.find(lFind.join(", ")).hide();
		} else {
			lSuggBox.find(lFind.join(", ")).show();
		}
	},
	soft : function(aThis, aAplState, aContentId) {
		var lComment = jQuery("#comment"+aContentId).val();
		var lTask = jQuery("#task").val().indexOf("master");
		
		//store comment, suggestion, state in lContentData
		jQuery("input[id^='suggestion'],textarea[id^='suggestion']", "#"+aContentId).each( function() {	//gather all suggestions content
			var lSelf = jQuery(this);
			var lId = lSelf.attr("id");
			var lContentId = lId.replace("suggestion","");
			var lContent = jQuery("#content"+lContentId).val();
			
			if(parseInt(lContentId) > 0){
				var lSuggestion = '';
				var lEditor = lSelf.attr('data-btns');
				if (typeof lEditor !== 'undefined' && lEditor !== false) {
					var lTinyCont = jQuery( tinyMCE.get(lId).getContent() ).unwrap();
					lSuggestion = jQuery("<div />").append(lTinyCont.clone()).html();
				} else {
					lSuggestion = lSelf.val();
				}
				
				var lParams = { 'content_id' : lContentId };
				var lExists = Flow.cmsContent.searchContent(Flow.cmsApl.states, lParams);
				if(lExists.length > 0){
					var lTempData = [];
					jQuery.each(Flow.cmsApl.states, function(lIdx, lObj){
						if(jQuery.inArray(lIdx, lExists) > -1) {
							lObj.content = lContent;
							lObj.suggestion = lSuggestion;
							lObj.comment = lComment;
							lObj.apl_state = parseInt(aAplState);
						}
						if((lTask < 0 && lObj.language != 'MA') || (lTask > -1 && lObj.language == 'MA') && lObj.content_id > 0) {
							lTempData.push(lObj);
						}
					});
					Flow.cmsApl.states = lTempData;
				}
			}
		});

		//add state to summary section
		Flow.cmsApl.buildSummary();
		
		jQuery(aThis).closest(".approvalBtnContainer").hide();
		if(jQuery(".approvalBtnContainer:not([style])").length < 1) {
			jQuery('.js-btn-step[data-orientation=next]').removeAttr("disabled");
		}
		
        var lElm = jQuery(".content_section:not(.dn)");
        if(lElm.next('.content_section').length  > 0){
        	jQuery('#approve_review .contnext').click();
        }
	},
	hard : function(aThis, aAplState, aContentId) {
		//store comment, suggestion, state in lContentData
		var lStatus = (aAplState != '1') ? 'approved' : 'draft';
		var lTask = jQuery("#task").val().indexOf("master");
		var lComment = jQuery("#comment"+aContentId).val();

		jQuery("input[id^='suggestion'],textarea[id^='suggestion']", "#"+aContentId).each( function() {	//gather all suggestions content
			var lSelf = jQuery(this);
			var lId = lSelf.attr("id");
			var lContentId = lId.replace("suggestion","");

			if(parseInt(lContentId) > 0){
				var lEditor = lSelf.attr('data-btns');
				if (typeof lEditor !== 'undefined' && lEditor !== false) {
					var lTinyCont = jQuery( tinyMCE.get(lId).getContent() ).unwrap();
					var lSuggestion = jQuery("<div />").append(lTinyCont.clone()).html();
				} else {
					var lSuggestion = lSelf.val();
				}
				var lContent = (aAplState == '2') ? lSuggestion : jQuery("#content"+lContentId).val();
				
				var lParams = { 'content_id' : lContentId };
				var lExists = Flow.cmsContent.searchContent(Flow.cmsApl.states, lParams);
				if(lExists.length > 0){
		    		var lTempData = [];
					jQuery.each(Flow.cmsApl.states, function(lIdx, lObj){
						if(jQuery.inArray(lIdx, lExists) > -1) {
							lObj.content = lContent;
							lObj.status = lStatus;
							lObj.apl_state = parseInt(aAplState);
							lObj.suggestion = lSuggestion;
							lObj.comment = lComment;
						}
						if((lTask < 0 && lObj.language != 'MA') || (lTask > -1 && lObj.language == 'MA') && lObj.content_id > 0) {
							lTempData.push(lObj);
						}
					});
					Flow.cmsApl.states = lTempData;
				}
			}
		});
			
		jQuery(aThis).closest(".approvalBtnContainer").hide();
		if(jQuery(".approvalBtnContainer:not([style])").length < 1) {
			jQuery('.js-btn-step[data-orientation=next]').removeAttr("disabled");
		}
		
        var lElm = jQuery(".content_section:not(.dn)");
        if(lElm.next('.content_section').length  > 0){
        	jQuery('#approve_review .contnext').click();
        }
	},
	complete : function() {
		var lTask = jQuery('#task').val();
		
		//check if on summary section
		var lSummary = jQuery(".content_section").last().attr("id");
		var lShown = jQuery(".content_section:not(.dn)").attr("id");
		if(lShown !== lSummary) {
			var lSummarySection = lSummary.match(/\d+/);
			jQuery("#collapse"+lSummarySection).click();
			jQuery("."+lSummary).click();
		}
		
		//set overall state to approved or rejected
		var lParams = { 'apl_state' : 1 };
		var lExists = Flow.cmsContent.searchContent(Flow.cmsApl.states, lParams);
		var lOverallState = (lExists.length > 0) ? 1 : 3;

		var lStateId = jQuery("#state_id").val();
		var lSubloopId = jQuery("#sub_loop_id").val();
		var lOverallComment = jQuery("#overallcomment").val();
		
		//gather lContentData and push to store data
		var lAct = (lTask.indexOf('approve') > -1) ? 'job-cms-apl.setapl' : 'job-cms-apl.setcontent';
		var lParams = {
    		act : lAct,
    		content: JSON.stringify(Flow.cmsApl.states),
    		stateid: lStateId,
    		subloop: lSubloopId,
    		task: lTask,
            jobid: jQuery("#jobid").val()
    	};
        jQuery.post('index.php', lParams, function(aData) {
        	//complete state for user
            Flow.apl.postStatusAndReload(lStateId, lSubloopId, lOverallState, lOverallComment);
            if(lTask.indexOf('check') > -1 && lOverallState == 1 && parseInt(aData) > 1) {
            	Flow.apl.restartSubLoop(lStateId, lSubloopId, 1);
            }
        }, 'json');
	},
	buildSummary : function() {
		var lHtm = '';
		var lTask = jQuery("#task").val().indexOf("master");
		jQuery.each(Flow.cmsApl.states, function(lIdx, lObj){
			var lLabel = (lObj.apl_state == 1) ? 'label-danger' : 'label-default';
			lLabel = (lObj.apl_state == 2 || lObj.apl_state == 3) ? 'label-success' : lLabel;

			if((lTask < 0 && lObj.language != 'MA') || (lTask > -1 && lObj.language == 'MA')) {
				lHtm += '<div class="summary">';
				lHtm += '<div class="label '+lLabel+' part_title" data-toggle="collapse" data-target="#summary'+lObj.content_id+'">';
				lHtm += lCategories[ lObj.category ]['name'];
				lHtm += '</div>';
				
				lHtm += '<div id="summary'+lObj.content_id+'" class="pull-left inp w100p collapse">';
				lHtm += '<b>Content:</b> '+lObj.content+'<br/>';
				lHtm += '<b>Suggestion:</b> '+lObj.suggestion+'<br/>';
				lHtm += '<b>Comment:</b> '+lObj.comment+'<br/>';
				lHtm += '</div>';
				lHtm += '</div>';
			}
		});
		
		jQuery("#summaryArea .summary").remove();
		jQuery(lHtm).insertBefore("#summaryArea .overallcomment");
	}
};

Flow.cmsAdd = {
	add : [],
	action : function(aDivId) {
		var lDiv = jQuery('#'+aDivId);
		var lParentId = lDiv.find("input[name$='_pid]']").val();
		var lLang = lDiv.find("input[name$='_lang]']").val();
		var lContentId = lDiv.find("input[name$='_cid]']").val();
		var lVersion = lDiv.find("input[name$='_ver]']").val();
		var lCategory = lDiv.find("input[name$='_category]']").val();
		
		var lContent = jQuery("#content"+aDivId);
		var lEditor = lContent.attr('data-btns');
		if (typeof lEditor !== 'undefined' && lEditor !== false) {
			var lTinyCont = jQuery( tinyMCE.get("content"+aDivId).getContent() ).unwrap();
			var lContentVal = jQuery("<div />").append(lTinyCont.clone()).html();
		} else {
			var lContentVal = lContent.val();
		}
		
		var lParams = { 'parent_id' : lParentId, 'language': lLang };
		var lExists = Flow.cmsContent.searchContent(Flow.cmsAdd.add, lParams);
		if(lExists.length > 0){
    		var lTempData = [];
			jQuery.each(Flow.cmsAdd.add, function(lIdx, lObj){
				if(jQuery.inArray(lIdx, lExists) > -1) {
					lObj.content = lContentVal;
				}
				lTempData.push(lObj);
			});
			this.add = lTempData;
		} else {
		    var lContent = {
		        'content_id': lContentId,
		        'parent_id': lParentId,
		      	'content': lContentVal, 
		      	'category': lCategory,
		        'language': lLang,
		        'version': lVersion,
		      	'position': '',
		        'group': lDiv.find("input[name$='_group]']").val(),
		      	'type': lDiv.find("input[name$='_type]']").val(),
		        'metadata': '',
		        'layout': lDiv.find("input[name$='_layout]']").val(),
		        'status': 'draft'
		    };
		    Flow.cmsAdd.add.push(lContent);
		}
		
		lDiv.find(".approvalBtnContainer").hide();
		if(jQuery(".approvalBtnContainer:not([style])").length < 1) {
			jQuery('.js-btn-step[data-orientation=next]').removeAttr("disabled");
		}
		
        var lElm = jQuery(".content_section:not(.dn)");
        if(lElm.next('.content_section').length  > 0){
        	jQuery('#add_translation .contnext').click();
        }
	},
	complete : function() {
		var lTask = jQuery('#task').val();
		var lStateId = jQuery("#state_id").val();
		var lSubloopId = jQuery("#sub_loop_id").val();
		
		var lParams = { 
			act : 'job-cms-apl.setcontent',
			content: JSON.stringify(Flow.cmsAdd.add),
    		stateid: lStateId,
    		subloop: lSubloopId,
			task: lTask,
	        jobid: jQuery("#jobid").val()
		};
	    jQuery.post('index.php', lParams, function(aData) {
	    	//complete state for user
	        Flow.apl.postStatusAndReload(lStateId, lSubloopId, 3, '');
	    }, 'json');
	},
};

Flow.cmsProduct = {
	hasChanges : function(lClientKey) {
		var lHtml = '<p>'+lang['phrase.product.recentchanges']+' <b>'+lClientKey+'</b><br/><br/>'+lang['phrase.job.update.question']+'</p>';
		
        jQuery(".productChanges").html(lHtml);
        jQuery("#productChange").modal('show');
	},
	update : function() {
		var lLanguages = {'MA': 'Master'};
		var lUpdate = jQuery(".phrase_update").length;
		var lNew = jQuery(".phrase_new").length;
		
		if(lNew == 0 && lUpdate == 0){
			jQuery("#referenceUpdate").val("no");
        	jQuery("#save").click();
		} else {
			jQuery("#translation option").each( function(){
				var lKey = jQuery(this).val();
				var lVal = jQuery(this).text();
				
				if(lKey != ""){
					lLanguages[lKey] = lVal;
				}
			});
			
			var lHtml = '<p>'+lang['phrase.job.changes']+':</p>';
			lHtml+= '<ul style="padding: 0;">';
			
			var lNewCont = new Array();
			var lUpdCont = new Array();
			jQuery(".phrase_new, .phrase_update").each( function() {
				var lSelf = jQuery(this);
				var lLang = lSelf.prop("id");
				lLang = lLang.split("_");
				var lCategory = lLang[0];
				lLang = lLanguages[lLang[2].toUpperCase()];
				
				if(lSelf.hasClass("phrase_update")) {
					lUpdCont.push('<li><i>'+lCategory+' ['+lLang+']:</i> '+lSelf.val()+'</li>');
				} else {
					lNewCont.push('<li><i>'+lCategory+' ['+lLang+']:</i> '+lSelf.val()+'</li>');
				}
			});
			
			if(lNewCont.length > 0) {
				lHtml+= '<li><b>'+lang['lib.new']+'</b></li><li>&nbsp;</li>';
				lHtml+= lNewCont.join("");
				lHtml+= '<li>&nbsp;</li>';
			}
			if(lUpdCont.length > 0) {
				lHtml+= '<li><b>'+lang['lib.update']+'</b></li><li>&nbsp;</li>';
				lHtml+= lUpdCont.join("");
			}
			lHtml+= '</ul><p>'+lang['phrase.reljob.update.question']+'</p>';
			
			jQuery("#updateProduct").html(lHtml);
			jQuery("#productUpdate").modal('show');
		}
		
		return false;
	}
};

Flow.chili = {
	buildArtwork : function() {
		//get url
		var lParams = {
            act : 'job-cms.geteditorurl',
            jobid: jQuery("input[name='jobid']").val(),
            src: jQuery("input[name='src']").val(),
            template : lJobData['chili_template_name']
        };
    	jQuery("#pag_progress").show();

        jQuery.post('index.php', lParams, function(aData) {
        	jQuery("#chiliEditor").attr("src", aData['editorURL']+"&d=matthewsbrandsolutions.co.uk").removeClass('dn');
			jQuery("#templateId").val(aData['template_id']);
        }, 'json');
		
		if(jQuery("#save_dialog").hasClass('ui-dialog-content')) {
			jQuery('#save_dialog').dialog('destroy');
		}
		var h = jQuery(window).height() - 50;
		var w = jQuery(window).width() - 50;
		var lPublish = lang['lib.publish'];
		var lCancel = lang['lib.cancel'];
		jQuery("#save_dialog").dialog({
			autoOpen: false, width: w, height: h, modal: true, resizable:false,
			buttons: {
				lPublish : function() { editor.ExecuteFunction("document","Save"); }, 
				lCancel : function() { jQuery(this).dialog("close"); return false; } 
			}
		});
		jQuery("#save_dialog").dialog("open"); //show dialog
	},
	addContent : function() {
		var lParams = {
            act : 'job-cms.getvariables',
            jobid: jQuery("input[name='jobid']").val(),
            src: jQuery("input[name='src']").val(),
            job : JSON.stringify(lJobData)
        };
		
        jQuery.post('index.php', lParams, function(aData) {
			for(variableId in aData){
				var val = aData[variableId];//convert(aData[variableId]);
				if(val != null) {
					if(jq.isNumeric(val)){
						val = (jq("#"+val).text() == '') ? val : jq("#"+val).text();
					}
					
					if(val.indexOf("<TextFlow") == -1){
						editor.SetProperty("document.variables["+variableId+"]","value",val);
					}
				}
			}
			jQuery("#pag_progress").hide();
        }, 'json');
	}
};

/*----------------------------IFRAME EDITOR----------------------------*/
function GetEditor() {
	if (document.getElementsByTagName('iframe').length > 0) {
		var lSrc = document.getElementById('chiliEditor').src;
		if (lSrc.indexOf('cpapp') > -1) {
			frameWindow = document.getElementById('chiliEditor').contentWindow;
			try{
				frameWindow.GetEditor(EditorLoaded);
			} catch(err){ /*console.log(err);*/ }
		}
	}
}

function EditorLoaded(jsInterface) {
	editor = frameWindow.editorObject;
}

function OnEditorEvent(type,targetID) {
	switch (type) {
		case "DocumentSaved":
			var lParams = {
				act : 'job-cms.generatepdf',
				jobid: jQuery("input[name='jobid']").val(),
				src: jQuery("input[name='src']").val(),
				template: jQuery("#templateId").val()
			};
			
			jQuery.post('index.php', lParams, function(aData) {}, 'json');
			jQuery("#save").click();
			break;
		case "DocumentFullyLoaded":
			Flow.chili.addContent();
			break;
	}
}

function convert(str) {
	if(str !== undefined){
		str = str.replace(/&amp;/g, "&");
		str = str.replace(/&gt;/g, ">");
		str = str.replace(/$lt;/g, "<");
		str = str.replace(/&quot;/g, '"');
		str = str.replace(/&#039;/g, "'");
	}
	
	return str;
}