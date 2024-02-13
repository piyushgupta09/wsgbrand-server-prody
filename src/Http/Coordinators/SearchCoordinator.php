<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;

class SearchCoordinator extends Coordinator
{
    public function topSearchTags()
    {
        $products = Product::select('tags')->get();
        $tags = [];
        
        // Add words you want to ignore here
        $ignoreWords = [
            "1", "2", "3", "4", "5", "6", "7", "8", "9", "0",
            "1", "live", "t-shirt", "shirt", "print", "&", "and", "",
            "the", "of", "to", "a", "in", "is", "you", "that", "it",
            "he", "was", "for", "on", "are", "as", "with", "his", "they",
            "I", "at", "be", "this", "have", "from", "or", "one", "had",
            "by", "word", "but", "not", "what", "all", "were", "we", "when",
            "your", "can", "said", "there", "use", "an", "each", "which", "she",
            "do", "how", "their", "if", "will", "up", "other", "about", "out",
            "many", "then", "them", "these", "so", "some", "her", "would", "make",
            "like", "him", "into", "time", "has", "look", "two", "more", "write",
            "go", "see", "number", "no", "way", "could", "people", "my", "than",
            "first", "water", "been", "call", "who", "oil", "its", "now", "find",
            "long", "down", "day", "did", "get", "come", "made", "may", "part",
            "front", "back", "side", "take", "also", "new", "work", "because", "any",
        ]; 
    
        foreach ($products as $product) {
            $stringTags = explode(',', $product->tags);
            foreach ($stringTags as $stringTag) {
                
                $stringTag = strtolower(trim($stringTag));
    
                // Ignore if tag is in the ignoreWords list
                if (in_array($stringTag, $ignoreWords)) {
                    continue;
                }
    
                // Ignore if tag matches the pattern
                if (preg_match('/^#\d{5}$/', $stringTag)) {
                    continue;
                }
    
                $tagWords = explode(' ', $stringTag);
    
                foreach ($tagWords as $tagWord) {
                    
                    // Ignore if tagWord is in the ignoreWords list
                    if (in_array($tagWord, $ignoreWords)) {
                        continue;
                    }
        
                    // Ignore if tagWord is already in the list
                    if (in_array(Str::title($tagWord), $tags)) {
                        continue;
                    }
        
                    $tags[] = Str::title($tagWord);
                }
            }
        }
        return $tags;
    }       

    public function search(Request $request)
    {
        $query = strtolower(trim($request->input('query')));
        $products = Product::whereRaw("MATCH(tags) AGAINST(? IN BOOLEAN MODE)", [$query])->get();
        return ProductResource::collection($products);
    }
}
