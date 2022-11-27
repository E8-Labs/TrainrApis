<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    const RoleAdmin = 1;
    const RoleTrainr = 2;
    const RoleClient = 3;
}
