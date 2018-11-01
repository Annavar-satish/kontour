@inject('view_manager', 'Kontenta\Kontour\Contracts\AdminViewManager')
@inject('widget_manager', 'Kontenta\Kontour\Contracts\AdminWidgetManager')

@extends($view_manager->layout())

@section($view_manager->mainSection())
  <header>
  @section($view_manager->toolHeaderSection())
    @foreach($widget_manager->getWidgetsForSection($view_manager->toolHeaderSection()) as $widget)
      {{ $widget }}
    @endforeach
  @show
  </header>
  
  @yield($view_manager->toolMainSection())

  @yield($view_manager->toolWidgetSection())
  
  @yield($view_manager->toolFooterSection())
@append