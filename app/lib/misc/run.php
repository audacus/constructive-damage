<?php
$cli = true;
include_once('index.php');

\RunCycle::updatehandlers();
$parts = array(
    APPLICATION_PATH,
    Config::get('app.path.misc')
);
$outputfile = "runtime.log";
array_push($parts, $outputfile);
$pathOutputfile = \ Helper::makePathFromParts($parts);
$last = microtime(true);
$last10avg = array();
$last100avg = array();
$last1000avg = array();
$last10000avg = array();
while(1){
    \RunCycle::run();
    $runtime = microtime(true) - $last;
    $output = "";
    $output = $output."last runtime: ".$runtime."\n";

    $last10avg[] = $runtime;
    $last100avg[] = $runtime;
    $last1000avg[] = $runtime;
    $last10000avg[] = $runtime;

    while(count($last10avg) - 10 > 0){
        unset($last10avg[0]);
        $last10avg = array_values($last10avg);
    }
    while(count($last100avg) - 100 > 0){
        unset($last100avg[0]);
        $last100avg = array_values($last100avg);
    }
    while(count($last1000avg) - 1000 > 0){
        unset($last1000avg[0]);
        $last1000avg = array_values($last1000avg);
    }
    while(count($last10000avg) - 10000 > 0){
        unset($last10000avg[0]);
        $last10000avg = array_values($last10000avg);
    }
    $avg = 0;
    $sum = 0;
    foreach($last10avg as $val)
        $sum = $sum + $val;
    $avg = $sum / count($last10avg);
    $output = $output."last 10 average: ".$avg." fps: ".(1.0 / $avg)."\n";
    $avg = 0;
    $sum = 0;
    foreach($last100avg as $val)
        $sum = $sum + $val;
    $avg = $sum / count($last100avg);
    $output = $output."last 100 average: ".$avg." fps: ".(1.0 / $avg)."\n";
    $avg = 0;
    $sum = 0;
    foreach($last1000avg as $val)
        $sum = $sum + $val;
    $avg = $sum / count($last1000avg);
    $output = $output."last 1000 average: ".$avg." fps: ".(1.0 / $avg)."\n";
    $avg = 0;
    $sum = 0;
    foreach($last10000avg as $val)
        $sum = $sum + $val;
    $avg = $sum / count($last10000avg);
    $output = $output."last 10000 average: ".$avg." fps: ".(1.0 / $avg)."\n";
    $f = fopen($pathOutputfile, 'w');
    fwrite($f, $output);
    fclose($f);
    usleep(50);
    $last = microtime(true);
}
?>
