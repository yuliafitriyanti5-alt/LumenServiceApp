<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use SimpleXMLElement;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class PostsController extends Controller
{
    // Validasi Accept Header (hanya application/json atau application/xml)
    private function validateAccept(Request $request)
    {
        $accept = $request->header('Accept');

        // Jika Postman mengirim */* atau kosong, anggap minta JSON
        if ($accept === '*/*' || empty($accept)) {
            return 'application/json';
        }

        if ($accept !== 'application/json' && $accept !== 'application/xml') {
            return false;
        }
        return $accept;
    }

    // Validasi Content-Type Header (dilonggarkan)
    private function validateContentType(Request $request)
    {
        $contentType = $request->header('Content-Type');

        // Izinkan JSON, Form-urlencoded, atau kosong
        if ($contentType === 'application/json' || $contentType === 'application/x-www-form-urlencoded' || empty($contentType)) {
            return true;
        }
        return false;
    }

    // Helper penentu format response (JSON atau XML)
    private function buildResponse($data, $acceptHeader, $status = 200)
    {
        if ($acceptHeader === 'application/xml') {
            $xml = new SimpleXMLElement('<root/>');
            $arrayData = json_decode(json_encode($data), true);
            $this->arrayToXml($arrayData, $xml);
            return response($xml->asXML(), $status)->header('Content-Type', 'application/xml');
        }

        return response()->json($data, $status);
    }

    // Helper konversi array ke XML
    private function arrayToXml($data, &$xmlData)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item';
            }
            if (is_array($value)) {
                $subnode = $xmlData->addChild($key);
                $this->arrayToXml($value, $subnode);
            } else {
                $xmlData->addChild((string)$key, htmlspecialchars((string)$value));
            }
        }
    }

    // GET /posts (Read All)
    public function index(Request $request)
    {
    //     $acceptHeader = $request->header('Accept');

    //     // validasi: hanya application/json atau application/xml yang valid
    //     if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
    //         $posts = Post::OrderBy("id", "DESC")->paginate(10);

    //         if ($acceptHeader === 'application/json') {
    //             // response json
    //             return response()->json($posts->items('data'), 200);
    //         } else {
    //             // create xml posts element
    //             $xml = new SimpleXMLElement('<posts/>');
    //             foreach ($posts->items('data') as $item) {
    //                 // create xml posts element
    //                 $xmlItem = $xml->addChild('post');

    //                 // mengubah setiap field post menjadi bentuk xml
    //                 $xmlItem->addChild('id', $item->id);
    //                 $xmlItem->addChild('title', $item->title);
    //                 $xmlItem->addChild('status', $item->status);
    //                 $xmlItem->addChild('content', $item->content);
    //                 $xmlItem->addChild('user_id', $item->user_id);
    //                 $xmlItem->addChild('created_at', $item->created_at);
    //                 $xmlItem->addChild('updated_at', $item->updated_at);
    //             }
    //             return $xml->asXML();
    //         }
    //     } else {
    //         return response('Not Acceptable!', 406);
    //     }
    // }
    $posts = Post::OrderBy("id", "DESC")->paginate(2)->toArray();
    $response = [
        "total_count" => $posts["total"],
        "limit" => $posts["per_page"],
        "pagination" => [
            "next_page" => $posts["next_page_url"],
            "current_page" => $posts["current_page"]
        ],
        "data" => $posts["data"],
    ];

    return response()->json($response, 200);
    }

    // GET /post/{id} (Read Detail) -> Tantangan #2
    public function show(Request $request, $id)
    {
        //  $acceptHeader = $request->header('Accept');

        // if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
        //     $post = Post::find($postId);

        //     if (!$post) {
        //         return response('Post not found', 404);
        //     }

        //     if ($acceptHeader === 'application/json') {
        //         return response()->json($post, 200);
        //     } else {
        //         $xml = new SimpleXMLElement('<post/>');
        //         $xml->addChild('id', $post->id);
        //         $xml->addChild('title', $post->title);
        //         $xml->addChild('status', $post->status);
        //         $xml->addChild('content', $post->content);
        //         $xml->addChild('user_id', $post->user_id);
        //         $xml->addChild('created_at', $post->created_at);
        //         $xml->addChild('updated_at', $post->updated_at);

        //         return $xml->asXML();
        //     }
        // } else {
        //     return response('Not Acceptable!', 406);
        // }

        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::find($id);
        if (!$post) {
            abort(404);
        }

        return $this->buildResponse($post, $accept, 200);
    }

    // POST /posts (Create) -> Tantangan #1
    public function store(Request $request)
    {
    //    $acceptHeader = $request->header('Accept');

    //     if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

    //         $contentTypeHeader = $request->header('Content-Type');

    //         if ($contentTypeHeader === 'application/json') {
    //             $input = $request->all();
    //             $post  = Post::create($input);

    //             // Tantangan 1: response berdasarkan Accept Header
    //             if ($acceptHeader === 'application/json') {
    //                 return response()->json($post, 200);
    //             } else {
    //                 $xml = new SimpleXMLElement('<post/>');
    //                 $xml->addChild('id', $post->id);
    //                 $xml->addChild('title', $post->title);
    //                 $xml->addChild('status', $post->status);
    //                 $xml->addChild('content', $post->content);
    //                 $xml->addChild('user_id', $post->user_id);
    //                 $xml->addChild('created_at', $post->created_at);
    //                 $xml->addChild('updated_at', $post->updated_at);

    //                 return $xml->asXML();
    //             }
    //         } else {
    //             return response('Unsupported Media Type', 415);
    //         }
    //     } else {
    //         return response('Not Acceptable!', 406);
        // $accept = $this->validateAccept($request);
        // if (!$accept) {
        //     return response('Not Acceptable!', 406);
        // }

        // if (!$this->validateContentType($request)) {
        //     return response('Unsupported Media Type', 415);
        // }

        // $post = Post::create($request->all());
        // return $this->buildResponse($post, $accept, 201);
        // $cekUser = DB::table('users')->get();
        // if ($cekUser->isEmpty()) {
        //     return response()->json(['error' => 'Tabel users KOSONG di database Lumen!'], 500);
        // }

        $input = $request->all();
        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    

        $post = Post::create($input);
        return response()->json($post, 200);
        return $this->buildResponse($post, $accept, 201);
    }

    // PUT /post/{id} (Update) -> Tantangan #2 & #3
    public function update(Request $request, $id)
    {
        //  $acceptHeader = $request->header('Accept');

        // if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

        //     $contentTypeHeader = $request->header('Content-Type');

        //     // Tantangan 3: validasi Content-Type pada update
        //     if ($contentTypeHeader === 'application/json') {
        //         $input = $request->all();
        //         $post  = Post::find($id);

        //         if (!$post) {
        //             return response('Post not found', 404);
        //         }

        //         $post->fill($input);
        //         $post->save();

        //         // Response berdasarkan Accept Header
        //         if ($acceptHeader === 'application/json') {
        //             return response()->json($post, 200);
        //         } else {
        //             $xml = new SimpleXMLElement('<post/>');
        //             $xml->addChild('id', $post->id);
        //             $xml->addChild('title', $post->title);
        //             $xml->addChild('status', $post->status);
        //             $xml->addChild('content', $post->content);
        //             $xml->addChild('user_id', $post->user_id);
        //             $xml->addChild('created_at', $post->created_at);
        //             $xml->addChild('updated_at', $post->updated_at);

        //             return $xml->asXML();
        //         }
        //     } else {
        //         return response('Unsupported Media Type', 415);
        //     }
        // } else {
        //     return response('Not Acceptable!', 406);
        // }

        // $accept = $this->validateAccept($request);
        // if (!$accept) {
        //     return response('Not Acceptable!', 406);
        // }

        // if (!$this->validateContentType($request)) {
        //     return response('Unsupported Media Type', 415);
        // }

        // $post = Post::find($id);
        // if (!$post) {
        //     return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        // }

        // $post->update($request->all());
        // return $this->buildResponse($post, $accept);

        $input = $request->all();

        $post = Post::find($id);

        if (!$post) {
            abort(404);
        }

        // validation
        $validationRules = [
            'title' => 'required|min:5',
            'content' => 'required|min:10',
            'status' => 'required|in:draft,published',
            'user_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // validation end

        $post->fill($input);
        $post->save();

        return response()->json($post, 200);

        // $post->update($request->all());
        // return $this->buildResponse($post, $accept);
    }

    // DELETE /post/{id} (Delete) -> Tantangan #2
    public function destroy(Request $request, $id)
    {
        //  $acceptHeader = $request->header('Accept');

        // if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
        //     $post = Post::find($id);

        //     if (!$post) {
        //         return response('Post not found', 404);
        //     }

        //     $post->delete();

        //     $data = [
        //         'message' => 'Post deleted successfully',
        //         'id'      => (int) $id
        //     ];

        //     if ($acceptHeader === 'application/json') {
        //         return response()->json($data, 200);
        //     } else {
        //         $xml = new SimpleXMLElement('<response/>');
        //         $xml->addChild('message', $data['message']);
        //         $xml->addChild('id', $data['id']);

        //         return $xml->asXML();
        //     }
        // } else {
        //     return response('Not Acceptable!', 406);
        // }

        $accept = $this->validateAccept($request);
        if (!$accept) {
            return response('Not Acceptable!', 406);
        }

        $post = Post::find($id);
        if (!$post) {
            return $this->buildResponse(['message' => 'Post not found'], $accept, 404);
        }

        $post->delete();
        return $this->buildResponse(['message' => 'Post deleted successfully'], $accept);
    }
}
