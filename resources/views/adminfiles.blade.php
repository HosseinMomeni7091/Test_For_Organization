@extends("layout.admin")


@section("body")
<!-- messages -->
<div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
    <span class="font-medium">Welcome to our website dear {{auth()->user()->email}}</span>
</div>
<div class="mx-8">
    <table class="text-center text-2xl w-1/2 font-bold">
        <tr>
            <td>No.</td>
            <td>Name</td>
            <td>Size</td>
            <td>Download Link</td>
        </tr>
        @foreach ($files as $key => $file)
        <tr class="text-center text-xl font-medium">
            <td>{{($loop->index)+1}}</td>
            <td>{{$file->name}}</td>
            <td>{{$file->size}}</td>
            <td>
                <a href="{{$downloads[$key]}}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
                    </svg>
                    <span>Download</span>
                </a>
            </td>
        </tr>
        @endforeach
        <tr>
            <td>Total </td>
            <td>{{$count}}</td>
        </tr>
    </table>
</div>



@endsection()