<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ufm extends Model
{
    use HasFactory;

    protected $table = 'ufms'; // Explicitly define the table name if different from model name

    protected $fillable = [
        'user_id',
        'exam_id',
        'description',
        'ufm_flag',
    ];

    // Relationship to User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to Exam model (assuming Oex_exam_master is your exam model)
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Oex_exam_master::class, 'exam_id');
    }

}
