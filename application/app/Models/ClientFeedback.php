<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientFeedback extends Model
{
    protected $fillable = ['name', 'profession', 'city', 'comment', 'rating'];

    protected $casts = ['is_approved' => 'boolean', 'rating' => 'integer'];

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function testimonial(): array
    {
        return [
            'name' => $this->name,
            'designation' => $this->city . ' - ' . $this->profession,
            'description' => $this->comment,
            'star_count' => $this->rating,
            'image' => asset('assets/images/general/favicon.png'),
        ];
    }
}
