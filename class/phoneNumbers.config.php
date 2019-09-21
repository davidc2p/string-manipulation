<?php
//Define pattern for each country according to https://en.wikipedia.org/wiki/National_conventions_for_writing_telephone_numbers
return array(
    array(
        'country' => 'PT', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' '), 
        'numCountry' => '351', 
        'acceptedMask' => array('/9[1236][0-9]{7}$/', '/2[12][0-9]{7}$/', '/2[0-9]{9}$/'), 
        'minLength' => 9, 
        'maxLength' => 10
    ),
    array(
        'country' => 'FR', 
        'countrySeparator' => '()',
        'charSeparators' => array('-', '+', ' '), 
        'numCountry' => '33', 
        'acceptedMask' => array('/0[0-9]{9}$/'), 
        'minLength' => 10, 
        'maxLength' => 10
    )
)

?>