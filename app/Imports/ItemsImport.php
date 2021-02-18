<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
        $info=$collection->toArray();
        unset($info[0]);
        array_values($info);
        
        $tipos=DB::table('itemtypes')->get();
        $tipos=array_values(array_unique(array_column($tipos->toArray(),'itemtype')));

        $branches=DB::table('branches')->get();
        $branches=array_values(array_unique(array_column($branches->toArray(),'branchcode')));
        
        $data=array();
        foreach($info as $j=>$inf){
            if(!in_array($inf[2],$tipos)){
                
                return "Debe ingresar un tipo de item correcto en la fila ".($j+1)."";
                exit;
            }
            if(!in_array($inf[3],$branches)){
                
                return "Debe ingresar un codigo de sede correcto en la fila ".($j+1)."";
                exit;
            }
            if($inf[2]=='EBOOK' && $inf[4]==''){
                
                return "Debe ingresar un link en la fila ".($j+1)."";
                exit;
            }
            $data[$j]['author']=$inf[0];
            $data[$j]['title']=$inf[1];
            $data[$j]['tipo_item']=$inf[2];
            $data[$j]['sede']=$inf[3];
            $data[$j]['link']=$inf[4];
            $data[$j]['codigo_barras']=$inf[5];
        }

        return 2;
    }

    
}
