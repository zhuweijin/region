<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    protected $guarded = ['created_at', 'updated_at'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
