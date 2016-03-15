----- Data in XML -----
1. Performs the following transformation: 
		- '&' (ampersand) transformation to '&amp;' 
		- '"' (double quote) transformation to '&quot;' 
		- ''' (single quote) transformation to '&#039;' 
		- '<' (sign "less than") transformation to '&lt;' 
		- '>' (sign "more than") transformation to '&gt;' 
		- '\r' (carriage return CR or 0x0D (13) в ASCII) transformation to '&#13;'
		- '\n' (new line LF or 0x0A (10) в ASCII) transformation to  '&#10;'
2. Decimal point - '.' (dot);  e.g. 2302.02
3. Encoding – (UTF-8);
4. Date Time   2010-02-09T11:06:35
5. Date  2010-02-09 (year-month-day)
6. Time  11:06:35 (hours: minutes: seconds)


Configuration options:
----------------------

The path to the configuration file - app/Resources/Сonfig/parameters.yml

Basic values:

debug: false                        # debugging (true, false)
environment: production             # mode: (production, test)
data_dir:  "c:/tmp"                 # data dir (here the output of your script)
url_test: "http://localhost:8888"   # Test the local HOST
ubki_login: "ubkiuser"              # UBKI - work login
ubki_pass: "ubkipass"               # UBKI - work pass
ubki_test_login: "partest"          # UBKI - test login
ubki_test_pass: "test38%"           # UBKI - test pass

----- Working with script ------

UBKI:INFO (Request data from UBKI)
---------
e.g. script.bat "ubki:index" "-t=info"

Runs the script with two options: 

option1 = "ubki:index" Command name
option2 =  "-t=info" Command type


UBKI:DATA (Data to UBKI)
---------
e.g. script.bat "ubki:index" "-t=data"

Runs the script with two options: 

option1 = "ubki:index" Command name
option2 =  "-t=data" Command type


UBKI:REGISTRY (Getting registry data from UBKI)
---------
e.g. script.bat "ubki:index" "-t=registry"
or
e.g. script.bat "ubki:index" "todo=REP&indate=20140501&idout=IN#0002133696&idalien=bsa&grp=&zip=ZLIB" "-t=registry"

Runs the script with two or three options: 

option1 = "ubki:index" Command name
option2 =  "todo=REP&indate=20140501&idout=IN#0002133696&idalien=bsa&grp=&zip=ZLIB" Command arguments
option3 =  "-t=registry" Command type


The results obtained:
--------------------

The results of the script will be located in the data directory (data_dir)

-  result.txt   ( The results of the script, format "ini" file)
-  script.log   (operation where it is executed, it is necessary to test and debug)
-  error.log    (errors that appeared during the script)
-  upload.xml   (data for the request or transmission to UBKI )
-  response.xml (response data on successful execution of the script)
-  response_err.xml (response data error when you run the script)
-  response_notice.xml (response data with comments on error script execution)

