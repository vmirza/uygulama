<?php

$template = $page->page->template;
switch ($params[2]) {
    default:
        header('location: /' . TEMPLATE . '/' . $params[0] . ',' . $params[1] . ',content');
        break;
// =============================================================================
// CONTENT
// =============================================================================
    case 'content':
        switch ($params[3]) {
            default:
                if (!POST) {
                    $page->page = $page->page->$lang;
                    $page->page->template = $template;
                    $page->page->urn = $params[1];
                    $page->page->media = addslashes(json_encode($page->page->media));
                }
                break;
            case 'update':
                if (POST) {
                    // Object Reference
                    $o = & $page->page->$lang;
                    $o->title = $_POST['title'] ? $_POST['title'] : $o->title;
                    $o->description = $_POST['description'] ? $_POST['description'] : $o->description;
                    $o->keywords = $_POST['keywords'] ? $_POST['keywords'] : $o->keywords;
                    $o->content = $_POST['content'] ? $_POST['content'] : $o->content;
                    // Media
                    if (is_array($_POST['alt']))
                        foreach ($_POST['alt'] as $k => $i) {
                            if ($o->media->$k) {
                                $o->media->$k->alt = $_POST['alt'][$k];
                            }
                        }
                    // Save content
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('"The content is updated!'));
                }
                break;
            case 'upload':
                if (FILE) {
                    // Object Reference
                    $o = & $page->page->$lang->media;
                    $ID = key($_FILES);
                    $file = $_FILES[$ID];
                    // MEDIA ID
                    $ID = (@count($o) + 1) . (microtime(1) * 10000);
                    $mimetypes = array(
                        'image/bmp' => 'bmp',
                        'image/gif' => 'gif',
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/svg+xml' => 'svg',
                        'image/tiff' => 'tiff');
                    $ext = $mimetypes[$mimetype = mime_content_type($file["tmp_name"])];
                    if ($ext) {
                        // GENERATE TMP FILE
                        $o->$ID->tmp = 'data/' . $params[1] . '/' . $urn . '-m' . $ID . '.' . $ext;
                        // UPLOAD THE IMAGE
                        u::upload($file['tmp_name'], $o->$ID->tmp);

                        $o->$ID->thumb = 'data/' . $params[1] . '/' . $urn . '-t' . $ID . '.jpg';
                        $o->$ID->src = 'data/' . $params[1] . '/' . $urn . '-m' . $ID . '.jpg';
                        $o->$ID->alt = $file['name'];

                        u::imageresize($o->$ID->tmp, $o->$ID->src, 620);
                        u::imageresize($o->$ID->src, $o->$ID->thumb, 140, 100, true);

                        // Delete tmp file
                        if ($o->$ID->tmp != $o->$ID->src)
                            @unlink($o->$ID->tmp);
                        // Clear tmp
                        unset($o->$ID->tmp);
                        // SAVE CHANGES
                        u::set('data/' . $params[1] . '/content', $page->page);
                        // Set ID
                        $o->$ID->ID = $ID;
                        // Uploaded media data
                        die(json_encode($o->$ID));
                    } else
                        die(json_encode(array('error' => u::translate('Unsupported mime type') . ': <b>' . $mimetype . '</b>')));
                }
                break;
            case 'sort':
                if (POST) {
                    // Object Reference
                    $o = & $page->page->$lang->media;
                    $dump = new stdClass();
                    foreach (explode(',', $_POST['order']) as $i) {
                        $dump->$i = $o->$i;
                    }
                    $o = $dump;
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('"Items are ordered!'));
                }
                break;
            case 'remove':
                if (($ID = $_POST['ID'])) {
                    // Object Reference
                    @$o = & $page->page->$lang->media;
                    @unlink($o->$ID->thumb);
                    @unlink($o->$ID->src);
                    unset($o->$ID);
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('Item is removed!'));
                }
                break;
        }
        break;
// =============================================================================
// CONTACTFORM
// =============================================================================
    case 'contactform':
        switch ($params[3]) {
            default:
                break;
            case 'update':
                if (POST) {
                    // Object Reference
                    $o = & $page->page;
                    $o->recipient = $_POST['recipient'] ? $_POST['recipient'] : $o->recipient;
                    // Save content
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('"Recipient email is updated!'));
                }
                break;
        }
        break;
// =============================================================================
// MAP
// =============================================================================
    case 'map':
        switch ($params[3]) {
            default:
                break;
            case 'update':
                if (POST) {
                    // Object Reference
                    $o = & $page->page;
                    $o->layout = $_POST['layout'] ? $_POST['layout'] : $o->layout;
                    // Save content
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('"The content is updated!'));
                }
                break;
        }
        break;
// =============================================================================
// LAYOUT
// =============================================================================
    case 'layout':
        switch ($params[3]) {
            default:
                break;
            case 'update':
                if (POST) {
                    // Object Reference
                    $o = & $page->page;
                    $o->layout = $_POST['layout'] ? $_POST['layout'] : $o->layout;
                    // Save content
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('"The content is updated!'));
                }
                break;
        }
        break;
}
/*
  if(!POST) {
  } elseif(!FILE) {
  switch ($params[2]){
  // MEDIA ALT
  case 'alt':
  if($_POST['ID'] && $page->page->media->$_POST['ID']) {
  $page->page->media->$_POST['ID']->alt = $_POST['value'];
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200', $_POST);
  } else $page = u::result('600', u::translate('Media item not found!'));
  break;
  // MEDIA REMOVE
  case 'removeMedia':
  @unlink('data/'.$params[1].'/m'.$_POST['ID'].'.jpg');
  @unlink('data/'.$params[1].'/t'.$_POST['ID'].'.jpg');
  if($_POST['ID'] && $page->page->media->$_POST['ID']) {
  unset($page->page->media->$_POST['ID']);
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200');
  } else $page = u::result('600', u::translate('Media item not found!'));
  break;
  // CONTENT SAVE
  default:



  $lang = LANG;
  $page->page->$lang->title = $_POST['title'] ? $_POST['title'] : $page->page->$lang->title;
  $page->page->$lang->description = $_POST['description'] ? $_POST['description'] : $page->page->$lang->description;
  $page->page->$lang->keywords = $_POST['keywords'] ? $_POST['keywords'] : $page->page->$lang->keywords;
  $page->page->$lang->content = $_POST['content'] ? $_POST['content'] : $page->page->$lang->content;
  $page->page->recipient = $_POST['recipient'] ? $_POST['recipient'] : $page->page->recipient;
  $links = array();
  foreach ($_POST['link-title'] as $k => $i){
  if($_POST['link-url'][$k] && $_POST['link-url'][$k] != 'http://')
  $links[] = array(
  'title' => ($i?$i:$_POST['link-url'][$k]),
  'url' => $_POST['link-url'][$k]
  );
  }
  $page->page->$lang->links = $links;
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200', sprintf(_('"%s" subpage is saved!'), $params[1]));
  break;
  }
  } else {
  $ext = '.'.end(explode(".", strtolower($_FILES['Filedata']['name'])));
  // MEDIA ID
  $ID = (@count($page->page->media) +1).(microtime(1)*10000);
  //if(!$page->page->media) $page->page->media = new stdClass();
  $page->page->media->$ID->type = 'image';
  $page->page->media->$ID->thumb = 'data/'.$params[1].'/t'.$ID.'.jpg';
  $page->page->media->$ID->alt = $_FILES['Filedata']['name'];
  $page->page->media->$ID->src = 'data/'.$params[1].'/m'.$ID.'.jpg';
  $page->page->media->$ID->tmp = 'data/'.$params[1].'/m'.$ID.$ext;
  // MEDIA UPLOAD
  u::upload($_FILES['Filedata']['tmp_name'], $page->page->media->$ID->tmp);
  // if type == image
  u::imageresize($page->page->media->$ID->tmp, $page->page->media->$ID->src, 905);
  u::imageresize($page->page->media->$ID->src, $page->page->media->$ID->thumb, 160, 90, true);
  // Delete tmp file
  if($page->page->media->$ID->tmp != $page->page->media->$ID->src)
  @unlink($page->page->media->$ID->tmp);
  // Clear tmp
  unset($page->page->media->$ID->tmp);
  // SAVE CHANGES
  u::set('data/'.$params[1].'/content', $page->page);
  // Set ID
  $page->page->media->$ID->ID = $ID;
  // Uploaded media data
  echo json_encode($page->page->media->$ID);
  exit;
  }

 */
?>
