<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductStockReportController extends Controller
{
    /**
     * Product stock report list show, search & filtering
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $search = $request['search'];
        $seller_id = $request['seller_id'];
        $sort = $request['sort'] ?? 'ASC';

        $query_param = ['search' => $search, 'sort' => $sort, 'seller_id' => $seller_id];

        $products = Product::when(empty($request['seller_id']) || $request['seller_id'] == 'all',function ($query){
                                $query->whereIn('added_by', ['admin', 'seller']);
                            })
                            ->when($request['seller_id'] == 'in_house',function ($query){
                                $query->where(['added_by' => 'admin']);
                            })
                            ->when($request['seller_id'] != 'in_house' && isset($request['seller_id']) && $request['seller_id'] != 'all',function ($query) use($request){
                                $query->where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
                            })
                            ->when($search, function($q) use($search){
                                $q->where('name','Like','%'.$search.'%');
                            })
                            ->orderBy('current_stock', $sort)
                            ->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.report.product-stock', compact('products','seller_id', 'search', 'sort'));
    }

    /**
     * Product total stock report export by excel
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export(Request $request){

        $sort = $request['sort'] ?? 'ASC';

        $products = Product::when(empty($request['seller_id']) || $request['seller_id'] == 'all',function ($query){
                                $query->whereIn('added_by', ['admin', 'seller']);
                            })
                            ->when($request['seller_id'] == 'in_house',function ($query){
                                $query->where(['added_by' => 'admin']);
                            })
                            ->when($request['seller_id'] != 'in_house' && isset($request['seller_id']) && $request['seller_id'] != 'all',function ($query) use($request){
                                $query->where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
                            })
                            ->orderBy('current_stock', $sort)->get();

        $data = array();
        foreach($products as $product){
            $data[] = array(
                'Product Name'   => $product->name,
                'Date'           => date('d M Y',strtotime($product->created_at)),
                'Total Stock'    => $product->current_stock,
            );
        }

        return (new FastExcel($data))->download('total_product_stock.xlsx');
    }

}
