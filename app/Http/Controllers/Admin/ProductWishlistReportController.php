<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Brian2694\Toastr\Facades\Toastr;

class ProductWishlistReportController extends Controller
{
    /**
     * Product wishlist report list show, search & filtering
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $search = $request['search'];
        $seller_id = $request['seller_id'];
        $sort = $request['sort'] ?? 'ASC';

        $products = Product::with(['wish_list'])
                            ->when(empty($request['seller_id']) || $request['seller_id'] == 'all',function ($query){
                                $query;
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
                            ->when($sort, function($query) use($sort){
                                $query->withCount('wish_list')
                                ->orderBy('wish_list_count', $sort);
                            })->paginate(Helpers::pagination_limit())->appends(['search'=>$request['search'],'seller_id'=>$request['seller_id'],'sort'=>$request['sort']]);

        return view('admin-views.report.product-in-wishlist', compact('products','search', 'seller_id', 'sort'));
    }

    /**
     * Product wishlist report export by excel
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function export(Request $request){
        $sort = $request['sort'] ?? 'ASC';

        $products = Product::with(['wish_list'])
                    ->when(empty($request['seller_id']) || $request['seller_id'] == 'all',function ($query){
                        $query;
                    })
                    ->when($request['seller_id'] == 'in_house',function ($query){
                        $query->where(['added_by' => 'admin']);
                    })
                    ->when($request['seller_id'] != 'in_house' && isset($request['seller_id']) && $request['seller_id'] != 'all',function ($query) use($request){
                        $query->where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
                    })
                    ->when($request['search'], function($q) use($request){
                        $q->where('name','Like','%'.$request['search'].'%');
                    })
                    ->when($sort, function($query) use($sort){
                        $query->withCount('wish_list')
                            ->orderBy('wish_list_count', $sort);
                    })->get();

        if ($products->count()==0) {
            Toastr::warning(\App\CPU\translate('Data is Not available!!!'));
            return back();
        }
        $data = array();
        foreach($products as $product){
            $data[] = array(
                'Product Name'      => $product->name,
                'Date'              => date('d M Y',strtotime($product->created_at)),
                'Total in Wishlist' => $product->wish_list_count,
            );
        }

        return (new FastExcel($data))->download('product_in_wishlist.xlsx');
    }
}
