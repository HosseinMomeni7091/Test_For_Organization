@extends("layout.buyer")


@section("body")
<!-- messages -->
<div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
    <span class="font-medium">{{$message}}</span>
</div>

<!-- Upload Files -->
<form action="{{route('upload')}}" class="mx-8 mb-8" method="post" enctype="multipart/form-data">
    @csrf
    <label class="block mb-2  text-2xl font-bold text-black-900 dark:text-gray-300" for="file_input">Upload file</label>
    <label class="font-medium text-lg">Name</label>
    <input class="w-1/8 h-8 m-2 p-2 border-2 rounded-lg" type="text" name="name" placeholder="Hossein (Optinal)" value="{{ old('name') }}" @readonly($count>=4)>
    <input class="block  w-1/3 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file" name="uploadedfile">
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">All type allowed to upload except virus</p>
    <div class="flex flex-row w-1/3 justify-between content-center">
        <button type="submit" class="w-1/8 text-black bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" @disabled($count>=4) >Upload</button>

        <p>Result of scan of uploaded file: </p>
    </div>
    <p class="text-red-600 text-medium font-medium">@if($count>=4)
        You already uploaded 4 files and there are not allowed to upload anymore.
        @endif</p>
    <p class="text-red-600 text-medium font-medium">Result of check of Duplication : {{$resultOfCheck["duplicate"]??""}}</p>

</form>

<div class="mx-8">
    <div class="text-2xl font-bold text-center border-b-4 w-1/2">
        Your Uploaded Files are as below:
    </div>
</div>

<div class="mx-8">
    <table class="text-center text-2xl w-1/2 font-bold">
        <tr>
            <td>No.</td>
            <td>Name</td>
            <td>Size</td>
        </tr>
        @foreach ($files as $file)
        <tr class="text-center text-xl font-medium">
            <td>{{($loop->index)+1}}</td>
            <td>{{$file->name}}</td>
            <td>{{$file->size}}</td>
        </tr>
        @endforeach
        <tr>
            <td>Total </td>
            <td>{{$count}}</td>
        </tr>
    </table>
</div>









@endsection()