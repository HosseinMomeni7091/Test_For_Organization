@extends("layout.admin")


@section("body")
<!-- messages -->
<div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
    <span class="font-medium">Welcome to our website dear {{auth()->user()->email}}</span>
</div>




@endsection()