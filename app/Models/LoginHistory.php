<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'email', 'status', 'ip_address'];

    public function toArray()
    {
        $attributes = parent::toArray();
        if (array_key_exists('created_at', $attributes)) {
            $attributes['created_at'] = Carbon::parse($attributes['created_at'])->toDateString();
        }
        if (array_key_exists('updated_at', $attributes)) {
            $attributes['updated_at'] = Carbon::parse($attributes['updated_at'])->toDateString();
        }
        // $fillableAttributes = $this->getFillable();
        // $missingAttributes = array_diff($fillableAttributes, array_keys($attributes));

        // foreach ($missingAttributes as $attribute) {
        //     $attributes[$attribute] = null;
        // }

        return $attributes;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
