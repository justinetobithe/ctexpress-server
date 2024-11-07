<?php

namespace App\Http\Controllers;

use App\Http\Requests\TerminalRequest;
use App\Models\Terminal;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $pageSize = $request->input('page_size', 10);
        $filter = $request->input('filter');
        $sortColumn = $request->input('sort_column', 'name');
        $sortDesc = $request->input('sort_desc', false) ? 'desc' : 'asc';

        $query = Terminal::query();

        if ($filter) {
            $query->where(function ($q) use ($filter) {
                $q->where('name', 'like', "%{$filter}%")
                    ->orWhere('longitude', 'like', "%{$filter}%")
                    ->orWhere('latitude', 'like', "%{$filter}%");
            });
        }

        if (in_array($sortColumn, ['name', 'longitude', 'latitude'])) {
            $query->orderBy($sortColumn, $sortDesc);
        }

        $terminals = $query->paginate($pageSize);

        return $this->success($terminals);
    }

    public function show(Terminal $termimal)
    {
        return $this->success(['status' => true, 'data' => $termimal]);
    }

    public function store(TerminalRequest $request)
    {
        $validated = $request->validated();

        $terminal = Terminal::create($validated->all());

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.created'),
            'data' => $terminal,
        ]);
    }

    public function update(TerminalRequest $request, string $id)
    {
        $terminal = Terminal::findOrFail($id);


        $terminal->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.updated'),
            'data' => $terminal,
        ], 200);
    }

    public function destroy(string $id)
    {
        $terminal = Terminal::findOrFail($id);
        $terminal->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('messages.success.deleted'),
            'terminal' => $terminal,
        ]);
    }
}
