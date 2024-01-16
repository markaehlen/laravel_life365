<?php

/*
|--------------------------------------------------------------------------
| Defaults
|--------------------------------------------------------------------------
|
*/

return [
    // "defaultData" => [
    //     "basicAnalysis" => true,
    //     "computeUncertainty" => false,
    //     "baseUnits" => "SI metric",
    //     "concUnits" => "% wt. conc.",
    //     "tradeNames" => "Generic product names",
    //     "useLocationDefaults" => true,
    //     "useFixedRepairInterval" => true,
    //     "location" => "New York",
    //     "subLocation" => "NEW YORK",
    //     "exposureType" => "Parking Garages",
    //     "ecoparameters" => [
    //         "baseYear" => date('d-m-Y'),
    //         "studyPeriod" => 150,
    //         "inflation" => 1.8,
    //         "discount" => 2.0,
    //         "baseMixCost" => 100,
    //         "repairCost" => 400,
    //         "areaToRepairPct" => 10,
    //         "repairIntervalYrs" => 10,
    //         "costBlackSteel" => 1,
    //         "costStainless" => 6.6,
    //         "costEpoxy" => 1.33,
    //         "costMembrane" => 33,
    //         "costSealer" => 7,
    //         "costInhibitor" => 1.5,
    //     ],
    //     "base_alt_color" => '#1CA085',
    //     "alt1_color" => '#2980B9',
    //     "alt2_color" => '#222F3D',
    //     "alt3_color" => '#F2C511',
    //     "alt4_color" => '#E84B3C',
    //     "alt5_color" => '#A463BF',
    // ],

    "defaultData"=> [
        "basicAnalysis"=> true,
        "computeUncertainty"=> false,
        "baseUnits"=> "SI metric",
        "concUnits"=> "% wt. conc.",
        "tradeNames"=> "Generic product names",
        "useLocationDefaults"=> true,
        "location"=> "New York",
        "subLocation"=> "NEW YORK",
        "exposureType"=> "Parking garages",
        "baseYear"=> 2018,
        "studyPeriod"=> 150,
        "inflation"=> 0.018,
        "discount"=> 0.02,
        "baseMixCost"=> 100,
        "repairCost"=> 400,
        "areaToRepairPct"=> 0.1,
        "repairIntervalYrs"=> 10,
        "useFixedRepairInterval"=> true,
        "costBlackSteel"=> 1,
        "costStainless"=> 6.6,
        "costEpoxy"=> 1.33,
        "costMembrane"=> 33,
        "costSealer"=> 7,
        "costInhibitor"=> 1.5,
       
    ],

    'colors' => [
        "base_alt_color" => '#1CA085',
        "alt1_color" => '#2980B9',
        "alt2_color" => '#222F3D',
        "alt3_color" => '#f97316',
        "alt4_color" => '#E84B3C',
        "alt5_color" => '#A463BF',
    ],

    "structures" => [
        'slabs and walls (1-D)' => 'slabs and walls (1-D)',
        'square column/beams (2-D)' => 'square column/beams (2-D)',
        'circular columns (2-D)' => 'circular columns (2-D)'
    ],

    "rebar_steel_types" => [
        'blackSteel' => 'Black Steel',
        'epoxyCoated' => 'Epoxy Coated',
        '316stainless' => '316 Stainless'
    ],

    "inhibitors" => [
        '<none>' => '<none>',
        'Ca Nitrite - 5 L/cub. met.' => 'Ca Nitrite - 5 L/cub. met.',
        'Ca Nitrite - 10 L/cub. met.' => 'Ca Nitrite - 10 L/cub. met.',
        'Ca Nitrite - 12.5 L/cub. met.' => 'Ca Nitrite - 12.5 L/cub. met.',
        'Ca Nitrite - 15 L/cub. met.' => 'Ca Nitrite - 15 L/cub. met.',
        'Ca Nitrite - 17.5 L/cub. met.' => 'Ca Nitrite - 17.5 L/cub. met.',
        'Ca Nitrite - 20 L/cub. met.' => 'Ca Nitrite - 20 L/cub. met.',
        'Ca Nitrite - 22.5 L/cub. met.' => 'Ca Nitrite - 22.5 L/cub. met.',
        'Ca Nitrite - 25 L/cub. met.' => 'Ca Nitrite - 25 L/cub. met.',
        'Ca Nitrite - 27.5 L/cub. met.' => 'Ca Nitrite - 27.5 L/cub. met.',
        'Ca Nitrite - 30 L/cub. met.' => 'Ca Nitrite - 30 L/cub. met.',
        'A&E - 5 L/cub. met.' => 'A&E - 5 L/cub. met.'
    ],

    "barrier_types" => [
        '<none>' => '<none>',
        'Membrane' => 'Membrane',
        'Sealer' => 'Sealer'
    ],

    "buildup_percentages" => [
        "Marine Tidal Zone" => 0.8,
        "Marine Spray Zone" =>  0.10,
        "Within 800 m of the ocean" => 0.04,
        "Within 1.5 km of the ocean" => 0.02,
        "Parking Garages" => 0.10,
        "Urban Highway Bridges" => 0.10,
        "Rural Highway Bridges" => 0.10,
    ],

    "exposure_max_cs" => [
        "Marine Tidal Zone" => 0.8,
        "Marine Spray Zone" =>  1.0,
        "Within 800 m of the ocean" => 0.6,
        "Within 1.5 km of the ocean" => 0.6,
        "Parking Garages" => 0.8,
        "Urban Highway Bridges" => 0.7,
        "Rural Highway Bridges" => 0.6,
    ],

    "si_acceptable_values" => [
        "slab_thickness_lower_limit" => 80,
        "slab_thickness_upper_limit" => 250,
        "slab_depth_lower_limit" => 10,
        "slab_depth_upper_limit" => 120,
        "column_width_lower_limit" => 227.3,
        "column_width_upper_limit" => 2500,
        "column_depth_lower_limit" => 10,
        "column_depth_upper_limit" => 45,
        "modeled_depth" => 25
    ],

    "us_acceptable_values" => [
        "slab_thickness_lower_limit" => 3,
        "slab_thickness_upper_limit" => 100,
        "slab_depth_lower_limit" => 0.5,
        "slab_depth_upper_limit" => 7,
        "column_width_lower_limit" => 9.1,
        "column_width_upper_limit" => 100,
        "column_depth_lower_limit" => 0.5,
        "column_depth_upper_limit" => 3.6,
        "modeled_depth" => 10
    ],

    "cm_acceptable_values" => [
        "slab_thickness_lower_limit" => 8,
        "slab_thickness_upper_limit" => 25,
        "slab_depth_lower_limit" => 1,
        "slab_depth_upper_limit" => 12,
        "column_width_lower_limit" => 22.7,
        "column_width_upper_limit" => 250,
        "column_depth_lower_limit" => 1,
        "column_depth_upper_limit" => 9,
        "modeled_depth" => 250
    ],

    "structure_default_values" => [
        "slab_thickness_value" => 200,
        "slab_depth_value" => 60,
        "square_column_width_value" => 227.3,
        "square_column_depth_value" => 45,
        "circular_column_width_value" => 227.3,
        "circular_column_depth_value" => 45,
    ],
    'defaultProjectData' => array(
        'project' =>
        array(
            'fileVersion' => NULL,
            'projectData' =>
            array(
                'alts' =>
                array(
                    'alt' =>
                    array(
                        0 =>
                        array(
                            'alternative' =>
                            array(
                                'concreteMixDesign' =>
                                array(
                                    'alpha' => 0,
                                    'beta' => 0,
                                    'ct' => 0.05,
                                    'd28' => 0,
                                    'detailedBarrier' =>
                                    array(
                                        'ageAtFailure' => 0,
                                        'areaCost' => 0,
                                        'barrierName' => '<none>',
                                        'initialEfficiency' => 0,
                                        'reapplyTime' => 0,
                                        'useDefault' => true,
                                    ),
                                    'dRef' => 0,
                                    'hydration' => 25,
                                    'inhibitor' => '<none>',
                                    'initiationInYears' => 0,
                                    'isUserDefineable' => false,
                                    'm' => 0,
                                    'percentClassFFlyAsh' => 0.0,
                                    'percentSilicaFume' => 0.0,
                                    'percentSlag' => 0.0,
                                    'propagationInYears' => 6,
                                    'rebarType' =>
                                    array(
                                        'percentOfTotal' => 0.012,
                                        'type' => 'Black Steel',
                                        'yearsFromInitToFailure' => 6,
                                    ),
                                    'serviceLifeInYears' => 99,
                                    'valuesHaveChanged' => true,
                                    'waterCementRatio' => 0.42,
                                ),
                                'costs' =>
                                array(
                                    0 =>
                                    array(
                                        'costName' => 'Construction cost',
                                        'costNumber' => 0,
                                        'costPrior' => '0',
                                        'costTiming' =>
                                        array(
                                            'endDate' => '0',
                                            'frequency' => '0',
                                            'startDate' => '0',
                                        ),
                                        'costType' => 'indep.',
                                        'quantity' => 2000,
                                        'totalCost' => 386480,
                                        'unitCost' => 193.24,
                                        'unitOfMeasure' => 'cub. met.',
                                    ),
                                    1 =>
                                    array(
                                        'costName' => 'Repair cost',
                                        'costNumber' => 1,
                                        'costPrior' => '0',
                                        'costTiming' =>
                                        array(
                                            'endDate' => '150',
                                            'frequency' => '10',
                                            'startDate' => '99',
                                        ),
                                        'costType' => 'dep.',
                                        'quantity' => 1000,
                                        'totalCost' => 400000,
                                        'unitCost' => 400,
                                        'unitOfMeasure' => 'sq. m.',
                                    ),
                                ),
                                'description' => 'A project that uses the normal mix of concrete',
                                'name' => 'Base case',
                                'userDefinedMixCost' => false,
                                'userMixCost' => 0,
                            ),
                        ),
                        1 =>
                        array(
                            'alternative' =>
                            array(
                                'concreteMixDesign' =>
                                array(
                                    'alpha' => 0,
                                    'beta' => 0,
                                    'ct' => 0.05,
                                    'd28' => 0,
                                    'detailedBarrier' =>
                                    array(
                                        'ageAtFailure' => 0,
                                        'areaCost' => 0,
                                        'barrierName' => '<none>',
                                        'initialEfficiency' => 0,
                                        'reapplyTime' => 0,
                                        'useDefault' => true,
                                    ),
                                    'dRef' => 0,
                                    'hydration' => 25,
                                    'inhibitor' => '<none>',
                                    'initiationInYears' => 0,
                                    'isUserDefineable' => false,
                                    'm' => 0,
                                    'percentClassFFlyAsh' => 0.0,
                                    'percentSilicaFume' => 0.0,
                                    'percentSlag' => 0.0,
                                    'propagationInYears' => 6,
                                    'rebarType' =>
                                    array(
                                        'percentOfTotal' => 0.012,
                                        'type' => 'Black Steel',
                                        'yearsFromInitToFailure' => 6,
                                    ),
                                    'serviceLifeInYears' => 99,
                                    'valuesHaveChanged' => true,
                                    'waterCementRatio' => 0.42,
                                ),
                                'costs' =>
                                array(
                                    0 =>
                                    array(
                                        'costName' => 'Construction cost',
                                        'costNumber' => 0,
                                        'costPrior' => '0',
                                        'costTiming' =>
                                        array(
                                            'endDate' => '0',
                                            'frequency' => '0',
                                            'startDate' => '0',
                                        ),
                                        'costType' => 'indep.',
                                        'quantity' => 2000,
                                        'totalCost' => 386480,
                                        'unitCost' => 193.24,
                                        'unitOfMeasure' => 'cub. met.',
                                    ),
                                    1 =>
                                    array(
                                        'costName' => 'Repair cost',
                                        'costNumber' => 1,
                                        'costPrior' => '0',
                                        'costTiming' =>
                                        array(
                                            'endDate' => '150',
                                            'frequency' => '10',
                                            'startDate' => '99',
                                        ),
                                        'costType' => 'dep.',
                                        'quantity' => 1000,
                                        'totalCost' => 400000,
                                        'unitCost' => 400,
                                        'unitOfMeasure' => 'sq. m.',
                                    ),
                                ),
                                'description' => 'A project that uses the a new mix of concrete',
                                'name' => 'Alternative 1',
                                'userDefinedMixCost' => false,
                                'userMixCost' => 0,
                            ),
                        ),
                    ),
                ),
                'baseUnits' => 'US units',
                'baseYear' => 2023,
                'concentrationUnits' => '% wt. conc.',
                'costs' =>
                array(
                    'areaToRepair' => 0.1,
                    'baseMixCost' => 100,
                    'blackSteel' => 1,
                    'epoxySteel' => 1.33,
                    'inhibitor' => 1.5,
                    'membrane' => 33,
                    'repairCost' => 400,
                    'repairInterval' => 10,
                    'sealant' => 7.000000000000001,
                    'stainlessSteel' => 6.6,
                    'useFixedRepairInterval' => true,
                ),
                'date' => date('m/d/Y'),
                'description' => 'Default settings for a new project',
                //exposure
                'doneByName' => 'Analyst',
                'exposureConditions' => [
                    'setManually' => false,
                    'c1556Sets' => [
                        0 => [
                            'ambientConcentration' => 0.085,
                            'baseUnits' => 'SI metric',
                            'bulkDiffusionCoefficient' => 4.859325694361982E-13,
                            'concentrationUnits' => '% wt. conc.',
                            'dateCreated' => 'October 15, 2012',
                            'days' => 365,
                            'depthConcPairs' => [
                                0 => [
                                    'concPctWt' => 0.368,
                                    'depthMM' => 1.0
                                ],
                                1 => [
                                    'concPctWt' => 0.45,
                                    'depthMM' => 2.0
                                ],
                                2 => [
                                    'concPctWt' => 0.41,
                                    'depthMM' => 3.0
                                ],
                                3 => [
                                    'concPctWt' => 0.326,
                                    'depthMM' => 4.0
                                ],
                                4 => [
                                    'concPctWt' => 0.266,
                                    'depthMM' => 5.0
                                ],
                                5 => [
                                    'concPctWt' => 0.231,
                                    'depthMM' => 6.0
                                ],
                                6 => [
                                    'concPctWt' => 0.175,
                                    'depthMM' => 7.0
                                ],
                                7 => [
                                    'concPctWt' => 0.183,
                                    'depthMM' => 8.0
                                ],
                                8 => [
                                    'concPctWt' => 0.132,
                                    'depthMM' => 9.0
                                ],
                                9 => [
                                    'concPctWt' => 0.124,
                                    'depthMM' => 10.0
                                ],
                                10 => [
                                    'concPctWt' => 0.117,
                                    'depthMM' => 15.0
                                ],
                                11 => [
                                    'concPctWt' => 0.08,
                                    'depthMM' => 20.0
                                ],
                                12 => [
                                    'concPctWt' => 0.078,
                                    'depthMM' => 25.0
                                ],
                            ],
                            'description' => 'Data from the ASTM C1556 Methodology Document',
                            'includeFirstPoint' => false,
                            'maxSurfaceConcentration' => 0.6048407496282759,
                            'name' => 'ASTM C1556 Set',
                            'timeToMax' => 7.0
                            
                        ],
                    ],
                    'useC1556' => true,
                    'c1556SetUsed' => 'ASTM C1556 Set',
                    'maxSurfaceConcentration' => 0.6048407496282759,
                    'timeToBuildUp' => 7.0,
                    'ageAtFirstExposure' => 28,
                    'monthOfFirstExposure' => 0.0
                ],
        
                //
                'exposureLocale' =>
                array(
                    'exposure' => 'Parking garages',
                    'location' => 'New York',
                    'subLocation' => 'NEW YORK',
                    'useThis' => true,
                ),
                'inflationRate' => 0.018,
                'realDiscountRate' => 0.02,
                'structureDimensions' =>
                array(
                    'area' => 10000,
                    'clearCover' => 60,
                    'overallThickness' => 200,
                    'trueThickness' => 200,
                ),
                'studyPeriod' => 150,
                'temperatureHistory' =>
                array(
                    'temp' =>
                    array(
                        0 =>
                        array(
                            'calendarMonth' => 0.5,
                            'temperature' => -0.4,
                        ),
                        1 =>
                        array(
                            'calendarMonth' => 1.5,
                            'temperature' => 0.7,
                        ),
                        2 =>
                        array(
                            'calendarMonth' => 2.5,
                            'temperature' => 5.3,
                        ),
                        3 =>
                        array(
                            'calendarMonth' => 3.5,
                            'temperature' => 10.8,
                        ),
                        4 =>
                        array(
                            'calendarMonth' => 4.5,
                            'temperature' => 16.6,
                        ),
                        5 =>
                        array(
                            'calendarMonth' => 5.5,
                            'temperature' => 21.7,
                        ),
                        6 =>
                        array(
                            'calendarMonth' => 6.5,
                            'temperature' => 24.7,
                        ),
                        7 =>
                        array(
                            'calendarMonth' => 7.5,
                            'temperature' => 24.1,
                        ),
                        8 =>
                        array(
                            'calendarMonth' => 8.5,
                            'temperature' => 20.1,
                        ),
                        9 =>
                        array(
                            'calendarMonth' => 9.5,
                            'temperature' => 14.1,
                        ),
                        10 =>
                        array(
                            'calendarMonth' => 10.5,
                            'temperature' => 8.6,
                        ),
                        11 =>
                        array(
                            'calendarMonth' => 11.5,
                            'temperature' => 2.6,
                        ),
                    ),
                ),
                'title' => 'New Project',
                'tradeNames' => 'Generic product names',
                'typeOfStructure' => 'slabs and walls (1-D)',
                'uncertainty' =>
                array(
                    'covCover' => 0.08,
                    'covCs' => 0.3,
                    'covCt' => 0.2,
                    'coVd28' => 0.25,
                    'coVm' => 0.25,
                    'useDefaults' => true,
                    'useUncertainty' => false,
                ),
            ),
        ),
    )
    // 'defaultProjectData' => [
    //     'project' => [
    //         'projectData' => [
    //             'title' => 'New Project',
    //             'description' => 'Project Description',
    //             'doneByName' => 'Analyst',
    //             'date' => date('m-d-Y'),
    //             'typeOfStructure' => 'slabs and walls (1-D)',
    //             'baseUnits' => 'US units',
    //             'concentrationUnits' => '% wt. conc.',
    //             'structureDimensions' => [
    //                 'overallThickness' => 200,
    //                 'trueThickness' => 200,
    //                 'clearCover' => 60,
    //                 'area' => 100000,
    //             ],
    //             'tradeNames' => 'Generic product names',
    //             'baseYear' => date('Y'),
    //             'studyPeriod' => 150,
    //             'inflationRate' => 0.018,
    //             'realDiscountRate' => 0.02,
    //             'exposureLocale' => [
    //                 'useThis' => true,
    //                 'location' => 'New York',
    //                 'subLocation' => 'NEW YORK',
    //                 'exposure' => 'Parking garages',
    //             ],
    //             'temperatureHistory' => [
    //                 'temp' => [
    //                     0 => [
    //                         'calendarMonth' => 0.5,
    //                         'temperature' => 13.888888888888971,
    //                     ],
    //                     1 => [
    //                         'calendarMonth' => 1.5,
    //                         'temperature' => 14.166666666666742,
    //                     ],
    //                     2 => [
    //                         'calendarMonth' => 2.5,
    //                         'temperature' => 15.000000000000057,
    //                     ],
    //                     3 => [
    //                         'calendarMonth' => 3.5,
    //                         'temperature' => 16.1111111111112,
    //                     ],
    //                     4 => [
    //                         'calendarMonth' => 4.5,
    //                         'temperature' => 17.77777777777783,
    //                     ],
    //                     5 => [
    //                         'calendarMonth' => 5.5,
    //                         'temperature' => 19.444444444444514,
    //                     ],
    //                     6 => [
    //                         'calendarMonth' => 6.5,
    //                         'temperature' => 21.1111111111112,
    //                     ],
    //                     7 => [
    //                         'calendarMonth' => 7.5,
    //                         'temperature' => 21.666666666666742,
    //                     ],
    //                     8 => [
    //                         'calendarMonth' => 8.5,
    //                         'temperature' => 20.83333333333337,
    //                     ],
    //                     9 => [
    //                         'calendarMonth' => 9.5,
    //                         'temperature' => 19.166666666666742,
    //                     ],
    //                     10 => [
    //                         'calendarMonth' => 10.5,
    //                         'temperature' => 16.1111111111112,
    //                     ],
    //                     11 => [
    //                         'calendarMonth' => 11.5,
    //                         'temperature' => 13.6111111111112,
    //                     ],
    //                 ],
    //             ],
    //             'exposureConditions' => [
    //                 'setManually' => false,
    //                 'c1556Sets' => [
    //                     0 => [
    //                         'ambientConcentration' => 0,
    //                         'baseUnits' => 'SI metric',
    //                         'bulkDiffusionCoefficient' => 3.48668689674473E-12,
    //                         'concentrationUnits' => '% wt. conc.',
    //                         'dateCreated' => date('m-d-Y'),
    //                         'days' => 28,
    //                         'depthConcPairs' => [
    //                             0 => [
    //                                 'concPctWt' => 0.6,
    //                                 'depthMM' => 1,
    //                             ],
    //                             1 => [
    //                                 'concPctWt' => 0.5,
    //                                 'depthMM' => 2,
    //                             ],
    //                             2 => [
    //                                 'concPctWt' => 0.6,
    //                                 'depthMM' => 3,
    //                             ],
    //                             3 => [
    //                                 'concPctWt' => 0.4,
    //                                 'depthMM' => 4,
    //                             ],
    //                             4 => [
    //                                 'concPctWt' => 0.2,
    //                                 'depthMM' => 5,
    //                             ],
    //                             5 => [
    //                                 'concPctWt' => 0.0,
    //                                 'depthMM' => 6,
    //                             ],
    //                         ],
    //                         'description' => '<add description>',
    //                         'includeFirstPoint' => false,
    //                         'maxSurfaceConcentration' => 9.755059563321685,
    //                         'name' => 'Sample',
    //                         'timeToMax' => 7.0,
    //                         'astmResults' => (object) array(),
    //                     ],
    //                 ],
    //                 'useC1556' => false,
    //                 'c1556SetUsed' => 'Sample',
    //                 'maxSurfaceConcentration' => 9.755059563321685,
    //                 'timeToBuildUp' => 7.0,
    //                 'ageAtFirstExposure' => 28,
    //                 'monthOfFirstExposure' => 0.0,
    //             ],
    //             'alts' => [
    //                 'alt' => [
    //                     0 => [
    //                         'alternative' => [
    //                             'name' => 'Sample 1',
    //                             'concreteMixDesign' => [
    //                                 'detailedBarrier' => [
    //                                     'useDefault' => true,
    //                                     'barrierName' => '<none>',
    //                                     'areaCost' => 0,
    //                                     'initialEfficiency' => 0,
    //                                     'ageAtFailure' => 0,
    //                                     'reapplyTime' => 0,
    //                                 ],
    //                                 'rebarType' => [
    //                                     'type' => 'Black Steel',
    //                                     'percentOfTotal' => 0.012,
    //                                     'yearsFromInitToFailure' => 6,
    //                                 ],
    //                                 'waterCementRatio' => 0.31,
    //                                 'percentSlag' => 0,
    //                                 'percentClassFFlyAsh' => 0.16,
    //                                 'percentSilicaFume' => 0.06,
    //                                 'serviceLifeInYears' => 20.333333333333336,
    //                                 'hydration' => 25.0,
    //                                 'valuesHaveChanged' => false,
    //                                 'alpha' => 0.0,
    //                                 'beta' => 0.0,
    //                                 'd28' => 5.5644E-9,
    //                                 'dRef' => 1.7949339123101182E-12,
    //                                 'ct' => 0.05,
    //                                 'm' => 0.33,
    //                                 'initiationInYears' => 14.333333333333334,
    //                                 'propagationInYears' => 6.0,
    //                                 'inhibitor' => '<none>',
    //                                 'isUserDefineable' => false,
    //                             ],
    //                             "costs"=> [
    //                                 [
    //                                     "costName"=> "Construction cost",
    //                                     "costNumber"=> 0,
    //                                     "costPrior"=> "0",
    //                                     "costTiming"=> [
    //                                         "endDate"=> "0",
    //                                         "frequency"=> "0",
    //                                         "startDate"=> "0"
    //                                     ],
    //                                     "costType"=> "indep.",
    //                                     "quantity"=> 2000,
    //                                     "totalCost"=> 386480,
    //                                     "unitCost"=> 193.24,
    //                                     "unitOfMeasure"=> "cub. met."
    //                                 ],
    //                                 [
    //                                     "costName"=> "Repair cost",
    //                                     "costNumber"=> 1,
    //                                     "costPrior"=> "0",
    //                                     "costTiming"=> [
    //                                         "endDate"=> "150",
    //                                         "frequency"=> "10",
    //                                         "startDate"=> "99"
    //                                     ],
    //                                     "costType"=> "dep.",
    //                                     "quantity"=> 1000,
    //                                     "totalCost"=> 400000,
    //                                     "unitCost"=> 400,
    //                                     "unitOfMeasure"=> "sq. m."
    //                                 ]
    //                             ],
    //                             'description' => '7000 psi @ 28days',
    //                             'userDefinedMixCost' => false,
    //                             'userMixCost' => 0,
    //                         ],
    //                     ],
    //                     1 => [
    //                         'alternative' => [
    //                             'name' => 'Sample 2',
    //                             'concreteMixDesign' => [
    //                                 'detailedBarrier' => [
    //                                     'useDefault' => true,
    //                                     'barrierName' => '<none>',
    //                                     'areaCost' => 0,
    //                                     'initialEfficiency' => 0,
    //                                     'ageAtFailure' => 0,
    //                                     'reapplyTime' => 0,
    //                                 ],
    //                                 'rebarType' => [
    //                                     'type' => 'Black Steel',
    //                                     'percentOfTotal' => 0.012,
    //                                     'yearsFromInitToFailure' => 6,
    //                                 ],
    //                                 'waterCementRatio' => 0.36,
    //                                 'percentSlag' => 0,
    //                                 'percentClassFFlyAsh' => 0.15,
    //                                 'percentSilicaFume' => 0,
    //                                 'serviceLifeInYears' => 8.333333333333334,
    //                                 'hydration' => 25.0,
    //                                 'valuesHaveChanged' => false,
    //                                 'alpha' => 0.0,
    //                                 'beta' => 0.0,
    //                                 'd28' => 1.9741E-8,
    //                                 'dRef' => 6.367955209079162E-12,
    //                                 'ct' => 0.05,
    //                                 'm' => 0.32,
    //                                 'initiationInYears' => 2.3333333333333335,
    //                                 'propagationInYears' => 6.0,
    //                                 'inhibitor' => '<none>',
    //                                 'isUserDefineable' => false,
    //                             ],
    //                             "costs"=> [
    //                                 [
    //                                     "costName"=> "Construction cost",
    //                                     "costNumber"=> 0,
    //                                     "costPrior"=> "0",
    //                                     "costTiming"=> [
    //                                         "endDate"=> "0",
    //                                         "frequency"=> "0",
    //                                         "startDate"=> "0"
    //                                     ],
    //                                     "costType"=> "indep.",
    //                                     "quantity"=> 2000,
    //                                     "totalCost"=> 386480,
    //                                     "unitCost"=> 193.24,
    //                                     "unitOfMeasure"=> "cub. met."
    //                                 ],
    //                                 [
    //                                     "costName"=> "Repair cost",
    //                                     "costNumber"=> 1,
    //                                     "costPrior"=> "0",
    //                                     "costTiming"=> [
    //                                         "endDate"=> "150",
    //                                         "frequency"=> "10",
    //                                         "startDate"=> "99"
    //                                     ],
    //                                     "costType"=> "dep.",
    //                                     "quantity"=> 1000,
    //                                     "totalCost"=> 400000,
    //                                     "unitCost"=> 400,
    //                                     "unitOfMeasure"=> "sq. m."
    //                                 ]
    //                             ],
    //                             'description' => '6000 psi @ 28days',
    //                             'userDefinedMixCost' => false,
    //                             'userMixCost' => 0,
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //             'uncertainty' => [
    //                 'useUncertainty' => false,
    //                 'useDefaults' => true,
    //                 "coVd28" => 0.25,
    //                 "coVm" => 0.25,
    //                 "covCs" => 0.3,
    //                 "covCt" => 0.2,
    //                 "covCover" => 0.08
    //             ],
    //             'costs' => [
    //                 'baseMixCost' => 76.46,
    //                 'repairCost' => 37.16,
    //                 'areaToRepair' => 0.1,
    //                 'repairInterval' => 10,
    //                 'useFixedRepairInterval' => true,
    //                 'blackSteel' => 0.45,
    //                 'stainlessSteel' => 2.99,
    //                 'epoxySteel' => 0.6,
    //                 'membrane' => 3.07,
    //                 'sealant' => 0.65,
    //                 'inhibitor' => 5.68,
    //             ],
    //         ],
    //         'fileVersion' => 212,
    //     ],
    // ]
];
