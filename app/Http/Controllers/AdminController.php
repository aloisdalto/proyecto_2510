<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index(){
        return view('admin.index');
    }

    public function brands(){
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand(){
        return view('admin.brand-add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name'=> 'required',
            'slug'=> 'required|unique:brands,slug',
            'img'=>'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->slug);
        $img = $request->file('img');
        $file_extention = $request->file('img')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateBrandThumbailsImage($img,$file_name);
        $brand->img = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status','Brand has been added succesfully!');
    }


    public function GenerateBrandThumbailsImage($img, $imgName) {
        $destinationPath = public_path('uploads/brands');

        // Asegúrate de que la carpeta exista
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true); // Crea la carpeta si no existe
        }

        //Image::read()
        $image = Image::read($img->path());
        $image->cover(124, 124, "top");
        $image->resize(124, 124, function($constraint) {
            $constraint->aspectRatio();
        });

        // Guarda la imagen en la ruta completa, asegurándote de que no haya espacios
        $image->save($destinationPath.'/'.$imgName);
    }

    /* public function GenerateBrandThumbailsImage($img , $imgName){
        $destinationPath = public_path('uploads/brands/');
        $image = Image::read($img->path());
        $image->cover(124,124,"top");
        $image->resize(124,124,function($constraint){
            $constraint -> aspectRatio();
        })->save($destinationPath.'/',$imgName);
    } */
}
