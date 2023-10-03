<?php

return [
    /*
     * Determine how many characters HashId should generate.
     */
    'hash_length' => env('HASHID_LENGTH', 8),

    /*
     * Determine HashId characters set.
     */
    'hash_alphabet' => env('HASHID_ALPHABET', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),

    /*
     * Override generated HashId salt.
    */
    'hash_salt' => env('HASHID_SALT', null),
];
