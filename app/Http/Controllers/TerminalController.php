<?php

namespace App\Http\Controllers;

use App\Http\Requests\TerminalRequest;
use App\Models\Terminal;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function index()
    {
        $terminals = Terminal::all();
        return response()->json([
            'status' => 'success',
            'data' => $terminals,
        ], 200);
    }

    public function store(TerminalRequest $request)
    {
        $validated = $request->validated();

        $terminal = Terminal::create([
            'name' => $validated['name'],
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal created successfully',
            'data' => $terminal,
        ], 201);
    }

    public function update(TerminalRequest $request, Terminal $terminal)
    {
        $validated = $request->validated();

        $terminal->name = $validated['name'];
        $terminal->longitude = $validated['longitude'];
        $terminal->latitude = $validated['latitude'];

        $terminal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal updated successfully',
            'data' => $terminal,
        ], 200);
    }

    public function destroy(Terminal $terminal)
    {
        $terminal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal deleted successfully',
        ], 200);
    }
}
