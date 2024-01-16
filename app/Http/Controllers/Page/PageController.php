<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\Helpwindow;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->first();
        return Inertia::render('Pages/Show', [
            'cms_page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => $page->content,
                'created_at' => $page->created_at->format('M/d/Y'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        //
    }

    public function getHelpForPage($slug)
    {
        if($slug==='individual-cost'){
            $slug='individual-costs';
        }
        
        if ($slug) {
            $help = Helpwindow::where('slug', $slug)->first();
            
            if ($help) {
                return Inertia::render('Help/Show', [
                    'help' => [
                        'id' => $help->id,
                        'title' => $help->title,
                        'content' => $help->content,
                    ],
                    'isProjectScreen' => true
                ]);
            } else {
                abort(404, 'Page Not Found.');
            }
        }
    }

    public function overview()
    {


        $overview = Helpwindow::where('slug', 'overview')->first();
        if ($overview) {
            return Inertia::render('Overview/Show', [
                'overview' => [
                    'id' => $overview->id,
                    'title' => $overview->title,
                    'content' => $overview->content,
                ],
            ]);
        } else {
            abort(404, 'Page Not Found.');
        }
    }
}
