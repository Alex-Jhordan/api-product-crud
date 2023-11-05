<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = [];

        $validation = $this->validation($request->all());

        if (!is_array($validation)) {
            Product::create($request->all());
            array_push($response, ['status' => 'success']);
            return response()->json($response);
        }
        else {
            return response()->json($validation);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $response = [];
        $validation = $this->validation($request->all());

        if (!is_array($validation)) {
            $product = Product::find($id);

            if ($product) {
                $product->fill($request->all())->save();
                array_push($response, ['status'=> 'success']);
            }
            else {
                array_push($response, ['status' => 'error']);
                array_push($response, ['errors' => 'No existe el ID']);
            }
            return response()->json($response);
        }
        else {
            return response()->json($validation);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = [];
        $product = Product::find($id);

        if ($product) {
            $product->delete();
            array_push($response, ['status'=> 'success']);
        }
        else {
            array_push($response, ['status' => 'error']);
            array_push($response, ['errors' => 'No existe el ID']);
        }
        return response()->json($response);
    }

    public function validation($parameters)
    {
        $response = [];

        $messages= [
            'max' => 'El campo :attribute NO debe tener más de :max caracteres',
            'required' => 'El campo :attribute NO debe de estar vacío',
            'price.numeric' => 'El precio debe ser numérico'
        ];
        $attributes = [
            'name' => 'nombre',
            'description' => 'descripción',
            'price' => 'precio'
        ];

        $validation = Validator::make($parameters,
        [
            'name' => 'required|string|max:80',
            'description' => 'required|string|max:150',
            'price' => 'required|decimal:0, 2',
        ], $messages, $attributes);

        if ($validation->fails()) {
            array_push($response, ['status' => 'error']);
            array_push($response, ['errors' => $validation->errors()]);
            return $response;
        }
        else {
            return true;
        }
    }
}
