<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class HashModel extends BasicModel
{
    use HashableId;
}
