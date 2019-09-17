<?php
    namespace App\StringLib;

    interface iString
    {
        public function isPhoneNumber(string $country, string $value, array $mask);
    }

    class libString  implements iString
    {
        public $a = ''; 

        public function isPhoneNumber(string $country, string $value, array $mask) {
            $result = true;

            switch ($country) {
                case 'PT':
                    $mask = ['93xxxxxxx', '96xxxxxxx'];
                    break;
            }


            return $result;
        }
    }

    ?>