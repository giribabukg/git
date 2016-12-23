// fmtDate (see: http://www.php.net/manual/en/function.date.php)
Date.prototype.fmtDate = function(aFmt) {
    var lZeroFill = function(aValue) {
        return ('0' + aValue).slice(-2);
    };

    var lElemArr = {
        'Y' : this.getFullYear(),
        'm' : this.getMonth() + 1,
        'd' : this.getDate(),
        'H' : this.getHours(),
        'i' : this.getMinutes(),
        's' : this.getSeconds()
    };

    for (var lElem in lElemArr) {
        if (new RegExp("("+ lElem +")").test(aFmt)) {
            var lValue = (lElem != 'Y') ? lZeroFill(lElemArr[lElem]) : lElemArr[lElem];
            aFmt = aFmt.replace(RegExp.$1, lValue);
        }
    }

    return aFmt;
};

// fmtPercentage
Flow.fmtPercentage = function(aPercentage) {
    return (aPercentage * 100).toFixed(2) + ' %';
};

// fmtFileSize
Flow.fmtFilesize = function(aFilesize) {
    var lValue = [1<<30, 1<<20, 1<<10, 1];
    var lUnit = [' GB', ' MB', ' kB', ' Byte'];

    for (var lCounter in lValue) {
        if (aFilesize >= lValue[lCounter]) {
            return (aFilesize / lValue[lCounter]).toFixed(1) + lUnit[lCounter];
        }
    }
};

// fmtByterate
Flow.fmtByterate = function(aBitrate) {
    var lValue = [(1<<30)*8, (1<<20)*8, (1<<10)*8, 8];
    var lUnit = [' GB/s', ' MB/s', ' kB/s', ' B/s'];

    for (var lCounter in lValue) {
        if (aBitrate >= lValue[lCounter]) {
            return (aBitrate / lValue[lCounter]).toFixed(1) + lUnit[lCounter];
        }
    }
};

// Flink aka FileLink
Flow.filelink = function() {
    "use strict";

    // this'n'gThat
    var gThat = this;

    // files queue
    var gQueue = [];

    // as some features (like multiple file upload) are not available for Microsoft Internet Explorer prior to verion 10, we do a simple version checking 
    var gIEVersion = Flow.IEver.detect();

    // whether a file is currently uploading or not
    var gUploading = false;

    // nomen est omen
    var gCurrentId = -1;

    // nomen est omen
    var gFilename = '';
    var gFiletype = '';
    var gFiletypes = '';

    // add
    this.addSingle = function(aParams) {
        var lFId = aParams['fid']; // alias (e.g. file_upload)
        var lSrc = aParams['src']; // job source/type: art, rep, etc.
        var lJId = aParams['jid']; // job id
        var lSub = aParams['sub']; // sub (currently available: dalim, dms, doc, pdf, rtp, wec) from job field
        var lMFS = aParams['mfs']; // upload_max_filesize from php.ini
        var lLan = aParams['lan']; // all translations relating to flink.*
        var lFTy = aParams['fty']; // file type
        var lCat = aParams['cat']; // file category

        var lOldFilename = ''; // original filename
        var lOldFiletype = ''; // original filetype
        var lNewFilename = ''; // in case the filename is changed during the upload process, the new filename is stored here

        var lButtonAdd = jQuery('#' + lFId + '_button_add');
        var lButtonUpload = jQuery('#' + lFId + '_button_upload');
        var lProgressValue = jQuery('#' + lFId + '_div_progress_value');
        var lProgressText = jQuery('#' + lFId + '_div_progress_text');

        jQuery().ready(function() {
            if (gIEVersion != -1 && gIEVersion < 10) {
                jQuery('#' + lFId + '_button_add').remove();
                jQuery('#' + lFId + '_container_add').html('<span class="flink_button"><img src="img/ico/16/new-hi.gif" alt=""><span>' + lLan['flink.add'] + '</span><input name="files[]" type="file" id="' + lFId + '"/></span>');

                jQuery('#' + lFId + '_button_upload').remove();
                jQuery('#' + lFId + '_container_upload').html('<span class="flink_button cursor_pointer" id="' + lFId + '_span_upload"><img src="img/ico/16/upload-lo.gif" alt=""><span>' + lLan['flink.upload'] + '</span></span>');

                jQuery('#' + lFId + '_span_upload').click(function() {Flow.flink.uploadSingle(lFId);});
            }

            lButtonAdd.removeAttr('disabled');

            jQuery('#' + lFId).fileupload({
                url: 'index.php?act=job-fil.filelinkupload&fid=' + lFId + '&src=' + lSrc + '&jid=' +lJId + '&sub=' + lSub + '&cat=' + lCat,
                type: 'post',
                dataType: 'json',
                // maxChunkSize: lMFS,
                multipart: true,
                formData: null,
                add: function(event, data) {
                    lOldFilename = data.files[0].name;

                    if (typeof(lFTy) !== "undefined" && lFTy !== null && lFTy !== '') {
                        lOldFiletype = lOldFilename.split('.').pop().toLowerCase();

                        if (lFTy.indexOf(lOldFiletype) < 0) {
                            alert(lOldFilename + ' ' + lLan['flink.filetype.fail']);
                            return false;
                        }
                    }

                    gQueue[lFId] = data;

                    if (lProgressValue.progressbar()) {
                        lProgressValue.progressbar('destroy');
                    }

                    lProgressValue.progressbar({
                        value: 0,
                        create: function() {
                            lProgressText.html('<p>' + lOldFilename + '</p>');
                        }
                    });

                    lProgressValue.addClass('progress_value');
                    lProgressText.addClass('progress_text');

                    jQuery('#' + lFId + '_container_upload span img').attr('src', 'img/ico/16/upload-hi.gif');
                    lButtonUpload.removeAttr('disabled');
                },
                progress: function(event, data) {
                    var lPercentage = parseInt(data.loaded / data.total * 100, 10);
                    var lfmtPercentage = Flow.fmtPercentage(data.loaded / data.total);
//                    var lfmtFilesizeTotal = Flow.fmtFilesize(data.total);
//                    var lfmtFilesizeLoaded = Flow.fmtFilesize(data.loaded);
                    var lfmtByterate = Flow.fmtByterate(data.bitrate);

                    lProgressValue.progressbar({
                        value: lPercentage,
                        change: function() {
                            lProgressText.html('<p>' + lOldFilename + '<br>' + lfmtPercentage + ' @ ' + lfmtByterate + '</p>');
                        }
                    });
                },
                fail: function(event, data) {
                    alert(lOldFilename + ' ' + lLan['flink.upload.fail']);
                },
                done: function(event, data) {
                    if (data.jqXHR.readyState == 4 && data.jqXHR.status == 200) {
                        lNewFilename = data.result.files[0].name;

                        if (lOldFilename != lNewFilename) {
                            lProgressText.html('<p>' + lLan['flink.file.rename'] + '<br>' + lNewFilename + '</p>');
                        }

                        lProgressText.html('<p>' + lNewFilename + '<br>' + lLan['flink.register'] + '</p>');

                        jQuery.ajax({
                            type : 'post',
                            url : 'index.php?act=job-fil.filelinksupload',
                            data : {fid: lFId, src: lSrc, jid: lJId, sub: lSub, fil: lNewFilename, old: lOldFilename, cat: lCat}
                        }).fail(function() {
                            lProgressText.html('<p>' + lLan['flink.register.fail'] + '</p>');
                        }).done(function() {
                            lProgressText.html('<p>' + lLan['flink.upload.done'] + '</p>');
                        });

                        if (lSub != 'dalim') {
                            jQuery.ajax({
                                type : 'post',
                                url : 'index.php?act=job-fil.filelinkuploadreg',
                                data : {src: lSrc, jid: lJId, sub: lSub, fil: lNewFilename, cat: lCat}
                            });
                        }
                    }
                },
                always: function(event, data) {
                    lButtonAdd.removeAttr('disabled');
                }
            });
        });
    };

    // upload
    this.uploadSingle = function(aFId) {
        var lFId = aFId;

        jQuery('#' + lFId + '_button_add').attr('disabled', true);
        jQuery('#' + lFId + '_button_upload').attr('disabled', true);

        gQueue[lFId].submit();
    };

    // add
    this.addMultiple = function(aParams) {
        var lAge = aParams['age']; // job, arc
        var lSrc = aParams['src']; // adm, art, com, mis, rep, sec, etc. 
        var lJId = aParams['jid']; // jobid
        var lSub = aParams['sub']; // dalim, doc, dms, pdf, wec, etc.
        var lCat = aParams['cat']; // category
        var lDiv = aParams['div']; // division
        var lFId = aParams['fid']; // alias (e.g. file_upload)
        var lMFS = aParams['mfs']; // max. filesize as defined in php.ini (upload_max_filesize)
        var lLan = aParams['lan']; // not the language id, but the translations itselves
        var lFFD = aParams['ffd']; // date format as defined in lan (lib.datetime.short)
        var lUFN = aParams['ufn']; // user fullname
        var lFTy = aParams['fty']; // file type

        gThat.gFiletypes = lFTy; // array of filetypes

        var lFilesSelect = jQuery('#flink_files_select');
        var lBodyList = jQuery('#flink_body_list');
        var lHeadButtonAdd = jQuery('#flink_head_button_add');
        var lHeadListProgressAllText = jQuery('#progress_text');
        var lHeadListProgressAllValue = jQuery('#progress_value');
        var lHeadListToggleAll = jQuery('#flink_head_list_checkbox_toggle_all');

        var lMaxHeight = jQuery(window).height() - 32;
        var lMaxWidth = jQuery(window).width() - 32;
        var lMinHeight = 160;
        var lMinWidth = 1024;
        var lHeight = 'auto';
        var lWidth = jQuery(window).width() - ((jQuery(window).width() - 1024) / 2);

        jQuery().ready(function() {
            // IE <10 can not handle input elements being triggered by button elements, so we have to beautify the input element to make it look like a button element 
            if (gIEVersion != -1 && gIEVersion < 10) {
                lHeadButtonAdd.remove();
                jQuery('#flink_container').html('<span class="flink_button"><img src="img/ico/16/new-hi.gif" alt=""><span>' + lLan['flink.add'] + '</span><input name="files[]" type="file" id="' + lFId + '"/></span>');
            }

            // as soon as the upload button has been created we can remove the >disabled< attribute to make it ready to work
            lHeadButtonAdd.removeAttr('disabled');

            // we need to differ between >Add< button clicks from the pop up window and from the main window
            var params = false;

            // when we click the >Add< button from the pop up window, we do not need to clean the pop up window
            if (params === false) {
                lBodyList.empty();
            }

            // we need to reset the progressbar
            if (lHeadListProgressAllValue.progressbar()) {
                lHeadListProgressAllValue.progressbar('destroy');
            }

            lHeadListProgressAllValue.progressbar({
                value: 0,
                create: function() {
                    lHeadListProgressAllText.html('<p>0%<br>0MB/s</p>');
                }	
            });

            lHeadListProgressAllValue.addClass('progress_value');
            lHeadListProgressAllText.addClass('progress_text');

            // we need to reset the checkbox
            lHeadListToggleAll.attr('checked', false);

            // jQuery File Upload
            jQuery('#' + lFId).fileupload({
                url: 'index.php?act=job-fil.filelinkupload&src=' + lSrc + '&jid=' +lJId + '&sub=' + lSub + '&div=' + lDiv + '&age=' + lAge,
                type: 'post',
                dataType: 'json',
                // maxChunkSize: lMFS, // do not forget to active the corresponding argument!
                multipart: true,
                add: function(addEvent, data) {
                    // when we click the >Add< button from the pop up window, we do not need to open up another pop up window
                    if (params === false) {
                        // jQuery UI dialog itself
                        lFilesSelect.dialog({
                            buttons: [
                                {
                                    text: lLan['flink.add'],
                                    id: 'dialog_add',
                                    click: function(aEvent) {
                                        // add files
                                        jQuery('#' + lFId).trigger("click", true);
                                    }
                                },{
                                    text: lLan['flink.cancel'],
                                    id: 'dialog_cancel',
                                    click: function(aEvent) {
                                        aEvent.preventDefault();

                                        if (gThat.gUploading == true) {
                                            if (confirm(lLan['flink.cancel.confirm']) == true) {
                                                var lFilename = gQueue[gThat.gCurrentId].files[0].name;

                                                data.submit().abort();

                                                jQuery.ajax({
                                                    url: 'index.php?act=job-fil.filelinkupload&src=' + lSrc + '&jid=' +lJId + '&sub=' + lSub + '&file=' + lFilename,
                                                    dataType: 'json',
                                                    data: {file: lFilename},
                                                    type: 'DELETE'
                                                });
                                            } else {
                                                return;
                                            }
                                        }

                                        // close dialog
                                        lFilesSelect.dialog('destroy');
                                        lBodyList.empty();

                                        // refresh the filelist in case there was no such trigger from AJaX (upload, upload all, cancel, cancel all, etc.)
                                        jQuery('#' + lDiv).html('<div style="text-align:center; position:relative; top:50px">' + Flow.Std.imgFinder("img/pag/ajx.gif", {'alt':'Loading...', 'class':'p16 ac'}) + '<br />Loading...</div>');
                                        jQuery.ajax({
                                            type : 'get',
                                            dataType: "html",
                                            url : 'index.php?act=job-'+lSrc+'-fil.get',
                                            data : {div: lDiv, src: lSrc, jid: lJId, sub: lSub, age: lAge},
                                            success: function(data) {
                                                jQuery('#' + lDiv).html(data);
                                            }
                                        });
                                    }
                                }],
                            height: lHeight,
                            maxHeight: lMaxHeight,
                            maxWidth: lMaxWidth,
                            minHeight: lMinHeight,
                            minWidth: lMinWidth,
                            modal: true,
                            open: function(aEvent) {
                                aEvent.preventDefault();

                                jQuery('.ui-widget-overlay').on('click', function() {
                                    jQuery('#dialog_cancel').effect('pulsate', {}, 750);
                                });
                            },
                            close: function(aEvent) {
                                aEvent.preventDefault();

                                lFilesSelect.dialog('destroy');
                                lBodyList.empty();
                            },
                            width: lWidth
                        });

                        // beautifying the jQuery UI dialog
                        lFilesSelect.css('padding', '0px').css('padding-top', '2px'); // ehemals lDialog
                        jQuery('.ui-dialog-buttonpane.ui-widget-content.ui-helper-clearfix').removeClass('ui-widget-content');

                        // IE < 10 can not upload multiple files, therefore the >Add< button needs to be removed
                        if (gIEVersion != -1 && gIEVersion < 10) {
                            jQuery('button span:contains(' + lLan['flink.add'] + ')').remove();
                            lFilesSelect.dialog('option', 'width', 1024);
                        }
                    }

                    var lOldFilename = '';
                    var lOldFiletype = '';
                    var lFiletypes = false;
                    var lTd = 'td1';
                    var lDisabled = '';

                    lOldFilename = data.files[0].name;
                    if (typeof(gThat.gFiletypes) !== "undefined" && gThat.gFiletypes !== null && gThat.gFiletypes != '') {
                        lOldFiletype = lOldFilename.split('.').pop().toLowerCase();

                        if (gThat.gFiletypes.indexOf(lOldFiletype) < 0) {
                        	lFiletypes = true;
                            lTd = 'td1r';
                            lDisabled = ' disabled="disabled"';
                        } else {
                        	lFiletypes = false;
                            lTd = 'td1';
                            lDisabled = '';
                        }
                    }

                    // get number of rows
                    var lTrLength = jQuery('tbody[id="flink_body_list"] > tr').length;

                    var lLanJSONEnc = JSON.stringify(lLan);
                    var lLanHTMLSpecialChars = lLanJSONEnc
                    .replace(/&/g, "&amp;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;");

                    // single row
                    var lRet = '<tr class="hi" id="' + (lTrLength + 1) + '">';
                    lRet += '<td class="' + lTd + ' ar w16 p4" id="ctr"></td>';
                    lRet += '<td class="' + lTd + ' p4" id="name" style="min-width:100px;max-width:200px;word-break:break-all;word-wrap:break-word">' + data.files[0].name + '</td>';
                    lRet += '<td class="' + lTd + ' nw w16 p4" id="category"></td>';

                    // IE < 10 can not read file size
                    if (gIEVersion == 'undefined' || gIEVersion == -1 || gIEVersion >= 10) {
                        lRet += '<td class="' + lTd + ' nw w16 p4" id="size">' + Flow.fmtFilesize(data.files[0].size) + '</td>';
                    } else if (gIEVersion != -1 && gIEVersion < 10) {
                        lRet += '<td class="' + lTd + ' nw w16 p4" id="size">' + lLan['flink.size.unavailable'] + '</td>';
                    }

                    lRet += '<td class="' + lTd + ' p4" id="user" style="min-width:100px;max-width:200px;word-break:break-all;word-wrap:break-word">' + lUFN + '</td>';
                    lRet += '<td class="' + lTd + ' nw w16 p4" id="date">' + new Date().fmtDate(lFFD) + '</td>';

                    // edit
                    lRet += '<td class="' + lTd + ' nw w100p p4" id="edit">' +
                                '<textarea id="flink_body_list_textarea_edit" class="w100p bsbb"></textarea>' +
                            '</td>';

                    // progress bar
                    lRet += '<td class="' + lTd + ' nw w100 p4" id="flink_body_list_div_progress">' +
                                '<div class="w100" id="progress_value">' +
                                    '<div id="progress_text"></div>' +
                                '</div>' +
                            '</td>';

                    // upload button
                    lRet += '<td class="' + lTd + ' nw w16 p4" id="flink_body_list_button_upload">' +
                                '<button class="btn w100p" type="button" id="flink_body_list_button_upload" onclick="Flow.flink.uploadMultiple(this);"' + lDisabled + '>' +
                                    '<div class="al">' +
                                        '<table cellpadding="2" cellspacing="0" border="0" class="al w25p">' +
                                            '<tbody>' +
                                                '<tr>' +
                                                    '<td>' +
                                                        '<img src="img/ico/16/upload-hi.gif" alt="">' +
                                                    '</td>' +
                                                    '<td class="nw al">' + lLan['flink.upload'] + '</td>' +
                                                '</tr>' +
                                            '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</button>' +
                            '</td>';

                    // cancel button
                    lRet += '<td class="' + lTd + ' nw w16 p4" id="flink_body_list_button_cancel">' +
                                '<button class="btn w100p" type="button" id="flink_body_list_button_cancel" onclick="Flow.flink.cancelMultiple(this,\'' + lSrc + '\',\'' + lJId + '\',\'' + lSub + '\');"' + lDisabled + '>' +
                                    '<div class="al">' +
                                        '<table cellpadding="2" cellspacing="0" border="0" class="al w25p">' +
                                            '<tbody>' +
                                                '<tr>' +
                                                    '<td>' +
                                                        '<img src="img/ico/16/ml-4.gif" alt="">' +
                                                    '</td>' +
                                                    '<td class="nw al">' + lLan['flink.cancel'] + '</td>' +
                                                '</tr>' +
                                            '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</button>' +
                            '</td>';

                    // checkbox
                    lRet += '<td class="' + lTd + ' nw w16 p4" id="flink_body_list_checkbox_toggle">' +
                                '<input type="checkbox" onclick="Flow.flink.toggleMultiple(' + lLanHTMLSpecialChars  + ');"' + lDisabled + '>' +
                                '<label>' + lLan['flink.toggle'] + '</label>' +
                            '</td>';

                    lRet += '</tr>';

                    // append single row to draft
                    lBodyList.append(lRet);

                    // update categories in single row
                    jQuery('tbody[id="flink_body_list"] td[id="category"]').html('<select>');
                    jQuery('tbody[id="flink_body_list"] td[id="category"] select').empty();
                    jQuery.each(lCat, function(lCatKey, lCatValue) {
                        jQuery('tbody[id="flink_body_list"] td[id="category"] select').append(jQuery("<option>").attr('value', lCatKey).text(lCatValue));
                    });

                    // we need to reset the progressbar
                    if (jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_value"]').progressbar()) {
                        jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_value"]').progressbar('destroy');
                    }

                    jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_value"]').progressbar({
                        value: 0,
                        create: function() {
                        	if (lFiletypes == false) {
                        		jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_text"]').html('<p>0%<br>0MB/s</p>');
                        	} else {
                        		jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_text"]').html('<p>' + lLan['flink.filetype.fail'] + '</p>');
                        	}
                        }
                    });

                    jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_value"]').addClass('progress_value');
                    jQuery('tr[id="' + (lTrLength + 1) + '"] div[id="progress_text"]').addClass('progress_text');

                    // restore altering background color
                    jQuery('tbody[id="flink_body_list"] tr:odd').find('td[id]').removeClass('td1').addClass('td2');
                    jQuery('tbody[id="flink_body_list"] tr:even').find('td[id]').removeClass('td2').addClass('td1');

                    // add to files[]
                    data.context = jQuery('tbody[id="flink_body_list"] tr[id="' + (lTrLength + 1) + '"]');
                    gQueue[lTrLength + 1] = data;
                },
                progress: function(progressEvent, data) {
                    if (gIEVersion == 'undefined' || gIEVersion == -1 || gIEVersion >= 10) {
                        var lPercentage = parseInt(data.loaded / data.total * 100, 10);
                        var lfmtPercentage = Flow.fmtPercentage(data.loaded / data.total);
//                        var lfmtFilesizeTotal = Flow.fmtFilesize(data.total);
//                        var lfmtFilesizeLoaded = Flow.fmtFilesize(data.loaded);
                        var lfmtByterate = Flow.fmtByterate(data.bitrate);

                        data.context.find('#progress_value').progressbar({
                            value: lPercentage,
                            change: function() {
                                data.context.find('#progress_text').html('<p>' + lfmtPercentage + '<br>' + lfmtByterate + '</p>');
                            }
                        });
                    } else if (gIEVersion != -1 && gIEVersion < 10) {
                        data.context.find('#progress_text').text(lLan['flink.uploading']).css('color', '#000000').fadeTo('slow', 0.25).fadeTo('slow', 1.0);
                    }
                },
                progressall: function(progressallEvent, data) {
                    if (gIEVersion == 'undefined' || gIEVersion == -1 || gIEVersion >= 10) {
                        var lPercentage = parseInt(data.loaded / data.total * 100, 10);
                        var lfmtPercentage = Flow.fmtPercentage(data.loaded / data.total);
//                        var lfmtFilesizeTotal = Flow.fmtFilesize(data.total);
//                        var lfmtFilesizeLoaded = Flow.fmtFilesize(data.loaded);
                        var lfmtByterate = Flow.fmtByterate(data.bitrate);

                        lHeadListProgressAllValue.progressbar({
                            value: lPercentage,
                            change: function() {
                                lHeadListProgressAllText.html('<p>' + lfmtPercentage + '<br>' + lfmtByterate + '</p>');
                            }
                        });
                    } else if (gIEVersion != -1 && gIEVersion < 10) {
                        lHeadListProgressAllText.text(lLan['flink.uploading.all']).css('color', '#FFFFFF').fadeTo('slow', 0.25).fadeTo('slow', 1.0);
                    }
                },
                fail: function(failEvent, data) {
                    var lName = data.context.find('td[id="name"]').text();
                    alert(lName + ' ' + lLan['flink.upload.fail']);
                },
                done: function(doneEvent, data) {
                    var lName = data.result.files[0].name;
                    var lCatId = data.context.find('td[id="category"] :selected').val();
                    var lCatName = data.context.find('td[id="category"] :selected').text();
                    var lEdit = data.context.find('td[id="edit"] textarea').val();

                    // counter
                    data.context.removeAttr('id');
                    var lNewNumber = jQuery('tbody[id="flink_body_list"] > tr:not([id])').length;
                    data.context.find('td[id="ctr"]').text(lNewNumber + '.');

                    // name
                    data.context.find('td[id="name"]').html('<a href="index.php?act=utl-fil.down&src=' + lSrc + '&jid=' + lJId + '&sub=' + lSub +'&fn=' + lName + '">' + lName + '</a>');

                    // category
                    data.context.find('td[id="category"]').text(lCatName);

                    // date
                    data.context.find('td[id="date"]').text(new Date().fmtDate(lFFD));

                    // edit
                    data.context.find('td[id="edit"] textarea').attr('readonly','readonly');

                    // deprecated
                    data.context.find('td[id="flink_body_list_div_progress"]').empty();
                    data.context.find('td[id="flink_body_list_button_upload"]').empty();
                    data.context.find('td[id="flink_body_list_button_cancel"]').empty();
                    data.context.find('td[id="flink_body_list_checkbox_toggle"]').empty();

                    if (lSub == 'pixelboxx') {
                        data.context.find('td[id="flink_body_list_div_progress_text"]').html('<p>' + lName + '<br>' + lLan['flink.register'] + '</p>');

                        jQuery.ajax({
                            type : 'post',
                            url : 'index.php?act=job-fil.filelinksupload',
                            data : {src: lSrc, jid: lJId, sub: lSub, fil: lName, old: lName}
                        });
                    }

                    jQuery.ajax({
                        type : 'post',
                        async : false,
                        url : 'index.php?act=job-fil.filelinkuploadreg',
                        data : {src: lSrc, jid: lJId, sub: lSub, fil: lName, cat: lCatId}
                    }).done(function() {
                        jQuery.ajax({
                            type : 'post',
                            url : 'index.php?act=job-fil.filelinkuploadtxt',
                            data : {src: lSrc, jid: lJId, sub: lSub, fil: lName, txt: lEdit}
                        });
                    });
                },
                always: function(alwaysEvent, data) {
                    alwaysEvent.preventDefault();

                    gThat.gUploading = false;
                    gThat.gCurrentId = -1;

                    var lTrLength = 0;
                    lBodyList.find('tr td[id="ctr"]').each(function() {
                        if (jQuery(this).text() === '')
                            lTrLength+=1;
                    });

                    if (lTrLength === 0) {
                        // close the jQuery UI dialog
                        lFilesSelect.dialog('destroy');
                        lBodyList.empty();

                        jQuery('#' + lDiv).html('<div style="text-align:center; position:relative; top:50px">' + Flow.Std.imgFinder("img/pag/ajx.gif", {'alt':'Loading...', 'class':'p16 ac'}) + '<br />Loading...</div>');
                        jQuery.ajax({
                            type : 'get',
                            dataType: "html",
                            url : 'index.php?act=job-'+lSrc+'-fil.get',
                            data : {div: lDiv, src: lSrc, jid: lJId, sub: lSub, age: lAge},
                            success: function(data) {
                                jQuery('#' + lDiv).html(data);
                            }
                        });
                    }
                }
            });
        });
    };

    this.uploadMultiple = function(aThis) {
        var lId = jQuery(aThis).closest('tbody[id="flink_body_list"] tr').attr('id');

        this.gUploading = true;
        this.gCurrentId = lId;

        gQueue[lId].submit();
    };

    this.uploadAllMultiple = function() {
        var lToggledLength = jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').length;

        if (lToggledLength === 0) {
            jQuery('tbody[id="flink_body_list"]').find('tr > td > button[id="flink_body_list_button_upload"]').each(function() {
            	if (!jQuery(this).is(":disabled")) {
                    jQuery(this).trigger('click');
            	}
            });
        } else {
            jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').each(function() {
                jQuery('tbody[id="flink_body_list"] tr[id="' + this.id + '"] > td > button[id="flink_body_list_button_upload"]').trigger('click');
            });
        }
    };

    this.cancelMultiple = function(aThis, aSrc, aJId, aSub) {
        var lThis = aThis;
        var lSrc = aSrc;
        var lJId = aJId;
        var lSub = aSub;

        // remove file from view
        var lId = jQuery(lThis).closest('tbody[id="flink_body_list"] tr').attr('id');
        jQuery('tbody[id="flink_body_list"] tr[id="' + lId + '"]').remove();

        // restore altering background color
        jQuery('tbody[id="flink_body_list"] tr:odd').find('td[id]').removeClass('td1').addClass('td2');
        jQuery('tbody[id="flink_body_list"] tr:even').find('td[id]').removeClass('td2').addClass('td1');

        // remove file from file system
        if (typeof gQueue[lId].jqXHR != 'undefined') {
            gQueue[lId].jqXHR.abort();
                    
            jQuery.ajax({
                url: 'index.php?act=job-fil.filelinkupload&src=' + lSrc + '&jid=' +lJId + '&sub=' + lSub + '&file=' + gQueue[lId].files[0].name,
                dataType: 'json',
                data: {file: gQueue[lId].files[0].name},
                type: 'DELETE'
            });
        }

        // remove file from filelist
        delete gQueue[lId];

        var lTrLength = 0;
        jQuery('tbody[id="flink_body_list"]').find('tr td[id="ctr"]').each(function() {
            lTrLength+=1;
        });

        if (lTrLength === 0) {
            jQuery('div[id="files-select"]').dialog().dialog("destroy");
        }
    };

    this.cancelAllMultiple = function() {
        var lToggledLength = jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').length;

        if (lToggledLength === 0) {
            jQuery('tbody[id="flink_body_list"]').find('tr > td > button[id="flink_body_list_button_cancel"]').each(function() {
                jQuery(this).trigger('click');
            });
        } else {
            jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').each(function() {
                jQuery('tbody[id="flink_body_list"] tr[id="' + this.id + '"] > td > button[id="flink_body_list_button_cancel"]').trigger('click');
            });
        }
    };

    this.toggleMultiple = function(aSrc) {
        var lLan = aSrc;

        if (jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').length > 0) {
            jQuery('#flink_head_list_button_upload_all_text_raw').html(lLan['flink.upload.selected']);
            jQuery('#flink_head_list_button_cancel_all_text_raw').html(lLan['flink.cancel.selected']);
        } else {
            jQuery('#flink_head_list_button_upload_all_text_raw').html(lLan['flink.upload.all']);
            jQuery('#flink_head_list_button_cancel_all_text_raw').html(lLan['flink.cancel.all']);
        }
    };

    this.toggleAllMultiple = function(aSrc) {
        var lLan = aSrc;

        var lCheckBoxes = jQuery('tbody[id="flink_body_list"] input[type="checkbox"]');
        lCheckBoxes.prop("checked", !lCheckBoxes.prop("checked"));

        if (jQuery('tbody[id="flink_body_list"] tr').filter(':has(:checkbox:checked)').length > 0) {
            jQuery('#flink_head_list_button_upload_all_text_raw').html(lLan['flink.upload.selected']);
            jQuery('#flink_head_list_button_cancel_all_text_raw').html(lLan['flink.cancel.selected']);
        } else {
            jQuery('#flink_head_list_button_upload_all_text_raw').html(lLan['flink.upload.all']);
            jQuery('#flink_head_list_button_cancel_all_text_raw').html(lLan['flink.cancel.all']);
        }
    };
};
Flow.flink = new Flow.filelink();