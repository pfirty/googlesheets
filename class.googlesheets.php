<?php
/**
 * Standard library for generete Json from googleSheets
 * 
 * <v class="20221221 1"></v>
 * 
 * Google API:
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
 */

 namespace lar;

class googleSheets {
  private $apiKey = '';
  private $url = 'https://sheets.googleapis.com/v4/spreadsheets/[idSheet]?includeGridData=true&ranges=[ranges]&key=[apiGoogleKey]';
  private $idSheet = '';
  private $sheetName = '';
  private $jsonData = '';
  private $fieldName = [];
  private $filename = ''; // if empty not save file cache
  private $firstColSuchAsKeyRec = false;
  private $firstRowIsTitle = false;
  private $timeValidityFile = '- 30 minutes';
  
  function __construct() {
  }

  /**
   * set google apiKey :: https://developers.google.com/maps/documentation/javascript/get-api-key
   */
  function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
  }

  /**
   * Set the publick key of googleSheet
   */
  function setIdSheet($idSheet) {
    $this->idSheet = $idSheet;

  }

  function setTimeValidityFileCache($val='30 minutes') {
    $this->timeValidityFile = $val;
  }

  /**
   * setting if the first row and first col is key of array
   */
  function setFirstColSuchAsKeyRec($val=false) {
    $this->firstColSuchAsKeyRec = $val;
  }
  /**
  * setting first row contains title col
  */
  function setFirstRowIsTitle($val=true) {
    $this->firstRowIsTitle = $val;
  }

  /**
   * setting name sheet use and specify coordinate
   * 
   * param sheetName string     example Sheet1
   * param rangeCoord string    example A1:F10
   */
  function setNameSheet($sheetName,$rangeCoord='') {
    $this->sheetName = $sheetName;
    if (!empty($rangeCoord)) {
      $this->sheetName .= '!'.$rangeCoord;
    }
  }

  /**
   * field names based on an array, or based on the first line
   * param $fieldName array     if it is not empty, it takes the nomenclature indicated
   *                            if empty, search for names in the first line
   */
  function setFieldName($fieldName=[]) {
    if (!empty($fieldName)) {
      $this->fieldName = $fieldName;
      return true;
    }

    // ignore first row is Title
    if (!$this->firstRowIsTitle) {
      return false;
    }

    
    $this->jsonData = isJson($this->jsonData) ? $this->jsonData : json_encode([]);
    $dati = json_decode($this->jsonData,true);
    if (!empty($dati['sheets'][0]['data'][0]['rowData'])) {
      $i = 0;
      foreach ($dati['sheets'][0]['data'][0]['rowData'][0]['values'] as $val) {
        $i++;
        $nameField = $i;
        if (!empty($val['formattedValue'])) {
          $nameField = $val['formattedValue'];
        }
        array_push($this->fieldName,$nameField);
      }
    }
    return true;
  }

  function getJson($forceDownload=false) {
    if (!$forceDownload && file_exists($this->filename) && date('YmdHi',strtotime($this->timeValidityFile)) < date('YmdHi',filemtime($this->filename)) ) {
      $this->jsonData = $this->getJsonFromFilename();
      $this->setFieldName();
      return $this->jsonData;
    }
    
    $url = $this->url;
    $url = str_replace('[ranges]',$this->sheetName,$url);
    $url = str_replace('[idSheet]',$this->idSheet,$url);
    $url = str_replace('[apiGoogleKey]',$this->apiKey,$url);
  
    $this->jsonData = file_get_contents($url);
 
    $this->saveJsonData();
    
    $this->setFieldName();

    return $this->jsonData;
  }

  function getJsonFromFilename() {
    $this->jsonData = json_encode([]);
    if (!empty($this->filename)) {
      $this->jsonData = file_get_contents($this->filename);
    } 
    return $this->jsonData;
  }

  /**
   * specify whether the name of the file where you want to save the results (used for storage)
   * if not setting, the file is not saved
   */
  function setFilename($filename) {
    $this->filename = $filename;
  }

  function saveJsonData() {
 
    if (!empty($this->filename)) {
      file_put_contents($this->filename,$this->jsonData);
      return true;
    }
    return false;
  }

  /**
   * returns an array formatted with the name of the columns from jsonData
   */
  function getFormatedResult() {
    $this->jsonData = isJson($this->jsonData) ? $this->jsonData : json_encode([]);
    $dati = json_decode($this->jsonData,true);
    $result = [];
    if (!empty($dati['sheets'][0]['data'][0]['rowData'])) {
      
      $i = -1;
      foreach ($dati['sheets'][0]['data'][0]['rowData'] as $row) {
        $i++;
        // ignor first line if is title
        if ($i==0 && $this->firstRowIsTitle) {
          continue;
        }

        $rec = [];
        foreach ($this->fieldName as $nameToRec) {
          $rec[$nameToRec] = '';
        }
        $keyArray = '';

        foreach ($row['values'] as $keyCell => $cell) {

          if (!empty($cell['formattedValue'])) {
            $value = $cell['formattedValue'];
            $keyArray = empty($keyArray) ? strtolower($value) : $keyArray;
            if (!empty($this->fieldName[$keyCell])) {
              $rec[$this->fieldName[$keyCell]] = $value;
            } else {
              array_push($rec,$value);
            }
          }
        }
        if ($this->firstColSuchAsKeyRec) {
          $result[$keyArray] = $rec;
        } else { 
          array_push($result,$rec);
        }
        
      }

    }
    return $result;
  }

}

?>