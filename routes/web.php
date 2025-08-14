<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Ro749\SharedUtils\ImageMapPro;

Route::middleware('web')->group(function () {
    Route::get('/api/autosave/{class}', function ($class) {
        return $class::instance()->get();
    });
    
    Route::post('/api/autosave/{class}', function (Request $request,string $class) {
        $fullClass = 'App\\Autosaves\\' . $class;
        $ans = $fullClass::instance()->save($request->input('param'));
        return response()->json(['success' => true]);
    });
    
    Route::get('/table/{table}/get', function (Request $request,string $table) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        return response()->json($table->get(
            $request->get('start'), 
            $request->get('length'),
            $request->get('search')['value'],
            $request->get('order')[0],
            $request->get('filters')?? []
        ));
    });

    Route::get('/table/{table}/selectors', function (Request $request,string $table) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        return response()->json($table->get_selectors());
    });
    
    Route::post('/table/{table}/delete', function (Request $request,string $table) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        $table->delete((int) $request->input('id'));
        return response()->json(['success' => true]);
    });
    
    Route::post('/table/{table}/save', function (Request $request,string $table) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        $table->save($request);
        return response()->json(['success' => true]);
        //$data = $table->get_metadata($request);
        //return response()->json($data );
    });

    Route::get('/table/{table}/get/{layer}', function (Request $request,string $table,string $layer) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        return response()->json($table->get(
            $request->get('start'), 
            $request->get('length'),
            $request->get('search')['value'],
            $request->get('order')[0],
            $request->get('filters')?? [],
            $layer
        ));
    });

    Route::get('/table/{table}/metadata/{layer}', function (Request $request,string $table,string $layer) {
        $fullClass = 'App\\Tables\\' . $table;
        $table = new $fullClass();
        return response()->json($table->get_metadata($layer));
    });
    
    Route::post('/form/{form}', function (Request $request,$form) {
        $ans = [];
        $formClass = "App\\Http\\Requests\\".$form;
        $formRequest = new $formClass();
        $redirect = $formRequest->prosses($request);
        if($redirect!="") {  
            $ans = ["redirect" => $redirect];
        }
        return response()->json($ans);
    });
    
    Route::post('/logout', function (Request $request) {
        Auth::logout(); // Logs out the current user
    
        $request->session()->invalidate(); // Prevent session reuse
        $request->session()->regenerateToken(); // CSRF protection
    
        return response()->json(['redirect' => '/']);
    });

    Route::get('imagemappro', function (Request $request){
        $imp = new ImageMapPro(
            colors:["#00ff00","#ff0000","#ffff00"],
            opacities:[0.8,0.8,0.8]
        );
        return $imp->get();
    })->name('image-map-pro');
});
