<?php

  print "<html><head></head><body>";
  $segment = $_GET["asset"];
  print "Segment id: " . $segment . "<br>";

  print "<form action='upload_file.php' method='post' enctype='multipart/form-data'>";
  print "<label for='file'>Upload a filename:</label>";
  print "<input type='file' name='file' id='file'><br>";
  print "<input type='hidden' name='segment' value='$segment'>";
  print "<input type='submit' name='submit' value='Submit'>";
  print "</form>";

exit;

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $restEndPointURL = $endPointBase->urls->base . "/API/rest/1.0";

  $restClient = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $restEndPointURL);
  $response = $restClient->get('/assets/contact/views');

  print "Select the contact view to export:";
  print "<form name='viewselect' action='./export.php' method='post'>";
  print "<input type='hidden' name='segment' value='$segment'>";
  print "<select name='view'>";
  foreach ($response->elements as $view) {
    print "<option value='" . $view->id . "'>" . $view->name . "</option>";
  }
  print "</select>";
  print "<input type='submit' value='Export'>";
  print "</form>";
  print "</body></html>";

?>
