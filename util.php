<?php

class Common
{

    /**
     * 生成微秒级订单号
     * @return  string  返回类型
     * @author nipeiquan
     */
    function create_sn()
    {
        $us = microtime();

        $us_arr = explode(' ', $us);

        $us_str = str_pad($us_arr[0] * 1000000, 6, 0);

        $time = substr($us_arr[1], 1);

        return $time . $us_str . str_pad(mt_rand(1, 9999), 4, "0", STR_PAD_LEFT);
    }

    /**
     * curl
     *
     * @param        $url
     * @param        $data
     * @param bool   $header
     * @param string $method
     *
     * @return  bool|string  返回类型
     * @author nipeiquan
     */
    function curl($url, $data, $header = false, $method = "POST")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }

    /**
     * 手机号打码
     *
     * @param $phone
     *
     * @return  mixed  返回类型
     * @author nipeiquan
     */
    function phoneProtect($phone)
    {

        if (!$phone) {
            return '';
        }
        //从第4位开始打4个*号
        return substr_replace($phone, '****', 3, 4);
    }

    /**
     * ID转邀请码
     *
     * @param $num
     *
     * @return  bool|string  返回类型
     * @author nipeiquan
     */
    function user_encode($num)
    {
        if ($num <= 0) {
            return false;
        }
        //十进制转36进制
        $char = base_convert($num, 10, 36);
        return $char;
    }

    /**
     * 邀请码至ID
     */
    function user_decode($num)
    {
        if ($num <= 0) {

            return false;
        }
        $char = base_convert($num, 36, 10);
        return $char;
    }

    /**
     * 格式化url获取参数
     * @param $query
     * @return  array  返回类型
     * @author nipeiquan
     */
    function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    /**
     * 敏感词正则匹配
     *
     * @param $str
     *
     * @return  bool  返回类型
     * @author nipeiquan
     */
    function getSensitiveWords($str)
    {
        $sensitive = '/select|insert|update|CR|document|LF|eval|delete|script|alert|\'|\/\*|\#|\--|\ --|\/|\*|\-|\+|\=|\~|\*@|\*!|\$|\%|\^|\&|\(|\)|\/|\/\/|\.\.\/|\.\/|union|into|load_file|outfile/';

        if (preg_match($sensitive, $str)) {
            return false;
        }

        return true;
    }

    /**
     * @Title      : 二维数组排序
     * @Description: todo()
     *
     * @param     $multi_array
     * @param     $sort_key
     * @param int $sort
     *
     * @return  array|bool  返回类型
     * @author     nipeiquan
     */
    function _multi_array_sort($multi_array, $sort_key, $sort = SORT_DESC)
    {
        if (is_array($multi_array)) {
            foreach ($multi_array as $row_array) {
                if (is_array($row_array)) {
                    $key_array[] = $row_array[$sort_key];
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
        array_multisort($key_array, $sort, $multi_array);
        return $multi_array;
    }

    function utf8_unicode($name)
    {
        $name = iconv('UTF-8', 'UCS-4', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0) {   //两个字节的文字
                $str .= base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
                //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
                //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        return substr($str, -4);
    }

    /**
     * 去除域名中的www
     * @param $domain
     * @return  string|string[]|null  返回类型
     * @author nipeiquan
     */
    function formatDomain($domain)
    {

        $domain = preg_replace("/www./", "", $domain,1);

        return $domain;
    }

    /**
     * 获取真实的url
     * @param $url
     * @return  false|mixed  返回类型
     * @author nipeiquan
     */
    function getRealUrl($url)
    {

        $header = get_headers($url, 1);
        if (strpos($header[0], '301') !== false || strpos($header[0], '302') !== false) {
            if(isset($header['Location'])){

                if (is_array($header['Location'])) {
                    return $header['Location'][count($header['Location']) - 1];
                } else {
                    return $header['Location'];
                }
            }
            if(isset($header['location'])){

                if (is_array($header['location'])) {
                    return $header['location'][count($header['location']) - 1];
                } else {
                    return $header['location'];
                }
            }
            return false;
        } else {
            return $url;
        }

    }

    /**
     * 检测是否为有效url地址
     * @param $url
     * @return  bool  返回类型
     * @author nipeiquan
     */
    function check_url($url){

        if (filter_var($url, FILTER_VALIDATE_URL) == false){

            return false;
        }

        return true;
    }

    /**
     * 去除字符串中的表情
     * @param string $str
     * @return  false|string  返回类型
     * @author nipeiquan
     */
    function filterEmoji($str=''){
        $str = json_encode($str);
        $str = json_decode(preg_replace("#(\\\ud[0-9a-f]{3})#i", "", $str));
        return iconv('gb2312//ignore', 'utf-8', iconv('utf-8', 'gb2312//ignore', $str));
    }

    /**
     * 数组去空
     * @param $array
     * @return  array|mixed|string  返回类型
     * @author nipeiquan
     */
    function filterNull($array){

        if(is_array($array)){
            return array_map(array(__CLASS__,"filterNull"),$array);
        }else{

            return is_null($array)?'':$array;
        }
    }

    /**
     * 去除字符串空格
     * @param $str
     * @return  string|string[]  返回类型
     * @author nipeiquan
     */
    function trimall($str){
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
        return str_replace($qian,$hou,$str);
    }

    /**
     * 不四舍五入保留小数
     * @param $number
     * @param $decimals
     * @return  string  返回类型
     * @author nipeiquan
     */
    function sprint_f($number,$decimals){

        $number = intval($number*100);

        $number = $number==0?0:$number/100;

        return number_format($number,$decimals,'.','');
    }

    /**
     * 格式化友好时间
     * @param      $timestamp
     * @param null $formats
     * @return  false|string  返回类型
     * @author nipeiquan
     */
    function formatDateTime($timestamp, $formats = null){
        if ($formats == null) {
            $formats = array(
                'DAY'           => '%s天前',
                'DAY_HOUR'      => '%s天%s小时前',
                'HOUR'          => '%s小时',
                'HOUR_MINUTE'   => '%s小时%s分前',
                'MINUTE'        => '%s分钟前',
                'MINUTE_SECOND' => '%s分钟%s秒前',
                'SECOND'        => '%s秒前',
            );
        }

        /* 计算出时间差 */
        $seconds = time() - $timestamp;
        $minutes = floor($seconds / 60);
        $hours   = floor($minutes / 60);
        $days    = floor($hours / 24);

        if ($days > 0 && $days < 31) {
            $diffFormat = 'DAY';
        } elseif($days == 0) {
            $diffFormat = ($hours > 0) ? 'HOUR' : 'MINUTE';
            if ($diffFormat == 'HOUR') {
                $diffFormat .= ($minutes > 0 && ($minutes - $hours * 60) > 0) ? '_MINUTE' : '';
            } else {
                $diffFormat = (($seconds - $minutes * 60) > 0 && $minutes > 0)
                    ? $diffFormat.'_SECOND' : 'SECOND';
            }
        }else{
            $diffFormat = 'TURE_DATE_TIME';//超出30天, 正常时间显示
        }

        $dateDiff = null;
        switch ($diffFormat) {
            case 'DAY':
                $dateDiff = sprintf($formats[$diffFormat], $days);
                break;
            case 'DAY_HOUR':
                $dateDiff = sprintf($formats[$diffFormat], $days, $hours - $days * 60);
                break;
            case 'HOUR':
                $dateDiff = sprintf($formats[$diffFormat], $hours);
                break;
            case 'HOUR_MINUTE':
                $dateDiff = sprintf($formats[$diffFormat], $hours, $minutes - $hours * 60);
                break;
            case 'MINUTE':
                $dateDiff = sprintf($formats[$diffFormat], $minutes);
                break;
            case 'MINUTE_SECOND':
                $dateDiff = sprintf($formats[$diffFormat], $minutes, $seconds - $minutes * 60);
                break;
            case 'SECOND':
                $dateDiff = sprintf($formats[$diffFormat], $seconds);
                break;
            default:
                $dateDiff = date('Y-m-d H:i:s');
        }
        return $dateDiff;
    }

    /**
     * 过滤图片
     * @param     $content
     * @param int $num
     * @return  mixed|string  返回类型
     * @author nipeiquan
     */
    function filterImgs($content,$num=0){

        $pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern,$content,$match);
        if(isset($match[1])&&!empty($match[1])){
            if($num===0){
                return $match[1];
            }
            if(is_numeric($num)&&isset($match[1][$num])){
                return $match[1][$num];
            }
        }
        return '';
    }

    /**
     * 通过两个经纬度计算距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return  array  返回类型
     * @author nipeiquan
     */
    function getDistanceBetweenPoints($lat1, $lng1, $lat2, $lng2) {
        $theta = $lng1 - $lng2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;//英里
        $feet = $miles * 5280;//英尺
        $kilometers = $miles * 1.609344;//千米
        $kilometers = round($kilometers,2);//千米
        return compact('miles','feet','kilometers');
    }

    /**
     * 获取毫秒时间戳
     * @return  mixed|string  返回类型
     * @author nipeiquan
     */
    function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    /**
     * 解析excel转数组（需要引入phpexcel）
     * @param $file
     * @return  array  返回类型
     * @author nipeiquan
     */
    function parseExcel($file){

        $reader = new \PHPExcel_Reader_Excel2007();

        $PHPExcel = $reader->load($file);

        $currentSheet = $PHPExcel->getSheet(0);

        //最大行号（1开始）
        $allRow = $currentSheet->getHighestRow();

        //取得最大列号（A开始）
        $allColumn = $currentSheet->getHighestColumn();

        $data = [] ;

        for($currentRow = 1;$currentRow <= $allRow;$currentRow++){

            $rowValue = [];

            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){

                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();

                $rowValue[] = $val;
            }

            $data[] = $rowValue;
        }

        return $data;
    }
}