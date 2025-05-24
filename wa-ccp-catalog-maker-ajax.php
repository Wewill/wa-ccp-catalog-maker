<?php

//ini_set('display_errors', false);

require_once('includes/ccppm.inc.php');
if (array_key_exists('method', $_POST)) {
  $method = $_POST['method'];
  $edition_slug = $_POST['edition_slug'];
  $edition_id = $_POST['edition_id'];
  $method = $_POST['method'];
  if (array_key_exists('data', $_POST))
    $data = $_POST['data'];
  else
    $data = [];
  $ccppm = new ccppm($edition_slug, $edition_id);
  
  print(json_encode($ccppm->run_ajax($method, $data)));
}
