<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\ExposureRequest;
use Inertia\Inertia;
use App\Models\Exposure;
use Config;

class ExposureController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(Exposure::class, 'exposure');
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Exposures/Index', [
      'filters' => Request::all('search', 'status', 'trashed', 'sort'),
      'exposures' => Exposure::filter(Request::only('search', 'status', 'trashed', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($exposure, $index) {
          return [
            'id' => $exposure->id,
            'srno' => ($index + 1),
            'name' => $exposure->name,
            'status' => $exposure->status,
            'is_default' => $exposure->is_default,
            'created_at' => $exposure->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $exposure->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', Exposure::class),
        'create' => Auth::guard('admin')->user()->can('create', Exposure::class),
        'update' => Auth::guard('admin')->user()->can('update', Exposure::class),
        'delete' => Auth::guard('admin')->user()->can('delete', Exposure::class),
      ],
    ]);
  }

  /**
   * Show the resource create view.
   * @return Response
   */
  public function create()
  {
    return Inertia::render('Exposures/Create');
  }

  /**
   * Save the specified resource.
   * @param ExposureRequest $request
   * @return Response
   */
  public function store(ExposureRequest $request)
  {
    try {
      Exposure::create([
        'name' => Request::get('name'),
      ]);
      return Redirect::route('admin.exposures.index')->with('success', 'Exposure has been created successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.exposures.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the resource edit view.
   * @param Exposure $exposure
   * @return Response
   */
  public function edit(Exposure $exposure)
  {
    return Inertia::render('Exposures/Edit', [
      'exposure' => [
        'id' => $exposure->id,
        'name' => $exposure->name,
        'created_at' => $exposure->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $exposure->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $exposure),
      ],
    ]);
  }
  /**
   * Update the specified resource.
   * @param ExposureRequest $request
   * @param Exposure $exposure
   * @return Response
   */
  public function update(ExposureRequest $request, Exposure $exposure)
  {
    try {
      $exposure->update(Request::only('name', 'status'));
      return Redirect::route('admin.exposures.index')->with('success', 'Exposure has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.exposures.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the specified resource.
   * @param Exposure $exposure
   * @return Response
   */
  public function show(Exposure $exposure)
  {
    return Inertia::render('Exposures/Show', [
      'exposure' => [
        'id' => $exposure->id,
        'name' => $exposure->name,
        'created_at' => $exposure->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $exposure->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $exposure),
      ],
    ]);
  }

  /**
   * Destroy the specified resource.
   * @param Exposure $exposure
   * @return Response
   */
  public function destroy(Exposure $exposure)
  {
    try {
      $exposure->delete();
      return Redirect::back()->with('success', 'Exposure has been deleted successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Restore the specified resource.
   * @param Exposure $exposure
   * @return Response
   */
  public function restore(Exposure $exposure)
  {
    try {
      $exposure->restore();
      return Redirect::back()->with('success', 'Exposure has been successfully restored.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Toggle status of the specified resource.
   * @param Exposure $exposure
   * @return Response
   */
  public function toggleStatus(Exposure $exposure)
  {
    try {
      $exposure->status = $exposure->status ? Exposure::IN_ACTIVE : Exposure::ACTIVE;
      $exposure->save();
      return Redirect::back()->with('success', 'Exposure status has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }
}
