<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;

class BasicModel extends Model
{
    protected $table = 'hashid_test';
    protected static $unguarded = true;
}
