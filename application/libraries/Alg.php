<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	class Alg {
		
		var $bg;
		
		public function __costruct() {
			parent::__construct();	
			$this->bg = 60;
		}
		
		function IIR($sf, $k) {
			return ($this->bg - $k) * $sf;
		}
		
		function IVInsulinAdjustment($cbg, $target_high, $target_low, $pbg, $psf) {
			if($cbg > $target_high && ($cbg / $pbg) > 0.85) :
				$sf = $psf * 1.25;
			elseif($cbg < $target_low) :
				$sf = $psf * 0.8;
			else :
				$sf = $psf;
			endif;	
			
			return $sf;
		}
		
		// SubQ Algorithm - Calculation of Adjustment Factor
		function SCAdjFactor($bg, $target_high, $target_low) {
			if($bg >= 180) :
				$af = 1.2;
			elseif($bg > $target_high && $bg < 180) :
				$af = 1.1;
			elseif($bg >= $target_low && $bg <= $target_high) :
				$af = 1;
			elseif($bg >= 70 && $bg < $target_low) :
				$af = 0.8;
			else :
				$af = 1;
			endif;
			
			return $af;
		}
		
		// SubQ Algorithm - Calculation of Correction Bolus
		// cbg is Current Blood Glucose
		// cf is the Correction Factor
		function SCCorrBolus($cbg, $cf, $target_low, $target_high) {
			if($target_high > $target_low) :
				$target_bg = $target_high - $target_low; // Determine the midpoint of the target range
			else :
				// Throw exception: Target range is not valid
			endif;
			$correction_bolus = ($cbg - $target_bg) / $cf;
			return $correction_bolus;
		}
		
		// SubQ Algorithm - Calculation of Meal Bolus
		// cf is the Correction Factor
		// prev_pp_bg is the previous day's post prandial bg, which is also the follow meal's pre prandial bg
		function SCCorrBolus2($target_low, $target_high, $prev_meal_bolus, $prev_pp_bg) {
			$af = SCAdjFactor($prev_pp_bg, $target_high, $target_low);
			$meal_bolus = $prev_meal_bolus * $af;
			return $meal_bolus;
		}
		
		
		// SubQ Algorithm - Calculation of Correction Bolus
		// The midsleep or pre-breakfast BG, whichever is lower, is used to choose an adjustment factor, which is multiplied by the current basal dose.
		function SCBasalDose($midsleep_bg, $breakfast_bg, $target_low, $target_high, $current_basal_dose) {
			if($midsleep_bg < $breakfast_bg) :
				$gov_bg = $midsleep_bg;
			else:
				$gov_bg = $breakfast_bg;
			endif;
			$af = SCAdjFactor($gov_bg, $target_high, $target_low);
			$new_basal_dose = $current_basal_dose * $af;
			return $new_basal_dose;
		}
		
		
		function InitialSensitivityFactor($weight) {
			return 0.0002 * $weight;	
		}
	}