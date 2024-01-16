<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\System;
use Inertia\Inertia;
use Config;

class SystemController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(System::class, 'system');
  }

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Systems/Index', [
      'filters' => Request::all('search', 'status', 'sort'),
      'systems' => System::isDependent()->filter(Request::only('search', 'status', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($system, $index) {
          return [
            'id' => $system->id,
            'srno' => ($index + 1),
            'name' => $system->name,
            'status' => $system->status,
            'is_base' => $system->is_base,
            'created_at' => $system->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $system->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', System::class),
        'update' => Auth::guard('admin')->user()->can('update', System::class),
      ],
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(Request $request)
  {
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
  }


  /**
     * Toggle status of the specified resource.
     * @param System $system
     * @return Response
     */
    public function toggleStatus(System $system)
    {
        try {
            $system->status = $system->status ? System::IN_ACTIVE : System::ACTIVE;
            $system->save();
            return Redirect::back()->with('success', 'System status has been updated successfully.');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
