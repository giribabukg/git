var jq = jQuery.noConflict();

jq(document).ready( function(){ // When page loads
	
	measure();
	displaySubs();
	labPrev();
	labPrevPrint();
	mTarget();
	yTarget();
	bTarget();
	mSample();
	ySample();
	bSample();
	
	jq("*[name='val[measuretype]']").change( function(){ measure(); });
	jq("*[name='val[printingprocess]']").change( function(){ printprocess(); });
	jq("*[name='val[number_sub]']").change( function(){ displaySubs(); });
	//jq("*[name='val[bwr1]']").change('input', function() {bwr1();});
	//jq("*[name='val[bwr2]']").change('input', function() {bwr2();});
	//jq("*[name='val[bwr3]']").change('input', function() {bwr3();});
	//jq("*[name='val[bwr4]']").change('input', function() {bwr4();});
	
	// Hide and show Results
	jq(".p8").show();
	jq(".hm").show();
	jq(".flx").hide();
	jq(".gra").hide();
	jq(".lit").hide();
	jq("#1994G").hide();
	jq("#1994T").hide();
	jq("#1976").hide();
	//jq("#2000").hide();
	jq("#cmc1").hide();
	jq("#cmc").hide();
	
	jq("*[name='val[hidbar]']").hide();
	jq("*[name='val[hidprinting]']").hide();
	jq("*[name='val[hidproof]']").hide();
	jq("*[name='val[number_sub]']").hide();
	
	addbarcodeview();
	addprintingview();
	addproofview();
	printprocess();

});

function measure(){
	var measure_type = jq("*[name='val[measuretype]']").val();
	jq(".measure span").text(measure_type);
}

function printprocess(){
	var print = jq("*[name='val[printingprocess]']").val();
	
	if(print == "Flexo Flexible Packaging"){
		jq( ".flexo" ).show();
		jq(".corr").hide();
	}
	
	if(print == "Flexo Corrugated"){
		jq( ".flexo" ).hide();
		jq( ".corr" ).show();
	}
	
	if(print == ""){
		jq( ".flexo" ).hide();
		jq( ".corr" ).hide();
	}
	
	if(print == "All"){
		jq( ".flexo" ).show();
		jq( ".corr" ).show();
	}
	
	if(print == ""){
		jq( ".flexo" ).show();
		jq( ".corr" ).show();
	}
	
	/*if(print == "Flexo Flexible Packaging" || print == "Preprint"){
		jq( ".flexo" ).show();
		jq( ".corr" ).hide();
	}else{
		jq( ".flexo" ).hide();
		jq( ".corr" ).hide();
	}
	
	if(print == "Flexo Corrugated"){
		jq( ".flexo" ).show();
		jq( ".corr" ).show();
	}
	
	*/
}

function displaySubs(res){
	
	if((typeof res === "undefined") || (typeof res === "NaN")){
		jq(".sub02").hide();
		jq(".sub03").hide();
		jq(".sub04").hide();
		jq(".sub05").hide();
		jq(".sub06").hide();
		jq(".sub07").hide();
		jq(".sub08").hide();
		jq(".sub09").hide();
		jq(".sub10").hide();
		jq(".sub11").hide();
		jq(".sub12").hide();
		jq(".sub13").hide();
		jq(".sub14").hide();
		jq(".sub15").hide();
	}
	
	if(res == 2){
		jq(".sub02").show();
	}
	if(res == 3){
		jq(".sub02").show();jq(".sub03").show();
	}
	if(res == 4){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();
	}
	if(res == 5){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
	}
	if(res == 6){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();
	}
	if(res == 7){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();
	}
	if(res == 8){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();
	}
	if(res == 9){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
	}
	if(res == 10){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();
	}
	if(res == 11){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();jq(".sub11").show();
	}
	if(res == 12){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();jq(".sub11").show();jq(".sub12").show();
	}
	if(res == 13){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();jq(".sub11").show();jq(".sub12").show();jq(".sub13").show();
	}
	if(res == 14){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();jq(".sub11").show();jq(".sub12").show();jq(".sub13").show();
		jq(".sub14").show();
	}
	if(res == 15){
		jq(".sub02").show();jq(".sub03").show();jq(".sub04").show();jq(".sub05").show();
		jq(".sub06").show();jq(".sub07").show();jq(".sub08").show();jq(".sub09").show();
		jq(".sub10").show();jq(".sub11").show();jq(".sub12").show();jq(".sub13").show();
		jq(".sub14").show();jq(".sub15").show();
	}
}

function subAdd(){
	
	var lang_num = jq("*[name='val[number_sub]']").val();
	if(lang_num == "NaN" || lang_num ==""){
		lang_num = 1;
	}
	var res = parseInt(lang_num) + 1; 
	jq("*[name='val[number_sub]']").val(res);
	displaySubs(res);
}

 //Cyan LAB Target
	labPrev = function() {
		var LabL1 = jq("*[name='val[chkl]']").val();
		var Laba1 = jq("*[name='val[chka]']").val();
		var Labb1 = jq("*[name='val[chkb]']").val();
		
		var cleanL = Number(LabL1);
		var cleanA = Number(Laba1);
		var cleanB = Number(Labb1);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;
		
		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R; //* 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);

		jq("#stand_prev").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
		
		var result = jq("*[name='val[decyan]']").val();
		if(result != ""){
			if(result > 2){
				jq('#img').attr('src', 'mand/mand_3/img/ico/fail.gif');
			}
			if(result < 2){
				jq('#img').attr('src', 'mand/mand_3/img/ico/pass.gif');
			}
		}
	};


//Magenta LAB Target
	mTarget = function() {
		var LabL1M = jq("*[name='val[mtl]']").val();
		var Laba1M = jq("*[name='val[mta]']").val();
		var Labb1M = jq("*[name='val[mtb]']").val();
		
		var cleanL = Number(LabL1M);
		var cleanA = Number(Laba1M);
		var cleanB = Number(Labb1M);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;
		
		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);

		jq("#magentaTraget").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
		
		var result = jq("*[name='val[demag]']").val();
		if(result != ""){
			if(result > 2){
				jq('#imgmag').attr('src', 'mand/mand_3/img/ico/fail.gif');
			}
			if(result < 2){
				jq('#imgmag').attr('src', 'mand/mand_3/img/ico/pass.gif');
			}
		}
	};


 //Yellow LAB Target
	yTarget = function() {
		var LabL1Y = jq("*[name='val[ytl]']").val();
		var Laba1Y = jq("*[name='val[yta]']").val();
		var Labb1Y = jq("*[name='val[ytb]']").val();
		
		var cleanL = Number(LabL1Y);
		var cleanA = Number(Laba1Y);
		var cleanB = Number(Labb1Y);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;
		
		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);

		jq("#yellowTraget").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
		
		var result = jq("*[name='val[deyel]']").val();
		if(result != ""){
			if(result > 2){
				jq('#imgyel').attr('src', 'mand/mand_3/img/ico/fail.gif');
			}
			if(result < 2){
				jq('#imgyel').attr('src', 'mand/mand_3/img/ico/pass.gif');
			}
		}
	};


 //Black LAB Target
	bTarget = function() {
		var LabL1B = jq("*[name='val[btl]']").val();
		var Laba1B = jq("*[name='val[bta]']").val();
		var Labb1B = jq("*[name='val[btb]']").val();
		
		var cleanL = Number(LabL1B);
		var cleanA = Number(Laba1B);
		var cleanB = Number(Labb1B);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;
		
		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);

		jq("#blackTraget").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
		
		var result = jq("*[name='val[debla]']").val();
		if(result != ""){
			if(result > 2){
				jq('#imgbla').attr('src', 'mand/mand_3/img/ico/fail.gif');
			}
			if(result < 2){
				jq('#imgbla').attr('src', 'mand/mand_3/img/ico/pass.gif');
			}
		}
	};


 //Cyan LAB Sample
	labPrevPrint = function() {
		var LabL1 = jq("*[name='val[printl]']").val();
		var Laba1 = jq("*[name='val[printa]']").val();
		var Labb1 = jq("*[name='val[printb]']").val();
		
		var cleanL = Number(LabL1);
		var cleanA = Number(Laba1);
		var cleanB = Number(Labb1);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;

		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}
		
		var R = var_R; //* 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);
		
		jq("#print_prev").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
	};


 //Magenta LAB Sample
	mSample = function() {
		var LabL1MS = jq("*[name='val[msl]']").val();
		var Laba1MS = jq("*[name='val[msa]']").val();
		var Labb1MS = jq("*[name='val[msb]']").val();
		
		var cleanL = Number(LabL1MS);
		var cleanA = Number(Laba1MS);
		var cleanB = Number(Labb1MS);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;

		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);
		
		jq("#magentaSample").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
	};


 //Yellow LAB Sample
	ySample = function() {
		var LabL1YS = jq("*[name='val[ysl]']").val();
		var Laba1YS = jq("*[name='val[ysa]']").val();
		var Labb1YS = jq("*[name='val[ysb]']").val();
		
		var cleanL = Number(LabL1YS);
		var cleanA = Number(Laba1YS);
		var cleanB = Number(Labb1YS);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;

		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);
		
		jq("#yellowSample").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
	};


 //Black LAB Sample
	bSample = function() {
		var LabL1BS = jq("*[name='val[bsl]']").val();
		var Laba1BS = jq("*[name='val[bsa]']").val();
		var Labb1BS = jq("*[name='val[bsb]']").val();
		
		var cleanL = Number(LabL1BS);
		var cleanA = Number(Laba1BS);
		var cleanB = Number(Labb1BS);
		
		var var_Y = (cleanL + 16) / 116;
		var var_X = (cleanA / 500) + var_Y;
		var var_Z = var_Y - cleanB / 200;

		if(var_Y > 0.008856){
			var_Y = Math.pow(var_Y,(3.0));
		} else {
			var_Y = (var_Y - 16 / 116) / 7.787;
		}

		if(var_X > 0.008856){
			var_X = Math.pow(var_X,(3.0));
		} else {
			var_X = (var_X - 16 / 116) / 7.787;
		}

		if(var_Z > 0.008856){
			var_Z = Math.pow(var_Z,(3.0));
		} else {
			var_Z = (var_Z - 16 / 116) / 7.787;
		}

		var X = var_X * 95.047;
		var Y = var_Y * 100;
		var Z = var_Z * 108.883;

		//Convert XYZ to RGB

		X = X /100;
		Y = Y /100;
		Z = Z /100;

		var var_R = X *  3.2406 + Y * -1.5372 + Z * -0.4986;
		var var_G = X * -0.9689 + Y *  1.8758 + Z *  0.0415;
		var var_B = X *  0.0557 + Y * -0.2040 + Z *  1.0570;

		if (var_R > 0.0031308){
			var_R = 1.055 * (Math.pow(var_R,(1.0/2.4)) - 0.055);
		} else {
			var_R = 12.92 * var_R;
		}

		if (var_G > 0.0031308){
			var_G = 1.055 * (Math.pow(var_G,(1.0/2.4)) - 0.055);
		} else {
			var_G = 12.92 * var_G;
		}

		if (var_B > 0.0031308){
			var_B = 1.055 * (Math.pow(var_B,(1.0/2.4)) - 0.055);
		} else {
			var_B = 12.92 * var_B;
		}

		var R = var_R * 255;
		var G = var_G * 255;
		var B = var_B * 255;
		
		var fR = R.toFixed(0);
		var fG = G.toFixed(0);
		var fB = B.toFixed(0);
		
		//alert( rgbToHex(46, 64, 41) );
		 var testR = Number(fR);
		 var testG = Number(fG);
		 var testB = Number(fB);
		 
		 var doneR =  Math.abs(testR);
		 var doneG =  Math.abs(testG);
		 var doneB =  Math.abs(testB);
		
		jq("#blackSample").css("background-color", "rgb(" + doneR + "," + doneG +"," + doneB + ")");
	};


//Add Barcode View
    addbarcodeview = function() {
    	jq(".barcode1").hide();
        jq(".barcode2").hide();
        
        var numberbar = jq("*[name='val[hidbar]']").val();
        
        if (!numberbar) {
        	numberbar = 0;
            jq("*[name='val[hidbar]']").val(numberbar);
        }

		if(numberbar == 1){
			jq(".barcode1").show();
		}

		if(numberbar == 2){
			jq(".barcode1").show();
			jq(".barcode2").show();
		}
	};


//Add Barcode Calc
	addbarcode = function() {
		var numberbarold = jq("*[name='old[hidbar]']").val();
		var numberbar = jq("*[name='val[hidbar]']").val();
	
		if (numberbar <= 2){
			numberbar = parseInt(numberbar) + 1;
			jq("*[name='old[hidbar]']").val(numberbarold);
			jq("*[name='val[hidbar]']").val(numberbar);
			jq(".barcode" + (numberbar)).show();
			//alert(".barcode" + numberbar);
		}
	
		if (numberbar >= 2){
			jq("*[name='val[hidbar]']").val(2);
		}
	};
	
//Remove Barcode
	removebarcode = function() {
		var numberbarold = jq("*[name='old[hidbar]']").val();
		var numberbar = jq("*[name='val[hidbar]']").val();
		
		if (confirm("Are you sure that you want Permanently delete the last Entry?") == true){
			if (numberbar <= 2){
				numberbar = parseInt(numberbar) - 1;
				jq("*[name='old[hidbar]']").val(numberbarold);
				jq("*[name='val[hidbar]']").val(numberbar);
				jq(".barcode" + (numberbar + 1)).hide();
				//alert(".barcode" + numberbar);
					//clear
					if(numberbar + 1 == 1){
						//Barcode 3
						jq("*[name='val[codetype2]']").val("");
						jq("*[name='val[minmag4]']").val("");
						jq("*[name='val[minmag5]']").val("");
						jq("*[name='val[bwr5]']").val("");
						jq("*[name='val[bwr6]']").val("");
						jq("*[name='val[barcoderesolution2]']").val("");
						jq("*[name='val[compensation2]']").val("");
						jq("*[name='val[narrowbar2]']").val("");
						jq("*[name='val[ratio2]']").val("");
						jq("*[name='val[barcodenote2]']").val("");
						
						//barcode4
						jq("*[name='val[codetype3]']").val("");
						jq("*[name='val[minmag6]']").val("");
						jq("*[name='val[minmag7]']").val("");
						jq("*[name='val[bwr7]']").val("");
						jq("*[name='val[bwr8]']").val("");
						jq("*[name='val[barcoderesolution3]']").val("");
						jq("*[name='val[compensation3]']").val("");
						jq("*[name='val[narrowbar3]']").val("");
						jq("*[name='val[ratio3]']").val("");
						jq("*[name='val[barcodenote3]']").val("");						
					}
					
					if(numberbar + 1 == 2){
						//Barcode 5
						jq("*[name='val[codetype4]']").val("");
						jq("*[name='val[minmag8]']").val("");
						jq("*[name='val[minmag9]']").val("");
						jq("*[name='val[bwr9]']").val("");
						jq("*[name='val[bwr10]']").val("");
						jq("*[name='val[barcoderesolution4]']").val("");
						jq("*[name='val[compensation4]']").val("");
						jq("*[name='val[narrowbar4]']").val("");
						jq("*[name='val[ratio4]']").val("");
						jq("*[name='val[barcodenote4]']").val("");
						
						//Barcode 6
						jq("*[name='val[codetype5]']").val("");
						jq("*[name='val[minmag10]']").val("");
						jq("*[name='val[minmag11]']").val("");
						jq("*[name='val[bwr11]']").val("");
						jq("*[name='val[bwr12]']").val("");
						jq("*[name='val[barcoderesolution5]']").val("");
						jq("*[name='val[compensation5]']").val("");
						jq("*[name='val[narrowbar5]']").val("");
						jq("*[name='val[ratio5]']").val("");
						jq("*[name='val[barcodenote5]']").val("");
					}
			}
	
			if (numberbar <= 0){
				jq("*[name='val[hidbar]']").val(0);
			}
			
		} else {
						
		}
	};


 //Add Printing View
	addprintingview = function() {

	jq(".printing1").hide();
	jq(".printing2").hide();
	jq(".printing3").hide();
	jq(".printing4").hide();
	jq(".printing5").hide();
		
		var numberpri = jq("*[name='val[hidprinting]']").val();

		if (!numberpri) {
                        numberpri = 0;
			jq("*[name='val[hidprinting]']").val(numberpri);
		}

		if(numberpri == 1){
			jq(".printing1").show();
		}

		if(numberpri == 2){
			jq(".printing1").show();
			jq(".printing2").show();
		}

		if(numberpri == 3){
			jq(".printing1").show();
			jq(".printing2").show();
			jq(".printing3").show();
		}
		
		if(numberpri == 4){
			jq(".printing1").show();
			jq(".printing2").show();
			jq(".printing3").show();
			jq(".printing4").show();
		}
		
		if(numberpri == 5){
			jq(".printing1").show();
			jq(".printing2").show();
			jq(".printing3").show();
			jq(".printing4").show();
			jq(".printing5").show();
		}
	};

	
	 //Add Printing Calc
	addprinting = function() {
		var numberpriold = jq("*[name='old[hidprinting]']").val();
		var numberpri = jq("*[name='val[hidprinting]']").val();

		if(numberpri <= 5){
			numberpri = parseInt(numberpri) + 1;
            jq("*[name='old[hidprinting]']").val(numberpriold);
            jq("*[name='val[hidprinting]']").val(numberpri);
            jq(".printing" + numberpri).show();
		}

		if(numberpri >= 5){
                        jq("*[name='val[hidprinting]']").val(5);
		}
	};
	
	//Remove Barcode
	removeprinting = function() {
		var numberpriold = jq("*[name='old[hidprinting]']").val();
		var numberpri = jq("*[name='val[hidprinting]']").val();
		
		if (confirm("Are you sure that you want Permanently delete the last Entry?") == true){
			if (numberpri <= 5){
				numberpri = parseInt(numberpri) - 1;
				jq("*[name='old[hidprinting]']").val(numberpriold);
				jq("*[name='val[hidprinting]']").val(numberpri);
				jq(".printing" + (numberpri + 1)).hide();
					//clear
					if(numberpri + 1 == 1){
						//Printing 1
						jq("*[name='val[plateset1]']").val("");
						jq("*[name='val[platetype1]']").val("");
						jq("*[name='val[platetype_for1]']").val("");
						jq("*[name='val[reliefdepth1]']").val("");
						jq("*[name='val[dotplate1]']").val("");
						jq("*[name='val[diganal1]']").val("");
						jq("*[name='val[flattop1]']").val("");
						jq("*[name='val[platedgc1]']").val("");
						jq("*[name='val[filmheightfixed1]']").val("");
						jq("*[name='val[preferredlinescreen1]']").val("");
						jq("*[name='val[requested_to_high1]']").val("");
						jq("*[name='val[platethickness1]']").val("");
						jq("*[name='val[mountingsheet1]']").val("");
						jq("*[name='val[stickyback1]']").val("");
						jq("*[name='val[foam1]']").val("");
						jq("*[name='val[totalhigh1]']").val("");
						jq("*[name='val[distortion1]']").val("");
						jq("*[name='val[dotshape1]']").val("");
						jq("*[name='val[resolution1]']").val("");					
					}
					
					if(numberpri + 1 == 2){
						//Printing 2
						jq("*[name='val[plateset2]']").val("");
						jq("*[name='val[platetype2]']").val("");
						jq("*[name='val[platetype_for2]']").val("");
						jq("*[name='val[reliefdepth2]']").val("");
						jq("*[name='val[dotplate2]']").val("");
						jq("*[name='val[diganal2]']").val("");
						jq("*[name='val[flattop2]']").val("");
						jq("*[name='val[platedgc2]']").val("");
						jq("*[name='val[filmheightfixed2]']").val("");
						jq("*[name='val[preferredlinescreen2]']").val("");
						jq("*[name='val[requested_to_high2]']").val("");
						jq("*[name='val[platethickness2]']").val("");
						jq("*[name='val[mountingsheet2]']").val("");
						jq("*[name='val[stickyback2]']").val("");
						jq("*[name='val[foam2]']").val("");
						jq("*[name='val[totalhigh2]']").val("");
						jq("*[name='val[distortion2]']").val("");
						jq("*[name='val[dotshape2]']").val("");
						jq("*[name='val[resolution2]']").val("");					
					}
					if(numberpri + 1 == 3){
						//Printing 3
						jq("*[name='val[plateset3]']").val("");
						jq("*[name='val[platetype3]']").val("");
						jq("*[name='val[platetype_for3]']").val("");
						jq("*[name='val[reliefdepth3]']").val("");
						jq("*[name='val[dotplate3]']").val("");
						jq("*[name='val[diganal3]']").val("");
						jq("*[name='val[flattop3]']").val("");
						jq("*[name='val[platedgc3]']").val("");
						jq("*[name='val[filmheightfixed3]']").val("");
						jq("*[name='val[preferredlinescreen3]']").val("");
						jq("*[name='val[requested_to_high3]']").val("");
						jq("*[name='val[platethickness3]']").val("");
						jq("*[name='val[mountingsheet3]']").val("");
						jq("*[name='val[stickyback3]']").val("");
						jq("*[name='val[foam3]']").val("");
						jq("*[name='val[totalhigh3]']").val("");
						jq("*[name='val[distortion3]']").val("");
						jq("*[name='val[dotshape3]']").val("");
						jq("*[name='val[resolution3]']").val("");					
					}
					if(numberpri + 1 == 4){
						//Printing 4
						jq("*[name='val[plateset4]']").val("");
						jq("*[name='val[platetype4]']").val("");
						jq("*[name='val[platetype_for4]']").val("");
						jq("*[name='val[reliefdepth4]']").val("");
						jq("*[name='val[dotplate4]']").val("");
						jq("*[name='val[diganal4]']").val("");
						jq("*[name='val[flattop4]']").val("");
						jq("*[name='val[platedgc4]']").val("");
						jq("*[name='val[filmheightfixed4]']").val("");
						jq("*[name='val[preferredlinescreen4]']").val("");
						jq("*[name='val[requested_to_high4]']").val("");
						jq("*[name='val[platethickness4]']").val("");
						jq("*[name='val[mountingsheet4]']").val("");
						jq("*[name='val[stickyback4]']").val("");
						jq("*[name='val[foam4]']").val("");
						jq("*[name='val[totalhigh4]']").val("");
						jq("*[name='val[distortion4]']").val("");
						jq("*[name='val[dotshape4]']").val("");
						jq("*[name='val[resolution4]']").val("");					
					}
					if(numberpri + 1 == 5){
						//Printing 5
						jq("*[name='val[plateset5]']").val("");
						jq("*[name='val[platetype5]']").val("");
						jq("*[name='val[platetype_for5]']").val("");
						jq("*[name='val[reliefdepth5]']").val("");
						jq("*[name='val[dotplate5]']").val("");
						jq("*[name='val[diganal5]']").val("");
						jq("*[name='val[flattop5]']").val("");
						jq("*[name='val[platedgc5]']").val("");
						jq("*[name='val[filmheightfixed5]']").val("");
						jq("*[name='val[preferredlinescreen5]']").val("");
						jq("*[name='val[requested_to_high5]']").val("");
						jq("*[name='val[platethickness5]']").val("");
						jq("*[name='val[mountingsheet5]']").val("");
						jq("*[name='val[stickyback5]']").val("");
						jq("*[name='val[foam5]']").val("");
						jq("*[name='val[totalhigh5]']").val("");
						jq("*[name='val[distortion5]']").val("");
						jq("*[name='val[dotshape5]']").val("");
						jq("*[name='val[resolution5]']").val("");					
					}
					
			}
	
			if (numberpri <= 0){
				jq("*[name='val[hidprinting]']").val(0);
			}
			
		} else {
						
		}
	};

	
	

	 //Add Proof View
	addproofview = function() {

		jq(".proof1").hide();
		jq(".proof2").hide();
		jq(".proof3").hide();
		jq(".proof4").hide();
		jq(".proof5").hide();
		
		var numberpro = jq("*[name='val[hidproof]']").val();

		if (!numberpro) {
			numberpro = 0;
			jq("*[name='val[hidproof]']").val(numberpro);
		}

		if(numberpro == 1){
			jq(".proof1").show();
		}

		if(numberpro == 2){
			jq(".proof1").show();
			jq(".proof2").show();
		}

		if(numberpro == 3){
			jq(".proof1").show();
			jq(".proof2").show();
			jq(".proof3").show();
		}
		
		if(numberpro == 4){
			jq(".proof1").show();
			jq(".proof2").show();
			jq(".proof3").show();
			jq(".proof4").show();
		}
		
		if(numberpro == 5){
			jq(".proof1").show();
			jq(".proof2").show();
			jq(".proof3").show();
			jq(".proof4").show();
			jq(".proof5").show();
		}
	};

 //Add Proof Calc
	addproof = function() {
		var numberproold = jq("*[name='old[hidproof]']").val();
		var numberpro = jq("*[name='val[hidproof]']").val();

		if(numberpro <= 5){
	        numberpro = parseInt(numberpro) + 1;
	        jq("*[name='old[hidproof]']").val(numberproold);
	        jq("*[name='val[hidproof]']").val(numberpro);
	        jq(".proof" + numberpro).show();
		}

		if(numberpro >= 5){
			jq("*[name='val[hidproof]']").val(5);
		}
	};
	
	//Remove Proof
	removeproof = function() {
		var numberproold = jq("*[name='old[hidproof]']").val();
		var numberpro = jq("*[name='val[hidproof]']").val();
		
		if (confirm("Are you sure that you want Permanently delete the last Entry?") == true){
			if (numberpro <= 5){
				numberpro = parseInt(numberpro) - 1;
				jq("*[name='old[hidproof]']").val(numberproold);
				jq("*[name='val[hidproof]']").val(numberpro);
				jq(".proof" + (numberpro + 1)).hide();
					//clear
					if(numberpro + 1 == 1){
						//Proof 1				
						jq("*[name='val[proofprocess1]']").val("");
						jq("*[name='val[proofsize1]']").val("");
						jq("*[name='val[legend1]']").val("");
						jq("*[name='val[profilename1]']").val("");
						jq("*[name='val[shipping1]']").val("");
						jq("*[name='val[fingerprintavailable1]']").val("");
						jq("*[name='val[proofnotes1]']").val("");
						jq("*[name='val[proofaddress1]']").val("");
					}
					if(numberpro + 1 == 2){
						//Proof 2
						jq("*[name='val[proofprocess2]']").val("");
						jq("*[name='val[proofsize2]']").val("");
						jq("*[name='val[legend2]']").val("");
						jq("*[name='val[profilename2]']").val("");
						jq("*[name='val[shipping2]']").val("");
						jq("*[name='val[fingerprintavailable2]']").val("");
						jq("*[name='val[proofnotes2]']").val("");
						jq("*[name='val[proofaddress2]']").val("");						
					}
					
					if(numberpro + 1 == 3){
						//Proof 3
						jq("*[name='val[proofprocess3]']").val("");
						jq("*[name='val[proofsize3]']").val("");
						jq("*[name='val[legend3]']").val("");
						jq("*[name='val[profilename3]']").val("");
						jq("*[name='val[shipping3]']").val("");
						jq("*[name='val[fingerprintavailable3]']").val("");
						jq("*[name='val[proofnotes3]']").val("");
						jq("*[name='val[proofaddress3]']").val("");					
					}
					if(numberpro + 1 == 4){
						//Proof 4
						jq("*[name='val[proofprocess4]']").val("");
						jq("*[name='val[proofsize4]']").val("");
						jq("*[name='val[legend4]']").val("");
						jq("*[name='val[profilename4]']").val("");
						jq("*[name='val[shipping4]']").val("");
						jq("*[name='val[fingerprintavailable4]']").val("");
						jq("*[name='val[proofnotes4]']").val("");
						jq("*[name='val[proofaddress4]']").val("");					
					}
					
					if(numberpro + 1 == 5){
						//Barcode 5
						jq("*[name='val[proofprocess5]']").val("");
						jq("*[name='val[proofsize5]']").val("");
						jq("*[name='val[legend5]']").val("");
						jq("*[name='val[profilename5]']").val("");
						jq("*[name='val[shipping5]']").val("");
						jq("*[name='val[fingerprintavailable5]']").val("");
						jq("*[name='val[proofnotes5]']").val("");
						jq("*[name='val[proofaddress5]']").val("");
					}
			}
	
			if (numberpro <= 0){
				jq("*[name='val[hidproof]']").val(0);
			}
			
		} else {
						
		}
	};



 //Number Range Validation - BWR 1
	bwr1 = function() {
		var bwr = jq("*[name='val[bwr1]']").val();
		var number = parseInt(bwr);
		if((number < 10) || (number > 200)){
			alert("Please make sure the values are between 10 and 200");
			jq("*[name='val[bwr1]']").css({ "border": '#FF0000 1px solid'});
		}else{
			jq("*[name='val[bwr1]']").css({ "border": '#659D32 1px solid'});
		}
	};


 //Number Range Validation - BWR 2
	bwr2 = function() {
		var bwr = jq("*[name='val[bwr2]']").val();
		var number = parseInt(bwr);
		if((number < 10) || (number > 200)){
			alert("Please make sure the values are between 10 and 200");
			jq("*[name='val[bwr2]']").css({ "border": '#FF0000 1px solid'});
		}else{
			jq("*[name='val[bwr2]']").css({ "border": '#659D32 1px solid'});
		}
	};


 //Number Range Validation - BWR 3
	bwr3 = function() {
		var bwr = jq("*[name='val[bwr3]']").val();
		var number = parseInt(bwr);
		if((number < 10) || (number > 200)){
			alert("Please make sure the values are between 10 and 200");
			jq("*[name='val[bwr3]']").css({ "border": '#FF0000 1px solid'});
		}else{
			jq("*[name='val[bwr3]']").css({ "border": '#659D32 1px solid'});
		}
	};


 //Number Range Validation - BWR 4
	bwr4 = function() {
		var bwr = jq("*[name='val[bwr4]']").val();
		var number = parseInt(bwr);
		if((number < 10) || (number > 200)){
			alert("Please make sure the values are between 10 and 200");
			jq("*[name='val[bwr4]']").css({ "border": '#FF0000 1px solid'});
		}else{
			jq("*[name='val[bwr4]']").css({ "border": '#659D32 1px solid'});
		}
	};






