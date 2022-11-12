<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Sitemap\SitemapGenerator;

class SiteMapController extends Controller
{
    public function index()
    {
        return view('admin-views/site-map/view');
    }

    public function download(){
        SitemapGenerator::create(url('/'))->writeToFile('public/sitemap.xml');
        return response()->download(public_path('sitemap.xml'));
    }
}
