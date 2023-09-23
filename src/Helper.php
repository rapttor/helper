<?php // ﷽
namespace RapTToR;

use GdImage;

/**
 * @author rapttor
 *
 * require __DIR__ . '/protected/vendor/autoload.php';
 */
$RapTToR_HELPER = array();
$RapTToR_LANGUAGES = array();



/**
 * [Description Helper]
 */
class Helper
{


    /**
     * `dump()` is a function that takes a variable as an argument and outputs it in a textarea
     * 
     * @param v The variable to dump.
     */
    public static function dump($v)
    {
        if (is_string($v))
            $v = htmlspecialchars($v, ENT_QUOTES);
        echo '<textarea style="margin-left:10%;width:88%;height:49vh;">';
        var_dump($v);
        echo '</textarea>';
    }

    /**
     * Check if variable is a date;
     * 
     * @param mixed $v
     * @returns [boolean]
     */
    public static function isDate($v)
    {
        $isDate = false;
        if (is_null($v))
            return false;
        $y = (int) Date("Y", strtotime($v));
        if ($y > 1971)
            $isDate = true;
        return $isDate;
    }

    /**
     * Pass string or integer to get valid date integer
     * @param mixed $date
     * @return [int] // date
     */
    public static function toDate($dateStr, $format = "Y-m-d")
    {
        date_default_timezone_set('UTC');
        $date = DateTime::createFromFormat($format, $dateStr);
        if ($date && ($date->format($format) === $dateStr))
            return $date->format($format);
        return false;
    }

    /**
     * Pass string or integer to get valid time integer
     * @param mixed $date
     * @return [int] // date
     */
    public static function toTime($date)
    {
        if (!is_numeric($date) && is_string($date))
            $date = strtotime($date);
        if (is_numeric($date))
            $date = date("Y-m-d H:i:s", $date);
        return $date;
    }



    /**
     * @param mixed $html
     * 
     * @return [type]
     */
    public static function to_utf8_2($html)
    {
        $html = html_entity_decode(htmlentities($html, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'ISO-8859-1');
        return $html;
    }

    /**
     * @param mixed $a
     * @param string $prefix
     * 
     * @return [type]
     */
    public static function meta2csv($a, $prefix = "")
    {
        $r = "";
        if (is_array($a)) {
            foreach ($a as $k => $d) {
                if (is_array($d)) {
                    $r .= self::meta2csv($d, $k);
                } elseif (is_string($d)) {
                    $r .= $d;
                }
            }
        } else
            if (is_string($a)) {
                $r .= $a;
            }
        return $r;
    }




    /**
     * @param mixed $meta
     * @param array $bad
     * 
     * @return [type]
     */
    public static function cleanup($meta, $bad = array())
    {
        foreach ($meta as $k => $v)
            if (is_string($v)) {
                $t = $v;
                $t = trim(str_ireplace("\t", "", $t));
                $t = \RapTToR\Helper::replaceAll("  ", " ", $t);
                $t = str_ireplace($bad, "", $t);
                $t = \RapTToR\Helper::replaceAll("&nbsp;", " ", $t);
                $t = \RapTToR\Helper::replaceAll("\t", " ", $t);
                $t = \RapTToR\Helper::replaceAll("\n", " ", $t);
                $t = \RapTToR\Helper::replaceAll("\r", " ", $t);

                //$clean = preg_replace( "/[^\p{L}|\p{N}]+/u", " ", $clean );
                //$clean = preg_replace( "/[\p{Z}]{2,}/u", " ", $clean );
                //$clean = preg_replace( '/[^\p{L}\p{N} ]+/', " ", $clean );
                //$clean = preg_replace( '/\W+/', " ", $clean );
                //$clean = preg_replace( "/[^[:alnum:][:space:]]/u", "", $clean );

                for ($i = 0; $i < 32; $i++)
                    $t = str_ireplace(chr($i), "", $t);
                $t = \RapTToR\Helper::replaceAll("  ", " ", $t);
                $t = preg_replace('/\s+/', " ", $t);
                $meta[$k] = $t;
            }
        return $meta;
    }

    /**
     * @param mixed $url
     * 
     * @return [type]
     */
    public static function validUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param mixed $email
     * 
     * @return [type]
     */
    public static function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param mixed $strOrgNumber
     * 
     * @return [type]
     */
    public static function onlyNumbers($strOrgNumber)
    {
        return preg_replace('/[^0-9.]+/', '', $strOrgNumber);
    }

    /**
     * @param mixed $url
     * 
     * @return [type]
     */
    public static function fixUrl($url)
    {
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace(":/", "://", $url);
        return $url;
    }


    /**
     * @param mixed $obj
     * @param bool $deep
     * 
     * @return [type]
     */
    public static function objectToArray($obj, $deep = true)
    {
        $reflectionClass = new \ReflectionClass(get_class($obj));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $val = $property->getValue($obj);
            if (true === $deep && is_object($val)) {
                $val = self::objectToArray($val);
            }
            $array[$property->getName()] = $val;
            $property->setAccessible(false);
        }
        return $array;
    }


    /**
     * @param bool $print
     * 
     * @return [type]
     */
    public static function again($print = false)
    {
        $result = "<script>
            document.location.reload();
        </script>";
        if ($print)
            echo $result;
        return $result;
    }


    /**
     * @param mixed $string
     * 
     * @return [type]
     */
    public static function parseUrls($string)
    {
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
        return (is_array($match[0])) ? $match[0] : null;
    }

    /**
     * @param mixed $date
     * @param $glue =
     * @param string $lang
     * 
     * @return [type]
     */
    public static function toEnglishDate($date, $glue = " ", $lang = "sv_SE")
    {
        $w = explode($glue, $date);
        $n = array();
        foreach ($w as $k => $m) {
            if (!is_numeric($m))
                $m = self::getEnglishMonthName($m, $lang);
            $n[$k] = $m;
        }
        return implode($glue, $n);
    }

    /**
     * @param mixed $foreignMonthName
     * @param string $setlocale
     * 
     * @return [type]
     */
    public static function getEnglishMonthName($foreignMonthName, $setlocale = 'sv_SE')
    {

        setlocale(LC_ALL, 'en_US');

        $month_numbers = range(1, 12);
        $english_months = array();
        $foreign_months = array();
        foreach ($month_numbers as $month)
            $english_months[] = strftime('%B', mktime(0, 0, 0, $month, 1, 2011));

        setlocale(LC_ALL, $setlocale); foreach ($month_numbers as $month)
            $foreign_months[] = strftime('%B', mktime(0, 0, 0, $month, 1, 2011));

        return str_replace($foreign_months, $english_months, $foreignMonthName);
    }

    /**
     * @param mixed $in
     * 
     * @return [type]
     */
    public static function to_utf8($in)
    {
        $out = array();
        if (is_array($in)) {
            foreach ($in as $key => $value) {
                $out[self::to_utf8($key)] = self::to_utf8($value);
            }
        } elseif (is_string($in)) {
            if (mb_detect_encoding($in) != "UTF-8")
                return utf8_encode($in);
            else
                return $in;
        } else {
            return $in;
        }
        return $out;
    }

    /**
     * @param mixed $a
     * 
     * @return [type]
     */
    public static function flat_array($a)
    {
        $n = array();
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                if (is_array($v))
                    $n[] = self::flat_array($v);
                if (is_string($v))
                    $n[] = $v;
            }
        }
        if (is_string($a))
            $n[] = $a;
        return $n;
    }







    /**
     * @param mixed $s
     * 
     * @return [type]
     */
    public static function slug($s)
    {
        $o = $s;
        if (is_array($s))
            $s = serialize($s);
        if (is_object($s))
            $s = json_encode($s);
        $s = self::cleanString($s);
        $s = str_replace(' ', '-', $s); // Replaces all spaces with hyphens.
        $s = preg_replace('/[^A-Za-z0-9\-]/', '', $s); // Removes special chars.
        return $s;
    }

    /**
     * The function takes a string and converts it into a URL-friendly slug format by replacing
     * non-letter or digit characters with a divider, transliterating, removing unwanted characters,
     * trimming, removing duplicate dividers, and converting to lowercase.
     * 
     * @param text The string that needs to be converted into a slug.
     * @param divider The character used to replace non-letter or non-digit characters in the input
     * text. By default, it is set to '-' (hyphen).
     * 
     * @return a string that has been converted to a slug format, which means it has been modified to
     * be more suitable for use in URLs and filenames. The returned string contains only lowercase
     * letters, digits, and hyphens, with no spaces or special characters. If the input string is
     * empty, the function returns the string "n-a".
     */
    public static function slugify($text, $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @param mixed $text
     * 
     * @return string
     */
    public static function cleanString($text)
    {
        // 1) convert á ô => a o
        $text = preg_replace("/[áàâãªä]/u", "a", $text);
        $text = preg_replace("/[ÁÀÂÃÄ]/u", "A", $text);
        $text = preg_replace("/[ÍÌÎÏ]/u", "I", $text);
        $text = preg_replace("/[íìîï]/u", "i", $text);
        $text = preg_replace("/[éèêë]/u", "e", $text);
        $text = preg_replace("/[ÉÈÊË]/u", "E", $text);
        $text = preg_replace("/[óòôõºö]/u", "o", $text);
        $text = preg_replace("/[ÓÒÔÕÖ]/u", "O", $text);
        $text = preg_replace("/[úùûü]/u", "u", $text);
        $text = preg_replace("/[ÚÙÛÜ]/u", "U", $text);
        $text = preg_replace("/[’‘‹›‚]/u", "'", $text);
        $text = preg_replace("/[“”«»„]/u", '"', $text);
        $text = str_replace("–", "-", $text);
        $text = str_replace(" ", " ", $text);
        $text = str_replace("ç", "c", $text);
        $text = str_replace("Ç", "C", $text);
        $text = str_replace("ñ", "n", $text);
        $text = str_replace("Ñ", "N", $text);

        //2) Translation CP1252. &ndash; => -
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans[chr(130)] = '&sbquo;'; // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;'; // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;'; // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;'; // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;'; // Dagger
        $trans[chr(135)] = '&Dagger;'; // Double Dagger
        $trans[chr(136)] = '&circ;'; // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;'; // Per Mille Sign
        $trans[chr(138)] = '&Scaron;'; // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;'; // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;'; // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;'; // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;'; // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;'; // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;'; // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;'; // Bullet
        $trans[chr(150)] = '&ndash;'; // En Dash
        $trans[chr(151)] = '&mdash;'; // Em Dash
        $trans[chr(152)] = '&tilde;'; // Small Tilde
        $trans[chr(153)] = '&trade;'; // Trade Mark Sign
        $trans[chr(154)] = '&scaron;'; // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;'; // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;'; // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;'; // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;'; // euro currency symbol
        ksort($trans);

        foreach ($trans as $k => $v) {
            $text = str_replace($v, $k, $text);
        }

        // 3) remove <p>, <br/> ...
        $text = strip_tags($text);

        // 4) &amp; => & &quot; => '
        $text = html_entity_decode($text);

        // 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
        $text = preg_replace('/[^(\x20-\x7F)]*/', '', $text);

        $targets = array('\r\n', '\n', '\r', '\t');
        $results = array(" ", " ", " ", "");
        $text = str_replace($targets, $results, $text);

        //XML compatible
        /*
        $text = str_replace("&", "and", $text);
        $text = str_replace("<", ".", $text);
        $text = str_replace(">", ".", $text);
        $text = str_replace("\\", "-", $text);
        $text = str_replace("/", "-", $text);
        */

        return ($text);
    }



    /**
     * @param mixed $filename
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $time
     * 
     * @return [type]
     */
    public static function timePassed($time)
    {
        if (is_null($time) || $time == "")
            return "";
        if (is_string($time))
            $time = strtotime($time);
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

    /**
     * @param mixed $ar
     * @param string $key
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $str
     * @param string $delimiter
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $title
     * @param null $icon
     * 
     * @return [type]
     */
    public static function header($title, $icon = null)
    {
        return '<h1 class="pull-right"><i class="icons icon-' . $icon . ' pull-right"></i>
            ' . $title . ' &nbsp;</h1>';
    }

    /**
     * @param mixed $mail
     * @param null $disposable
     * 
     * @return [type]
     */
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

    /**
     * @param null $cat
     * @param null $id
     * 
     * @return [type]
     */
    public static function imgurl($cat = null, $id = null)
    {
        $base = "";
        return $base . "/uploads/" . $cat . "/" . $id . ".jpg";
    }

    /**
     * @param null $cat
     * @param null $id
     * @param string $class
     * 
     * @return [type]
     */
    public static function img($cat = null, $id = null, $class = "img-responsive")
    {
        return "<img src='" . self::imgurl($cat, $id) . "'  class='$class'>";
    }

    /**
     * @param null $cat
     * 
     * @return [type]
     */
    public static function uploadDir($cat = null)
    {
        $base = "";
        if (isset($_SERVER["HTTP_HOST"])) {
            $base = $_SERVER['DOCUMENT_ROOT'] . "/";
        }
        return $base . "/uploads/" . (is_null($cat) ? "" : $cat . '/');
    }

    /**
     * @param mixed $a
     * @param mixed $i
     * @param null $default
     * 
     * @return [type]
     */
    public static function arrayValue($a, $i, $default = null)
    {
        return (is_array($a) && isset($a[$i])) ? $a[$i] : $default;
    }


    public static function Dow($id = null)
    {
        $a = array(
            0 => self::t("front", "Sunday"),
            1 => self::t("front", "Monday"),
            2 => self::t("front", "Tuesday"),
            3 => self::t("front", "Wednesday"),
            4 => self::t("front", "Thursday"),
            5 => self::t("front", "Friday"),
            6 => self::t("front", "Saturday"),

        );
        if (!is_null($id) && isset($a[$id]))
            return $a[$id];
        return $a;
    }

    /**
     * @param string $min_date
     * @param string $max_date
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $str
     * @param string $dom
     * 
     * @return [type]
     */
    public static function domain($str, $dom = "")
    {
        return (strpos($str, "http") === false) ? "http://" . $str : $str;
    }

    /**
     * @param mixed $url
     * @param null $text
     * @param string $options
     * 
     * @return [type]
     */
    public static function link($url, $text = null, $options = 'target="_blank"')
    {
        if (is_null($text))
            $text = $url;
        $link = self::domain($url);
        return "<a href='$link' $options>$text</a>";
    }

    /**
     * @param mixed $datetime
     * @param bool $full
     * 
     * @return [type]
     */
    public static function time_elapsed_string($datetime, $full = false)
    {
        $now = new \DateTime;
        $ago = new \DateTime($datetime, new \DateTimeZone(date_default_timezone_get()));
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

        if (!$full)
            $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * @param mixed $tm
     * @param int $rcs
     * 
     * @return [type]
     */
    public static function ago($tm, $rcs = 0)
    {
        if (is_string($tm))
            $tm = strtotime($tm);
        $cur_tm = time();
        $dif = $cur_tm - $tm;
        $pds = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
        $lngh = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

        for ($v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1); $v--)
            ;
        if ($v < 0)
            $v = 0;
        $_tm = $cur_tm - ($dif % $lngh[$v]);
        $no = floor($no);
        if ($no <> 1)
            $pds[$v] .= 's';
        $x = sprintf("%d %s ", $no, $pds[$v]);
        if (($rcs == 1) && ($v >= 1) && (($cur_tm - $_tm) > 0))
            $x .= self::ago($_tm);
        return $x;
    }

    /**
     * @param mixed $str
     * @param int $length
     * @param mixed $more
     * 
     * @return [type]
     */
    public static function more($str, $length = 200, $more = "<!-- more -->")
    {
        if (strlen($str) < $length)
            return $str;

        $id = "SH" . sha1($str);
        $length = (strpos($str, $more) !== false) ? strpos($str, $more) : $length;

        return "<div id='$id'><div class='excerpt'>" . substr($str, 0, $length) . "</div><div style='display:none;' class='more'>" . substr($str, $length, strlen($str)) . "</div>
        </div><a href='javascript:;' title='$length / " . strlen($str) . "' style='cursor:pointer;' onclick='$(\"#$id .more\").toggle();'>[...]</a>";
    }



    /**
     * @param mixed $menu
     * 
     * @return [type]
     */
    public static function IconMenu($menu)
    {
        $result = "";
        foreach ($menu as $m)
            $result .= self::Icon($m);
        return $result;
    }

    /**
     * @param mixed $i
     * 
     * @return [type]
     */
    public static function Icon($i)
    {
        $result = "";
        if (!isset($i["value"]) && isset($i["url"]))
            $i["value"] = $i["url"];
        if (isset($i["value"]) && isset($i["ion"]) && isset($i["title"]))
            $result = "<div class='icontext' onclick='window.location.href=\"" .
                $i["value"] . "\"'>
        <i class='{$i["ion"]}'></i>
        <small>{$i["title"]}</small>
        </div>";

        return $result;
    }

    /**
     * @param mixed $a
     * @param mixed $k
     * @param string $d
     * 
     * @return [type]
     */
    public static function aVal($a, $k, $d = "")
    {
        if (is_object($a))
            $a = (array) $a;
        return (is_array($a) && isset($a[$k])) ? $a[$k] : $d;
    }

    /**
     * @param mixed $a
     * @param mixed $k
     * @param mixed $v
     * 
     * @return [type]
     */
    public static function aFind($a, $k, $v)
    {
        if (is_array($a))
            foreach ($a as $item) {
                if (is_array($item) && isset($item[$k]) && $item[$k] == $v)
                    return $item;
                if (is_object($item))
                    foreach ($item as $key => $value)
                        if ($k == $key && $v == $value)
                            return $item;
            }
        return null;
    }

    /**
     * @param string $title
     * 
     * @return [type]
     */
    public static function back($title = "Back")
    {
        return "<div class='clearfix'></div><a style='clear:both;margin:10px 0;' class='btn btn-primary' onclick='history.go(-1);'><i class='fa fa-caret-left'></i> " .
            $title . "</a><div class='clearfix'></div>";
    }

    /**
     * @param mixed $data
     * @param bool $cache
     * @param bool $die
     * @param bool $convert
     * 
     * @return [type]
     */
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


    /**
     * @param mixed $str
     * @param mixed $params
     * 
     * @return [type]
     */
    public static function map($str, $params)
    {
        foreach ($params as $key => $value)
            $str = str_replace($key, $value, $str);
        return $str;
    }


    /**
     * @param mixed $url
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $data
     * 
     * @return [type]
     */
    public static function exportModelAsJson($data)
    {
        return $json = (!self::is_json($data)) ? json_encode($data) : $data;
    }

    /**
     * @param mixed $text
     * @param mixed $length
     * 
     * @return [type]
     */
    public static function ellipsis($text, $length)
    {
        return (mb_strlen($text) > $length) ? mb_substr($text, 0, $length) . '... ' : $text;
    }

    /**
     * @param mixed $what
     * @param mixed $with
     * @param mixed $str
     * 
     * @return [type]
     */
    public static function replaceAll($what, $with, $str)
    {
        while (stripos($str, $what))
            $str = str_ireplace($what, $with, $str);
        return $str;
    }

    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    public static function urlText($str)
    {
        return self::replaceAll('__', '_', preg_replace('/[^\w]/', '_', $str));
    }

    /**
     * @return [type]
     */
    public static function cors()
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400'); // cache for 1 day
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
        if ($desc)
            $sorter = array_reverse($sorter);
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
        if (is_string($str))
            $arr = explode($delimiter, $str);
        foreach ($arr as $key => $value)
            $arr[$key] = trim($value);
        $arr = array_unique($arr);
        return $arr;
    }

    /**
     * @param mixed $object
     * 
     * @return [type]
     */
    public static function vardumper($object)
    {
        echo "<pre>";
        var_dump($object);
        echo "</pre>";
        die;
    }


    /**
     * @param mixed $date
     * @param string $format
     * 
     * @return [type]
     */
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * @param mixed $date
     * @param $format =
     * 
     * @return [type]
     */
    public static function validateTime($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


    /**
     * @param null $message
     * @param string $type
     * @param null $value
     * 
     * @return [type]
     */
    public static function debug($message = null, $type = "info", $value = null)
    {
        if (is_null($message))
            return self::is_debug();
        global $RapTToR_HELPER;
        $debug = array("message" => $message, "type" => $type, "value" => $value);
        $RapTToR_HELPER["debug"][] = $debug;
        error_log(json_encode($debug));
    }


    static public function getCCType($cardNumber)
    {
        // Remove non-digits from the number
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        // Validate the length
        $len = strlen($cardNumber);
        if ($len < 15 || $len > 16) {
            return false;
        } else {
            switch ($cardNumber) {
                case (preg_match('/^4/', $cardNumber) >= 1):
                    return 'VISA';
                case (preg_match('/^5[1-5]/', $cardNumber) >= 1):
                    return 'MASTERCARD';
                case (preg_match('/^3[47]/', $cardNumber) >= 1):
                    return 'AMEX';
                case (preg_match('/^3(?:0[0-5]|[68])/', $cardNumber) >= 1):
                    return 'DINERS';
                case (preg_match('/^6(?:011|5)/', $cardNumber) >= 1):
                    return 'DISCOVER';
                case (preg_match('/^(?:2131|1800|35\d{3})/', $cardNumber) >= 1):
                    return 'JCB';
                default:
                    // ALIPAY, AMEX, BANCONTACT, BONUS, DINERS, DIRECTDEBIT, EPRZELEWY, EPS, GIROPAY, IDEAL, INVOICE, JCB, KLARNA, MAESTRO, MASTERCARD, MYONE, PAYPAL, PAYDIREKT, POSTCARD, POSTFINANCE, SAFERPAYTEST, SOFORT, TWINT, UNIONPAY, VISA, VPAY, WLCRYPTOPAYMENTS.
                    // return 'ALIPAY,AMEX,BANCONTACT,DINERS,DIRECTDEBIT,JCB,KLARNA,MAESTRO,MASTERCARD,POSTCARD,POSTFINANCE,TWINT,VISA';
                    return false;
            }
        }
    }

    function validateCCChecksum($number)
    {

        // Remove non-digits from the number
        $number = preg_replace('/\D/', '', $number);

        // Get the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Split up the number into single digits and get the total
        $total = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];

            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;

                // If the sum is two digits, add them together
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            // Sum up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? TRUE : FALSE;
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
            if (is_object($arVariables))
                $arVariables = (array) $arVariables;
            if (!is_null($sendthis))
                $arVariables["this"] = $sendthis;
            extract($arVariables);
            if ($return)
                ob_start();
            include($strTemplate);
            if ($return)
                $strResult = ob_get_clean();
            if ($return) {
                return $strResult;
            } else {
                echo $strResult;
                return true;
            }
        } else
            return "Template not found $strViewFile";
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
        if ($close)
            fclose($filehandle);
    }


    /**
     * @param mixed $filename
     * 
     * @return GdImage
     */
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
            case 'bmp':
                return imagecreatefrombmp($filename);
                break;
            case 'tga':
                return imagecreatefromtga($filename);
                break;
            case 'xbm':
                return imagecreatefromxbm($filename);
                break;
            case 'wbmp':
                return imagecreatefromwbmp($filename);
                break;
            case 'xpm':
                return imagecreatefromxpm($filename);
                break;
            case 'gif':
                return imagecreatefromgif($filename);
                break;
            case 'gd2':
                return imagecreatefromgd2($filename);
                break;
            default:
                return false;
                // throw new \Exception('File "' . $filename . '" is not valid jpg, png or gif image.');
                break;
        }
        return false;
    }

    /**
     * @param mixed $severity
     * @param mixed $message
     * @param mixed $filename
     * @param mixed $lineno
     * 
     * @return [type]
     */
    public static function exceptions_error_handler($severity, $message, $filename, $lineno)
    {
        throw new \ErrorException($message, 0, $severity, $filename, $lineno);
    }

    // set_error_handler('exceptions_error_handler');

    /**
     * @param mixed $src
     * @param mixed $dest
     * @param mixed $desired_height
     * 
     * @return GdImage
     */
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
            return $virtual_image;
        }
        return false;
    }

    /**
     * @param $title =
     * @param int $id
     * 
     * @return [type]
     */
    public static function array0($title = "None selected", $id = 0)
    {
        return array($id => $title);
    }

    /**
     * @param mixed $value
     * @param string $table
     * 
     * @return [type]
     */
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
        $arr = (array) $value;
        $result = array();
        foreach ($arr as $key => $v) {
            $temp = json_encode($v);
            if (strlen($temp) > 2 && $temp !== "null") {
                $result[$key] = $v;
                if ((int) $v == $v && is_numeric($v))
                    $result[$key] = (int) $v;
                if ((float) $v == $v && is_numeric($v))
                    $result[$key] = (float) $v;
            }
        }
        return $result;
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


    /**
     * for criteria, ids to get for processing.
     * 
     * @param $title =
     * 
     * @return [type]
     */
    public static function None($title = "None selected")
    {
        return array(0 => $title);
    }




    /**
     * @return [type]
     */
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

    /**
     * system root
     * @return [type]
     */
    public static function base()
    {
        return dirname(__FILE__);
    }

    /**
     * translate service
     * 
     * @param mixed $section
     * @param mixed $text
     * @param string $language
     * @param bool $force
     * 
     * @return [type]
     */
    public static function t($section, $text, $language = "en", $force = false)
    {
        global $RapTToR_LANGUAGES;
        $base = self::base();
        $filename = $base . '/languages/' . $language . '/' . $section . '.json';
        $lang = null;
        if (isset($RapTToR_LANGUAGES[$language]))
            $lang = $RapTToR_LANGUAGES[$language];

        if ((is_null($lang) || $force) && is_file($filename)) {
            $lang = json_decode($filename, true);
            if (is_array($lang)) {
                $RapTToR_LANGUAGES[$language] = $lang;
            }
        }
        if (isset($lang[$text]))
            $text = $lang[$text];

        return $text;
    }

    /**
     * @return [type]
     */
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




    /**
     * create statically.io CDN from image
     * $host defaults to ngrok, HTTP_HOST
     * @param mixed $imageurl
     * @param array $replace
     * @param null $host
     * 
     * @return [type]
     */
    public static function cdnImage($imageurl, $replace = array(), $host = null)
    {
        if (is_null($host)) {
            $host = self::ngrok();
            if (is_null($host) && isset($_SERVER["HTTP_HOST"]))
                $host = $_SERVER["HTTP_HOST"];
        }

        // https://cdn.statically.io/img/cdn.rapttor.com/f=auto/[imageloaclurl:/uploads/companymedia/1/31.jpg]
        $with = array_values($replace);
        $replace = array_keys($replace);

        $img = str_ireplace($replace, $with, $imageurl);
        if (substr($img, 0, 4) != 'http' && substr($img, 0, 1) == '/')
            $img = 'https://cdn.statically.io/img/' . $host . '/f=auto' . $imageurl;
        return $img;
    }



    /**
     * Save json to file + gzipped file.
     * 
     * @param mixed $file
     * @param mixed $json
     * @param bool $delete
     * 
     * @return [type]
     */
    public static function saveData($file, $json, $delete = false)
    {
        $cmp = gzcompress($json);
        if ($delete) {
            if (is_file($file))
                unlink($file);
            if (is_file($file . '.gz'))
                unlink($file . '.gz');
        }
        $okc = @file_put_contents($file . '.gz', $cmp);
        $ok = @file_put_contents($file, $json);
        return $okc && $ok;
    }


    /**
     * 
     * Generate remote user UID
     * 
     * @param null $nick
     * 
     * @return [type]
     */
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

    /**
    * Get php://input as array, and merge $_REQUEST if mapping on.
    
    * @param bool $map
    * 
    * @return [type]
    */
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

    /**
     * @param bool $notimelimit
     * 
     * @return [type]
     */
    static public function showErrors($notimelimit = true)
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        if ($notimelimit)
            set_time_limit(-1);
    }


    /**
     * Social networks data
     * @return [type]
     */
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

    /**
     * Check if request call is from mobile phone
     * @return [type]
     */
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

    /**
     * Generate metatags
     * @param options [site,title,image,description,url,fbif,fbusername, creator]
     */
    public static function metaData($options, $print = false)
    {
        $site = self::aVal($options, "site");
        $title = (string) self::aVal($options, "title");
        $image = (string) self::aVal($options, "image");
        $description = (string) self::aVal($options, "description");
        $url = (string) self::aVal($options, "url");
        $fbid = (string) self::aVal($options, "fbid", 0);
        $fbusername = (string) self::aVal($options, "fbusername", null);
        $twitter = (string) self::aVal($options, "creator", "rapttors");
        $creator = (string) self::aVal($options, "creator", "RapTToR");

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
        if ($print)
            echo $s;
        return $s;
    }

    /**
     * @param mixed $link
     * @param mixed $print
     * 
     * @return [type]
     */
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
        if ($print)
            echo $s;
        return $s;
    }


    /**
     * @param $param (null for all)
     * @param $port (4040)
     * @param $tunnel (0)
     */
    public static function ngrok($param = "public_html", $port = 4040, $tunnel = 0)
    {
        $n = file_get_contents("http://127.0.0.1:$port/api/tunnels");
        $j = json_decode($n, true);
        if (is_null($param))
            return $j;
        return (isset($j) && $j && is_array($j)
            && isset($j["tunnels"]) && is_array($j["tunnels"])
            && isset($j["tunnels"][$tunnel]) && is_array($j["tunnels"][0])
            && isset($j["tunnels"][$tunnel][$param]))
            ? $j["tunnels"][$tunnel][$param] : null;
    }




    /**
     * @return [type]
     */
    static public function repeat()
    {
        ?>
                                        window.location.reload();
                                <?php
    }


    /**
     * @return [type]
     */
    static public function is_debug()
    {
        return isset($_REQUEST["debug"]) ? (int) $_REQUEST["debug"] : false;
    }

    /**
     * @return [type]
     */
    static public function is_force()
    {
        return isset($_REQUEST["force"]) ? (int) $_REQUEST["force"] : false;
    }


    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    static public function uncompress($str)
    {
        $temp = @gzuncompress($str);
        return $temp;
    }

    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    static public function uncmp($str)
    {
        return self::uncompress(utf8_decode($str));
    }


    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    static public function compress($str)
    {
        $cmp = gzcompress($str, 9);
        return $cmp;
    }

    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    static public function cmp($str)
    {
        return utf8_encode((string) self::compress($str));
    }


    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    static public function isCompressed($str)
    {
        $ok = false;
        $str1 = utf8_decode($str);
        if (@gzuncompress($str) !== false)
            $ok = true;
        if (@gzuncompress($str1) !== false)
            $ok = true;
        return $ok;
    }









    /**
     * @param mixed $bucketid
     * @param mixed $where
     * 
     * @return [type]
     */
    public static function s3download($bucketid, $where)
    {
        $cmd = "aws s3 sync s3://$bucketid $where";
        return array("cmd" => $cmd, "result" => self::exec($cmd));
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
     * @return string|null
     */
    public static function bearerCreate()
    {
        return sha1(microtime(true)) . md5(rand(1, 10000)) . sha1(json_encode($_SERVER));
        return null;
    }


    /**
     * Get data from table by kv array criteria and set new one with kv+attributes if not exists;
     * @param $table
     * @param $kv
     * @param null $attributes
     * @return array|CActiveRecord|mixed|null
     */
    public static function getset($table, $kv, $attributes = null)
    {
        /** @var CActiveRecord $class */
        $class = ucfirst($table);
        $d = $class::model()->findByAttributes($kv);
        if (!$d && !is_null($attributes)) {
            $d = new $class();

            foreach ($kv as $k => $v)
                $d->$k = $v; foreach ($attributes as $k => $v)
                $d->$k = $v;
            try {
                $d->save();
            } catch (\Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        }
        return $d;
    }

    /**
     * Acceptable formats
     * @return array
     */
    public static function formats()
    {
        return array(
            0 => "Html",
            1 => "JSON",
            2 => "Xml",
        );
    }



    /**
     * @return [type]
     */
    public static function backgrounds()
    {
        $dir = dirname(__FILE__) . '/../../images/videos/';
        $videos = scandir($dir);
        if (is_array($videos))
            foreach ($videos as $video)
                if (stripos($video, ".mp4")) {
                    $name = trim(str_ireplace(".mp4", "", $video));
                    $backgrounds[$name] = $video;
                }
        return $backgrounds;
    }

    /**
     * @return [type]
     */
    public static function languages()
    {
        return array(
            "ba" => "Bosanski",
            "bg" => "Български",
            "me" => "Crnogorski",
            "de" => "Deutsch",
            "en" => "English",
            "fr" => "Française",
            "hr" => "Hrvatski",
            "it" => "Italiano",
            "mk" => "Македонски",
            "du" => "Nederlandse",
            "ru" => "Română",
            "ru" => "Русский",
            "rs" => "Srpski",
            "sl" => "Slovenščina",
            "sv" => "Swenska",
            "tr" => "Türk",
            "ch" => "中文",
            //"yu" => "ExYu",
            "ko" => "한국어",
        );
    }





    /**
     * @return [type]
     */
    public function YesNo()
    {
        return array(
            -1 => Helper::t('front', "No"),
            0 => Helper::t('front', "Not set"),
            1 => Helper::t('front', "Yes"),
        );
    }



    /**
     * @param mixed $cmd
     * 
     * @return [type]
     */
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






    /**
     * @param mixed $modelarr
     * @param string $id
     * 
     * @return [type]
     */
    public static function enumerate($modelarr, $id = "id")
    {
        $result = array();
        if (is_array($modelarr))
            foreach ($modelarr as $m)
                if (isset($m->$id))
                    $result[$m->$id] = $m;
        return $result;
    }

    /**
     * @param mixed $text
     * 
     * @return [type]
     */
    public static function mailText($text)
    {
        $text = self::br2nl($text);
        $text = self::htmlChars($text);
        $text = self::nbsp($text);
        return $text;
    }

    /**
     * @param mixed $text
     * 
     * @return [type]
     */
    public static function htmlChars($text)
    {
        return htmlspecialchars($text, ENT_QUOTES);
    }

    /**
     * @param mixed $text
     * 
     * @return [type]
     */
    public static function br2nl($text)
    {
        return str_ireplace(
            array('<br />', '<br>', '<br/>'),
            chr(13) . chr(10) . "\r\n" . '%0D%0A',
            $text
        );
    }

    /**
     * @param mixed $text
     * 
     * @return [type]
     */
    public static function nbsp($text)
    {
        return str_ireplace(' ', '&nbsp;', $text);
    }


    /**
     * @param $t Tagging
     * @param mixed $t
     * 
     * @return [type]
     */
    static public function isTestSet($t)
    {
        return (
            (
                (date("Y-m-d", strtotime($t->validatedat)) >= "2020-09-18" &&
                    date("Y-m-d", strtotime($t->validatedat)) <= "2020-10-01")
                or (date("Y-m-d", strtotime($t->validatedat)) >= "2020-11-7" &&
                    date("Y-m-d", strtotime($t->validatedat)) <= "2020-11-13"))
            &&
            $t->validatedby == 15);
    }

    /**
     * @param mixed $criteria
     * 
     * @return [type]
     */
    static public function criteriaTestset($criteria)
    {
        $testsetfrom = "2020-09-18";
        $testsetto = "2020-10-01";
        $testsetfrom2 = "2020-11-7";
        $testsetto2 = "2020-11-13";
        $criteria->addCondition("(validatedat>=:testsetfrom and validatedat<=:testsetto) or (validatedat>=:testsetfrom2 and validatedat<=:testsetto2)");
        $criteria->addCondition("validatedby=15");
        $criteria->params["testsetfrom"] = $testsetfrom;
        $criteria->params["testsetto"] = $testsetto;
        $criteria->params["testsetfrom2"] = $testsetfrom2;
        $criteria->params["testsetto2"] = $testsetto2;
        return $criteria;
    }

    /**
     * @return [type]
     */
    public static function status()
    {
        return array(
            -2 => self::t("front", "deleted"),
            -1 => self::t("front", "inactive"),
            0 => self::t("front", "default"),
            1 => self::t("front", "active"),
            2 => self::t("front", "featured"),
        );
    }

    /**
     * @param mixed $c
     * @param bool $onlynew
     * 
     * @return [type]
     */
    public static function unserialize($c, $onlynew = false)
    {
        $a = array();
        if (is_array($c)) {
            $a = $c;
        } else if (is_string($c)) {
            $a = json_decode($c, true);
        }
        $new = array();
        if (isset($a) && is_array($a))
            foreach ($a as $v) {
                $a[Helper::aVal($v, "name")] = Helper::aVal($v, "value");
                $new[Helper::aVal($v, "name")] = Helper::aVal($v, "value");
            }
        return ($onlynew) ? $new : $a;
    }


    /**
     * @return [type]
     */
    public static function storeDir()
    {
        return "../../front/data/";
    }

    /**
     * @return [type]
     */
    public static function htmlNoCache()
    {
        return '
        <meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />';
    }

    public static function mb_ucfirst($str, $encoding = "UTF-8", $lower_str_end = false)
    {
        $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);
        $str_end = "";
        if ($lower_str_end) {
            $str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        } else {
            $str_end = mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
        }
        $str = $first_letter . $str_end;
        return $str;
    }


    public static function decontaminate_text(
        $text,
        $remove_tags = true,
        $remove_line_breaks = true,
        $remove_BOM = true,
        $ensure_utf8_encoding = true,
        $ensure_quotes_are_properly_displayed = true,
        $decode_html_entities = true
    ) {

        if ('' != $text && is_string($text)) {
            $text = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $text);
            $text = str_replace(']]>', ']]&gt;', $text);

            if ($remove_tags) {
                // Which tags to allow (none!)
                // $text = strip_tags($text, '<p>,<strong>,<span>,<a>');
                $text = strip_tags($text, '');
            }

            if ($remove_line_breaks) {
                $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
                $text = trim($text);
            }

            if ($remove_BOM) {
                // Source: https://stackoverflow.com/a/31594983/1766219
                if (0 === strpos(bin2hex($text), 'efbbbf')) {
                    $text = substr($text, 3);
                }
            }

            if ($ensure_utf8_encoding) {

                // Check if UTF8-encoding
                if (utf8_encode(utf8_decode($text)) != $text) {
                    $text = mb_convert_encoding($text, 'utf-8', 'auto');
                }
            }

            if ($ensure_quotes_are_properly_displayed) {
                $text = str_replace('&quot;', '"', $text);
            }

            if ($decode_html_entities) {
                $text = html_entity_decode($text);
            }

            /**
             * Other things to try
             * - the chr-function: https://stackoverflow.com/a/20845642/1766219
             * - stripslashes (THIS ONE BROKE MY JSON DECODING, AFTER IT STARTED WORKING, THOUGH): https://stackoverflow.com/a/28540745/1766219
             * - This (improved?) JSON-decoder didn't help me, but it sure looks fancy: https://stackoverflow.com/a/43694325/1766219
             */
        }
        return $text;
    }

    /**
     * @param mixed $string
     * 
     * @return [type]
     */
    public static function all2Lat($string)
    {
        $rus = array('š', 'Š', 'Đ', 'đ', 'Č', 'č', 'Ć', 'ć', 'Ž', 'ž');
        $lat = array('s', 'S', 'Dj', 'dj', 'C', 'c', 'C', 'c', 'Z', 'z');
        $string = str_replace($rus, $lat, $string);
        return ($string);
    }

    /**
     * @param mixed $text
     * 
     * @return [type]
     */
    public static function cp1250_to_utf2($text)
    {
        $dict = array(
            chr(225) => 'á',
            chr(228) => 'ä',
            chr(232) => 'č',
            chr(239) => 'ď',
            chr(233) => 'é',
            chr(236) => 'ě',
            chr(237) => 'í',
            chr(229) => 'ĺ',
            chr(229) => 'ľ',
            chr(242) => 'ň',
            chr(244) => 'ô',
            chr(243) => 'ó',
            chr(154) => 'š',
            chr(248) => 'ř',
            chr(250) => 'ú',
            chr(249) => 'ů',
            chr(157) => 'ť',
            chr(253) => 'ý',
            chr(158) => 'ž',
            chr(193) => 'Á',
            chr(196) => 'Ä',
            chr(200) => 'Č',
            chr(207) => 'Ď',
            chr(201) => 'É',
            chr(204) => 'Ě',
            chr(205) => 'Í',
            chr(197) => 'Ĺ',
            chr(188) => 'Ľ',
            chr(210) => 'Ň',
            chr(212) => 'Ô',
            chr(211) => 'Ó',
            chr(138) => 'Š',
            chr(216) => 'Ř',
            chr(218) => 'Ú',
            chr(217) => 'Ů',
            chr(141) => 'Ť',
            chr(221) => 'Ý',
            chr(142) => 'Ž',
            chr(150) => '-'
        );
        return strtr($text, $dict);
    }


    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    public static function win2ascii($str)
    {

        $old = $str;

        if (false)
            $str = strtr(
                $str,
                "\xE1\xE8\xEF\xEC\xE9\xED\xF2",
                "\x61\x63\x64\x65\x65\x69\x6E"
            );

        if (false)
            $str = strtr(
                $str,
                "\xF3\xF8\x9A\x9D\xF9\xFA\xFD\x9E\xF4\xBC\xBE",
                "\x6F\x72\x73\x74\x75\x75\x79\x7A\x6F\x4C\x6C"
            );

        if (false)
            $str = strtr(
                $str,
                "\xC1\xC8\xCF\xCC\xC9\xCD\xC2\xD3\xD8",
                "\x41\x43\x44\x45\x45\x49\x4E\x4F\x52"
            );

        if (false)
            $str = strtr(
                $str,
                "\x8A\x8D\xDA\xDD\x8E\xD2\xD9\xEF\xCF",
                "\x53\x54\x55\x59\x5A\x4E\x55\x64\x44"
            );

        $str = strtr(
            $str,
            "\xE1\xE8\xEF\xEC\xE9\xED\xF2\xF3\xF8\x9A\x9D\xF9\xFA\xFD\x9E\xF4\xBC" .
            "\xBE\xC1\xC8\xCF\xCC\xC9\xCD\xC2\xD3\xD8\x8A\x8D\xDA\xDD\x8E\xD2\xD9\xEF\xCF",

            "\x61\x63\x64\x65\x65\x69\x6E\x6F\x72\x73\x74\x75\x75\x79\x7A\x6F\x4C" .
            "\x6C\x41\x43\x44\x45\x45\x49\x4E\x4F\x52\x53\x54\x55\x59\x5A\x4E\x55\x64\x44"
        );

        $str = strtr(
            $str,
            "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7
         \xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1
         \xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0
         \xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC
         \xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8
         \xF9\xFA\xFB\xFD\xFF",
            "!ao?AAAAAC
         EEEEIIIIDN
         OOOOOUUUYa
         aaaaceeeei
         iiidnooooo
         uuuyy"
        );

        $str = strtr($str, array("\xC4" => "Ae", "\xC6" => "AE", "\xD6" => "Oe", "\xDC" => "Ue", "\xDE" => "TH", "\xDF" => "ss", "\xE4" => "ae", "\xE6" => "ae", "\xF6" => "oe", "\xFC" => "ue", "\xFE" => "th"));

        /* if (stripos($old, 'dij') == false && stripos($str, 'dij') != false)
        $str = str_ireplace('dij', 'dj', $str); */

        return $str;
    }

    /**
     * @param mixed $string
     * @param bool $german
     * 
     * @return [type]
     */
    public static function remove_accents($string, $german = false)
    {
        // Single letters
        $single_fr = explode(" ", "À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;");
        $single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");
        $single = array();
        for ($i = 0; $i < count($single_fr); $i++) {
            $single[$single_fr[$i]] = $single_to[$i];
        }
        // Ligatures
        $ligatures = array("Æ" => "Ae", "æ" => "ae", "Œ" => "Oe", "œ" => "oe", "ß" => "ss");
        // German umlauts
        $umlauts = array("Ä" => "Ae", "ä" => "ae", "Ö" => "Oe", "ö" => "oe", "Ü" => "Ue", "ü" => "ue");
        // Replace
        $replacements = array_merge($single, $ligatures);
        if ($german)
            $replacements = array_merge($replacements, $umlauts);
        $string = strtr($string, $replacements);
        return $string;
    }

    /**
     * @param mixed $str
     * @param mixed $from
     * @param null $to
     * 
     * @return [type]
     */
    public static function mb_strtr($str, $from, $to = "")
    {
        if (is_array($from)) {
            $from = array_map('utf8_decode', $from);
            $from = array_map('utf8_decode', array_flip($from));
            return utf8_encode(strtr(utf8_decode($str), array_flip($from)));
        }
        return utf8_encode(strtr(utf8_decode($str), utf8_decode($from), utf8_decode($to)));
    }

    /**
     * @param mixed $cp1252
     * 
     * @return [type]
     */
    public static function transcribe_cp1252_to_latin1($cp1252)
    {
        return strtr(
            $cp1252,
            array(
                "\x80" => "e",
                "\x81" => " ",
                "\x82" => "'",
                "\x83" => 'f',
                "\x84" => '"',
                "\x85" => "...",
                "\x86" => "+",
                "\x87" => "#",
                "\x88" => "^",
                "\x89" => "0/00",
                "\x8A" => "S",
                "\x8B" => "<",
                "\x8C" => "OE",
                "\x8D" => " ",
                "\x8E" => "Z",
                "\x8F" => " ",
                "\x90" => " ",
                "\x91" => "`",
                "\x92" => "'",
                "\x93" => '"',
                "\x94" => '"',
                "\x95" => "*",
                "\x96" => "-",
                "\x97" => "--",
                "\x98" => "~",
                "\x99" => "(TM)",
                "\x9A" => "s",
                "\x9B" => ">",
                "\x9C" => "oe",
                "\x9D" => " ",
                "\x9E" => "z",
                "\x9F" => "Y"
            )
        );
    }

    /**
     * @param mixed $str
     * 
     * @return [type]
     */
    public static function remove_accent($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

    /**
     * @param mixed $string
     * 
     * @return [type]
     */
    public static function normalizeText($string)
    {
        $table = array(
            'Š' => 'S',
            'š' => 's',
            'Đ' => 'Dj',
            'đ' => 'dj',
            'Ž' => 'Z',
            'ž' => 'z',
            'Č' => 'C',
            'č' => 'c',
            'Ć' => 'C',
            'ć' => 'c',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'Þ' => 'B',
            'ß' => 'Ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ý' => 'y',
            'ý' => 'y',
            'þ' => 'b',
            'ÿ' => 'y',
            'Ŕ' => 'R',
            'ŕ' => 'r',
            chr(0x8A) => chr(0xA9),
            chr(0x8C) => chr(0xA6),
            chr(0x8D) => chr(0xAB),
            chr(0x8E) => chr(0xAE),
            chr(0x8F) => chr(0xAC),
            chr(0x9C) => chr(0xB6),
            chr(0x9D) => chr(0xBB),
            chr(0xA1) => chr(0xB7),
            chr(0xA5) => chr(0xA1),
            chr(0xBC) => chr(0xA5),
            chr(0x9F) => chr(0xBC),
            chr(0xB9) => chr(0xB1),
            chr(0x9A) => chr(0xB9),
            chr(0xBE) => chr(0xB5),
            chr(0x9E) => chr(0xBE),
            chr(0x80) => '&euro;',
            chr(0x82) => '&sbquo;',
            chr(0x84) => '&bdquo;',
            chr(0x85) => '&hellip;',
            chr(0x86) => '&dagger;',
            chr(0x87) => '&Dagger;',
            chr(0x89) => '&permil;',
            chr(0x8B) => '&lsaquo;',
            chr(0x91) => '&lsquo;',
            chr(0x92) => '&rsquo;',
            chr(0x93) => '&ldquo;',
            chr(0x94) => '&rdquo;',
            chr(0x95) => '&bull;',
            chr(0x96) => '&ndash;',
            chr(0x97) => '&mdash;',
            chr(0x99) => '&trade;',
            chr(0x9B) => '&rsquo;',
            chr(0xA6) => '&brvbar;',
            chr(0xA9) => '&copy;',
            chr(0xAB) => '&laquo;',
            chr(0xAE) => '&reg;',
            chr(0xB1) => '&plusmn;',
            chr(0xB5) => '&micro;',
            chr(0xB6) => '&para;',
            chr(0xB7) => '&middot;',
            chr(0xBB) => '&raquo;',
            "\x80" => "e",
            "\x81" => " ",
            "\x82" => "'",
            "\x83" => 'f',
            "\x84" => '"',
            "\x85" => "...",
            "\x86" => "+",
            "\x87" => "#",
            "\x88" => "^",
            "\x89" => "0/00",
            "\x8A" => "S",
            "\x8B" => "<",
            "\x8C" => "OE",
            "\x8D" => " ",
            "\x8E" => "Z",
            "\x8F" => " ",
            "\x90" => " ",
            "\x91" => "`",
            "\x92" => "'",
            "\x93" => '"',
            "\x94" => '"',
            "\x95" => "*",
            "\x96" => "-",
            "\x97" => "--",
            "\x98" => "~",
            "\x99" => "(TM)",
            "\x9A" => "s",
            "\x9B" => ">",
            "\x9C" => "oe",
            "\x9D" => " ",
            "\x9E" => "z",
            "\x9F" => "Y",
            // ---
            chr(225) => 'á',
            chr(228) => 'ä',
            chr(232) => 'č',
            chr(239) => 'ď',
            chr(233) => 'é',
            chr(236) => 'ě',
            chr(237) => 'í',
            chr(229) => 'ĺ',
            chr(229) => 'ľ',
            chr(242) => 'ň',
            chr(244) => 'ô',
            chr(243) => 'ó',
            chr(154) => 'š',
            chr(248) => 'ř',
            chr(250) => 'ú',
            chr(249) => 'ů',
            chr(157) => 'ť',
            chr(253) => 'ý',
            chr(158) => 'ž',
            chr(193) => 'Á',
            chr(196) => 'Ä',
            chr(200) => 'Č',
            chr(207) => 'Ď',
            chr(201) => 'É',
            chr(204) => 'Ě',
            chr(205) => 'Í',
            chr(197) => 'Ĺ',
            chr(188) => 'Ľ',
            chr(210) => 'Ň',
            chr(212) => 'Ô',
            chr(211) => 'Ó',
            chr(138) => 'Š',
            chr(216) => 'Ř',
            chr(218) => 'Ú',
            chr(217) => 'Ů',
            chr(141) => 'Ť',
            chr(221) => 'Ý',
            chr(142) => 'Ž',
            chr(150) => '-',
        );
        return strtr($string, $table);
    }

    /**
     * @param mixed $text
     * @param string $encoding
     * 
     * @return [type]
     */
    public static function w1250_to_utf8($text, $encoding = 'ISO-8859-2')
    {
        // map based on:
        // http://konfiguracja.c0.pl/iso02vscp1250en.html
        // http://konfiguracja.c0.pl/webpl/index_en.html#examp
        // http://www.htmlentities.com/html/entities/
        $map = array(
            chr(0x8A) => chr(0xA9),
            chr(0x8C) => chr(0xA6),
            chr(0x8D) => chr(0xAB),
            chr(0x8E) => chr(0xAE),
            chr(0x8F) => chr(0xAC),
            chr(0x9C) => chr(0xB6),
            chr(0x9D) => chr(0xBB),
            chr(0xA1) => chr(0xB7),
            chr(0xA5) => chr(0xA1),
            chr(0xBC) => chr(0xA5),
            chr(0x9F) => chr(0xBC),
            chr(0xB9) => chr(0xB1),
            chr(0x9A) => chr(0xB9),
            chr(0xBE) => chr(0xB5),
            chr(0x9E) => chr(0xBE),
            chr(0x80) => '&euro;',
            chr(0x82) => '&sbquo;',
            chr(0x84) => '&bdquo;',
            chr(0x85) => '&hellip;',
            chr(0x86) => '&dagger;',
            chr(0x87) => '&Dagger;',
            chr(0x89) => '&permil;',
            chr(0x8B) => '&lsaquo;',
            chr(0x91) => '&lsquo;',
            chr(0x92) => '&rsquo;',
            chr(0x93) => '&ldquo;',
            chr(0x94) => '&rdquo;',
            chr(0x95) => '&bull;',
            chr(0x96) => '&ndash;',
            chr(0x97) => '&mdash;',
            chr(0x99) => '&trade;',
            chr(0x9B) => '&rsquo;',
            chr(0xA6) => '&brvbar;',
            chr(0xA9) => '&copy;',
            chr(0xAB) => '&laquo;',
            chr(0xAE) => '&reg;',
            chr(0xB1) => '&plusmn;',
            chr(0xB5) => '&micro;',
            chr(0xB6) => '&para;',
            chr(0xB7) => '&middot;',
            chr(0xBB) => '&raquo;',
        );
        $text = html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', $encoding), ENT_QUOTES, 'UTF-8');
        $text = strtr($text, $map);
        return $text;
    }

    /**
     * $s original text
     * $a array(replace, with)
     */
    public static function replaceText($s, $a)
    {
        if (is_string($a))
            $a = array(0 => array($a), 1 => array(""));
        $s = str_ireplace($a[0], $a[1], $s);
        return $s;
    }


    /**
     * @param mixed $ws
     * 
     * @return [type]
     */
    public static function normalizeString($ws)
    {
        $ws = str_ireplace(
            array("-", " .", " ,", " ?", " !", " :", "  "),
            array(" - ", ".", ",", "?", "!", ": ", " "),
            $ws
        );
        return $ws;
    }

    /**
     * $title string
     * $words array
     * @return array
     */
    public static function w2a($title, $words)
    {
        //$ws = preg_split("/u(?<=\w)\b\s*/", $title);
        $ws = $title;
        $ws = str_ireplace(
            array("-", ".", ",", "?", "!"),
            array(" - ", " .", " ,", " ?", " !"),
            $ws
        );
        $ws = explode(" ", $ws);
        $wa = array();
        foreach ($ws as $w) {
            if (isset($words[$w])) {
                $wa[] = $words[$w];
            } else {
                $wa[] = $w;
            }
        }
        return $wa;
    }

    /**
     * $a array
     * $words array 
     * @return string
     */
    public static function a2w($a, $words)
    {
        $t = array_flip($words);
        foreach ($a as $k => $i)
            if (is_numeric($i)) {
                if (isset($t[$i])) {
                    $a[$k] = $t[$i];
                }
            }
        $s = implode(' ', $a);
        $s = self::normalizeString($s);
        return $s;
    }

    /**
     * @param mixed $w
     * @param mixed $mn
     * @param string $replace
     * 
     * @return [type]
     */
    public static function removeAll($w, $mn, $replace = "")
    {
        while (stripos($w, $mn) > -1) {
            $o = $w;
            $w = str_ireplace($mn, $replace, $w);
        }
        return $w;
    }



    /** LZW compression
     * @param string data to compress
     * @return string binary data
     */
    public static function lzw_compress($string)
    {
        // compression
        $dictionary = array_flip(range("\0", "\xFF"));
        $word = "";
        $codes = array();
        for ($i = 0; $i <= strlen($string); $i++) {
            $x = substr($string, $i, 1);
            if (strlen($x) && isset($dictionary[$word . $x])) {
                $word .= $x;
            } elseif ($i) {
                $codes[] = $dictionary[$word];
                $dictionary[$word . $x] = count($dictionary);
                $word = $x;
            }
        }

        // convert codes to binary string
        $dictionary_count = 256;
        $bits = 8; // ceil(log($dictionary_count, 2))
        $return = "";
        $rest = 0;
        $rest_length = 0;
        foreach ($codes as $code) {
            $rest = ($rest << $bits) + $code;
            $rest_length += $bits;
            $dictionary_count++;
            if ($dictionary_count >> $bits) {
                $bits++;
            }
            while ($rest_length > 7) {
                $rest_length -= 8;
                $return .= chr($rest >> $rest_length);
                $rest &= (1 << $rest_length) - 1;
            }
        }
        return $return . ($rest_length ? chr($rest << (8 - $rest_length)) : "");
    }




    /** LZW decompression
     * @param string compressed binary data
     * @return string original data
     */
    public static function lzw_decompress($binary)
    {
        // convert binary string to codes
        $dictionary_count = 256;
        $bits = 8; // ceil(log($dictionary_count, 2))
        $codes = array();
        $rest = 0;
        $rest_length = 0;
        for ($i = 0; $i < strlen($binary); $i++) {
            $rest = ($rest << 8) + ord($binary[$i]);
            $rest_length += 8;
            if ($rest_length >= $bits) {
                $rest_length -= $bits;
                $codes[] = $rest >> $rest_length;
                $rest &= (1 << $rest_length) - 1;
                $dictionary_count++;
                if ($dictionary_count >> $bits) {
                    $bits++;
                }
            }
        }

        // decompression
        $dictionary = range("\0", "\xFF");
        $return = "";
        $word = null;
        foreach ($codes as $i => $code) {
            $element = $dictionary[$code];
            if (!isset($element)) {
                $element = $word . $word[0];
            }
            $return .= $element;
            if ($i) {
                $dictionary[] = $word . $element[0];
            }
            $word = $element;
        }
        return $return;
    }

    /**
     * It converts any text to UTF-8
     * 
     * @param text The text to be converted.
     * 
     * @return the text in UTF-8 format.
     */

    public static function toutf8($text)
    {
        return iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
    }


    /**
     * @param mixed $var
     * @param  $deep
     * 
     * @return [type]
     */
    public static function any2utf8($var, $deep = TRUE)
    {
        if (is_array($var)) {
            foreach ($var as $key => $value) {
                if ($deep) {
                    $var[$key] = self::any2utf8($value, $deep);
                } elseif (!is_array($value) && !is_object($value) && !mb_detect_encoding($value, 'utf-8', true)) {
                    $var[$key] = utf8_encode($value);
                }
            }
            return $var;
        } elseif (is_object($var)) {
            foreach ($var as $key => $value) {
                if ($deep) {
                    $var->$key = self::any2utf8($value, $deep);
                } elseif (!is_array($value) && !is_object($value) && !mb_detect_encoding($value, 'utf-8', true)) {
                    $var->$key = utf8_encode($value);
                }
            }
            return $var;
        } else {
            return (!mb_detect_encoding($var, 'utf-8', true)) ? utf8_encode($var) : $var;
        }
    }

    /**
     * It encodes the data in base64url format.
     * 
     * @param data The data to be encoded.
     * 
     * @return The base64url_encode function is returning the base64 encoded string with the + and /
     * characters replaced with - and _ characters.
     */
    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * It takes a base64url encoded string and converts it to a base64 encoded string
     * 
     * @param data The data to be encoded.
     * 
     * @return The base64url_decode function is returning the base64 decoded string.
     */
    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }


    /**
     * It returns an array of gender options.
     * 
     * @param id The id of the option you want to return. If null, it will return the entire array.
     */
    public static function gender($id = null)
    {
        $result = array(
            0 => self::t('front', "Male"),
            1 => self::t('front', "Female"),
            2 => self::t('front', "Rather not say"),
            3 => self::t('front', "Other"),
        );
        return self::arrChoice($result, $id);
    }


    /**
     * It takes an array of objects and returns an array of objects indexed by the id property of the
     * objects
     * 
     * @param a The array to re-index
     * 
     * @return An array of objects with the key being the id of the object.
     */
    public static function reIndex($a)
    {
        if (is_array($a)) {
            $temp = array();
            foreach ($a as $o)
                $temp[$o->id] = $o;
            return $temp;
        }
        return $a;
    }



    /**
     * If the email address is not valid, return false. Otherwise, return true
     * 
     * @param email The email address to validate.
     * 
     * @return A boolean value.
     */
    public static function isValidEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }


    /**
     * > If the second argument is null, return the first argument, otherwise return the value of the
     * first argument at the index of the second argument
     * 
     * @param a The array to check
     * @param i the index of the array you want to return. If you don't specify this, the entire array
     * will be returned.
     */
    public static function arrOrValue($a, $i = null)
    {
        $r = $a;
        if (!is_null($i)) {
            $r = "";
            if (isset($a[$i]))
                $r = $a[$i];
        }
        return $r;
    }


    /**
     * Generates a random string of a given size.
     * 
     * @param size The length of the string you want to generate.
     * 
     * @return A random string of characters.
     */
    public static function generateRandomString($size)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $size; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }



    /**
     * General method, get array value by id, if exists
     * without parameter it return the options;
     */
    public static function arrChoice($arr, $i = null)
    {
        if (!is_null($i)) {
            if (isset($arr[$i])) {
                return $arr[$i];
            } else
                return 0;
        }
        return $arr;
    }



    /**
     * return html tag with star on for rating
     */
    public static function staron()
    {
        return '<i class="fa fa-star"></i>';
    }

    /**
     * return html tag with star off for rating
     */
    public static function staroff()
    {
        return '<i class="fa fa-star-o"></i>';
    }

    /**
     * The function "stars" returns a string of stars and non-stars based on the input value.
     * 
     * @param r The parameter "r" in the "stars" function represents the number of stars to be turned
     * on.
     * 
     * @return a string of stars, where the number of "on" stars is equal to the input parameter ``,
     * and the number of "off" stars is equal to `5 - `.
     */
    public static function stars($r)
    {
        $s = '';
        for ($i = 0; $i < $r; $i++)
            $s .= self::staron();
        for ($i = 0; $i < 5 - $r; $i++)
            $s .= self::staroff();
        return $s;
    }



    /**
     * Show oading rotating circle while ajax call is peformed
     */
    public static function loading($text = "Loading...")
    {
        $loading = "<i class='fa fa-spin fa-spinner'></i> " . $text;
        return $loading;
    }

    /**
     * It takes an amount, a total, and a number of decimal places, and returns the percentage of the
     * amount to the total
     * 
     * @param amount The amount to calculate the percentage for.
     * @param total The total amount of items.
     * @param decimals The number of decimal places to round to.
     * 
     * @return The percentage of the amount to the total.
     */

    public static function percentage($amount, $total, $decimals = 0)
    {
        $percent = 0;
        if ($amount > 0 && $total > 0) {
            $percent = 100 / $total * $amount;
        }
        return round($percent, $decimals);
    }

    /**
     * It takes a string of text and returns an array with the number of minutes and seconds it would take
     * to read it
     * 
     * @param text The text you want to estimate the reading time for.
     * @param wpm Words per minute. This is the average reading speed of an adult.
     * 
     * @return An array with two keys, minutes and seconds.
     */

    public static function estimateReadingTime($text, $wpm = 200)
    {
        $totalWords = str_word_count(strip_tags($text));
        $minutes = floor($totalWords / $wpm);
        $seconds = floor($totalWords % $wpm / ($wpm / 60));

        return array(
            'minutes' => $minutes,
            'seconds' => $seconds
        );
    }


    /**
     * It takes a timestamp and returns a string like "2 hours ago", "3 days ago", "in 2 weeks", etc
     * 
     * @param ts The timestamp to compare to.
     * @param n The current timestamp. If you don't set it, the current time will be used.
     * 
     * @return The difference between the current time and the time passed in.
     */
    public static function dateDiff($ts, $n = null)
    {

        if (is_null($n))
            $n = time();

        if (!ctype_digit($ts))
            $ts = strtotime($ts);

        $diff = $n - $ts;
        if ($diff == 0)
            return 'now';
        elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60)
                    return 'just now';
                if ($diff < 120)
                    return '1 minute ago';
                if ($diff < 3600)
                    return floor($diff / 60) . ' minutes ago';
                if ($diff < 7200)
                    return '1 hour ago';
                if ($diff < 86400)
                    return floor($diff / 3600) . ' hours ago';
            }
            if ($day_diff == 1)
                return 'Yesterday';
            if ($day_diff < 7)
                return $day_diff . ' days ago';
            if ($day_diff < 31)
                return ceil($day_diff / 7) . ' weeks ago';
            if ($day_diff < 60)
                return 'last month';
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120)
                    return 'in a minute';
                if ($diff < 3600)
                    return 'in ' . floor($diff / 60) . ' minutes';
                if ($diff < 7200)
                    return 'in an hour';
                if ($diff < 86400)
                    return 'in ' . floor($diff / 3600) . ' hours';
            }
            if ($day_diff == 1)
                return 'Tomorrow';
            if ($day_diff < 4)
                return date('l', $ts);
            if ($day_diff < 7 + (7 - date('w')))
                return 'next week';
            if (ceil($day_diff / 7) < 4)
                return 'in ' . ceil($day_diff / 7) . ' weeks';
            if (date('n', $ts) == date('n') + 1)
                return 'next month';
            return date('F Y', $ts);
        }
    }


    /**
     * This function retrieves command line parameters and stores them in the  array.
     * 
     * @return an array of command line parameters passed to the script. It parses the `` global
     * variable and converts the parameters into key-value pairs in the `` superglobal array.
     * The function then returns the `` array.
     */
    public function commandLineParams()
    {
        global $argv;
        if (is_array($argv) && count($argv) > 0)
            foreach ($argv as $k => $a)
                if (stripos($a, "=") !== false) {
                    $r = explode("=", $a);
                    $_REQUEST[$r[0]] = $r[1];
                } else {
                    if ($k % 2 == 0 && $k > 1) {
                        $_REQUEST[$argv[$k - 1]] = $argv[$k];
                    }
                }
        return $_REQUEST;
    }


    /**
     * It generates a random string of characters, hashes it, converts it to lowercase, splits it into
     * groups of 6 characters, and joins them with a dash
     * 
     * Great to use as API key/secret generators
     * 
     * @param characters The total number of characters in the API key.
     * @param group The number of characters to group together.
     * 
     * @return A string of characters that is 24 characters long, with a dash every 6 characters.
     */
    public static function generateApiKey($characters = 24, $group = 6, $salt = "APP1", $prefix = "APP")
    {
        return $prefix . '-' . implode('-', str_split(substr(strtolower(sha1($salt . microtime() . rand(1000, 9999))), 0, $characters), $group));
    }


    /**
     * This function flushes the output buffer and sleeps for 1 second.
     */
    public static function flushProgress()
    {
        if (ob_get_level() == 0)
            ob_start();
        ob_flush();
        flush();
        usleep(1);
        ob_end_flush();
        ob_start();
    }

    /**
     * The function checks if a given date string is valid according to a specified format in PHP.
     * 
     * @param date The date string that needs to be validated. It should be in the format specified by
     * the  parameter (default is 'Y-m-d', which represents year-month-day format).
     * @param format The format parameter specifies the format of the date string passed as the first
     * argument to the function isAValidDate(). The default format is 'Y-m-d', which represents the
     * date in the format of year-month-day. However, you can pass any valid date format string to this
     * parameter.
     * 
     * @return The function `isAValidDate` returns a boolean value indicating whether the given date
     * string is valid according to the specified format. It returns `true` if the date is valid and
     * matches the format, and `false` otherwise.
     */
    public static function isAValidDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    /**
     * The function checks if a given string is a valid date.
     * 
     * @param myDateString The parameter `` is a string that represents a date and/or time
     * in a specific format. The function `strtotime()` is used to convert this string into a Unix
     * timestamp, which is a numeric value representing the number of seconds since January 1, 1970,
     * 00:00
     * 
     * @return A boolean value indicating whether the input string can be converted to a valid date and
     * time using the `strtotime()` function.
     */
    public static function isAValidDateString($myDateString)
    {
        return (bool) strtotime($myDateString);
    }


    /**
     * The function determines the language of a given text by counting the occurrences of the most
     * frequent words in different languages and returning the language with the highest count, with a
     * fallback to a default language if no clear winner is found.
     * 
     * @param text The input text that needs to be analyzed to determine the language.
     * @param default The default language to return if no language is detected from the input text.
     * 
     * @return the language code of the most likely language of the input text based on the occurrence
     * of the most frequent words in the text. If no language is detected, it returns the default
     * language code provided as an argument.
     */
    public static function getTextLanguage($text, $default = null)
    {
        $supported_languages = array(
            'en',
            'de',
            'fr',
            'es',
        );
        // German word list
        // from http://wortschatz.uni-leipzig.de/Papers/top100de.txt
        $wordList['de'] = array(
            'der',
            'die',
            'und',
            'in',
            'den',
            'von',
            'zu',
            'das',
            'mit',
            'sich',
            'des',
            'auf',
            'für',
            'ist',
            'im',
            'dem',
            'nicht',
            'ein',
            'Die',
            'eine'
        );
        // English word list
        // from http://en.wikipedia.org/wiki/Most_common_words_in_English
        $wordList['en'] = array(
            'the',
            'be',
            'to',
            'of',
            'and',
            'a',
            'in',
            'that',
            'have',
            'I',
            'it',
            'for',
            'not',
            'on',
            'with',
            'he',
            'as',
            'you',
            'do',
            'at'
        );
        // French word list
        // from https://1000mostcommonwords.com/1000-most-common-french-words/
        $wordList['fr'] = array(
            'comme',
            'que',
            'tait',
            'pour',
            'sur',
            'sont',
            'avec',
            'tre',
            'un',
            'ce',
            'par',
            'mais',
            'que',
            'est',
            'il',
            'eu',
            'la',
            'et',
            'dans'
        );

        // Spanish word list
        // from https://spanishforyourjob.com/commonwords/
        $wordList['es'] = array(
            'que',
            'no',
            'a',
            'la',
            'el',
            'es',
            'y',
            'en',
            'lo',
            'un',
            'por',
            'qu',
            'si',
            'una',
            'los',
            'con',
            'para',
            'est',
            'eso',
            'las'
        );
        // clean out the input string - note we don't have any non-ASCII 
        // characters in the word lists... change this if it is not the 
        // case in your language wordlists!
        $text = preg_replace("/[^A-Za-z]/", ' ', $text);
        // count the occurrences of the most frequent words
        foreach ($supported_languages as $language) {
            $counter[$language] = 0;
        }
        for ($i = 0; $i < 20; $i++) {
            foreach ($supported_languages as $language) {
                $counter[$language] = $counter[$language] +
                    // I believe this is way faster than fancy RegEx solutions
                    substr_count($text, ' ' . $wordList[$language][$i] . ' ');
                ;
            }
        }
        // get max counter value
        // from http://stackoverflow.com/a/1461363
        $max = max($counter);
        $maxs = array_keys($counter, $max);
        // if there are two winners - fall back to default!
        if (count($maxs) == 1) {
            $winner = $maxs[0];
            $second = 0;
            // get runner-up (second place)
            foreach ($supported_languages as $language) {
                if ($language <> $winner) {
                    if ($counter[$language] > $second) {
                        $second = $counter[$language];
                    }
                }
            }
            // apply arbitrary threshold of 10%
            if (($second / $max) < 0.1) {
                return $winner;
            }
        }
        return $default;
    }

    /**
     * The function pads a string with a specified character on the left side until it reaches a
     * specified length.
     * 
     * @param s  is the string that needs to be padded.
     * @param l The parameter "l" in the function "padl" is an optional parameter that specifies the
     * desired length of the resulting string. If this parameter is not provided, the default value of
     * 0 will be used.
     * @param c The parameter "c" is a string that represents the character to be used for padding. By
     * default, it is set to a space character.
     * 
     * @return the string `` padded with the character `` on the left side until it reaches the
     * length ``.
     */
    public function padl($s, $l = 0, $c = " ")
    {
        while (strlen($s) < $l)
            $s = $c . $s;
        return $s;
    }

    /**
     * The function checks if a given date is valid and returns true if the year is greater than 1800.
     * 
     * @param d The parameter "d" is a variable that represents a date. It can be either a string or a
     * timestamp. The function "validDate" checks if the year of the given date is greater than 1700
     * and returns a boolean value.
     * 
     * @return The function is checking if the input date is valid and returns a boolean value.
     * Specifically, it checks if the year of the input date is greater than 1700 and returns true if
     * it is, and false otherwise.
     */
    public static function validDate($d)
    {
        if (is_string($d))
            $d = strtotime($d);
        return date("Y", $d) > 1800;
    }

    /**
     * The function retrieves an RSS feed and converts it into HTML format with options to limit the
     * number of items, show date and description, and cache the results.
     * 
     * @param feed_url The URL of the RSS feed to be parsed and displayed as HTML.
     * @param max_item_cnt The maximum number of items to display from the RSS feed.
     * @param show_date A boolean value that determines whether to display the date of each feed item.
     * If set to true, the date will be displayed. If set to false, the date will not be displayed.
     * @param show_description A boolean value that determines whether to show the description of each
     * feed item or not. If set to true, the description will be displayed.
     * @param max_words The maximum number of words to display in the description of each feed item. If
     * set to 0, the full description will be displayed.
     * @param cache_timeout The amount of time (in seconds) to cache the RSS feed before checking for
     * updates. If set to 0, caching is disabled.
     * @param cache_prefix The prefix to use for the cache file name. It is concatenated with the MD5
     * hash of the feed URL to create a unique cache file name.
     * 
     * @return an HTML string that displays a list of RSS feed items. The number of items displayed,
     * whether to show the date and description, and the maximum number of words in the description can
     * be customized using the function parameters. The function also caches the RSS feed content to
     * improve performance.
     */
    public static function get_rss_feed_as_html($feed_url, $max_item_cnt = 10, $show_date = true, $show_description = true, $max_words = 0, $cache_timeout = 7200, $cache_prefix = "/tmp/rss2html-")
    {
        $result = "";
        // get feeds and parse items
        $rss = new \DOMDocument();
        $cache_file = $cache_prefix . md5($feed_url);
        // load from file or load content
        if (
            $cache_timeout > 0 &&
            is_file($cache_file) &&
            (filemtime($cache_file) + $cache_timeout > time())
        ) {
            $rss->load($cache_file);
        } else {
            $rss->load($feed_url);
            if ($cache_timeout > 0) {
                $rss->save($cache_file);
            }
        }
        $feed = array();
        foreach ($rss->getElementsByTagName('item') as $node) {
            $item = array(
                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'content' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
            );
            $content = $node->getElementsByTagName('encoded'); // <content:encoded>
            if ($content->length > 0) {
                $item['content'] = $content->item(0)->nodeValue;
            }
            array_push($feed, $item);
        }
        // real good count
        if ($max_item_cnt > count($feed)) {
            $max_item_cnt = count($feed);
        }
        $result .= '<ul class="feed-lists">';
        for ($x = 0; $x < $max_item_cnt; $x++) {
            $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
            $link = $feed[$x]['link'];
            $result .= '<li class="feed-item">';
            $result .= '<div class="feed-title"><strong><a href="' . $link . '" title="' . $title . '">' . $title . '</a></strong></div>';
            if ($show_date) {
                $date = date('l F d, Y', strtotime($feed[$x]['date']));
                $result .= '<small class="feed-date"><em>Posted on ' . $date . '</em></small>';
            }
            if ($show_description) {
                $description = $feed[$x]['desc'];
                $content = $feed[$x]['content'];
                // find the img
                $has_image = preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                // no html tags
                $description = strip_tags(preg_replace('/(<(script|style)\b[^>]*>).*?(<\/\2>)/s', "$1$3", $description), '');
                // whether cut by number of words
                if ($max_words > 0) {
                    $arr = explode(' ', $description);
                    if ($max_words < count($arr)) {
                        $description = '';
                        $w_cnt = 0;
                        foreach ($arr as $w) {
                            $description .= $w . ' ';
                            $w_cnt = $w_cnt + 1;
                            if ($w_cnt == $max_words) {
                                break;
                            }
                        }
                        $description .= " ...";
                    }
                }
                // add img if it exists
                if ($has_image == 1) {
                    $description = '<img class="feed-item-image" src="' . $image['src'] . '" />' . $description;
                }
                $result .= '<div class="feed-description">' . $description;
                $result .= ' <a href="' . $link . '" title="' . $title . '">Continue Reading &raquo;</a>' . '</div>';
            }
            $result .= '</li>';
        }
        $result .= '</ul>';
        return $result;
    }




    /**
     * @param mixed $string
     * 
     * @return [type]
     */
    public static function is_json($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string, true))))) ? true : false;
    }

    /**
     * @param mixed $string
     * 
     * @return [type]
     */
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

    /**
     * @param mixed $arr
     * @param mixed $key
     * @param mixed $def
     * 
     * @return [type]
     */
    public static function jsonValue($arr, $key, $def) // support old calls;
    {
        return self::json_value($arr, $key, $def);
    }

    /**
     * @return [type]
     */
    public static function json_error()
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


    /**
     * [Description for json_array]
     *
     * @param mixed $data
     * 
     * @return [type]
     * 
     */
    public static function json_array($data)
    {
        if (is_array($data))
            foreach ($data as $k => $v) {
                if (is_numeric($k)) {
                    if (is_array($v)) {
                        if (!isset($v["id"]))
                            $data[$k][$k] = $k;
                    } else if (is_scalar($v)) {
                        $data[$k] = array(
                            "id" => $k,
                            "name" => $v,
                        );
                    }
                }
            }
        return json_encode(array_values($data));
    }

    /**
     * [Description for json_value]
     *
     * @param mixed $s
     * @param mixed $key
     * 
     * @return [type]
     * 
     */
    public static function json_value($arr, $key, $def=false)
    {
        $value = $def;
        if (is_string($arr))
            $arr = json_decode($arr, true);
        if (is_array($arr) && isset($arr[$key]))
            $value=$arr[$key];
        return $value;
    }

    /**
     * [Description for sendOk]
     *
     * @param mixed $data
     * 
     * @return [type]
     * 
     */
    public static function sendOk($data)
    {
        self::send(array("status" => "OK", "data" => $data));
    }

    /**
     * [Description for sendError]
     *
     * @param mixed $msg
     * 
     * @return [type]
     * 
     */
    public static function sendError($msg)
    {
        self::send(array("status" => "ERROR", "message" => $msg));
    }



}