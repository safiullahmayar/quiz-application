<?php

namespace App\Http\Controllers;

use App\Models\ufm;
use Illuminate\Http\Request;

class UfmController extends Controller
{

    public function ufm_list()
{
    $ufmRecords = ufm::all();
    $ufmRecords = ufm::with(['user:id,name', 'exam:id,title'])
        ->get()
        ->map(function($item) {
            return [
                'id'=>$item->id,
                'student_name' => $item->user->name ?? 'N/A',
                'exam_name' => $item->exam->title ?? 'N/A', // Changed from 'name' to 'title' to match your exam table
                'description' => $item->description,
                'ufm_flag' => $item->ufm_flag,
                'created_at' => $item->created_at
            ];
        });

    return view('admin\ufmList', compact('ufmRecords'));

}
public function destroy($id)
{
    $ufm = ufm::findOrFail($id);
    $ufm->delete();

    return redirect()->back()
        ->with('success', 'UFM record deleted successfully');
}
}
