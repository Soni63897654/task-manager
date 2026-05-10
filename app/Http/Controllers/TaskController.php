<?php
namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'), 
        ];
    }
    public function dashboard() 
    {
        $totalTasks = Task::where('user_id', Auth::id())->count();
        $pendingTasks =Task::where('user_id', Auth::id())->where('status', 'pending')->count();
        return view('dashboard', compact('totalTasks', 'pendingTasks'));
    }

    public function index(Request $request) {
        return view('task.index');
    }

    public function fetchTaskList(Request $request)
    {
        $tasks = Task::where('user_id', auth()->id())
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('due_date'), function ($query) use ($request) {
                $query->whereDate('due_date', $request->due_date);
            })
            ->latest()
            ->paginate(6);

        return response()->json($tasks);
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors() 
            ], 422);
        }
        Task::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date
        ]);
        return response()->json(['success' => true, 'message' => 'Task Created!']);
    }

    public function update(Request $request) 
    {
        $id = $request->id; 
        $task = Task::where('user_id', auth()->id())->find($id);
        if (!$task) {
            return response()->json([
                'success' => false, 
                'message' => 'Task not found or unauthorized'
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'id'         => 'required|integer',
            'title'      => 'required|string|max:255',
            'due_date'   => 'required|date',
            'status'     => 'required',
            'description'=> 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $task->update($request->only(['title', 'description', 'status', 'due_date']));
        return response()->json([
            'success' => true, 
            'message' => 'Task Updated Successfully!'
        ]);
    }

    public function delete(Request $request) {
        Task::where('user_id', Auth::id())->findOrFail($request->id)->delete();
        return response()->json(['success' => true, 'message' => 'Task Deleted!']);
    }
}