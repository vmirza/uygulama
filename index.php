<?php

// the control for COOKIELESS JS and CSS request!
if (@$_GET['FORMAT'] != 'js' && @$_GET['FORMAT'] != 'css') {
    header("Cache-control: private");
    if (isset($_GET['TOKEN']))
        session_id($_GET['TOKEN']);
    session_start();
    define('TOKEN', session_id());
}
// the option for security
define('TEMPLATEUPLOAD', true);
// get configurations
include 'data/config.php';
// get library
include 'lib/uygulama.php';
// detect request format
switch (@$_GET['FORMAT']) {
    case 'css':
        $m = @date('D, d M Y H:i:s e',THEMEMODIFIED);
        $e = @date('D, d M Y H:i:s e',THEMEMODIFIED+290304000);
        header('cache-control: public, max-age=290304000');
        header('last-modified: '.$m);
        header('expires: '.$e);
        header('Content-Type: text/css');
        echo u::css($_GET['GET']);
        break;
    case 'js':
        $m = @date('D, d M Y H:i:s e',THEMEMODIFIED);
        $e = @date('D, d M Y H:i:s e',THEMEMODIFIED+290304000);
        header('cache-control: public, max-age=290304000');
        header('last-modified: '.$m);
        header('expires: '.$e);
        header('Content-Type: text/javascript');
        echo u::js($_GET['GET']);
        break;
    case 'captcha':
        $project = u::initialize();
        u::captcha();
        break;
    case 'archive':
        u::download($_GET['GET']);
        break;
    default:
        $project = u::initialize();
        //die('<pre>'.print_r($_REQUEST,1).'</pre>');
        // SETUP ?
        if (!ADMIN && !APASSWORD && ($_GET['TEMPLATE'] != 'admin' || ($_GET['PARAMS'] != 'project,accounts' && $_GET['PARAMS'] != 'languages'))) {
            $_SESSION['ACCESS'] = 3;
            header('Location: /admin/project,accounts');
            exit();
        }
        $project->page = u::page($project);
        // JSON
        if (FORMAT == 'json') {
            header('content-type: application/json');
            echo json_encode($project->page);
            exit;
        }
        $project->template = u::template($project->page);
        // HTML PAGE for XHTTP REQUESTS
        if (FORMAT == 'page') {
            echo '<script>' . u::js(',' . $project->page->template) . '</script>';
            echo '<style>' . u::css(',' . $project->page->template) . '</style>';
            echo $project->template;
            exit;
        }
        // THEME
        u::theme($project);
        break;
}
?>
