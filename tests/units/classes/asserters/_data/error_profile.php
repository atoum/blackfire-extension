<?php

return array(
    'id' => 'a6337421-337a-47c3-a1ef-35f606883edd',
    'label' => '',
    'created_at' => '2015-12-05T17:57:42+0000',
    'updated_at' => '2015-12-05T17:57:43+0000',
    'status' =>
        array (
            'name' => 'finished',
            'code' => 64,
            'failure_reason' => NULL,
            'updated_at' => '2015-12-05T17:57:43+0000',
        ),
    'arguments' =>
        array (
        ),
    'layers' => NULL,
    'report' =>
        array (
            'state' => 'failed',
            'tests' =>
                array (
                    0 =>
                        array (
                            'name' => 'Temps d\'execution',
                            'state' => 'failed',
                            'failures' =>
                                array (
                                    0 => 'main.wall_time 4s < 1s',
                                ),
                        ),
                ),
        ),
    'store' =>
        array (
        ),
    '_links' =>
        array (
            'self' =>
                array (
                    'href' => 'https://blackfire.io/api/v1/profiles/a6337421-337a-47c3-a1ef-35f606883edd',
                ),
            'graph_url' =>
                array (
                    'href' => 'https://blackfire.io/profiles/a6337421-337a-47c3-a1ef-35f606883edd/graph',
                ),
            'store' =>
                array (
                    'href' => 'https://blackfire.io/api/v1/profiles/a6337421-337a-47c3-a1ef-35f606883edd/store',
                ),
            'promote_reference' =>
                array (
                    'href' => 'https://blackfire.io/api/v1/profiles/a6337421-337a-47c3-a1ef-35f606883edd/promote-reference',
                ),
        ),
    'envelope' =>
        array (
            'ct' => 1,
            'wt' => 4001272,
            'cpu' => 1220,
            'mu' => 190224,
            'pmu' => 191808,
            'io' => 4000052,
            'nw' => 0,
            'nw_in' => 0,
            'nw_out' => 0,
        ),
);
