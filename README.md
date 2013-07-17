JSON2CSV-PHP
============

Convert JSON output from the FCC mobile measurement application to CSV

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
