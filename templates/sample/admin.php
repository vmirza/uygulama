<?php

echo '<h1>admin.php</h1>';
echo '<pre>' . print_r(get_defined_vars(), 1) . '</pre>';

$page->var = 'It is defined in admin.php';
?>