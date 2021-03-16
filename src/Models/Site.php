<?php

namespace Tritiyo\Site\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id', 'user_id', 'location', 'site_code', 'material', 'site_head', 'budget'
    ];
}
