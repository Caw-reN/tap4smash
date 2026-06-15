<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table          = 'admins';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $allowedFields  = ['username', 'password'];

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
