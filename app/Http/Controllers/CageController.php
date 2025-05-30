<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cage;

class CageController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Cages/Index');
    }

    public function list(Request $request)
    {
        $query = Cage::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('number_of_fingerlings', 'like', "%{$search}%");
            });
        }

        $cages = $query->paginate(10);

        return response()->json([
            'cages' => $cages,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number_of_fingerlings' => 'required|integer',
            'feed_types_id' => 'required|exists:feed_types,id',
            'investor_id' => 'required|exists:investors,id',
        ]);

        $cage = Cage::create($request->all());

        return response()->json([
            'message' => 'Cage created successfully',
            'cage' => $cage
        ]);
    }

    public function update(Request $request, Cage $cage)
    {
        $request->validate([
            'number_of_fingerlings' => 'required|integer',
            'feed_types_id' => 'required|exists:feed_types,id',
            'investor_id' => 'required|exists:investors,id',
        ]);

        $cage->update($request->all());

        return response()->json([
            'message' => 'Cage updated successfully',
            'cage' => $cage
        ]);
    }

    public function destroy(Cage $cage)
    {
        $cage->delete();

        return response()->json([
            'message' => 'Cage deleted successfully'
        ]);
    }

    public function show(Cage $cage)
    {
        return Inertia::render('Cages/View', [
            'cage' => $cage
        ]);
    }
} 