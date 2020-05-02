<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Movie;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getMovies', function(){
   $peliculas = Movie::orderBy('title')->get();
   return $peliculas;
});

Route::post('guardarPelicula', function (Request $req){
    //dd($req);
    //var_dump($req);
    $input = $req->all();

    // $data = json_decode($req->input('LOQUEVIENEDESDEFETCH'));

    $findMovie = Movie::find($input['id']);
    if($input['id'] == $findMovie->id){
        $movie = $findMovie;
    }else{
        $movie = new Movie;
    }    

    $movie->poster = $input['title'].".jpg";
    $movie->title = $input['title'];
    $movie->rating = $input['rating'];
    $movie->awards = $input['awards'];
    $movie->release_date = $input['release_date'];

    $movie->save();

    return response(['status' => 'ok','mensaje' =>'Se ha guardado correctamente la pelicual '.$movie->title, 'pelicula' => $movie], 201);

});

Route::get('/delete/{id}', function($id){
    $pelicula = Movie::find($id);

    $pelicula->delete();
    return $pelicula;
});