<?php

namespace Kontenta\Kontour\Widgets;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\View;
use Kontenta\Kontour\Concerns\ResolvesAdminUser;
use Kontenta\Kontour\Contracts\PersonalRecentVisitsWidget as PersonalRecentVisitsWidgetContract;
use Kontenta\Kontour\RecentVisitsRepository;

class PersonalRecentVisitsWidget implements PersonalRecentVisitsWidgetContract
{
    use ResolvesAdminUser;

    protected $repository;

    public function __construct(RecentVisitsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function toHtml()
    {
        return View::make('kontour::widgets.personalRecentVisits', ['visits' => $this->getVisits()])->render();
    }

    public function isAuthorized(Authorizable $user = null): bool
    {
        return (bool) $user;
    }

    private function getVisits()
    {
        return $this->repository->getShowVisits()->merge($this->repository->getEditVisits())->filter(function ($visit) {
            return $visit->getUser()->is($this->user()) and $visit->getLink()->isAuthorized($this->user());
        })->unique(function ($visit) {
            return $visit->getLink()->getUrl();
        })->sortByDesc(function ($visit) {
            return $visit->getDateTime();
        });
    }
}
