<?php

//ini_set('display_errors', false);
require_once('config.inc.php');
require_once('includes/ccpcm.inc.php');
if (array_key_exists('method', $_POST)) {
  $method = $_POST['method'];
  $edition_slug = $_POST['edition_slug'];
  $edition_id = $_POST['edition_id'];
  $method = $_POST['method'];
  if (array_key_exists('data', $_POST))
    $data = $_POST['data'];
  else
    $data = [];
  $ccpcm = new ccpcm($edition_slug, $edition_id);
  
  print(json_encode($ccpcm->run_ajax($method, $data)));
}
