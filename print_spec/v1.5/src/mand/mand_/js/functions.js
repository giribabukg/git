
jQuery(document).ready( function() {
	jQuery(showFields('barcode_count'));
	document.getElementsByName('val[read_understood_instructions_op]')[0].disabled=true;
	
});


function combineFields(lAlias, lField) {
	var lVal = [];
	jQuery("input[name^='val["+lAlias+"']").each( function() {
		
		var lInp = jQuery(this);
		var lName = lInp.attr('name');
		if(lName.indexOf(lField) == -1 && lInp.val() !== '') {
			lVal.push(lInp.val());
		}
	});

	jQuery("*[name='val["+lField+"]']").val( lVal.join(", ") );
	
}

function showFields(lvalue) {
	//to show the barcode fields
	jQuery("table.bar1 tr").hide();
	var lfield = "val[" + lvalue + "]"
	var lnum = document.getElementsByName(lfield)[0].value;
		if(lnum != null) {
			for (i = 1; i <= lnum; i++) {
				var lhidefield = "table.bar1 tr.barcode" + i
				jQuery(lhidefield).show();
			}
		} else {
			//alert('none');
		}
}

function errorcollect(lError) {
	//alert('none');
	//var myValue = jQuery("*[name='val["+lError+"]']").val();
	var orgValue = jQuery("*[name='val[error_comments]']").val();
	var myValue = jQuery("*[name='val["+lError+"]']").val();
	var ltext = myValue.replace(new RegExp(",", "g"), '\n');
	var myValue = jQuery("*[name='val[error_comments]']").val(orgValue + '\n' +  ltext);
	//
	//var tisha = jQuery("*[data-val='ea1']").val();
	//alert(tisha);
}


