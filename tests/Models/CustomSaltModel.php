<?php

namespace Tests\Models;

class CustomSaltModel extends HashModel
{
    public function getHashIdSalt(): string
    {
        return 'custom-salt';
    }
}
