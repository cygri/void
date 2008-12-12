<?php  

include_once 'cloud-lib.php';  

$languages = array('PHP'            => '9.243',
                   'Python'         => '5.012',
                   'ActionScript'   => '0.472',
                   'Lisp/Scheme'    => '0.419',
                   'Lua'            => '0.415',
                   'Pascal'         => '0.400',
                   'Java'           => '20.715',
                   'PowerShell'     => '0.384',
                   'COBOL'          => '0.360',
                   'SAS'            => '0.640',
                   'JavaScript'     => '3.130',
                   'PL/SQL'         => '0.700',
                   '(Visual) Basic' => '10.490',
                   'D'              => '1.265',
                   'Ruby'           => '2.762',
                   'Delphi'         => '3.055',
                   'C#'             => '4.334',
                   'Perl'           => '4.841',
                   'C++'            => '10.716',
                   'C'              => '15.379');

$languages_wiki = array('PHP'       => 'http://en.wikipedia.org/wiki/PHP',
                   'Python'         => 'http://en.wikipedia.org/wiki/Python_(programming_language)',
                   'ActionScript'   => 'http://en.wikipedia.org/wiki/ActionScript',
                   'Lisp/Scheme'    => 'http://en.wikipedia.org/wiki/Lisp_(programming_language)',
                   'Lua'            => 'http://en.wikipedia.org/wiki/Lua_(programming_language)',
                   'Pascal'         => 'http://en.wikipedia.org/wiki/Pascal_Programming_Language',
                   'Java'           => 'http://en.wikipedia.org/wiki/Java',
                   'PowerShell'     => 'http://en.wikipedia.org/wiki/PowerShell',
                   'COBOL'          => 'http://en.wikipedia.org/wiki/COBOL',
                   'SAS'            => 'http://en.wikipedia.org/wiki/SAS_programming_language',
                   'JavaScript'     => 'http://en.wikipedia.org/wiki/JavaScript',
                   'PL/SQL'         => 'http://en.wikipedia.org/wiki/PL/SQL',
                   '(Visual) Basic' => 'http://en.wikipedia.org/wiki/Visual_Basic',
                   'D'              => 'http://en.wikipedia.org/wiki/D_programming_language',
                   'Ruby'           => 'http://en.wikipedia.org/wiki/Ruby',
                   'Delphi'         => 'http://en.wikipedia.org/wiki/Delphi',
                   'C#'             => 'http://en.wikipedia.org/wiki/C_Sharp_(programming_language)',
                   'Perl'           => 'http://en.wikipedia.org/wiki/Perl',
                   'C++'            => 'http://en.wikipedia.org/wiki/C%2B%2B',
                   'C'              => 'http://en.wikipedia.org/wiki/C_programming');


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE>Tag Cloud Generator</TITLE>
  <LINK REL="stylesheet" HREF="cloud-style.css" TYPE="text/css">  
 </HEAD>
 <BODY>
	<?php
	 echo renderCloud($languages, $languages_wiki, true);
	?>
	</div>
 </BODY>
</HTML>





