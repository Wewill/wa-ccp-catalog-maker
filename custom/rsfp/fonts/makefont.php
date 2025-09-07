<?php

    $output = "this.pdfMake = this.pdfMake || {}; this.pdfMake.vfs = {\n";
    $phpDir=dir('.');
    $files = array();
    while (($file=$phpDir->read())!==false) {
	if (!in_array($file, ['.DS_Store','.','..', 'makefont.php', 'vfs_fonts.js', 'makecss.php', 'disabled', 'fonts.css'])) {
		if ($file[0] != '.')
			  $files[] = $file;
        }
    }

    asort($files);
    $files = array_values($files);
    foreach($files as $file) {
        $output .= '"';
    #     $output .= pathinfo($file, PATHINFO_FILENAME);
        $output .= str_replace(' ', '', $file);
        $output .= '":"';
        $output .= base64_encode(file_get_contents($file));
        $output .= '"'."\n".',';
        print "FILE : ".str_replace(' ', '', $file)."\n";
    }
    print "\n";
    $output=substr($output,0,-1);
    $output .= "}";

    $fh = fopen(__DIR__.'/../js/vfs_fonts.js', 'w') or die("CAN'T OPEN FILE FOR WRITING");
    fwrite($fh,$output);
    fclose($fh);
    echo 'vfs_fonts.js created'."\n";
