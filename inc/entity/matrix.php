<?php


class Matrix
{
    public $row;
    public $column;

    public $body;

    public function __construct( $row, $column, $body)
    {
        $this->row = $row;
        $this->column = $column;
        $this->body = array();
        foreach (explode(";", $body) as $value){
            array_push($this->body, array_map('intVal', explode(",", $value)));
        }
    }

    public function getBodyElement($i, $j){
        return $this->body[$i][$j];
    }

    public function makeGrah(){
        $size = $this->row * $this->column;
        $grah = array_fill(0, $size, 0);
        $grah[0] = array(
            1               => $this->body[0][1],
            $this->column   => $this->body[1][0]
        );

        for($i = 1; $i <  $this->column - 1; ++$i){
            $grah[$i] = array(
                $i-1               => $this->body[0][$i-1],
                $i+1               => $this->body[0][$i+1],
                $this->column+$i   => $this->body[1][$i]
            );
        }

        $grah[$this->column - 1] = array(
            $this->column - 2       => $this->body[0][$this->column - 2],
            2*($this->column - 1)   => $this->body[1][$this->column - 1]
        );
        $count = $this->column;
        for($row = 1; $row <  $this->row - 1; ++$row){
            $grah[$count] = array(
                $count-$this->column => $this->body[$row-1][0],
                $count + 1           => $this->body[$row  ][1],
                $count+$this->column => $this->body[$row+1][0]
            );++$count;

            for($col = 1; $col <  $this->column - 1; ++$col){
                $grah[$count] = array(
                    $count-$this->column => $this->body[$row-1][$col    ],
                    $count - 1           => $this->body[$row  ][$col - 1],
                    $count + 1           => $this->body[$row  ][$col + 1],
                    $count+$this->column => $this->body[$row-1][$col    ]
                );++$count;
            }

            $grah[$count] = array(
                $count-$this->column => $this->body[$row-1][$this->column - 1],
                $count - 1           => $this->body[$row  ][$this->column - 2],
                $count+$this->column => $this->body[$row-1][$this->column - 1]
            );++$count;
        }

        $grah[$count] = array(
            $count + 1                  => $this->body[$this->row - 1][1],
            $count - $this->column      => $this->body[$this->row - 2][0]
        );++$count;

        for($i = 1; $i <  $this->column - 1; ++$i){
            $grah[$count] = array(
                $count-1               => $this->body[$this->row - 1][$i-1],
                $count+1               => $this->body[$this->row - 1][$i+1],
                $count-$this->column   => $this->body[$this->row - 2][$i  ]
            );++$count;
        }

        $grah[$count] = array(
            $count - 1              => $this->body[$this->row - 1][$this->column - 2],
            $count-$this->column    => $this->body[$this->row - 2][$this->column - 1]
        );

        return  $grah;
    }

    private function minNode(&$grah){
        $min        = $this->row * $this->column * 9 + 1;
        $min_key    = -1;
        foreach($grah as $key=>$value){
            if($grah[$key]['queue'] && $min > $grah[$key]['value']){
                $min        = $grah[$key]['value'];
                $min_key    = $key;
            }
        }
        
        return $min_key;
    }

    public function execute($start, $end){
        $min        = $this->row * $this->column * 9 + 1;
        $grah       = $this->makeGrah();
        $Q          = $grah;
        $S          = array();

        foreach ($Q as &$node){
            /*foreach ($node as $key => $value){
                $node[$key] = $min;
            }*/
            $node['value']  = $min;
            $node['queue']  = true;
        }

       $s = $start[1]   * $this->column + $start[0];
       $e = $end[1]     * $this->column + $end[0];
       $Q[$s]['value'] = 0;
       

       while(true){
        $min = $this->minNode($Q);

        if($min === -1) break;
        $Q[$min]['queue'] = false;
        $value = $Q[$min]['value'];

        foreach($Q[$min] as $key=>$next_val){
            if($key !== 'value' && $key !== 'queue' && $value + $next_val < $Q[$key]['value']){
                $Q[$key]['value']   = $value + $next_val;
                $S[$key]            = array($min, $Q[$key]);
            }
        }
       }


       $path = array();
        $pos = $e;
        while($pos != $s){
            $path[] = $pos;
            $pos = $S[$pos][0];
        }
        $path[] = $s;
        $path = array_reverse($path);

        return  json_encode([
            'length' => $Q[$e]['value'],
            'path' => '['.implode(',', $path).']'
        ]);
    }
}