<?php // ﷽‎
namespace RapTToR;

/**
 * @author rapttor
 *
 * require __DIR__ . '/protected/vendor/autoload.php';
 */
$RapTToR_HELPER = array();

class Helper extends \Controller
{

    public static function mime_content_type($filename)
    {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    public static function timePassed($time)
    {
        if (is_null($time) || $time == "") return "";
        if (is_string($time)) $time = strtotime($time);
        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $intPlural = 0;

        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        $arTimeUnits = array(
            'year' => ['year', 'years'],
            'month' => ['month', 'months'],
            'week' => ['week', 'weeks'],
            'day' => ['day', 'days'],
            'hour' => ['hour', 'hours'],
            'minute' => ['minute', 'minutes'],
            'second' => ['second', 'seconds']
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            } else {
                $numberOfUnits = floor($time / $unit);

                if ($numberOfUnits > 1) {
                    $intPlural = 1;
                }

                return $numberOfUnits . ' ' . $arTimeUnits[$text][$intPlural];
                // return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
            }
        }
    }

    public static function mapArray($ar, $key = "id")
    {
        $arNew = array();
        foreach ($ar as $value) {
            $newKey = null;
            if (is_object($value) && property_exists($value, $key))
                $newKey = $value->$key;
            if (is_array($value) && isset($value[$key]))
                $newKey = $value[$key];
            $arNew[$newKey] = $value;
        }
        return $arNew;
    }

    public static function urlClean($str, $delimiter = '-')
    {
        $str = trim($str);
        setlocale(LC_ALL, 'en_US.UTF8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[|+ -]+/", $delimiter, $clean);
        return $clean;
    }

    public static function header($title, $icon = null)
    {
        return '<h1 class="pull-right"><i class="icons icon-' . $icon . ' pull-right"></i>
            ' . $title . ' &nbsp;</h1>';
    }

    public static function checkEmail($mail, $disposable = null)
    {
        $disposable_mail = array();
        if (is_null($disposable)) {
            $base = "";
            $disposable_mail = file_get_contents($base . "/../protected/modules/email/disposable-email.csv");
            $disposable_mail = explode(",", $disposable_mail);
        }
        foreach ($disposable_mail as $disposable) {
            list(, $mail_domain) = explode('@', $mail);
            if (strcasecmp($mail_domain, $disposable) == 0) {
                return true;
            }
        }
        return false;
    }

    public static function imgurl($cat = null, $id = null)
    {
        $base = "";
        return $base . "/uploads/" . $cat . "/" . $id . ".jpg";
    }

    public static function img($cat = null, $id = null, $class = "img-responsive")
    {
        return "<img src='" . self::imgurl($cat, $id) . "'  class='$class'>";
    }

    public static function uploadDir($cat = null)
    {
        $base = "";
        if (isset($_SERVER["HTTP_HOST"])) {
            $base = $_SERVER['DOCUMENT_ROOT'] . "/";
        }
        return $base . "/uploads/" . (is_null($cat) ? "" : $cat . '/');
    }

    public static function arrayValue($a, $i, $default = null)
    {
        return (is_array($a) && isset($a[$i])) ? $a[$i] : $default;
    }


    public static function rand_date($min_date = "01-01-2016", $max_date = "31-12-2016")
    {
        /* Gets 2 dates as string, earlier and later date.
           Returns date in between them.
        */

        $min_epoch = strtotime($min_date);
        $max_epoch = strtotime($max_date);

        $rand_epoch = rand($min_epoch, $max_epoch);

        return date('Y-m-d H:i:s', $rand_epoch);
    }

    public static function domain($str, $dom = "")
    {
        return (strpos($str, "http") === false) ? "http://" . $str : $str;
    }

    public static function link($url, $text = null, $options = 'target="_blank"')
    {
        if (is_null($text)) $text = $url;
        $link = self::domain($url);
        return "<a href='$link' $options>$text</a>";
    }

    public static function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime, new DateTimeZone(date_default_timezone_get()));
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public static function ago($tm, $rcs = 0)
    {
        if (is_string($tm)) $tm = strtotime($tm);
        $cur_tm = time();
        $dif = $cur_tm - $tm;
        $pds = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--);
        if ($v < 0) $v = 0;
        $_tm = $cur_tm - ($dif % $lngh[$v]);
        $no = floor($no);
        if ($no <> 1)
            $pds[$v] .= 's';
        $x = sprintf("%d %s ", $no, $pds[$v]);
        if (($rcs == 1) && ($v >= 1) && (($cur_tm - $_tm) > 0))
            $x .= self::ago($_tm);
        return $x;
    }

    public static function more($str, $length = 200, $more = "<!-- more -->")
    {
        if (strlen($str) < $length)
            return $str;

        $id = "SH" . sha1($str);
        $length = (strpos($str, $more) !== false) ? strpos($str, $more) : $length;

        return "<div id='$id'><div class='excerpt'>" . substr($str, 0, $length) . "</div><div style='display:none;' class='more'>" . substr($str, $length, strlen($str)) . "</div>
        </div><a href='javascript:;' title='$length / " . strlen($str) . "' style='cursor:pointer;' onclick='$(\"#$id .more\").toggle();'>[...]</a>";
    }



    public static function IconMenu($menu)
    {
        $result = "";
        foreach ($menu as $m) $result .= self::Icon($m);
        return $result;
    }

    public static function Icon($i)
    {
        $result = "";
        if (!isset($i["value"]) && isset($i["url"])) $i["value"] = $i["url"];
        if (isset($i["value"]) && isset($i["ion"]) && isset($i["title"])) $result = "<div class='icontext' onclick='window.location.href=\"" .
            $i["value"] . "\"'>
        <i class='{$i["ion"]}'></i>
        <small>{$i["title"]}</small>
        </div>";

        return $result;
    }

    public static function aVal($a, $k, $d = "")
    {
        if (is_object($a)) $a = (array)$a;
        return (is_array($a) && isset($a[$k])) ? $a[$k] : $d;
    }

    public static function aFind($a, $k, $v)
    {
        if (is_array($a)) foreach ($a as $item) {
            if (is_array($item) && isset($item[$k]) && $item[$k] == $v) return $item;
            if (is_object($item)) foreach ($item as $key => $value)
                if ($k == $key && $v == $value) return $item;
        }
        return null;
    }

    public static function back($title = "Back")
    {
        return "<div class='clearfix'></div><a style='clear:both;margin:10px 0;' class='btn btn-primary' onclick='history.go(-1);'><i class='fa fa-caret-left'></i> " .
            $title . "</a><div class='clearfix'></div>";
    }

    public static function is_json($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string, true))))) ? true : false;
    }

    public static function json_validate($string)
    {
        // decode the JSON data
        $result = json_decode($string);

        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
                // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            // throw the Exception or exit // or whatever :)
            exit($error);
        }

        // everything is OK
        return $result;
    }

    public static function jsonValue($arr, $key, $def)
    {
        $value = $def;
        if (isset($arr[$key]) && strlen(trim(strip_tags($arr[$key]))) > 2) $value = json_decode($arr[$key]);
        return $value;
    }

    public static function jsonError()
    {
        $error = null;
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
                // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
                // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }
        return $error;
    }

    public static function send($data, $cache = false, $die = true, $convert = true)
    {
        if ($convert) {
            if (is_array($data))
                $data = json_encode($data);
            $json = (!self::is_json($data)) ? json_encode($data, JSON_PRETTY_PRINT) : $data;
        }
        if (!$cache) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 1 Avg 1999 05:00:00 GMT');
        }
        header('Content-Type: application/json');
        echo $json;
        if ($die) {
            die;
        }
    }


    public static function map($str, $params)
    {
        foreach ($params as $key => $value)
            $str = str_replace($key, $value, $str);
        return $str;
    }


    public static function curl($url)
    {
        if (!function_exists('curl_version')) {
            exit("Enable cURL in PHP");
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $data = curl_exec($ch);
        $error = "";
        if (isset($_GET["debug"]))
            $error = 'Curl error: ' . curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode >= 200 && $httpcode < 300 && $data) {
            return $data;
        } else {
            error_log($error);
            return $error;
        }
    }

    public static function exportModelAsJson($data)
    {
        return $json = (!self::is_json($data)) ? json_encode($data) : $data;
    }

    public static function ellipsis($text, $length)
    {
        return (mb_strlen($text) > $length) ? mb_substr($text, 0, $length) . '... ' : $text;
    }

    public static function replaceAll($what, $with, $str)
    {
        while (stripos($str, $what)) $str = str_ireplace($what, $with, $str);
        return $str;
    }

    public static function urlText($str)
    {
        return self::replaceAll('__', '_', preg_replace('/[^\w]/', '_', $str));
    }

    public static function cors()
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    /**
     * Sort array or arrays items on key
     * @param $array
     * @param $key
     * @param bool $desc
     * @return array
     */
    public static function sortItems($array, $key, $desc = false)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        if ($desc) $sorter = array_reverse($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;

        return $array;
    }

    /**
     * @param $str
     * @param string $delimiter
     * @return array
     */
    public static function str2arr($str, $delimiter = ',')
    {
        $arr = array();
        if (is_string($str)) $arr = explode($delimiter, $str);
        foreach ($arr as $key => $value) $arr[$key] = trim($value);
        $arr = array_unique($arr);
        return $arr;
    }

    public static function vardumper($object)
    {
        echo "<pre>";
        var_dump($object);
        die;
    }


    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public static function validateTime($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


    public function btnEdit($obj, $params = array())
    {
        $objClass = get_class($obj);
        $class = (isset($params["class"])) ? $params["class"] : "pull-right";
        $check = (isset($params["check"])) ? $params["check"] : true;
        $showTitle = (isset($params["btnTitle"])) ? true : false;
        $canEdit = true;
        if ($check) $canEdit = $this->canEdit();
        if ($canEdit) {
            $title = false;
            $url = $this->linkEdit($obj);
            if (is_object($obj)) {
                $title = ((property_exists($obj, "title")) ? $obj->title : $objClass);
            } else if (is_array($obj) && isset($obj["class"])) {
                $title = $obj["class"];
            } else if (is_string($obj)) $title = $obj;
            $title = (isset($params["btnTitle"])) ? $params["btnTitle"] : $title;
            if (!$showTitle) $title = "";
            return '<a class="btn btn-edit btn-link ' . $class . '" href="' . $url . '">
                    <i class="la la-pencil"></i> 
                    <span class="title">' . $title . '</span>
                </a>';
        }
        return "";
    }

    public function btnView($obj, $class = "pull-right", $check = true)
    {
        $url = $this->linkView($obj);
        $title = (property_exists($obj, "title")) ? $obj->title : get_class($obj);
        return '<a class="btn btn-edit btn-link ' . $class . '" href="' . $url . '">
                    <i class="la la-pencil"></i> 
                    <span class="title">' . $title . '</span>
                </a>';
    }

    public function btnAdd($type, $params = array())
    {
        if (is_object($type))
            $type = strtolower(get_class($type));

        $class = (isset($params["class"])) ? $params["class"] : "pull-right";
        $check = (isset($params["check"])) ? $params["check"] : true;
        $title = (isset($params["btnTitle"])) ? $params["btnTitle"] : 'Add ' . $type;
        $canEdit = true;
        if ($check) $canEdit = $this->canEdit();
        if ($canEdit) {
            $url = $this->linkAdd($type, $params);
            return '<a title="' . $title . '" class="btn btn-edit btn-link ' . $class . '" href="' . $url . '">
                    <i class="la la-plus"></i> 
                    <span class="title">' . $title . '</span>
                </a>';
        }
        return "";
    }

    public static function debug($message, $type = "info", $value = null)
    {
        global $RapTToR_HELPER;
        $debug = array("message" => $message, "type" => $type, "value" => $value);
        $RapTToR_HELPER["debug"][] = $debug;
        error_log(json_encode($debug));
    }

    /**
     * @param $strViewFile
     * @param array $arVariables
     * @param bool $return false
     * @param null $sendthis null - pass $this inside object methods, accessible in template
     * @return bool|string
     */
    public static function template($strViewFile, $arVariables = [], $return = false, $sendthis = null)
    {
        $strTemplate = $strViewFile . ".php";
        $strResult = "";
        if (file_exists($strTemplate)) {
            if (is_object($arVariables)) $arVariables = (array)$arVariables;
            if (!is_null($sendthis)) $arVariables["this"] = $sendthis;
            extract($arVariables);
            if ($return) ob_start();
            include($strTemplate);
            if ($return) $strResult = ob_get_clean();
            if ($return) {
                return $strResult;
            } else {
                echo $strResult;
                return true;
            }
        } else return "Template not found $strViewFile";
    }

    /**
     * @param $filehandle = fopen("{$filename}", "r")
     * @param $callback function($data);
     * @param int $rows 1000
     * @param string $delimiter ,
     * @param bool $close true
     */
    public function processLargeCSV($filehandle, $callback, $rows = 1000, $delimiter = ",", $close = true)
    {
        while (($data = fgetcsv($filehandle, $rows, $delimiter)) !== FALSE) {
            call_user_func($callback, $data);
        }
        if ($close) fclose($filehandle);
    }
}
