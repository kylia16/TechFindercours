<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $table = 'interventions';
    protected $primaryKey = 'code_interv';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'code_interv',
        'date_interv',
        'description_interv',
        'code_user',
        'code_comp',
    ];

    public function client()
    {
        return $this->belongsTo(Utilisateur::class, 'code_user_client', 'code_user');
    }

    public function technicien()
    {
        return $this->belongsTo(Utilisateur::class, 'code_user_tech', 'code_user');
    }

    public function competence()
    {
        return $this->belongsTo(Competence::class, 'code_comp', 'code_comp');
    }
}
