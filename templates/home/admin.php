<?

$lang = LANG;
$template = $page->page->template;

switch ($params[2]) {
    default:
        header('location: /' . TEMPLATE . '/' . $params[0] . ',' . $params[1] . ',info');
        break;
// =============================================================================
// INFO
// =============================================================================
    case 'info':
        switch ($params[3]) {
            default:
                if (POST) {
                    // Object Reference
                    $o = & $page->page->$lang;
                    $o->title = $_POST['title'] ? $_POST['title'] : $o->title;
                    $o->description = $_POST['description'] ? $_POST['description'] : $o->description;
                    $o->keywords = $_POST['keywords'] ? $_POST['keywords'] : $o->keywords;
                    $o->content = isset($_POST['content']) ? $_POST['content'] : $o->content;
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', sprintf(_('"%s" page is updated!'), $params[1]));
                }
                break;
        }
        break;

// =============================================================================
// SLIDE
// =============================================================================
    case 'slide':
        switch ($params[3]) {
            default:
                $page->page = $page->page->$lang;
                $page->page->template = $template;
                $page->page->slide = addslashes(json_encode($page->page->slide));
                break;
            case 'upload':

                if (FILE) {
                    $mimetypes = array(
                        'image/bmp' => 'bmp',
                        'image/gif' => 'gif',
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/svg+xml' => 'svg',
                        'image/tiff' => 'tiff');

                    $ext = $mimetypes[$mimetype = mime_content_type($_FILES["slide"]["tmp_name"])];

                    if ($ext) {
                        // GENERATE NEW SLIDE ID
                        $ID = (@count($page->page->$lang->slide) + 1) . (microtime(1) * 10000);
                        // GENERATE TMP FILE
                        $page->page->$lang->slide->$ID->tmp = 'data/' . $params[1] . '/slide-m' . $ID . '.' . $ext;
                        // UPLOAD THE SLIDE IMAGE
                        u::upload($_FILES['slide']['tmp_name'], $page->page->$lang->slide->$ID->tmp);

                        $page->page->$lang->slide->$ID->thumb = 'data/' . $params[1] . '/slide-t' . $ID . '.jpg';
                        $page->page->$lang->slide->$ID->alt = $_FILES['slide']['name'];
                        $page->page->$lang->slide->$ID->src = 'data/' . $params[1] . '/slide-m' . $ID . '.jpg';

                        u::imageresize($page->page->$lang->slide->$ID->tmp, $page->page->$lang->slide->$ID->src, 940, 300, true);
                        u::imageresize($page->page->$lang->slide->$ID->src, $page->page->$lang->slide->$ID->thumb, 188, 60, true);

                        // Delete tmp file
                        if ($page->page->$lang->slide->$ID->tmp != $page->page->$lang->slide->$ID->src)
                            @unlink($page->page->$lang->slide->$ID->tmp);
                        // Clear tmp
                        unset($page->page->$lang->slide->$ID->tmp);
                        // SAVE CHANGES
                        u::set('data/' . $params[1] . '/content', $page->page);
                        // Set ID
                        $page->page->$lang->slide->$ID->ID = $ID;
                        // Uploaded media data
                        die(json_encode($page->page->$lang->slide->$ID));
                    } else
                        die(json_encode(array('error' => u::translate('Unupported mime type') . ': <b>' . $mimetype . '</b>')));
                }

                break;
            case 'update':
                foreach ($_POST['title'] as $k => $i) {
                    if ($page->page->$lang->slide->$k) {
                        $page->page->$lang->slide->$k->alt = $_POST['title'][$k];
                        $page->page->$lang->slide->$k->link = $_POST['link'][$k];
                    }
                }
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200', u::translate('Slide data is updated!'));
                break;
                break;
            case 'sort':
                $object = & $page->page->$lang->slide;
                $dump = new stdClass();
                foreach (explode(',', $_POST['order']) as $i) {
                    $dump->$i = $object->$i;
                }
                $object = $dump;
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200', u::translate('"Items are ordered!'));
                break;
            case 'remove':
                @unlink('data/' . $params[1] . '/slide-m' . $_POST['ID'] . '.jpg');
                @unlink('data/' . $params[1] . '/slide-t' . $_POST['ID'] . '.jpg');
                if ($_POST['ID'] && $page->page->$lang->slide->$_POST['ID']) {
                    unset($page->page->$lang->slide->$_POST['ID']);
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200');
                } else
                    $page = u::result('600', u::translate('Slide item not found!'));
                break;
            case 'removeall':
                foreach ($page->page->$lang->slide as $k => $i) {
                    @unlink('data/' . $params[1] . '/slide-m' . $k . '.jpg');
                    @unlink('data/' . $params[1] . '/slide-t' . $k . '.jpg');
                }
                unset($page->page->$lang->slide);
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200');
                break;
        }

        break;
// =============================================================================
// CAROUSEL
// =============================================================================
    case 'carousel':
        switch ($params[3]) {
            default:
                $page->page->$lang->carousel->items = str_replace("'", "\'", json_encode($page->page->$lang->carousel->items));
                break;
            case 'add':
                $ID = (@count($page->page->$lang->carousel->items) + 1) . (microtime(1) * 10000);
                $page->page->$lang->carousel->items->$ID = null;
                // SAVE CHANGES
                u::set('data/' . $params[1] . '/content', $page->page);
                // Set ID
                $page->page->$lang->carousel->items->$ID->ID = $ID;
                $page = u::result('200', $page->page->$lang->carousel->items->$ID);
                break;
            case 'upload':
                if (FILE) {
                    $mimetypes = array(
                        'image/bmp' => 'bmp',
                        'image/gif' => 'gif',
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/svg+xml' => 'svg',
                        'image/tiff' => 'tiff');
                    $ID = key($_FILES);
                    $file = $_FILES[$ID];
                    $ext = $mimetypes[$mimetype = mime_content_type($file["tmp_name"])];
                    if ($ext) {
                        // GENERATE TMP FILE
                        $page->page->$lang->carousel->items->$ID->tmp = 'data/' . $params[1] . '/carousel-m' . $ID . '.' . $ext;
                        // UPLOAD THE carousel IMAGE
                        u::upload($file['tmp_name'], $page->page->$lang->carousel->items->$ID->tmp);

                        $page->page->$lang->carousel->items->$ID->thumb = 'data/' . $params[1] . '/carousel-t' . $ID . '.jpg';
                        //$page->page->$lang->carousel->$ID->title = $file['name'];
                        $page->page->$lang->carousel->items->$ID->src = 'data/' . $params[1] . '/carousel-m' . $ID . '.jpg';

                        u::imageresize($page->page->$lang->carousel->items->$ID->tmp, $page->page->$lang->carousel->items->$ID->src, 400, 0);
                        u::imageresize($page->page->$lang->carousel->items->$ID->src, $page->page->$lang->carousel->items->$ID->thumb, 200, 150, true);

                        // Delete tmp file
                        if ($page->page->$lang->carousel->items->$ID->tmp != $page->page->$lang->carousel->items->$ID->src)
                            @unlink($page->page->$lang->carousel->$ID->tmp);
                        // Clear tmp
                        unset($page->page->$lang->carousel->items->$ID->tmp);
                        // SAVE CHANGES
                        u::set('data/' . $params[1] . '/content', $page->page);
                        // Set ID
                        $page->page->$lang->carousel->items->$ID->ID = $ID;
                        // Uploaded media data
                        die(json_encode($page->page->$lang->carousel->items->$ID));
                    } else
                        die(json_encode(array('error' => u::translate('Unupported mime type') . ': <b>' . $mimetype . '</b>')));
                }
                break;
            case 'update':
                $page->page->$lang->carousel->title = $_POST['title']['main'];
                foreach ($_POST['title'] as $k => $i) {
                    if ($page->page->$lang->carousel->items->$k) {
                        $page->page->$lang->carousel->items->$k->title = $_POST['title'][$k];
                        $page->page->$lang->carousel->items->$k->link = $_POST['link'][$k];
                        $page->page->$lang->carousel->items->$k->content = $_POST['content'][$k];
                    }
                }
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200', u::translate('carousel data is updated!'));
                break;
            case 'sort':
                $object = & $page->page->$lang->carousel;
                $dump = new stdClass();
                foreach (explode(',', $_POST['order']) as $i) {
                    $dump->$i = $object->$i;
                }
                $object = $dump;
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200', u::translate('"Items are ordered!'));
                break;
            case 'remove':
                @unlink('data/' . $params[1] . '/carousel-m' . $_POST['ID'] . '.jpg');
                @unlink('data/' . $params[1] . '/carousel-t' . $_POST['ID'] . '.jpg');
                if ($_POST['ID'] && $page->page->$lang->carousel->items->$_POST['ID']) {
                    unset($page->page->$lang->carousel->items->$_POST['ID']);
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200');
                } else
                    $page = u::result('600', u::translate('carousel item not found!'));
                break;
            case 'removeall':
                foreach ($page->page->$lang->carousel->items as $k => $i) {
                    @unlink('data/' . $params[1] . '/carousel-m' . $k . '.jpg');
                    @unlink('data/' . $params[1] . '/carousel-t' . $k . '.jpg');
                }
                unset($page->page->$lang->carousel);
                u::set('data/' . $params[1] . '/content', $page->page);
                $page = u::result('200');
                break;
        }
        break;
}
/*
  if (!POST) {

  } elseif (!FILE) {
  switch ($params[2]) {
  // MEDIA ALT
  case 'alt':
  if ($_POST['ID'] && $page->page->media->$_POST['ID']) {
  $page->page->media->$_POST['ID']->alt = $_POST['value'];
  u::set('data/' . $params[1] . '/content', $page->page);
  $page = u::result('200', $_POST);
  } else
  $page = u::result('600', u::translate('Media item not found!'));
  break;
  // MEDIA REMOVE
  case 'removeMedia':
  @unlink('data/' . $params[1] . '/m' . $_POST['ID'] . '.jpg');
  @unlink('data/' . $params[1] . '/t' . $_POST['ID'] . '.jpg');
  if ($_POST['ID'] && $page->page->media->$_POST['ID']) {
  unset($page->page->media->$_POST['ID']);
  u::set('data/' . $params[1] . '/content', $page->page);
  $page = u::result('200');
  } else
  $page = u::result('600', u::translate('Media item not found!'));
  break;
  // CONTENT SAVE
  default:
  $page->page->$lang->title = $_POST['title'] ? $_POST['title'] : $page->page->$lang->title;
  $page->page->$lang->description = $_POST['description'] ? $_POST['description'] : $page->page->$lang->description;
  $page->page->$lang->keywords = $_POST['keywords'] ? $_POST['keywords'] : $page->page->$lang->keywords;
  $page->page->$lang->content = $_POST['content'] ? $_POST['content'] : $page->page->$lang->content;
  u::set('data/' . $params[1] . '/content', $page->page);
  $page = u::result('200', sprintf(_('"%s" subpage is saved!'), $params[1]));
  break;
  }
  } else {

  $ext = '.' . end(explode(".", strtolower($_FILES['Filedata']['name'])));
  // MEDIA ID
  $ID = (@count($page->page->media) + 1) . (microtime(1) * 10000);
  // TMP FILE
  $page->page->media->$ID->tmp = 'data/' . $params[1] . '/m' . $ID . $ext;
  // MEDIA UPLOAD
  u::upload($_FILES['Filedata']['tmp_name'], $page->page->media->$ID->tmp);

  switch ($ext) {
  case '.jpeg':
  case '.jpg':
  case '.gif':
  case '.png':

  //if(!$page->page->media) $page->page->media = new stdClass();
  $page->page->media->$ID->type = 'image';
  $page->page->media->$ID->thumb = 'data/' . $params[1] . '/t' . $ID . '.jpg';
  $page->page->media->$ID->alt = $_FILES['Filedata']['name'];
  $page->page->media->$ID->src = 'data/' . $params[1] . '/m' . $ID . '.jpg';

  u::imageresize($page->page->media->$ID->tmp, $page->page->media->$ID->src, 905);
  u::imageresize($page->page->media->$ID->src, $page->page->media->$ID->thumb, 160);

  break;
  case '.pdf':
  case '.doc':
  case '.xls':
  case '.ppt':
  case '.odt':
  case '.ods':
  case '.odp':
  case '.psd':
  case '.fla':
  case '.zip':
  case '.gz':

  $page->page->media->$ID->type = $ext;
  //$page->page->media->$ID->thumb = 'data/'.$params[1].'/t'.$ID.'.jpg';
  //$page->page->media->$ID->thumb = 'data/'.$params[1].'/t'.$ID.'.jpg';
  $page->page->media->$ID->alt = $_FILES['Filedata']['name'];
  $page->page->media->$ID->src = 'data/' . $params[1] . '/m' . $ID . $ext;
  //copy($page->page->media->$ID->tmp, $page->page->media->$ID->src);

  break;
  /* case 'mp3':
  case 'acc':
  case 'ogg':
  case 'js': */
/*
  case '.mp4':
  case '.f4v':
  case '.flv':

  $page->page->media->$ID->type = 'video';
  //$page->page->media->$ID->thumb = 'data/'.$params[1].'/t'.$ID.'.jpg';
  $page->page->media->$ID->alt = $_FILES['Filedata']['name'];
  $page->page->media->$ID->src = 'data/' . $params[1] . '/m' . $ID . $ext;
  //copy($page->page->media->$ID->tmp, $page->page->media->$ID->src);

  break;
  case '.swf':

  $page->page->media->$ID->type = 'flash';
  //$page->page->media->$ID->thumb = 'data/'.$params[1].'/t'.$ID.'.jpg';
  $page->page->media->$ID->alt = $_FILES['Filedata']['name'];
  $page->page->media->$ID->src = 'data/' . $params[1] . '/m' . $ID . $ext;
  //copy($page->page->media->$ID->tmp, $page->page->media->$ID->src);

  break;
  }

  // Delete tmp file
  if ($page->page->media->$ID->tmp != $page->page->media->$ID->src)
  @unlink($page->page->media->$ID->tmp);
  // Clear tmp
  unset($page->page->media->$ID->tmp);
  // SAVE CHANGES
  u::set('data/' . $params[1] . '/content', $page->page);
  // Set ID
  $page->page->media->$ID->ID = $ID;
  // Uploaded media data
  die(json_encode($page->page->media->$ID));
  }
 * */
?>