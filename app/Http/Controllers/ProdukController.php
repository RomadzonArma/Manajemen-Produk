<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Produk;
use PDF;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $kategori = Category::all()->pluck('nama_kategori', 'id_kategori');

        return view('produk.index', compact('kategori'));
    }

    public function data(){
        $produk = Produk::leftJoin('categories','categories.id_kategori','produks.id_kategori')
        ->select('produks.*','nama_kategori')->get();

        return datatables()
        ->of($produk)
        ->addIndexColumn()
        ->addColumn('select_all', function($produk){
            return '
                <input type="checkbox" name="id_produk[]" value="'. $produk->id_produk .'">
            ';
        })
        ->addColumn('kode_produk', function($produk){
            return '<span class = "label label-success">'. $produk->kode_produk . '</span>';
        })
        ->addColumn('harga_beli', function($produk){
            return format_uang($produk->harga_beli);
        })
        ->addColumn('harga_jual', function($produk){
            return format_uang($produk->harga_jual);
        })
        ->addColumn('stok', function($produk){
            return format_uang($produk->stok);
        })
        ->addColumn('aksi', function ($produk) {
            return '
            <div class="btn-group">
                <button type="button" onclick="editForm(`'. route('produk.update', $produk->id_produk) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                <button type="button" onclick="deleteData(`'. route('produk.destroy', $produk->id_produk) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
            </div>
            ';
        })
        ->rawColumns(['aksi','kode_produk','select_all'])
        ->make(true);
    }

    public function store(Request $request)
    {
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P'. tambah_nol_didepan((int)$produk->id_produk +1, 6);

        $produk = Produk::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id){
        $produk = Produk::find($id);
        return response()->json($produk);
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    public function destroy($id){
        $produk = Produk::findOrFail($id);
        $produk->delete();
        return response(null, 204);
    }

    public function deleteSelected(Request $request){
        foreach($request->id_produk as $id){
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request){
        $dataProduk = array();
        foreach ($request->id_produk as $id){
            $produk = Produk::find($id);
            $dataProduk[] = $produk;
        }

        $no = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataProduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('produk.pdf');
    }
}
