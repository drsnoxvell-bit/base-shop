<?php

namespace App\Http\Controllers;

use App\Support\ShopStack;
use Illuminate\View\View;

class SpaController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(ShopStack::isSpa(), 404);

        return view('spa');
    }
}
