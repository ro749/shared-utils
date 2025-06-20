<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    $table = $fullClass::instance();
    return response()->json($table->get(
        $request->get('start'), 
        $request->get('length'),
        $request->get('search')['value'],
        $request->get('order')[0],
        $request->get('filter')
    ));
});

Route::post('/table/{table}/delete', function (Request $request,string $table) {
    $fullClass = 'App\\Tables\\' . $table;
    $table = $fullClass::instance();
    $table->delete((int) $request->input('id'));
    return response()->json(['success' => true]);
});

Route::post('/table/{table}/save', function (Request $request,string $table) {
    $fullClass = 'App\\Tables\\' . $table;
    $table = $fullClass::instance();
    $table->save($request->input('id'),$request->input('args'));
    return response()->json(['success' => true]);
});

Route::post('/form/{form}', function (Request $request,$form) {
    $ans = [];
    $formClass = "App\\Http\\Requests\\".$form;
    $formRequest = $formClass::instanciate();
    $url = $formRequest->prosses($request);
    if($url!="") {  
        $ans = ["url" => $url];
    }
    return response()->json($ans);
});