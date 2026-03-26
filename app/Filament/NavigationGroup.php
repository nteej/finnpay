<?php

namespace App\Filament;

enum NavigationGroup: string
{
    case UserManagement = 'User Management';
    case Payments       = 'Payments';
    case Settings       = 'Settings';
}
