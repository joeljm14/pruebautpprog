<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;


class Listado extends Controller
{
    public function action(Request $request){

        if($request->ajax())
        {
            
           
                $data=DB::table('biblio')
                ->join('biblioitems','biblio.biblionumber','=','biblioitems.biblionumber')
                ->join('itemtypes','biblioitems.itemtype','=','itemtypes.itemtype')
                ->select('biblio.biblionumber','biblioitems.isbn','itemtypes.description','biblioitems.publishercode','biblio.author','biblio.title','biblio.timestamp')
                ->get();
            
            
        
            echo json_encode($data);
        }  
    }

    public function detalle(Request $request){

        if($request->ajax())
        {
            $id=$request->get('id');

            $data=DB::table('items')
            ->join('branches','items.homebranch','=','branches.branchcode')
            ->join('itemtypes','items.itype','=','itemtypes.itemtype')
            ->where('items.biblionumber',$id)
            ->select('items.itemnumber','items.barcode','branches.branchname','itemtypes.description','items.itemcallnumber')
            ->get();
 
            echo json_encode($data);
        }  
    }

    public function import(Request $request){

        if($request->ajax())
        {
            $file = $request->file('file')->store('import');

            try {
                Excel::import(new ItemsImport, $file);

                $mensaje=1;
             }
             catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                
                $mensaje=$failures;
             }

             

            echo json_encode($mensaje);
        }  
    }
}