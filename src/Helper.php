<?php // ﷽‎
namespace RapTToR;

/**
 * @author rapttor
 *
 * require __DIR__ . '/protected/vendor/autoload.php';
 */
$RapTToR_HELPER = array();
$RapTToR_LANGUAGES = array();

class Helper
{


    public static function parseEmails($string)
    {
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $string, $matches);
        return $matches[0];
    }

    public static function cleanup($meta, $bad = array())
    {
        foreach ($meta as $k => $v) if (is_string($v)) {
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

    public static function validUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function validEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function onlyNumbers($strOrgNumber)
    {
        return preg_replace('/[^0-9.]+/', '', $strOrgNumber);
    }

    public static function fixUrl($url)
    {
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace("//", "/", $url);
        $url = str_ireplace(":/", "://", $url);
        return $url;
    }


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


    public static function again($print = false)
    {
        $result = "<script>
            document.location.reload();
        </script>";
        if ($print) echo $result;
        return $result;
    }


    public static function parseUrls($string)
    {
        preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
        return (is_array($match[0])) ? $match[0] : null;
    }

    public static function toEnglishDate($date, $glue = " ", $lang = "sv_SE")
    {
        $w = explode($glue, $date);
        $n = array();
        foreach ($w as $k => $m) {
            if (!is_numeric($m)) $m = self::getEnglishMonthName($m, $lang);
            $n[$k] = $m;
        }
        return implode($glue, $n);
    }

    public static function getEnglishMonthName($foreignMonthName, $setlocale = 'sv_SE')
    {

        setlocale(LC_ALL, 'en_US');

        $month_numbers = range(1, 12);
        $english_months = array();
        $foreign_months = array();
        foreach ($month_numbers as $month)
            $english_months[] = strftime('%B', mktime(0, 0, 0, $month, 1, 2011));

        setlocale(LC_ALL, $setlocale);

        foreach ($month_numbers as $month)
            $foreign_months[] = strftime('%B', mktime(0, 0, 0, $month, 1, 2011));

        return str_replace($foreign_months, $english_months, $foreignMonthName);
    }

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

    public static function flat_array($a)
    {
        $n = array();
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                if (is_array($v)) $n[] = self::flat_array($v);
                if (is_string($v)) $n[] = $v;
            }
        }
        if (is_string($a)) $n[] = $a;
        return $n;
    }



    public static function curl_get_yql($URL)
    {
        $yql_base_url = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = "select * from html where url='$URL'";
        $yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query);
        $yql_query_url .= "&format=json";
        return self::get($yql_query_url);
    }

    public static function post($URL, $data, $proxy = null, $agent = null, $debug = false)
    {
        return self::get($URL, $proxy = null, $agent = null, $debug = false, $data);
    }

    public static function get($URL, $proxy = null, $agent = null, $debug = false, $data = null)
    {
        $c = curl_init();
        $p = "";

        /* if (!is_null($proxy)) {
            $p = Proxy::one($proxy);
            curl_setopt($c, CURLOPT_HTTPPROXYTUNNEL, 0);
            curl_setopt($c, CURLOPT_PROXY, $p[0]);
            curl_setopt($c, CURLOPT_PROXYPORT, $p[1]);
        } */
        $a = "";
        if (!is_null($agent)) {
            $a = self::agent($agent, self::agentsBot());
            curl_setopt($c, CURLOPT_USERAGENT, $a);
        }
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($c, CURLOPT_TIMEOUT, 40);
        curl_setopt($c, CURLOPT_URL, $URL);
        if (!is_null($data)) {
            if (is_array($data)) {
                $postvars = '';
                foreach ($data as $key => $value) {
                    $postvars .= $key . "=" . $value . "&";
                }
            } else $postvars = $data;
            curl_setopt($c, CURLOPT_POSTFIELDS, $postvars);
        }
        $contents = curl_exec($c);
        if ($debug) {
            $info = "";
            if ($c) $info = curl_getinfo($c);
            if (!$contents) $contents = null;
            return array(
                "url" => $URL,
                "agent" => $a,
                "proxy" => $p,
                "info" => $info,
                "size" => strlen($contents),
                "data" => $contents,
            );
        }
        curl_close($c);
        if ($contents) {
            return $contents;
        } else return FALSE;
    }

    public static function slug($s)
    {
        $o = $s;
        if (is_array($s)) $s = serialize($s);
        if (is_object($s)) $s = json_encode($s);
        $s = self::cleanString($s);
        $s = str_replace(' ', '-', $s); // Replaces all spaces with hyphens.
        $s = preg_replace('/[^A-Za-z0-9\-]/', '', $s); // Removes special chars.
        return $s;
    }


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
        $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;';    // Dagger
        $trans[chr(135)] = '&Dagger;';    // Double Dagger
        $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;';    // Per Mille Sign
        $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;';    // Bullet
        $trans[chr(150)] = '&ndash;';    // En Dash
        $trans[chr(151)] = '&mdash;';    // Em Dash
        $trans[chr(152)] = '&tilde;';    // Small Tilde
        $trans[chr(153)] = '&trade;';    // Trade Mark Sign
        $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;';    // euro currency symbol
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

    public static function serverIP($ip, $allow_private = false, $proxy_ip = [])
    {
        if (!is_string($ip) || is_array($proxy_ip) && in_array($ip, $proxy_ip)) return false;
        $filter_flag = FILTER_FLAG_NO_RES_RANGE;

        if (!$allow_private) {
            //Disallow loopback IP range which doesn't get filtered via 'FILTER_FLAG_NO_PRIV_RANGE' [1]
            //[1] https://www.php.net/manual/en/filter.filters.validate.php
            if (preg_match('/^127\.$/', $ip)) return false;
            $filter_flag |= FILTER_FLAG_NO_PRIV_RANGE;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, $filter_flag) !== false;
    }
    public static function clientIP($allow_private = false)
    {
        //Place your trusted proxy server IPs here.
        $proxy_ip = array('127.0.0.1');

        //The header to look for (Make sure to pick the one that your trusted reverse proxy is sending or else you can get spoofed)
        $header = 'HTTP_X_FORWARDED_FOR'; //HTTP_CLIENT_IP, HTTP_X_FORWARDED, HTTP_FORWARDED_FOR, HTTP_FORWARDED

        //If 'REMOTE_ADDR' seems to be a valid client IP, use it.
        if (self::serverIP($_SERVER['REMOTE_ADDR'], $allow_private, $proxy_ip)) return $_SERVER['REMOTE_ADDR'];

        if (isset($_SERVER[$header])) {
            //Split comma separated values [1] in the header and traverse the proxy chain backwards.
            //[1] https://en.wikipedia.org/wiki/X-Forwarded-For#Format
            $chain = array_reverse(preg_split('/\s*,\s*/', $_SERVER[$header]));
            foreach ($chain as $ip) if (self::serverIP($ip, $allow_private, $proxy_ip)) return $ip;
        }

        return null;
    }


    public static function agentsBot()
    {
        return array(
            "Google bot" =>
            "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)",
            "Bing bot" =>
            "Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)",
            "Yahoo! bot" =>
            "Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)",
        );
    }

    public static function agentsDesktop()
    {
        return array(
            "Windows 10-based PC using Edge browser" =>
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246",
            "Chrome OS-based laptop using Chrome browser (Chromebook)" =>
            "Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36",
            "Mac OS X-based computer using a Safari browser" =>
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9",
            "Windows 7-based PC using a Chrome browser" =>
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36",
            "Linux-based PC using a Firefox browser" =>
            "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1",
        );
    }

    public static function agentsTablet()
    {
        return array(
            "Google Pixel C" =>
            "Mozilla/5.0 (Linux; Android 7.0; Pixel C Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36",
            "Sony Xperia Z4 Tablet" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; SGP771 Build/32.2.A.0.253; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/52.0.2743.98 Safari/537.36",
            "Nvidia Shield Tablet K1" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; SHIELD Tablet K1 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Safari/537.36",
            "Samsung Galaxy Tab S3" =>
            "Mozilla/5.0 (Linux; Android 7.0; SM-T827R4 Build/NRD90M) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.116 Safari/537.36",
            "Samsung Galaxy Tab A" =>
            "Mozilla/5.0 (Linux; Android 5.0.2; SAMSUNG SM-T550 Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/3.3 Chrome/38.0.2125.102 Safari/537.36",
            "Amazon Kindle Fire HDX 7" =>
            "Mozilla/5.0 (Linux; Android 4.4.3; KFTHWI Build/KTU84M) AppleWebKit/537.36 (KHTML, like Gecko) Silk/47.1.79 like Chrome/47.0.2526.80 Safari/537.36",
            "LG G Pad 7.0" =>
            "Mozilla/5.0 (Linux; Android 5.0.2; LG-V410/V41020c Build/LRX22G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/34.0.1847.118 Safari/537.36",
        );
    }

    public static function agentsWindowsMobile()
    {
        return array(
            "Microsoft Lumia 650" =>
            "Mozilla/5.0 (Windows Phone 10.0; Android 6.0.1; Microsoft; RM-1152) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Mobile Safari/537.36 Edge/15.15254",
            "Microsoft Lumia 550" =>
            "Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; RM-1127_16056) AppleWebKit/537.36(KHTML, like Gecko) Chrome/42.0.2311.135 Mobile Safari/537.36 Edge/12.10536",
            "Microsoft Lumia 950" =>
            "Mozilla/5.0 (Windows Phone 10.0; Android 4.2.1; Microsoft; Lumia 950) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Mobile Safari/537.36 Edge/13.1058",
        );
    }

    public static function agentsIOS()
    {
        return array(
            "Apple iPhone XR (Safari)" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1",
            "Apple iPhone XS (Chrome)" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/69.0.3497.105 Mobile/15E148 Safari/605.1",
            "Apple iPhone XS Max (Firefox)" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/13.2b11866 Mobile/16A366 Safari/605.1.15",
            "Apple iPhone X" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1",
            "Apple iPhone 8" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.34 (KHTML, like Gecko) Version/11.0 Mobile/15A5341f Safari/604.1",
            "Apple iPhone 8 Plus" =>
            "Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A5370a Safari/604.1",
            "Apple iPhone 7" =>
            "Mozilla/5.0 (iPhone9,3; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1",
            "Apple iPhone 7 Plus" =>
            "Mozilla/5.0 (iPhone9,4; U; CPU iPhone OS 10_0_1 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A403 Safari/602.1",
            "Apple iPhone 6" =>
            "Mozilla/5.0 (Apple-iPhone7C2/1202.466; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3",
        );
    }

    public static function agentsAndroid()
    {
        return array(
            "Samsung Galaxy S9" =>
            "Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36",
            "Samsung Galaxy S8" =>
            "Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36",
            "Samsung Galaxy S7" =>
            "Mozilla/5.0 (Linux; Android 7.0; SM-G930VC Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36",
            "Samsung Galaxy S7 Edge" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36",
            "Samsung Galaxy S6" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; SM-G920V Build/MMB29K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36",
            "Samsung Galaxy S6 Edge Plus" =>
            "Mozilla/5.0 (Linux; Android 5.1.1; SM-G928X Build/LMY47X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36",
            "Nexus 6P" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; Nexus 6P Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36",
            "Sony Xperia XZ" =>
            "Mozilla/5.0 (Linux; Android 7.1.1; G8231 Build/41.2.A.0.219; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36",
            "Sony Xperia Z5" =>
            "Mozilla/5.0 (Linux; Android 6.0.1; E6653 Build/32.2.A.0.253) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.36",
            "HTC One X10" =>
            "Mozilla/5.0 (Linux; Android 6.0; HTC One X10 Build/MRA58K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/61.0.3163.98 Mobile Safari/537.36",
            "HTC One M9" =>
            "Mozilla/5.0 (Linux; Android 6.0; HTC One M9 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.3",
        );
    }

    public static function agents()
    {
        return
            self::agentsAndroid() +
            self::agentsDesktop() +
            self::agentsIOS() +
            self::agentsTablet() +
            self::agentsWindowsMobile();
    }

    /**
     * @param null $id (<1 for random)
     * @param null $agents
     * @return mixed
     */
    public static function agent($id = null, $agents = null)
    {
        if (is_null($agents)) $agents = self::agents();
        $agents = array_values($agents);
        if (is_null($id) || $id < 1) $id = rand(0, count($agents) - 1);
        return $agents[$id];
    }


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



    /**
     * create statically.io CDN from image
     * $host defaults to ngrok, HTTP_HOST
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
     */
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

    /**
     * Generate remote user UID
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

    public static function showErrors()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    /**
     * Social networks data
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


    /**
     * @param $param (null for all)
     * @param $port (4040)
     * @param $tunnel (0)
     */
    public static function ngrok($param = "public_html", $port = 4040, $tunnel = 0)
    {
        $n = file_get_contents("http://127.0.0.1:$port/api/tunnels");
        $j = json_decode($n, true);
        if (is_null($param)) return $j;
        return (isset($j) && $j && is_array($j)
            && isset($j["tunnels"]) && is_array($j["tunnels"])
            && isset($j["tunnels"][$tunnel]) && is_array($j["tunnels"][0])
            && isset($j["tunnels"][$tunnel][$param]))
            ? $j["tunnels"][$tunnel][$param] : null;
    }
}
