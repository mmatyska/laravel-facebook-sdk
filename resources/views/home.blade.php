@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">User Some Data From FB...</div>

                <div class="panel-body">
                    You are logged in!
                    {{ Auth::user()->name }}
                    <div>
                        <h3>Personal Data</h3>
                        <ul>
                            @if(session('userPersonalData'))
                                @foreach(session('userPersonalData') as $userPersonalData)
                                    <li>{{ $userPersonalData }}</li>
                                @endforeach
                            @else
                                Getting Personal Data failed...
                            @endif
                        </ul>
                    </div>
                    <div>
                        <h3>User lasts albums</h3>
                        @if(session('userPhotos'))
                            @foreach(session('userPhotos') as $photo)
                                <img src="{{ $photo['picture'] }}" alt="">
                            @endforeach
                        @else
                            Getting Albums failed...
                        @endif
                    </div>
                    <div>
                        <h3>User Lasts Activities</h3>
                        @if(session('userPosts'))
                            @foreach(session('userPosts') as $posts)
                                @foreach($posts as $post )
                                    <ul>
                                    @if(array_key_exists('story',$posts))
                                        <li>{{$posts['story']}}</li>
                                    @endif
                                    </ul>
                                @endforeach
                            @endforeach
                        @else
                            Getting User Ativities failed...
                        @endif
                    </div>
                    <div>
                        <h3>User Likes</h3>
                        <ul>
                        @foreach(session('userLikes') as $likes)
                            <li>{{$likes['name']}}</li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
