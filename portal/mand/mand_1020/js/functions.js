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
	
function OpenInNewTab(url) {
	  var url = "http://10.207.7.16/core/?id="
		  var service_id = jq("input[name^='val[service_order_id']").val();
	  var win = window.open(url + service_id, '_blank');
	  win.focus();
	}