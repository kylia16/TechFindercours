<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    protected $table = 'competences';
    protected $primaryKey = 'code_comp';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'code_comp',
        'label_comp',
        'description_comp',
    ];

    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'user_competences', 'code_comp', 'code_user');
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class, 'code_comp', 'code_comp');
    }

    public function userCompetences()
    {
        return $this->hasMany(User_Competence::class, 'code_comp', 'code_comp');
    }


}
