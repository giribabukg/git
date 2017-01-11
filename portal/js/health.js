Flow.health = {
update : function (aRes) {


},


initMonitorPage : function() {
jQuery('.health-system-header').click(function(aElement) {
Flow.health.togSystem(aElement.target);
});
setInterval(Flow.health.refresh, 20000);
},


testSystem : function (aSysId) {
var url = 'index.php?act=health.testsystem&id='+aSysId;
jQuery.post(url, function(aResponse) {
for (var id in aResponse) {
var row = aResponse[id];
jQuery('#h-img-'+id).hide();
}


}, 'json');
},


togSystem : function(aElement) {
var ul = jQuery(aElement).closest('li').find('ul');
jQuery(ul).toggle();
},


flash : function(aElement) {
        jQuery(aElement).fadeTo('fast', 0.1, function() {
            jQuery(this).fadeTo('fast', 1.0);
        });
},


updateView : function(aResponse) {
for (var id in aResponse) {
var row = aResponse[id];
var state = row['last_status'];
var update  = row['last_update'];


var elRow = jQuery('.hs'+id);
var elState = jQuery(elRow).find('.health-service-state');
if (elState.attr('data-state') != state) {
elState.attr('data-state', state);
var img = '03'; //green
            if ('warn' == state) img = '02';
            if ('error' == state) img = '01';
            if ('skip' == state) img = '00';
            jQuery('#h-img-'+id).attr('src','img/ico/16/flag-'+img+'.gif');
    Flow.health.flash(elRow);
}


var elDate = jQuery(elRow).find('.health-service-date');
if (elDate.text() != update) {
    elDate.text(update);
    Flow.health.flash(elDate);
}
//jQuery('#h-img-'+id).hide();
}
},


refresh : function() {
var url = 'index.php?act=health.refresh';
jQuery.post(url, function(aResponse) {
Flow.health.updateView(aResponse['states']);
jQuery('#health-messages').replaceWith(aResponse['msg']);
}, 'json');
},


runAll : function() {
var url = 'index.php?act=health.runall';
jQuery.post(url, function(aResponse) {
Flow.health.updateView(aResponse['states']);
jQuery('#health-messages').replaceWith(aResponse['msg']);
// Flow.health.flash('#health-messages');
}, 'json');
}

}