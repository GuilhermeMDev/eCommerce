<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $products = Product::all();
        return view('home', compact('products')); //É uma opçao de formata ao receber os dados do model..
    }

    //Abre a pagina de editar produto
    public function edit(Product $product)
    {
//        dd($product);
        return view('product_edit', [

            'product' => $product
        ]);
    }

    //Recebe requisicao para update (PUT)
    public function update(Product $product, Request $request)
    {
        $input = $request->validate([
            'name' => 'string|required',
            'price' => 'string|required',
            'stock' => 'integer|nullable',
            'cover' => 'image|nullable',
            'description' => 'string|nullable',
        ]);
        //tratamento de imagens para salvar no banco e localmente *storage/app/public/products
        if (!empty($input['cover']) && $input['cover']->isValid()) {

            $input['cover'] = $input['cover']->store('products');
//            $file = $input['cover'];
//            $path = $file->store('products');
//            $input['cover'] = $path;
        }
        $product->fill($input);
        $product->save();

        return Redirect::route('home');


    }

    //Abre a página de criar produto
    public function create()
    {
        return view('product_create');
    }

    //Recebe a requisiçao de criar (POST)
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => 'string|required',
            'price' => 'string|required',
            'stock' => 'integer|nullable',
            'cover' => 'image|nullable',
            'description' => 'string|nullable',
        ]);
        $input['slug'] = Str::slug($input['name']); //Criando um novo item no array a cima

        //tratamento de imagens para salvar no banco e localmente *storage/app/public/products
        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('products'); //Para alterar essa variavel de ambiente la no .env, assim passo a digitar somente 'products por exemplo.
            $input['cover'] = $path; //substituindo

        }
        Product::create($input);

        return Redirect::route('home');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return Redirect::route('home');
    }
}
