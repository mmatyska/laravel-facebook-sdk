@extends('layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <a href="/"  class="btn btn-primary">Home</a>
            </div>
        </div>
        <h3>Personal information</h3>
        <ul>
            @foreach($userBasicData as $userInfo)
                    <li>{{$userInfo}}</li>
            @endforeach

        </ul>

        <h3>User albums</h3>
        <div class="row">
            <div class="col-md-12">
                <div class="thumbnail  center-block">

                    @foreach($userAlbums as $album)
                        <h4>{{$album['name']}}</h4>
                        @foreach($album['photos'] as $photos)
                            <img class="img-thumbnail img-responsive" style="display:inline-block" width="304" height="236" src="{{$photos['picture']}}" alt="">
                        @endforeach
                    @endforeach

                </div>
            </div>
        </div>

        <h3>User Likes</h3>
        <ul>

            @foreach($userLikes as $likes)
                <li>{{$likes['name']}}</li>
            @endforeach

        </ul>
    </div>
@endsection
