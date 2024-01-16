<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Jenssegers\Agent\Agent;
use App\Models\Page;

$agent = new Agent();

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        if ($request->is("admin/*")) {
            $this->rootView = "admin";
        }

        $agent = new Agent();

        if (Auth::getDefaultDriver() == 'admin') {
            return array_merge(parent::share($request), [
                'auth' => function () use ($request) {
                    return [
                        'user' => $request->user() ? [
                            'id' => $request->user()->id,
                            'first_name' => $request->user()->first_name,
                            'last_name' => $request->user()->last_name,
                            'email' => $request->user()->email,
                            'phone' => $request->user()->phone,
                            'photo' => $request->user()->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                            'is_active' => $request->user()->is_active,
                        ] : null,
                    ];
                },
                'can' => $request->user() ? [
                    'access_users' => $request->user()->can('viewAny', \App\Models\User::class),
                    'access_faqs' => $request->user()->can('viewAny', \App\Models\Faq::class),
                    'access_pages' => $request->user()->can('viewAny', \App\Models\Page::class),
                    'access_settings' => $request->user()->can('viewAny', \App\Models\Setting::class),
                    'access_enquiries' => $request->user()->can('viewAny', \App\Models\Enquiry::class),
                    'access_email_templates' => $request->user()->can('viewAny', \App\Models\EmailTemplate::class),
                    'access_testimonials' => $request->user()->can('viewAny', \App\Models\Testimonial::class),
                    'access_systems' => $request->user()->can('viewAny', \App\Models\System::class),
                    'access_locations' => $request->user()->can('viewAny', \App\Models\Location::class),
                    'access_sub_locations' => $request->user()->can('viewAny', \App\Models\Sublocation::class),
                ] : [],
                'settings' => Yaml::parse(Storage::disk('local')->get('settings.yml')),
                'flash' => function () use ($request) {
                    return [
                        'success' => $request->session()->get('success'),
                        'error' => $request->session()->get('error'),
                        'warning' => $request->session()->get('warning'),
                    ];
                },
                'isMobile' => $agent->isMobile()
            ]);
        } else {
            return array_merge(parent::share($request), [
                'auth' => function () use ($request) {
                    return [
                        'user' => Auth::guard('user')->check() ? [
                            'id' => Auth::guard('user')->user()->id,
                            'first_name' => Auth::guard('user')->user()->first_name,
                            'last_name' => Auth::guard('user')->user()->last_name,
                            'email' => Auth::guard('user')->user()->email,
                            'phone' => Auth::guard('user')->user()->phone,
                            'photo' => Auth::guard('user')->user()->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                            'is_active' => Auth::guard('user')->user()->is_active,
                        ] : null,
                    ];
                },
                'cms_pages' => Page::get(),
                'settings' => Yaml::parse(Storage::disk('local')->get('settings.yml')),
                'isProjectScreen' => false,
                'currentAnalysis' => session('projectID') ? true : false,
                'flash' => function () use ($request) {
                    return [
                        'success' => $request->session()->get('success'),
                        'error' => $request->session()->get('error'),
                        'warning' => $request->session()->get('warning'),
                    ];
                },
                'isMobile' => $agent->isMobile()
            ]);
        }
    }
}
