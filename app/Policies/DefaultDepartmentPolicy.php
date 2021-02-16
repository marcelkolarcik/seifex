<?php

namespace App\Policies;

use App\Admin;
use App\Services\DutyCheckAdmin;
use Illuminate\Auth\Access\HandlesAuthorization;

class DefaultDepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public $check;
    
    public function __construct(DutyCheckAdmin $check)
    {
        $this->check    =   $check;
    }
    
    public function create_departments(Admin $admin)
    {
        return $this->check->admin($admin,'create-departments');
    }
}
