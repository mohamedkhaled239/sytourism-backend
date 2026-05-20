<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_super_admin',
        'account_type',
        'governorate_id',
        'tourism_type_id',
        'permissions',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
        'permissions' => 'array',
        'last_login_at' => 'datetime',
    ];

    public function hasPermission($permission)
    {
        if ($this->is_super_admin) {
            return true;
        }
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function isDataEntry(): bool
    {
        return in_array($this->account_type, [
            'data_entry_tourism_establishments',
            'data_entry_attractions_routes',
        ], true);
    }

    public function accountTypeLabel(): string
    {
        return match ($this->account_type) {
            'data_entry_tourism_establishments' => 'مدخل منشآت سياحية',
            'data_entry_attractions_routes' => 'مدخل مواقع الجذب والمسارات',
            default => $this->is_super_admin ? 'مشرف عام' : 'مشرف',
        };
    }
}
