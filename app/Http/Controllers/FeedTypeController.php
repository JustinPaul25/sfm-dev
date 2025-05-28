<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeedType;
use Inertia\Inertia;

class FeedTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('FeedTypes/Index');
    }

    public function list(Request $request)
    {
        $feedTypes = FeedType::withTrashed()->orderByDesc('created_at')->get();
        return response()->json($feedTypes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'feed_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
        ]);
        $feedType = FeedType::create($validated);
        return response()->json($feedType);
    }

    public function update(Request $request, FeedType $feedType)
    {
        $validated = $request->validate([
            'feed_type' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
        ]);
        $feedType->update($validated);
        return response()->json($feedType);
    }

    public function destroy(FeedType $feedType)
    {
        $feedType->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $feedType = FeedType::withTrashed()->findOrFail($id);
        $feedType->restore();
        return response()->json($feedType);
    }
}
