<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tag_list = Tag::orderBy("id")->paginate(10);

        return view('tag.index', compact('tag_list'));
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
            'name'      =>  ['required', 'max:255', 'unique:tags'],
        ];

        $this->validate($request, $rules);
        //----------------------------------------------------------

        $tag = Tag::create([
            'name'      =>  $request->name,
        ]);

        Session::flash('success', '標籤 : '.$tag->name.' 新增成功');

        return redirect()->route('tag.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
            'name'      =>  ['required', 'max:255', "unique:tags,name,$id"],
        ];

        $this->validate($request, $rules);
        //----------------------------------------------------------

        $tag = Tag::find($id);
        $tag->name = $request->name;
        $tag->save();

        Session::flash('success', '標籤 : '.$tag->name.' 更新成功');

        return redirect()->route('tag.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        $tag->article()->detach();

        $tag->delete();

        Session::flash('success', '標籤 : '.$tag->name.' 已刪除');

        return redirect()->route('tag.index');
    }
}
