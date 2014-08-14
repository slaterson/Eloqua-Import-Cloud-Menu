<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";

  class sync {
    public $syncedInstanceUri;
    public $callbackUrl;
  }

  class Contact {
    public $C_EmailAddress;
    public $C_FirstName;
    public $C_LastName;
    public $C_Company;
    public $C_Date_1;
  }

  class syncActions {
    public $destination;
    public $action;
  }

  class import {
    public $syncActions;
    public $isSyncTriggeredOnImport;
    public $name;
    public $updateRule;
    public $identifierFieldName;
    public $dataRetentionDuration;
    public $fields;
  }

  $fileName = $_POST["file"];
  $segment = $_POST["segment"];
  $unique = $_POST["unique"];

#  print $unique . "<br>";

  $i = 0;
  foreach ($_POST as $key => $value) {
    if ($key != "segment" && $key != "file" && $key != "unique") {
      $header[$i] = $key;
      $fields[$key] = $value;
      $i++;
#      print "$key => $value<br>";
    }
  }

  foreach ($fields as $key => $value) {
    if ($unique == $value) {
      $unique = $key;
    }
  }

  $myfile = fopen($fileName, "r") or die("Unable to open file!");
  $firstRow = explode(",", str_replace("\n", "", fgets($myfile)));
  $j = 0;
  while (($line = fgetcsv($myfile)) !== FALSE) {
    $i = 0;
    foreach($line as $value) {
      $data[$j][$header[$i]] = str_replace("\n", '', $value);
#      print $header[$i] . " - " . $value . "<br>";
      $i++;
    }
    $j++;
  }
  fclose($myfile);

#  print json_encode($fields, JSON_PRETTY_PRINT);
#  print "<br><br>";
#  print json_encode($data, JSON_PRETTY_PRINT);
#  print "<br><br>";

  $syncActions[0] = new syncActions();
  $syncActions[0]->destination = "{{ContactList[135]}}";
  $syncActions[0]->action = "add";

  $import = new import();
#  $import->syncActions = $syncActions;
  $import->isSyncTriggeredOnImport = "false";
  $import->name = "Cloud Menu Import";
  $import->updateRule = "always";
  $import->identifierFieldName = $unique;
  $import->dataRetentionDuration = "PT1H";
  $import->fields = $fields;

  print json_encode($import, JSON_PRETTY_PRINT);
  print "<br><br>";

  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $response = $client->post('/contacts/imports', $import);
  $import_uri = $response->uri;
  print json_encode($response, JSON_PRETTY_PRINT);
  print "<br><br>";

  $import_data = $client->post($import_uri . "/data", $data);
#  print json_encode($import_data, JSON_PRETTY_PRINT);
#  print "<br><br>";

  $sync = new sync();
  $sync->syncedInstanceUri = $import_uri;
  $sync->callbackUrl = "https://mungkey.org/eloqua/menuImport/imported.php";
  $response = $client->post('/syncs', $sync);
  $syncInstance = $response->uri;
#  print json_encode($response, JSON_PRETTY_PRINT);
#  print "<br><br>";

#  print "<br><br><br>";
#  var_dump($_POST);

?>
