<?php

namespace App\Http\Controllers\api;

use App\models\mod_common;
use App\models\mod_h5;
use Illuminate\Http\Request;

class ctl_h5 extends Controller
{
    public function detail(Request $request)
    {
        header('Content-Type: text/html; charset=utf-8');

        $id = $request->input('id');
        if(empty($id)) return page_error(['code' => 404]);

        $row = mod_h5::detail(['id' => $id]);

        if(empty($row)) return page_error(['code' => 404]);

        return view('api.h5_detail', [
            'row'   =>  $row,
        ]);
    }
}
