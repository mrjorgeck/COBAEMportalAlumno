<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.auditoria.index', [
            'eventos' => DB::table('activity_log')->latest()->paginate(50),
        ]);
    }
}
