//document.domain = "matthewsbrandsolutions.co.uk";
var frameWindow, editor;

Flow.cmsDialog = {
    item : function() { //show item dialog
        var lDiv = jQuery('<div>', {id: 'item_dlg'});
        var lParams = {
        		jobid: jQuery("input[name='jobid']").val(), 
        		src: jQuery("input[name='src']").val()
        };
        var lProgress = jQuery("#pag_progress");
        lProgress.show();
        jQuery.post('index.php?act=job-cms.getitemdlg', lParams, function(aData) {
            lDiv.html(aData);
	        jQuery(lDiv).dialog({
	        	create: function() {
	                var lDialog = jQuery(this);
	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
	                	lDialog.dialog('destroy').remove();
	                });
	                Flow.cmsTemplate.select(); //if templateid field has something selected the template will pre-populate
	        	},
	            title : 'Add Content Items',
	            modal : true,
	            height: jQuery(window).height() - 50,
	            width: 600,
	            buttons : {
	                'SaveNewTemplate' : {
	                	id : 'saveNewTemplate',
	                	text: 'Save New Template',
	                	click : function() {
		                	var lItems = Flow.cmsTemplate.getItems();
		                	if(Object.keys(lItems).length > 0)
		                		Flow.cmsDialog.templateSave(lItems);
		                }
	                },
	                'UpdateTemplate' : {
	                	id : 'updateTemplate',
	                	text : 'Update Template',
	                	click : function() {
		                	var lItems = Flow.cmsTemplate.getItems();
		                	
		                	if(Object.keys(lItems).length > 0){
		    	            	var lName = jQuery("#templates").val();
		    	            	if(lName != '')
		    	            		Flow.cmsTemplate.save(lName, lItems, 'update'); //save template and set items on page
		                	}
		                }
	                },
	                "Use Selection" : function() {
	                	var lCombos = Flow.cmsTemplate.getItems();
	                	if(Object.keys(lCombos).length > 0) {
	                	  Flow.cmsForm.setItems(lCombos); //just combos
	                	  
	                	  jQuery(this).dialog('destroy').remove();
	                	}

	                },
	                Close : function() {
	                    jQuery(this).dialog('destroy').remove();
	                }
	            }
	        });
	        lProgress.hide();
	        
	        jQuery('#saveNewTemplate').attr("disabled", true).addClass("ui-state-disabled");
	        jQuery('#updateTemplate').attr("disabled", true).addClass("ui-state-disabled");
        });
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
	    	
	    	jQuery(lNewItem).insertAfter(lItem);
	
			jQuery('#saveNewTemplate').removeAttr("disabled").removeClass("ui-state-disabled");
			var lTemplate = jQuery("#templates").val();
	    	if(lTemplate != ''){
	    		jQuery('#updateTemplate').removeAttr("disabled").removeClass("ui-state-disabled");
	    	}
    	}
    },
    removeItem : function(aThis) { //remove row from item dialog
    	var lItem = jQuery(aThis).closest(".content_item");
    	
    	var lDiv = jQuery('<div>', {id: 'remove_item_dlg'});
        lDiv.html("<h1>Are you sure you wish to remove this item?</h1>");
        jQuery(lDiv).dialog({
        	create: function() {
                var lDialog = jQuery(this);
                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
                	lDialog.dialog('destroy').remove();
                });
        	},
            title : 'Content Item Removal',
            modal : true,
            width : 320,
            buttons : {
                Yes : function() {
                	lItem.remove();
                    jQuery(this).dialog('destroy').remove();
                },
                No : function() {
                    jQuery(this).dialog('destroy').remove();
                }
            }
        });
    },
    search : function(aId) { //showing the search dialog
		var lContent = '';
		var lLabel = 'Insert Content';
		
    	if(aId !== 0){ //CONTENT SEARCH
    		aId = aId.replace('search_', '');
    		var lContent = jQuery("#"+aId).val();
    		var lLabel = 'Update Content';
    	}

        var lDiv = jQuery('<div>', {id: 'search_dlg'});
    	var lParams = {
            act : 'job-cms.getsearchdlg',
            id: aId,
            content: lContent,
            meta: {},
            lang: 'MA',
            jobid: jQuery("input[name='jobid']").val(),
            src: jQuery("input[name='src']").val(),
            job : JSON.stringify(lJobData)
            
        };
        var lProgress = jQuery("#pag_progress");
        lProgress.show();
        jQuery.post('index.php', lParams, function(aData) {
        	lDiv.html(aData);
	        jQuery(lDiv).dialog({
	        	create: function() {
	                var lDialog = jQuery(this);
	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
	                	lDialog.dialog('destroy').remove();
	                });
	        	},
	            title : 'Select Content',
	            modal : true,
	            width : 700,
	            buttons : [
	              {
	                text: lLabel,
	                click : function() {
	                	var lDialog = jQuery(this);
	                	
	                	var lIds = [];
	                	jQuery(".suggestion.active").each( function() {
	                		var lId = jQuery(this).attr("id");
	                		lIds.push(lId);
	                	});
	                	var lParams = {
                            act : 'job-cms.getresult',
                            ids: JSON.stringify(lIds),
                            jobid: jQuery("input[name='jobid']").val(),
                            src: jQuery("input[name='src']").val(),
                            job : JSON.stringify(lJobData)
                        };
                        jQuery.post('index.php', lParams, function(aData) {
                        	var lItem = jQuery("#item_id").val();
                        	if(lItem == '0'){ //Global Search
                        		Flow.cmsForm.setItems(aData['combos'], aData['data'], true);
                        	} else {
                        		Flow.cmsForm.replaceContent(lItem, aData['data']);
                        	}
		                    lDialog.dialog('destroy').remove();
                        }, 'json');
	                }
	              },
	              {
	                text : "Close",
	                click : function() {
	                    jQuery(this).dialog('destroy').remove();
	                }
	              }
	            ]
	        });
          lProgress.hide();
        });
    },
    templateSave : function(aItems) {
    	//save template dialog
		var lNameDiv = jQuery('<div>', {id: 'name_dlg'});
		var lData = '<p>Please enter a name for the template:</p><b>Name</b>&nbsp;';
		lData += '<input type="text" class="text ui-widget-content ui-corner-all" value="" id="template_name" name="template_name" />';
		lNameDiv.html(lData);
		jQuery(lNameDiv).dialog({
			create: function() {
                var lDialog = jQuery(this);
                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
                	lDialog.dialog('destroy').remove();
                });
        	},
			title : 'Template Name',
            modal : true,
            height: 200,
            width: 300,
            buttons : {
	            Okay : function() {
	            	var lName = jQuery("#template_name").val();
	            	
	            	if(lName != ''){
	            		jQuery("#template_name").removeAttr("style");
	            		Flow.cmsTemplate.save(lName, aItems, 'new'); //save template and set items on page
	                	jQuery(this).dialog('destroy').remove();
	            	} else {
	            		jQuery("#template_name").attr("style", "border: 1px solid #FF0000;");
	            	}
                }
            }
		});
    }
};

Flow.cmsTemplate = {	
    select : function() { //when selecting a template gather data from db and auto fill dialog
    	var lTemplate = jQuery("#templates").val();
    	
    	if(lTemplate != ''){
    		jQuery('#saveNewTemplate').removeAttr("disabled").removeClass("ui-state-disabled");
	        jQuery('#updateTemplate').removeAttr("disabled").removeClass("ui-state-disabled");
	        
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
    			jQuery(".content_item").find("select[name='cat']").attr("id", "category1"); //reset category id
    			jQuery(".content_item").find("select[name='layout']").attr("id", "layout1"); //reset layout id
    			jQuery(".content_item").find("input[name='amount']").attr("id", "amount1"); //reset amount id
    			
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
    		}, 'json');
    	} else {
	        jQuery('#saveNewTemplate').attr("disabled", true).addClass("ui-state-disabled");
	        jQuery('#updateTemplate').attr("disabled", true).addClass("ui-state-disabled");
	        
	        jQuery(".content_item").not(":last").remove();
			
			var lRow = jQuery(".removeBtn:last").parent().parent();
			lRow.find(".cms_cat").val('');
			lRow.find(".cms_amt").val(1);

			var lLayoutSel = lRow.find(".cms_lay");
 			lLayoutSel.find('option[value!=""]').remove();
        	for(lKey in lLayoutOptions) {
        	  var lOption = jQuery("<option></option>").attr("value", lKey).text(lLayoutOptions[lKey]);
        	  lLayoutSel.append( lOption );
        	}
    	}
    },
    getItems : function() { //gather items from item dialog to use
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
    save : function(aName, aItems, aTyp) { //saving item dialog selection as a template
    	var lParams = {
            act : 'job-cms.settemplate',
            name: aName,
            data: JSON.stringify(aItems),
            type: aTyp,
            src: jQuery("input[name='src']").val()
        };
        jQuery.post('index.php', lParams, function(aData) { 
        	var lDiv = jQuery('<div>', {id: 'confirm_dlg'});
    		var lData = '<p>Template: '+aName+' has been ';
    		lData += (aTyp == 'new') ? 'created ' : 'updated ';
    		lData += 'successfully</p>';
    		lDiv.html(lData);
    		jQuery(lDiv).dialog({
    			create: function() {
	                var lDialog = jQuery(this);
	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
	                	lDialog.dialog('destroy').remove();
	                });
	        	},
    			title : 'Template confirmation',
	            modal : true,
	            height: 200,
	            width: 300,
	            buttons : {
	                Close : function() {
	                	var lTemplates = jQuery("#templates");
	                	if(aTyp == 'new') {
	                		var lOption = jQuery("<option></option>").attr("value",aName).text(aName);
	                		lTemplates.append( lOption );
	                	}
	                	lTemplates.val(aName).change();
	                	
	                    jQuery(this).dialog('destroy').remove();
	                }
	            }
    		});
        });
    }
};

Flow.cmsForm = {
	showTranslation : function() {
    	var lLang = jQuery("#translation").val().toLowerCase();
    	
    	jQuery("div[class^='translation_']").addClass("dn");
    	jQuery(".translation_"+lLang).removeClass("dn");
    },
    highlightTranslations : function() {
    	var lMissing = [];
    	
    	jQuery(".no_content").each( function() {
    		var lId = jQuery(this).attr("id");
    		lId = lId.split("_");
    		if(lId[2] !== "ma") {
	    		var lLanguage = jQuery("#translation").find("option[value='"+lId[2].toUpperCase()+"']").text();
	    		lLanguage = "<li><b>"+lLanguage+"</b></li>";
	
	    		if(jQuery.inArray(lLanguage, lMissing) == -1){
	    			lMissing.push(lLanguage);
	    		}
	    	}
    	});

    	if(lMissing.size() > 0){
	    	var lDiv = jQuery('<div>', {id: 'missing_dlg'});
	    	var lHtml = '<p>The following languages are missing from the content on this job:</p><ol style="list-style-type: circle;">';
	    	lHtml+= lMissing.join("");
	    	lHtml+= '</ol>';
			lDiv.html(lHtml);
			
			/*jQuery(lDiv).dialog({
				create: function() {
	                var lDialog = jQuery(this);
	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
	                	lDialog.dialog('destroy').remove();
	                });
	        	},
				title : 'Missing Translations',
	            modal : true,
	            height: 250,
	            width: 400,
	            buttons : {
	              "Send for Translation" : function() {
			        jQuery(this).dialog('destroy').remove();
			      },
	              Close : function() {
	                jQuery(this).dialog('destroy').remove();
	              }
	            }
			});*/
    	} else {
    		jQuery("#build_artwork").removeClass("dn");
    	}
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
          var lProgress = jQuery("#pag_progress");
          lProgress.show();
	    	
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
	        	Flow.cmsForm.highlightTranslations();
	        	lProgress.hide();
	        }, 'json');
    	}
    },
    removeItem : function(aId, aClass) { //remove item from layout (general layout/multiple)
        var lDiv = jQuery('<div>', {id: 'remove_dlg'});
        lDiv.html("<h1>Are you sure you wish to remove this content from the job ?</h1>");
        jQuery(lDiv).dialog({
        	create: function() {
                var lDialog = jQuery(this);
                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
                	lDialog.dialog('destroy').remove();
                });
        	},
            title : 'Job Content Removal',
            modal : true,
            width : 320,
            buttons : {
                Yes : function() {
                	var lItem = jQuery("#"+aId).closest("div."+aClass);
                	var lCategory = aId.split("_")[1];
                	var lSection = lItem.parent(".section");
                	lItem.remove();
                	
                	if(parseInt( lSection.find("."+aClass).length ) < 1 && aClass == 'item'){ //only for item and not multiple selection
                		lSection.remove();
                	}
                	Flow.cmsLayout.consolidateItem(lCategory+"_1_ma");
                	
                    jQuery(this).dialog('destroy').remove();
                },
                No : function() {
                    jQuery(this).dialog('destroy').remove();
                }
            }
        });
        
    },
    variableContent : function(aId, aTyp) { //variable layout to add variable fields or consolidation
    	var lElm = jQuery("#"+aId);
        var lCategory = aId.replace(/\d+/g, '');
    	var lText = jQuery("#"+aId+" option:selected").text();
    	var lVariables = 0;
    	var lRegexp = /{variable/g;

    	while ((lMatch = lRegexp.exec(lText)) != null) {
    	  lVariables += 1;
    	}
    	
    	if(aTyp == 'variables'){
    		jQuery("input[id^='variable"+aId+"']").remove();
	    	var lBody = '';
	    	for(var lI=1; lI<=lVariables; lI++){
	    		lBody += '<input id="variable'+aId+lI+'" type="text" class="inp" style="float:left;height:26px;margin:0 5px;" value="" placeholder="{variable'+lI+'}" onblur="Flow.cms.form.variableContent(\''+aId+'\',\'cons\')" />';  
	    	}
	    	lElm.after( lBody );
    	} else if(aTyp == 'cons'){
    		var lCons = lText;
    		var lMeta = [];
	    	for(var lI=1; lI<=lVariables; lI++){
	    		var lVariable = jQuery("#variable"+aId+lI).val();
	    		if(lVariable != ''){
	    			lCons = lCons.replace('{variable'+lI+'}', lVariable);
	    			lMeta.push(lVariable);
	    		}
	    	}

	    	lElm.parent().siblings(".consolidation").html(lCons);
	    	jQuery("#"+lId+"_meta").val( lMeta.join(",") );
    	}
    },
    replaceContent : function(aId, aData) { //when selecting content from search dialog, replace with selection
    	var lId = aId.substr(0, aId.length-2);
    	
    	for(lType in aData){
    		for(lCat in aData[lType]){
    			for(lLayout in aData[lType][lCat]){
    				for(lParentId in aData[lType][lCat][lLayout]){
    					for(lLang in aData[lType][lCat][lLayout][lParentId]){
    						var lContent = aData[lType][lCat][lLayout][lParentId][lLang];
    						var lLang = lLang.toLowerCase();
    						
					    	var lCid = lContent['content_id'];
					    	jQuery('input[name="content['+lId+lLang+'_cid]"]').val(lCid);//content_id
					    	
					    	var lPid = lContent['parent_id'];
					    	jQuery('input[name="content['+lId+lLang+'_pid]"]').val(lPid);//parent_id
					    	
					    	var lCont = lContent['content'];
					    	lCont = jQuery('<div>'+lCont+'</div>').text();
    						jQuery("#"+lId+lLang).val(lCont);//content
					    	
					    	var lVersion = lContent['version'];
					    	jQuery('input[name="content['+lId+lLang+'_ver]"]').val(lVersion);//version
					    	
					    	jQuery('input[name="old['+lId+lLang+']"]').val(lCont);
    					}
    				}
    			}
    		}
    	}
    	Flow.cmsForm.highlightTranslations();
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
    }
};

Flow.cmsLayout = {
	text : [],
    duplicateItem : function(aId) { //duplicate item in multi/ingred layouts
    	var lSelf = jQuery("#"+aId);
    	
    	if(lSelf.val() !== "") {
	        var lContent = lSelf.closest(".content");
	        var lItem = lContent.parent(".item");
	        var lClone = lContent.html();
	        
	        var lId = aId.replace("add_", "");
	    	lId = lId.substr(0, lId.length-3);
	        var lCategory = lId.replace(/\d+/g, '');
	        
	        var lContainer = jQuery('#job_content');
	    	var lSize = lContainer.find(".content").length;
	    	
			var lCheck = lCategory+lSize+"_ma";
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
		
		    	lClone.insertAfter(lContent);
	    	}
    	}
    },
    consolidateItem : function(aId) { //consolidate items into area within layout (multi, ingred)
    	var lItem = jQuery("#"+aId).closest(".item");
        var lParts = aId.split("_");
        var lCategory = lParts[0];
        var lNum = lParts[1];
        var lLanguage = lParts[2];
        var lLayout = lCategory.toLowerCase().indexOf('ingred');
        var lLayout2 = lCategory.toLowerCase().indexOf('zutaten');
        
        var lContent = []
        lItem.find("input[name^='val["+lCategory+"']").each( function() {
			var lInpId = jQuery(this).attr("id");
        	var lInpVal = jQuery(this).val();
        	var lClass = jQuery(this).attr("name").indexOf(lLanguage);

        	if(lInpVal !== 'on' && lInpVal !== '' && lClass > -1){
    			//var lVal = (Flow.cmsLayout.text[lInpId] == undefined) ? lInpVal : Flow.cmsLayout.text[lInpId];
        		if(lLayout > -1 || lLayout2 > -1){
        			var lPerc = jQuery("input[name='meta["+lInpId+"_meta]']").val();
        			if(lPerc !== ''){
        				lInpVal += " ("+lPerc+"%)";
        			}
        		}

        		if(lContent.length < 1){ //captialise the first character of first item
        			lInpVal = lInpVal.charAt(0).toUpperCase() + lInpVal.slice(1);
        		}
        		lContent.push( lInpVal );
        	}
        });
        
        var lContHtml = lContent.join(", ");
        if(lLayout > -1 && lContHtml !== ''){
        	lContHtml = "<b>Ingredients: </b>"+lContHtml+".";
        }
        if(lLayout2 > -1 && lContHtml !== ''){
        	lContHtml = "<b>Zutaten: </b>"+lContHtml+".";
        }
        lItem.find("div[class*='"+lLanguage+" consolidation']").html(lContHtml);
    },
    searchDictionary : function(aId) { //get autocomplete suggestions for ingredients layout
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
            	var lParams = { act : 'job-cms.dictionary', category: lCategory, lang: lLanguage, val: lElm.val() };
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
            		act : 'job-cms.gettranslations', 
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
                    	Flow.cmsLayout.text[lId] = aVal.content;

                		//replace text without tags
                    	var lNewVal = Flow.cmsLayout.text[lId].replace(/(<([^>]+)>)/ig,"");
                    	jQuery("#"+lId).val(lNewVal);
                    	
                    	Flow.cmsLayout.duplicateItem(lId);
                    	Flow.cmsLayout.consolidateItem(lId);
                	});
                }, 'json');
            },
            open: function(event, ui) {
            	jQuery(".ui-autocomplete").css("z-index", 1000);
            }
       });
    }
};

Flow.cmsSearch = {
	results : function() { //displays search results in search dialog
    	var lMeta = {};
    	jQuery("input:checkbox[name^='meta']:checked").each( function() {
    		var lId = jQuery(this).prop("id");
    		var lValue = jQuery(this).prop("value");
    		
    		lMeta[lId] = lValue;
    	});
    	
    	var lParams = {
            act : 'job-cms.search',
            content: jQuery("#cms_search").val(),
            meta: lMeta,
            language: 'MA',
            id: jQuery("#cms_cat").val()
        };
    	var lDiv = jQuery("#pag_progress");
    	lDiv.show();

        jQuery.post('index.php', lParams, function(aData) {
        	var lResults = jQuery("#cms_sugg");
        	lResults.html("");
        	
        	for(var i=0; i < aData.length; i++){
        		var lSugg = aData[i];

        		var lSuggestion = jQuery('<div>', {'id': lSugg['content_id'], 'class': "p8 m5 suggestion "+lSugg['language'], onclick: "Flow.cmsSearch.toggle(this);"});
        		
        		var lVersion = jQuery('<span>', {'class': "m5 version"}).text(lSugg['version']);
        		lVersion.appendTo(lSuggestion);
        		
        		var lContent = jQuery('<span>', {'class': "content"}).html(lSugg['content']);
        		lContent.appendTo(lSuggestion);
        		
        		var lLanguage = lSugg['language'].charAt(0).toUpperCase() + lSugg['language'].substr(1);
        		var lTooltip = '<b>Category:</b> '+lSugg['categories']+'<br/><b>Language:</b> '+lLanguage+'<br/><b>Version:</b> '+lSugg['version'];
        		lTooltip+= '<br/><b>Status:</b> '+lSugg['status'];
        		var lInfo = jQuery('<span>', {'class': "info fr", 'data-toggle': 'tooltip', 'data-tooltip-head' : '', 'data-tooltip-body': lTooltip}).html("<img src='img/wave8/ico/16/ml-1.gif' alt='' />");
        		lInfo.appendTo(lSuggestion);
        		
        		lResults.append(lSuggestion);
        	}
        	lDiv.hide();
        }, 'json');
    },
    toggle : function(aResult) { //toggle content div in search dialog if chosen or unselected
    	var lSelf = jQuery(aResult);
    	var lItemId = jQuery("#item_id").val();
    	
    	if(lItemId !== "0"){
    		jQuery(".suggestion.active").removeClass("active");
        }
    	
    	lSelf.toggleClass("active");
    },
    check : function (aSelf) {
    	jQuery("#"+aSelf).closest("tr").prev().find("input:checkbox[name^='meta']").each( function() {
    		var lChkBox = jQuery(this);
    		if(!lChkBox.is(":checked")){
    			lChkBox.prop('checked', 'checked');
    		}
    	});
    	Flow.cmsSearch.results();
    }
};

Flow.cmsProduct = {
	hasChanges : function(lClientKey) {
		var lHtml = '<p>Recent changes have been made to the product <b>'+lClientKey+'</b>.<br/><br/>Do you wish to update the job with these changes?</p>';
		
		var lDiv = jQuery('<div>', {id: 'product_dlg'});
        lDiv.html(lHtml);
        jQuery(lDiv).dialog({
        	create: function() {
                var lDialog = jQuery(this);
                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
                	lDialog.dialog('destroy').remove();
                });
        	},
            title : 'Product Content Updated',
            modal : true,
            width : 500,
            buttons : {
                Yes : function() {
                	var lParams = { 
                		act : 'job-cms.updatejob',
                		code : lClientKey,
                        jobid: jQuery("input[name='jobid']").val(),
                        src: jQuery("input[name='src']").val(),
                        job : JSON.stringify(lJobData)
                    };
                	jQuery.post('index.php', lParams, function(aData) {
                		//alert user with updated notification
                		var lDiv = jQuery('<div>', {id: 'confirm_dlg'});
                		var lData = '<p>Job has been updated with differences from product '+lClientKey+'.</p>';
                		lDiv.html(lData);
                		jQuery(lDiv).dialog({
                			create: function() {
            	                var lDialog = jQuery(this);
            	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
            	                	lDialog.dialog('destroy').remove();
            	                });
            	        	},
                			title : 'Template confirmation',
            	            modal : true,
            	            height: 200,
            	            width: 300,
            	            buttons : {
            	                Close : function() {
            	                    jQuery(this).dialog('destroy').remove();
            	                }
            	            }
                		});
                	}, 'json');
                	jQuery(this).dialog('destroy').remove();
                },
                No : function() {
                    jQuery(this).dialog('destroy').remove();
                }
            }
        });
	},
	update : function() {
		var lLanguages = {'MA': 'Master'};
		var lUpdate = jQuery(".phrase_update").length;
		var lNew = jQuery(".phrase_new").length;
		
		if(lNew == 0 && lUpdate == 0){
			jQuery("#referenceUpdate").val("no");
        	jQuery("form").submit();
		} else {
			jQuery("#translation option").each( function(){
				var lKey = jQuery(this).val();
				var lVal = jQuery(this).text();
				
				if(lKey != ""){
					lLanguages[lKey] = lVal;
				}
			});
			
			var lHtml = '<p>The following changes have been made to the product:</p>';
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
				lHtml+= '<li><b>NEW</b></li><li>&nbsp;</li>';
				lHtml+= lNewCont.join("");
				lHtml+= '<li>&nbsp;</li>';
			}
			if(lUpdCont.length > 0) {
				lHtml+= '<li><b>UPDATE</b></li><li>&nbsp;</li>';
				lHtml+= lUpdCont.join("");
			}
			lHtml+= '</ul><p>Do you wish to update any related jobs with these changes?</p>';
			
			var lDiv = jQuery('<div>', {id: 'reference_dlg'});
	        lDiv.html(lHtml);
	        jQuery(lDiv).dialog({
	        	create: function() {
	                var lDialog = jQuery(this);
	                lDialog.closest('div.ui-dialog').find('.ui-dialog-titlebar-close').click(function(e) {
	                	lDialog.dialog('destroy').remove();
	                });
	        	},
	            title : 'Product Content Update',
	            modal : true,
	            width : 500,
	            buttons : {
	                Yes : function() {
	                	jQuery("#referenceUpdate").val("yes");
	                	jQuery("form").submit();
	                    jQuery(this).dialog('destroy').remove();
	                },
	                No : function() {
	                	jQuery("#referenceUpdate").val("no");
	                	jQuery("form").submit();
	                    jQuery(this).dialog('destroy').remove();
	                }
	            }
	        });
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
		jQuery("#save_dialog").dialog({
			autoOpen: false, width: w, height: h, modal: true, resizable:false,
			buttons: {
				"Publish": function() {
					editor.ExecuteFunction("document","Save");
				}, 
				"Cancel": function() { jQuery(this).dialog("close"); return false; } 
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
						val = (jq("#"+val).text() == '' ? val : jq("#"+val).text());
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
			} catch(err){
				//console.log(err);		
			}
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