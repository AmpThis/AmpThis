<?php include 'application/views/global/header.php'; ?>
<div id="content" class="WRAP">
<?php include 'application/views/global/toolbar.php'; ?>
  <div class="section" id="patient_details">
  <? foreach($patients as $patient) : ?>
    <div class="detailed-info <?= $patient->class; ?>">
      <div class="column single">
        <div class="box info">
          <div class="pediatric_horizontal">Pediatric</div>
          <ul class="stats">
            <li><span>Patient ID:</span> <?= $patient->AccountNumber; ?></li>
            <li class="under18"><span>DOB: </span><?= $patient->Birthday; ?></li>
            <li><span>Height:</span> <?= $patient->Height; ?> inches</li>
            <li><span>Weight:</span> <?= $patient->Weight; ?> KG</li>
            <li><span>Physician:</span> <?= $patient->PhysicianSuffix . ' ' . $patient->PhysicianFirstName . ' ' . $patient->PhysicianLastName . ' ' . $patient->PhysicianCredentials; ?></li>
            <li><span>Diagnosis:</span> <?= $patient->DiagnosisLabel; ?></li>
          </ul>
          <ul class="edit">
            <li class="left"><a href="javascript:;">View All Info</a></li>
            <li class="edit"><a href="javascript:;">Edit</a></li>
          </ul>
          <div class="clearfix">&nbsp;</div>
        </div>
      </div>
      <div class="column two">
        <div class="box current-insulin padd">
          <h2 class="title">Current Insulin</h2>
          <div class="val"><?= $patient->InsulinRate; ?></div>
          <div class="ammount">units/hr</div>
          <div class="clearfix">&nbsp;</div>
        </div>
        <div class="box target-range padd">
          <h2 class="title">Target Range</h2>
          <div class="val"><?= $patient->TargetLow . '-' . $patient->TargetHigh; ?></div>
          <div class="ammount">mg/dl</div>
          <div class="clearfix">&nbsp;</div>
        </div>
      </div>
      <div class="column two">
        <div class="box current-bg padd">
          <h2 class="title">Current BG</h2>
          <div class="val"><?= $patient->BGValue; ?></div>
          <div class="ammount">unit/ml</div>
          <div class="clearfix"></div>
        </div>
        <div class="box insulin-concentration padd">
          <h2 class="title">Insulin concentration</h2>
          <div class="val"><?= $patient->InsulinConcentration; ?></div>
          <div class="ammount">unit/ml</div>
          <div class="clearfix">&nbsp;</div>
        </div>
      </div>
      <div class="column single">
        <div class="box meal-bolus-activated padd">
          <h2 class="title-button">Meal Bolus: Activated</h2>
          <div class="time">
          	<div class="CLEARFIX"></div>
          </div>
          	<?
			
				$date_time = explode(' ', $patient->NextBGAt);
				
				$date = date('Y-n-j H:i:s', strtotime($patient->NextBGAt));
				$now = date('Y-n-j H:i:s');
			?>
			<script type="text/javascript">
                <?  if($date > $now) { ?>
                    $(document).ready(function() {
						$('div.time').countdown({until:new Date(
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
                        $('div.time').text('BG DUE!');
                    });
                <? } ?>
				
				function TimesUp() {
					$('div.time').empty();
					$('div.time').text('BG DUE!');	
				}
            </script>
          <div class="meal-buttons"> <a href="javascript:show_pop();" rel="enter-bg" class="meal-buttons enter-bg ROUNDED_CORNERS">Enter BG</a>
            <div class="window insulin_rate">
              <h2>Confirm Insulin Rate</h2>
              <div class="window_content">
                <div class="head">
                  <ul>
                    <li><span>Patient ID:</span> 91237884525</li>
                    <li><span>Name:</span> Barrett, Ned</li>
                  </ul>
                </div>
                <div class="slide_1">
                  <label>Enter BG Value:</label>
                  <input type="text" class="short_input" value="" />
                  <span>mg/dl</span>
                  <div class="clearfix"></div>
                  <label>Confirm BG Value:</label>
                  <input type="text" class="short_input" value="" />
                  <div class="clearfix"></div>
                  <label>is this a pre-meal BG:</label>
                  <input type="radio" value="1" class="radio_button" />
                  <label>Yes</label>
                  <input type="radio" value="0" class="radio_button" checked="checked" />
                  <label>No</label>
                  <div class="clearfix"></div>
                  <label>Meal Plan - Number of Carbs Per Meal:</label>
                  <input type="text" class="short_input" value="" />
                </div>
                <div class="slide_2">
                  <div class="check_box popup">
                    <h2>Adjust Insulin Infusion rate to::</h2>
                    <ul>
                      <li>
                        <h3 class="amount">2.0 units/hr</h3>
                      </li>
                      <li class="checkbox">
                        <input class="checkboxStyled" type="radio" value="insulin_rate_adjust" />
                      </li>
                    </ul>
                  </div>
                  <div class="check_box popup">
                    <h2>Fluid Infusion Rate:</h2>
                    <ul>
                      <li>
                        <h3 class="amount">125 ml/hr</h3>
                        <h4 class="desc">D5+0.45% NaCI</h4>
                      </li>
                      <li class="checkbox">
                        <input class="checkboxStyled" type="radio" value="fluid_infusion_rate" />
                      </li>
                    </ul>
                  </div>
                  <p class="note"><span>Note:</span> Make sure potassium (k) is greater than 4.0 mEq/L Patient should receive at least 5 grans of glucose per hour</p>
                </div>
                <div class="form_buttons">
                  <input type="submit" class="submit_button" value="Complete" />
                  <input type="reset" class="cancel_button" value="Cancel" />
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
            <div class="window bg_success">
              <p>Insulin dose adjustment has been recorded</p>
            </div>
            <a href="javascript:;" rel="stop-meal" class="meal-buttons stop-meal ROUNDED_CORNERS">Stop Meal</a> </div>
          <div class="clearfix">&nbsp;</div>
        </div>
      </div>
      <div class="clearfix">&nbsp;</div>
    </div>
      <? endforeach; ?>

    <div class="separater">&nbsp;</div>
    <div class="line-graph"> <img src="<?= base_url(); ?>assets/imgs/graph.jpg" width="100%" alt="Patient Graph" /> </div>
    <div class="patient-chart">
      <ul class="table-tabs">
        <li class="active"><a href="#">Blood Glucose Values</a></li>
        <li><a href="#">Glucometrix&trade; and Lab Values</a></li>
        <li><a href="#">Comments (2)</a></li>
        <li><a href="#">History</a></li>
      </ul>
      <table class="details-table zebra-table">
        <thead>
          <tr>
            <th style="text-align:left;">Date</th>
            <th style="text-align:center;">BG Value</th>
            <th style="text-align:center;">Insulin Rate</th>
            <th style="text-align:center;">Target Range</th>
            <th style="text-align:center;">Next BG Due</th>
          </tr>
        </thead>
        <tbody>
        <? foreach($recent_bg as $item) : ?>
          <tr>
            <td><?= $item->CreateDate; ?></td>
            <td style="text-align:center;"><?= $item->BGValue; ?> mg/dl</td>
            <td style="text-align:center;"><?= $item->InsulinRate; ?></td>
            <td style="text-align:center;"><?= $item->TargetLow . '-' . $item->TargetHigh; ?> mg/dl</td>
            <td style="text-align:center;"><?= $item->NextBGAt; ?></td>
          </tr>
        <? endforeach; ?>
        </tbody>
      </table>
      <div class="clearfix"></div>
    </div>
  </div>
</div>
<? foreach($patients as $patient) : ?>
<div id="iv_bg_popup" class="popup_window iv">
	<h1>Select BG Type</h1>
    <div class="popup_content">
    	<ul class="patient_details">
        	<li id="popup_patient_id">
            	<span>Patient ID: </span><?= $patient->AccountNumber; ?>
            </li>
            <li id="popup_patient_name">
            	<span>Name:</span> <?= $patient->LastName; ?>, <?= $patient->FirstName; ?>
            </li>
        </ul>
        <form name="iv_enter_bg">
			<h2>Select BG:</h2>
            <ul class="popup_form">
                <li>
                    <div class="selectbox form">
                        <select name="iv_bg" class="SELECT">
                            <option value="0">-- Select BG --</option>
                            <option value="1">Breakfast</option>
                            <option value="2">Lunch</option>
                            <option value="3">Dinner</option>
                            <option value="4">Bedtime</option>
                            <option value="5">Misc</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                </li>
            </ul>
            <h2>Please enter the current blood glucose value</h2>
            <ul class="popup_form">
            	<li>
                	<label>Enter BG Value:</label>
                    <input type="text" name="bg_value" class="INPUT ROUNDED_CORNERS" value="" />
                    <span>mg/dl</span>
                </li>
                <li>
                	<label>Re-enter BG Value:</label>
                    <input type="text" name="confirm_bg" class="INPUT ROUNDED_CORNERS" value="" />
                    <span>mg/dl</span>
                </li>
                <li>
                	<label>Is this patient able to eat?</label>
                    <input type="radio" name="eat" checked="checked" value="1" />
                    <label class="radio">Yes</label>
                    <input type="radio" name="eat" value="0" />
                    <label class="radio">No</label>
                </li>
            </ul>
        	<div class="check_box popup disabled hidden">
            	<h2>Adjust Insulin Infusion rate to:</h2>
                <ul>
                	<li>
                        <h3 class="amount rate"><span class="units"></span> units/hr</h3>
                    </li>
                    <li class="checkbox disabled">&nbsp;</li>
                </ul>
                <div class="clearfix"></div>
            	<h2>Fluid Infusion Rate:</h2>
                <ul>
                	<li>
                    	<h3 class="amount fluid"><span class="units"></span> units/hr</h3>
                        <h4 class="desc">Per Orders</h4>
                    </li>
                    <li class="checkbox disabled">&nbsp;</li>
                </ul>
                <div class="clearfix"></div>
                <p class="note">Note: Make sure potassium (K) is greater than 4.0 mEq/L. Patient should receive at least 5 grams of glucose per hour.</p>
            </div>
            <div class="form_buttons">
            	<input type="submit" class="submit_button" value="Submit" />
                <input type="reset" class="cancel_button" value="Cancel" />
                <div class="clearfix"></div>
            </div>        
        </form>
    </div>
    <? endforeach; ?>
<?php include 'application/views/global/footer.php'; ?>