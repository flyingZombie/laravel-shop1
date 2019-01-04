<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
    	//$products = Product::query()->where('on_sale', true)->paginate(16);
        /*
    	$builder = Product::query()->where('on_sale', true);

    	if($search = $request->input('search', '')) {
    		$like = '%'.$search.'%';

    		$builder->where(function ($query) use ($like) {
    			$query->where('title', 'like', $like) 
    			  ->orWhere('description', 'like', $like)
    			  ->orWhereHas('skus', function ($query) use ($like) {
    			  	$query->where('title', 'like', $like) 
    			  		->orWhere('description', 'like', $like);
    			  });
    		});
    	}

    	if ($request->input('category_id') && $category = Category::find($request->input('category_id'))) {
    	    if($category->is_directory) {
              $builder->whereHas('category', function ($query) use ($category) {
                  $query->where('path', 'like', $category->path.$category->id.'-%');
              });
            } else {
    	        $builder->where('category_id', $category->id);
            }
        }

    	if ($order = $request->input('order', '')) {
    		if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
    			if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
    				$builder->orderBy($m[1], $m[2]);
    			}
    		}
    	}

    	$products = $builder->paginate(16);
        */

        $page = $request->input('page', 1);

        $perPage = 16;

        $params = [
          'index' => 'products',
            'type' => '_doc',
            'body' => [
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
                'query' => [
                    'bool' => [
                        'filter' => [
                            ['term' => ['on_sale' => true]],
                        ]
                    ]
                ]
            ],
        ];

        if ($order = $request->input('order', '')) {

            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    $params['body']['sort'] = [[$m[1] => $m[2]]];
                }
            }
        }

        if($request->input('category_id') && $category = Category::find($request->input('category_id'))) {

            if ($category->is_directory) {
                $params['body']['query']['bool']['filter'][] = [
                    'prefix' => ['category_path' => $category->path.$category->id.'-'],
                ];
            } else {
                $params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
            }
        }

        if ($search = $request->input('search', '')) {

            $keywords = array_filter(explode(' ', $search));

            $params['body']['query']['bool']['must'] = [];

            foreach ($keywords as $keyword) {
                $params['body']['query']['bool']['must'][] = [

                        'multi_match' => [
                            'query' => $keyword,
                            'fields' => [
                                'title^3',
                                'long_title^2',
                                'category^2',
                                'description',
                                'skus_title',
                                'skus_description',
                                'properties_value',
                            ]
                        ],
                ];
            }
        }

        if ($search || isset($category)) {
            $params['body']['aggs'] = [
                'properties' => [
                    'nested' => [
                        'path' => 'properties',
                    ],
                    'aggs' => [
                      'properties' => [
                          'terms' => [
                              'field' => 'properties.name',
                          ],
                          'aggs' => [
                              'value' => [
                                  'terms' => [
                                      'field' => 'properties.value',
                                  ],
                              ],
                          ],
                      ],
                    ],
                ],
            ];
        }

        $propertyFilters = [];

        if ($filterString = $request->input('filters')) {

          $filterArray = explode('|', $filterString);

          foreach ($filterArray as $filter) {

            list($name, $value) = explode(':', $filter);

            $propertyFilters[$name] = $value;

            $params['body']['query']['bool']['filter'][] = [
              'nested' => [
                  'path' => 'properties',
                  'query' => [
                      //['term' => ['properties.name' => $name ]],
                      //['term' => ['properties.value' => $value]],
                      ['term' => ['properties.search_value' => $filter]]
                  ],
              ],
            ];
          }
        }

        $result = app('es')->search($params);

        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->orderByRaw(sprintf("FIND_IN_SET(id, '%s')", join(',', $productIds)))
            ->get();

        $pager = new LengthAwarePaginator($products, $result['hits']['total'], $perPage, $page, [
            'path' => route('products.index', false),
    ]);

        $properties = [];

        if (isset($result['aggregations'])) {
            $properties = collect($result['aggregations']['properties']['properties']['buckets'])
                ->map(function ($bucket) {
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['value']['buckets'])->pluck('key')->all(),
                    ];
                })->filter(function ($property) use ($propertyFilters) {

                    return count($property['values']) > 1 && !isset($propertyFilters[$property['key']]);

                });
        }

    	return view('products.index', [
    		'products' => $pager, //$products,
    		'filters' => [
    			'search' => '',//$search,
    			'order' => $order,
    		],
            'category' => $category ?? null,
            'properties' => $properties,
            'propertyFilters' => $propertyFilters,
    		]);
    }

    public function show(Product $product, Request $request)
    {
    	if (!$product->on_sale) {
    		throw new InvalidRequestException('This product is not for sale');
    	}

        $favored = false;

        if ($user = $request->user()) {
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
            ->with(['order.user','productSku'])
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->limit(10)
            ->get();

    	return view('products.show', ['product' => $product, 'favored' => $favored, 'reviews' => $reviews ]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();

        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request) 
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);
        return [];
    }
}
