<?php

include __DIR__ . '/threads.php';

$commands = array();

for ( $i=0; $i<10; $i++ ) {
	$commands[] = "bash -c 'sleep `shuf -i 1-5 -n 1`; echo $i'";
}

$threads = new Multithread( $commands );
$threads->run();

foreach ( $threads->commands as $key=>$command ) {
	echo "Command: ".$command."\n";
	echo "Output: ".$threads->output[$key];
	echo "Error: ".$threads->error[$key]."\n\n";
}

