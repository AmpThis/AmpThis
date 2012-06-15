<?php include 'application/views/global/header.php'; ?>
<div id="content" class="WRAP">
<?php include 'application/views/global/toolbar.php'; ?>
<?php include 'application/views/global/admin_nav.php'; ?>
    <div class="section column_content forms">
        <h2><?= $page_title; ?></h2>
		<?php echo form_open("admin/update_general_settings"); ?>
            <p class="group">
                <label>Glucose, unit of measure:</label>
                <select class="SELECT" name="GlucoseUnitOfMeasure">
                    <option value="0">Select Unit of Measure</option>
                    <option value="mg/dl">mg/dl</option>
                    <option value="mmol/l">mmol/l</option>
                </select>
                <div class="tooltip">
                    <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>Choose the measurement the system is using: for glucose: milligrams per deciliter (mg/dl) or millimoles per liter (mmol/l). Default unit of measure is: mg/dl.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Insulin, unit of measure:</label>
                <select class="SELECT" name="InsulinUnitOfMeasure">
                    <option value="0">Select Unit of Measure</option>
                    <option value="units/hr">units/hr</option>
                    <option value="cc/hr">cc/hr</option>
                    <option value="ml/hr">ml/hr</option>
                    <option value="units/hr">units/hr</option>
                </select>
                <div class="tooltip"> <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>Choose the measurement the system is using for insulin: milliliters per hour (ml/hr) or cubic centimeters per hour (cc/hr). Default unit of measure if: units/hr.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Hospital Name on Printed Reports:</label>
                <input type="text" name="HospitalName" class="INPUT ROUNDED_CORNERS" />
                <div class="tooltip"> <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>Enter the name of the institution that will display on all printed reports from your institution.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Weight:</label>
                <select class="SELECT" name="WeightUnitOfMeasure">
                    <option value="0">Select Measure of Weight</option>
                    <option value="kg">kg</option>
                    <option value="lbs">lbs</option>
                </select>
                <div class="tooltip"> <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>Select measure to be used: either Killograms (kg) or pounds (lbs). Default unit of measure is: kg.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Height:</label>
                <select class="SELECT" name="HeightUnitOfMeasure">
                    <option value="0">Select Measure of Height</option>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                </select>
                <div class="tooltip"> <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>Select measure to be used: either Centimeters (cm) or Inches (in). Default unit of measure is: cm.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Global 2nd Nurse Verification:</label>
                <select class="SELECT" name="2ndNurseVerification">
                    <option value="0">Select</option>
                    <option value="1">Disabled</option>
                    <option value="2">Enabled</option>
                </select>
            </p>
            <p class="group">
                <label>Force BG Login:</label>
                <select class="SELECT" name="ForceBGLogin">
                    <option value="0">Select</option>
                    <option value="1">Disabled</option>
                    <option value="2">Enabled</option>
                </select>
            </p>
            <p class="group">
                <label>Force Focus when BG Due:</label>
                <select class="SELECT" name="BGDueForceFocus">
                    <option value="0">Select</option>
                    <option value="1">Disabled</option>
                    <option value="2">Enabled</option>
                </select>
            </p>
            <p class="group">
                <label>Re-Alarm:</label>
                <input type="text" name="AlarmSnoozeTime" class="INPUT ROUNDED_CORNERS" />
                <span class="trailing">minutes</span>
                <div class="tooltip"> <span>?</span>
                    <div class="details">
                        <h4>More Information</h4>
                        <p>The amount of time you can delay alarm sounds. Default time: 10 minutes.</p>
                    </div>
                </div>
            </p>
            <p class="group">
                <label>Alarm Snooze:</label>
                <select class="SELECT" name="AlarmSnooze">
                    <option value="0">Select</option>
                    <option value="1">Disabled</option>
                    <option value="2">Enabled</option>
                </select>
            </p>
            <div class="buttons">
                <input class="green_button" type="submit" value="Save" />
                <input class="grey_button" type="reset" value="Cancel" />
            </div>
   		 </form>
  	</div>
    <div class="CLEARFIX"></div>
</div>
<script type="text/javascript">
	if($('div.section').height() < $('div.admin_nav').height()) {
		$('div.section').css('height', ($('div.admin_nav').height() - 20));
	}else {
		$('div.admin_nav').css('height', ($('div.section').height() - 20));	
	}
</script> 
<?php include 'application/views/global/footer.php'; ?>