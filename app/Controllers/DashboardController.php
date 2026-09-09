<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\OutgoingLetter;

final class DashboardController
{
    public function index(): void { Auth::requireLogin(); View::render('dashboard/index', OutgoingLetter::dashboardStats()); }
}
