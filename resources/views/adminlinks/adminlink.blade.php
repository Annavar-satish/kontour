<a{!! $link->getUrl() ? ' href="'.htmlspecialchars($link->getUrl()).'"' : '' !!}{!! $link->getDescription() ? ' title="'.htmlspecialchars($link->getDescription()).'"' : '' !!}{!! url()->full() == $link->getUrl() ? ' aria-current="page"' : '' !!}>{{ $link->getName() }}</a>
