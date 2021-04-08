<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Page;
class SitemapController extends Controller
{
	public function index() {
		return response()->view('sitemap.index')->header('Content-Type', 'text/xml');
	}

	public function products() {
		$products = Product::orderBy("id", "desc")->take(1000)->select(["slug", "feature_image", "updated_at"])->get();
		return response()->view('sitemap.products', [
		'products' => $products,
		])->header('Content-Type', 'text/xml');
	}

	public function categories() {
		$categories = Category::with('get_subcategory')->orderBy("id", "desc")->get();
		$pages = Page::orderBy("id", "desc")->select(['updated_at','slug'])->get();

		return response()->view('sitemap.category', [
		'categories' => $categories,
		'pages' => $pages,
		])->header('Content-Type', 'text/xml');
	}


    public function catSitemap(){
        return view('frontend.pages.category-sitemap');
    }
}
