<?php
    use App\StringLib as lib;

    include_once "class/string.class.php";


    $phoneNumber = "(+351)21-851 82 52";
    $a = new lib\ValidateString();
    //validate if phone number is from portugal
    $b = $a->isPhoneNumber($phoneNumber, false, 'PT');
    print 'validate if phone number is from portugal: ';
    var_dump ($b);

    //validate if phone number is from france
    $b = $a->isPhoneNumber($phoneNumber, false, 'FR');
    print 'validate if phone number is from France: ';
    var_dump ($b);

    //validade if phone number is from any country
    $b = $a->isPhoneNumber($phoneNumber, false);
    print 'validade if phone number is from any country: ';
    var_dump ($b);

    //validade against a specific pattern (begins with 71 or 72 and 9 digits long)
    $b = $a->isPhoneNumber($phoneNumber, false, 'PT', array('acceptedMask' => array('/7[12][0-9]{7}$/')));
    print 'validade against a specific pattern (begins with 71 or 72 and 9 digits long): ';
    var_dump ($b);

    //validade is phone number really exists
    $b = $a->isPhoneNumber($phoneNumber, true);
    print 'validade is phone number really exists: ';
    var_dump ($b);


?>