<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\Notice;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Only query stats for Admin/Staff — Students don't need them
        if ($user->hasRole(['Admin', 'Staff'])) {
            $stats = [
                'courses' => Course::count(),
                'categories' => CourseCategory::count(),
                'blog_posts' => BlogPost::count(),
                'notices' => Notice::count(),
                'contacts' => Contact::count(),
                'enrollments' => JoinNowQuery::count(),
                'subscribers' => NewsLetter::count(),
                'students' => User::role('Student')->count(),
            ];

            $recentEnrollments = JoinNowQuery::latest()->limit(5)->get();
            $recentContacts = Contact::latest()->limit(5)->get();
        } else {
            $stats = [];
            $recentEnrollments = collect();
            $recentContacts = collect();
        }

        return view('dashboard', compact('stats', 'recentEnrollments', 'recentContacts'));
    }
}
