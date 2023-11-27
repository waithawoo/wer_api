<?php

namespace App\Http\Repositories;

use App\Http\Repositories\BaseRepo;
use App\Models\LoginHistory;

class LoginHistoryRepo extends BaseRepo
{
    public function __construct(LoginHistory $model)
    {
        parent::__construct($model);
    }

    // Add your custom repository logic here
}
