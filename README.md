# googlesheets
Standard php library for generate Json from googleSheets

To use this class, you must be registered in Google Cloud as a developer.
https://console.cloud.google.com/

In console.cloud.google.com create your project and enable Google Sheets API (for more details see ref documetation at the bottom of this page) 


**use class**
- include class.googlesheets.php in your php
- change namespace if you want

**Example to use class**
```
$googleSheets = new googleSheets();
$googleSheets->setApiKey([YOUR_api_KEY]);
$googleSheets->setIdSheet([YOUR_ID_SHEET]);
$googleSheets->setNameSheet([SHEET_NAME]);
$googleSheets->setFilename($filename);          //if you want save your result in file, specify filename
$googleSheets->setFirstRowIsTitle();            // set if the first rows contains title
$googleSheets->getJson();                       // get data
$result = $googleSheets->getFormatedResult();   // return array formated
```


# ref documentation

**Google API**

 First create new project in console.cloud.google.com/apis
  - Enable Google Sheets API
  - Save your apiGoogleKey
    https://console.cloud.google.com/apis/library/sheets.googleapis.com?organizationId=0&project=[apiName]
 
 **Google sheets**
 
 https://docs.google.com/spreadsheets/u/0/
 1. Create new spreadsheets document
 2. file / condividi / pubblica su web
 3. condividi / accesso generale (chiuque abbia il link) / visualizzatore / copy link
 4. The idSheet is in the url
    example: https://docs.google.com/spreadsheets/d/[your_idSheet_is_here]/edit?usp=sharing
    
 **Google Sheets reference**
 
 https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets/get
 
 range/selects cols 
 
 - possible range: $range = 'Sheet1!A1:F10'; -> docu: https://www.nidup.io/blog/manipulate-google-sheets-in-php-with-api
  
 URL example:
 
 - https://sheets.googleapis.com/v4/spreadsheets/[idSheet]?includeGridData=true&ranges=A2:D100&key=[apiGoogleKey]
