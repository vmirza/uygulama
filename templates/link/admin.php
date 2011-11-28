<?
if(POST) {
    $page->page->$lang->link = $_POST['link'];
    u::set('data/'.$params[1].'/content', $page->page);
    $page = u::result('200',sprintf(_('"%s" subpage is saved!'),$params[1]));
} else {
    $page->page->$lang->link = $page->page->$lang->link ? $page->page->$lang->link : 'http://';
}
?>