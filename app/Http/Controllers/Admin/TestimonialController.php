<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TestimonialRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Inertia\Inertia;
use Config;

class TestimonialController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Testimonial::class, 'testimonial');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Testimonials/Index', [
            'filters' => Request::all('search', 'status', 'trashed', 'sort'),
            'testimonials' => Testimonial::filter(Request::only('search', 'status', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($testimonial, $index) {
                    return [
                        'id' => $testimonial->id,
                        'srno' => ($index + 1),
                        'title' => $testimonial->title,
                        'name' => $testimonial->name,
                        'photo' => $testimonial->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                        'is_active' => $testimonial->is_active,
                        'created_at' => $testimonial->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $testimonial->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', Testimonial::class),
                'create' => Auth::guard('admin')->user()->can('create', Testimonial::class),
                'update' => Auth::guard('admin')->user()->can('update', Testimonial::class),
                'delete' => Auth::guard('admin')->user()->can('delete', Testimonial::class),
            ],
        ]);
    }

    /**
     * Show the resource create view.
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Testimonials/Create');
    }

    /**
     * Save the specified resource.
     * @param TestimonialRequest $request
     * @return Response
     */
    public function store(TestimonialRequest $request)
    {
        try {
            Testimonial::create([
                'title' => Request::get('title'),
                'name' => Request::get('name'),
                'description' => Request::get('description'),
                'photo_path' => Request::file('photo') ? Request::file('photo')->store('testimonials', 'public') : null,
            ]);
            return Redirect::route('admin.testimonials.index')->with('success', 'Testimonial has been created successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.testimonials.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the resource edit view.
     * @param Testimonial $testimonial
     * @return Response
     */
    public function edit(Testimonial $testimonial)
    {
        return Inertia::render('Testimonials/Edit', [
            'testimonial' => [
                'id' => $testimonial->id,
                'title' => $testimonial->title,
                'name' => $testimonial->name,
                'description' => $testimonial->description,
                'photo' => $testimonial->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                'created_at' => $testimonial->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                'deleted_at' => $testimonial->deleted_at,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $testimonial),
            ],
        ]);
    }
    /**
     * Update the specified resource.
     * @param TestimonialRequest $request
     * @param Testimonial $testimonial
     * @return Response
     */
    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        try {
            $testimonial->update(Request::only('title', 'name', 'description'));
            if (Request::file('photo')) {
                $testimonial->update(['photo_path' => Request::file('photo')->store('testimonials')]);
            }
            return Redirect::route('admin.testimonials.index')->with('success', 'Testimonial has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.testimonials.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the specified resource.
     * @param Testimonial $testimonial
     * @return Response
     */
    public function show(Testimonial $testimonial)
    {
        return Inertia::render('Testimonials/Show', [
            'testimonial' => [
                'id' => $testimonial->id,
                'title' => $testimonial->title,
                'name' => $testimonial->name,
                'description' => $testimonial->description,
                'photo' => $testimonial->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                'created_at' => $testimonial->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                'deleted_at' => $testimonial->deleted_at,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $testimonial),
            ],
        ]);
    }

    /**
     * Destroy the specified resource.
     * @param Testimonial $testimonial
     * @return Response
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            $testimonial->delete();
            return Redirect::back()->with('success', 'Testimonial has been deleted successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Restore the specified resource.
     * @param Testimonial $testimonial
     * @return Response
     */
    public function restore(Testimonial $testimonial)
    {
        try {
            $testimonial->restore();
            return Redirect::back()->with('success', 'Testimonial has been successfully restored.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Toggle status of the specified resource.
     * @param Testimonial $testimonial
     * @return Response
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        try {
            $testimonial->is_active = $testimonial->is_active ? Testimonial::IN_ACTIVE : Testimonial::ACTIVE;
            $testimonial->save();
            return Redirect::back()->with('success', 'Testimonial status has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
