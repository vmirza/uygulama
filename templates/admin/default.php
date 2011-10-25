<?

$params = preg_split('/,/', PARAMS, 6);
if (ACCESS && ACCESS < 3 && !in_array($params[0], array('page', 'pages', 'logout')))
    $params[0] = 'pages';
if (!ACCESS && $params[0] != 'language')
    $params[0] = 'login';
switch ($params[0]) {

// LOGIN 
// =============================================================================
// =============================================================================
// =============================================================================

    case 'login':
        if ($_POST['username'] == ADMIN && sha1($_POST['password']) == APASSWORD) {
            $_SESSION['ACCESS'] = 3;
            $page = u::result('200', u::translate('You are being redirected...'), null, '/admin/options');
        } elseif (EDITORIAL && $_POST['username'] == EDITOR && sha1($_POST['password']) == EPASSWORD) {
            $_SESSION['ACCESS'] = 1;
            $page = u::result('200', u::translate('You are being redirected...'), null, '/admin/pages');
        } else {
            $page = u::result('400', u::translate('Wrong password or username'), array('username', 'password'));
        }
        break;

// SETUP | CONFIG
// OPTIONS | SEO | LANGUAGES | DEFPAGE =========================================

    default:
    case 'options':
    case 'languages':
    case 'theme':
    case 'defpage':
        $page->robots = @file_get_contents('data/robots.txt');
        $page->humans = @file_get_contents('data/humans.txt');
        if (POST) {
            $project = new stdClass(); //u::get('data/options');
            /* $dns = dns_get_record($_POST['domain'],DNS_A);
              if($dns[0]['ip'] != $_SERVER['SERVER_ADDR']) {
              $page = u::result('600',u::translate('A DNS record of the domain name does not match the IP address of the server'),array('domain'));
              } else { */
            $project->development = isset($_POST['development']) ? ($_POST['development'] ? 1 : 0) : DEVELOPMENT;
            $project->ajax = isset($_POST['ajax']) ? ($_POST['ajax'] ? 1 : 0) : AJAX;
            $project->editorial = isset($_POST['editorial']) ? ($_POST['editorial'] ? 1 : 0) : EDITORIAL;
            //$project->domain = $_POST['domain'] ? $_POST['domain'] : DOMAIN;
            $project->cookieless = isset($_POST['cookieless']) ? ($_POST['cookieless'] ? $_POST['cookieless'] : DOMAIN ) : (COOKIELESS ? COOKIELESS : $project->domain);
            $project->admin = $_POST['admin'] ? $_POST['admin'] : ADMIN;
            $project->editor = $_POST['editor'] ? $_POST['editor'] : EDITOR;
            if ($_POST['arepassword'] && $_POST['apassword'] != $_POST['arepassword']) {
                $page = u::result('400', u::translate('Admin password and confirmation do not match!'));
            } elseif ($_POST['erepassword'] && $_POST['epassword'] != $_POST['erepassword']) {
                $page = u::result('400', u::translate('Editor password and confirmation do not match!'));
            } else {
                $project->apassword = $_POST['apassword'] ? sha1($_POST['apassword']) : APASSWORD;
                $project->epassword = $_POST['epassword'] ? sha1($_POST['epassword']) : EPASSWORD;
                if (!$project->admin || !$project->apassword || ($project->editorial && (!$project->editor || !$project->epassword))) {
                    $page = u::result('400', u::translate('Admin fields must not be null!'));
                } else {
                    $project->theme = ($_SESSION['THEME'] = ($_POST['theme'] ? $_POST['theme'] : THEME));
                    $project->analytics = $_POST['analytics'] ? $_POST['analytics'] : ANALYTICS;
                    /*$project->alexa = isset($_POST['alexa']) ? $_POST['alexa'] : ALEXA;
                    $project->webmaster = isset($_POST['webmaster']) ? $_POST['webmaster'] : WEBMASTER;
                    $project->siteexplorer = isset($_POST['siteexplorer']) ? $_POST['siteexplorer'] : SITEEXPLORER;

                    $project->geoplacename = isset($_POST['geoplacename']) ? $_POST['geoplacename'] : GEOPLACENAME;
                    $project->georegion = isset($_POST['georegion']) ? $_POST['georegion'] : GEOREGION;
                    $project->geolatitude = isset($_POST['geolatitude']) ? $_POST['geolatitude'] : GEOLATITUDE;
                    $project->geolongitude = isset($_POST['geolongitude']) ? $_POST['geolongitude'] : GEOLONGITUDE;
                    */
                    $project->multilingual = isset($_POST['multilingual']) ? $_POST['multilingual'] : MULTILINGUAL;
                    $project->defaultlang = $_POST['defaultlang'] ? $_POST['defaultlang'] : DEFLANG;
                    if ($_POST['defaultlang']) {
                        $_SESSION['LANG'] = $project->defaultlang;
                    }
                    $supportedlangs = implode(',', $_POST['supportedlangs']);
                    $project->supportedlangs = $supportedlangs ? $supportedlangs : SUPPORTEDLANGS;
                    $project->defaultpage = $_POST['defaultpage'] ? $_POST['defaultpage'] : DEFPAGE;

                    $config = "<?php" . "\n"
                            . "define('DEVELOPMENT', $project->development);" . "\n"
                            . "define('AJAX', $project->ajax);" . "\n"
                            //. "define('DOMAIN', '$project->domain');" . "\n"
                            . "define('COOKIELESS', '$project->cookieless');" . "\n"
                            . "define('ADMIN', '$project->admin');" . "\n"
                            . "define('APASSWORD', '$project->apassword');" . "\n"
                            . "define('EDITORIAL', $project->editorial);" . "\n"
                            . "define('EDITOR', '$project->editor');" . "\n"
                            . "define('EPASSWORD', '$project->epassword');" . "\n"
                            . "define('DEFTHEME', '$project->theme');" . "\n"
                            . "define('ANALYTICS', '$project->analytics');" . "\n"
                            /*. "define('ALEXA', '$project->alexa');" . "\n"
                            . "define('WEBMASTER', '$project->webmaster');" . "\n"
                            . "define('SITEEXPLORER', '$project->siteexplorer');" . "\n"
                            . "define('GEOPLACENAME', '$project->geoplacename');" . "\n"
                            . "define('GEOREGION', '$project->georegion');" . "\n"
                            . "define('GEOLATITUDE', '$project->geolatitude');" . "\n"
                            . "define('GEOLONGITUDE', '$project->geolongitude');" . "\n"
                             * 
                             */
                            . "define('MULTILINGUAL', $project->multilingual);" . "\n"
                            . "define('SUPPORTEDLANGS', '$project->supportedlangs');" . "\n"
                            . "define('DEFLANG', '$project->defaultlang');" . "\n"
                            . "define('DEFPAGE', '$project->defaultpage');" . "\n"
                            . "?>";
                    file_put_contents('data/config.php', $config);

                    if ($_POST['robots'])
                        file_put_contents('data/robots.txt', $_POST['robots']);
                    if ($_POST['humans'])
                        file_put_contents('data/humans.txt', $_POST['humans']);

                    $page = u::result('200', u::translate('Options saved!'));
                }
            }
            // }
        }
        break;
// LANGUAGE 
// =============================================================================
// =============================================================================
// =============================================================================
    case 'language':
        $_SESSION['LANG'] = $_POST['lang'];
        $page = u::result('200');
        break;
// TRANSLATIONS 
// =============================================================================
// =============================================================================
// =============================================================================
    case 'translations':
        if (in_array($_POST['type'], array('lib', 'templates'))) {
            $path = $_POST['type'] == 'lib' ? 'lib/' : $_POST['type'] . '/' . $_POST['part'] . '/';
            if (u::set($path . 'dictionary/' . $lang, $_POST['word']))
                $page = u::result('200', u::translate('Dictionary saved!'));
            else
                $page = u::result('400', u::translate('Dictionary does not save! It can have not writing permission!'));
        } else
            $page = u::result('400', u::translate('Undefined type!'));
        break;
// THEMES 
// =============================================================================
// =============================================================================
// =============================================================================
    case 'themes':
        switch ($params[1]) {
            default:
                if (u::upload($_FILES['theme']['tmp_name'], 'themes/' . $_FILES['theme']['name'])) {
                    u::extract('themes/' . $_FILES['theme']['name'], 'themes/' . basename($_FILES['theme']['name'], '.zip'));
                    unlink('themes/' . $_FILES['theme']['name']);
                    $page = u::result('200', u::translate('Upload succesful!'));
                } else
                    $page = u::result('400', sprintf(u::translate('Upload failed! Please check %s folder writing permission.'), '<b>themes/</b>'));
                break;
// LOGO ========================================================================
            case 'logo':
                if (u::upload($_FILES['logo']['tmp_name'], 'data/logo.png')) {
                    $page = u::result('200', u::translate('Upload succesful!'));
                } else
                    $page = u::result('400', u::translate('Upload failed!'));
                break;
// FAVICON =====================================================================
            case 'favicon':
                if (u::upload($_FILES['favicon']['tmp_name'], 'data/favicon.ico')) {
                    $page = u::result('200', u::translate('Upload succesful!'));
                } else
                    $page = u::result('400', u::translate('Upload failed!'));
                break;
        }
        break;
// TEMPLATES 
// =============================================================================
// =============================================================================
// =============================================================================
    case 'templates':
        if (TEMPLATEUPLOAD)
            if (u::upload($_FILES['template']['tmp_name'], 'templates/' . $_FILES['template']['name'])) {
                u::extract('templates/' . $_FILES['template']['name'], 'templates/' . basename($_FILES['template']['name'], '.zip'));
                unlink('templates/' . $_FILES['template']['name']);
                $page = u::result('200', u::translate('Upload succesful!'));
            } else
                $page = u::result('400', sprintf(u::translate('Upload failed! Please check %s folder writing permission.'), '<b>templates/</b>'));
        else
            $page = u::result('600', u::translate('Template upload not allowed'));
        break;

// INFO ========================================================================

    case 'info':
        $lang = LANG;
        $project = new stdClass();
        $project = u::get('data/project');
        if (POST) {
            $project->title->$lang = $_POST['title'] ? $_POST['title'] : $project->$lang->title;
            //$project->description->$lang = $_POST['description'] ? $_POST['description'] : $project->$lang->description;
            //$project->keywords->$lang = $_POST['keywords'] ? $_POST['keywords'] : $project->$lang->keywords;
            $project->copyright->$lang = $_POST['copyright'] ? $_POST['copyright'] : $project->$lang->copyright;
            u::set('data/project', $project);
            $page = u::result('200', u::translate('Project info saved!'));
        } else
            $page = $project;
        break;

// PAGES
// =============================================================================
// =============================================================================
// =============================================================================
    case 'pages':
        $page = new stdClass();
        $page->navigator = u::get('data/project');
        if (POST) {
            $_POST['url'] = strtolower($_POST['url']);
            $project = u::get('data/project');
            switch ($params[1]) {
                case 'add':
                    foreach ($project->pages as $k => $i)
                        $pages[] = $k;
                    if (!preg_match('/([0-9a-z_-]+)/', $_POST['url'])) {
                        $page = u::result('600', u::translate('Url can only contain english characters, - _ and numbers!'), 'url');
                    } elseif (in_array($_POST['url'], $pages)) {
                        $page = u::result('600', u::translate('Page already exists!'), 'url');
                    } else {
                        $project->pages->$_POST['url'] = null;
                        u::set('data/project', $project);
                        $page = u::result('200', array('url'=>$_POST['url']));
                    }
                    break;
                case 'set':
                    foreach ($_POST['name'] as $k => $i) {
                        $project->pages->$k->$lang = $i;
                    }
                    u::set('data/project', $project);
                    u::sitemap($project->pages);
                    $page = u::result('200', u::translate('Navigator saved!'));
                    break;
                case 'del':
                    unset($project->pages->$_POST['url']);
                    u::rmdir('data/' . $_POST['url']);
                    u::set('data/project', $project);
                    u::sitemap($project->pages);
                    $page = u::result('200', u::translate('Page deleted.'));
                    break;
                case 'sort':
                    $pages = new stdClass();
                    foreach (explode(',', $_POST['order']) as $page)
                        $pages->$page = $project->pages->$page;
                    $project->pages = $pages;
                    unset($pages);
                    u::set('data/project', $project);
                    $page = u::result('200', u::translate('Navigator saved!'));
                    break;
            }
            unset($project);
        }
        break;

// TEMPLATE ====================================================================

    case 'template':
        $page = new stdClass();
        $page->template = $_POST['template'];
        @mkdir('data/' . $_POST['page']);
        u::set('data/' . $_POST['page'] . '/content', $page);
        $page = u::result('200', sprintf(u::translate('%s page saved!'), $_POST['page']));
        break;

// PAGE ========================================================================

    case 'page':
        $lang = LANG;
        $deflang = DEFLANG;
        $page = new stdClass();
        $page->page = u::get('data/' . $params[1] . '/content');
        include 'templates/' . $page->page->template . '/admin.php';
        break;

// LOGOUT ======================================================================

    case 'logout':
        $_SESSION['ACCESS'] = 0;
        unset($_SESSION);
        header('location: /admin');
        break;
}
?>