<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Setting;
use PDF;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(){
        return view('member.index');
    }

    public function data(){
        $member = Member::orderBy('id_member', 'desc')->get();
        return datatables()
        ->of($member)
        ->addIndexColumn($member)
        ->addColumn('select_all', function($member){
            return '
                <input type = "checkbox" name="id_member[]" value="'.$member->id_member.'"></input>
            ';
        })
        ->addColumn('kode_member', function ($member) {
            return '<span class="label label-success">'. $member->kode_member .'<span>';
        })
        ->addColumn('aksi', function ($member){
            return '
                <div class="btn-group">
                    <button type="button" onclick = "editForm(`'.route('member.update', $member->id_member).'`)" class="btn btn-flat btn-primary"><i class="fa fa-pencil"></i></button>
                    <buttton type="button" onclick = "deleteData(`'.route('member.destroy', $member->id_member).'`)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></buttton>
                </div>
            ';
        })
        ->rawColumns(['aksi','select_all', 'kode_member'])
        ->make(true);
    }

    public function store(Request $request){
        $member = new Member();
        $request['kode_member'] = 'M'. tambah_nol_didepan((int)Member::max('id_member') + 1, 6);
        $member->create($request->all());
        return response()->json('Data berhasil disimpan', 200);
    }

    public function show($id){
        $member = Member::findOrFail($id);
        return response()->json($member, 200);
    }

    public function update(Request $request,$id){
        $member = Member::find($id);
        $member->update($request->all());
        return response()->json('Data berhasil disimpan', 200);
    }

    public function destroy($id)
    {
        $member = Member::find($id);
        $member->delete();

        return response(null, 204);
    }

    public function cetakMember(Request $request){
        $datamember = collect(array());
        foreach($request->id_member as $id){
            $member = Member::find($id);
            $datamember[] = $member;
        }

        $datamember = $datamember->chunk(2);
        $setting = Setting::first();

        $no = 1;
        $pdf = PDF::loadView('member.cetak', compact('datamember', 'no', 'setting'));
        $pdf->setPaper(array(0,0,566.93, 850.39), 'potrait');
        return $pdf->stream('member.pdf');
    }

}
