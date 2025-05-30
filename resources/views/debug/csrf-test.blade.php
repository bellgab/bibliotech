<!DOCTYPE html>
<html>
<head>
    <title>CSRF Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>CSRF Test Form</h1>
    
    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif
    
    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('debug.csrf.post') }}">
        @csrf
        <input type="text" name="test_field" placeholder="Enter test text" required>
        <button type="submit">Submit</button>
    </form>
    
    <hr>
    
    <h2>Session Info</h2>
    <ul>
        <li>CSRF Token: {{ csrf_token() }}</li>
        <li>Session ID: {{ session()->getId() }}</li>
        <li>Session Driver: {{ config('session.driver') }}</li>
    </ul>
</body>
</html>
