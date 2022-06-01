<?php
$linenum = $_GET["line"];

switch($linenum){
    case "1":
        $param = "1001";
        break;
    case "2":
        $param = "1002";
        break;
    case "3":
        $param = "1003";
        break;
    case "4":
        $param = "1004";
        break;
    case "5":
        $param = "1005";
        break;
    case "6":
        $param = "1006";
        break;
    case "7":
        $param = "1007";
        break;
    case "8":
        $param = "1008";
        break;
    case "9":
        $param = "1009";
        break;
    case "G":
        $param = "1067";
        break;
    case "K":
        $param = "1063";
        break;
    case "S":
        $param = "1077";
        break;
    case "GH":
        $param = "1065";
        break;
    case "U":
        $param = "1092";
        break;
    case "KK":
        $param = "0000";
        break;
    case "SU":
        $param = "0000";
        break;
    case "I":
        $param = "1069";
        break;
}

    $agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36';
    $curlsession = curl_init ();
    curl_setopt ($curlsession, CURLOPT_URL, "http://m.bus.go.kr/mBus/subway/getStatnByRoute.bms?subwayId=".$param); // 파싱 주소 url
    //curl_setopt ($curlsession, CURLOPT_SSL_VERIFYPEER, FALSE); // 인증서 체크같은데 true 시 안되는 경우가 많다.
    //curl_setopt ($curlsession, CURLOPT_SSLVERSION,3); // SSL 버젼 (https 접속시에 필요)
    curl_setopt ($curlsession, CURLOPT_HEADER, 0);
    curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curlsession, CURLOPT_POST, 0); // POST = 1, GET = 0
    curl_setopt ($curlsession, CURLOPT_USERAGENT, $agent);
    curl_setopt ($curlsession, CURLOPT_TIMEOUT, 120); // 해당 웹사이트가 오래걸릴수 있으므로 2분동안 타임아웃 대기
    $buffer = curl_exec ($curlsession);
    $buffer = json_decode(iconv("EUC-KR", "UTF-8", $buffer), true);

    $cinfo = curl_getinfo($curlsession);
    curl_close($curlsession);
 
    if ($cinfo['http_code'] != 200){
        return $cinfo['http_code'];
    }

    $a =[];
    foreach($buffer["resultList"] as $e){
        $stnm = preg_replace('/\([0-9가-힣A-z]+\)$/u', '', $e["statnNm"]);
        $a[] = $stnm;
    }
    echo json_encode($a, JSON_UNESCAPED_UNICODE);
    exit();
?>