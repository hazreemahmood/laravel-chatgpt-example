<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{

    public function show($user_id)
    {
        $file_upload = FileUpload::where('user_id', $user_id)->get(DB::raw('*, REPLACE(file_upload_url, "uploads/", "") AS file_url'));
        
        return view('file_upload', ['file_upload' => $file_upload]);
    }

    public function upload(Request $request)
    {
        // return response()->json(['success' => true, 'message' => $request->hasFile('file')]);
        // Validate the uploaded file
        // $request->validate([
        //     'file' => 'required|file|mimes:pdf,txt,TXT|max:2048',
        // ]);

        // Check if the file is present in the request
        if ($request->hasFile('file')) {
            // Store the file
            $file = $request->file('file');
            $user_id = $request->post('user_id');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = $user_id . '_' . time() . '.' . $fileExtension;
            $filePath = $file->storeAs('uploads', $fileName, 'public'); // Store the file in the 'public' disk
            FileUpload::createFileUpload($user_id, $filePath);
            return response()->json(['success' => true, 'message' => 'Content saved successfully!']);
            // Redirect to the document viewing page with the file name
        }

        // If no file is uploaded, return an error response
        return response()->json(['success' => false, 'message' => 'No file uploaded']);
    }

    public function viewDocument($fileName)
    {
        $filePath = storage_path('app/public/uploads/' . $fileName);

        // Determine if it's a PDF or text file
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $content = '';

        if ($fileExtension == 'txt') {
            // Read the content of the .txt file
            $content = file_get_contents($filePath);
        } elseif ($fileExtension == 'pdf') {
            // Parse the PDF file using the PDF Parser library
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $content = $pdf->getText();  // Extract the text from the PDF
        }

        // Return the view with the extracted content and file type
        return view('file_viewer', ['content' => $content, 'fileType' => $fileExtension, 'fileName' => $fileName]);
    }

    public function saveDocument(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'path' => 'required|string',
        ]);

        // Save the content back to the original file
        $filePath = storage_path("app/public/uploads/{$request->path}");

        // Save content based on the file type
        if (pathinfo($request->path, PATHINFO_EXTENSION) === 'pdf') {
            // Note: Overwriting PDF files programmatically can be complex
            // You might need a PDF library to create a new PDF from the text.
            return response()->json(['error' => 'Saving PDF content directly is not supported.'], 400);
        } else {
            file_put_contents($filePath, $request->content);
        }

        return response()->json(['message' => 'Content saved successfully!']);
    }

    public function destroy($id){
        FileUpload::destroy($id);
        return response()->json(['success' => true, 'message' => 'File Deleted.']);
    }
}
