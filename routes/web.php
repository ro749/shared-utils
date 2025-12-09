<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
Route::middleware('web')->group(function () {
    
    Route::get('/table/{table}/get', function (Request $request,string $table) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        $table->make_it_modifiable();
        return response()->json($table->get(
            $request->get('start'), 
            $request->get('length'),
            $request->get('search')['value'],
            $request->get('order')[0]??null,
            $request->get('filters')?? [],
            $request->get('start_date')?? null,
            $request->get('end_date')?? null
        ));
    });

    Route::get('/table/{table}/selectors', function (Request $request,string $table) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        $table->make_it_modifiable();
        return response()->json($table->get_selectors());
    });
    
    Route::post('/table/{table}/delete', function (Request $request,string $table) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        $table->delete((int) $request->input('id'));
        return response()->json(['success' => true]);
    });
    
    Route::post('/table/{table}/save', function (Request $request,string $table) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        $table->save($request);
        return response()->json(['success' => true]);
        //$data = $table->get_metadata($request);
        //return response()->json($data );
    });

    Route::get('/table/{table}/get/{layer}', function (Request $request,string $table,string $layer) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        return response()->json($table->get(
            $request->get('start'), 
            $request->get('length'),
            $request->get('search')['value'],
            $request->get('order')[0]??null,
            $request->get('filters')?? [],
            $layer
        ));
    });

    Route::get('/table/{table}/metadata', function (Request $request,string $table) {
        $fullClass = config('overrides.tables.'.$table);
        $table = new $fullClass();
        return response()->json(
            $table->get_metadata(
                $request->get('layer', 0), 
                $request->get('selected_id', 0)
            )
        );
    });
    
    Route::post('/form/{form}', function (Request $request,$form) {
        $ans = [];
        $formClass = config('overrides.forms.'.$form);
        $form = new $formClass();
        $data = $form->prosses($request);
        if($data!="" && is_string($data)) { 
            $ans = ["redirect" => $data];
            return response()->json(data: $ans);
        }
        return response()->json(data: $data);
    });

    Route::post('/form/{form}/preview/{field}', function (Request $request,$form,$field) {
        $ans = [];
        $formClass = config('overrides.forms.'.$form);
        $form = new $formClass();
        $file = $request->file($field);
        $form->fields[$field]->preview($file);
    });

    
    Route::post('/form/{form}/validate', function (Request $request,$form) {
        $ans = [];
        $formClass = config('overrides.forms.'.$form);
        $form = new $formClass();
        return $form->validate_field($request);
    });

    Route::get('/data/{data}', function (Request $request,$data) {
        $fullClass = config('overrides.data.'.$data);
        $data = new $fullClass();
        Log::debug($data->get_data());
        return response()->json($data->get_data());
    });
    
    Route::post('/logout', function (Request $request) {
        Auth::logout(); // Logs out the current user
    
        $request->session()->invalidate(); // Prevent session reuse
        $request->session()->regenerateToken(); // CSRF protection
    
        return response()->json(['redirect' => '/']);
    });
});
