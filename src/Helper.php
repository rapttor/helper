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


    public static function imageCreateFromAny($filename)
    {
        if (!file_exists($filename)) {
            // throw new InvalidArgumentException('File "' . $filename . '" not found.');
            return false;
        }
        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
                break;

            case 'png':
                return imagecreatefrompng($filename);
                break;

            case 'gif':
                return imagecreatefromgif($filename);
                break;

            default:
                throw new Exception('File "' . $filename . '" is not valid jpg, png or gif image.');
                break;
        }
    }

    public static function exceptions_error_handler($severity, $message, $filename, $lineno)
    {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }

    // set_error_handler('exceptions_error_handler');

    public static function createThumb($src, $dest, $desired_height)
    {
        /* read the source image */
        $source_image = self::imageCreateFromAny($src);
        if ($source_image) {
            $width = imagesx($source_image);
            $height = imagesy($source_image);
            /* find the "desired height" of this thumbnail, relative to the desired width  */
            $desired_width = floor($width * ($desired_height / $height));
            /* create a new, "virtual" image */
            $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
            /* copy source image at a resized size */
            imagecopyresized($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
            //    imageantialias($virtual_image, true); //enose PHP ei toeta seda
            /* create the physical thumbnail image to its destination */
            imagejpeg($virtual_image, $dest);
        }
    }

    public static function array0($title = "None selected", $id = 0)
    {
        return array($id => $title);
    }

    public static function metaKey($value, $table = "item")
    {
        return $table . "_" . $value;
    }

    /**
     * @param array $values
     * @return array 
     */
    public static function arrWithValues($value)
    {
        $arr = (array)$value;
        $result = array();
        foreach ($arr as $key => $v) {
            $temp = json_encode($v);
            if (strlen($temp) > 2 && $temp !== "null") {
                $result[$key] = $v;
                if ((int)$v == $v && is_numeric($v)) $result[$key] = (int)$v;
                if ((float)$v == $v && is_numeric($v)) $result[$key] = (float)$v;
            }
        }
        return $result;
    }


    /**
     * @param $pass
     * @return string
     */
    public static function encryptPassword($pass)
    {
        return sha1($pass);
    }


    /**
     * Build json status
     * @param $status
     * @param string $message
     * @return array
     */
    public static function jsonStatus($status = "OK", $message = "")
    {
        return array("status" => $status, "message" => $message);
    }


    // for criteria, ids to get for processing.
    public static function None($title = "None selected")
    {
        return array(0 => $title);
    }




    public static function unitsList()
    {
        $result = array();
        $units = self::unitsOfMeasurement();
        foreach ($units as $k => $u) {
            $result[$k] = array();
            foreach ($u as $ku => $kv)
                $result[$k][$ku] = $kv["title"];
        }
        return $result;
    }

    // system root
    public static function base()
    {
        return dirname(__FILE__);
    }

    // translate service
    public static function t($section, $text, $language = "en")
    {
        $base = self::base();
        $filename = $base . '/languages/' . $language . '/' . $section . '.json';
        if (is_file($filename)) {
            $lang = json_decode($filename, true);
            if (isset($lang[$text]))
                $text = $lang[$text];
        }
        return $text;
    }

    public static function unitsOfMeasurement()
    {
        $units = array(
            self::t('front', "Units volume") => array(
                101 => array("title" => self::t('front', "Liter"), "value" => 1),
                102 => array("title" => self::t('front', "Gallon UK"), "value" => 4.54609),
                103 => array("title" => self::t('front', "Gallon US"), "value" => 3.78541),
                104 => array("title" => self::t('front', "m3"), "value" => 1),
            ),
            self::t('front', "Units length") => array(
                201 => array("title" => self::t('front', "Centimeter"), "value" => 0.01),
                202 => array("title" => self::t('front', "Millimeter"), "value" => 0.001),
                203 => array("title" => self::t('front', "Meter"), "value" => 1),
                204 => array("title" => self::t('front', "Yard"), "value" => 0.9144),
                205 => array("title" => self::t('front', "Inch"), "value" => 0.0254),
                206 => array("title" => self::t('front', "Feet"), "value" => 0.3048),
                207 => array("title" => self::t('front', "Kilometre"), "value" => 1000),
                208 => array("title" => self::t('front', "Mile"), "value" => 1609.34),
                209 => array("title" => self::t('front', "Nautical mile"), "value" => 1852),
            ),
            self::t('front', "Units weight") => array(
                301 => array("title" => self::t('front', "Kilogram"), "value" => 1),
                302 => array("title" => self::t('front', "Kilogram"), "value" => 1),
                303 => array("title" => self::t('front', "Tonne"), "value" => 1000),
                304 => array("title" => self::t('front', "UK Tonn"), "value" => 1016.05),
                305 => array("title" => self::t('front', "US Tonn"), "value" => 907.185),
                306 => array("title" => self::t('front', "Pound"), "value" => 0.453592),
                307 => array("title" => self::t('front', "Ounce"), "value" => 0.0283495),
            )
        );
        return $units;
    }

    public static function exec($cmd)
    {
        $result = array();
        $cmd = escapeshellcmd($cmd);
        ob_start();
        $result["output"] = shell_exec($cmd);
        $result["content"] = ob_get_contents();
        ob_end_clean(); //Use this instead of ob_flush()
        return $result;
    }



    public static function cdnImage($image, $replace = array())
    {
        // https://cdn.statically.io/img/cdn.rapttor.com/f=auto/influencer/dashboard/uploads/companymedia/1/31.jpg
        $with = '';
        $img = str_ireplace($replace, $with, $image);
        if (substr($img, 0, 4) != 'http' && substr($img, 0, 1) == '/')
            $img = 'https://cdn.statically.io/img/cdn.rapttor.com/f=auto' . $image;
        return $img;
    }


    public static function saveData($file, $json, $delete = false)
    {
        $cmp = gzcompress($json);
        if ($delete) {
            if (is_file($file)) unlink($file);
            if (is_file($file . '.gz')) unlink($file . '.gz');
        }
        $okc = @file_put_contents($file . '.gz', $cmp);
        $ok = @file_put_contents($file, $json);
        return $okc && $ok;
    }

    public static function remoteUserId($nick = null)
    {
        $remoteport = self::aVal($_SERVER, "REMOTE_PORT");
        $remoteaddr = self::aVal($_SERVER, "REMOTE_ADDR");
        $useragent = self::aVal($_SERVER, "HTTP_USER_AGENT");
        $combined = $remoteport . $remoteaddr . $useragent . $nick;
        $uid = sha1($combined);
        // echo $combined.' '.$uid;
        return $uid;
    }

    public static function retrieveJsonPostData($map = false)
    {
        $rawData = file_get_contents("php://input");
        $data = null;
        if ($map && $rawData) {
            $data = json_decode($rawData, true);
            foreach ($data as $k => $v)
                if (!isset($_REQUEST[$v])) {
                    $_REQUEST[$k] = $v;
                }
            return $data;
        }
    }

    public static function showErrors()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public static function SocialNetworks()
    {

        $networks = array(
            "facebook" => array("title" => "facebook", "sign" => 'facebook', "prefix" => "https://facebook.com/"),
            "linkedin" => array("title" => "linkedin", "sign" => 'linkedin', "prefix" => "https://linkedin.com/"),
            "twitter" => array("title" => "twitter", "sign" => 'twitter', "prefix" => "http://twitter.com/"),
            "instagram" => array("title" => "instagram", "sign" => 'instagram', "prefix" => "http://instagram.com/"),
            "youtube" => array("title" => "youtube", "sign" => 'youtube', "prefix" => "http://youtube.com/"),
            "pinterest" => array("title" => "pinterest", "sign" => 'pinterest', "prefix" => "http://pinterest.com/"),
            "tiktok" => array("title" => "tiktok", "sign" => 'tiktok', "prefix" => "http://tiktok.com/"),
            "twitch" => array("title" => "twitch", "sign" => 'twitch', "prefix" => "http://twitch.tv/"),
            "soundcloud" => array("title" => "soundcloud", "sign" => 'soundcloud', "prefix" => "http://soundcloud.com/"),
            "amliop" => array("title" => "amliop", "sign" => 'amliop', "prefix" => "http://amliop.com/"),
            "amazon" => array("title" => "amazon", "sign" => 'amazon', "prefix" => "http://amazon.com/"),
            "ebay" => array("title" => "ebay", "sign" => 'ebay', "prefix" => "http://ebay.com/"),
            "linktree" => array("title" => "linktree", "sign" => 'linktree', "prefix" => "http://linktr.ee/"),
            "shopify" => array("title" => "shopify", "sign" => 'shopify', "prefix" => "http://shopify.com/"),
            "applestore" => array("title" => "applestore", "sign" => 'applestore', "prefix" => "http://applestore.com/"),
            "playstore" => array("title" => "playstore", "sign" => 'playstore', "prefix" => "http://playstore.com/"),
            "huaweistore" => array("title" => "huaweistore", "sign" => 'huaweistore', "prefix" => "http://huaweistore.com/"),
            "venmo" => array("title" => "venmo", "sign" => 'venmo', "prefix" => "http://venmo.com/"),
            "patreon" => array("title" => "patreon", "sign" => 'patreon', "prefix" => "http://patreon.com/"),
            "kickstarter" => array("title" => "kickstarter", "sign" => 'kickstarter', "prefix" => "http://kickstarter.com/"),
            "snapchat" => array("title" => "snapchat", "sign" => 'snapchat', "prefix" => "http://snapchat.com/"),
            "whatsapp" => array("title" => "whatsapp", "sign" => 'whatsapp', "prefix" => "http://whatsapp.com/"),
            "wechat" => array("title" => "wechat", "sign" => 'wechat', "prefix" => "http://wechat.com/"),
            "viber" => array("title" => "viber", "sign" => 'viber', "prefix" => "http://viber.com/"),
            "qq" => array("title" => "qq", "sign" => 'qq', "prefix" => "http://qq.com/"),
            "qzone" => array("title" => "qzone", "sign" => 'qzone', "prefix" => "http://qzone.com/"),
            "tumblr" => array("title" => "tumblr", "sign" => 'tumblr', "prefix" => "http://tumblr.com/"),
            "blog" => array("title" => "blog", "sign" => 'blog', "prefix" => "http://blog.com/"),
            "blogger" => array("title" => "blogger", "sign" => 'blogger', "prefix" => "http://blogger.com/"),
            "reddit" => array("title" => "reddit", "sign" => 'reddit', "prefix" => "http://reddit.com/"),
            "foursquare" => array("title" => "foursquare", "sign" => 'foursquare', "prefix" => "http://foursquare.com/"),
            "baidu" => array("title" => "baidu", "sign" => 'baidu', "prefix" => "http://baidu.com/"),
            "telegram" => array("title" => "telegram", "sign" => 'telegram', "prefix" => "http://telegram.com/"),
            "medium" => array("title" => "medium", "sign" => 'medium', "prefix" => "http://medium.com/"),
            "wordpress" => array("title" => "wordpress", "sign" => 'wordpress', "prefix" => "http://wordpress.com/"),
        );
        return $networks;
    }

    public static function is_mobile()
    {
        $is_mobile = '0';

        if (preg_match('/(android|up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $is_mobile = 1;
        }

        if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $is_mobile = 1;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array('w3c ', 'acs-', 'alav', 'alca', 'amoi', 'andr', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');

        if (in_array($mobile_ua, $mobile_agents)) {
            $is_mobile = 1;
        }

        if (isset($_SERVER['ALL_HTTP'])) {
            if (strpos(strtolower($_SERVER['ALL_HTTP']), 'OperaMini') > 0) {
                $is_mobile = 1;
            }
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') > 0) {
            $is_mobile = 0;
        }

        return $is_mobile;
    }

    public static function metaData($options, $print = false)
    {
        $site = self::aVal($options, "site");
        $title = self::aVal($options, "title");
        $image = self::aVal($options, "image");
        $description = self::aVal($options, "description");
        $url = self::aVal($options, "url");
        $fbid = self::aVal($options, "fbid", 0);
        $fbusername = self::aVal($options, "fbusername", null);
        $twitter = self::aVal($options, "creator", "rapttors");
        $creator = self::aVal($options, "creator", "RapTToR");

        $s = '
        <meta property="og:type" content="website" />
        <meta property="og:title" content="' . $title . '">
        <meta property="og:description" content="' . $description . '">
        <meta property="og:image" content="' . $image . '">
        <meta property="og:url" content="' . $url . '">
        <meta name="twitter:card" content="' . $image . '">
        <meta name="twitter:creator" content="@' . $twitter . '">

        <!--  Non-Essential, But Recommended -->
        <meta property="og:site_name" content="' . $site . '">
        <meta name="twitter:image:alt" content="' . $title . '">
        ';
        if (strlen($fbid) > 0 || $fbid > 0 || strlen($fbusername) > 0) {
            $s .= '
        <!--  Non-Essential, But Required for Analytics -->
        <meta property="fb:app_id" content="your_app_id" />
        <meta name="twitter:site" content="@website-username">
        ';
        }
        if ($print) echo $s;
        return $s;
    }

    public static function shareOnLine($link, $print)
    {
        $s = "<script>
            if (!window.shareControl) { 
                function shareControl(thisObj, shareType) {
                    var slide = thisObj.closest('section.slide');
                    var slideNum = slide.data('slidenum');
                    var slideName = slide.find('img:first').attr('alt');
                    var slideId = slide.attr('id');
                    var currentDomain = window.location.hostname;
                    if (shareType == 'email') { //email share
                    window.location = 'mailto:?subject=\"Title - '+ slideName +'\"&body=View Slide at - '+ currentDomain +'/survey.php?id='+ slideId;
                    } else if (shareType == 'facebook'){ //facebook share
                        window.open('https://www.facebook.com/sharer/sharer.php?u='+ currentDomain +'/survey.php?title='+ title +'&id='+ slideId, '_blank');
                    } else if (shareType == 'twitter'){ //twitter share
                        window.open('https://twitter.com/home?status='+ currentDomain +'/survey.php?title='+ title +'&id='+ slideId, '_blank');
                    } else { //linkedin share
                        window.open('https://www.linkedin.com/shareArticle?mini=true&url='+ currentDomain +'/survey.php?title='+ title +'&id='+ slideId, '_blank');
                    }
                }
                //share control links
                $('.emailShare').on('click', function(){ //on email click
                    shareControl($(this), 'email');
                });
                $('.facebookShare').on('click', function(){ //on facebook click
                    shareControl($(this), 'facebook');
                });
                $('.twitterShare').on('click', function(){ //on twitter click
                    shareControl( $(this), 'twitter');
                });
                $('.linkedinShare').on('click', function(){ //on linkedin click
                    shareControl($(this), 'linkedin');
                });
            }
            </script>
            <div class='shareControl'>
            <span class='emailShare'><i class='fa fa-envelope'></i> Email</span>
            <span class='facebookShare'><i class='fa fa-facebook'></i> facebook</span>
            <span class='twitterShare'><i class='fa fa-twitter'></i> twitter</span>
            <span class='linkedinShare'><i class='fa fa-linkedin'></i> linkedIn</span>
            </div>
            ";
        if ($print) echo $s;
        return $s;
    }
}
