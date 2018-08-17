<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articles;
use App\Category;
use App\Tag;
use Auth;
use Session;
use Image;
use File;
use Storage;
use View;

class ArticleController extends Controller
{

    protected $folder_name = 'article';
    protected $resize_width = 1080;
    protected $resize_height = 400;

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
        $article_list = Articles::orderBy('id', 'DESC')->paginate(10);

        return view('article.index', compact('article_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category_list = Category::where('parent_id', 0)->get();

        $tag_list = Tag::orderBy("id")->get();

        // 原本想法 只要底下沒有類別 主類別就可以作為文章類別使用
        // 但後續若主類別底下新增了子類別 避免麻煩
        // foreach ($category_list as $key => $category) {
        //     if(sizeof($category->children)){
        //         unset($category_list[$key]);
        //     }
        // }

        return view('article.create', compact('category_list', 'tag_list'));
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
            'title'         =>  ['required', 'max:255'],
            'slug'          =>  ['required', 'max:255', 'unique:articles'],
            'description'   =>  ['required'],
            'content'       =>  ['required'],
            'category_id'   =>  ['required','integer', 'min:0'],
            'status'        =>  ['required', 'boolean'],
            'image'         =>  ['required', 'image'],
        ];

        $messages = [
            'slug.unique'       =>  '建議連結已被使用',
            'content.required'  =>  '文章內容不可空白',
        ];

        $this->validate($request, $rules, $messages);
        //-----------------------------------------------------------------驗證區

        if ($request->category_id==0) {
            $request->category_id = null;
        }

        $article = Articles::create([
            'author_id'     =>  Auth::id(),    
            'title'         =>  $request->title,
            'slug'          =>  str_slug($request->slug),
            'description'   =>  $request->description,
            'content'       =>  $request->content,
            'category_id'   =>  $request->category_id,
            'status'        =>  $request->status,
        ]);
        //-----------------------------------------------------------------create會受到fillable影響 save不會
        //-----------------------------------------------------------------為了拿到unique id 這裡先不儲存image

        if(!File::exists(storage_path().'/app/public/article')) {
            File::makeDirectory(storage_path().'/app/public/article');
        }

        $crop_array = [
            'width'     =>  $request->width,
            'height'    =>  $request->height,
            'x'         =>  $request->x,
            'y'         =>  $request->y,
        ];

        $db_filename = $this->crop_handle(
            $request->file('image'), 
            $article->id, 
            $crop_array, 
            $this->resize_width, 
            $this->resize_height
        );

        $article->image = $db_filename;
        $article->save();
        //-------------------------------------------------------------------圖片儲存區

        $article->tag()->sync($request->tag_id, false);
        //-------------------------------------------------------------------文章標籤多對多關聯區

        Session::flash('success', '文章已儲存成功,');
        Session::flash('link_text', '點此進行查看');
        Session::flash('link_url', route('page.single.article', $article->slug));

        return redirect(route('article.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $article = Articles::find($id);

        return view('article.show', compact('article', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = Articles::find($id);

        $category_list = Category::where('parent_id', 0)->get();

        $tag_list = Tag::orderBy("id")->get();

        $article_tag = $this->get_tag_id_implode($id);

        return view('article.edit', compact('article', 'category_list', 'tag_list', 'article_tag'));
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
            'title'         =>  ['required', 'max:255'],
            'slug'          =>  ['required', 'max:255', "unique:articles,slug,$id"],
            'description'   =>  ['required'],
            'content'       =>  ['required'],
            'category_id'   =>  ['required', 'integer', 'min:0'],
            'status'        =>  ['required', 'boolean'],
            'image'         =>  ['image'],
        ];

        $messages = [
            'slug.unique'       =>  '建議連結已被使用',
            'content.required'  =>  '文章內容不可空白',
        ];

        $this->validate($request, $rules, $messages);
        //-----------------------------------------------------------------

        $article = Articles::find($id);

        if($request->hasFile('image')){
            $crop_array = [
                'width'     =>  $request->width,
                'height'    =>  $request->height,
                'x'         =>  $request->x,
                'y'         =>  $request->y,
            ];

            $db_filename = $this->crop_handle(
                $request->file('image'), 
                $article->id, 
                $crop_array, 
                $this->resize_width, 
                $this->resize_height, 
                $article->image
            );

            $article->image = $db_filename;
        }
        //-----------------------------------------------------------------

        if ($request->category_id==0) {
            $request->category_id = null;
        }

        $article->title = $request->title;
        $article->slug = str_slug($request->slug);
        $article->description = $request->description;
        $article->content = $request->content;
        $article->category_id = $request->category_id;
        $article->status = $request->status;

        $article->save();
        //-----------------------------------------------------------------

        $article->tag()->sync($request->tag_id);
        //-------------------------------------------------------------------文章標籤多對多關聯區

        Session::flash('success', '文章已修改成功,');
        Session::flash('link_text', '點此進行查看');
        Session::flash('link_url', route('page.single.article', $article->slug));

        return redirect(route('article.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article = Articles::find($id);

        @Storage::delete('public/'.$article->image);

        $article->tag()->detach();

        $article->delete();

        Session::flash('success', '文章已刪除成功');

        return redirect(route('article.index'));
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
        //2018/8/16 可以用storage_path或_public_path
        //->save( storage('app/public/'.$db_filename) )
        Image::make($ori_image)
            ->crop((int)$crop_array['width'], (int)$crop_array['height'], (int)$crop_array['x'], (int)$crop_array['y'])
            ->resize($resize_width, $resize_height)
            ->save(storage_path('app/public/'.$db_filename));
            //->save('../storage/app/public/'.$db_filename);

        return $db_filename;
    }

    /**
     * 取回多對多tag_id的implode結果
     * ( 作為參數傳給edit 操作select2這個多選外掛來選取已選標籤 )
     */
    public function get_tag_id_implode($id)
    {
        $article = Articles::find($id);

        $temp_array = [];

        foreach ($article->tag as $key => $tag) {
            array_push($temp_array, $tag['id']);
        }

        $article_tag = implode($temp_array, ',');

        return $article_tag;
    }
}
