<?php
header('Content-type: image/jpeg');
echo file_get_contents(isset($_GET["url"])?$_GET["url"]:'http://static.jb51.net/images/v1/loading-16-16.gif');
?>