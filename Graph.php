<?php

class Graph
{
    public $v_num;
    public $edges;
    public $adj_matrix;
    public $distance_matrix;

    /**
     * Graph constructor.
     * @param array $edges - [u, v, len]
     * @param integer $v_num - number of vertexes
     * @param $is_oriented
     */
    function __construct($edges, $v_num, $is_oriented)
    {
        $this->v_num = $v_num;

        if (!$is_oriented) {
            $this->double_edges($edges);
        }

        $this->edges = $edges;

        $this->build_adj_matrix();
        $this->build_distance_matrix();
    }

//    ---

    function double_edges(&$edges)
    {
        for ($i = 0, $num = count($edges); $i < $num; $i++) {
            $edge = $edges[$i];
            $edges [] = [$edge[1], $edge[0], $edge[2]];
        }
    }

    function build_adj_matrix()
    {
        $this->adj_matrix = $this->matrix_init(0);

        foreach ($this->edges as $edge) {
            $u = $edge[0];
            $v = $edge[1];

            $this->adj_matrix[$u][$v] = 1;
            $this->adj_matrix[$v][$u] = 1;
        }
    }

    function list_connections(&$degrees, &$links)
    {
        $degrees = [];
        $links = [];

        for ($i = 0; $i < $this->v_num; $i++) {
            $degrees [] = 0;
            $links [] = [];
        }

        for ($i = 0, $num = count($this->edges); $i < $num; $i++) {
            $u = $this->edges[$i][0];
            $v = $this->edges[$i][1];

            $degrees[$u]++;
            $links[$u] [] = $v;
        }
    }

    function matrix_init($default)
    {
        $matrix = [];

        for ($i = 0; $i < $this->v_num; $i++) {
            $matrix[$i] = [];

            for ($a = 0; $a < $this->v_num; $a++) {
                $matrix[$i][$a] = $default;
            }
        }

        return $matrix;
    }

    function build_distance_matrix()
    {
        $this->distance_matrix = $this->matrix_init(INF);

        foreach ($this->edges as $edge) {
            $u = $edge[0];
            $v = $edge[1];
            $dist = $edge[2];

            $this->distance_matrix[$u][$v] = $dist;
            $this->distance_matrix[$v][$u] = $dist;
        }
    }
}

function matrix_output($matrix)
{
    $v_num = count($matrix);

    echo "  | ";
    for ($i = 0; $i < $v_num; $i++) {
        echo str_pad($i, 2, ' ', STR_PAD_LEFT) . ' ';
    }

    echo "\n" . str_pad('', 3 * $v_num + 3, '-', STR_PAD_LEFT);

    echo "\n";

    for ($i = 0; $i < $v_num; $i++) {
        echo str_pad($i, 2, ' ', STR_PAD_LEFT) . '| ';

        foreach ($matrix[$i] as $item) {
            echo str_pad($item, 2, ' ', STR_PAD_LEFT) . ' ';
        }

        echo "\n";
    }
}

function arr_output($arr)
{
    echo '[ ';
    foreach ($arr as $item) {
        echo $item . ', ';
    }
    echo "]\n";
}