<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PdfController extends Controller
{

    public function index()
    {
        $pdfs = Pdf::all();
        return response()->json(['pdfs' => $pdfs], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'pdf_document' => 'required|mimes:pdf',
            'pdf_image' => 'required|mimes:jpeg,jpg,png',
            'pdf_description' => 'required',
            'category_id' => 'required'
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $pdf = new Pdf();
        $pdf->name = $request->name;
        $pdf->pdf_description = $request->pdf_description;
        $pdf->category_id = $request->category_id;
        $image_name = time() . $request->file('pdf_document')->getClientOriginalName();
        $request->file('pdf_document')->move('storage/pdf_document', $image_name);
        $pdf->pdf_document = asset('storage/pdf_document/' . $image_name);
        $pdf_name = time() . $request->file('pdf_image')->getClientOriginalName();

        $request->file('pdf_image')->move('storage/pdf_images', $pdf_name);
        $pdf->pdf_image = asset('storage/pdf_images/' . $pdf_name);

        $pdf->save();

      // return redirect()->back()->with(['message' => 'PDF added Successfully!']);
      return response()->json(['message' => 'PDF added Successfully!'], 200);
    }

    public function destroy($id)
    {
        $pdf = Pdf::findOrFail($id);
        //regex for extracting the filename from the link of any length - not implemented
        //preg_match("([^/]+$)", $pdf->pdf_image, $matches);
        if ($pdf) {
            $imagePath = parse_url($pdf->pdf_image);
            File::delete(public_path($imagePath['path']));
            $documentPath = parse_url($pdf->pdf_document);
            File::delete(public_path($documentPath['path']));
            $pdf->delete();
            return response()->json(['message' => 'Pdf deleted Successfully!'], 200);
        } else {
            return response()->json(['error' => 'Pdf not found!'], 404);
        }
    }
}
