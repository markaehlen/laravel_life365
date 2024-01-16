<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\EcoparameterRequest;
use Inertia\Inertia;
use App\Models\Ecoparameter;
use App\Models\Unit;
use Config;

class EcoparameterController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(Ecoparameter::class, 'ecoparameter');
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Ecoparameters/Index', [
      'filters' => Request::all('search', 'status', 'trashed', 'sort'),
      'sections' => config('units.sections'),
      'rate_types' => config('units.parameter_rate_types'),
      'ecoparameters' => Ecoparameter::filter(Request::only('search', 'status', 'trashed', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($ecoparameter, $index) {
          return [
            'id' => $ecoparameter->id,
            'srno' => ($index + 1),
            'name' => $ecoparameter->name,
            'api_identifier' => $ecoparameter->identifier,
            // 'section' => config('units.sections')[$ecoparameter->section],
            'type' => $ecoparameter->type ? $ecoparameter->type : null,
            'default_value' => $ecoparameter->default_value,
            'default_unit' => $ecoparameter->default_unit ? Unit::find($ecoparameter->default_unit) : '',
            'created_at' => $ecoparameter->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $ecoparameter->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', Ecoparameter::class),
        'create' => Auth::guard('admin')->user()->can('create', Ecoparameter::class),
        'update' => Auth::guard('admin')->user()->can('update', Ecoparameter::class),
        'delete' => Auth::guard('admin')->user()->can('delete', Ecoparameter::class),
      ],
    ]);
  }

  /**
   * Show the resource create view.
   * @return Response
   */
  public function create()
  {
    return Inertia::render('Ecoparameters/Create', [
      'sections' => config('units.sections'),
      'rate_types' => config('units.parameter_rate_types'),
    ]);
  }

  /**
   * Save the specified resource.
   * @param EcoparameterRequest $request
   * @return Response
   */
  public function store(EcoparameterRequest $request)
  {
    try {
      $ecoparameter = Ecoparameter::create([
        'name' => Request::get('name', 'section', 'type', 'default_value'),
      ]);
      return Redirect::route('admin.ecoparameters.index')->with('success', 'Eco-parameter has been created successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.ecoparameters.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the resource edit view.
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function edit(Ecoparameter $ecoparameter)
  {
    return Inertia::render('Ecoparameters/Edit', [
      'sections' => config('units.sections'),
      'rate_types' => config('units.parameter_rate_types'),
      'ecoparameter' => [
        'id' => $ecoparameter->id,
        'name' => $ecoparameter->name,
        'section' => config('units.sections')[$ecoparameter->section],
        'type' => config('units.parameter_rate_types')[$ecoparameter->type],
        'default_value' => $ecoparameter->default_value,
        'created_at' => $ecoparameter->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $ecoparameter->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $ecoparameter),
      ],
    ]);
  }
  /**
   * Update the specified resource.
   * @param EcoparameterRequest $request
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function update(EcoparameterRequest $request, Ecoparameter $ecoparameter)
  {
    try {
      $ecoparameter->update(Request::only('name', 'section', 'type', 'default_value'));
      return Redirect::route('admin.ecoparameters.index')->with('success', 'Eco-parameter has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.ecoparameters.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the specified resource.
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function show(Ecoparameter $ecoparameter)
  {
    return Inertia::render('Ecoparameters/Show', [
      'ecoparameter' => [
        'id' => $ecoparameter->id,
        'name' => $ecoparameter->name,
        'section' => config('units.sections')[$ecoparameter->section],
        'type' => $ecoparameter->type ? $ecoparameter->type : 'NA',
        'default_value' => $ecoparameter->default_value,
        'created_at' => $ecoparameter->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $ecoparameter->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $ecoparameter),
      ],
    ]);
  }

  /**
   * Destroy the specified resource.
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function destroy(Ecoparameter $ecoparameter)
  {
    try {
      $ecoparameter->delete();
      return Redirect::back()->with('success', 'Eco-parameter has been deleted successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Restore the specified resource.
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function restore(Ecoparameter $ecoparameter)
  {
    try {
      $ecoparameter->restore();
      return Redirect::back()->with('success', 'Eco-parameter has been successfully restored.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Toggle status of the specified resource.
   * @param Ecoparameter $ecoparameter
   * @return Response
   */
  public function toggleStatus(Ecoparameter $ecoparameter)
  {
    try {
      $ecoparameter->status = $ecoparameter->status ? Ecoparameter::IN_ACTIVE : Ecoparameter::ACTIVE;
      $ecoparameter->save();
      return Redirect::back()->with('success', 'Eco-parameter status has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }
}
