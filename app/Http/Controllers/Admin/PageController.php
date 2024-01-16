<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\PageRequest;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Page;
use Config;

class PageController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Page::class, 'page');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Pages/Index', [
            'filters' => Request::all('search', 'trashed', 'sort'),
            'pages' => Page::filter(Request::only('search', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($page, $index) {
                    return [
                        'id' => $page->id,
                        'srno' => ($index + 1),
                        'title' => $page->title,
                        'created_at' => $page->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $page->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', Page::class),
                'update' => Auth::guard('admin')->user()->can('update', Page::class),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Pages/Create');
    }

    /**
     * Show the resource edit view.
     * @param Page $faq
     * @return Response
     */
    public function edit(Page $page)
    {
        return Inertia::render('Pages/Edit', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'meta_title' => $page->meta_title,
                'meta_keyword' => $page->meta_keyword,
                'meta_description' => $page->meta_description,
                'excerpt' => $page->excerpt,
                'content' => $page->content,
            ],
        ]);
    }
    /**
     * Update the specified resource.
     * @param PageRequest $request
     * @param Page $page
     * @return Response
     */
    public function update(PageRequest $request, Page $page)
    {
        try {
            $page->update(Request::only('title', 'excerpt', 'content', 'meta_title', 'meta_keyword', 'meta_description'));
            return Redirect::route('admin.pages.index')->with('success', 'Page has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.pages.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Show the specified resource.
     * @param Page $page
     * @return Response
     */
    public function show(Page $page)
    {
        return Inertia::render('Pages/Show', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'meta_title' => $page->meta_title,
                'meta_keyword' => $page->meta_keyword,
                'meta_description' => $page->meta_description,
                'excerpt' => $page->excerpt,
                'content' => $page->content,
                'created_at' => $page->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            ],
        ]);
    }
    /**
     * Save the specified resource.
     * @param PageRequest $request
     * @return Response
     */
    public function store(PageRequest $request)
    {
        try {
            Page::create([
                'title'             => Request::get('title'),
                'meta_title'        => Request::get('meta_title'),
                'meta_keyword'      => Request::get('meta_keyword'),
                'meta_description'  => Request::get('meta_description'),
                'excerpt'           => Request::get('excerpt'),
                'content'           => Request::get('content'),
                'slug'              => Str::slug(Request::get('title'), '-'),
            ]);
            return Redirect::route('admin.pages.index')->with('success', 'Page has been created successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.pages.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }
    /**
     * Destroy the specified resource.
     * @param Page $faq
      */
    public function destroy(Page $page)
    {
        try {
            $page->delete();
            return Redirect::back()->with('success', 'Page has been deleted successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
