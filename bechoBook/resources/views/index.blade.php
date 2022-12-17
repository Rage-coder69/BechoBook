<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload PDF</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light">
    <div class="mb-4">
        @if(session()->has('message'))
            <p class="text-primary h5">{{ session('message') }}</p>
        @endif
    </div>
    <form action="{{ route('addPdf') }}" class="bg-white p-5 rounded-4 shadow-lg w-25" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="exampleInputName" class="form-label">PDF Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputName" aria-describedby="nameHelp" name="name" >
            @error('name')
                <p class="text-danger p-3">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="exampleInputPdfDocument" class="form-label">PDF Document</label>
            <input type="file" class="form-control @error('pdf_document') is-invalid @enderror" id="exampleInputPdfDocument" aria-describedby="pdfDocumentHelp" name="pdf_document">
            @error('pdf_document')
            <p class="text-danger p-3">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="exampleInputPdfImage" class="form-label">PDF Image</label>
            <input type="file" class="form-control @error('pdf_image') is-invalid @enderror" id="exampleInputPdfImage" aria-describedby="pdfImageHelp" name="pdf_image">
            @error('pdf_image')
            <p class="text-danger p-3">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="exampleFormControlPdfDescription" class="form-label">PDF Description</label>
            <textarea class="form-control @error('pdf_description') is-invalid @enderror" id="exampleFormControlPdfDescription" rows="5" name="pdf_description"></textarea>
            @error('pdf_description')
            <p class="text-danger p-3">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-3">
            <label for="exampleFormControlPdfCategory" class="form-label">Category</label>
            <select class="form-select @error('category_id') is-invalid @enderror" id="exampleFormControlPdfCategory" aria-label="Default select example" name="category_id">
                <option selected value="">Select</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->sub_category_name }}</option>
                @endforeach
            </select>
            @error('category_id')
            <p class="text-danger p-3">Category is required</p>
            @enderror
        </div>
        <div class="mb-3 d-flex flex-row justify-content-between align-items-center">
            <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            <button type="submit" class="btn btn-primary btn-lg">Add</button>
        </div>
    </form>
</div>
</body>
</html>
