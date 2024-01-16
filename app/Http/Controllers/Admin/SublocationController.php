<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Admin\SublocationRequest;
use Inertia\Inertia;
use App\Models\Location;
use App\Models\Exposure;
use App\Models\Sublocation;
use Config;

class SublocationController extends Controller
{

  /**
   * Create the controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->authorizeResource(Sublocation::class, 'sublocation');
  }

  /**
   * Display a listing of the resource.
   * @return Response
   */
  public function index()
  {
    return Inertia::render('Sublocations/Index', [
      'filters' => Request::all('search', 'status', 'trashed', 'sort'),
      'locations' => Location::pluck('name', 'id')->toArray(),
      'exposures' => Exposure::pluck('name', 'id')->toArray(),
      'sublocations' => Sublocation::filter(Request::only('search', 'status', 'trashed', 'sort'))
        ->paginate(Config::get('pagination.admin_per_page'))
        ->withQueryString()
        ->through(function ($sublocation, $index) {
          return [
            'id' => $sublocation->id,
            'srno' => ($index + 1),
            'name' => $sublocation->name,
            'status' => $sublocation->status,
            'is_default' => $sublocation->is_default,
            'location' => $sublocation->location,
            'exposures' => $sublocation->exposures,
            'created_at' => $sublocation->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
            'deleted_at' => $sublocation->deleted_at,
          ];
        }),
      'authorize' => [
        'view' => Auth::guard('admin')->user()->can('viewAny', Sublocation::class),
        'create' => Auth::guard('admin')->user()->can('create', Sublocation::class),
        'update' => Auth::guard('admin')->user()->can('update', Sublocation::class),
        'delete' => Auth::guard('admin')->user()->can('delete', Sublocation::class),
      ],
    ]);
  }

  /**
   * Show the resource create view.
   * @return Response
   */
  public function create()
  {
    $exposures = Exposure::all();
    $outerArr = [];
    foreach ($exposures as $key => $exposure) {
      $innerArr = [];
      $innerArr['label'] = $exposure->name;
      $innerArr['value'] = $exposure->id;
      $outerArr[] = $innerArr;
    }

    return Inertia::render('Sublocations/Create', [
      'locations' => Location::pluck('name', 'id')->toArray(),
      'exposures' => $outerArr,
    ]);
  }

  /**
   * Save the specified resource.
   * @param SublocationRequest $request
   * @return Response
   */
  public function store(SublocationRequest $request)
  {
    try {
      $sublocation = Sublocation::create([
        'name' => Request::get('name'),
        'location_id' => Request::get('location_id'),
        'is_marine' => Request::get('is_marine'),
        'jan_temp' => Request::get('jan_temp'),
        'feb_temp' => Request::get('feb_temp'),
        'mar_temp' => Request::get('mar_temp'),
        'apr_temp' => Request::get('apr_temp'),
        'may_temp' => Request::get('may_temp'),
        'jun_temp' => Request::get('jun_temp'),
        'jul_temp' => Request::get('jul_temp'),
        'aug_temp' => Request::get('aug_temp'),
        'sep_temp' => Request::get('sep_temp'),
        'oct_temp' => Request::get('oct_temp'),
        'nov_temp' => Request::get('nov_temp'),
        'dec_temp' => Request::get('dec_temp'),
        'avg_temp' => Request::get('avg_temp'),
        'snow_day' => Request::get('snow_day'),
        'mean_rh_perc' => Request::get('mean_rh_perc'),
        'build_up' => Request::get('build_up'),
        'std_dev' => Request::get('std_dev'),
        'max_cs' => Request::get('max_cs'),
        'cost_zerofourtwo' => Request::get('cost_zerofourtwo'),
        'cost_black' => Request::get('cost_black'),
        'cost_stainless' => Request::get('cost_stainless'),
        'cost_epoxy' => Request::get('cost_epoxy'),
        'membrane' => Request::get('membrane'),
        'cost_sealer' => Request::get('cost_sealer')
      ]);
      $exposures = [];
      if ($request->has('exposures')) {
        $selected_exposures = $request->exposures;
        for ($i = 0; $i < sizeof($selected_exposures); $i++) {
          array_push($exposures, $selected_exposures[$i]['value']);
        }
      }
      $sublocation->exposures()->sync($exposures);
      return Redirect::route('admin.sublocations.index')->with('success', 'Sub-location has been created successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.sublocations.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the resource edit view.
   * @param Sublocation $sublocation
   * @return Response
   */
  public function edit(Sublocation $sublocation)
  {
    //For All Options
    $exposures = Exposure::all();
    $outerArr = [];
    foreach ($exposures as $key => $exposure) {
      $innerArr = [];
      $innerArr['label'] = $exposure->name;
      $innerArr['value'] = $exposure->id;
      $outerArr[] = $innerArr;
    }

    //For Selected Options
    $selected_outerArr = [];
    foreach ($sublocation->exposures as $exposure) {
      $innerArr = [];
      $innerArr['label'] = $exposure->name;
      $innerArr['value'] = $exposure->id;
      $selected_outerArr[] = $innerArr;
    }

    return Inertia::render('Sublocations/Edit', [
      'locations' => Location::pluck('name', 'id')->toArray(),
      'exposures' => $outerArr,
      'sublocation' => [
        'id' => $sublocation->id,
        'name' => $sublocation->name,
        'location' => $sublocation->location,
        'is_marine' => $sublocation->is_marine,
        'jan_temp' => $sublocation->jan_temp,
        'feb_temp' => $sublocation->feb_temp,
        'mar_temp' => $sublocation->mar_temp,
        'apr_temp' => $sublocation->apr_temp,
        'may_temp' => $sublocation->may_temp,
        'jun_temp' => $sublocation->jun_temp,
        'jul_temp' => $sublocation->jul_temp,
        'aug_temp' => $sublocation->aug_temp,
        'sep_temp' => $sublocation->sep_temp,
        'oct_temp' => $sublocation->oct_temp,
        'nov_temp' => $sublocation->nov_temp,
        'dec_temp' => $sublocation->dec_temp,
        'avg_temp' => $sublocation->avg_temp,
        'snow_day' => $sublocation->snow_day,
        'mean_rh_perc' => $sublocation->mean_rh_perc,
        'build_up' => $sublocation->build_up,
        'std_dev' => $sublocation->std_dev,
        'max_cs' => $sublocation->max_cs,
        'cost_zerofourtwo' => $sublocation->cost_zerofourtwo,
        'cost_black' => $sublocation->cost_black,
        'cost_stainless' => $sublocation->cost_stainless,
        'cost_epoxy' => $sublocation->cost_epoxy,
        'membrane' => $sublocation->membrane,
        'cost_sealer' => $sublocation->cost_sealer,
        'exposures' => $selected_outerArr,
        'created_at' => $sublocation->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $sublocation->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $sublocation),
      ],
    ]);
  }
  /**
   * Update the specified resource.
   * @param SublocationRequest $request
   * @param Sublocation $location
   * @return Response
   */
  public function update(SublocationRequest $request, Sublocation $sublocation)
  {
    try {
      $sublocation->update(Request::only(
        'name',
        'location_id',
        'is_marine',
        'jan_temp',
        'feb_temp',
        'mar_temp',
        'apr_temp',
        'may_temp',
        'jun_temp',
        'jul_temp',
        'aug_temp',
        'sep_temp',
        'oct_temp',
        'nov_temp',
        'dec_temp',
        'avg_temp',
        'snow_day',
        'mean_rh_perc',
        'build_up',
        'std_dev',
        'max_cs',
        'cost_zerofourtwo',
        'cost_black',
        'cost_stainless',
        'cost_epoxy',
        'membrane',
        'cost_sealer'
      ));
      $exposures = [];
      if ($request->has('exposures')) {
        $selected_exposures = $request->exposures;
        for ($i = 0; $i < sizeof($selected_exposures); $i++) {
          array_push($exposures, $selected_exposures[$i]['value']);
        }
      }
      $sublocation->exposures()->sync($exposures);
      return Redirect::route('admin.sublocations.index')->with('success', 'Sub-location has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::route('admin.sublocations.index')->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Show the specified resource.
   * @param Sublocation $sublocation
   * @return Response
   */
  public function show(Sublocation $sublocation)
  {
    return Inertia::render('Sublocations/Show', [
      'sublocation' => [
        'id' => $sublocation->id,
        'name' => $sublocation->name,
        'location' => $sublocation->location,
        'is_marine' => $sublocation->is_marine,
        'jan_temp' => $sublocation->jan_temp,
        'feb_temp' => $sublocation->feb_temp,
        'mar_temp' => $sublocation->mar_temp,
        'apr_temp' => $sublocation->apr_temp,
        'may_temp' => $sublocation->may_temp,
        'jun_temp' => $sublocation->jun_temp,
        'jul_temp' => $sublocation->jul_temp,
        'aug_temp' => $sublocation->aug_temp,
        'sep_temp' => $sublocation->sep_temp,
        'oct_temp' => $sublocation->oct_temp,
        'nov_temp' => $sublocation->nov_temp,
        'dec_temp' => $sublocation->dec_temp,
        'avg_temp' => $sublocation->avg_temp,
        'snow_day' => $sublocation->snow_day,
        'mean_rh_perc' => $sublocation->mean_rh_perc,
        'build_up' => $sublocation->build_up,
        'std_dev' => $sublocation->std_dev,
        'max_cs' => $sublocation->max_cs,
        'cost_zerofourtwo' => $sublocation->cost_zerofourtwo,
        'cost_black' => $sublocation->cost_black,
        'cost_stainless' => $sublocation->cost_stainless,
        'cost_epoxy' => $sublocation->cost_epoxy,
        'membrane' => $sublocation->membrane,
        'cost_sealer' => $sublocation->cost_sealer,
        'exposures' => $sublocation->exposures,
        'created_at' => $sublocation->created_at->format(Config('constants.ADMIN_DATE_FORMAT')),
        'deleted_at' => $sublocation->deleted_at,
      ],
      'authorize' => [
        'restore' => Auth::guard('admin')->user()->can('restore', $sublocation),
      ],
    ]);
  }

  /**
   * Destroy the specified resource.
   * @param Sublocation $sublocation
   * @return Response
   */
  public function destroy(Sublocation $sublocation)
  {
    try {
      $sublocation->delete();
      return Redirect::back()->with('success', 'Sub-location has been deleted successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Restore the specified resource.
   * @param Sublocation $sublocation
   * @return Response
   */
  public function restore(Sublocation $sublocation)
  {
    try {
      $sublocation->restore();
      return Redirect::back()->with('success', 'Sub-location has been successfully restored.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }

  /**
   * Toggle status of the specified resource.
   * @param Sublocation $sublocation
   * @return Response
   */
  public function toggleStatus(Sublocation $sublocation)
  {
    try {
      $sublocation->status = $sublocation->status ? Sublocation::IN_ACTIVE : Sublocation::ACTIVE;
      $sublocation->save();
      return Redirect::back()->with('success', 'Sub-location status has been updated successfully.');
    } catch (\Exception $e) {
      return Redirect::back()->with('error', 'Something went wrong. Please try again later.');
    }
  }
}
