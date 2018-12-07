<?php

namespace Kontenta\Kontour\Widgets;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Kontenta\Kontour\Concerns\ResolvesAdminUser;
use Kontenta\Kontour\Contracts\AdminLink;
use Kontenta\Kontour\Contracts\MenuWidget as MenuWidgetContract;

class MenuWidget implements MenuWidgetContract
{
    use ResolvesAdminUser;

    /**
     * @var Collection
     */
    protected $links;

    public function __construct()
    {
        $this->links = new Collection();
    }

    public function toHtml()
    {
        return View::make('kontour::widgets.menu', ['links' => $this->authorizedLinks()])->render();
    }

    public function addLink(AdminLink $link, string $desiredHeading = null): MenuWidgetContract
    {
        if (empty($desiredHeading)) {
            $desiredHeading = 'main';
        }

        if (!$this->links->has($desiredHeading)) {
            $this->links->put($desiredHeading, new Collection());
        }

        $this->links->get($desiredHeading)->push($link);

        return $this;
    }

    public function getHeadings(): Collection
    {
        return $this->links->keys();
    }

    public function isAuthorized(Authorizable $user = null): bool
    {
        return (bool) $user;
    }

    protected function authorizedLinks()
    {
        return $this->links->map(function ($links) {
            return $links->filter->isAuthorized($this->adminUser());
        });
    }
}
