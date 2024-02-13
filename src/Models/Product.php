<?php

namespace Fpaipl\Prody\Models;

use App\Models\User;
use Illuminate\Support\Str;
use Fpaipl\Brandy\Models\Po;
use Fpaipl\Prody\Models\Tax;
use Fpaipl\Prody\Models\Pomr;
use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Prody\Models\Brand;
use Fpaipl\Brandy\Models\Stock;
use Fpaipl\Brandy\Models\Ledger;
use Fpaipl\Prody\Models\Category;
use Fpaipl\Prody\Models\Overhead;
use Fpaipl\Panel\Traits\HasStatus;
use Fpaipl\Panel\Traits\ManageTag;
use Fpaipl\Panel\Traits\NamedSlug;
use Fpaipl\Prody\Models\Attribute;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Fpaipl\Brandy\Models\StockItem;
use Fpaipl\Prody\Models\Collection;
use Fpaipl\Prody\Models\Consumable;
use Illuminate\Support\Facades\Log;
use Fpaipl\Panel\Traits\ManageModel;
use Fpaipl\Prody\Models\ProductUser;
use Fpaipl\Prody\Models\ProductRange;
use Fpaipl\Prody\Models\ProductOption;
use Fpaipl\Prody\Models\ProductProcess;
use Illuminate\Database\Eloquent\Model;
use Fpaipl\Prody\Models\ProductDecision;
use Fpaipl\Prody\Models\ProductMaterial;
use Fpaipl\Prody\Models\ProductOverhead;
use Fpaipl\Prody\Models\ProductAttribute;
use Fpaipl\Prody\Models\ProductConsumable;
use Fpaipl\Prody\Models\ProductMeasurement;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Fpaipl\Prody\Models\Category as ParentModel;
use Fpaipl\Prody\Http\Livewire\ProductMeasurements;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Represents a Product model in the application.
 *
 * The Product model implements the HasMedia contract which guarantees that the model can handle media. 
 * It also uses several traits including those for handling media, logging activity, managing slugs, 
 * managing media, soft deleting records, managing model, managing tags, cascading soft deletes, and 
 * restoring soft deletes. 
 *
 * It also defines relationships that should be deleted or restored along with this model, and those 
 * that restrict soft deletes when they exist. It also provides methods to determine if there are 
 * dependent relationships and to get these relationships.
 */
class Product extends Model
{
    use
        Authx,
        LogsActivity,
        NamedSlug,
        ManageTag,
        HasStatus,
        SoftDeletes,
        ManageModel;

    const STATUS = ['draft', 'live'];
    const UPDATE_EVENT = 'update_product';

    // static created
    public static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            $product->generateDecisions();
            $product->addToRecommendedCollection();
            $product->addToRangedCollection();
        });

        static::updated(function ($product) {
            $product->addToRecommendedCollection();
            $product->addToRangedCollection();
        });

        static::deleted(function ($product) {
            $product->removeFromRecommendedCollection();
            $product->removeFromRangedCollection();
        });
    }

    public function productDecisions()
    {
        return $this->hasOne(ProductDecision::class);
    }

    public function generateDecisions()
    {
        ProductDecision::create([
            'product_id' => $this->id,
        ]);
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * The attributes that are mass assignable.
     *
     * This property lists all the attributes that can be set as mass assignable,
     * using the `fill` or `create` methods of the Eloquent model. It protects 
     * against mass-assignment vulnerabilities by ensuring that only the 
     * included fields are allowed to be set this way.
     *
     * @var array
     */
    protected $fillable = [
        'status',        // The status of the product
        'brand_id',      // The ID of the associated brand
        'category_id',   // The ID of the associated category
        'tax_id',        // The ID of the associated tax class
        'name',          // The name of the product
        'code',          // The product's unique code or SKU
        'details',       // Detailed information about the product
        'moq',           // Minimum order quantity for the product
        // Desicion making
        'ecomm', // Sell online ecomm store
        'retail', // Sell on retailpur
        'inbulk', // Sell on wholesaleGuruji
        'offline', // Sell offline dukaan
        'vendor', // Buy from vendor (as per customer order)
        'factory', // Buy from factory (as per self order)
        'market', // Buy from market (as per self decision)
        'decision_locked', // Decision locked
    ];

    /**
     * Provides the validation rules for the product model.
     *
     * This static method returns an array of validation rules for the product model. 
     * These rules are used when validating input data for creating or updating products. 
     * The rules are set for 'brand_id', 'tax_id', 'category_id', 'name', 'details', and 'moq'.
     *
     * @return array The validation rules.
     */
    public static function validationRules()
    {
        return [
            'brand_id' => ['required', 'exists:brands,id'],
            'tax_id' => ['required', 'exists:taxes,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:5000'],
            'moq' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }



    /**
     * Retrieve the category id of a product based on the category name in the row data.
     * If the category name is empty, null is returned.
     *
     * @param array $row The data of a row.
     * @return null|int The id of the category or null.
     */
    public static function getCategoryId($row)
    {
        if (empty($row['category_name'])) {
            return null;
        } else {
            return ParentModel::where('slug', Str::slug($row['category_name']))->first()->id;
        }
    }

    /**
     * Adds the product to a collection based on its ranged price.
     * Also attaches the first product option to the collection.
     *
     * @return bool Returns true if the operation is successful, false otherwise
     */
    public function addToRangedCollection()
    {
        // Retrieve the first product range
        $firstRange = $this->productRanges()->first();

        // If there's no first product range, exit the function
        if (!$firstRange) {
            return false;
        }

        // Calculate the ranged price based on the first product range
        $rangePrice = $firstRange->rate;
        $rangedPrice = ceil($rangePrice / 100) * 100 - 1;
        $rangedPrice = (int) $rangedPrice;

        // Generate a slug for the collection based on the ranged price
        $rangedCollectionSlug = 'under-' . $rangedPrice;

        // Look for an existing collection with that slug
        $collection = Collection::where('slug', $rangedCollectionSlug)->first();

        // Retrieve the ID of the first product option if it exists
        $firstProductOptionId = optional($this->productOptions()->first())->id;

        // If there's no first product option, exit the function
        if (!$firstProductOptionId) {
            return false;
        }

        // If the collection already exists
        if ($collection) {
            // Check if the product is already in the collection
            if (!$collection->products->contains($this->id)) {
                // Attach the product and its first product option to the collection
                $collection->products()->attach($this->id, ['product_option_id' => $firstProductOptionId]);
            }
            return true;
        } else {
            // Create a new collection if it doesn't exist
            $index = Collection::count();
            $collection = Collection::create([
                'name' => 'Under ' . $rangedPrice,
                'type' => 'ranged',
                'order' => $index + 1,
                'info' => 'This collection contains products with price under ' . $rangedPrice . '.',
            ]);

            // Attach the product and its first product option to the new collection
            $collection->products()->attach($this->id, ['product_option_id' => $firstProductOptionId]);

            // Assuming $this->productOptions() returns a collection of ProductOption models
            $productOption = $this->productOptions()->first();

            if ($productOption && $productOption->hasMedia(Collection::MEDIA_COLLECTION_NAME)) {
                $mediaUrl = $productOption->getFirstMediaUrl(Collection::MEDIA_COLLECTION_NAME);

                // Assuming $collection is the instance where you want to add media
                $collection->addMediaFromUrl($mediaUrl)->toMediaCollection(Collection::MEDIA_COLLECTION_NAME);
            }
            return true;
        }
    }

    /**
     * Removes the product from a collection based on its ranged price.
     *
     * @return bool Returns true if the operation is successful, false otherwise
     */
    public function removeFromRangedCollection()
    {
        $productCollection = $this->collections()->where('type', 'ranged')->get();
        foreach ($productCollection as $collection) {
            $collection->products()->detach($this->id);
        }
        return true;
    }

    public function addToRecommendedCollection()
    {
        // Look for an existing collection with that slug
        $collection = Collection::where('slug', 'recommended')->first();

        // Retrieve the ID of the first product option if it exists
        $firstProductOptionId = optional($this->productOptions()->first())->id;

        // If there's no first product option, exit the function
        if (!$firstProductOptionId) {
            return false;
        }

        // If the collection already exists
        if ($collection) {
            // Check if the product is already in the collection
            if (!$collection->products->contains($this->id)) {
                // Attach the product and its first product option to the collection
                $collection->products()->attach($this->id, ['product_option_id' => $firstProductOptionId]);
            }
            return true;
        }
    }

    public function removeFromRecommendedCollection()
    {
        // Look for an existing collection with that slug
        $collection = Collection::where('slug', 'recommended')->first();

        // If the collection already exists
        if ($collection) {
            // Check if the product is already in the collection
            if ($collection->products->contains($this->id)) {
                // Detach the product and its first product option to the collection
                $collection->products()->detach($this->id);
            }
            return true;
        }
    }

    public function getBase64Image()
    {
        // Get the first media object with the 's400' conversion
        $mediaItem = $this->productOptions?->first()?->getFirstMedia($this->productOptions?->first()->getMediaCollectionName());

        // Check if the media item exists
        if ($mediaItem) {
            // Get the image path
            $imagePath = $mediaItem->getPath('s400');

            // Check if the image file actually exists on the disk
            if (file_exists($imagePath)) {
                // Read the image file and convert it to a Base64 string
                $fileData = file_get_contents($imagePath);
                $base64Image = 'data:image/png;base64,' . base64_encode($fileData);

                return $base64Image;
            }
        }

        return '';
    }

    /*------------------- RELATIONSHIPS -----------------*/

    /**
     * Define the relationship between the Product and Category models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Define the relationship between the Product and Brand models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Define the relationship between the Product and Tax models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Define the relationship between the Product and Material models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productMaterials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class);
    }

    /**
     * Define the relationship between the Product and ProductOption models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * Define the relationship between the Product and ProductRange models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productRanges(): HasMany
    {
        return $this->hasMany(ProductRange::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    /**
     * Define the relationship between the Product and Collection models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

    public function productProcesses(): HasMany
    {
        return $this->hasMany(ProductProcess::class);
    }

    public function productOverheads(): HasMany
    {
        return $this->hasMany(ProductOverhead::class);
    }

    public function productConsumables(): HasMany
    {
        return $this->hasMany(ProductConsumable::class);
    }

    public function pomrs()
    {
        return $this->hasManyThrough(
            Pomr::class,
            ProductRange::class,
            'product_id', // Foreign key on ProductRange table...
            'product_range_id', // Foreign key on Pomr table...
            'id', // Local key on Product table...
            'id'  // Local key on ProductRange table...
        );
    }

    /**
     * Define the relationship between the Product and Cart models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // public function carts(): BelongsToMany
    // {
    //     return $this->belongsToMany('Fpaipl\Shopy\Models\Cart', 'cart_products')
    //                 ->withPivot('quantity')
    //                 ->withTimestamps();
    // }

    // public function attributes(): MorphMany
    // {
    //     return $this->morphMany(Attribute::class, 'attributable');
    // }

    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function productMeasurements(): HasMany
    {
        return $this->hasMany(ProductMeasurement::class);
    }

    // public function isInAnyCart(): bool
    // {
    //     return $this->carts()->exists();
    // }

    // public function isInUserCart()
    // {
    //     $userId = Auth::id(); // Get the currently logged-in user's ID

    //     $userCart = $this->carts()
    //                 ->whereHas('user', function ($query) use ($userId) {
    //                     $query->where('id', $userId);
    //                 })
    //                 ->first();

    //     return $userCart ? true : false;
    // }

    // public function getUserCartProductOrderType()
    // {
    //     $user = Auth::id(); // Get the currently logged-in user's ID
    //     $userCart = Cart::where('user_id', $user)->first();

    //     if (!$userCart) {
    //         return null; // Return null if the user does not have a cart
    //     }

    //     $userCartProduct = $userCart->cartProducts()
    //                                 ->where('product_id', $this->id)
    //                                 ->with('cartItems')
    //                                 ->first();

    //     if (isset($userCartProduct)) {
    //         return $userCartProduct->order_type;
    //     } else {
    //         return null;
    //     }
    // }

    // public function getUserCartProducts()
    // {
    //     $user = Auth::id(); // Get the currently logged-in user's ID
    //     $userCart = Cart::where('user_id', $user)->first();

    //     if (!$userCart) {
    //         return null; // Return null if the user does not have a cart
    //     }

    //     $userCartProduct = $userCart->cartProducts()
    //                                 ->where('product_id', $this->id)
    //                                 ->with('cartItems')
    //                                 ->first();

    //     if (isset($userCartProduct)) {
    //         return CartItemResource::collection($userCartProduct->cartItems);
    //     } else {
    //         return null;
    //     }


    //     // $userId = Auth::id(); // Get the currently logged-in user's ID

    //     // $userCart = $this->carts()
    //     //             ->whereHas('user', function ($query) use ($userId) {
    //     //                 $query->where('id', $userId);
    //     //             })
    //     //             ->with('cartProducts.cartItems')
    //     //             ->first();

    //     // return $userCart?->cartProducts;
    // }

    public function getImage($conversion = ProductOption::MEDIA_CONVERSION_PREVIEW)
    {
        // if productOptions is empty, return placeholder image
        if ($this->productOptions()->count() == 0) {
            return config('app.url') . '/storage/assets/placeholders/product_100.webp';
        }
        return $this->productOptions()?->first()?->getImage($conversion);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    // Category

    public function getParentSlug($key)
    {
        $parentSlug = '';
        if (!empty($this->$key) && $this->categoryWithTrashed) {
            $parentSlug = $this->categoryWithTrashed->slug;
        }
        return $parentSlug;
    }

    public function getParentName($key)
    {
        $parentName = '';
        if (!empty($this->$key) && $this->categoryWithTrashed) {
            $parentName = $this->categoryWithTrashed->slug;
        }
        return $parentName;
    }

    public function hasCategory()
    {
        return $this->category()->count();
    }

    public function categoryWithTrashed()
    {
        return $this->category()->withTrashed();
    }

    public function scopeUncollectioned($query)
    {
        return $query->whereDoesntHave('collections');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, ProductUser::class);
    }

    public function pos()
    {
        return $this->hasMany(Po::class);
    }

    // Helper Functions

    // public function getTimestamp($value) {
    //     return getTimestamp($this->$value);
    // }

    // public function getValue($key){

    //     return $this->$key;
    // }

    public function getTableData($key)
    {
        switch ($key) {
            case 'brand':
                return $this->brand->name;
            case 'category_id': 
                $parentName = $this->category->getParentFullName($this->category);
                return ($parentName ? $parentName . ' ' : '') . $this->category->name;
            case 'tax_id':
                $taxName = '';
                if (!empty($this->tax_id) && $this->tax) {
                    $taxName = $this->tax->name;
                }
                return $taxName;
            default:
                return $this->{$key};
        }
    }

    // public function inCart(){
    //     $colorSizes = [];
    //     $cartProducts = [];
    //     $count=0;
    //     foreach($this->colors as $color){
    //         foreach($color->colorSizes as $colorSize){
    //             array_push($colorSizes, $colorSize->id);
    //         }
    //     }
    //     foreach(CartProduct::all() as $cartProduct){
    //         if(in_array($cartProduct->color_size_id, $colorSizes)){
    //             array_push($cartProducts, $cartProduct->id);
    //             $count++;
    //         }
    //     }

    //     return [
    //         'modelName' => 'CartProduct',
    //         'cartProducts' => $cartProducts,
    //         'total' => $count,
    //     ];
    // }

    public function getStockItem($productOptionId, $productRangeId)
    {
        return $this->stock()->stockItems()->where('product_option_id', $productOptionId)->where('product_range_id', $productRangeId)->first();
    }

    /**
     * Generate stock and stock items for live products.
     *
     * Iterates over all products marked as 'live' and generates stock and stock items
     * for each combination of product options and ranges. Skips creating stocks or stock items
     * if they already exist.
     *
     * @return void
     */
    public function generateStocks()
    {
        DB::beginTransaction();

        try {
            // if (!$this->decision_locked) {
            //     throw new \Exception('Product decision is not locked.');
            // }

            $searchtags = [];
            $productOptions = $this->productOptions;
            $productRanges = $this->productRanges;

            if ($productOptions->isEmpty() || $productRanges->isEmpty()) {
                throw new \Exception('Product has no product options or ranges.');
            }

            $newStock = Stock::firstOrCreate(
                [
                    // Use attributes to find an existing record
                    'sid' => $this->code,
                    'sku' => $this->slug,
                ],
                [
                    // Attributes for the new record if it doesn't exist
                    'name' => $this->name,
                    'product_id' => $this->id,
                    'product_sid' => $this->sid,
                    'product_name' => $this->name,
                    'product_code' => $this->code,
                    'mrp' => 100,
                    'price' => 100,
                    'moq' => $this->moq,
                ]
            );

            // throw new \Exception that unable to create stock
            if (!$newStock) {
                throw new \Exception('Unable to create stock.');
            }

            foreach ($productOptions as $option) {
                foreach ($productRanges as $range) {

                    $sidId = $this->code . "-" . $option->id . "-" . $range->id;
                    $name = $this->name . " " . $option->name . " " . $range->name;
                    $skuId = $this->slug . "_" . $option->slug . "_" . $range->slug;

                    $newStockItem = StockItem::firstOrCreate(
                        [
                            'stock_id' => $newStock->id,
                            'name' => $name,
                            'sid' => $sidId,
                            'sku' => $skuId,
                        ],
                        [
                            'product_id' => $this->id,
                            'product_name' => $this->name,
                            'product_sid' => $this->sid,
                            'product_code' => $this->code,
                            'mrp' => $range->mrp,
                            'price' => $range->rate,
                            'moq' => $this->moq,
                            'product_option_id' => $option->id,
                            'product_option_sid' => $option->slug,
                            'product_option_name' => $option->name,
                            'product_range_id' => $range->id,
                            'product_range_sid' => $range->slug,
                            'product_range_name' => $range->name,
                        ]
                    );

                    // throw new \Exception that unable to create stock item
                    if (!$newStockItem) {
                        throw new \Exception('Unable to create stock item.');
                    }

                    $tags = [];
                    $tags[] = $newStockItem->sku;
                    $tags[] = $newStockItem->sid;
                    $tags[] = $newStockItem->name;
                    $tags[] = $newStockItem->product_sid;
                    $tags[] = $newStockItem->product_name;
                    $tags[] = $newStockItem->product_code;
                    $tags[] = $newStockItem->product_option_sid;
                    $tags[] = $newStockItem->product_option_name;
                    $tags[] = $newStockItem->product_range_sid;
                    $tags[] = $newStockItem->product_range_name;
                    $searchtags[] = $newStockItem->product_option_name;
                    $searchtags[] = $newStockItem->product_range_name;
                    $tags = array_unique($tags);
                    $newStockItem->tags = implode(', ', $tags);
                    $newStockItem->save();
                }
            }

            $newStock->tags = implode(', ', array_unique($searchtags));
            $newStock->save();

            DB::commit();
            return ['status' => 'success', 'message' => 'Stocks generated successfully.'];
        } catch (\Exception $e) {
            // Roll back the transaction
            DB::rollBack();
    
            // Log the error
            Log::error('Error generating stocks: ' . $e->getMessage());
    
            // Return the specific error message
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteStocks()
    {
        DB::beginTransaction();

        try {
            // Fetch the stock related to the product
            $stock = Stock::where('product_id', $this->id)->first();

            if (!$stock) {
                throw new \Exception('Stock not found.');
            }

            // Check if the stock can be safely deleted

            // it quantity is not zero or it has any ledger
            if ($stock->quantity != 0) {
                throw new \Exception('Stock cannot be deleted if it has balance quantity.');
            }

            if ($stock->ledgers()->count() > 0) {
                throw new \Exception('Stock cannot be deleted if it has ledger');
            }

            if ($stock->someCondition) {
                throw new \Exception('Stock cannot be deleted under certain conditions.');
            }

            // Delete related stock items
            $stockItems = StockItem::where('stock_id', $stock->id)->get();
            foreach ($stockItems as $stockItem) {
                $stockItem->forceDelete();
            }

            // Delete the stock
            $stock->forceDelete(); 

            DB::commit();
            return ['status' => 'success', 'message' => 'Stock and related stock items deleted successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting stocks: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function deleteProduct()
    {
        DB::beginTransaction();

        try {
            // Fetch the stock related to the product
            $stock = Stock::where('product_id', $this->id)->first();

            if ($this->status == 'live') {
                throw new \Exception('Product cannot be deleted if it is live.');
            }

            if ($stock) {
                throw new \Exception('Product cannot be deleted if it has stock.');
            }

            if ($this->productOptions()->count() > 0) {
                throw new \Exception('Product cannot be deleted if it has product options.');
            }

            if ($this->productRanges()->count() > 0) {
                throw new \Exception('Product cannot be deleted if it has product ranges.');
            }

            if ($this->productMaterials()->count() > 0) {
                throw new \Exception('Product cannot be deleted if it has product materials.');
            }

            if ($this->productProcesses()->count() > 0) {
                throw new \Exception('Product cannot be deleted if it has product processes.');
            }

            if ($this->pos()->count() > 0) {
                throw new \Exception('Product cannot be deleted if it has POs.');
            }

            // Delete related product processes
            $productProcesses = ProductProcess::where('product_id', $this->id)->get();
            foreach ($productProcesses as $productProcess) {
                $productProcess->forceDelete();
            }

            // Delete related product overheads
            $productOverheads = ProductOverhead::where('product_id', $this->id)->get();
            foreach ($productOverheads as $productOverhead) {
                $productOverhead->forceDelete();
            }

            // Delete related product users
            $productUsers = ProductUser::where('product_id', $this->id)->get();
            foreach ($productUsers as $productUser) {
                $productUser->forceDelete();
            }

            // Delete related product materials
            $productMaterials = ProductMaterial::where('product_id', $this->id)->get();
            foreach ($productMaterials as $productMaterial) {
                $productMaterial->forceDelete();
            }

            // Delete related product options
            $productOptions = ProductOption::where('product_id', $this->id)->get();
            foreach ($productOptions as $productOption) {
                $productOption->forceDelete();
            }

            // Delete related product ranges
            $productRanges = ProductRange::where('product_id', $this->id)->get();
            foreach ($productRanges as $productRange) {
                $productRange->forceDelete();
            }

            // Delete the product
            $this->forceDelete();

            DB::commit();
            return ['status' => 'success', 'message' => 'Product, stock and related stock items deleted successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('model_log');
    }

    public function overheads()
    {
        return $this->belongsToMany(Overhead::class, 'product_overhead')->withPivot('cost', 'ratio', 'reasons');
    }

    // Products have many consumables
    public function consumables()
    {
        return $this->belongsToMany(Consumable::class, 'product_consumable')->withPivot('quantity', 'cost', 'reasons');
    }
}
