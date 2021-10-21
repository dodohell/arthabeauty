<?php
//
$timeo_start = microtime(true);
ini_set("memory_limit","128M");
//

$html = "
<style>

body { font-family: sans; text-align: justify; }
p { font-family: sans; }
div { font-family: sans; }

</style>

<h4>Bulgarian</h4>
<p>\xd0\x96\xd1\x8a\xd0\xbb\xd1\x82\xd0\xb0\xd1\x82\xd0\xb0 \xd0\xb4\xd1\x8e\xd0\xbb\xd1\x8f \xd0\xb1\xd0\xb5\xd1\x88\xd0\xb5 \xd1\x89\xd0\xb0\xd1\x81\xd1\x82\xd0\xbb\xd0\xb8\xd0\xb2\xd0\xb0, \xd1\x87\xd0\xb5 \xd0\xbf\xd1\x83\xd1\x85\xd1\x8a\xd1\x82, \xd0\xba\xd0\xbe\xd0\xb9\xd1\x82\xd0\xbe \xd1\x86\xd1\x8a\xd1\x84\xd0\xbd\xd0\xb0, \xd0\xb7\xd0\xb0\xd0\xbc\xd1\x80\xd1\x8a\xd0\xb7\xd0\xbd\xd0\xb0 \xd0\xba\xd0\xb0\xd1\x82\xd0\xbe \xd0\xb3\xd1\x8c\xd0\xbe\xd0\xbd.</p> 
asdfасдф
<table border='1'><tr><td>asdfasdfасдф</td><td></td></tr></table>
";





//==============================================================
//==============================================================
//==============================================================
include("../mpdf.php");

$mpdf=new mPDF(); 

$mpdf->useAdobeCJK = true;		// Default setting in config.php
						// You can set this to false if you have defined other CJK fonts

$mpdf->SetAutoFont(AUTOFONT_ALL);	//	AUTOFONT_CJK | AUTOFONT_THAIVIET | AUTOFONT_RTL | AUTOFONT_INDIC	// AUTOFONT_ALL
						// () = default ALL, 0 turns OFF (default initially)

$mpdf->WriteHTML($html);

$mpdf->Output(); 

exit;
//==============================================================
//==============================================================
//==============================================================


?>