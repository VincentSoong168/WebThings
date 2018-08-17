<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Session;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category_list = Category::where("parent_id", null)->orderBy('id')->paginate(10);

        $cate_parent_list = Category::where('parent_id', null)->get();

        return view('category.index', compact('category_list', 'cate_parent_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules=[
            'name'      =>  ['required', 'max:255', 'unique:categories'],
            'parent_id' =>  ['required', 'integer', 'min:0'],
        ];

        $this->validate($request, $rules);
        //----------------------------------------------------------

        if ($request->parent_id==0) {
            $request->parent_id = null;
        }

        $category = Category::create([
            'name'      =>  $request->name,
            'parent_id' =>  $request->parent_id,
        ]);

        Session::flash('success', '文章類別 : '.$category->name.'  新增成功');

        return redirect(route('category.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);

        if ( $category->parent_id==0 && sizeof($category->children)==0 ) {
            $cate_parent_list = Category::where('parent_id', 0)->where('id', '!=', $id)->get();
        }else{
            $cate_parent_list = Category::where('parent_id', 0)->get();
        }

        return view('category.edit', compact('category', 'cate_parent_list'));
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
            'name'      =>  ['required', 'max:255', "unique:categories,name,$id"],
            'parent_id' =>  ['integer', 'min:0'],
        ];

        $this->validate($request, $rules);
        //----------------------------------------------------------

        $category = Category::find($id);

        $category->name = $request->name;
        if(isset($request->parent_id)){
            if ($request->parent_id==0) {
                $request->parent_id = null;
            }

            $category->parent_id = $request->parent_id;
        }
        $category->save();

        Session::flash('success', '文章類別 : '.$category->name.'  更新成功');

        return redirect(route('category.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        $category->delete();

        Session::flash('success', '文章類別 : '.$category->name.'刪除成功');

        return redirect(route('category.index'));
    }
}
