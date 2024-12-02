<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    public function __invoke(Page $page)
    {
        return view('front.page', compact('page'));
    }
}
