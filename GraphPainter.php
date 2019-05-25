<?php

//require(dirname(__FILE__) . '../../DS/graph/Graph.php');
require_once "Graph.php";

class GraphPainter
{
    public $graph;
    public $links;
    public $degrees;
    public $colors_table;
    public $colors_num;

    /**
     * @param Graph $graph
     */
    function __construct($graph)
    {
        $this->graph = $graph;

        // get graph vertexes degrees and links
        $this->degrees = [];
        $this->links = [];

        $graph->list_connections($this->degrees, $this->links);
        arsort($this->degrees, SORT_DESC);

        // initialize empty vertex -> color table
        $this->colors_table = [];

        for ($i = 0; $i < $graph->v_num; $i++) {
            $this->colors_table [] = null;
        }
    }

    function paint()
    {
        $this->greedy_paint();
        $this->bee_repaint();

        return $this->colors_table;
    }

//    ---

    function greedy_paint()
    {
        $curr_col = 0;
        $to_paint = array_keys($this->degrees);

        while (!empty($to_paint)) {
            $curr_col++;

            for ($i = 0, $num = count($to_paint); $i < $num; $i++) {
                $v = $to_paint[$i];

                // if vertex is linked to vertexes, painted the same color, skip
                if (!$this->color_check($v, $curr_col)) continue;

                $this->colors_table[$v] = $curr_col;
                unset($to_paint[$i]);
            }

            // reindexing
            $to_paint = array_values($to_paint);
        }

        $this->colors_num = $curr_col;
    }

    function bee_repaint()
    {
        // vertex with max degree
        $u = get_first_key($this->degrees);

        $colors = [];

        for ($i = 0; $i < $this->colors_num; $i++) {
            $colors [] = $i;
        }

        foreach ($this->links[$u] as $v) {
            echo $v . ' ';
            $u_col = $this->colors_table[$u];
            $v_col = $this->colors_table[$v];

            // check if is ok to switch colors
            if ($this->color_check($u, $v_col) && $this->color_check($v, $u_col)) {
                echo 'checked' . $v . ' ';
                $this->colors_table[$u] = $v_col;
                $this->colors_table[$v] = $u_col;

                $col_copy = $colors;

                foreach ($this->links[$v] as $link2) {
                    if (($pos = array_search($this->colors_table[$link2], $col_copy)) !== false) {
                        array_unshift($col_copy, $pos);
                    }
                }

                var_dump($col_copy);
            }
        }
    }

    /**
     * Checks if $vertex linked vertexes
     * aren't panted in $color, returns false if one of them is
     *
     * @param $vertex
     * @param $color
     * @return bool
     */
    function color_check($vertex, $color)
    {
        foreach ($this->links[$vertex] as $linked) {
            if ($this->colors_table[$linked] === $color) {
                return false;
            }
        }

        return true;
    }
}

function get_first_key($arr)
{
    foreach ($arr as $key => $val) {
        return $key;
    }

    return null;
}