<?php

namespace Tests\Models;

class CustomKeyModel extends HashModel
{
    protected $hashKey = 'somethingUnique';
}
