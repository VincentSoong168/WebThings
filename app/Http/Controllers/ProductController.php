<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Auth;
use Session;
use Image;
use File;
use Storage;
use View;

class ProductController extends Controller
{
    protected $folder_name = 'product';
    protected $resize_width = 250;
    protected $resize_height = 350;

    public function __construct()
    {
        $this->middleware('auth');
        View::share('resize_width', $this->resize_width);
        View::share('resize_height', $this->resize_height);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_list = Product::paginate(10);

        return view('product.index', compact('product_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name'          =>  ['required', 'max:255'],
            'description'   =>  ['required', 'max:255'],
            'price'         =>  ['required', 'integer', 'min:0'],
            'image'         =>  ['required', 'image'],
        ];

        $this->validate($request, $rules);
        //-----------------------------------------------------------------驗證區

        $product = Product::create([
            'name'          =>  $request->name,
            'description'   =>  $request->description,
            'price'         =>  $request->price,
        ]);
        //-----------------------------------------------------------------create會受到fillable影響 save不會
        //-----------------------------------------------------------------為了拿到unique id 這裡先不儲存image

        if(!File::exists(storage_path().'/app/public/'.$this->folder_name)) {
            File::makeDirectory(storage_path().'/app/public/'.$this->folder_name);
        }

        $crop_array = [
            'width'     =>  $request->width,
            'height'    =>  $request->height,
            'x'         =>  $request->x,
            'y'         =>  $request->y,
        ];

        $db_filename = $this->crop_handle(
            $request->file('image'), 
            $product->id, 
            $crop_array, 
            $this->resize_width, 
            $this->resize_height
        );

        $product->image = $db_filename;
        $product->save();
        //-------------------------------------------------------------------圖片儲存區

        Session::flash('success', '產品已創建成功,');
        Session::flash('link_text', '點此進行查看');
        Session::flash('link_url', route('page.single.article', $product->id));

        return redirect()->route('product.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);

        return view('product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name'          =>  ['required', 'max:255'],
            'description'   =>  ['required', 'max:255'],
            'price'         =>  ['required', 'integer', 'min:0'],
            'image'         =>  ['image'],
        ];

        $this->validate($request, $rules);
        //-----------------------------------------------------------------驗證區

        $product = Product::find($id);

        if($request->hasFile('image')){
            if(!File::exists(storage_path().'/app/public/'.$this->folder_name)) {
                File::makeDirectory(storage_path().'/app/public/'.$this->folder_name);
            }

            $crop_array = [
                'width'     =>  $request->width,
                'height'    =>  $request->height,
                'x'         =>  $request->x,
                'y'         =>  $request->y,
            ];

            $db_filename = $this->crop_handle(
                $request->file('image'), 
                $product->id, 
                $crop_array, 
                $this->resize_width, 
                $this->resize_height
            );

            $product->image = $db_filename;
        }
        //-------------------------------------------------------------------圖片儲存區

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->save();
        //-------------------------------------------------------------------

        Session::flash('success', $product->name.'已更新,');
        Session::flash('link_text', '點此進行查看');
        Session::flash('link_url', route('page.single.product', $product->id));

        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        @Storage::delete('public/'.$product->image);

        $product->delete();

        Session::flash('success', '產品已刪除');

        return redirect(route('product.index'));
    }

    /**
     * 進行縮圖上傳的儲存處理
     */
    public function crop_handle($ori_image, $unique_id, $crop_array, $resize_width, $resize_height, $del_path=null)
    {
        if ($del_path) {
            @Storage::delete('public/'.$del_path);
        }

        $extension = $ori_image->getClientOriginalExtension();

        $db_filename =  $this->folder_name.'/'.$unique_id.'_'.date("YmdHis").'.'.$extension;

        //save()的起始路徑是在root/public 所以要先..回到root
        Image::make($ori_image)
            ->crop((int)$crop_array['width'], (int)$crop_array['height'], (int)$crop_array['x'], (int)$crop_array['y'])
            ->resize($resize_width, $resize_height)
            ->save('../storage/app/public/'.$db_filename);

        return $db_filename;
    }
}
