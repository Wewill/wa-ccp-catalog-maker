<?php

$phpDir=dir('.');
$files = array();
while (($file=$phpDir->read())!==false) {
  if ($file != '.DS_Store' && $file!='..' && $file!='.' && $file!='makefont.php' && $file!='vfs_fonts.js' && $file != 'makecss.php') {
    $files[] = $file;
  }
}
$files = array('Stereonic-L.woff');
asort($files);
$files = array_values($files);
$output = '';
foreach($files as $file) {
  if ($file[0] == '.')
    continue;
  list($name, $ext) = explode('.', $file);
  $output .= "@font-face { \n";
    $output .= "	font-family: '$name';\n";
//    $output .= "	src: url('http://dev.fifam.fr/wp-content/plugins/ccp-pdf-maker/fonts/$file');\n";
    $output .= "  src: url(\"data:application/font-woff;base64,";
    $output .= base64_encode(file_get_contents($file));
    $output .= "\") format(\"woff\");";
    $output .= "}\n\n";
  }


  $fh = fopen('fonts.css', 'w') or die("CAN'T OPEN FILE FOR WRITING");
  fwrite($fh,$output);
  fclose($fh);
  echo 'fonts.css created'."\n";
