<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Log;

class DocumentApiController extends Controller
{
    /**
     * @function: to fetch documents data.
     *
     * @author: Santhosha G
     *
     * @created-on: 11 Feb 2026
     *
     * @updated-on: 11 Feb 2026
     */
    public function index()
    {
        try {
            $documents = Document::get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'documents' => $documents]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to Upload documents data.
     *
     * @author: Santhosha G
     *
     * @created-on: 12 Feb 2026
     *
     * @updated-on: 12 Feb 2026
     */
    public function uploadFile(Request $request)
    {
        $base64 = $request->image;

        $data     = explode(',', $base64);
        $fileData = base64_decode($data[1]);

        $folder = $request->folder;

        $filename = $request->filename . '_' . time() . '.' . $request->extension;

        Storage::disk('public')->put("$folder/$filename", $fileData);

        return response()->json([
            'status'  => 'S',
            'message' => trans('returnmessage.document_uploaded_success'),
            'path'    => "$folder/$filename",
        ]);
    }

    /**
     * @function: to Upload documents data.
     *
     * @author: Santhosha G
     *
     * @created-on: 11 Feb 2026
     *
     * @updated-on: 11 Feb 2026
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            // file object validation
            'file'                => 'required|array',
            'file.file_name'      => 'required|string',
            'file.file_path'      => 'required|string',
            'file.file_type'      => 'required|string',
            'file.file_size'      => 'required|integer',
            'file.mime'           => 'required|string',
        ]);

        $authUserId = Auth::id();

        $document = Document::create([
            'title'       => $request->title,
            'description' => $request->description,
            'category'    => $request->category,
            'file_name'   => $request->file['file_name'],
            'file_path'   => $request->file['file_path'],
            'file_type'   => $request->file['file_type'],   // extension (xlsx)
            'file_size'   => $request->file['file_size'],   // bytes
            'mime'   => $request->file['mime'],
            'created_by'  => $authUserId,
            'updated_by'  => $authUserId,
        ]);

        return response()->json([
            'status'  => 'S',
            'message' => trans('returnmessage.document_uploaded_success'),
            'data'    => $document,
        ]);
    }

    /**
     * @function: to updating the documents data.
     *
     * @author: Santhosha G
     *
     * @created-on: 12 Feb 2026
     *
     * @updated-on: 12 Feb 2026
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            // ✅ file optional during update
            'file'                => 'nullable|array',
            'file.file_name'      => 'nullable|string',
            'file.file_path'      => 'nullable|string',
            'file.file_type'      => 'nullable|string',
            'file.file_size'      => 'nullable|integer',
            'file.mime'           => 'nullable|string',
        ]);

        $authUserId = Auth::id();

        $document = Document::findOrFail($id);

        // ✅ basic fields
        $document->title       = $request->title;
        $document->description = $request->description;
        $document->category    = $request->category;

        // ✅ only update file if new one provided
        if ($request->filled('file')) {
            $document->file_name = $request->file['file_name'];
            $document->file_path = $request->file['file_path'];
            $document->file_type = $request->file['file_type'];
            $document->file_size = $request->file['file_size'];
            $document->mime      = $request->file['mime'];
        }

        $document->updated_by = $authUserId;

        $document->save();

        return response()->json([
            'status'  => 'S',
            'message' => trans('returnmessage.document_updated_success'),
            'data'    => $document,
        ]);
    }

    /**
     * @function: to edit Document details.
     *
     * @author: Santhosha G
     *
     * @created-on: 12 Feb, 2022
     *
     * @updated-on: N/A
     */
    public function editDocument($slug)
    {
        try {
            $documents = Document::where('slug', $slug)->first();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'documents' => $documents]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function delete($id)
    {
        try {
            $document = Document::find($id);

            if (!$document) {
                return response()->json([
                    'status' => 'F',
                    'message' => 'Document not found'
                ]);
            }

            $path = $document->file_path;

            // delete physical file
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // delete DB record
            $document->delete();

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);

        } catch (\Exception $e) {
            Log::info($e);
            return response()->json([
                'status' => 'F',
                'message' => $e->getMessage()
            ]);
        }
    }

}
