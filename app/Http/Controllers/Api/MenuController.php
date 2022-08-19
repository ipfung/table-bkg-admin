<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperUser = $this->isSuperLevel($user);
        $isExternalCoach = $this->isExternalCoachLevel($user);
        $menu = [
            ['label' => 'Dashboard', 'icon' => 'pi pi-fw pi-th-large', 'routerLink' => ['/']],
            ['label' => 'Calendar', 'icon' => 'pi pi-fw pi-calendar', 'routerLink' => ['/calendar'], 'visible' => $isSuperUser],
            ['label' => 'Appointment', 'icon' => 'pi pi-fw pi-exclamation-circle', 'routerLink' => ['/appointment-list']],
            ['label' => 'Finance', 'icon' => 'pi pi-fw pi-dollar', 'routerLink' => ['/finance']],
            ['label' => 'Partner', 'icon' => 'pi pi-fw pi-id-card', 'routerLink' => ['/partner-list'], 'visible' => $isSuperUser],
            ['label' => 'Trainer Student', 'icon' => 'pi pi-fw pi-users', 'routerLink' => ['/trainer-student-list'], 'visible' => $isExternalCoach],
            ['label' => 'Settings', 'icon' => 'pi pi-fw pi-cog', 'visible' => $isSuperUser, 'items' => [
                ['label' => 'Room', 'icon' => 'pi pi-fw pi-table', 'routerLink' => ['/settings/table-list']],
                ['label' => 'Working Hours', 'icon' => 'pi pi-fw pi-clock', 'routerLink' => ['/settings/working-hours-list']],
            ]],
        ];
        return $menu;
    }

}
