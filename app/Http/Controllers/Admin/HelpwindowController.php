<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Helpwindow;
use Inertia\Inertia;
use Config;

class HelpwindowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Helpwindow/Index', [
            'filters' => Request::all('search', 'trashed', 'sort'),
            'pages' => Helpwindow::filter(Request::only('search', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($helpwindow, $index) {
                    return [
                        'id' => $helpwindow->id,
                        'srno' => ($index + 1),
                        'title' => $helpwindow->title,
                        'created_at' => $helpwindow->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $helpwindow->deleted_at,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Helpwindow  $helpwindow
     * @return \Illuminate\Http\Response
     */
    public function show(Helpwindow $helpwindow)
    {
        return Inertia::render('Helpwindow/Show', [
            'page' => [
                'id' => $helpwindow->id,
                'title' => $helpwindow->title,
                'meta_title' => $helpwindow->meta_title,
                'meta_keyword' => $helpwindow->meta_keyword,
                'meta_description' => $helpwindow->meta_description,
                'excerpt' => $helpwindow->excerpt,
                'content' => $helpwindow->content,
                'created_at' => $helpwindow->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Helpwindow  $helpwindow
     * @return \Illuminate\Http\Response
     */
    public function edit(Helpwindow $helpwindow)
    {
        return Inertia::render('Helpwindow/Edit', [
            'page' => [
                'id' => $helpwindow->id,
                'title' => $helpwindow->title,
                'meta_title' => $helpwindow->meta_title,
                'meta_keyword' => $helpwindow->meta_keyword,
                'meta_description' => $helpwindow->meta_description,
                'excerpt' => $helpwindow->excerpt,
                'content' => $helpwindow->content,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Helpwindow  $helpwindow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Helpwindow $helpwindow)
    {
        try {
            $helpwindow->update(Request::only('title', 'excerpt', 'content', 'meta_title', 'meta_keyword', 'meta_description'));
            return Redirect::route('admin.helpwindows.index')->with('success', 'Help Window has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.helpwindows.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Helpwindow  $helpwindow
     * @return \Illuminate\Http\Response
     */
    public function destroy(Helpwindow $helpwindow)
    {
        //
    }
}
