<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Order;
use Session;
use Auth;
use Mail;
use View;
use App\Mail\Payment_Confirm;
use PDF;
use Excel;

class CartController extends Controller
{
    public $status_array = [
        0   =>  '待付款',
        1   =>  '已付款',
    ];

    public function __construct()
    {
        $this->middleware('auth', ['only' => ['showList', 'send_to_ecpay']]);
        View::share('status_array', $this->status_array);
    }

    /**
     * 購物車已放入商品一覽
     */
	public function index(Request $request)
	{
		if ( !$request->session()->has('cart') ){
			Session::flash('fail', '購物車為空');

			return redirect()->route('page.product.list');
		}

		$cart = Session::get('cart');
		$product_list = $cart['product_list'];
		$total_price = $cart['total_price'];

		return view('cart.index', compact('product_list', 'total_price'));
	}

    /**
     * 供已登入的用戶查看自己目前所擁有的訂單列表
     */
    public function showList(Request $request)
    {

        $order_list = (new Order)->get_order_list(Auth::id(), $request->order_status, 10);

        return view('cart.list', compact('order_list'));
    }

    /**
     * 供已登入的用戶查看選定的訂單明細
     * 若非訂單所有者則會退回上一頁
     */
    public function showDetail(Request $request, $id)
    {
        $order = Order::find($id);

        if ( $order->user_id != Auth::id() ) {
            return redirect()->back();
        }

        return view('cart.detail', compact('order'));
    }

    /**
     * 產出PDF訂單供下載
     * 若非訂單所有者則會退回上一頁
     */
    public function createPDF(Request $request, $id)
    {
        $order = Order::find($id);

        if ( $order->user_id != Auth::id() ) {
            return redirect()->back();
        }

        //$mpdf = new \Mpdf\Mpdf();
        $mpdf = new PDF();
        $mpdf->autoLangToFont = true; 
        $mpdf->WriteHTML(view('pdf.cart_detail', compact('order'))->render());
        $mpdf->Output($order->order_no, 'I');
    }

    /**
     * 產出訂單列表的Excel檔供下載
     * 若非訂單所有者則會退回上一頁
     */
    public function exportExcel(Request $request)
    {
        $rules = [
            'user_id'       =>  ['required', 'exists:users,id'],
            'order_status'  =>  ['integer'],
        ];

        $this->validate($request, $rules);
        //-----------------------------------------------------------------驗證區

        $order_list = new Order;

        if (isset($request->order_status) && $request->order_status!=99) {
            $order_list = $order_list->where('status', $request->order_status);
        }

        $order_list = $order_list->where('user_id', $request->user_id)->get();

        Excel::create('會員_'.Auth::user()->name.'_訂單一覽', function ($excel) use ($order_list) {
            $excel->sheet('hello', function ($sheet) use ($order_list) {
                $row_count = 1;
                $sheet->appendRow([
                    '#', 
                    '訂單編號',
                    '訂單價格',
                    '訂單狀態',
                    '訂單建立時間',
                ]);
                foreach ($order_list as $order) {
                    $sheet->appendRow([
                        $row_count,
                        $order->order_no,
                        $order->price,
                        $this->status_array[$order->status],
                        $order->created_at,
                    ]);
                    $row_count++;
                }
            });
        })->download('xls');
    }

    /**
     * 清空購物車內的商品
     */
	public function destroy_all(Request $request)
	{
		$request->session()->forget('cart');

		return redirect()->route('page.product.list');
	}

    /**
     * 購物車內指定商品數量+1
     */
    public function add_to_cart(Request $request, $id)
    {
    	if( $request->session()->has('cart') ){
    		$cart = $request->session()->get('cart');
    		$product_list = $cart['product_list'];
    		$total_price = $cart['total_price'];

    		if ( array_key_exists($id, $product_list) ){
    			$product_list[$id]['quantity']++;
    			$total_price += $product_list[$id]['collection']->price;
    		} else {
    			$product = Product::find($id);

    			$product_array = [
	    			'quantity'		=>	1,
	    			'collection'	=>	$product
	    		];

	    		$product_list[$id] = $product_array;
	    		$total_price += $product_list[$id]['collection']->price;
    		}

            $cart['product_list'] = $product_list;
            $cart['total_price'] = $total_price;
            $cart['total_quantity']++;

            $request->session()->put('cart', $cart);

    	} else {
    		$product = Product::find($id);

    		$product_array = [
    			'quantity'		=>	1,
    			'collection'	=>	$product
    		];

    		$product_list[$id] = $product_array;

    		$total_price = 0;

    		foreach ($product_list as $product) {
    			$total_price += $product['collection']->price*$product['quantity'];
    		}

    		$cart = [
    			'product_list'      =>	$product_list,
    			'total_price'       =>	$total_price,
                'total_quantity'    =>  1
    		];
    	}

        $request->session()->put('cart', $cart);

    	Session::flash('success', '商品 : "'.$product_list[$id]['collection']->name.'" 已加入購物車<br>目前總價 : '.$total_price);

    	return redirect()->back();
    }

    /**
     * 購物車內指定商品數量減一
     * 減到零時則將該商品由購物車中移除
     */
    public function remove_from_cart(Request $request, $id)
    {
        if ( $request->session()->has('cart') ){

            $cart = $request->session()->get('cart');
            $product_list = $cart['product_list'];
            $total_price = $cart['total_price'];

            if ( array_key_exists($id, $product_list) ){

                $product_name = $product_list[$id]['collection']->name;

                $product_list[$id]['quantity']--;
                $total_price -= $product_list[$id]['collection']->price;

                if ( $product_list[$id]['quantity']==0 ) {
                    unset($product_list[$id]);
                }

            } else {

                Session::flash('fail', '錯誤!!<br>購物車內並未包含"'.$product_list[$id]['collection']->name.'"商品!!');
                return redirect()->back();

            }

        } else {
            
            Session::flash('fail', '錯誤!!<br>購物車為空!!');
            return redirect()->back();

        }

        if (sizeof($product_list)!=0){
            $cart['product_list'] = $product_list;
            $cart['total_price'] = $total_price;
            $cart['total_quantity']--;
            $request->session()->put('cart', $cart);
            Session::flash('success', '已從購物車中移除一項商品 : "'.$product_name.'"<br>目前總價 : '.$total_price);
        } else {
            $request->session()->forget('cart');
            Session::save();
            Session::flash('success', '已從購物車中移除一項商品 : "'.$product_name.'"<br>目前購物車為空');
        }

        return redirect()->back();
    }

    /**
     * 將購物車內容寫入訂單資料庫
     * 然後以表單發送至綠界 進入綠界網站的金流選擇頁面
     */
    public function send_to_ecpay(Request $request)
    {
        $cart = $request->session()->get('cart');

        $order = new Order;
        $order->order_no = 'yoga'.date('YmdHis');
        $order->user_id = Auth::id();
        $order->list = json_encode($cart['product_list']);
        $order->price = $cart['total_price'];
        $order->status = 0;
        $order->save();
        //--------------------------------------------------------------------

        $detail_array = [];
        foreach ($cart['product_list'] as $id => $product) {
            array_push($detail_array, $product['collection']->name.' -- '.$product['quantity'].'本');
        }
        $detail = implode('#', $detail_array);

        $url = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

        $params = [
            'ChoosePayment'         =>  'ALL',
            'ClientBackURL'         =>  route('page.product.list'),
            'EncryptType'           =>  1,
            'ItemName'              =>  $detail,
            'MerchantID'            =>  env('ECPAY_MERCHANT_ID'),
            'MerchantTradeDate'     =>  date('Y/m/d H:i:s'),
            'MerchantTradeNo'       =>  date('YmdHis'),
            'PaymentType'           =>  'aio',
            'ReturnURL'             =>  route('cart.receive', $order->order_no),
            'TotalAmount'           =>  $cart['total_price'],
            'TradeDesc'             =>  '交易描述訊息',
        ];
        //--------------------------------------------------------------------

        $CheckMacValue = $this->generate_check_value($params, env('ECPAY_HASH_KEY'), env('ECPAY_HASH_IV'));

        $params['CheckMacValue'] = $CheckMacValue;

        $request->session()->forget('cart');
        Session::save();
        //--------------------------------------------------------------------

        $this->ecpay_checkout("_self", $params, $url, $CheckMacValue);
    }

    /**
     * 接收綠界回傳的資料改變訂單狀態
     * 成功的狀況下以佇列寄信給用戶
     */
    public function ecpay_receive(Request $request, $order_no)
    {
        $CheckMacValue = $this->generate_check_value($request->all(), env('ECPAY_HASH_KEY'), env('ECPAY_HASH_IV'));

        $order = Order::where('order_no', $order_no)->first();

        if($request->CheckMacValue!=$CheckMacValue){
            $order->json = $request->CheckMacValue.'!='.$CheckMacValue;
            $order->save();
        } else {
            if($request->RtnCode==1){
                $order->status = 1;
                $order->save();
                //Mail::queue 需要 php artisan queue:listen
                Mail::to($order->user['email'])->queue(new Payment_Confirm($order));
            } else {
                $order->json = json_encode($request->all());
                $order->save();
            }
        }
    }

    /**
     * 將訂單參數傳入 製成表單並直接執行 將用戶導到綠界網站
     */
    public function ecpay_checkout($target = "_self",$arParameters = array(),$ServiceURL='',$CheckMacValue){
        //產生檢查碼
        $szCheckMacValue = $CheckMacValue;
       
        //生成表單，自動送出
        $szHtml =  '<!DOCTYPE html>';
        $szHtml .= '<html>';
        $szHtml .=     '<head>';
        $szHtml .=         '<meta charset="utf-8">';
        $szHtml .=     '</head>';
        $szHtml .=     '<body>';
        $szHtml .=         "<form id=\"__ecpayForm\" method=\"post\" target=\"{$target}\" action=\"{$ServiceURL}\">";
        
        foreach ($arParameters as $keys => $value) {
            $szHtml .=         "<input type=\"hidden\" name=\"{$keys}\" value=\"{$value}\" />";
        }
        $szHtml .=             "<input type=\"hidden\" name=\"CheckMacValue\" value=\"{$szCheckMacValue}\" />";
        $szHtml .=         '</form>';
        $szHtml .=         '<script type="text/javascript">document.getElementById("__ecpayForm").submit();</script>';
        $szHtml .=     '</body>';
        $szHtml .= '</html>';
        echo $szHtml ;
        exit;
    }

    /**
     * 綠界的檢查碼製作函式
     */
    public function generate_check_value($params, $HashKey, $HashIV)
    {
        $CheckMacValue = '';

        unset($params['CheckMacValue']);
        ksort($params);

        foreach ($params as $key => $value) {
            $CheckMacValue .= $key.'='.$value.'&';
        }

        $CheckMacValue = 'HashKey='.$HashKey.'&'.$CheckMacValue;
        $CheckMacValue .= 'HashIV='.$HashIV;
        $CheckMacValue = urlencode($CheckMacValue);
        $CheckMacValue = strtolower($CheckMacValue);
        $CheckMacValue = str_replace('%2d', '-', $CheckMacValue);
        $CheckMacValue = str_replace('%5f', '_', $CheckMacValue);
        $CheckMacValue = str_replace('%2e', '.', $CheckMacValue);
        $CheckMacValue = str_replace('%21', '!', $CheckMacValue);
        $CheckMacValue = str_replace('%2a', '*', $CheckMacValue);
        $CheckMacValue = str_replace('%28', '(', $CheckMacValue);
        $CheckMacValue = str_replace('%29', ')', $CheckMacValue);
        $CheckMacValue = hash('sha256', $CheckMacValue);
        $CheckMacValue = strtoupper($CheckMacValue);

        return $CheckMacValue;
    }

    public function fake()
    {
        $target = '_self';
        $ServiceURL = route('cart.receive', '20180809213222');
        $szCheckMacValue = 123;
       
        //生成表單，自動送出
        $szHtml =  '<!DOCTYPE html>';
        $szHtml .= '<html>';
        $szHtml .=     '<head>';
        $szHtml .=         '<meta charset="utf-8">';
        $szHtml .=     '</head>';
        $szHtml .=     '<body>';
        $szHtml .=         "<form id=\"__ecpayForm\" method=\"post\" target=\"{$target}\" action=\"{$ServiceURL}\">";
        $szHtml .=             "<input type=\"hidden\" name=\"CheckMacValue\" value=\"{$szCheckMacValue}\" />";
        $szHtml .=         '</form>';
        $szHtml .=         '<script type="text/javascript">document.getElementById("__ecpayForm").submit();</script>';
        $szHtml .=     '</body>';
        $szHtml .= '</html>';
        echo $szHtml ;
        exit;
    }
}
