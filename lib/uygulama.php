<?php

/*
  ╠═ Uygulama ═══════════════════════════════════════════════════════════════
  Software: Uygulama :: Web Builder
  Version: beta 2.0
  Support: http://uygulama.net
  Author: goker.cebeci
  Contact: http://uygulama.net
  -------------------------------------------------------------------------
  License: Distributed under the Lesser General Public License (LGPL)
  http://www.gnu.org/copyleft/lesser.html
  This program is distributed in the hope that it will be useful - WITHOUT
  ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
  FITNESS FOR A PARTICULAR PURPOSE.
  ═══════════════════════════════════════════════════════════════════════════ */

class u {

    public static function set($file, $content) {
        return file_put_contents($file . '.json', json_encode($content));
    }

    public static function get($file) {
        return json_decode(file_get_contents($file . '.json'));
    }

    public static function upload($source, $destination) {
        return move_uploaded_file($source, $destination);
    }

    public static function remove($file) {
        return unlink($file);
    }

    public static function rmdir($dir) {
        foreach (glob($dir . '*', GLOB_MARK) as $item) {
            if (is_dir($item))
                self::rmdir($item);
            else
                unlink($item);
        }
        if (is_dir($dir))
            rmdir($dir);
    }

    public static function archive($path, $archive, $pathcleaning = false) {
        $zip = new ZipArchive();
        if ($zip->open($archive, ZIPARCHIVE::CREATE) !== TRUE) {
            die("Could not open archive");
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($iterator as $key => $value)
            if (!preg_match('/\.svn\//', $key) && basename($key) != '.' && basename($key) != '..') {
                $zip->addFile(realpath($key), $pathcleaning ? str_replace($path, '', $key) : $key) or die("ERROR: Could not add file: $key");
            }
        $zip->close();
    }

    public static function download($get) {
        if (is_file($get . '.zip')) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $get . '.zip"');
            header('Content-Transfer-Encoding: binary');
            readfile($get . '.zip');
        } else {
            if (!$_SESSION['ACCESS'])
                header('location: /admin');
            list($type, $name) = preg_split('/\//', $get, 3);
            if (!in_array($type, array('themes', 'templates', 'data')))
                die(u::translate('undefined type'));
            if ($type == 'data') {
                $dir = 'data/';
                $name = 'data';
            } else
                $dir = $type . '/' . $name . '/';
            self::archive($dir, 'data/' . $name . '.zip', true);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $name . '.zip"');
            header('Content-Transfer-Encoding: binary');
            readfile('data/' . $name . '.zip');
            unlink('data/' . $name . '.zip');
        }
    }

    public static function extract($archive, $to) {
        $zip = new ZipArchive();
        $return = ($zip->open($archive) !== true);
        $zip->extractTo($to);
        $zip->close();
        return $return;
    }

    // JSON based gettext :]
    public static function dictionaries() {
        $dictionaries = array();
        $dictionaries['uygulama'] = 'lib,uygulama';
        $dictionaries['admin'] = 'templates,admin';
        $templates = self::templates();
        foreach ($templates as $template)
            $dictionaries[$template] = 'templates,' . $template;
        return $dictionaries;
    }

    public static function dictionary($type, $part) {
        if (!in_array($type, array('lib', 'templates')))
            return null;
        $path = $type == 'lib' ? 'lib/' : $type . '/' . $part . '/';
        $dictionary = $_SESSION['dictionary'][LANG][$type . '.' . $part]; //apc_fetch($type.'.'.$part);
        if (!$dictionary) {
            $file = is_file($path . 'dictionary/' . LANG . '.json') ? $path . 'dictionary/' . LANG . '.json' : $path . 'dictionary/' . DEFLANG . '.json';
            $dictionary = json_decode(@file_get_contents($file));
            $_SESSION['dictionary'][LANG][$type . '.' . $part] = $dictionary; //apc_add($type.'.'.$part, $dictionary);
        }
        return $dictionary;
    }

    public static function translate($word) {
        $caller = debug_backtrace();
        list($type, $part) = explode('/', dirname(str_replace($_SERVER['DOCUMENT_ROOT'] . '/', '', $caller[0]['file'])));
        //return $type.','.$part;
        //$lang = LANG;
        $key = sha1($word);
        $dictionary = self::dictionary($type, $part);
        return $dictionary->$key ? $dictionary->$key : $word;
        //return $word;
    }

    public static function gettext($type, $part) {
        $lang = LANG;
        if (!in_array($type, array('lib', 'templates')))
            return null;
        $path = $type == 'lib' ? 'lib/' : $type . '/' . $part . '/';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $regex = new RegexIterator($iterator, '/^.+\.(php|html)$/i', RecursiveRegexIterator::GET_MATCH);
        $gettext = new stdClass();
        $dictionary = @self::get($path . 'dictionary/' . $lang);
        foreach ($regex as $key => $value) {
            $source = file_get_contents($key);
            //echo '<pre>'.htmlentities($source).'</pre>';
            preg_match_all('/(u::translate\([\'"])(.*?)([\'"]\))/', $source, $matches);
            foreach ($matches[2] as $word) {
                $key = sha1($word);
                $gettext->$key->def = $word;
                $gettext->$key->$lang = $dictionary->$key;
            }
        }
        //print_r($dictionary);
        unset($dictionary);
        return $gettext;
        //self::set($type.'/'.$part.'/dictionary/words', $dictionary);
        //apc_delete('dictionary');
    }

    public static function themes() {
        return array_diff(scandir('themes/'), Array(".", "..", ".svn"));
        //return @glob('themes/*', GLOB_NOSORT);
    }

    public static function templates() {
        return array_diff(scandir('templates/'), Array(".", "..", ".svn", "admin"));
        //return @glob('themes/*', GLOB_NOSORT);
    }

    public static function locales() {
        //'lang' is short language code [2 digit]
        return array(
            'index' => array(
                'ar-sa' => 'ar',
                'bg-bg' => 'bg',
                'cs-cz' => 'cs',
                'da-dk' => 'da',
                'de-de' => 'de',
                'el-gr' => 'el',
                'en-gb' => 'en',
                'en-us' => 'us',
                'es-es' => 'es',
                'fi-fi' => 'fi',
                'fr-fr' => 'fr',
                'hr-hr' => 'hr',
                'hu-hu' => 'hu',
                'it-it' => 'it',
                'iw-il' => 'he',
                'ja-jp' => 'ja',
                'ko-kr' => 'ko',
                'nl-nl' => 'nl',
                'nn-no' => 'nn',
                'pl-pl' => 'pl',
                'pt-br' => 'br',
                'pt-pt' => 'pt',
                'ro-ro' => 'ro',
                'ru-ru' => 'ru',
                'sk-sk' => 'sk',
                'sv-se' => 'sv',
                'tr-tr' => 'tr',
                'zh-cn' => 'zh',
                'zh-tw' => 'tw'
            ),
            'locales' => array(
                'ar-sa' => array('locale' => 'ar_SA', 'name' => 'Arabic', 'original' => 'عربي'),
                'bg-bg' => array('locale' => 'bg_BG', 'name' => 'Bulgarian', 'original' => 'Български'),
                'cs-cz' => array('locale' => 'cs_CZ', 'name' => 'Czech', 'original' => 'Čeština'),
                'da-dk' => array('locale' => 'da_DK', 'name' => 'Danish', 'original' => 'Dansk'),
                'de-de' => array('locale' => 'de_DE', 'name' => 'German', 'original' => 'Deutsch'),
                'el-gr' => array('locale' => 'el_GR', 'name' => 'Greek', 'original' => 'Ελληνικά'),
                'en-gb' => array('locale' => 'en_GB', 'name' => 'English (UK)', 'original' => 'English (British)'),
                'en-us' => array('locale' => 'en_US', 'name' => 'English (US)', 'original' => 'English (US)'),
                'es-es' => array('locale' => 'es_ES', 'name' => 'Spanish', 'original' => 'Español'),
                'fi-fi' => array('locale' => 'fi_FI', 'name' => 'Finnish', 'original' => 'suomi'),
                'fr-fr' => array('locale' => 'fr_FR', 'name' => 'French', 'original' => 'Français'),
                'hr-hr' => array('locale' => 'hr_HR', 'name' => 'Croatian', 'original' => 'Hrvatski'),
                'hu-hu' => array('locale' => 'hu_HU', 'name' => 'Hungarian', 'original' => 'Magyar'),
                'it-it' => array('locale' => 'it_IT', 'name' => 'Italian', 'original' => 'Italiano'),
                'iw-il' => array('locale' => 'iw_IL', 'name' => 'Hebrew', 'original' => 'עברית'),
                'ja-jp' => array('locale' => 'ja_JP', 'name' => 'Japanese', 'original' => '日本語'),
                'ko-kr' => array('locale' => 'ko_KR', 'name' => 'Korean', 'original' => '한국어'),
                'nl-nl' => array('locale' => 'nl_NL', 'name' => 'Dutch', 'original' => 'Nederlands'),
                'nn-no' => array('locale' => 'nn_NO', 'name' => 'Norwegian', 'original' => 'Norsk nynorsk'),
                'pl-pl' => array('locale' => 'pl_PL', 'name' => 'Polish', 'original' => 'Polski'),
                'pt-br' => array('locale' => 'pt_BR', 'name' => 'Portuguese (Brazilian)', 'original' => 'Português (do Brasil)'),
                'pt-pt' => array('locale' => 'pt_PT', 'name' => 'Portuguese', 'original' => 'Português'),
                'ro-ro' => array('locale' => 'ro_RO', 'name' => 'Romanian', 'original' => 'română'),
                'ru-ru' => array('locale' => 'ru_RU', 'name' => 'Russian', 'original' => 'Русский'),
                'sk-sk' => array('locale' => 'sk_SK', 'name' => 'Slovak', 'original' => 'slovenčina'),
                'sv-se' => array('locale' => 'sv_SE', 'name' => 'Swedish', 'original' => 'Svenska'),
                'tr-tr' => array('locale' => 'tr_TR', 'name' => 'Turkish', 'original' => 'Türkçe'),
                'zh-cn' => array('locale' => 'zh_CN', 'name' => 'Chinese (China)', 'original' => '中文 (简体)'),
                'zh-tw' => array('locale' => 'zh_TW', 'name' => 'Chinese (Taiwan)', 'original' => '正體中文 (繁體)')
            )
        );
    }

    public static function locale($locale, $lang, $multilingual) {
        $locale = strtolower($locale);
        $locales = self::locales();
        $deflocale = array_search((DEFLANG || 'en'), $locales['index']);
        if ($lang)
            $locale = array_search($lang, $locales['index']);
        return strstr(SUPPORTEDLANGS, $locales['index'][$locale]) && $multilingual ? array($locales['locales'][$locale]['locale'], $locales['index'][$locale]) : array($locales['locales'][$deflocale]['locale'], $locales['index'][$deflocale]);
    }

    public static function languages($all = false) {
        $locales = self::locales();
        $supportedlangs = explode(',', SUPPORTEDLANGS);
        foreach ($locales['index'] as $locale => $code)
            if ($all || strstr(SUPPORTEDLANGS, $code))
                $languages[$code] = $locales['locales'][$locale]['original'];
        return $languages;
    }

    public static function browser() {
        $browser = $_SERVER['HTTP_USER_AGENT'];
        preg_match('/(WebKit|Presto|Trident|Gecko)\//', $browser, $browser);
        return $browser[1] ? strtolower($browser[1]) : 'trident';
    }

    public static function css($get) {
        $browser = self::browser();
        // SPLIT GET DATA
        list($theme, $template, $admintemplate) = preg_split('/,/', $get, 3);
        $info = "/*\n"
                . "THEME         : " . $theme . "\n"
                . "TEMPLATE      : " . $template . "\n"
                . "ADMIN TEMPLATE: " . $admintemplate . "\n"
                . "*/\n";
        if ($theme) {
            // THEME CSS FILES
            $styles .= file_get_contents('themes/' . $theme . '/css/'
                            . ($template == 'admin' ? 'admin' : 'default' )
                            . '.css') . "\n";
            // BROWSER HACK
            $styles .= @ file_get_contents('themes/' . $theme . '/browsers/' . $browser . '.css');
        }
        // TEMPLATES CSS FILES
        $styles .= @ file_get_contents('templates/' . $template . '/default.css');
        if ($admintemplate)
            $styles .= @ file_get_contents('templates/' . $admintemplate . '/admin.css');
        if (DEVELOPMENT)
            return $info . $styles;
        else
            return $info . preg_replace(array(
                '!//[^\n\r]+!',
                '/[\r\n\t\s]+/s',
                '#/\*.*?\*/#',
                '/[\s]*([\{\};:])[\s]*/',
                '/^\s+/',
                '/}/'
                    ), array(
                '',
                ' ',
                '',
                '\1',
                '',
                "}\n",
                    ), $styles);
    }

    public static function js($get) {
        $browser = self::browser();
        // SPLIT GET DATA
        list($theme, $template, $admintemplate) = preg_split('/,/', $get, 3);
        $info = "/*\n"
                . "THEME         : " . $theme . "\n"
                . "TEMPLATE      : " . $template . "\n"
                . "ADMIN TEMPLATE: " . $admintemplate . "\n"
                . "*/\n";
        if ($theme) {
            // JS DEFINES
            $js = "var THEME = '$theme';\nvar TEMPLATE = '$template';\n";
            //// JS LANGUAGE
            $js .= 'var _lang = {"close":"' . u::translate('Close')
                    . '", "ok":"' . u::translate('Ok')
                    . '", "cancel":"' . u::translate('Cancel')
                    . '"};';
            // THEME JS FILES
            $js .= file_get_contents('themes/' . $theme . '/js/'
                            . ($template == 'admin' ? 'admin' : 'default' )
                            . '.js') . "\n";
            // BROWSER HACK
            $js .= @ file_get_contents('themes/' . $theme . '/browsers/' . $browser . '.js');
        }
        // TEMPLATES JS FILES
        $js .= @ file_get_contents('templates/' . $template . '/default.js');
        if ($admintemplate)
            $js .= @ file_get_contents('templates/' . $admintemplate . '/admin.js');
        if (DEVELOPMENT)
            return $info . $js;
        else
            return $info . preg_replace(array(
                '!//[^\n\r]+!',
                //'/[\r\n\t\s]+/s',
                '/^[\r\n\t\s]+/m',
                '#/\*.*?\*/#',
                '/[\s]*([\{\},;:\=\+\-\?\|\&])[\s]*/',
                '/^\s+/'
                    //'/\);/'
                    ), array(
                '',
                //' ',
                '',
                '',
                '\1',
                ''
                    //");\n"
                    ), $js);
    }

    public static function sitemap($pages) {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n"
                . "  xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\n"
                . "  xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
                . "<url>\n"
                . "  <loc>http://" . DOMAIN . "/</loc>\n"
                . "</url>\n";
        $sitemapindex = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . "<sitemapindex xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n"
                . "  xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd\"\n"
                . "  xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        foreach ($pages as $page => $name) {
            if (MULTILANGUAL)
                foreach (self::languages() as $lang => $i) {
                    $sitemap .= "<url>\n"
                            . "  <loc>http://" . DOMAIN . "/$lang/$page</loc>\n"
                            . "</url>\n";
                }
            else
                $sitemap .= "<url>\n"
                        . "  <loc>http://" . DOMAIN . "/" . DEFLANG . "/$page</loc>\n"
                        . "</url>\n";

            if (is_file($map = "data/$page/sitemap.xml"))
                $sitemapindex .= "<sitemap>\n"
                        . "  <loc>http://" . DOMAIN . "/$map</loc>\n"
                        . "  <lastmod>" . @date("Y-m-d") . "</lastmod>\n"
                        . "</sitemap>\n";
        }
        $sitemap .= '</urlset>';
        $sitemapindex .= '</sitemapindex>';
        file_put_contents('data/sitemap.xml', $sitemap);
        file_put_contents('data/sitemapindex.xml', $sitemapindex);
    }

    public static function captcha() {
        header('Content-type: image/png');
        header("Content-Disposition:inline ; filename=captcha.png");
        $length = 4;
        $captcha = imagecreatetruecolor(100, 45);
        $width = imagesx($captcha) - 4;
        $height = imagesy($captcha) - 4;
        $im = @imagecreate($width, $height);
        imagecolorallocate($im, 255, 255, 255);
        for ($i = 0; $i < ($width * $height) / 450; $i++) {
            imageline($im, rand(3, $width), rand(3, $height), rand(3, $width), rand(3, $height), imagecolorallocate($im, 205, 205, 222));
        }
        $possible = '23456789bcdfghjkmnpqrstvwxyz';
        $codeText = '';
        $i = 0;
        $left = 5;
        $font = 'themes/' . THEME . '/fonts/captcha.ttf';
        while ($i < $length) {
            $codeText .= $codeChar = (rand(0, 10) > 5) ? substr($possible, rand(0, strlen($possible) - 1), 1) : (substr($possible, rand(0, strlen($possible) - 1), 1));
            $fontSize = $height * (rand(40, 60) / 100);
            imagettftext($im, $fontSize, rand(-21, 21), $left, $height * .8, imagecolorallocate($im, 69, 69, 69), $font, $codeChar);
            $left += $fontSize;
            $i++;
        }
        imagejpeg($im);
        imagedestroy($im);
        $_SESSION['CAPTCHA'] = $codeText;
        echo $captcha;
    }

    public static function imageresize($image, $destination, $width = 0, $height = 0, $crop = false, $quality = 80) {

        $source = @imagecreatefromstring($image);
        $w = @imagesx($source);
        if (!$w) {
            // Find format
            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            // JPEG image
            $func = imagecreatefrom . (($ext == "jpg") ? 'jpeg' : $ext);
            $image = @$func($image);
        } else
            $image = $source;
        $dext = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        if ($image) {
            // Get dimensions
            $w = imagesx($image);
            $h = imagesy($image);
            if (($width && $w > $width) || ($height && $h > $height)) {
                $ratio = $w / $h;
                if (($ratio >= 1 || $height == 0) && !$crop) {
                    $new_height = $width / $ratio;
                    $new_width = $width;
                } elseif ($crop && $ratio <= ($width / $height)) {
                    $new_height = $width / $ratio;
                    $new_width = $width;
                } else {
                    $new_width = $height * $ratio;
                    $new_height = $height;
                }
            } else {
                $new_width = $w;
                $new_height = $h;
            }
            $x_mid = $new_width * .5;  //horizontal middle
            $y_mid = $new_height * .5; //vertical middle
            // Resample
            $new = imagecreatetruecolor(round($new_width), round($new_height));
            imagecopyresampled($new, $image, 0, 0, 0, 0, $new_width, $new_height, $w, $h);

            // Crop
            if ($crop) {
                $crop = imagecreatetruecolor($width ? $width : $new_width, $height ? $height : $new_height);
                imagecopyresampled($crop, $new, 0, 0, ($x_mid - ($width * .5)), 0, $width, $height, $width, $height);
                //($y_mid - ($height * .5))
            }

            // Output
            // Enable interlancing [for progressive JPEG]
            imageinterlace($crop ? $crop : $new, true);

            if ($dext == '') {
                $dext = $ext;
                $destination .= '.' . $ext;
            }
            switch ($dext) {
                case 'jpeg':
                case 'jpg':
                    imagejpeg($crop ? $crop : $new, $destination, $quality);
                    break;
                case 'png':
                    $pngQuality = ($quality - 100) / 11.111111;
                    $pngQuality = round(abs($pngQuality));
                    imagepng($crop ? $crop : $new, $destination, $pngQuality);
                    break;
                case 'gif':
                    imagepng($crop ? $crop : $new, $destination);
                    break;
            }
            @imagedestroy($image);
            @imagedestroy($new);
            @imagedestroy($crop);
        }
    }

    public static function email($sender, $recipient, $subject, $message) {
        $textmessage = strip_tags(str_replace(array('<br>', '<br/>', '<br />'), array("\n\r", "\n\r", "\n\r"), $message));
        $template = file_get_contents('themes/' . THEME . '/email.html');
        $message = str_replace(array(
            '[[TITLE]]',
            '[[DOMAIN]]',
            '[[MESSAGE]]'
                ), array(
            TITLE,
            DOMAIN,
            $message,
                ), $template);
        $boundary = sha1(time() . DOMAIN);
        $headers = ""
                . 'From: ' . $sender . "\n"
                . 'MIME-Version: 1.0' . "\n"
                . 'Content-Type: multipart/alternative;' . "\n"
                . '	boundary="' . $boundary . '"'
                . "\n";
        $message = "\n"
                . '--' . $boundary . "\n"
                . 'Content-Type: text/plain; charset = "utf-8"' . "\n"
                . 'Content-Transfer-Encoding: 8bit' . "\n"
                . $textmessage . "\n"
                . '--' . $boundary . "\n"
                . 'Content-Type: text/html; charset = "utf-8"' . "\n"
                . 'Content-Transfer-Encoding: 8bit' . "\n"
                . $message . "\n"
                . '--' . $boundary . '--' . "\n"
                . "\n";
        return mail($recipient, $subject, $message, $headers);
    }

    public static function result($status, $message = null, $fields = null, $redirect = null, $js = true, $format = FORMAT) {
        $statuses = array(200 => u::translate('Successfully'), 400 => u::translate('Alert'), 600 => u::translate('Error'), 800 => u::translate('System Error'));
        $data = new stdClass();
        //if(!$js) header('location: http://'.DOMAIN.$redirect);
        $data->code = $status;
        $data->status = $statuses[$status];
        $data->message = $message;
        $data->fields = $fields;
        $data->redirect = $redirect;
        return $data;
    }

    public static function initialize() {
        /*if (DOMAIN && (DOMAIN != $_SERVER['HTTP_HOST'] && COOKIELESS != $_SERVER['HTTP_HOST'])) {
            header('location: http://' . DOMAIN);
            exit;
        }*/
        $headers = getallheaders();
        define('POST', count($_POST));
        define('FILE', (count($_FILES) || $headers['x-file-name']) ? 1 : 0);
        define('CAPTCHA', $_SESSION['CAPTCHA']);
        define('ACCESS', $_SESSION['ACCESS']);
        // THEME SESSION for THEME PREVIEW
        $_SESSION['THEME'] = $_GET['THEME'] ? $_GET['THEME'] : ($_SESSION['THEME'] ? $_SESSION['THEME'] : (DEFTHEME || 'default'));
        define('THEME', $_SESSION['THEME']);
        define('FORMAT', $_GET['FORMAT'] ? $_GET['FORMAT'] : 'html');
        // LANGUAGES / LOCALES
        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5);
        $lang = $_REQUEST['LANG'] ? $_REQUEST['LANG'] : $_SESSION['LANG'];
        list($locale, $lang) = self::locale($locale, $lang, MULTILINGUAL);
        define('LANG', $_SESSION['LANG'] = $lang);
        define('LOCALE', $locale);
        define('BROWSER', self::browser());
        // GET PROJECT INFO
        $project = @self::get('data/project');
        // supported language list
        $project->languages = self::languages();
        define('PAGE', $_GET['PAGE'] ? $_GET['PAGE'] : (DEFPAGE || 'home'));
        define('PARAMS', $_GET['PARAMS'] ? $_GET['PARAMS'] : 'default');
        return $project;
    }

    public static function page($project) {
        $lang = LANG;
        $deflang = DEFLANG;
        $page = @self::get('data/' . PAGE . '/content');
        define('TEMPLATE', $_GET['TEMPLATE'] ? $_GET['TEMPLATE'] : ($page->template ? $page->template : PAGE));
        include 'templates/' . TEMPLATE . '/default.php';
        $project->title->$lang = $project->title->$lang ? $project->title->$lang : $project->title->$deflang;
        define('TITLE', $project->title->$lang . ($page->$lang->title ? ' / ' . $page->$lang->title : ''));
        define('DESCRIPTION', $page->$lang->description ? $page->$lang->description : $project->description->$lang);
        define('KEYWORDS', $page->$lang->keywords ? $page->$lang->keywords : $project->keywords->$lang);
        define('COPYRIGHT', $project->copyright->$lang);
        define('CANONICALLINK', 'http://' . DOMAIN . '/' . PAGE
                . (PARAMS && PARAMS != 'default' ? '/' . PARAMS : ''));
        return $page;
    }

    public static function template($page = null) {
        $lang = LANG;
        $template = 'templates/' . TEMPLATE . '/default.html';
        if (is_file($template)) {
            ob_start();
            include $template;
            $contents = ob_get_contents();
            ob_end_clean();
            return $contents;
        }
        return false;
    }

    public static function theme_engine($matches) {
        $lang = LANG;
        switch ($matches[1]) {
            case 'nav':
                $project = @self::get('data/project');
                if ($project->pages)
                    foreach ($project->pages as $link => $page)
                        if ($page->$lang)
                            $r .= str_replace(array(
                                '[[link]]',
                                '[[selected]]',
                                '[[name]]'), array(
                                $link,
                                ($link == PAGE ? 'selected' : ''),
                                $page->$lang
                                    ), $matches[2]);
                break;
            case 'languages':
                if (MULTILINGUAL) {
                    $languages = self::languages();
                    $page = substr($_SERVER['REQUEST_URI'], 3);
                    if ($languages)
                        foreach ($languages as $code => $name)
                            $r .= str_replace(array(
                                '[[link]]',
                                '[[selected]]',
                                '[[code]]',
                                '[[name]]'), array(
                                $code . $page,
                                ($code == LANG ? 'selected' : ''),
                                $code,
                                $name
                                    ), $matches[2]);
                }
                break;
            default:
                $admin = (TEMPLATE == 'admin');
                $analytics = ANALYTICS;
                if ($$matches[1])
                    $r = str_replace('[[else]]', '', $matches[2]);
                else
                    $r = $matches[5];
                break;
        }

        return $r; //'||thebele'.print_r($matches,1).'||';
    }

    public static function theme($project) {
        //print_r($project); exit;
        if (is_file('themes/' . THEME . '/default.html')) {
            // for admin
            $template = (!$project->page->page->template || TEMPLATE == $project->page->page->template ? '' : ',' . $project->page->page->template);
            $theme = file_get_contents('themes/' . THEME . '/default.html');
            $theme = str_replace(array(
                '[[AJAX]]',
                '[[TITLE]]',
                '[[DESCRIPTION]]',
                '[[KEYWORDS]]',
                '[[ANALYTICS]]',
                '[[ALEXA]]',
                '[[WEBMASTER]]',
                '[[SITEEXPLORER]]',
                '[[GEOPLACENAME]]',
                '[[GEOREGION]]',
                '[[GEOLATITUDE]]',
                '[[GEOLONGITUDE]]',
                '[[DOMAIN]]',
                '[[COOKIELESS]]',
                '[[THEME]]',
                '[[JS]]',
                '[[CSS]]',
                '[[LANG]]',
                '[[PAGE]]',
                '[[PAGECONTENT]]',
                '[[COPYRIGHT]]',
                '[[CANONICALLINK]]',
                '[[REQUESTURI]]'
                    ), array(
                AJAX,
                TITLE,
                DESCRIPTION,
                KEYWORDS,
                ANALYTICS,
                ALEXA,
                WEBMASTER,
                SITEEXPLORER,
                GEOPLACENAME,
                GEOREGION,
                GEOLATITUDE,
                GEOLONGITUDE,
                DOMAIN,
                (COOKIELESS ? '//' . COOKIELESS : ''),
                THEME,
                THEME . ',' . TEMPLATE . $template,
                THEME . ',' . TEMPLATE . $template,
                LANG,
                PAGE,
                $project->template,
                COPYRIGHT,
                CANONICALLINK,
                ($_SERVER['REQUEST_URI'] != '/' ? $_SERVER['REQUEST_URI'] : DEFPAGE)
                    ), $theme);
            $theme = preg_replace_callback(
                    array(
                "/\[each\[([a-z]+)]](((?!\[\[endeach]]).)+)\[\[endeach]]/ms",
                "/\[if\[([A-z]+)]](((?!\[\[else]]).)+(\[\[else]]))?(((?!\[\[endif]]).)+)\[\[endif]]/ms"
                    //"/\[each\[([a-z]+)]]((?:[^[]|\[\[(?!endeach]]))+)\[\[endeach]]/ims",
                    //"/\[if\[([a-z]+)]]((?:[^[]|\[\[(?!endif]]))+)\[\[endif]]/ims"
                    ), "u::theme_engine", $theme);
            if (DEVELOPMENT)
                echo $theme;
            else
                echo preg_replace(array(
                    '/^[\r\n\t\s]+/m',
                    '#<\!--.*?\--\>#',
                    '/^\s+/'
                        ), array(
                    '',
                    '',
                    ''
                        ), $theme);
        } else {
            echo sprintf(u::translate('"%s" theme not found. %s click here%s to return to default theme.'), THEME, '<a href="/' . DEFTHEME . '.thm">', '</a>');
            exit;
        }
    }

}

?>
