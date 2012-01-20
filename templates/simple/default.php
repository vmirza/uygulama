<?php

$template = $page->template;
$layout = $page->layout;
$subpages = $page->subpages;
// Set layout
if ($layout == 'left' || $layout == 'right') {
    $param = (PARAMS != 'default') ? PARAMS : key($subpages);
    $page = $page->subpages->$param;
}
// Check content language
if (!$page->$lang->title) {
    foreach ($project->languages as $k => $i) {
        if ($page->$k->content)
            $t .= '<li><a href="/' . $k . '/' . PAGE . '">'
                    . $page->$k->title . ' - ( ' . $i . ' )</a></li>';
    }
    $page->$lang->title = PAGE . ' ' . $project->languages->$lang;
    $page->$lang->description = sprintf(u::translate('The page "%s" has not translated in "%s" language yet.')
            , PAGE, $project->languages[$lang]);
    $page->$lang->keywords = PAGE . ', ' . $project->languages[$lang];
    $page->$lang->content = $page->$lang->description . '<br/><br/>';
    $page->$lang->content .= u::translate('Available translations:')
            . '<ul>' . $t . '</ul>';
}
// Clear subpages data
foreach($subpages as &$i){
    $i = $i->$lang->title;
};
// Set required parameters
$page = $page->$lang;
$page->template = $template;
$page->layout = $layout;
$page->subpages = $subpages;
?>