<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table      = 'utilisateur';
    protected $primaryKey = 'code_user';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = true;

    protected $fillable = [
        'code_user',
        'nom_user',
        'prenom_user',
        'login_user',
        'password_user',
        'tel_user',
        'sexe_user',
        'role_user',
        'etat_user',
    ];

    protected $hidden = ['password_user'];

    public function getAuthPassword(): string
    {
        return $this->password_user;
    }

    public function competences()
    {
        return $this->belongsToMany(
            Competence::class,
            'user_competence',
            'code_user',
            'code_comp'
        );
    }

    public function interventionsClient()
    {
        return $this->hasMany(Intervention::class, 'code_user_client', 'code_user');
    }

    public function interventionsTechnicien()
    {
        return $this->hasMany(Intervention::class, 'code_user_techn', 'code_user');
    }
    public function getAuthIdentifierName(): string
{
    return 'code_user';
}
}
