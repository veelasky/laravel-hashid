<?php

namespace Tests\Models;

class PersistingModelWithCustomName extends PersistingModel
{
    protected $hashColumnName = 'custom_name';
}
