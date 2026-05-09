<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'act',
        'name',
        'description',
        'image',
        'script',
        'shortcode',
        'support',
        'status',
        'deleted_at',
    ];

    protected $casts = [
        'shortcode' => 'object',
    ];

    public function scopeGenerateScript()
    {
        $script = $this->script;
        foreach ($this->shortcode as $key => $item) {
            $script = str_replace('{{' . $key . '}}', $item->value, $script);
        }
        return $script;
    }
}
