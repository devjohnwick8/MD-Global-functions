

<!-- for sorting a page data  -->


<form action="{{route('popular_products')}}" method="get">
    <div class="sort-resultSearch">
        <p>Sorted Result By</p>
        <select name="filter" onchange="this.form.submit()" required="">
            <option value="latest" {{'created_at' == $orderByColumn && 'desc' == $orderable  ? 'selected' : '' }} >
           
                Latest</option>
            <option value="asc"  {{'asc' == $orderable && 'selling_price' == $orderByColumn  ? 'selected' : '' }}
          
             >Low Price</option>
            <option value="desc"  {{'desc' == $orderable && 'selling_price' == $orderByColumn  ? 'selected' : '' }}
     
            >High Price</option>	
        </select>			
    </div>
</form>





<?php 
public function popular_products( Request  $request){


        $query = Product::query();
        $query->where('featured', '=', 1)
              ->where('status', 1);

        if ($request->has('filter')) {
            if ($request->filter == 'asc') {
                $orderByColumn = 'selling_price';
                $orderable = 'asc';
            } elseif ($request->filter == 'desc') {
                $orderByColumn = 'selling_price';
                $orderable = 'desc';
            } else {
                $orderByColumn = 'created_at';
                $orderable = 'desc';
            }
            $query->orderBy($orderByColumn, $orderable);
        } else {
            $orderByColumn = 'created_at';
            $orderable = 'desc';
            $query->orderBy($orderByColumn, $orderable);
        }

        $products = $query->paginate(12)->appends(request()->query());


        $countProducts =  Product::where('featured' ,'=', 1)
                            ->where('status', 1)->count();

        return view('frontend.frontend_layout.product_page.popular_products',compact('products','orderable' , 'countProducts' , 'orderByColumn'));


    }
?>