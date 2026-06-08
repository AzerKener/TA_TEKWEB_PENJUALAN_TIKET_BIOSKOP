<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FnbItem;

class FnbController extends Controller
{
    public function index()
    {
        $fnbItems = FnbItem::available()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('customer.fnb.index', compact('fnbItems'));
    }
}
