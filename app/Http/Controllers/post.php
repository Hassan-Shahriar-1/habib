<?php

namespace App\Http\Controllers;

use App\Models\comments;
use App\Models\posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Validator;


class post extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->user=Auth::user();
    }

    public function create(Request $request){
        
          $validator = Validator::make($request->all(), [
            'title' => 'required|max:10',
            'description' => 'required|min:20',
        ]);
        if($validator->fails()){

            return response()->json([
                'sts'=>'error',
                'msg'=>'There is Problem With Your Form data validation'

            ]);
        }
        $data=[];
        $data['title']=$request->title;
        $data['uid']=$this->user->id;
        $data['description']=$request->description;
        $insert=posts::insert($data);
       if($insert==true){
        return response()->json([
            'sts'=>'ok',
            'msg'=>'Post Inserted Successfully'
        ]);

       }else{
           return response()->json([
               'sts'=>'error',
               'msg'=>'there is something problem with insertation'
           ]);
       }
            
    }


    public function like($id){
        $check_post=posts::where('id',$id)->first();
        if($check_post){
            $check_post->like=$check_post->like+1;
            return response()->json([
                'sts'=>'ok','msg'=>'success'
            ]);

        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'post not found'
            ]);
        }
    }


    public function dislike($id){
        $check_post=posts::where('id',$id)->first();
        if($check_post){
            $check_post->dislike=$check_post->dislike+1;
            return response()->json([
                'sts'=>'ok','msg'=>'success'
            ]);

        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'post not found'
            ]);
        }
    }

    public function commnets(Request $request,$id){

        $validator = Validator::make($request->all(), [
            'comment' => 'required|max:200',
        ]);
        if($validator->fails()){

            return response()->json([
                'sts'=>'error',
                'msg'=>'Comment should not more than 200 character '

            ]);
        }
        $post_check=posts::select('id')->where('id',$id)->first();
        if($post_check){
            $data['pid']=$post_check->id;
            $data['uid']=$this->user->id;
            $data['name']=$this->user->name;
            $data['comment']=$request['comment'];
            comments::insert($data);
            return response()->json([
                'sts'=>'ok',
                'msg'=>'comment succcessfully done'
            ]);

        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'post not found'
            ]);
        }
    }

    public function otherpsot($oid){
        $data=posts::where('uid',$oid)->get();
        if($data->isNotEmpty()){
            return response()->json([
                'sts'=>'ok',
                'data'=>$data,
            ]);
        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'He isnt post anything yet'
            ]);
        }
    }

    public function mypost(){
        $data=posts::where('uid',$this->user->id)->get();
        if($data->isNotEmpty()){
            return response()->json([
                'sts'=>'ok',
                'data'=>$data,
            ]);
        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'You didnt Post  anything yet'
            ]);
        }
    }

    public function allpost(){
        $data=posts::paginate(10);
        
        if($data->isNotEmpty()){
            return response()->json([
                'sts'=>'ok',
                'data'=>$data,
            ]);
        }else{
            return response()->json([
                'sts'=>'error',
                'msg'=>'Nobody didnt post anything yet'
            ]);
        }
    }
}
