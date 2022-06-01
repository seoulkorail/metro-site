<?php
    ini_set('display_errors', '0');
    function getDataS($linenum){
        $url = "https://smss.seoulmetro.co.kr/api/3010.do";

        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "lineNumCd=".$linenum);
    
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true)["ttcVOList"];
    }
    function getDataT($linenum){
        $param = "100".$linenum;
        if($linenum == "SI")
            $param = "1077";
        else if($linenum == "A")
            $param = "1065";
        else if($linenum == "UI")
            $param = "1092";
        $url = "http://m.bus.go.kr/mBus/subway/getLcByRoute.bms?subwayId=".$param;
        $curlsession = curl_init ();
        curl_setopt ($curlsession, CURLOPT_URL, $url);
        curl_setopt ($curlsession, CURLOPT_RETURNTRANSFER, 1);
        $buffer = curl_exec ($curlsession);
        curl_close($curlsession);

        $buffer = json_decode(iconv("EUC-KR", "UTF-8", $buffer), true);
        $buffer = $buffer["resultList"];
        //이름:
        $re = [];
        foreach ($buffer as $e){
            $trainNo = ltrim($e["trainNo"], "0");
            $tmp["trainSttus"] = $e["trainSttus"];
            $tmp["statnTnm"] = $e["statnTnm"];
            $tmp["directAt"] = $e["directAt"];
            $tmp["statnNm"] = $e["statnNm"];
            $tmp["updnLine"] = $e["updnLine"];
            $re[$trainNo] = $tmp;
        }
        return $re;
    }

    $line = $_GET["line"];

    if(!($line == "A" || $line == "SI" || $line == "UI" || $line == "9")){
        $data = getDataS($line);
        
        $topisdata = null;
        if($line == "1" || $line == "3" || $line == "4" || $line == "7")
            $topisdata = getDataT($line);

        $keyset = ["1" => "진입", "2" => "도착", "3" => "출발", "4" => "운행중"];
        $result = [];
        foreach($data as $e){
            $tmp=[];
            if($line !== "2")
                $trainY = ($line == "3" || $line == "4" ? substr($e['trainY'], 0, 1).$line.substr($e['trainY'], 2) : substr($e["trainY"], 0, 1).ltrim(substr($e["trainY"], -4), '0'));
            else
                $trainY = "S".$e["trainY"];
            $tmp["trainNo"] = $trainY;
            $tmp["trainP"] = $e['trainP'] ?? "";
            if($e["trainP"] == "000")
                $tmp["trainP"] = "";
            $tmp["sts"] = $keyset[$e["sts"]];
            $tmp["dir"] = ($e["dir"] == "1" ? "상행" : "하행");
            $tmp["statnNm"] = preg_replace('/\([0-9가-힣A-z]+\)$/u', '', $e["stationNm"]);
            $tmp["statnTnm"] = preg_replace('/\([0-9가-힣A-z]+\)$/u', '', ($topisdata != null ? $topisdata[substr($trainY, 1)]["statnTnm"] ?? $e["statnTnm"] : $e["statnTnm"]));
            $tmp["directAt"] = $e["directAt"] ?? 0;
            $result[] = $tmp;
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit();
    }

    $data = getDataT($line);

    $result = [];
    foreach($data as $key => $e){
        $tmp=[];
        $trainY = $key."";

        if($line !== "SI"){
            $tmp["trainNo"] = $trainY;
            $tmp["trainP"] = "";
        } else {
            $tmp["trainNo"] = "모름";
            $tmp["trainP"] = "D".$trainY;
        }

        $tmp["sts"] = ($e["trainSttus"] == "전역출발" ? "진입" : $e["trainSttus"]);
        $tmp["dir"] = $e["updnLine"];
        $tmp["statnNm"] = preg_replace('/\([0-9가-힣A-z]+\)$/u', '', $e["statnNm"]);
        $tmp["statnTnm"] = preg_replace('/\([0-9가-힣A-z]+\)$/u', '', $e["statnTnm"]);
        $tmp["directAt"] = $e["directAt"] ?? 0;
        $result[] = $tmp;
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>