/* Scroll to Top
-------------------------------------------------- */
jQuery(document).ready(function(){
    jQuery(window).scroll(function(){
        if (jQuery(this).scrollTop() > 100) {
            jQuery('.scrollup').fadeIn();
        } else {
            jQuery('.scrollup').fadeOut();
    }
});
// scroll-to-top animate
jQuery('.scrollup').click(function(){
    jQuery("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
});


/* Tooltips
-------------------------------------------------- */
$('[data-toggle=tooltip]').tooltip({placement: 'bottom'});


/* Operation Accordion Toggle
-------------------------------------------------- */
function toggleOperation(e) {
	//toggle chevron
    $(e.target)
        .prev('.panel-heading')
        .find("span.panelhandle")
        .toggleClass('glyphicon-chevron-down glyphicon-chevron-right');
}
$('#accordion').on('hide.bs.collapse', toggleOperation);
$('#accordion').on('show.bs.collapse', toggleOperation);


/* Confirmed Operations Toggle
-------------------------------------------------- */
$('div.text-muted').fadeToggle(0, 'linear'); //hide "confirmed" operations
$('.toggleCnfCount').append($('div.text-muted').length+' '); //add count of hidden operations

//hide toggle if there are no confirmed operations
if ($('div.text-muted').length == 0) {
	$('a.toggleCnf').hide();
}

$('.toggleCnf').click(function (event) {
	event.preventDefault();
	$('div.text-muted').fadeToggle();
	$('.toggleCnfIcon').toggleClass('glyphicon-chevron-down glyphicon-chevron-right');
});