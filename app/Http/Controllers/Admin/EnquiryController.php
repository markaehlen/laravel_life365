<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Enquiry;
use Inertia\Inertia;
use Exception;
use Config;

class EnquiryController extends Controller
{

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Enquiry::class, 'enquiry');
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Enquiries/Index', [
            'filters' => Request::all('search', 'status', 'trashed', 'sort'),
            'enquiries' => Enquiry::filter(Request::only('search', 'status', 'trashed', 'sort'))
                ->paginate(Config::get('pagination.admin_per_page'))
                ->withQueryString()
                ->through(function ($enquiry, $index) {
                    return [
                        'id' => $enquiry->id,
                        'srno' => ($index + 1),
                        'name' => $enquiry->name,
                        'email' => $enquiry->email,
                        'subject' => $enquiry->subject,
                        'is_read' => $enquiry->is_read,
                        'created_at' => $enquiry->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                        'deleted_at' => $enquiry->deleted_at,
                    ];
                }),
            'authorize' => [
                'view' => Auth::guard('admin')->user()->can('viewAny', Enquiry::class),
                'delete' => Auth::guard('admin')->user()->can('delete', Enquiry::class),
            ],
        ]);
    }

    /**
     * Show the specified resource.
     * @param Enquiry $enquiry
     * @return Response
     */
    public function show(Enquiry $enquiry)
    {
        if (!$enquiry->is_read) {
            $enquiry->is_read = true;
            $enquiry->save();
        }
        return Inertia::render('Enquiries/Show', [
            'enquiry' => [
                'id' => $enquiry->id,
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'subject' => $enquiry->subject,
                'message' => $enquiry->message,
                'created_at' => $enquiry->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
                'deleted_at' => $enquiry->deleted_at,
            ],
            'authorize' => [
                'restore' => Auth::guard('admin')->user()->can('restore', $enquiry),
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Enquiry $enquiry
     * @return Response
     */
    public function destroy(Enquiry $enquiry)
    {
        try {
            $enquiry->delete();
            return Redirect::back()->with('success', 'Enquiry deleted.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Something  went wrong. Please try again later.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param Enquiry $enquiry
     * @return Response
     */
    public function restore(Enquiry $enquiry)
    {
        try {
            $enquiry->restore();
            return Redirect::back()->with('success', 'Enquiry restored.');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Something  went wrong. Please try again later.');
        }
    }
}
