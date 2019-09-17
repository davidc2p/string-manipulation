<?php
    use App\StringLib as lib;

    include_once "class/string.class.php";

    $text = "text";
    $a = new lib\libString($text);
    $b = $a->isPhoneNumber('PT', $text);

    print 'b: '.$b;

?>