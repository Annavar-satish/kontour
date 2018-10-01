<?php

namespace Kontenta\KontourSupport\Tests\Feature\Fakes;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Kontenta\Kontour\Events\AdminToolVisited;
use Kontenta\KontourSupport\AdminLink;
use Kontenta\Kontour\ShowAdminVisit;
use Kontenta\Kontour\Concerns\RegistersAdminWidgets;
use Kontenta\Kontour\Contracts\ItemHistoryWidget;

class UserlandController extends BaseController
{
    use RegistersAdminWidgets;

    public function index()
    {
        $link = new AdminLink(url()->full(), 'Recent Userland Tool');
        $user = Auth::guard(config('kontour.guard'))->user();
        $visit = new ShowAdminVisit($link, $user);
        event(new AdminToolVisited($visit));
        return view('userland::index');
    }

    public function create()
    {
        //
    }

    public function store()
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $widget = app(ItemHistoryWidget::class);
        $this->registerAdminWidget($widget);
        $widget->addCreateEntry(new \DateTime(), Auth::guard(config('kontour.guard'))->user());

        return view('userland::index');
    }

    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

}
