<?php
// var traceur include
$page_fonction = true;
if(empty($page_index))
	die('SECURITY ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');
////////////////////////////
// gestion des membres :
////////////////////////////

/*************************************************/
// fonction met en forme les valeur pour l output
function parser_to_ressource($input=null)
{
	if(empty($input['NOM_MANAGER_LONG']) || empty($input['NOM_MANAGER_SHORT']) || empty($input['manga']))
		return -4;
	$out = $input['NOM_MANAGER_LONG']. ' ' .$input['NOM_MANAGER_SHORT']. "\n";
	foreach($input['manga'] as $array){
		if(check_manga_line_is_full($array))
			$out.=$array['LONG_PROJECT_NAME'].' '.$array['SHORT_PROJECT_NAME'].' '.$array['FIRST_CHAPTER'].' '.$array['LAST_CHAPTER'].' '.$array['FIRST_TOME'].' '.$array['LAST_TOME'].' '.$array['STATE_AND_GENDER'].' '.$array['INFOPNG'].' '.$array['CHAPTER_SPECIALS']."\n";
	}
	$out.= '#';
	return $out;
}
/*************************************************/
// fonction check si les valeur de la ligne sont correcte
function check_manga_line_is_full($input)
{
	// Check que toutes les variables sont remplies & correctment
	if	(empty($input) 
		|| empty($input['LONG_PROJECT_NAME']) 
		|| empty($input['SHORT_PROJECT_NAME']) 
		|| !isset($input['FIRST_CHAPTER']) // ?
		|| !isset($input['LAST_CHAPTER']) // ?
		|| !isset($input['FIRST_TOME']) // ?
		|| !isset($input['LAST_TOME']) // ?
		|| empty($input['STATE_AND_GENDER']) 
		|| !isset($input['INFOPNG']) // ?
		|| !isset($input['CHAPTER_SPECIALS'])
		|| strlen($input['LONG_PROJECT_NAME']) > 50 
		|| strlen($input['SHORT_PROJECT_NAME']) > 10 
		|| !is_numeric($input['FIRST_CHAPTER']) 
		|| !is_numeric($input['LAST_CHAPTER']) 
		|| !is_numeric($input['FIRST_TOME']) 
		|| !is_numeric($input['LAST_TOME']) 
		|| !is_numeric($input['STATE_AND_GENDER']) 
		|| !is_numeric($input['INFOPNG']) 
		|| !is_numeric($input['CHAPTER_SPECIALS'])
		)
		return false;
	else
		return true;
}
/*************************************************/
// fonction lancer le dl du ficher
function set_download($str_file=null, $name = 'rakshata-manga-2')
{
	$size = strlen($str_file);
	header("Content-Type: application/force-download;");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $size");
	header("Content-Disposition: attachment; filename=\"" .$name. "\"");
	header("Expires: 0");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	echo $str_file;
	die();
}
/*************************************************/
// fonction convertire utf8 en ascii
function set_cookie($mixed=null,$name=null, $time_out=30758400)
{
	if(is_array($mixed))
		foreach($mixed as $key=>$value)
			set_cookie($value, $name.'['.$key.']');
	else
		setcookie($name, $mixed, time() + $time_out, null, null, false, true);
}
/*************************************************/
// fonction convertire utf8 en ascii
function utf8_to_ascii($mixed, $ascii='ISO-8859-1//TRANSLIT')
{
	if(is_array($mixed)){ // si on lui passe un array on recursive
		foreach($mixed as $key => $value){
			if(is_array($value))
				$out[$key] = utf8_to_ascii($value);
			else
				$out[$key] = iconv("UTF-8", $ascii, $value);
		}
	}
	else
		$out = iconv("UTF-8", $ascii, $mixed);
	return $out;
}
/*************************************************/
// fonction remet les output_value[] en output[]value
function sort_array($array)
{
	for($i=0;isset($array['LONG_PROJECT_NAME'][$i]) ;$i++)
		foreach($array as $key=>$value)
			$out[$i][$key] = $value[$i];
	return $out;
}
/*************************************************/
// fonction faire un echo des erreurs de l'array passee
function show_error($mixed)
{
	if(is_array($mixed))
	{
		echo '<div class="error">'."\n";
		foreach($mixed as $echo_error) // parcours la liste des error
		{
			echo isset($i)? "<br />\n":null;
			echo $echo_error;
			$i = 1;
		}
		echo "\n".'</div>';
	}
	else
		echo '<div class="error">'.$mixed.'</div>';
		
}
/*************************************************/
// fonction echape les " du csv
function csv($texte)
{
	return str_replace('"', '""', $texte);
}
/*************************************************/
// fonction de mise en log
function log_f($pseudo, $action)
{
		//ouverture ou creation du fichier de log
	$file = 'log/log_' .date('y-m'). '.csv';
	if(!($fichier_log = @fopen($file, 'a+')))
		die('CONFIGURATION ERROR : Please Contact Your Admin, <a href="mailto:contact@rakshata.com">contact@rakshata.com</a>.');

	chmod($file, 0606);
		//enregistrement de l'entree
	fputs($fichier_log , '"' .date('Y-m-d-H:i:s'). '","' .$_SERVER['REMOTE_ADDR']. '","' .csv($pseudo). '","' .csv($action). "\"\n");
		//fermeture du fichier
	fclose($fichier_log);
}


