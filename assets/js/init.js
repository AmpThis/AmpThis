$(document).ready(function() {
	$('.SELECT').customSelect();
	
	$('#header h1').click(function() {
		var href = $(this).attr('title');
		window.location.href = href;
	});
	
	$('div.tooltip').hover(function() {
		$(this).find('div.details').toggle('slow');
	});
	

});

var bg = 60;

function IIR(sf, k) {
	return (k - 60) * sf;
}

function IVInsulinAdjustment(cbg, target_high, target_low, pbg, psf) {
	if(cbg > target_high && (cbg / pbg) > 0.85) {
		sf = psf * 1.25;
	}else if(cbg < target_low) {
		sf = psf * 0.8;
	} else{
		sf = psf;
	}	
	
	return sf;
}

function InitialSensitivityFactor(weight) {
	return 0.0002 * weight;	
}
