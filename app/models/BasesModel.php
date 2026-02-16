<?php

namespace app\models;

use flight\database\PdoWrapper;

abstract class BaseModel
{
    protected PdoWrapper $db;

    public function __construct(PdoWrapper $db)
    {
        $this->db = $db;
    }
}