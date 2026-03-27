<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Competence extends Model
{
    protected $table = 'user_competences';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = true;
    protected $fillable = [
        'code_user',
        'code_comp',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'code_user', 'code_user');
    }
}
