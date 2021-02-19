<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection,WithValidation
{

    use Importable, SkipsErrors;
   
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
           
            $data[$j]['author']=$inf[0];
            $data[$j]['title']=$inf[1];
            $data[$j]['itype']=$inf[2];
            $data[$j]['homebranch']=$inf[3];
            $data[$j]['uri']=$inf[4];
            $data[$j]['barcode']=$inf[5];
        }

        

        foreach($data as $a=>$dat){
            DB::table('biblio')->insert([
                'author' => $dat['author'],
                'title' => $dat['title'],
                'datecreated'=>Date('Y-m-d H:i:s')
            ]);
            $jm=DB::table('biblio')->first();
            $idb=$jm->biblionumber;
            DB::table('biblioitems')->insert([
                'biblionumber' => $idb,
                'itemtype' => $dat['itype'],
            ]);
           

            $jm2=DB::table('biblioitems')->first();
            $idbi=$jm2->biblioitemnumber;
            $arr=explode("|",$dat['barcode']);
            //print_r($arr); exit;
            $ins=[];
            for($i=0;$i<count($arr);$i++){
                $l=substr($arr[$i], 1);
                $ins=array('biblionumber'=>$idb,
                'biblioitemnumber'=>$idbi,
                            'homebranch'=>$dat['homebranch'],
                        'uri'=>$dat['uri'],
                        'damaged'=>0,
                        'itype'=>$dat['itype'],
                        'acquitype'=>'COMP',
                    'barcode'=>$l);
                    DB::table('items')->insert($ins);    
            }
        }
    }
    

    public function rules(): array
    {

        return [
            
             '0' => function($attribute, $value, $onFailure) {
                 
                 if($attribute>1.0){
                    if($value==''){
                        $onFailure('Debe ingresar un Título ');
                    }
                 }
                
              },
              '1' => function($attribute, $value, $onFailure) {
                 
                if($attribute>1.1){
                   if($value==''){
                       $onFailure('Debe ingresar un Autor ');
                   }
                }
             },
             '2' => function($attribute, $value, $onFailure) {
                $tipos=DB::table('itemtypes')->get();
                $tipos=array_values(array_unique(array_column($tipos->toArray(),'itemtype')));
                if($attribute>1.2){
                   if(!in_array($value,$tipos)){
                
                        $onFailure('Debe ingresar Item_types que existan');
                    }
                }
             },
             '3' => function($attribute, $value, $onFailure) {
                $branches=DB::table('branches')->get();
                $branches=array_values(array_unique(array_column($branches->toArray(),'branchcode')));
                if($attribute>1.3){
                    if(!in_array($value,$branches)){
                
                        $onFailure('Debe ingresar sedes que existan');
                    }
                }
             },
             '5' => function($attribute, $value, $onFailure) {
                if($attribute>1.5){
                    $arr=explode("|",$value);
                    //print_r($arr); exit;
                    for($i=0;$i<count($arr);$i++){
                        if($arr[$i][0]!='p'){
                            $onFailure('Los codigos de barra deben iniciar con p');
                        }
                    }
                 }
             }
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public static function afterImport(AfterImport $event)
    {
    }

    public function onFailure(Failure ...$failure)
    {
    }

    
}
