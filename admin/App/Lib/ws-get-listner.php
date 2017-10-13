<?
	$date = new DateTime();
	echo $date->getTimestamp(). PHP_EOL;
	sleep(5);
	$date->add(new DateInterval('5'));
	echo $date->getTimestamp();

?>
