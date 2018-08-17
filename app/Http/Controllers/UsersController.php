<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Users;
use Session;
use View;
use Image;
use File;
use Storage;
use Hash;

class UsersController extends Controller
{
    protected $folder_name = 'user';
    protected $resize_width = 200;
    protected $resize_height = 200;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        View::share('resize_width', $this->resize_width);
        View::share('resize_height', $this->resize_height);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('front.index');
    }

    public function edit(Request $request, $id)
    {
        $user = Users::find($id);

        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name'                  =>  ['required', 'string'],
            'old_password'          =>  ['nullable', 'required_with:password,password_confirmation', 'string', 'min:6'],
            'password'              =>  ['nullable', 'required_with:old_password,password_confirmation', 'string', 'min:6', 'confirmed'],
            'password_confirmation' =>  ['required_with:old_password,password'],
            'image'                 =>  ['image'],
        ];

        $this->validate($request, $rules);
        //------------------------------------------------------------

        $user = Users::find($id);
        $user->name = $request->name;

        if ( !empty($request->old_password) ) {
            if ( Hash::check($request->old_password, $user->password) ) {
                $user->password = bcrypt($request->password);
            } else {
                Session::flash('fail', '密碼錯誤');
                return redirect()->back();
            }
        }
        //-----------------------------------------------------------------------------------
        if ($request->hasFile('image')) {
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
                $user->id, 
                $crop_array, 
                $this->resize_width, 
                $this->resize_height, 
                $user->image
            );

            $user->image = $db_filename;
        }
        //-----------------------------------------------------------------------------------

        $user->save();

        return redirect()->route('home');
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
