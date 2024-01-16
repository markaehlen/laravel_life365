<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\LocationRequest;
use Inertia\Inertia;
use App\Models\Location;
use Config;

class LocationController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(Location::class, 'location');
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Locations/Index', [
      'filters' => Request::all('search', 'status', 'trashed', 'sort'),
      'locations' => Location::filter(Request::only('search', 'status', 'trashed', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($location, $index) {
          return [
            'id' => $location->id,
            'srno' => ($index + 1),
            'name' => $location->name,
            'status' => $location->status,
            'is_default' => $location->is_default,
            'created_at' => $location->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $location->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', Location::class),
        'create' => Auth::guard('admin')->user()->can('create', Location::class),
        'update' => Auth::guard('admin')->user()->can('update', Location::class),
        'delete' => Auth::guard('admin')->user()->can('delete', Location::class),
      ],
    ]);
  }

  /**
   * Show the resource create view.
   * @return Response
   */
  public function create()
  {
    return Inertia::render('Locations/Create');
  }

  /**
   * Save the specified resource.
   * @param LocationRequest $request
   * @return Response
   */
  public function store(LocationRequest $request)
  {
    try {
      Location::create([
        'name' => Request::get('name'),
      ]);
      return Redirect::route('admin.locations.index')->with('success', 'Location has been created successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.locations.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the resource edit view.
   * @param Location $location
   * @return Response
   */
  public function edit(Location $location)
  {
    return Inertia::render('Locations/Edit', [
      'location' => [
        'id' => $location->id,
        'name' => $location->name,
        'created_at' => $location->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $location->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $location),
      ],
    ]);
  }
  /**
   * Update the specified resource.
   * @param LocationRequest $request
   * @param Location $location
   * @return Response
   */
  public function update(LocationRequest $request, Location $location)
  {
    try {
      $location->update(Request::only('name', 'status'));
      return Redirect::route('admin.locations.index')->with('success', 'Location has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.locations.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the specified resource.
   * @param Location $location
   * @return Response
   */
  public function show(Location $location)
  {
    return Inertia::render('Locations/Show', [
      'location' => [
        'id' => $location->id,
        'name' => $location->name,
        'created_at' => $location->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $location->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $location),
      ],
    ]);
  }

  /**
   * Destroy the specified resource.
   * @param Location $location
   * @return Response
   */
  public function destroy(Location $location)
  {
    try {
      $location->delete();
      return Redirect::back()->with('success', 'Location has been deleted successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Restore the specified resource.
   * @param Location $location
   * @return Response
   */
  public function restore(Location $location)
  {
    try {
      $location->restore();
      return Redirect::back()->with('success', 'Location has been successfully restored.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Toggle status of the specified resource.
   * @param Location $location
   * @return Response
   */
  public function toggleStatus(Location $location)
  {
    try {
      $location->status = $location->status ? Location::IN_ACTIVE : Location::ACTIVE;
      $location->save();
      return Redirect::back()->with('success', 'Location status has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }
}
