<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function uploadFiles()
    {
        $files = MediaFile::where('user_id', Auth::id())->latest()->get();
        return view('media.files-upload', compact('files'));
    }

    public function uploadFilesPost(Request $request)
    {
        $request->validate([
            'files.*' => 'required|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'files.*.mimes' => 'File type not supported! Please upload only JPG, JPEG, PNG, or PDF.',
            'files.*.max'   => 'Each file should be less than 5MB.',
        ]);
        if ($request->hasFile('files')) {
            $uploadedFiles = [];
            foreach ($request->file('files') as $file) {
                $path = $file->store('documents', 'public');
                $media = MediaFile::create([
                    'user_id'       => Auth::id(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_type'     => $file->getClientMimeType(),
                ]);

                $uploadedFiles[] = [
                    'url'  => asset('storage/' . $path),
                    'name' => $media->original_name,
                    'type' => $media->file_type
                ];
            }
            return response()->json(['success' => true, 'files' => $uploadedFiles]);
        }
        return response()->json(['success' => false, 'message' => 'No files detected'], 400);
    }
}