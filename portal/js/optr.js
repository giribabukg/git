var period = 'Monthly';
var type = 'Unique';
var hrDay = 0;

jQuery(document).ready(function(){
	getChart(period, type);
    
    var html = "<input value='Hourly' type='button' class='btn rptBtn' id='hourly' /> ";
    html += "<input value='Daily' type='button' class='btn rptBtn' id='daily' /> <input value='Weekly' type='button' class='btn rptBtn' id='weekly' /> ";
    html += "<input value='Monthly' type='button' class='btn rptBtn dis' id='monthly' disabled='disabled' />";
    html += "<br/><br/><input value='Unique' type='radio' name='type' checked='checked' /> Unique Users<br/><input value='Openings' type='radio' name='type' /> Number of times opened";
    jQuery('#periods').html(html);
    
    jQuery("input[name=type]").on('click', function(){
    	jQuery("#pag_progress").show();   	
    	type = jQuery(this).val();
    	getChart(period, type, hrDay);
    	
    	jQuery("#pag_progress").hide(); 
    });
    
    jQuery("#daily, #weekly, #monthly, #hourly").on('click', function(){   	
    	period = jQuery(this).val();
    	getChart(period, type, hrDay);
        
    	jQuery(".rptBtn").removeAttr("disabled");
    	jQuery(this).attr("disabled", "disabled");
    	if(period == 'Hourly')
    		hrDay = 0;
    });
    
    jQuery("#lt").on('click', function(){
    	hrDay = hrDay - 1;
    	getChart(period, type, hrDay);
    });
    
    jQuery("#gt").on('click', function(){
    	hrDay = hrDay + 1;
    	getChart(period, type, hrDay);
    });
});

function getChart(period, type, days){
	jQuery("#pag_progress").show();
    jQuery('#lt, #gt, #graph, #periods').hide();
	var lData = [];
	jQuery.ajax({
		type: 'post',
	    url: 'index.php?act=ajx.getchartdata',
	    async: false,
	    data : {per: period, typ: type, dtInc: days},
	    dataType: 'json',
	    success: function(aData) {
	      lData = aData;
	    }
	});
	
	var series = [];
	for(mand in lData['series']){
		var array_values = new Array();

		for (var key in lData['series'][mand]) {
		    array_values.push(lData['series'][mand][key]);
		}
		series.push({ name: mand, data: array_values});
	}
	
	var text = period + ' ' + type + ' User Usage';
	var date = new Date();
	date.setDate(date.getDate() + hrDay);
	if(period == 'Hourly'){
		var day = date.getDate();
		var month = date.getMonth() + 1;
		if (month < 10) month = "0" + month;
		var year = date.getFullYear();
		date = day+'/'+month+'/'+year;
		
		text = period + ' ' + type + ' User Usage for ' + date;
	}
	var chart = {
        title: { text: text, x: -20, style: { color: '#455560', fontSize: '16px' } },
        xAxis: { categories: lData['categories'], labels: { style: { width: '12000px' } } },
        yAxis: { title: { text: 'Users' }, plotLines: [{ value: 0, width: 1, color: '#808080' }], min: 0 }, 
        tooltip: { valueSuffix: ' users' },
        legend: { useHTML: false },
        series: series
    };
	
	if(period == 'Hourly')
		chart['xAxis']['labels']['rotation'] = 90;

    jQuery('#graph').show().highcharts(chart);
    jQuery('#periods').show();
    if(period == 'Hourly')
    	jQuery('#lt, #gt').show();
	jQuery("#pag_progress").hide();   
}