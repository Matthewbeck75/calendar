<?php

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	if (isset($_POST['month'])) {
		$date_month = $_POST['month'];
		$date_year = $_POST['year'];
	} else { //previous or next was pressed
		$date_month = $_POST['current_month'];
		if (isset($_POST['previous'])) {
			$date_month --;
		} else { //must be next
			$date_month ++;
		}
		$date_year = $_POST['current_year'];
		
		if (isset($_POST['previous']) && $date_month == 0 ) { //go back one year and reset month to December
			$date_month = 12;
			$date_year --;
		} else if (isset($_POST['next']) && $date_month == 13) { //go forward one year and reset month to January
			$date_month = 1;
			$date_year ++;
		}
		
		
		
	} 
	
 
} else { //no form submittal so use system time as default date
	$date_month = date('n', time()); 
	$date_year = date('Y', time()); 
}

	$this_month = new DateTime();
	$this_month->setDate($date_year, $date_month, 1);
	$current_date_display = $this_month->format('M') . ' ' . $this_month->format('Y'); //for use in <h1>
	$timestamp = mktime(0,0,0,$date_month, 0, $date_year); //convert date to unix timestamp
	$first_day_of_week = date('w', $timestamp); //effectively number of days to be incluced from last month
	
	$days_in_month = cal_days_in_month(CAL_GREGORIAN, $this_month->format('m'), $this_month->format('Y'));

	$this_month->modify('first day of -1 month'); // modify object to previous month
	$days_in_prev_month = cal_days_in_month(CAL_GREGORIAN, $this_month->format('m'), $this_month->format('Y'));
	
	$days_of_month = array();
	$days_in_prev_month = ($days_in_prev_month - $first_day_of_week) +1; //+1 to counter 0 based index.
	while ($first_day_of_week) {	
		$days_of_month[] = $days_in_prev_month; //add prev days to array starting at lowest
		$days_in_prev_month ++; 
		$first_day_of_week --;
	}
	for ($i=1; $i < $days_in_month + 1; $i++) { //add all days of current month to days array
		$days_of_month[] = $i;
	}
	
	$number_of_entries = count($days_of_month); //get total entries to work out how many fields will be empty
	$required_additons = ((ceil($number_of_entries)%7 === 0) ? ceil($number_of_entries) : round(($number_of_entries+7/2)/7)*7)  - $number_of_entries; //math to get the remainder of a round to 7
	for ($i=1; $i < $required_additons + 1; $i++) { //add remainder days in
		$days_of_month[] = $i;
	}
	$rows_needed = count($days_of_month) / 7; //recount number of entries in array to determine rows needed. No rounding necessary

?>

<!DOCTYPE html>

<html>
	<head>
		<title>Calendar</title>
		<meta charset="UTF-8">
	</head>
	<body>
	<h1><?php echo $current_date_display ?></h1>
	<div>
		<table style="height: 200px;">
			<tr>
				<?php
				$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
				
				for($i=0; $i<=6; ++$i){ //loop to add days of week to <th> elements
					echo '<th>' . $days[$i] . '</th>';
				}
				?>
			</tr>
		
			<?php
				$day_count = 0;
				for ($i= 1; $i < $rows_needed + 1; $i++) { //loop to create each row
					echo '<tr>';
					
					for ($b=0; $b < 7; $b++) { //loop to create each field, 7 per row. 
						echo '<td>' . $days_of_month[$day_count] . '</td>';
						$day_count ++; 
					}
					
					echo '</tr>';
				}
				
			?>
			
		</table>

		
	</div>
	<form method="post" action="calendar.php">
		<input type="submit" value="previous" name="previous">
		<input type="hidden" value="<?php echo $date_month?>" name="current_month"> <!-- send current date to script -->
		<input type="hidden" value="<?php echo $date_year ?>" name="current_year">
	</form>
		<form method="post" action="calendar.php">
	<input type="submit" value="next" name="next">
		<input type="hidden" value="<?php echo $date_month?>" name="current_month">
		<input type="hidden" value="<?php echo $date_year ?>" name="current_year">
	</form>	
	<form method="post" action="calendar.php">
		<select name="year">
		<?php
		for($i=1990; $i<=2030; ++$i){ //add all years in
  		echo '<option value="' . $i . '"';
			if ($date_year == $i) { //if year is currently loaded then mark as selected
				echo ' selected="selected"';
			}
  		echo '>' . $i . '</option>';
		}
		?>	
		</select>
		<select name="month">
		<?php
		for($i=1; $i<=12; ++$i){ //add all months in
  		echo '<option value="' . $i . '"';
			if ($date_month == $i) { 
				echo ' selected="selected"';
			}
  		echo '>' . date('F', mktime(0, 0, 0, $i, 1, 0)). '</option>'; //print month names
		}
		?>	
		</select>
		
		<input type="submit" value="submit">
	</form>	
	
	</body>
</html>
