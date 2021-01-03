<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class HashModel extends Model
{
    use HashableId;

    protected $table = 'hashid_test';
    protected static $unguarded = true;
}
