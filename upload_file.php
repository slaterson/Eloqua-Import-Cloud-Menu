<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $contactFields = $client->get('/contacts/fields');

  $cFields = "";
  $uFields = "";
  foreach ($contactFields->items as $cField) {
    $uFields = $uFields . "<option value='$cField->internalName'>$cField->name</option>";
    $cFields = $cFields . "<option value='$cField->statement'>$cField->name</option>";
  }

  $segment = $_POST["segment"];
  if ($_FILES["file"]["error"] > 0) {
    echo "Error: " . $_FILES["file"]["error"] . "<br>";
  } else {
#    echo "Upload: " . $_FILES["file"]["name"] . "<br>";
#    echo "Type: " . $_FILES["file"]["type"] . "<br>";
#    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
#    echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br>";
    move_uploaded_file($_FILES["file"]["tmp_name"],"files/" . $_FILES["file"]["name"]);
    $fileName = "files/" . $_FILES["file"]["name"];
    print "Segment: $segment<br>";

    $myfile = fopen($fileName, "r") or die("Unable to open file!");
    $header = explode(",", str_replace("\n", "", fgets($myfile)));

    print "<form name='importMapping' method='post' action='./importDef.php'>";
    print "<input type='hidden' name='segment' value='$segment'>";
    print "<input type='hidden' name='file' value='$fileName'>";
    print "<table><tbody>";
    print "<tr><td>Unique ID:</td><td><select name='unique'>$cFields</select></td>";

    $i = 0;
    foreach ($header as $field) {
      print "<tr><td>" . $field . "</td>";
      print "<td><select name='" . $header[$i] . "'>$cFields</select></td></tr>";
      $i++;
    }
    print "<tr><td><input type='submit' value='Next'></td></tr>";
    print "</tbody></table>";
    print "</form>";

#    $j = 0;
#    while (($line = fgetcsv($myfile)) !== FALSE) {
#      $i = 0;
#      foreach($line as $value) {
#        $data[$j][$header[$i]] = str_replace("\n", '', $value);
#        print $header[$i] . " - " . $value . "<br>";
#	$i++;
#      }
#      $j++;
#    }
#    fclose($myfile);


#    print json_encode($data, JSON_PRETTY_PRINT);
#    echo fgets($myfile) . "<br>";

  }

?>
