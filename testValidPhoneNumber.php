<?php
    use App\StringLib as lib;

    include_once "class/string.class.php";


    $phoneNumber = "(+351)21-851 82 52";
    $a = new lib\ValidateString();
    //Validate if phone number is from portugal
    $b = $a->isPhoneNumber($phoneNumber, false, 'PT');
    print '<br>Validate if phone number '.$phoneNumber.' is from portugal: ';
    var_dump ($b);

    //Validate if phone number is from france
    $b = $a->isPhoneNumber($phoneNumber, false, 'FR');
    print '<br>Validate if phone number '.$phoneNumber.' is from France: ';
    var_dump ($b);

    //Validate if phone number is from any country
    $b = $a->isPhoneNumber($phoneNumber, false);
    print '<br>Validate if phone number '.$phoneNumber.' is from any country: ';
    var_dump ($b);

    //Validate if phone number is from any country without country code (not found)
    $phoneNumber = "51-851-82-5273";
    $b = $a->isPhoneNumber($phoneNumber, false);
    print '<br>Validate if phone number '.$phoneNumber.' is from any country without country code: ';
    var_dump ($b);

    //Validate if phone number is from any country without country code (found)
    $phoneNumber = "01-851-82-527";
    $b = $a->isPhoneNumber($phoneNumber, false);
    print '<br>Validate if phone number '.$phoneNumber.' is from any country without country code: ';
    var_dump ($b);

    //Validate if phone number is from Belgium (found)
    $phoneNumber = "(+32)455/118.252";
    $b = $a->isPhoneNumber($phoneNumber, false);
    print '<br>Validate if phone number '.$phoneNumber.' is from any country without country code: ';
    var_dump ($b);

    //Validate against a specific pattern (begins with 71 or 72 and 9 digits long)
    $b = $a->isPhoneNumber($phoneNumber, false, null, array('acceptedMask' => array('/7[12][0-9]{7}$/')));
    print '<br>Validate phone number '.$phoneNumber.' against a specific pattern (begins with 71 or 72 and 9 digits long): ';
    var_dump ($b);

    //Validate is phone number really exists
    $phoneNumber = "(351)21-851-82-52";
    $b = $a->isPhoneNumber($phoneNumber, true);
    print '<br>Validate is phone number '.$phoneNumber.' really exists: ';
    var_dump ($b);


?>