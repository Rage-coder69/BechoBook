<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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
                return response()->json(['books' => $books, 'pages' => $books->lastPage(), 'found' => count($books->items()),
                    'success' => true,
                    'message' => 'Books fetched successfully'
                ], 200);
            }
            else{
                return response()->json(['error' => 'Page number does not exist!',
                    'success' => false
                ], 400);
            }
        }else {
            $books = Book::with('category', 'user')->orderBy('id', 'desc')->get();
            return response()->json(['books' => $books, 'found' => $books->count(),
                'success' => true,
                'message' => 'Books fetched successfully'
            ], 200);
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
        return response()->json(['book' => $book,
            'success' => true,
            'message' => 'Book fetched successfully'
        ], 200);
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
        return response()->json(['message' => 'Book Added Successfully!',
            'success' => true,
        ], 200);
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
            return response()->json(['message' => 'Book Deleted Successfully!',
                'success' => true,
            ], 200);
        } else {
            return response()->json(['message' => 'Book Not Found!',
                'success' => false,
            ], 404);
        }
    }

    public function point2point($lat1, $lon1, $lat2, $lon2): float|int
    {
        /*$latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * 6371;*/

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344);
    }

    public function filteredBooks(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'user_lat' => 'required',
            'user_long' => 'required',
            'category_id' => 'nullable',
            'author_name' => 'nullable',
            'book_title' => 'nullable',
        ]);

        if($request->has('category_id') && $request->category_id == 0) {
            $books = Book::with('category','user')->get();
            foreach ($books as $book) {
                $book->distance = $this->point2point($request->user_lat, $request->user_long, $book->location_latitude, $book->location_longitude);
            }

            $books = $books->sortBy([
                ['distance', 'asc'],
            ]);

            // paginate the books
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 30;
            $pagedData = $books->slice(($currentPage - 1) * $perPage, $perPage)->flatten()->all();
            $books = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($books), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

            return response()->json(['books' => $books, 'found' => $books->count(),
                'success' => true,
                'message' => 'Books fetched successfully'
            ], 200);


        }else {
            //$books = Book::with('category', 'user')->where('category_id','=', $request->id)->where('book_title','like', '%'.$request->name.'%')->where('author_name', 'like', '%'.$request->author_name.'%')->get();
            //$books = Book::where('category_id', $request->category_id)->orWhere('book_title', 'like', '%'.$request->book_title.'%')->orWhere('author_name', 'like', '%'.$request->author_name.'%')->with('category', 'user')->get();
            /*$query = "SELECT * FROM books";
            $bindings = [];

            if ($request->category_id) {
                $query .= " WHERE category_id = ?";
                $bindings[] = $request->category_id;
            }

            if ($request->book_title) {
                $query .= ($request->category_id ? " AND" : " WHERE") . " book_title LIKE ?";
                $bindings[] = '%' . $request->book_title . '%';
            }

            if ($request->author_name) {
                $query .= ($request->category_id || $request->book_title ? " AND" : " WHERE") . " author_name LIKE ?";
                $bindings[] = '%' . $request->author_name . '%';
            }

            $books = collect( DB::select(DB::raw($query), $bindings));*/
            $books = Book::with('user', 'category')
                ->when($request->category_id, function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                })
                ->when($request->book_title, function ($query) use ($request) {
                    return $query->where('book_title', 'like', '%'.$request->book_title.'%');
                })
                ->when($request->author_name, function ($query) use ($request) {
                    return $query->where('author_name', 'like', '%'.$request->author_name.'%');
                })
                ->get();

            foreach ($books as $book) {
                $book->distance = $this->haversineGreatCircleDistance($request->user_lat, $request->user_long, $book->location_latitude, $book->location_longitude);
            }

            $books = $books->sortBy([
                ['distance', 'asc'],
            ]);

            // paginate the books
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 30;
            // added flatten to remove the numeric keys of the objects
            $pagedData = $books->slice(($currentPage - 1) * $perPage, $perPage)->flatten()->all();
            $books = new \Illuminate\Pagination\LengthAwarePaginator($pagedData, count($books), $perPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

            return response()->json(['books' => $books, 'found' => $books->count(),
                'success' => true,
                'message' => 'Books fetched successfully',
            ], 200);
        }
    }
}
