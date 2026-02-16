<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalContents = Content::where('user_id', $userId)->count();
        $draftContents = Content::where('user_id', $userId)->where('status', 'draft')->count();
        $publishedContents = Content::where('user_id', $userId)->where('status', 'published')->count();
        $availableTemplates = Template::where('status', 'active')->count();

        $recentContents = Content::with('template.slots', 'images')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'totalContents',
            'draftContents',
            'publishedContents',
            'availableTemplates',
            'recentContents'
        ));
    }
}
