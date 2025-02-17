<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments');

        $attachment = $task->attachments()->create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'File uploaded successfully');
    }

    public function destroy(Attachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::delete($attachment->file_path);
        $attachment->delete();

        return redirect()->back()->with('success', 'File deleted successfully');
    }

    public function download(Attachment $attachment)
    {
        $this->authorize('view', $attachment);

        return Storage::download(
            $attachment->file_path,
            $attachment->name
        );
    }
}
