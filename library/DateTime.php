<?php

namespace Nth;

class DateTime {

    public static $formats = [
        //Day
        'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z'
        //Week
        , 'W'
        //Month
        , 'F', 'm', 'M', 'n', 't'
        //Year
        , 'L', 'o', 'Y', 'y'
        //Time
        , 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u'
        //Timezone
        , 'e', 'I', 'O', 'P', 'T', 'Z'
        //Full Date/Time
        , 'c', 'r', 'U'
    ];

}
