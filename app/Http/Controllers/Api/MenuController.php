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
            ['label' => 'Focus', 'icon' => 'pi pi-fw pi-calendar', 'routerLink' => ['/appointment-blocks'], 'visible' => $isExternalCoach],
            ['label' => 'Appointment', 'icon' => 'pi pi-fw pi-exclamation-circle', 'routerLink' => ['/appointment-list']],
            ['label' => 'Finance', 'icon' => 'pi pi-fw pi-dollar', 'routerLink' => ['/finance'], 'visible' => config("app.jws.settings.finance")],
            ['label' => 'Report', 'icon' => 'pi pi-fw pi-chart-line', 'visible' => $isSuperUser, 'expanded' => true, 'items' => [
                ['label' => 'Sales Report', 'icon' => 'pi pi-fw pi-chart-bar', 'routerLink' => ['/report-sales'], 'visible' => true],
                ['label' => 'Trainer Commission Report', 'icon' => 'pi pi-fw pi-percentage', 'routerLink' => ['/report-trainer-commissions'], 'visible' => ($isSuperUser && config("app.jws.settings.packages"))],

            ]],
            ['label' => 'Trainer', 'icon' => 'pi pi-fw pi-users', 'routerLink' => ['/trainer-student-list'], 'visible' => $isExternalCoach],
            ['label' => 'Student', 'icon' => 'pi pi-fw pi-id-card', 'routerLink' => ['/partner-list/Student'], 'visible' => $isSuperUser],
            ['label' => 'Settings', 'icon' => 'pi pi-fw pi-cog', 'visible' => $isSuperUser, 'expanded' => true, 'items' => [
                ['label' => 'Packages', 'icon' => 'pi pi-fw pi-book', 'routerLink' => ['/settings/package-list'], 'visible' => ($isSuperUser && config("app.jws.settings.packages"))],
                ['label' => 'Room', 'icon' => 'pi pi-fw ' . config("app.jws.settings.room_icon"), 'routerLink' => ['/settings/table-list'], 'visible' => $isExternalCoach],
                ['label' => 'Service', 'icon' => 'pi pi-fw pi-tags', 'routerLink' => ['/settings/service-list'], 'visible' => $isExternalCoach],
                ['label' => 'Working Hours', 'icon' => 'pi pi-fw pi-clock', 'routerLink' => ['/settings/working-hours-list'], 'visible' => (config("app.jws.settings.timeslots") != 'trainer_date' && $isExternalCoach)],
            ]],
        ];
        return $menu;
    }

}
