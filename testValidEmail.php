<?php
    use App\StringLib as lib;

    include_once "class/string.class.php";


    $email = "dadomingues@gmail.com";
    $a = new lib\ValidateString();

    // Validate if email is valid
    $b = $a->isEmail($email);
    print '<br>Validate if email '.$email.' is valid: ';
    var_dump ($b);

    // Validate if email is valid and exists
    $b = $a->isEmail($email, true);
    print '<br>Validate if email '.$email.' is valid and exists: ';
    var_dump ($b);

    // Validate if email is valid and exists
    $email = "dadominguescccccccccc@gmail.com";
    $b = $a->isEmail($email, true);
    print '<br>Validate if email '.$email.' is valid and exists: ';
    var_dump ($b);

?>