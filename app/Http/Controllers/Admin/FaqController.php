<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\FaqRequest;
use Inertia\Inertia;
use App\Models\Faq;
use Config;

class FaqController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Faq::class, 'faq');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Faqs/Index', [
            'filters' => Request::all('search', 'status', 'trashed', 'sort'),
            'faqs' => Faq::filter(Request::only('search', 'status', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($faq, $index) {
                    return [
                        'id' => $faq->id,
                        'srno' => ($index + 1),
                        'title' => $faq->title,
                        'is_active' => $faq->is_active,
                        'created_at' => $faq->created_at->format('M/d/Y'),
                        'deleted_at' => $faq->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', Faq::class),
                'create' => Auth::guard('admin')->user()->can('create', Faq::class),
                'update' => Auth::guard('admin')->user()->can('update', Faq::class),
                'delete' => Auth::guard('admin')->user()->can('delete', Faq::class),
            ],
        ]);
    }

    /**
     * Show the resource create view.
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Faqs/Create');
    }

    /**
     * Save the specified resource.
     * @param FaqRequest $request
     * @return Response
     */
    public function store(FaqRequest $request)
    {
        try {
            Faq::create([
                'title' => Request::get('title'),
                'description' => Request::get('description'),
            ]);
            return Redirect::route('admin.faqs.index')->with('success', 'Faq has been created successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.faqs.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the resource edit view.
     * @param Faq $faq
     * @return Response
     */
    public function edit(Faq $faq)
    {
        return Inertia::render('Faqs/Edit', [
            'faq' => [
                'id' => $faq->id,
                'title' => $faq->title,
                'description' => $faq->description,
                'created_at' => $faq->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                'deleted_at' => $faq->deleted_at,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $faq),
            ],
        ]);
    }
    /**
     * Update the specified resource.
     * @param FaqRequest $request
     * @param Faq $faq
     * @return Response
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        try {
            $faq->update(Request::only('title', 'description'));
            return Redirect::route('admin.faqs.index')->with('success', 'Faq has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.faqs.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the specified resource.
     * @param Faq $faq
     * @return Response
     */
    public function show(Faq $faq)
    {
        return Inertia::render('Faqs/Show', [
            'faq' => [
                'id' => $faq->id,
                'title' => $faq->title,
                'description' => $faq->description,
                'created_at' => $faq->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                'deleted_at' => $faq->deleted_at,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $faq),
            ],
        ]);
    }

    /**
     * Destroy the specified resource.
     * @param Faq $faq
     * @return Response
     */
    public function destroy(Faq $faq)
    {
        try {
            $faq->delete();
            return Redirect::back()->with('success', 'Faq has been deleted successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Restore the specified resource.
     * @param Faq $faq
     * @return Response
     */
    public function restore(Faq $faq)
    {
        try {
            $faq->restore();
            return Redirect::back()->with('success', 'Faq has been successfully restored.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Toggle status of the specified resource.
     * @param Faq $faq
     * @return Response
     */
    public function toggleStatus(Faq $faq)
    {
        try {
            $faq->is_active = $faq->is_active ? Faq::IN_ACTIVE : Faq::ACTIVE;
            $faq->save();
            return Redirect::back()->with('success', 'Faq status has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
