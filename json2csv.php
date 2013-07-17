/*
2013 Measuring Broadband America Program
Mobile Measurement Data JSON2CSV Application
Copyright (C) 2013  SamKnows Ltd.

This Application converts JSON formated output from the FCC mobile measurement application to CSV.
It does not capture every field stored in the JSON output, just the most relevant ones.

The application supports two options:

1. Processing a CSV Output file from a single JSON test results file:
   php -q json2csv.php testresults.json > testresults.csv

2. Processing a CSV Output file from a directory of JSON test result files:
   php -q json2csv.php /path/to/json/files/ > testresults.csv


Please note:
- This Application has only been tested on Linux platforms, and may require modifications to work on Windows or other environments.
- This Applications requires a PHP 5.1 or above installation.

The FCC Measuring Broadband America (MBA) Program's Mobile Measurement Effort developed in cooperation with SamKnows Ltd. and diverse stakeholders employs an client-server based anonymized data collection approach to gather broadband performance data in an open and transparent manner with the highest commitment to protecting participants privacy.  All data collected is thoroughly analyzed and processed prior to public release to ensure that subscribers’ privacy interests are protected.

Data related to the radio characteristics of the handset, information about the handset type and operating system (OS) version, the GPS coordinates available from the handset at the time each test is run, the date and time of the observation, and the results of active test results are recorded on the handset in JSON(JavaScript Object Notation) nested data elements within flat files.  These JSON files are then transmitted to storage servers at periodic intervals after the completion of active test measurements.

This Android application source code is made available under the GNU GPL2 for testing purposes only and intended for participants in the SamKnows/FCC Measuring Broadband American program.  It is not intended for general release and this repository may be disabled at any time.


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

<?php

$arg = @$_SERVER['argv'][1];
if (!file_exists($arg)) {
   echo "Usage: php -q ".$_SERVER['argv'][0]." <json_file|directory_of_json_files>\n";
   exit(1);
}

echo '"Datetime","Self_Id","Source_IP","SIM_Operator","App_Version","Result_Type",';
echo '"Download_Target","Download_Success","Download_Mbit_sec","Download_Bytes","Download_Usec",';
echo '"Upload_Target","Upload_Success","Upload_Mbit_sec","Upload_Bytes","Upload_Usec",';
echo '"Latency_Target","Latency_Success","Latency_Avg_Msec","Latency_Min_Msec","Latency_Max_Msec","Latency_Loss_Pct",';
echo '"Active_Interface_Type","Network_Operator","Roaming","GSM Signal","CDMA DBM"';
echo "\n";

// Single file
if (substr($arg, -strlen(".json")) == ".json") {
   $arr = json_decode(file_get_contents($arg),true);
   if ($arr) print_csv($arr);

// Directory
} else if (is_dir($arg)) {
   $files = scandir($arg);
   foreach($files as $f) {
      if (substr($f, -strlen(".json")) != ".json") continue;
      $arr = json_decode(file_get_contents($arg."/".$f),true);
      if ($arr) print_csv($arr);
   }
}

function print_csv($arr) {

    echo '"'.@$arr['datetime'].'",';
    echo '"'.@$arr['user_self_id'].'",';
    echo '"'.@$arr['_sourceip'].'",';
    echo '"'.@$arr['sim_operator_code'].'",';
    echo '"'.@$arr['app_version_name'].'",';
    echo '"'.@$arr['submission_type'].'",';
    
    // HTTPGETMT
    $target = $mbits_sec = $success = $bytes = $duration = '';
    foreach(@$arr['tests'] as $id=>$test) {
        if ($test['type'] != "JHTTPGETMT") continue;
        $target = $test['target'];
        $mbits_sec = $test['bytes_sec']*0.000008;
        $success = $test['success'];
        $bytes = $test['transfer_bytes'];
        $duration = $test['transfer_time'];
        break;
    }
    echo '"'.$target.'",';
    echo '"'.$success.'",';
    echo '"'.$mbits_sec.'",';
    echo '"'.$bytes.'",';
    echo '"'.$duration.'",';
    
    // HTTPPOSTMT
    $target = $mbits_sec = $success = $bytes = $duration = '';
    foreach(@$arr['tests'] as $id=>$test) {
        if ($test['type'] != "JHTTPPOSTMT") continue;
        $target = $test['target'];
        $mbits_sec = $test['bytes_sec']*0.000008;
        $success = $test['success'];
        $bytes = $test['transfer_bytes'];
        $duration = $test['transfer_time'];
        break;
    }
    echo '"'.$target.'",';
    echo '"'.$success.'",';
    echo '"'.$mbits_sec.'",';
    echo '"'.$bytes.'",';
    echo '"'.$duration.'",';
    
    // UDPLATENCY
    $target = $success = $avg_ms = $min_ms = $max_ms = $loss = '';
    foreach(@$arr['tests'] as $id=>$test) {
        if ($test['type'] != "JUDPLATENCY") continue;
        $target = $test['target'];
        $success = $test['success'];
        $avg_ms = $test['rtt_avg']/1000;
        $min_ms = $test['rtt_min']/1000;
        $max_ms = $test['rtt_max']/1000;
        if ($test['lost_packets']+$test['received_packets'] > 0) {
           $loss = (($test['lost_packets']/($test['lost_packets']+$test['received_packets']))*100)."%";
        }
        break;
    }
    echo '"'.$target.'",';
    echo '"'.$success.'",';
    echo '"'.$avg_ms.'",';
    echo '"'.$min_ms.'",';
    echo '"'.$max_ms.'",';
    echo '"'.$loss.'",';
    
    // Network info
    $active_interface = $network_name = $roaming = '';
    foreach(@$arr['metrics'] as $id=>$test) {
        if ($test['type'] != "network_data") continue;
        $active_interface = $test['active_network_type'];
        $network_name = $test['network_operator_name'];
        $roaming = $test['roaming'];
        break;
    }
    echo '"'.$active_interface.'",';
    echo '"'.$network_name.'",';
    echo '"'.$roaming.'",';
    
    // GSM signal strength
    $gsm_signal_strength = '';
    foreach(@$arr['metrics'] as $id=>$test) {
        if ($test['type'] != "gsm_cell_location") continue;
        $gsm_signal_strength = $test['signal_strength'];
        break;
    }
    echo '"'.$gsm_signal_strength.'",';
    
    // CDMA signal strength
    $cdma_dbm = '';
    foreach(@$arr['metrics'] as $id=>$test) {
        if ($test['type'] != "cdma_cell_location") continue;
        $cdma_dbm = @$test['dbm'];
        break;
    }
    echo '"'.$cdma_dbm.'",';

    echo "\n";
}
