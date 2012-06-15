<?php include 'application/views/global/header.php'; ?>
<div id="content" class="WRAP">
<?php include 'application/views/global/toolbar.php'; ?>
    <div id="tabs">
    	<ul>
        	<li class="current"><a href="#" class="iv">IV Insulin Infusion Patients (<?= $iv_count; ?>)</a><div class="tab_arrow"></div></li>
            <li><a href="#" class="subq">Subcut. Insulin Injection Patients (<?= $subq_count; ?>)</a><div class="tab_arrow"></div></li>
        </ul>
        <div class="CLEARFIX"></div>
    </div>
    <div class="section" id="iv">
    	<? foreach($iv_patients as $patient) : ?>
        	<div class="patient" title="patients_details_iv_<?= $patient->AccountNumber; ?>" id="patient_<?= $patient->PatientID; ?>">
                <div class="patient_header">
                    <ul>
                        <li class="name"><?= $patient->LastName; ?>, <?= $patient->FirstName; ?></li>
                        <li class="id"><span>ID: </span><?= $patient->AccountNumber; ?></li>
                        <li class="room"><span>ROOM: </span><?= $patient->RoomNumber; ?></li>
                        <li class="dob"><span>DOB: </span><?= $patient->Birthday; ?></li>
                        <li class="bg">Next BG Due:</li>
                    </ul>
                    <div class="CLEARFIX"></div>
                </div>
                <div class="patient_info">
                	<ul>
                    	<li><span>LastBG:</span> </li>
                        <li><span>Last Insulin Rate:</span> <?= $patient->InsulinConcentration; ?> units/hr</li>
                    </ul>
                    <ul>
                    	<li><span>Target Range:</span> <?= $patient->TargetLow; ?>-<?= $patient->TargetHigh; ?> mg/dl</li>
                        <li><span>Next BG Due:</span> <?= $patient->NextBGAt; ?></li>
                    </ul>
                    <div class="CLEARFIX"></div>
                </div>
                <div class="msg iv msg_<?= $patient->divid; ?>"><div class="pulse countdown_<?= $patient->divid; ?>"></div></div>
          	<?
			
				$date_time = explode(' ', $patient->NextBGAt);
				
				$date = date('Y-n-j H:i:s', strtotime($patient->NextBGAt));
				$now = date('Y-n-j H:i:s');
			?>
			<script type="text/javascript">
                <?  if($date > $now) { ?>
                    $(document).ready(function() {
						$('div.msg_<?= $patient->divid; ?>').countdown({until:new Date(
							"<?= date('Y', strtotime($date)); ?>",
							"<?= date('n', strtotime($date)); ?>" - 1,
							"<?= date('j', strtotime($date)); ?>",
							"<?= date('H', strtotime($date)); ?>",
							"<?= date('i', strtotime($date)); ?>",
							"<?= date('s', strtotime($date)); ?>"), 
							serverSync:serverTime, 
							onExpiry:TimesUp}
						);
                    });
                <? }else { ?>
                    $(document).ready(function() {
                        $('div.countdown_<?= $patient->divid; ?>').text('BG DUE!');
						$('div#patient_<?= $patient->PatientID; ?>').addClass('alert');
                    });
                <? } ?>         
				
				$('div.msg_<?= $patient->divid; ?>').click(function() {
					var patient_details = new Array();
					patient_details['name'] = '<?= $patient->LastName . ' ' . $patient->FirstName; ?>';
					patient_details['id'] = '<?= $patient->divid; ?>';
					patient_details['account_number'] = '<?= $patient->AccountNumber; ?>';
					patient_details['target_high'] = '<?= $patient->TargetHigh; ?>';
					patient_details['target_low'] = '<?= $patient->TargetLow; ?>';
					patient_details['previous_bg'] = '<?= $patient->BGValue; ?>';
					patient_details['previous_sf'] = '<?= $patient->SensitivityFactor; ?>';
					patient_details['weight'] = '<?= $patient->Weight; ?>';
					
					ShowBGForm(patient_details);
				})
				   
            </script>
                <div class="pediatric_icon">&nbsp;</div>
            </div>
        <? endforeach; ?>
    </div>
    <div class="section" id="subq" style="display:none;">
    	<? foreach($subq_patients as $patient) : ?>
        	<div class="patient <?= $patient->class; ?>" title="patients_details_subq_<?= $patient->divid; ?>">
                <div class="patient_header">
                    <ul>
                        <li class="name"><?= $patient->LastName; ?>, <?= $patient->Firstname; ?></li>
                        <li class="id"><span>ID: </span><?= $patient->PatientHash; ?></li>
                        <li class="room"><span>ROOM: </span><?= $patient->RoomNum; ?> <?= $patient->HospitalUnit; ?></li>
                        <li class="dob"><span>DOB: </span><?= $patient->Birthday; ?></li>
                        <li class="bg">Next BG Due:</li>
                    </ul>
                    <div class="CLEARFIX"></div>
                </div>
                <div class="patient_info">
                    <ul>
                        <li><span>Basal Insulin:</span> <? //= $patient->BasalDose; ?></li>
                        <li><span>Insulin Type:</span> <? //= $patient->InsulinType; ?></li>
                    </ul>
                    <ul>
                        <li><span>Last BG:</span> <? //= $patient->BGValue; ?> mg/dl</li>
                        <li><span>BG Type</span> <? //= $patient->CheckType; ?></li>
                    </ul>
                    <ul>
                        <li><span>Basal Dose:</span> <?//= $patient->BasalDose; ?></li>
                    </ul>
                    <div class="CLEARFIX"></div>
                </div>
                <div class="msg iv"><div class="pulse">Lunch</div></div>
                <div class="pediatric_icon">&nbsp;</div>
            </div>
    	<? endforeach; ?>
    </div>
        <div id="navigate" class="homepage_paginate">
            <select id="filter" class="SELECT filter">
            	<option value="--">Filter</option>
            	<option value="5">5</option>
            	<option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
                <option value="60">60</option>
                <option value="70">70</option>
                <option value="80">80</option>
                <option value="90">90</option>
                <option value="100">100</option>
            </select>
        </div> 

</div>
<script type="text/javascript">
	//hide all but 8 patients
	pagingNav(10);
	
	$(document).ready(function() {
		
			
		$('#filter').change(function() {
			var value = $(this).val();
			$('ul#pagination').remove();
			pagingNav(value);
		});
		
		//switching content
		$('div#tabs ul li a').click(function() {
			$(this).parent().siblings().removeClass('current');
			$(this).parent().addClass('current');
			
			var content_div = $(this).attr('class');
			$('div.section').hide();
			
			$('#'+content_div).animate({
				opacity:'toggle'
			}, 1000);
		});
		
		$('div#iv div.alert div.msg').empty();
		$('div#iv div.alert div.msg').html('<span>BG DUE!</span>');
		
		$('div.patient_header, div.patient_info').click(function() {
			var id = $(this).parent().attr('title');
			var url = id.replace(/[_\W]+/g, "/");
			
			var href = 'http://' + document.location.hostname + '/' + url;
			
			document.location.href = href;
	
		});
		$('div.alert div.msg').illuminate({
			'intensity': '1',
			'color': '#de0900',
			'blink': 'true',
			'blinkSpeed': '550',
		});	

	});
	
	function TimesUp() {
		$(this).parent().addClass('alert');
		$(this).empty();
		$(this).html('BG DUE!');
		$(this).illuminate({
			'intensity': '1',
			'color': '#de0900',
			'blink': 'true',
			'blinkSpeed': '550',
		});	
	}
	
	function pagingNav($count) {
		$('#iv').easyPaginate({step:$count});	
	}
	
	function ShowBGForm(patient_details) {
		//fill hidden elements that will pass to the controller
		$('div#enter_bg_form div.popup_content form').find('input#patient_id_form').attr('value', patient_details['id']);
		$('div#enter_bg_form div.popup_content form').find('input#target_high_form').attr('value', patient_details['target_high']);
		$('div#enter_bg_form div.popup_content form').find('input#target_low_form').attr('value', patient_details['target_low']);
		$('div#enter_bg_form div.popup_content form').find('input#previous_bg_form').attr('value', patient_details['previous_bg']);
		$('div#enter_bg_form div.popup_content form').find('input#previous_sf_form').attr('value', patient_details['previous_sf']);
		$('div#enter_bg_form div.popup_content form').find('input#weight_form').attr('value', patient_details['weight']);
				
		$('div#enter_bg_form').find('li#popup_patient_id span#this_id').empty();
		$('div#enter_bg_form').find('li#popup_patient_id span#this_id').html(patient_details['account_number']);
		$('div#enter_bg_form').find('li#popup_patient_name span#this_name').empty();
		$('div#enter_bg_form').find('li#popup_patient_name span#this_name').html(patient_details['name']);
		show_pop('#enter_bg_form');
	}
	
	function show_pop(popupid) {
		//fade in the right popup
		$(popupid).fadeIn('fast');
		
		re_center_popup(popupid)
		
		//we need the blackout behind the popup
		$('body').prepend('<div id="fade"></div>');
		$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn('fast');
		
		$('#fade').click(function() {
			$(popupid).fadeOut('fast');
			$('#fade').fadeOut('fast', function() {
				$(this).remove();
			});
		});
		
		$(popupid).find('input.cancel_button').click(function() {
			$('#fade').trigger('click');	
		});
		
		$(popupid).find('div.popup_content input[type=text]').attr('value', '');
		$(popupid).find('div.check_box').removeClass('enabled').addClass('disabled');
		$(popupid).find('div.popup_content div.check_box').css('display','none');
		
		$(popupid).find('input[name=pre_meal]').click(function() {
			var bg = $(popupid).find('input#form_bg_value').attr('value');
			var bgc = $(popupid).find('input#confirm_form_bg_value').attr('value');
			//alert(typeof(bg));
			if(bg == bgc) {
				$(popupid).find('p.errors').text('');
				$(popupid).find('div.form_buttons').fadeIn('slow');
				
				var cbg = $('#form_bg_value').val();
				var target_high = $('#target_high_form').val();
				var target_low = $('#target_low_form').val();
				var pbg = $('#previous_bg_form').val();
				var psf = $('#previous_sf_form').val();
				var weight = $('#weight_form').val();
				
				//var sf = IVInsulinAdjustment(cbg, target_high, target_low, pbg, psf);
				var sf = InitialSensitivityFactor(weight);
				var rate = IIR(sf, cbg);
				
				$('#iir_rate').text(rate + ' units/hr');
				$('div.check_box').fadeIn('slow', function() {
					re_center_popup(popupid);
				});
				
				
			}else {
				$(popupid).find('p.errors').text('BG Values must match');
				$(popupid).find('div.form_buttons').fadeOut('slow');
			}
			
		});
		
		$(popupid).find('div.check_box ul li.checkbox').click(function() {
			if($(this).hasClass('disabled')) {
				$(this).removeClass('disabled').addClass('enabled');	
			}else {
				$(this).removeClass('enabled').addClass('disabled');	
			}
			
			
			
		});
	}
	
	function re_center_popup(popupid) {
		//center the popup
		var popuptopmargin = ($(popupid).height() + 10) / 2;
		var popupleftmargin = ($(popupid).width() + 10) / 2;
		
		//apply the values on the margin
		$(popupid).css({
			'margin-top' : -popuptopmargin,
			'margin-left' : -popupleftmargin
		});
	}
	
</script>
<div id="enter_bg_form" class="popup_window iv">
	<h1>Enter Blood Glucose </h1>
    <div class="popup_content">
    	<ul class="patient_details">
        	<li id="popup_patient_id">
            	<span>Patient ID:</span> 
                <span id="this_id"></span>
            </li>
            <li id="popup_patient_name">
            	<span>Name:</span>
                <span id="this_name"></span>
            </li>
        </ul>
        <?	 
			$attributes = array(
				'class' => 'enter_bg',
				'name' => 'enter_bg',
				'id' => 'enter_bg' 
			);
			
			echo form_open('patients/submit_bg', $attributes); 
			
		?>
            <h2>Please enter the current blood glucose value</h2>
            <ul class="popup_form">
            	<li>
                	<label>Enter BG Value:</label>
                    <input type="text" name="bg_value" id="form_bg_value" class="INPUT ROUNDED_CORNERS" value="" />
                    <span>mg/dl</span>
                </li>
                <li>
                	<label>Re-enter BG Value:</label>
                    <input type="text" name="confirm_value" id="confirm_form_bg_value" class="INPUT ROUNDED_CORNERS" value="" />
                    <span>mg/dl</span>
                    <p class="errors red"></p>
                </li>
                <li>
                	<label>Is this a pre-meal BG:?</label>
                    <input type="radio" name="pre_meal" id="pre_meal" value="1" />
                    <label class="radio">Yes</label>
                    <input type="radio" name="pre_meal" value="0" />
                    <label class="radio">No</label>
                </li>
            </ul>
            <div class="check_box popup disabled hidden">
            	<ul>
                	<li class="title">
                    	<h2>Adjust Insulin Infusion Rate to:</h2>
                    	<h3 class="amount" id="iir_rate"></h3>
                    </li>
                    <li class="checkbox disabled">&nbsp;</li>
                </ul>
                <div class="CLEARFIX"></div>
                <ul>
                	<li class="title">
                    	<h2>Fluid Infusion Rate:</h2>
                        <h3 class="amount" id="fluid_infusion_rate"></h3>
                        <h4 class="desc">Per Orders</h4>
                    </li>
                    <li class="checkbox disabled">&nbsp;</li>
                </ul>
                <div class="CLEARFIX"></div>
                <p class="note">Note: Make sure potassium (K) is greater than 4.0 mEq/L. Patient should receive at least 5 grams of glucose per hour.</p>
            </div>
            
            <input type="hidden" name="patient_id" id="patient_id_form" value="" />
            <input type="hidden" name="target_high" id="target_high_form" value="" />
            <input type="hidden" name="target_low" id="target_low_form" value="" />
            <input type="hidden" name="previous_bg" id="previous_bg_form" value="" />
            <input type="hidden" name="previous_sf" id="previous_sf_form" value="" />
            <input type="hidden" name="weight" id="weight_form" value="" />
            
            <div class="form_buttons" style="display:none;">
            	<input type="submit" class="submit_button" value="Continue" />
                <input type="reset" class="cancel_button" value="Cancel" />
                <div class="clearfix"></div>
            </div>   
		</form>
      </div>
    <div class="CLEARFIX"></div>
</div>
<?php include 'application/views/global/footer.php'; ?>