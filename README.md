# googlesheets
Standard library for generete Json from googleSheets


# use
include class class.googlesheets.php in your projects

# change namespace if you want

# Example to use
  $googleSheets = new googleSheets();
  $googleSheets->setApiKey([YOUR_api_KEY]);
  $googleSheets->setIdSheet([YOUR_ID_SHEET]);
  $googleSheets->setNameSheet([SHEET_NAME]);

  // if you want save your result in file, specify filename
  $googleSheets->setFilename($filename);

  // set if the first rows contains title
  $googleSheets->setFirstRowIsTitle();

  // get json
  $googleSheets->getJson();

  // return array formated
  $result = $googleSheets->getFormatedResult();

# more documentation

Google API:
 *  First create new project in console.cloud.google.com/apis
 *  - Enable Google Sheets API
 *  - Save your apiGoogleKey
 *  https://console.cloud.google.com/apis/library/sheets.googleapis.com?organizationId=0&project=[apiName]
 * 
 * Google sheets: 
 *   https://docs.google.com/spreadsheets/u/0/
 *   1. Creazione new document dello:
 *   2. file / condividi / pubblica su web
 *   3. condividi / accesso generale (chiuque abbia il link) / visualizzatore / copia link
 *   4. la chiave del link contiene idSheet
 *      example: https://docs.google.com/spreadsheets/d/[your_idSheet_is_here]/edit?usp=sharing
 *   
 * Google Sheets reference:
 *   https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets/get
 * 
 *   // selects cols 
 *   // possible range: $range = 'Sheet1!A1:F10'; -> docu: https://www.nidup.io/blog/manipulate-google-sheets-in-php-with-api
 * 
 * 
 *   Example call:
 *   url = https://sheets.googleapis.com/v4/spreadsheets/[idSheet]?includeGridData=true&ranges=A2:D100&key=[apiGoogleKey]
 * 