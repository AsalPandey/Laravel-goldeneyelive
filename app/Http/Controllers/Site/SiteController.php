<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use App\Models\Notice;
use App\Models\FAQ;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $data= [
            'notices' => Notice::get()->where('status','active'),
            'courses' => Courses::get()->take(3)
        ];
        return view('site.index', $data);
    }
    public function about()
    {
        return view('site.about.about');
    }
    public function aboutDetail()
    {
        return view('site.about.about-detail');
    }
    public function faq()
    {
        $data= [
            'faqs' => FAQ::get()->where('status','active')
        ];
        return view('site.faq.faq',$data);
    }
    public function privacyPolicy()
    {
        return view('site.others.privacyPolicy');
    }
    public function termsAndConditions()
    {
        return view('site.others.termsAndConditions');
    }
}
