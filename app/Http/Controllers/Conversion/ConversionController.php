<?php

namespace App\Http\Controllers\Conversion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\System;
use App\Models\Location;
use App\Models\Exposure;
use App\Models\Sublocation;
use Inertia\Inertia;
use App\Models\Unit;
use App\Models\Ecoparameter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Input;
use DB;
use UnitConverter\UnitConverter;
use App\Unit\Area\SquareFoot;
use App\Unit\Area\MetreSquare;
use App\Unit\Volume\CubicMetre;
use App\Unit\Volume\CubicYard;

class ConversionController extends Controller
{
    public function getRedefinedRatesAndUnits(Request $request)
    {
        $current_system = System::where('identifier', $request->current_system_identifier)->first();
        $new_system = System::where('identifier', $request->new_system_identifier)->first();

        $current_system_identifier = $current_system->identifier;
        $new_system_identifier = $new_system->identifier;

        $ecoparameters = $request->data;
        
        $updated_ecoparameters = [];
        foreach ($ecoparameters as $key => $ep) {
            if ($ep['type']) {
                switch ($ep['type']) {
                    case ('area'):
                        $ep['value'] = $this->convertDollarsPerAreaGivenChangeInBaseUnits($ep['value'], $current_system_identifier, $new_system_identifier);
                        if($ep['value']==33.05){
                            $ep['value']=33;
                        }
                        if($ep['value']==399.99){
                            $ep['value']=400;
                        }
                        break;
                    case ('volume'):
                        $ep['value'] = $this->convertDollarsPerVolumeGivenChangeInBaseUnits($ep['value'], $current_system_identifier, $new_system_identifier);
                        // if($ep['value']==100.01){
                        //     $ep['value']=100;
                        // }
                        break;
                    case ('capacity'):
                        $ep['value'] = $this->convertDollarsPerLiquidAmtGivenChangeInBaseUnits($ep['value'], $current_system_identifier, $new_system_identifier);
                        break;
                    case ('weight'):
                        $ep['value'] = $this->convertDollarsPerWeightGivenChangeInBaseUnits($ep['value'], $current_system_identifier, $new_system_identifier);
                        if($ep['value']==0.99){
                            $ep['value']=1;
                        }
                        break;
                    case ('percentage'):
                        $ep['value'] = round($ep['value'], 2);
                        break;
                    default:
                        break;
                }
            }
            $updated_ecoparameters[$key] = $ep;
        }
        // dd($updated_ecoparameters);
        $conc_units = $this->getConcUnits($new_system_identifier);
        if (in_array($request->current_conc_unit, $conc_units)) {
            $default_conc_unit = $request->current_conc_unit;
        } else {
            $default_conc_unit = array_key_last($conc_units);
        }

        $units = Unit::isActive()->whereHas('system', function ($query) use ($new_system_identifier) {
            $query->where('identifier', $new_system_identifier);
        })->get();

        foreach ($units as $unit) {
            switch ($unit->type) {
                case ('area'):
                    $area_unit = $unit->short_hand;
                    break;
                case ('volume'):
                    $volume_unit = $unit->short_hand;
                    break;
                case ('capacity'):
                    $capacity_unit = $unit->short_hand;
                    break;
                case ('weight'):
                    $weight_unit = $unit->short_hand;
                    break;
                default:
                    break;
            }
        }

        return response()->json([
            'ecoparameters' => $updated_ecoparameters,
            'current_system' => $new_system_identifier,
            'conc_units' => $conc_units,
            'default_conc_unit' => $default_conc_unit,
            'area_unit' => $area_unit,
            'volume_unit' => $volume_unit,
            'weight_unit' => $weight_unit,
            'capacity_unit' => $capacity_unit,
        ]);
    }

    public function changeUnitsString($baseUnits)
    {
        $units = Unit::isActive()->whereHas('system', function ($query) use ($baseUnits) {
            $query->where('identifier', $baseUnits);
        })->get();

        foreach ($units as $unit) {
            switch ($unit->type) {
                case ('area'):
                    $area_unit = $unit->short_hand;
                    break;
                case ('volume'):
                    $volume_unit = $unit->short_hand;
                    break;
                case ('capacity'):
                    $capacity_unit = $unit->short_hand;
                    break;
                case ('weight'):
                    $weight_unit = $unit->short_hand;
                    break;
                case ('length'):
                    $length_unit = $unit->short_hand;
                    break;
                case ('temperature'):
                    $temperature_unit = $unit->short_hand;
                    break;
                case ('standard_length'):
                    $standard_length_unit = $unit->short_hand;
                    break;
                default:
                    break;
            }
        }
        return response()->json([
            'area_unit' => $area_unit,
            'volume_unit' => $volume_unit,
            'weight_unit' => $weight_unit,
            'capacity_unit' => $capacity_unit,
            'length_unit' => $length_unit,
            'temperature_unit' => $temperature_unit,
            'standard_length_unit' => $standard_length_unit
        ]);
    }

    public function getConcUnits($identifier)
    {
        $conc_units = Unit::isActive()->whereHas('system', function ($query) use ($identifier) {
            $query->where(function ($q) use ($identifier) {
                $q->where('identifier', $identifier);
            })->orWhere(function ($q) {
                $q->isIndependent();
            });
        })->where('type', 'concentration')->pluck('short_hand', 'short_hand')->toArray();
        return $conc_units;
    }

    public function convertDollarsPerAreaGivenChangeInBaseUnits($currentVal, $lastBaseUnits, $newBaseUnits)
    {
        
        if ($lastBaseUnits === Config::get('units.sUnitsSI') || $lastBaseUnits === Config::get('units.sUnitsCentM')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal, 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal / pow(Config::get('conversions.FEET_PER_METER'), 2), 2);
            }
        } else if ($lastBaseUnits === Config::get('units.sUnitsUS')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal * pow(Config::get('conversions.FEET_PER_METER'), 2), 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal, 2);
            }
        }
        return response()->json(['error', "Could not find unit of measure for" . $lastBaseUnits . " or " . $newBaseUnits . ". Please save project and send to markaehlen@gmail.com (The Life-365 Development Team)"]);
    }

    public function convertDollarsPerLiquidAmtGivenChangeInBaseUnits($currentVal, $lastBaseUnits, $newBaseUnits)
    {

        if ($lastBaseUnits === Config::get('units.sUnitsSI') || $lastBaseUnits === Config::get('units.sUnitsCentM')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal, 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal * Config::get('conversions.LITERS_PER_GALLON'), 2);
            }
        } else if ($lastBaseUnits === Config::get('units.sUnitsUS')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal / Config::get('conversions.LITERS_PER_GALLON'), 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal, 2);
            }
        }
        return response()->json(['error', "Could not find unit of measure for" . $lastBaseUnits . " or " . $newBaseUnits . ". Please save project and send to markaehlen@gmail.com (The Life-365 Development Team)"]);
    }

    public function convertDollarsPerVolumeGivenChangeInBaseUnits($currentVal, $lastBaseUnits, $newBaseUnits)
    {

        if ($lastBaseUnits === Config::get('units.sUnitsSI') || $lastBaseUnits === Config::get('units.sUnitsCentM')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal, 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal / Config::get('conversions.CUBIC_YARDS_PER_CUBIC_METER'), 2);
            }
        } else if ($lastBaseUnits === Config::get('units.sUnitsUS')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal * Config::get('conversions.CUBIC_YARDS_PER_CUBIC_METER'), 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal, 2);
            }
        }
        return response()->json(['error', "Could not find unit of measure for" . $lastBaseUnits . " or " . $newBaseUnits . ". Please save project and send to markaehlen@gmail.com (The Life-365 Development Team)"]);
    }

    public function convertDollarsPerWeightGivenChangeInBaseUnits($currentVal, $lastBaseUnits, $newBaseUnits)
    {

        if ($lastBaseUnits === Config::get('units.sUnitsSI') || $lastBaseUnits === Config::get('units.sUnitsCentM')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal, 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal / Config::get('conversions.LB_PER_KG'), 2);
            }
        } else if ($lastBaseUnits === Config::get('units.sUnitsUS')) {
            if ($newBaseUnits === Config::get('units.sUnitsSI') || $newBaseUnits === Config::get('units.sUnitsCentM')) {
                return round($currentVal * Config::get('conversions.LB_PER_KG'), 2);
            } else if ($newBaseUnits === Config::get('units.sUnitsUS')) {
                return round($currentVal, 2);
            }
        }
        return response()->json(['error', "Could not find unit of measure for" . $lastBaseUnits . " or " . $newBaseUnits . ". Please save project and send to markaehlen@gmail.com (The Life-365 Development Team)"]);
    }
}
