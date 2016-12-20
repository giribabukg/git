var jq = jQuery.noConflict();

jQuery(document).ready(function(){
	var act = jQuery("#page_act").val();
	if(act == 'job-apl'){
		act = 'job-' + jQuery("input[name='src']").val();
		jQuery(".job-apl").addClass(act).removeClass("job-apl");
	}
	jQuery(".mmHi").addClass(act);
	
	jQuery("#cmykBtn").on('click', function(){
		cmykProcess();
	});
});


function convertStringToDate(date){
	if(date != ""){
		var index1 = date.indexOf("-");
		var index2 = date.indexOf("/");
		if(index1 != -1){
			var myDate = date.split("-");
			if(myDate[1] == "Jan"){ myDate[1] = 1; } else if(myDate[1] == "Feb"){ myDate[1] = 2; } else if(myDate[1] == "Mar"){ myDate[1] = 3; } else if(myDate[1] == "Apr"){ myDate[1] = 4; } else if(myDate[1] == "May"){ myDate[1] = 5; } else if(myDate[1] == "Jun"){ myDate[1] = 6; } else if(myDate[1] == "Jul"){ myDate[1] = 7; } else if(myDate[1] == "Aug"){ myDate[1] = 8; } if(myDate[1] == "Sep"){ myDate[1] = 9; } else if(myDate[1] == "Oct"){ myDate[1] = 10; } else if(myDate[1] == "Nov"){ myDate[1] = 11; } else if(myDate[1] == "Dec"){ myDate[1] = 12; }
			myDate[2] = "20"+myDate[2];
			date = new Date(myDate[2],myDate[1]-1,myDate[0]);
		} else if(index2 != -1){
			var myDate = date.split("/");
			date = new Date(myDate[2],myDate[1]-1,myDate[0]);
		} else {
			var myDate = date.split(".");
			date = new Date(myDate[2],myDate[1]-1,myDate[0]);
		}
	} else {
		date = new Date();
	}
	
	return date;
}

function convertToTextNewDate(d){
	var day = new String(d.getDate());
	if(day.length == 1){
		day = "0"+day;
	}
	
	var month = new String(d.getMonth()+1);
	if(month == 1){ month = "Jan"; } else if(month == 2){ month = "Feb"; } else if(month == 3){ month = "Mar"; } else if(month == 4){ month = "Apr"; } else if(month == 5){ month = "May"; } else if(month == 6){ month = "Jun"; } else if(month == 7){ month = "Jul"; } else if(month == 8){ month = "Aug"; } if(month == 9){ month = "Sep"; } else if(month == 10){ month = "Oct"; } else if(month == 11){ month = "Nov"; } else if(month == 12){ month = "Dec"; }
	
	var year = new String(d.getFullYear());
	year = year.substring(2, year.length);
	
	return  day+'-'+month+'-'+year;
}

function changeMonth(myDate){
	if(myDate == "Jan"){ myDate = "01"; } else if(myDate == "Feb"){ myDate = "02"; } else if(myDate == "Mar"){ myDate = "03"; } else if(myDate == "Apr"){ myDate = "04"; } else if(myDate == "May"){ myDate = "05"; } else if(myDate == "Jun"){ myDate = "06"; } else if(myDate == "Jul"){ myDate = "07"; } else if(myDate == "Aug"){ myDate = "08"; } if(myDate == "Sep"){ myDate = "09"; } else if(myDate == "Oct"){ myDate = "10"; } else if(myDate == "Nov"){ myDate = "11"; } else if(myDate == "Dec"){ myDate = "12"; }
	
	return myDate;
}

function addBusinessDays(d,n) {
    d = new Date(d.getTime());

    var day = d.getDay();

    d.setDate(
        d.getDate() + n +
        (day === 6 ? 2 : +!day) +
        (Math.floor((n - 1 + (day % 6 || 1)) / 5) * 2));

    return d;
}

function addBusinessDaysPlusOne(blank) {
    var today = new Date();
	deliveryDate = addBusinessDays(today,6);
	jq("*[name='val[design_file_delivery]']").val(convertToTextNewDate(deliveryDate));
	jq("*[name='val[artwork_briefing_for_approval]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,3)));
	jq("*[name='val[artwork_pdf]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,8)));
	jq("*[name='val[artwork_approved]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,13)));
	jq("*[name='val[prepress_pdf]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,18)));
	jq("*[name='val[prepress_approved]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,23)));
	jq("*[name='val[print_data_delivery]']").val(convertToTextNewDate(addBusinessDays(deliveryDate,26)));
}

function cmykProcess(){
	var data = new Array(
		["00ffff","Process","Cyan","rgb(0,255,255)"],
		["ff00ff","Process","Magenta","rgb(255,0,255)"],
		["ffff00","Process","Yellow","rgb(255,255,0)"],
		["000000","Process","Black","rgb(0,0,0)"]
	);
	
	for(var i=0; i<data.length; i++){
		jq("input[name='val[colour_rgb_"+(i+1)+"]']").val(data[i][0]);
		jq("select[name='val[colour_sys_"+(i+1)+"]']").val(data[i][1]);
		jq("input[name='val[colour_name_"+(i+1)+"]']").val(data[i][2]);
		jq("#c1div"+(i+1)).css({ "background" : "none repeat scroll 0% 0% "+data[i][3] });
	}
}

function mars_creation(){
	var today = new Date();
	var date = jq("*[name='val[creation]']");

	var v = addBusinessDays(today, 0);
	date.removeAttr('readonly');
	date.val(convertToTextNewDate(v));
	date.attr('readonly', 'readonly');
}

function mars_retouch(){
	var today = new Date();
	var v = addBusinessDays(today, 15);
	var field = jq("*[name='val[ddl_08]']");
	field.removeAttr('readonly');
	field.val(convertToTextNewDate(v));
	field.attr('readonly', 'readonly');

	var startDate = jq("*[name='val[ddl_02]']").val();
	var flag = jq("*[name='val[retouch]']");
	if(flag.val() != ""){
		var timing = [];
		switch(flag.val()){
			case "Yes":
			  timing = {'ddl_01': 5};
			  break;
			case "No":
			  timing = {'ddl_01': 3};
			  break;
		}

		startDate = convertStringToDate(startDate);
		var v = addBusinessDays(startDate, 0);
		for(date in timing){
			v = addBusinessDays(v, timing[date]);
			var field = jq("*[name='val["+date+"]']");
			field.removeAttr('readonly');
			field.val(convertToTextNewDate(v));
			field.attr('readonly', 'readonly');
		}
	} else {
		flag.css("border", "1px solid red");
		alert("Please fill out the Retouch field");
		return false;
	}
}

function mars_dates(){
	var today = new Date();
	var startDate = jq("*[name='val[ddl_02]']").val();
	var flag = jq("*[name='val[retouch]']");
	if(flag.val() != ""){
		var timing = [];
		switch(flag.val()){
			case "Yes":
			  timing = {'ddl_03': 4, 'ddl_05': 1, 'despatch_date': 5};
			  break;
			case "No":
			  timing = {'ddl_03': 2, 'ddl_05': 1, 'despatch_date': 5};
			  break;
		}

		startDate = convertStringToDate(startDate);
		var v = addBusinessDays(startDate, 0);
		for(date in timing){
			v = addBusinessDays(v, timing[date]);
			var field = jq("*[name='val["+date+"]']");
			field.removeAttr('readonly');
			field.val(convertToTextNewDate(v));
			field.attr('readonly', 'readonly');
		}
	} else {
		flag.css("border", "1px solid red");
		alert("Please fill out the Retouch field");
		return false;
	}
}

function mars_cutterd(){
	var today = new Date();
	var timing = {'ddl_24': 2, 'ddl_25': 2};

	var v = addBusinessDays(today, 0);
	for(date in timing){
		v = addBusinessDays(v, timing[date]);
		var field = jq("*[name='val["+date+"]']");
		field.removeAttr('readonly');
		field.val(convertToTextNewDate(v));
		field.attr('readonly', 'readonly');
	}
}

function mars_amends(){
	var startDate = jq("*[name='val[ddl_03]']").val();
	
	if(startDate !== ""){
		startDate = convertStringToDate(startDate);
		var timing = {'ddl_03': 1, 'ddl_05': 1, 'despatch_date': 5};
		
		for(date in timing){
			v = addBusinessDays(startDate, timing[date]);
			var field = jq("*[name='val["+date+"]']");
			field.removeAttr('readonly');
			field.val(convertToTextNewDate(v));
			field.attr('readonly', 'readonly');
		}
	}
}

function mars_human_deadlines(){
	var abffield = jq('input[name="val[ddl_27]"]').val();
	
	if(abffield == ""){
		var abf = new Date();
		
		abf.setDate(abf.getDate() + 1);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_27]"]').val(day + "." + month + "." + year);
		
		var abffield = jq('input[name="val[ddl_27]"]').val();
        var date = abffield.split(".");
        if ( date[0].length < 2 ) {
            date[0] = "0" + date[0];
        }
		if ( date[1].length < 2 ) {
            date[1] = "0" + date[1];
        }
		
		var findate = date[0]+"-"+date[1]+"-"+date[2];
		var abfnew = new Date( findate.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3") );
		
		abfnew.setDate(abfnew.getDate() + 2);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_28]"]').val(day + "." + month + "." + year);
		
		abfnew.setDate(abfnew.getDate() + 3);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_29]"]').val(day + "." + month + "." + year);
		
		abfnew.setDate(abfnew.getDate() + 4);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_30]"]').val(day + "." + month + "." + year);
		
		abfnew.setDate(abfnew.getDate() + 3);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_31]"]').val(day + "." + month + "." + year);
		
		abfnew.setDate(abfnew.getDate() + 4);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_32]"]').val(day + "." + month + "." + year);
		
		abfnew.setDate(abfnew.getDate() + 2);var month = abfnew.getMonth() + 1;var day = abfnew.getDate();var year = abfnew.getFullYear();
		jq('input[name="val[ddl_33]"]').val(day + "." + month + "." + year);
	}else{
		var date = abffield.split(".");
		 if ( date[0].length < 2 ) {
            date[0] = "0" + date[0];
        }
		if ( date[1].length < 2 ) {
            date[1] = "0" + date[1];
        }
		var findate = date[0]+"-"+date[1]+"-"+date[2];
		var abf = new Date( findate.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3") );
		
		abf.setDate(abf.getDate() + 1);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_28]"]').val(day + "." + month + "." + year);
		
		abf.setDate(abf.getDate() + 2);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_29]"]').val(day + "." + month + "." + year);
		
		abf.setDate(abf.getDate() + 4);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_30]"]').val(day + "." + month + "." + year);
		
		abf.setDate(abf.getDate() + 2);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_31]"]').val(day + "." + month + "." + year);
		
		abf.setDate(abf.getDate() + 4);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_32]"]').val(day + "." + month + "." + year);
		
		abf.setDate(abf.getDate() + 2);var month = abf.getMonth() + 1;var day = abf.getDate();var year = abf.getFullYear();
		jq('input[name="val[ddl_33]"]').val(day + "." + month + "." + year);
	}
}

