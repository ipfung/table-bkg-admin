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
            ['label' => 'Calendar', 'icon' => 'pi pi-fw pi-calendar', 'routerLink' => ['/calendar'], 'visible' => $isExternalCoach],
            ['label' => 'Appointment', 'icon' => 'pi pi-fw pi-exclamation-circle', 'routerLink' => ['/appointment-list']],
            ['label' => 'Finance', 'icon' => 'pi pi-fw pi-dollar', 'routerLink' => ['/finance'], 'visible' => config("app.jws.settings.finance")],
            ['label' => 'Trainer', 'icon' => 'pi pi-fw pi-users', 'routerLink' => ['/trainer-student-list'], 'visible' => $isExternalCoach],
            ['label' => 'Student', 'icon' => 'pi pi-fw pi-id-card', 'routerLink' => ['/partner-list/Student'], 'visible' => $isSuperUser],
            ['label' => 'Settings', 'icon' => 'pi pi-fw pi-cog', 'visible' => $isSuperUser, 'expanded' => true, 'items' => [
//                ['label' => 'User', 'icon' => 'pi pi-fw pi-id-card', 'routerLink' => ['/partner-list/User'], 'visible' => $isSuperUser],
                ['label' => 'Room', 'icon' => 'pi pi-fw ' . config("app.jws.settings.room_icon"), 'routerLink' => ['/settings/table-list'], 'visible' => $isExternalCoach],
                ['label' => 'Working Hours', 'icon' => 'pi pi-fw pi-clock', 'routerLink' => ['/settings/working-hours-list'], 'visible' => $isExternalCoach],
            ]],
        ];
        return $menu;
    }

}
