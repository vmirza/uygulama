<?

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
                    $page->page->content = htmlentities($page->page->content, null, 'UTF-8');
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
                        $o->$ID->tmp = 'data/' . $params[1] . '/m' . $ID . '.' . $ext;
                        // UPLOAD THE IMAGE
                        u::upload($file['tmp_name'], $o->$ID->tmp);

                        $o->$ID->thumb = 'data/' . $params[1] . '/t' . $ID . '.jpg';
                        $o->$ID->src = 'data/' . $params[1] . '/m' . $ID . '.jpg';
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
// SUBPAGES
// =============================================================================
    case 'subpages':
        switch ($params[3]) {
            default:
                if ($page->page->subpages)
                    foreach ($page->page->subpages as &$i)
                        $i = $i->$lang;
                $page->page->subpages = addslashes(json_encode($page->page->subpages));
                break;
            case 'add':
                if (POST) {
                    // Object Reference
                    $o = & $page->page->subpages;
                    $urn = strtolower($_POST['urn']);
                    if ($o)
                        foreach ($o as $link => $i) {
                            if ($urn == $link)
                                $duplicate = true;
                        }
                    if ($duplicate)
                        $page = u::result('400', sprintf(u::translate('"%s" subpage is already created!'), $urn), 'title');
                    elseif ($urn) {
                        $o->$urn = new stdClass();
                        $o->$urn->$lang->title = $_POST['title'];
                        u::set('data/' . $params[1] . '/content', $page->page);
                        $o->$urn->$lang->ID = $urn;
                        die(json_encode($o->$urn->$lang));
                    } else {
                        $page = u::result('400', u::translate('Please, write a correct title.'), 'title');
                    }
                }
                break;
            case 'update':
                if (POST) {
                    if (is_array($_POST['title'])) {
                        foreach ($_POST['title'] as $k => $i)
                            if ($page->page->subpages->$k)
                                $page->page->subpages->$k->$lang->title = $_POST['title'][$k];
                    }
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('titles are updated!'));
                }
                break;
            case 'sort':
                if (POST) {
                    // Object Reference
                    $o = & $page->page->subpages;
                    $dump = new stdClass();
                    foreach (explode(',', $_POST['order']) as $i) {
                        $dump->$i = $o->$i;
                    }
                    $o = $dump;
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', sprintf(u::translate('"%s" subpage is ordered!'), $params[2]));
                }
                break;
            case 'remove':
                if (($ID = $_POST['ID'])) {
                    // Object Reference
                    @$o = & $page->page->subpages;
                    if ($o->$ID)
                        foreach ($o->$ID->media as $i) {
                            @unlink($i->thumb);
                            @unlink($i->src);
                        }
                    unset($o->$ID);
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('Item is removed!'));
                }
                break;
            case 'removeall':
                if (POST) {
                    // Object Reference
                    @$o = & $page->page->subpages;
                    foreach ($o as $k => &$i) {
                        if ($i->media)
                            foreach ($i->media as $m) {
                                @unlink($m->thumb);
                                @unlink($m->src);
                            }
                    }
                    $o = null;
                    u::set('data/' . $params[1] . '/content', $page->page);
                    $page = u::result('200', u::translate('All items are removed!'));
                }
                break;
        }
        break;
// =============================================================================
// SUBPAGE
// =============================================================================
    case 'subpage':
        $urn = $params[3];
        switch ($params[4]) {
            default:
                if ($page->page->subpages->$urn) {
                    $page->page->subpage = $page->page->subpages->$urn->$lang;
                    $page->page->subpage->urn = $urn;
                    $page->page->subpage->content = htmlentities($page->page->subpage->content, null, 'UTF-8');
                    $page->page->subpage->media = addslashes(json_encode($page->page->subpage->media));
                }
                break;
            case 'update':
                if (POST && $urn) {
                    // Object Reference
                    $o = & $page->page->subpages->$urn->$lang;
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
                    $page = u::result('200', u::translate('"Items are ordered!'));
                }
                break;
            case 'upload':
                if (FILE) {
                    // Object Reference
                    $o = & $page->page->subpages->$urn->$lang->media;
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
                    $o = & $page->page->subpages->$urn->$lang->media;
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
                    @$o = & $page->page->subpages->$urn->$lang->media;
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
  $subpage = strtolower($params[2]);
  if($subpage) $page->subpage = $page->page->subpages->$subpage;
  } elseif(!FILE) {
  switch ($params[2]){
  case 'add':
  $urn = strtolower($_POST['urn']);
  if($page->page->subpages)
  foreach ($page->page->subpages as $link=>$i){
  if($urn == $link) $duplicate = true;
  }
  if($duplicate)
  $page = u::result('400',sprintf(u::translate('"%s" subpage link is used!'),$urn));
  else {
  $page->page->subpages->$urn = new stdClass();
  $page->page->subpages->$urn->$lang->title = $_POST['title'];
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200',sprintf(u::translate('"%s" subpage is created!'),$_POST['title']));
  }
  break;

  case 'del':
  if($page->page->subpages->$_POST['urn']->media)
  foreach ($page->page->subpages->$_POST['urn']->media as $i){
  @unlink($i->thumb);
  @unlink($i->src);
  }
  unset($page->page->subpages->$_POST['urn']);
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200',sprintf(u::translate('"%s" subpage is deleted!'),$_POST['urn']));
  break;
  default:
  $lang = LANG;
  $subpage = strtolower($params[2]);
  // CONTENT MEDIA MANIPULATION
  switch ($params[3]){
  // MEDIA ALT
  case 'alt':
  if($_POST['ID'] && $page->page->subpages->$subpage->media->$_POST['ID']) {
  $page->page->subpages->$subpage->media->$_POST['ID']->alt = $_POST['value'];
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200',$_POST);
  } else $page = u::result('600',u::translate('Media item not found!'));
  break;
  // MEDIA REMOVE
  case 'removeMedia':
  @unlink('data/'.$params[1].'/m'.$_POST['ID'].'.jpg');
  @unlink('data/'.$params[1].'/t'.$_POST['ID'].'.jpg');
  if($_POST['ID'] && $page->page->subpages->$subpage->media->$_POST['ID']) {
  unset($page->page->subpages->$subpage->media->$_POST['ID']);
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200');
  } else $page = u::result('600',u::translate('Media item not found!'));
  break;
  };
  // Content data
  if(!$params[3]){
  $page->page->subpages->$subpage->$lang->title 		= $_POST['title']
  ? stripslashes($_POST['title']) 		: $page->page->subpages->$subpage->$lang->title;
  $page->page->subpages->$subpage->$lang->description = $_POST['description']
  ? stripslashes($_POST['description']) 	: $page->page->subpages->$subpage->$lang->description;
  $page->page->subpages->$subpage->$lang->keywords 	= $_POST['keywords']
  ? stripslashes($_POST['keywords']) 		: $page->page->subpages->$subpage->$lang->keywords;
  $page->page->subpages->$subpage->$lang->content 	= $_POST['content']
  ? stripslashes($_POST['content']) 		: $page->page->subpages->$subpage->$lang->content;
  // Save content
  u::set('data/'.$params[1].'/content', $page->page);
  $page = u::result('200',sprintf(u::translate('"%s" subpage is saved!'),$params[1]));
  }
  break;
  }
  } else {
  $subpage = strtolower($params[2]);
  $ext = '.'.end(explode(".",strtolower($_FILES['Filedata']['name'])));
  // MEDIA ID
  $ID = (@count($page->page->subpages->$subpage->media) +1).(microtime(1)*10000);
  //if(!$page->page->media) $page->page->media = new stdClass();
  $page->page->subpages->$subpage->media->$ID->type 	='image';
  $page->page->subpages->$subpage->media->$ID->thumb 	= 'data/'.$params[1].'/t'.$ID.'.jpg';
  $page->page->subpages->$subpage->media->$ID->alt 	= $_FILES['Filedata']['name'];
  $page->page->subpages->$subpage->media->$ID->src 	= 'data/'.$params[1].'/m'.$ID.'.jpg';
  $page->page->subpages->$subpage->media->$ID->tmp 	= 'data/'.$params[1].'/m'.$ID.$ext;
  // MEDIA UPLOAD
  u::upload($_FILES['Filedata']['tmp_name'], $page->page->subpages->$subpage->media->$ID->tmp);
  // if type == image
  u::imageresize($page->page->subpages->$subpage->media->$ID->tmp, $page->page->subpages->$subpage->media->$ID->src, 600, 600);
  u::imageresize($page->page->subpages->$subpage->media->$ID->src, $page->page->subpages->$subpage->media->$ID->thumb, 160);
  // Delete tmp file
  if($page->page->subpages->$subpage->media->$ID->tmp != $page->page->subpages->$subpage->media->$ID->src)
  @unlink($page->page->subpages->$subpage->media->$ID->tmp);
  // Clear tmp
  unset($page->page->subpages->$subpage->media->$ID->tmp);
  // SAVE CHANGES
  u::set('data/'.$params[1].'/content', $page->page);
  // Set ID
  $page->page->subpages->$subpage->media->$ID->ID = $ID;
  // Uploaded media data
  echo json_encode($page->page->subpages->$subpage->media->$ID); exit;

  //u::set('data/files',$_FILES);
  /*
  if(u::upload($_FILES['Filedata']['tmp_name'], 'data/'.$params[1].'/'.$params[2].'.jpg')){
  u::imageresize('data/'.$params[1].'/'.$params[2].'.jpg', 'data/'.$params[1].'/'.$params[2].'.jpg', 600);
  $page = u::result('200',sprintf(u::translate('"%s" image is uploaded!'),$params[1]));
  } else $page = u::result('400',sprintf(u::translate('"%s" image is not uploaded!'),$params[1]));

  }
 */
?>