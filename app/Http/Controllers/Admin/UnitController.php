<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\UnitRequest;
use Inertia\Inertia;
use App\Models\System;
use App\Models\Unit;
use Config;

class UnitController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(Unit::class, 'unit');
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Units/Index', [
      'filters' => Request::all('search', 'status', 'trashed', 'sort'),
      'systems' => System::pluck('name', 'id')->toArray(),
      'unit_types' => config('units.unit_types'),
      'units' => Unit::filter(Request::only('search', 'status', 'trashed', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($unit, $index) {
          return [
            'id' => $unit->id,
            'srno' => ($index + 1),
            'name' => $unit->name,
            'status' => $unit->status,
            'system' => $unit->system,
            'short_hand' => $unit->short_hand,
            'type' => config('units.unit_types')[$unit->type],
            'created_at' => $unit->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $unit->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', Unit::class),
        'create' => Auth::guard('admin')->user()->can('create', Unit::class),
        'update' => Auth::guard('admin')->user()->can('update', Unit::class),
        'delete' => Auth::guard('admin')->user()->can('delete', Unit::class),
      ],
    ]);
  }

  /**
   * Show the resource create view.
   * @return Response
   */
  public function create()
  {
    return Inertia::render('Units/Create', [
      'systems' => System::pluck('name', 'id')->toArray(),
      'unit_types' => config('units.unit_types'),
    ]);
  }

  /**
   * Save the specified resource.
   * @param UnitRequest $request
   * @return Response
   */
  public function store(UnitRequest $request)
  {
    try {
      $unit = Unit::create([
        'name' => Request::get('name'),
        'system_id' => Request::get('system_id'),
        'type' => Request::get('type'),
        'short_hand' => Request::get('short_hand'),
        'conversion_factor' => Request::get('conversion_factor'),
      ]);
      return Redirect::route('admin.units.index')->with('success', 'Unit has been created successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.units.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the resource edit view.
   * @param Unit $unit
   * @return Response
   */
  public function edit(Unit $unit)
  {
    return Inertia::render('Units/Edit', [
      'systems' => System::pluck('name', 'id')->toArray(),
      'unit_types' => config('units.unit_types'),
      'unit' => [
        'id' => $unit->id,
        'name' => $unit->name,
        'system' => $unit->system,
        'short_hand' => $unit->short_hand,
        'type' => $unit->type,
        'conversion_factor' => $unit->conversion_factor,
        'created_at' => $unit->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $unit->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $unit),
      ],
    ]);
  }
  /**
   * Update the specified resource.
   * @param UnitRequest $request
   * @param Unit $system
   * @return Response
   */
  public function update(UnitRequest $request, Unit $unit)
  {
    try {
      $unit->update(Request::only('name', 'short_hand', 'system_id', 'type', 'conversion_factor'));
      return Redirect::route('admin.units.index')->with('success', 'Unit has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.units.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the specified resource.
   * @param Unit $unit
   * @return Response
   */
  public function show(Unit $unit)
  {
    return Inertia::render('Units/Show', [
      'unit' => [
        'id' => $unit->id,
        'name' => $unit->name,
        'system' => $unit->system,
        'type' => config('units.unit_types')[$unit->type],
        'conversion_factor' => $unit->conversion_factor,
        'conversion_reference' => $unit->conversion_reference ? Unit::find($unit->conversion_reference) : null,
        'created_at' => $unit->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $unit->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $unit),
      ],
    ]);
  }

  /**
   * Destroy the specified resource.
   * @param Unit $unit
   * @return Response
   */
  public function destroy(Unit $unit)
  {
    try {
      $unit->delete();
      return Redirect::back()->with('success', 'Unit has been deleted successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Restore the specified resource.
   * @param Unit $unit
   * @return Response
   */
  public function restore(Unit $unit)
  {
    try {
      $unit->restore();
      return Redirect::back()->with('success', 'Unit has been successfully restored.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Toggle status of the specified resource.
   * @param Unit $unit
   * @return Response
   */
  public function toggleStatus(Unit $unit)
  {
    try {
      $unit->status = $unit->status ? Unit::IN_ACTIVE : Unit::ACTIVE;
      $unit->save();
      return Redirect::back()->with('success', 'Unit status has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }
}
