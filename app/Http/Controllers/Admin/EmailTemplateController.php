<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\EmailTemplateRequest;
use App\Models\EmailTemplate;
use Inertia\Inertia;
use Config;

class EmailTemplateController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(EmailTemplate::class, 'email_template');
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('EmailTemplates/Index', [
            'filters' => Request::all('search', 'trashed', 'sort'),
            'emailTemplates' => EmailTemplate::filter(Request::only('search', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($emailTemplate, $index) {
                    return [
                        'id' => $emailTemplate->id,
                        'srno' => ($index + 1),
                        'name' => $emailTemplate->name,
                        'subject' => $emailTemplate->subject,
                        'created_at' => $emailTemplate->created_at->format(Config('constants.ADMIN_DATE_FORMAT'))
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', EmailTemplate::class),
                'update' => Auth::guard('admin')->user()->can('update', EmailTemplate::class),
            ],
        ]);
    }

    /**
     * Show the resource edit view.
     * @param EmailTemplate $emailTemplate
     * @return Response
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return Inertia::render('EmailTemplates/Edit', [
            'emailTemplate' => [
                'id' => $emailTemplate->id,
                'name' => $emailTemplate->name,
                'subject' => $emailTemplate->subject,
                'content' =>  $emailTemplate->content,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $emailTemplate),
            ],
        ]);
    }

    /**
     * Update the specified resource.
     * @param EmailTemplateRequest $request
     * @param EmailTemplate $emailTemplate
     * @return Response
     */
    public function update(EmailTemplateRequest $request, EmailTemplate $emailTemplate)
    {
        try {
            $emailTemplate->update(Request::only('subject', 'content'));
            return Redirect::route('admin.email-templates.index')->with('success', 'Email template has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::route('admin.email-templates.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }

    /**
     * Preview the specified resource.
     * @param Request $request
     * @param EmailTemplate $emailTemplate
     * @return Response
     */
    public function show(Request $request, EmailTemplate $emailTemplate)
    {
        try {
            $modelClass = $emailTemplate->model;
            $mailClass = $emailTemplate->class;
            $model = $modelClass::inRandomOrder()->first();
            return (new $mailClass($model, 'tempusertoken'))->render();
        } catch (\Exception $e) {
            return Redirect::route('admin.email-templates.index')->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
