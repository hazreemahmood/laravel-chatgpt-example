<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validate the file upload
        $request->validate([
            'file' => 'required|file|mimes:jpg,png,jpeg,gif,pdf|max:2048',
        ]);

        // Check if the file exists in the request
        if ($request->hasFile('file')) {
            // Retrieve the file
            $file = $request->file('file');

            // Generate a unique name for the file before saving it
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Define the path where you want to store the file
            $filePath = $file->storeAs('uploads', $fileName, 'public'); // Store in the "public" disk

            // You can store the file path in the database or return it as a response
            return back()
                ->with('success', 'File uploaded successfully.')
                ->with('file', $fileName);
        }

        return back()->with('error', 'Please select a file to upload.');
    }
}
