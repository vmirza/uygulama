<?

if (!$page->$lang->description) {
    foreach ($project->languages as $k => $i) {
        if ($page->$k->content) {
            $t .= '<li><a href="/' . $k . '/' . PAGE . '">'
                    . $page->$k->description . ' - ( ' . $i . ' )</a></li>';
            $j .= $k;
        }
    }
    if ($j) {
        $page->$lang->title = PAGE . ' ' . $project->languages->$lang;
        $page->$lang->description = sprintf(u::translate('The page "%s" has not translated in "%s" language yet.')
                , PAGE, $project->languages[$lang]);
        $page->$lang->keywords = PAGE . ', ' . $project->languages[$lang];
        $page->$lang->content = $page->$lang->description . '<br/><br/>';
        $page->$lang->content .= u::translate('Available translations:')
                . '<ul>' . $t . '</ul>';
    } else {
        $page->$lang->title = PAGE;
        $page->$lang->description = sprintf(u::translate('The page "%s" has not published yet.')
                , PAGE);
        $page->$lang->keywords = PAGE;
        $page->$lang->content = '<h1>' . $page->$lang->description . '</h1>';
    }
}
?>