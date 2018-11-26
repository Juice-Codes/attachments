<?php

return [

    /*
     * Setup how long (minutes) should eloquent model be stored in
     * cache to reduce database sql query for same attachment.
     */

    'cache-time' => 60,

    /*
     * This package use hashids to generate attachment identify key.
     * To let the output key more unique, you could generate a random
     * string and set it to JA_HASHIDS_SALT environment variable.
     *
     * @reference: https://github.com/ivanakimov/hashids.php
     */

    'hashids-salt' => env('JA_HASHIDS_SALT', ''),

];
