<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sampling;

class SamplingController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Samplings/Index');
    }

    public function list(Request $request)
    {
        $query = Sampling::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('doc', 'like', "%{$search}%");
            });
        }

        $samplings = $query->paginate(10);

        return response()->json([
            'samplings' => $samplings,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'date_sampling' => 'required|date',
            'doc' => 'required|string',
            'cage_no' => 'required|string',
        ]);

        $sampling = Sampling::create($request->all());

        return response()->json([
            'message' => 'Sampling created successfully',
            'sampling' => $sampling
        ]);
    }

    public function update(Request $request, Sampling $sampling)
    {
        $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'date_sampling' => 'required|date',
            'doc' => 'required|string',
            'cage_no' => 'required|string',
        ]);

        $sampling->update($request->all());

        return response()->json([
            'message' => 'Sampling updated successfully',
            'sampling' => $sampling
        ]);
    }

    public function destroy(Sampling $sampling)
    {
        $sampling->delete();

        return response()->json([
            'message' => 'Sampling deleted successfully'
        ]);
    }

    public function report(Request $request)
    {
        return Inertia::render('Samplings/SamplingReport');
    }
}
