<?


$fp 		= fopen('master.zip', 'w');
$wsUpdate 	=  ("https://github.com/octocat/Spoon-Knife/archive/master.zip");
fwrite($fp,$wsUpdate);
fclose($fp);


$wsUpdate 	= file_get_contents("https://raw.githubusercontent.com/websheep/cms/master/README.md");
echo $wsUpdate;
