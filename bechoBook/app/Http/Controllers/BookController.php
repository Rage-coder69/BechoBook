<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class BookController extends Controller
{

    /*public function getPaginatedBooks(): \Illuminate\Http\JsonResponse
    {
        $books = Book::with('category', 'user')->paginate(30);
        return response()->json(['books' => $books], 200);
    }*/

    public function getBooks(Request $request): \Illuminate\Http\JsonResponse
    {
        if($request->filled('page') && !empty($request->filled('page'))){
            $books = Book::with('category', 'user')->paginate(30);
            if($request->page >= 1 && $request->page <= $books->lastPage()) {
                $books = Book::with('category', 'user')->orderBy('id', 'desc')->paginate(30, ['*'], 'page', $request->page);
                return response()->json(['books' => $books, 'pages' => $books->lastPage(), 'found' => count($books->items())], 200);
            }
            else{
                return response()->json(['error' => 'Page number does not exist!'], 400);
            }
        }else {
            $books = Book::with('category', 'user')->orderBy('id', 'desc')->get();
            return response()->json(['books' => $books, 'found' => $books->count()], 200);
        }
    }

    /*public function changeURL() {
        $books = Book::all();
        foreach ($books as $book) {
            $bookImages = $book->book_images;
            $temp = [];
            foreach($bookImages as $image){
                $image = str_replace("http://bookbecho.lazyguider.in/", "", $image);
                $temp[] = $image;
            }
            $book->book_images = $temp;
            $book->save();
        }
        return response()->json(['message' => 'success'], 200);
    }*/

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::with('category', 'user')->findOrFail($id);
        return response()->json(['book' => $book], 200);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'book_title' => "required",
            'book_description' => "required",
            'author_name' => "required",
            'book_edition' => "required",
            'book_publisher' => "required",
            'category_id' => "required",
            'location_longitude'  => "required",
            'location_latitude' => "required",
            'location' => "required",
            'book_price' => "required",
            'book_selling_price' => "required",
            'book_images' => "required",
            'user_id' => "required",
            // 'is_request' => "required",
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        $book = new Book();
        $book->book_title = $request->book_title;
        $book->book_description = $request->book_description;
        $book->author_name = $request->author_name;
        $book->book_edition = $request->book_edition;
        $book->book_publisher = $request->book_publisher;
        $book->category_id = $request->category_id;
        $book->location_longitude = $request->location_longitude;
        $book->location_latitude = $request->location_latitude;
        $book->location = $request->location;
        $book->book_price = $request->book_price;
        $book->book_selling_price = $request->book_selling_price;

        if ($request->is_request)
            $book->is_request = $request->is_request;
        else
            $book->is_request = 0;

        if ($request->hasFile('book_images')) {
            $images = array();
            foreach ($request->book_images as $book_image) {
                $image_name = time() . $book_image->getClientOriginalName();
                $book_image->move('storage/books', $image_name);
                $images[] = 'storage/books/'.$image_name;
            }
            $book->book_images = $images;
        }
        $book->user_id = $request->user_id;
        $book->save();
        return response()->json(['message' => 'Book Added Successfully!'], 200);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
       $book = Book::find($id);
        if ($book) {
            foreach ($book->book_images as $book_image) {
                $imagePath = parse_url($book_image);
                File::delete(public_path($imagePath['path']));
            }
            $book->delete();
            return response()->json(['message' => 'Book Deleted Successfully!'], 200);
        } else {
            return response()->json(['message' => 'Book Not Found!'], 404);
        }
    }

    public function filteredBooks(Request $request): \Illuminate\Http\JsonResponse
    {
        if($request->filled('page') && !empty($request->page)){
            $books = Book::with('category', 'user')->where('category_id', $request->id)->where('book_title','like', '%'.$request->name.'%')->where('author_name', 'like', '%'.$request->author_name.'%')->paginate(30);
            if($request->page >= 1 && $request->page <= $books->lastPage()) {
                $books = Book::with('category', 'user')->where('category_id', 'like','%'.$request->id.'%')->where('book_title','like', '%'.$request->name.'%')->where('author_name', 'like', '%'.$request->author_name.'%')->paginate(30);
                return response()->json(['books' => $books, 'pages' => $books->lastPage(),'found' => count($books->items())], 200);
            }
            else{
                return response()->json(['error' => 'Page number does not exist!'], 400);
            }
        }else {
            $books = Book::with('category', 'user')->where('category_id', 'like','%'.$request->id.'%')->where('book_title','like', '%'.$request->name.'%')->where('author_name', 'like', '%'.$request->author_name.'%')->get();
            return response()->json(['books' => $books], 200);
        }
    }
}