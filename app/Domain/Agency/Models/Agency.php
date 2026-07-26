<?php

namespace App\Domain\Agency\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'legal_name', 'email', 'phone', 'address', 'status'])]
class Agency extends Model
{
    use HasFactory;
}
