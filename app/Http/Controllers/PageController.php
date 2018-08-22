<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articles;
use App\Product;
use Session;
use Image;

class PageController extends Controller
{
    public function show_article_list(Request $request)
    {
        $article_list = (new Articles)->get_article_for_page($request->tag_id);

    	return view('page.article_list', compact('article_list'));
    }

    public function show_single_article($slug)
    {
        $article = Articles::where('slug', $slug)->first();

        if(!$article){
            Session::flash('fail', 'No slug result');

            return redirect(route('page.article.list'));
        }

        return view('page.single_article', compact('article'));
    }

    public function show_product_list(Request $request)
    {
        $product_list = Product::paginate(9);

        return view('page.product_list', compact('product_list'));
    }

    public function pdf(Request $request)
    {
        if ( !$request->session()->has('cart') ){
            Session::flash('fail', '購物車為空');

            return redirect()->route('page.product.list');
        }

        $cart = Session::get('cart');
        $product_list = $cart['product_list'];
        $total_price = $cart['total_price'];

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->autoLangToFont = true; 
        $mpdf->WriteHTML(view('order_pdf', compact('product_list', 'total_price'))->render());
        $mpdf->Output('yourFileName.pdf', 'I');
        //return view('order_pdf', compact('product_list', 'total_price'));
    }
}
