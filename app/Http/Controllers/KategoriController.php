<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(){
        return view('kategori.index');
    }

    public function data(){
        $katgeori = Category::orderBy('id_kategori','desc')->get();

        return datatables()
        ->of($katgeori)
        ->addIndexColumn($katgeori)
        ->addColumn('aksi', function ($katgeori){
            return '
                <div class = "btn-group">
                    <button onclick = "editForm(`'.route('kategori.update', $katgeori->id_kategori) .'`)" class = "btn btn-info btn-sm btn-flat"><i class = "fa fa-pencil"></i></button>
                    <button onclick = "deleteData(`'.route('kategori.destroy', $katgeori->id_kategori) .'`)" class = "btn btn-danger btn-sm btn-flat"><i class = "fa fa-trash"></i></button>
                </div>
            ';
        })
        ->rawColumns(['aksi'])
        ->make(true);
    }

    public function store(Request $request){
        $katgeori = new Category();
        $katgeori->nama_kategori = $request->nama_kategori;
        $katgeori->save();

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id){
        $katgeori = Category::findOrFail($id);
        return response()->json($katgeori);
    }

    public function update(Request $request, $id){
        $katgeori = Category::find($id);
        $katgeori->nama_kategori = $request->nama_kategori;
        $katgeori->update();
        return response()->json('Data berhasil disimpan', 200);
    }

    public function destroy($id){
        $katgeori = Category::find($id);
        $katgeori->delete();

        return  response(null, 204);
    }
}
