<?php

namespace Tests\Models;

use Veelasky\LaravelHashId\Eloquent\HashableId;

class HashModel extends BasicModel
{
    use HashableId;
}
