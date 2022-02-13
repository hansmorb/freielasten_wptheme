<?php

function render_item_availability($cb_availability) {
	$print = '<div class="cb-postgrid-item-availability">';
	/*
	echo "<pre>";
	echo "cb_vailability";
	print_r($cb_availability);
	echo "</pre>";
	*/
 	$calendarData = $cb_availability;
	$date  = new DateTime();
	$today = $date->format( "Y-m-d" );
	$last_day = $calendarData['endDate'];
	$gotStartDate = false;
	$gotEndDate   = false;
	$dayIterator  = 0;
	foreach ( $calendarData['days'] as $day => $data ) {

		// Skip additonal days
		if ( ! $gotStartDate && $day !== $today ) {
			continue;
		} else {
			$gotStartDate = true;
		}

		if ( $gotEndDate ) {
			continue;
		}

		if ( $day == $last_day ) {
			$gotEndDate = true;
		}
		$day_days = date("d", strtotime($day));
		$day_month = date("m", strtotime($day));
		// Check day state
		if ( ! count( $data['slots'] ) ) {
			$print .= '<div class="cb-postgrid-item-availability-day no-timeframe">';
		} elseif ( $data['holiday'] ) {
			$print .= '<div class="cb-postgrid-item-availability-day location-closed">';
		} elseif ( $data['locked'] ) {
			if ( $data['firstSlotBooked'] && $data['lastSlotBooked'] ) {
				$print .= '<div class="cb-postgrid-item-availability-day booked">';
		} elseif ( $data['partiallyBookedDay'] ) {
				$print .= '<div class="cb-postgrid-item-availability-day partially-booked">';
		}
		} else {
			$print .= '<div class="cb-postgrid-item-availability-day available">';
		}
		$print.= '<div class="cb-postgrid-availability-days">' . $day_days .'</div>' . '<div class="cb-postgrid-availability-month">' .$day_month.'</div></div>';
	}
	$print .= '</div>'; /*END class="cb-postgrid-item-availability"*/
	return $print;
}

 ?>
