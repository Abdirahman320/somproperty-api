<?php
return [
    'defaults' => ['guard'=>'web','passwords'=>'users'],
    'guards' => [
        'web'    => ['driver'=>'session','provider'=>'users'],
        'admin'  => ['driver'=>'session','provider'=>'admins'],
        'owner'  => ['driver'=>'session','provider'=>'owners'],
        'tenant' => ['driver'=>'session','provider'=>'tenants'],
        'agent'  => ['driver'=>'session','provider'=>'agents'],
    ],
    'providers' => [
        'users'   => ['driver'=>'eloquent','model'=>App\Models\User::class],
        'admins'  => ['driver'=>'eloquent','model'=>App\Models\AdminUser::class],
        'owners'  => ['driver'=>'eloquent','model'=>App\Models\Owner::class],
        'tenants' => ['driver'=>'eloquent','model'=>App\Models\Tenant::class],
        'agents'  => ['driver'=>'eloquent','model'=>App\Models\Agent::class],
    ],
    'passwords' => [
        'users' => ['provider'=>'users','table'=>'password_reset_tokens','expire'=>60,'throttle'=>60],
    ],
    'password_timeout' => 10800,
];
