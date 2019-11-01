# Kontour

[![Build Status](https://travis-ci.org/kontenta/kontour.svg?branch=master)](https://travis-ci.org/kontenta/kontour)

Kontour is a package of admin page utilities for Laravel.
It provides a shared "frame" for the admin routes you create
in your Laravel apps, or in packages you write.

The idea is that your admin tools can pull in and use functionality from Kontour
to provide a consistent experience for the whole admin area of a website.
Your admin tools are built using standard Laravel routes, controllers,
authentication, authorization, validation, views, etc.
Kontour is there to provide enhancements and reusable elements for your admin
area.

You need at least **Laravel 5.7** and **PHP 7.1** to use this package.

## Features

- Admin login and password reset routes with configurable Guard
  to separate admin users from frontend users.
- Extendable Blade Layouts with named sections for admin tool views
  and configurable stylesheet and javascript dependencies.
- Widgets that are placeable in named Blade sections:
  - Global widgets for menu, logout, and recently used tools.
  - Tool widgets for feedback messages, crumbtrail, and item history.
- Admin route groups with configurable url-prefix and domain.
- Reusable form input Blade includes/components.
- Authorization for `AdminLink`s ensures that the current user has privileges
  before echoing links.

## Architecture

- Kontour is installed as a dependency, not a boilerplate.
- Kontour uses core Laravel functionality wherever possible,
  for example authentication and authorization.

## Install

Maybe you're here because some package you installed requires Kontour for its
admin pages? In that case it's already installed by composer, but you may still
want to read further below about how to configure Kontour to your liking.

Installing Kontour explicitly in your Laravel project:

```bash
composer require kontenta/kontour
```

## Checking the route list

Kontour, and packages using it, will register routes automatically in your
Laravel app. To keep track of what's happening you may print all the routes
using artisan:

```bash
php artisan route:list -c
```

The list will display information about every URI, route name, and middleware
in your app.
Among others you'll find the `kontour.login`, `kontour.logout`,
and `kontour.index` routes.
If these routes are not to your liking there are configuration values you can
set to change the url prefix or domain.

## Configure Kontour in your Laravel project

Publish the configuration with artisan:

```bash
php artisan vendor:publish --tag="kontour-config"
```

Then you can edit `config/kontour.php` and uncomment any of the
[example settings](https://github.com/kontenta/kontour/blob/master/config/kontour.php)
you want to tweak.

## Logging in

By default the Kontour dashboard route `kontour.index` is reached by going to
`/admin` in your browser.

To enable login you need to make sure the user model you want to give access to
the admin area implements the
[`Kontenta\Kontour\Contracts\AdminUser` contract](https://github.com/kontenta/kontour/blob/master/src/Contracts/AdminUser.php)
which has method `getDisplayName()` that should return... a display name!

The default Kontour configuration uses Laravel's `web` Guard from
`config/auth.php` which in turn uses the Eloquent user provider with model
`App\User::class`.
If you're happy to let **all** your users into the admin area
(i.e. you have no front end users) you can modify that user class to implement
the interface, by having it extend `Kontenta\Kontour\Auth\AdminUser`.

This requirement is deliberate to avoid any situation where someone accidentally
gives front end users access to their admin routes.
You need to make an active choice about which user model to let into the admin
area.

### Creating a separate user provider for admins

The most common situation is that you want a separate table and model for
admin users, and a separate Laravel User Provider and Guard to go with that.

1. Create an Eloquent model and table.
   The simplest way is to make copies of Laravel's `app/User.php` model and
   create users table migration in `database\migrations` and modify them
   to your needs.
2. Make sure the model implements `Kontenta\Kontour\Contracts\AdminUser`,
   perhaps by extending `Kontenta\Kontour\Auth\AdminUser`.
3. Edit `config/auth.php` to add a Guard, User Provider and perhaps a password
   reset configuration:

   ```php
   'guards' => [
     //...
     'admin' => [
       'driver' => 'session',
       'provider' => 'admins',
     ],
   ],

   'providers' => [
     //...
     'admins' => [
       'driver' => 'eloquent',
       'model' => App\AdminUser::class, // Your admin user model
     ],
   ],

   'passwords' => [
     //...
     'admins' => [
       'provider' => 'admins',
       'table' => 'password_resets', //using same table as the main user model
       'expire' => 60,
     ],
   ],
   ```

4. Edit `config/kontour.php` and tell it to use the name of your admin guard,
   and the passwords configuration:

   ```php
   'guard' => 'admin',
   'passwords' => 'admins',
   ```

### Creating admin users

It doesn't make sense to have a public registration for admin users so
the easiest way to create admin users for development and production is through `php artisan tinker`:

```php
/* Use the name of your admin model, this examples uses the default App\User */

// List all users
App\User::all();

// Start building a new user object
$user = new App\User();

// Set fields
$user->name = 'Admin';
$user->email = 'admin@yourdomain.net';

// Set a password (remember to send it to the user):
$user->password = bcrypt(...);
// ...or have the user reset password before logging in (if you've added a password reset configuration):
$user->password = '';

// Then save the user!
$user->save();
```

If you're feeling adventuorus, you can then create an admin tool within Kontour
to let a logged in admin create and invite new admin users!

## Publish the default CSS and js in your Laravel project

You probably want to add some style to your admin area,
perhaps pure HTML is too brutalist for your taste...
A good place to start is the default Kontour stylesheet.

Publish the CSS file using artisan:

```bash
php artisan vendor:publish --tag="kontour-styling"
```

Then edit `config/kontour.php` and uncomment `'css/kontour.css'` in the
`stylesheets` array to make every admin page pull in the stylesheet.

### Javascript

The included javascript includes a feature to confirm any delete-action before submitting those forms,
and a confirmation before leaving a page with "dirty" form inputs.

The procedure to publish javascript using artisan:

```bash
php artisan vendor:publish --tag="kontour-js"
```

Then edit `config/kontour.php` and uncomment `'js/kontour.js'` in the
`javascripts` array to make every admin page pull in the javascript.

## Registering admin routes

In a service provider you can register your admin routes
using methods from the
[`RegistersAdminRoutes` trait](https://github.com/kontenta/kontour/blob/master/src/Concerns/RegistersAdminWidgets.php).

## Running code only before admin routes are accessed

For anything that needs to be "booted" before an admin page/route is loaded,
inject `Kontenta\Kontour\Contracts\AdminBootManager` and add callables to it
using `beforeRoute()`.
Those callables will be called (with any dependencies injected) by a middleware.
This avoids running admin-related code on every page load on the public site.

## Extending Kontour's Blade layouts

In the Blade views you create for your admin pages you can inject
a "view manager" instance:

```php
@inject('view_manager', 'Kontenta\Kontour\Contracts\AdminViewManager')
```

...that can be used to pull out one of the common Blade layouts to extend for
any admin pages that wants to be part of the family:

```php
@extends($view_manager->toolLayout())
```

The `toolLayout` has sections `kontourToolHeader`, `kontourToolMain`,
`kontourToolWidgets`, and `kontourToolFooter` for you to populate.

```php
@section('kontourToolHeader')
  <h1>A splendid tool</h1>
  @parent
@endsection

@section('kontourToolMain')
  <form ...>
    <input ...>
    <button type="submit">Save</button>
  </form>
@endsection
```

It's a good idea to include `@parent` in your sections for other content,
for example registered widgets.

## Templates

Kontour provides
[some Blade views](https://github.com/kontenta/kontour/tree/master/resources/views)
that can be used with `@include` or `@component` to display common elements in your admin views.

### Form templates

[The form views](https://github.com/kontenta/kontour/tree/master/resources/views/forms)
generate form inputs along with labels and validation feedback.

```php
<form ...>
@include('kontour::forms.input', ['name' => 'username', 'type' => 'email'])
</form>
```

The form views will prefill inputs with data from a `$model` variable if it is set in the blade view,
so you may just pass an Eloquent model to the view.

[Laravel's `$errors` bag](https://laravel.com/docs/5.8/validation#quick-displaying-the-validation-errors)
is used to display errors for inputs.
If you have [named error bags](https://laravel.com/docs/5.8/validation#named-error-bags) in your view,
you can put one of those bags into the `$errors` variable by including a partial view.
This is actually a good pattern for scoping variables to one of the forms on your page, if you have more than one.

```php
@include('my_form_partial', ['errors' => $errors->my_form_bag, 'model' => $user])
```

If the `$errors` bag contains any errors,
[old input data from the previous request](https://laravel.com/docs/5.8/helpers#method-old)
will be used to repopulate the form.

The `id` attribute is set automatically on created elements that need it,
and it's usually derieved from the `$name` variable.
If you get an id conflict on a page where two inputs may have the same name,
e.g. in different forms, different `$idPrefix` can be passed along to the templates
to make the ids unique.

#### Input autofocus

The variable `$autofocusControlId` can be set to the id of the input you want to `autofocus`,
usually the first field with errors.
If no `$idPrefix` is set, this conveniently corresponds to the keys in Laravel's `$errors` bag.
It's best to set it as high up as possible in the view, before any forms are included.
You could even set in the controller and pass it along to the view.

### Common parameters

All inputs need at least the `$name` parameter
and optional `$placeholder` and `$ariaDescribedById` parameters.

All form views take a `$controlAttributes` `array` that can be used to set any additional html attributes
on the form control element.
This can be useful for setting `required`, `disabled`, `readonly`, `autocomplete`, and other attributes specific to the
[different input types](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input#Form_<input>_types).

```php
<form ...>
@include('kontour::forms.input', [
  'name' => 'country_code',
  'controlAttributes' => ['required', 'autocomplete' => 'country', 'size' => '2']
]])
</form>
```

The corresponding parameter to put extra attributes on the label tag is `$labelAttributes`.

### Available input templates

- `textarea` - Pass `$value` to set input contents.
- `input` - Same API as `textarea`, but you can pass `$type` to set the input type (defaults to `text`).
- `select` - Pass `$options` as an `array` of key-values and an optional `$selected` `string` and `$disabledOptions` `array`.
- `radiobuttons` - Same API as `select` for printing radiobuttons instead.
- `multiselect` - Same API as `select` but optional `$selected` `array` instead of `string`.
- `checkboxes` - Same API as `multiselect` for printing checkboxes instead.
- `checkbox` - Pass optional `$checked` as `boolean` and `$value` for a `value` attribute other than
  default `1` (or `$checkboxDefaultValue`).

### Button templates

[The button views](https://github.com/kontenta/kontour/tree/master/resources/views/buttons)
generate buttom elements for common actions like "create", "update", and "destroy",
as well as a "generic" button, and a "link"-like button.
The button views take a `$buttonAttributes` array of html attributes to set on the button element.

```php
@component('kontour::buttons.generic', ['type' => 'reset'])
  Oh, the old <code>reset</code> button!
@endcomponent
```

There's also a logout button and hamburger menu button.

### Time templates

There's a [view](https://github.com/kontenta/kontour/tree/master/resources/views/elements/time.blade.php)
for rendering [`<time>` tags](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/time)
to which you supply a [`Carbon`](https://carbon.nesbot.com)
`$carbon` variable and it prints a proper `datetime` atom string attribute
and by default a human readable time difference.

```php
@include('kontour::elements.time', ['carbon' => \Carbon\Carbon::now()])
```

You may also pass a `$format` string to display the tag contents in a specific format
instad of the default relative time.
If you pass `['format' => true]` the default format from Kontour's
[config file](https://github.com/kontenta/kontour/blob/master/config/kontour.php)
will be used.

## Adding menu items

Usually adding menu items is done in a service provider's boot method:

```php
use Kontenta\Kontour\Contracts\AdminBootManager;
use Kontenta\Kontour\Contracts\MenuWidget;
use Kontenta\Kontour\AdminLink;

$this->app->make(AdminBootManager::class)->beforeRoute(function (MenuWidget $menuWidget) {
  $menuWidget->addLink(
    AdminLink::create('A menu item', route('named.route'))
      ->registerAbilityForAuthorization('gate or other ability'),
    'A menu heading'
  );
});
```

## Authorizing controller actions

The
[`AuthorizesAdminRequests` trait](https://github.com/kontenta/kontour/blob/master/src/Concerns/AuthorizesAdminRequests.php)
has convenince methods for controllers that both authorizes the current user
against an ability, and dispatches an event that records the visit for the
recent visits widgets.

With the trait used on your controller you can call
`$this->authorizeShowAdminVisit()` for view-only routes or
`$this->authorizeEditAdminVisit()` for routes that present a form.

Both methods take 4 parameters:

- The name of the ability to authorize against
- The name of the link to present in recent visits widgets
- The description string for link `title` attribute (optional)
- Arguments for the ability (optional)

## Registering widgets

All widgets implement the
[`AdminWidget` interface](https://github.com/kontenta/kontour/blob/master/src/Contracts/AdminWidget.php)
and can be registered into a section from a service provider
or controller using methods from the
[`RegistersAdminWidgets`](`https://github.com/kontenta/kontour/blob/master/src/Concerns/RegistersAdminWidgets.php`)
trait.

In the `kontour.php` config file you may specify the widgets for all
admin pages using the `global_widgets` array, mapping classname/contract to the
desired section name.

## Fallback implementations

This package contains implementations of the Kontour contracts that are used as
a fallback whenever no other implementation has been registered in the Laravel
service container.

Overriding implementations may be registered by service providers of other
packages or your main application.
