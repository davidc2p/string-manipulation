<?php
//Define pattern for each country according to https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers
//Complete this array for more countries. This is supplied as sample only.
return array(
    array(
        'country' => 'PT', 
        'countryName' => 'Portugal', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' '), 
        'numCountry' => '351', 
        'acceptedMask' => array('/9[1236][0-9]{7}$/', '/2[12][0-9]{7}$/', '/2[0-9]{9}$/'), 
        'minLength' => 9, 
        'maxLength' => 10
    ),
    array(
        'country' => 'BE', 
        'countryName' => 'Belgium', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' ', '/', '.'), 
        'numCountry' => '32', 
        'acceptedMask' => array('/0[0-9]{8}$/', '/04[0-9]{8}$/', '/4[0-9]{8}$/'), 
        'minLength' => 9, 
        'maxLength' => 10
    ),
    array(
        'country' => 'DE', 
        'countryName' => 'Germany', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' '), 
        'numCountry' => '49', 
        'acceptedMask' => array('/0[0-9]{10}$/', '/0[0-9]{12}$/', '/[0-9]{10}$/'), 
        'minLength' => 10, 
        'maxLength' => 12
    ),
    array(
        'country' => 'FR', 
        'countryName' => 'France', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' '), 
        'numCountry' => '33', 
        'acceptedMask' => array('/0[0-9]{9}$/'), 
        'minLength' => 10, 
        'maxLength' => 10
    )
)

?>