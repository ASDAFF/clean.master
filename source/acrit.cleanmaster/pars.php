<?
include($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");
	$dir = $_SERVER['DOCUMENT_ROOT'];
	global $DB;
	$step=$_REQUEST["step"];
	function cleanCompTpls(){
		global $DB;
		$res=$DB->Query("select * from files where type='tpl'");
		while($r=$res->Fetch()){

			$res2=$DB->Query("select * from files where type<>'tpl' and cont LIKE '%".mysql_escape_string('"'.$r["cont"]).'","'.$r["path"].'"')."%' LIMIT 1");
			$r2=$res2->Fetch();
			if(!$r2["id"])
				DeleteDirFilesEx($r['fname']);
		}
	}
	function p($a){?><prE><?print_r($a);?></pre><?}
	function lDir($path,$scape_dir=array()){
		$a=array();
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					if (is_dir($path."/".$entry) === true  && !in_array(str_replace($_SERVER["DOCUMENT_ROOT"],"",$path."/".$entry),$scape_dir)){
						$a["dirs"][]=str_replace($_SERVER["DOCUMENT_ROOT"],"",$path."/".$entry);
					}elseif(!is_dir($path."/".$entry)){
						$a["files"][]=str_replace($_SERVER["DOCUMENT_ROOT"],"",$path."/".$entry);
					}
				}
			}
			closedir($handle);
		}
		return $a;
	}
	function lDirRecursive($path,$scape_dir=array(),$a){
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					if (is_dir($path."/".$entry) === true  && !in_array(str_replace($_SERVER["DOCUMENT_ROOT"],"",$path."/".$entry),$scape_dir)){
						$a=lDirRecursive($path."/".$entry,$scape_dir,$a);
					}elseif(!is_dir($path."/".$entry)){
						$a["files"][]=str_replace($_SERVER["DOCUMENT_ROOT"],"",$path."/".$entry);
					}
				}
			}
			closedir($handle);
		}
		return $a;
	}

switch($_REQUEST['act']){
case 'struct':

	if(intval($_REQUEST["step"])==0){
		$DB->Query("DROP TABLE `files`");
		$DB->Query("CREATE TABLE IF NOT EXISTS `files`(
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`cont` TEXT,
			`path` VARCHAR(255),
			`fname` VARCHAR(250),
			`type` VARCHAR(3),
			`checked` INT(1),
			PRIMARY KEY (id)
		) ENGINE=MyISAM;");
		$DB->Query("CREATE FULLTEXT INDEX `fs` ON `files` (`cont`)");
		$DB->Query("CREATE UNIQUE INDEX `fs2` ON `files` (`path`)");
		$DB->Query("TRUNCATE TABLE `files`");
		echo json_encode(array("act"=>"struct","nextstep"=>($step+1),"continious"=>true));
	}else{


	$a=lDir($dir,array("/bitrix","/upload","/images"));
	if($_REQUEST["step"]==1){
		$vals=array();
		foreach ($a["files"] as $file) {
			$vals[]="(id,'".(substr($file,-3)=="php"? mysql_escape_string(str_replace("\r","",str_replace("\n","",file_get_contents($dir.$file)))):"")."','".mysql_escape_string(basename($file))."','".mysql_escape_string($file)."','".substr($file,-3)."')";
		}
		//if(!empty($vals))
		//$DB->Query("INSERT IGNORE INTO `files` VALUES ".implode(",",$vals));
	}else{
		$vals=array();
		$res=lDirRecursive($dir.$a["dirs"][$_REQUEST["step"]-2],array("/bitrix","/upload","/images"));
		foreach ($res["files"] as $file) {
			$vals[]="(id,'".(substr($file,-3)=="php"? mysql_escape_string(str_replace("\r","",str_replace("\n","",file_get_contents($dir.$file)))):"")."','".mysql_escape_string(basename($file))."','".mysql_escape_string($file)."','".(substr($file,-3)=="php"?substr($file,-3):"")."','')";
		}
		if(!empty($vals))
		$DB->Query("INSERT IGNORE INTO `files` VALUES ".implode(",",$vals));

	}
	if(count($a["dirs"])+2>=$step)
		echo json_encode(array("act"=>"struct","nextstep"=>($step+1),"continious"=>true));
	else
		echo json_encode(array("act"=>"struct","nextstep"=>($step+1),"continious"=>false));
	}
break;
case 'tpl':
	$vals=array();
	$a=lDir($dir."/bitrix/templates");
	foreach($a["dirs"] as $tpl){
		$vals=array();
		$b=lDir($dir.$tpl);
		foreach($b["files"] as $file)
			$vals[]="(id,'".(substr($file,-3)=="php"? mysql_escape_string(str_replace("\r","",str_replace("\n","",file_get_contents($dir.$file)))):"")."','".mysql_escape_string(basename($file))."','".mysql_escape_string($file)."','".(substr($file,-3)=="php"?"php":"" )."','')";

		if(!empty($vals))
			$DB->Query("INSERT IGNORE INTO `files` VALUES ".implode(",",$vals));
	}
	$vals=array();

	foreach($a["dirs"] as $tpl){

		$b=lDir($dir.$tpl."/components");
		foreach($b["dirs"] as $cmp){
			$c=lDir($dir.$cmp);
			$cmp=basename($cmp);
			foreach($c["dirs"] as $tpl3){
				$d=lDir($dir.$tpl3);
				$tpl3=basename($tpl3);
				foreach($d["dirs"] as $tpl4)
				$vals[]="(id,'".$cmp.":".$tpl3."','".mysql_escape_string(basename($tpl4))."','".mysql_escape_string($tpl4)."','tpl','')";
			}
		}
	}
	if(!empty($vals))
		$DB->Query("INSERT IGNORE INTO `files` VALUES ".implode(",",$vals));
	echo json_encode(array("act"=>"tpl","nextstep"=>($step+1),"continious"=>false));
break;
case 'img':
	$vals=array();
	$a=lDirRecursive($dir."/upload");
	$c=count($a);
	$elstep=1000;
	for($i=($step*$elstep);$i<($elstep*($step+1));$i++){
		if($a["files"][$i]){
			$file=$a["files"][$i];
			$vals[]="(id,'".str_replace("/".basename($file),"",str_replace("/upload/","",$file))."','".mysql_escape_string(basename($file))."','".mysql_escape_string($file)."','img','')";
		}
	}
	if(!empty($vals))
		$DB->Query("INSERT IGNORE INTO `files` VALUES ".implode(",",$vals));

	if($c>$elstep*$step)
		echo json_encode(array("act"=>"img","nextstep"=>($step+1),"continious"=>true));
	else
		echo json_encode(array("act"=>"img","nextstep"=>($step+1),"continious"=>false));
break;
}