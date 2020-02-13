<?php 
/**
 * function common
 * */ 

function d($data = NULL, $is_output = true, $is_html_encode = true){
    $now_date_time = "[" . date("Y/m/d h:i:s") . "] " ;
	if(is_null($data)){
		$str = "<font color='red'><i>NULL</i></font>";
	}elseif($data === ""){
		$str = "<font color='red'><i>Empty string</i></font>";
	}elseif($data instanceof \Exception){
		$td1 = "<td style=\"background-color:#00AA00;color:#FFF;border-top:1px solid #000;border-right:1px solid #000;padding-left:5px;padding-right:5px;\">";
		$td2 = "<td style=\"border-top:1px solid #000;padding:5px;\">";

		$str = "<font color='red'><b>Exception:</b></font><br>";

		$str .= "<table style=\"border-bottom:1px solid #000;border-left:1px solid #000;border-right:1px solid #000;\" cellpadding=\"0\" cellspacing=\"0\">";
		$str 	.= "<tr>" . $td1 . "code</td>" . $td2 . $data->getCode() . "</td></tr>";
		$str 	.= "<tr>" . $td1 . "message</td>" . $td2 . $data->getMessage() . "</td></tr>";
		$str 	.= "<tr>" . $td1 . "file</td>" . $td2 . $data->getFile() . "</td></tr>";
		$str 	.= "<tr>" . $td1 . "line</td>" . $td2 . $data->getLine() . "</td></tr>";
		$str 	.= "<tr>" . $td1 . "previous</td>" . $td2 . $data->getPrevious() . "</td></tr>";
		$str 	.= "<tr>" . $td1 . "details</td>" . $td2 . $data->__toString() . "</td></tr>";
		$str .= "</table>";
	}elseif(is_array($data)){
		if(count($data) === 0){
			$str = "<span style ='color: #190cab;'>Array</span>(<span style ='color: #609;'>0</span>): []";
		}else{
			$str = "<span style ='color: #190cab;'>Array</span>(<span style ='color: #609;'>".count($data)."</span>): [";
            $str .= "<br/>";
            $str .= "<div style='padding-left:30px;'>";
			foreach ($data as $key => $value) {
                $str .= "<span style=\"color:#43c1a5;line-height:30px;\">" . $key . "</span>";
                $str .= "  =>  ";
                $str .= d($value, false);
                $str .= "<br/>";
            }
            $str .= "</div>";
            $str .= "]";
		}
	}elseif(is_resource($data)){
		$data_array = mysqli_fetch_all($data);
		$str = d($data_array, false);
	}elseif(is_object($data)){

		$reflect 					= new \ReflectionClass($data);
		$className 					= $reflect->getName();

		$arr["FullClassPathName"] 		= $className;
		$arr["Namespace"] 				= $reflect->getNamespaceName();
		$arr["ShortClassName"] 			= $reflect->getShortName();
		if(in_array($arr["ShortClassName"], array('mysql_result', 'mysqli_result'))){
			unset($arr["FullClassPathName"]);

			$arr["fetch_all"] 			= $data->fetch_all();

		}else{
			$arr["Attributes"] 			= get_object_vars($data);
			$arr["Methods"] 			= get_class_methods($className);
		}

		if(empty($arr["Namespace"])){
			unset($arr["Namespace"]);
		}
		if(empty($arr["Methods"])){
			unset($arr["Methods"]);
		}

		$str = d($arr, false);
	}elseif(is_numeric($data) && (gettype($data) !== "string")){
        if(isset($a)) {
            $str .= "(<span style ='color: #190cab;'>Numeric</span>) " . $data;
        }else{
            $str = "(<span style ='color: #190cab;'>Numeric</span>) " . $data;
        }
	}elseif(is_bool($data) && ($data === true || $data === false)){
		$str = "<font color='red'><i>" . (($data === true) ? "True" : "False") . "</i></font>";
	}else{
		$str = $data;
		if($is_html_encode){
			$str = htmlspecialchars($str);
		}
        $str = preg_replace("/(\r|\n)/", "<br>" . PHP_EOL, $str);
        $str = "(<span style ='color: #190cab;'>String</span>) " . $str;
	}

	if($is_output){
		echo $now_date_time.$str;
	}
	return $str;
}

function dn($data = NULL, $is_html_encode = true){
	d($data, true, $is_html_encode);
	echo "<br>" . PHP_EOL;
}

function dd($data = NULL, $is_html_encode = true){
	dn($data, $is_html_encode);
	exit;
}

function dt($message = ""){
	dn("[" . date("Y/m/d h:i:s") . "] " . $message);
}

function djson($json = NULL, $isExited = false){
	if(is_string($json)){
		$json = json_decode($json);
	}

	dn($json);

	if($isExited){
		exit;
	}
}

function ddjson($json = NULL){
	djson($json, true);
}

function debugMessage($message){
	dt($message);
}

function current_url()
{
	return $_SERVER['PHP_SELF'];
}

function getClientIp() {
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if(isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if(isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}


function toJpNengoDateTime(string $date=NULL, bool $hasTime = false)
{
	if(!isset($date) || (empty($date))){
		return '';
	}
	$japaneseEras = array(
		701  => '大宝',
		704  => '慶雲',
		708  => '和銅',
		715  => '霊亀',
		717  => '養老',
		724  => '神亀',
		729  => '天平',
		749  => '天平感宝',
		749  => '天平勝宝',
		757  => '天平宝字',
		765  => '天平神護',
		767  => '神護景雲',
		770  => '宝亀',
		781  => '天応',
		782  => '延暦',
		806  => '大同',
		810  => '弘仁',
		824  => '天長',
		834  => '承和',
		848  => '嘉祥',
		851  => '仁寿',
		854  => '斉衡',
		857  => '天安',
		859  => '貞観',
		877  => '元慶',
		885  => '仁和',
		889  => '寛平',
		898  => '昌泰',
		901  => '延喜',
		923  => '延長',
		931  => '承平',
		938  => '天慶',
		947  => '天暦',
		957  => '天徳',
		961  => '応和',
		964  => '康保',
		968  => '安和',
		970  => '天禄',
		973  => '天延',
		976  => '貞元',
		978  => '天元',
		983  => '永観',
		985  => '寛和',
		987  => '永延',
		988  => '永祚',
		990  => '正暦',
		995  => '長徳',
		999  => '長保',
		1004  => '寛弘',
		1012  => '長和',
		1017  => '寛仁',
		1021  => '治安',
		1024  => '万寿',
		1028  => '長元',
		1037  => '長暦',
		1040  => '長久',
		1044  => '寛徳',
		1046  => '永承',
		1053  => '天喜',
		1058  => '康平',
		1065  => '治暦',
		1069  => '延久',
		1074  => '承保',
		1077  => '承暦',
		1081  => '永保',
		1084  => '応徳',
		1087  => '寛治',
		1094  => '嘉保',
		1096  => '永長',
		1097  => '承徳',
		1099  => '康和',
		1104  => '長治',
		1106  => '嘉承',
		1108  => '天仁',
		1110  => '天永',
		1113  => '永久',
		1118  => '元永',
		1120  => '保安',
		1124  => '天治',
		1126  => '大治',
		1131  => '天承',
		1132  => '長承',
		1135  => '保延',
		1141  => '永治',
		1142  => '康治',
		1144  => '天養',
		1145  => '久安',
		1151  => '仁平',
		1154  => '久寿',
		1156  => '保元',
		1159  => '平治',
		1160  => '永暦',
		1161  => '応保',
		1163  => '長寛',
		1165  => '永万',
		1166  => '仁安',
		1169  => '嘉応',
		1171  => '承安',
		1175  => '安元',
		1177  => '治承',
		1181  => '養和',
		1182  => '寿永',
		1184  => '元暦',
		1185  => '文治',
		1190  => '建久',
		1199  => '正治',
		1201  => '建仁',
		1204  => '元久',
		1206  => '建永',
		1207  => '承元',
		1211  => '建暦',
		1213  => '建保',
		1219  => '承久',
		1222  => '貞応',
		1224  => '元仁',
		1225  => '嘉禄',
		1227  => '安貞',
		1229  => '寛喜',
		1232  => '貞永',
		1233  => '天福',
		1234  => '文暦',
		1235  => '嘉禎',
		1238  => '暦仁',
		1239  => '延応',
		1240  => '仁治',
		1243  => '寛元',
		1247  => '宝治',
		1249  => '建長',
		1256  => '康元',
		1257  => '正嘉',
		1259  => '正元',
		1260  => '文応',
		1261  => '弘長',
		1264  => '文永',
		1275  => '建治',
		1278  => '弘安',
		1288  => '正応',
		1293  => '永仁',
		1299  => '正安',
		1302  => '乾元',
		1303  => '嘉元',
		1306  => '徳治',
		1308  => '延慶',
		1311  => '応長',
		1312  => '正和',
		1317  => '文保',
		1319  => '元応',
		1321  => '元亨',
		1324  => '正中',
		1326  => '嘉暦',
		1329  => '元徳',
		1331  => '元弘',
		1334  => '建武',
		1336  => '延元',
		1340  => '興国',
		1346  => '正平',
		1370  => '建徳',
		1372  => '文中',
		1375  => '天授',
		1381  => '弘和',
		1384  => '元中',
		1338  => '暦応',
		1342  => '康永',
		1345  => '貞和',
		1350  => '観応',
		1352  => '文和',
		1356  => '延文',
		1361  => '康安',
		1362  => '貞治',
		1368  => '応安',
		1375  => '永和',
		1379  => '康暦',
		1381  => '永徳',
		1384  => '至徳',
		1387  => '嘉慶',
		1389  => '康応',
		1390  => '明徳',
		1394  => '応永',
		1428  => '正長',
		1429  => '永享',
		1441  => '嘉吉',
		1444  => '文安',
		1449  => '宝徳',
		1452  => '享徳',
		1455  => '康正',
		1457  => '長禄',
		1460  => '寛正',
		1466  => '文正',
		1467  => '応仁',
		1469  => '文明',
		1487  => '長享',
		1489  => '延徳',
		1492  => '明応',
		1501  => '文亀',
		1504  => '永正',
		1521  => '大永',
		1528  => '享禄',
		1532  => '天文',
		1555  => '弘治',
		1558  => '永禄',
		1570  => '元亀',
		1573  => '天正',
		1592  => '文禄',
		1596  => '慶長',
		1615  => '元和',
		1624  => '寛永',
		1644  => '正保',
		1648  => '慶安',
		1652  => '承応',
		1655  => '明暦',
		1658  => '万治',
		1661  => '寛文',
		1673  => '延宝',
		1681  => '天和',
		1684  => '貞享',
		1688  => '元禄',
		1704  => '宝永',
		1711  => '正徳',
		1716  => '享保',
		1736  => '元文',
		1741  => '寛保',
		1744  => '延享',
		1748  => '寛延',
		1751  => '宝暦',
		1764  => '明和',
		1772  => '安永',
		1781  => '天明',
		1789  => '寛政',
		1801  => '享和',
		1804  => '文化',
		1818  => '文政',
		1830  => '天保',
		1844  => '弘化',
		1848  => '嘉永',
		1858  => '安政',
		1860  => '万延',
		1861  => '文久',
		1864  => '元治',
		1865  => '慶応',
		1868  => '明治',
		1912  => '大正',
		1926  => '昭和',
		1989  => '平成',
		2019  => '令和',
	);

	$dates = date_parse($date);
	$year = $dates['year'];

	$eraYear = 0;
	$era = '';
	foreach ($japaneseEras as $eraYearKey => $eraValue) {
		if($eraYearKey < $year){
			$eraYear = $eraYearKey;
			$era = $eraValue;
		}else{
			break;
		}
	}

	$eraYear = $year - $eraYear + 1;

	$dates['year'] = $era . $eraYear;

	$japaneseDate = $dates['year'] . '年' . $dates['month'] . '月' . $dates['day'] . '日';
	if($hasTime){
		$japaneseDate .= ' ' . $dates['hour'] . ':' . $dates['minute'] . ':' . $dates['second'];
	}

	return $japaneseDate;
}

?>