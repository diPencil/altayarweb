<?php

// Isolated regression check: never connects to the configured production database.
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::create('https://altayarvip.com/client-feedback'));
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
]);
$app['config']->set('database.default', 'sqlite');
$app['config']->set('database.connections.sqlite', [
    'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
]);
$app['config']->set('cache.default', 'array');
$app['config']->set('session.driver', 'array');
$app['config']->set('app.providers', Illuminate\Support\ServiceProvider::defaultProviders()->toArray());
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
]);
set_exception_handler(function (Throwable $exception): void {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
});
foreach (Illuminate\Support\ServiceProvider::defaultProviders()->toArray() as $provider) {
    $app->register($provider);
}
$app->register(App\Providers\RouteServiceProvider::class);
$app->boot();
$app['view']->setFinder(new class($app['files'], $app['config']['view.paths']) extends Illuminate\View\FileViewFinder {
    public function find($view)
    {
        if (in_array($view, ['presets.default.layouts.frontend', 'admin.layouts.app'])) {
            return __DIR__ . '/fixtures/feedback_layout.blade.php';
        }
        return parent::find($view);
    }
});
$app['view']->addNamespace('pagination', dirname(__DIR__) . '/vendor/laravel/framework/src/Illuminate/Pagination/resources/views');
$general = new App\Models\GeneralSetting();
$general->active_template = 'default';
$app['cache']->put('GeneralSetting', $general);
$session = $app['session']->driver();
$session->start();
$baseRequest = Illuminate\Http\Request::create('https://altayarvip.com/client-feedback');
$baseRequest->setLaravelSession($session);
$app->instance('request', $baseRequest);
$app['url']->setRequest($baseRequest);
$app['redirect']->setSession($session);
$migration = require dirname(__DIR__) . '/database/migrations/2026_09_17_000001_create_client_feedback_table.php';
$migration->up();
Illuminate\Support\Facades\Schema::create('frontends', function ($table) {
    $table->id();
    $table->string('data_keys');
    $table->text('data_values')->nullable();
});

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
    echo "PASS: {$message}\n";
}

function feedbackRequest(array $data): App\Http\Requests\ClientFeedbackRequest
{
    $request = App\Http\Requests\ClientFeedbackRequest::create('/client-feedback', 'POST', $data);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setLaravelSession(app('session')->driver());
    $request->validateResolved();
    return $request;
}

$data = ['name' => ' Test Client ', 'profession' => 'New Profession', 'city' => 'Riyadh', 'comment' => '<script>alert(1)</script> Great service', 'rating' => 4, 'is_approved' => 1];
$public = new App\Http\Controllers\ClientFeedbackController();
$public->store(feedbackRequest($data));
$item = App\Models\ClientFeedback::firstOrFail();
check($item->name === 'Test Client', 'Input is trimmed');
check(!$item->is_approved && App\Models\ClientFeedback::approved()->count() === 0, 'Public submission cannot approve itself');

try {
    feedbackRequest(array_merge($data, ['rating' => 6, 'comment' => 'short']));
    throw new RuntimeException('Invalid submission was accepted');
} catch (Illuminate\Validation\ValidationException $exception) {
    check(isset($exception->errors()['rating'], $exception->errors()['comment']), 'Invalid rating and short comment are rejected');
}

$admin = new App\Http\Controllers\Admin\ClientFeedbackController();
$admin->approve($item->id);
check(App\Models\ClientFeedback::approved()->count() === 1, 'Admin approval publishes feedback');
$testimonial = $item->fresh()->testimonial();
check($testimonial['star_count'] === 4 && str_ends_with($testimonial['image'], '/assets/images/general/favicon.png'), 'Rating and automatic image match testimonial format');
$admin->update(feedbackRequest(array_merge($data, ['name' => 'Edited Client', 'is_approved' => 0])), $item->id);
check($item->fresh()->name === 'Edited Client' && App\Models\ClientFeedback::approved()->count() === 0, 'Admin can edit and unpublish feedback');

$app['view']->share('activeTemplate', 'presets.default.');
$app['view']->share('errors', new Illuminate\Support\ViewErrorBag());
$app->setLocale('en');
$html = view('presets.default.components.client_feedback_form', ['professions' => collect(['Engineer']), 'cities' => collect(['Riyadh'])])->render();
check(str_contains($html, 'name="profession"') && str_contains($html, 'name="city"') && str_contains($html, 'name="_token"'), 'Public form renders profession, city and CSRF fields');
check(str_contains($html, 'Write your review') && str_contains($html, 'dir="ltr"') && !str_contains($html, 'اكتب تعليقك'), 'English form uses English labels and LTR direction');
check((new App\Http\Requests\ClientFeedbackRequest())->attributes()['name'] === 'Name', 'English validation field names are localized');
$app->setLocale('ar');
$arabicHtml = view('presets.default.components.client_feedback_form', ['professions' => collect(['Engineer']), 'cities' => collect(['Riyadh'])])->render();
check(str_contains($arabicHtml, 'اكتب تعليقك') && str_contains($arabicHtml, 'dir="rtl"'), 'Arabic form keeps Arabic labels and RTL direction');
check((new App\Http\Requests\ClientFeedbackRequest())->attributes()['name'] === 'الاسم', 'Arabic validation field names are localized');
$app->setLocale('en');
$sidebar = file_get_contents(resource_path('views/admin/components/sidenav.blade.php'));
check(substr_count($sidebar, "route('admin.client-feedback.index')") === 1
    && strpos($sidebar, "@lang('Membership')") < strpos($sidebar, "route('admin.client-feedback.index')")
    && strpos($sidebar, "route('admin.client-feedback.index')") < strpos($sidebar, "@lang('Booking Management')"), 'Client Feedback appears once below Membership in Users Management');
$site = new App\Http\Controllers\SiteController();
$pendingHtml = $site->clientFeedback()->render();
check(!str_contains($pendingHtml, 'Edited Client') && !str_contains($pendingHtml, 'value="New Profession"'), 'Pending feedback and its profession are hidden from public output');
$admin->approve($item->id);
$approvedHtml = $site->clientFeedback()->render();
check(str_contains($approvedHtml, 'Edited Client') && str_contains($approvedHtml, 'value="New Profession"'), 'Approved feedback and new profession appear on the public page');
check(str_contains($approvedHtml, '&lt;script&gt;') && !str_contains($approvedHtml, '<script>alert(1)</script>'), 'User comments are HTML escaped');
check(substr_count($approvedHtml, 'testimonial-item style-two') === substr_count($pendingHtml, 'testimonial-item style-two') + 1, 'Existing testimonial cards are preserved');
check(str_contains($admin->index()->render(), 'Edited Client') && str_contains($admin->edit($item->id)->render(), 'name="is_approved"'), 'Admin list and edit views render');
$compiler = $app['blade.compiler'];
foreach (['presets/default/client_feedback', 'admin/client_feedback/index', 'admin/client_feedback/edit'] as $view) {
    $compiler->compile(resource_path("views/{$view}.blade.php"));
}
check(true, 'Feedback Blade views compile');
$routes = $app['router']->getRoutes();
check(in_array('admin', $routes->getByName('admin.client-feedback.approve')->gatherMiddleware()), 'Approval route requires admin authentication');
check(in_array('throttle:3,10', $routes->getByName('public.client.feedback.store')->gatherMiddleware()), 'Public submission is rate limited');

$admin->destroy($item->id);
check(App\Models\ClientFeedback::count() === 0, 'Admin can delete feedback');
$migration->down();
echo "All feedback checks passed using SQLite memory database.\n";
