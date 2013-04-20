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
	if(empty($input['const']['NOM_MANAGER_LONG']) || empty($input['const']['NOM_MANAGER_SHORT']) || empty($input['data']))
		return false;
	$head = str_replace(' ','_',$input['const']['NOM_MANAGER_LONG']). ' ' .str_replace(' ','_',$input['const']['NOM_MANAGER_SHORT']). "\n";
	$out = null;
	foreach($input['data'] as $array){
		if(($array = check_manga_line($array)))
			$out.=$array['LONG_PROJECT_NAME'].' '.$array['SHORT_PROJECT_NAME'].' '.$array['FIRST_CHAPTER'].' '.$array['LAST_CHAPTER'].' '.$array['FIRST_TOME'].' '.$array['LAST_TOME'].' '.$array['STATE'].$array['GENDER'].' '.$array['INFOPNG'].' '.$array['CHAPTER_SPECIALS']."\n";
	}
	return (empty($out))? false : $head . $out;
}
/*************************************************/
// fonction check si les valeur de la ligne sont correcte
function check_manga_line($input)
{
	// Check que toutes les variables sont remplies & correctment
	if	(!isset($input['LONG_PROJECT_NAME'])
		|| $input['LONG_PROJECT_NAME'] == null
		|| strlen($input['LONG_PROJECT_NAME']) > 50
		|| !isset($input['SHORT_PROJECT_NAME'])
		|| $input['SHORT_PROJECT_NAME'] == null
		|| strlen($input['SHORT_PROJECT_NAME']) > 10
		|| empty($input['STATE'])
		|| empty($input['GENDER'])
		|| !isset($input['CHAPTER_SPECIALS'])
		)
		return false;
	if 	(	(!isset($input['FIRST_CHAPTER']) 
			|| $input['FIRST_CHAPTER'] == null
			|| !isset($input['LAST_CHAPTER'])
			|| $input['LAST_CHAPTER'] == null
			)
		&& // error si chap et tome vide
			(!isset($input['FIRST_TOME']) 
			|| $input['FIRST_TOME'] == null
			|| !isset($input['LAST_TOME'])
			|| $input['LAST_TOME'] == null
			)
		)
		return false;
	// on met les 0 pour les valeurs vide
	$input['SHORT_PROJECT_NAME'] = str_replace(' ', '_', $input['SHORT_PROJECT_NAME']);
	$input['LONG_PROJECT_NAME'] = str_replace(' ', '_', $input['LONG_PROJECT_NAME']);
	$input['CHAPTER_SPECIALS'] =(int) (empty($input['CHAPTER_SPECIALS']))? 0 : $input['CHAPTER_SPECIALS'];	
	$input['FIRST_CHAPTER'] =(int) (empty($input['FIRST_CHAPTER']))? -1 : $input['FIRST_CHAPTER'];	
	$input['LAST_CHAPTER'] =(int) (empty($input['LAST_CHAPTER']))? -1 : $input['LAST_CHAPTER'];	
	$input['GENDER'] =(int) ($input['GENDER'] < 1 || $input['GENDER'] > 4)? 1 : $input['GENDER'];
	$input['STATE'] =(int) ($input['STATE'] < 1 || $input['STATE'] > 3)? 1 : $input['STATE'];
	$input['FIRST_TOME'] =(int) (empty($input['FIRST_TOME']))? -1 : $input['FIRST_TOME'];	
	$input['LAST_TOME'] =(int) (empty($input['LAST_TOME']))? -1 : $input['LAST_TOME'];
	$input['INFOPNG'] =(int) (empty($input['INFOPNG']))? 0 : 1;
	
	if	(!is_numeric($input['FIRST_CHAPTER']) 
		|| !is_numeric($input['LAST_CHAPTER']) 
		|| !is_numeric($input['FIRST_TOME']) 
		|| !is_numeric($input['LAST_TOME']) 
		|| !is_numeric($input['STATE']) 
		|| !is_numeric($input['GENDER']) 
		|| !is_numeric($input['INFOPNG']) 
		|| !is_numeric($input['CHAPTER_SPECIALS'])
		|| $input['FIRST_CHAPTER'] > $input['LAST_CHAPTER']
		|| $input['FIRST_TOME'] > $input['LAST_TOME']
		)
		return false;
	return $input;
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
function switch_utf8_ascii($mixed, $out='ISO-8859-1//TRANSLIT', $in='UTF-8')
{
	if(is_array($mixed)){ // si on lui passe un array on recursive
		foreach($mixed as $key => $value){
			if(is_array($value))
				$out[$key] = switch_utf8_ascii($value, $out, $in);
			else
				$out[$key] = iconv($in, $out, $value);
		}
	}
	else
		$out = iconv($in, $out, $mixed);
	return $out;
}
/*************************************************/
// fonction remet les output_value[] en output[]value
function sort_array($array)
{
	$out = null;
	for($i=0;isset($array['LONG_PROJECT_NAME'][$i]) ;$i++)
		foreach($array as $key=>$value)
			$out[$i][$key] = (isset($value[$i]))? $value[$i] : null;
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


