<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\HelpTip;
use Inertia\Inertia;
use Config;
use App\Http\Requests\Admin\HelpTipsRequest;

class HelpTipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('HelpTips/Index', [
            'filters' => Request::all('search', 'trashed', 'sort'),
            'tips' => HelpTip::filter(Request::only('search', 'trashed', 'sort'))
                ->paginate(Config('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($helpTip, $index) {
                    return [
                        'id'        => $helpTip->id,
                        'srno'      => ($index + 1),
                        'title'     => $helpTip->title,
                        'category'  => $helpTip->category,
                        'slug'      => $helpTip->slug,
                        'created_at' => $helpTip->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $helpTip->deleted_at,
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
        return Inertia::render('HelpTips/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HelpTipsRequest $request)
    {
        try {
            HelpTip::create([
                'title'        => Request::get('title'),
                'category'     => Request::get('category'),
                'slug'         => Str::slug(Request::get('title') . '_' . Request::get('category'), '_'),
                'content'      => Request::get('content'),
            ]);
            return Redirect::route('admin.help-tips.index')->with('success', 'HelpTips has been created successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.help-tips.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HelpTip  $helpTip
     * @return \Illuminate\Http\Response
     */
    public function show(HelpTip $helpTip)
    {
        return Inertia::render('HelpTips/Show', [
            'tip' => [
                'id' => $helpTip->id,
                'title' => $helpTip->title,
                'slug' => $helpTip->slug,
                'content' => $helpTip->content,
                'created_at' => $helpTip->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HelpTip  $helpTip
     * @return \Illuminate\Http\Response
     */
    public function edit(HelpTip $helpTip)
    {
        return Inertia::render('HelpTips/Edit', [
            'page' => [
                'id'        => $helpTip->id,
                'title'     => $helpTip->title,
                'category'  => $helpTip->category,
                'content'   => $helpTip->content,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HelpTip  $helpTip
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HelpTip $helpTip)
    {
        try {
            $helpTip->update(Request::only('title', 'slug', 'content', 'category'));
            return Redirect::route('admin.help-tips.index')->with('success', 'Help Tips has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.help-tips.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HelpTip  $helpTip
     * @return \Illuminate\Http\Response
     */
    public function destroy(HelpTip $helpTip)
    {
        //
    }
}
