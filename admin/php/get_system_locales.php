<?php
    require_once('./request_head.php');

    $locales = array_slice(scandir('../../locale'), 2);

    echo json_encode($locales);
?>
