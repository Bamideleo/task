<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Storage;
use Carbon\Carbon;

class TaskController extends Controller
{
    //    The function to get the task 
   public function index()
   {
       $data =Task::select('name','description','type')->paginate(10);
       return response()->json([
        'status'=>200,
        'data'=>[$data]
    ]);
     
   }

//    The function to save the task 
   public function saveTask(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'name'=>'required|max:50',
        'description'=>'required|max:250',
        'file'=>'required|mimes:jpeg,png,jpg,gif|max:5000',
        'type'=>'required|numeric|min:1'
    ]);
      if($validator->fails()){
        return response()->json([
          'validat_err'=>$validator->messages()
      ]);
      }else{
    if($request->file('file')){
        $destination_path ='private/images';
        $image = $request->file('file');
        $image_name = $image->getClientOriginalName();
        $path = $request->file('file')->storeAs($destination_path,$image_name);
        $input['file'] = 'private/images/'.$image_name;
    }
    $task = new Task;
    $task->name = $request->input('name');
   $task->description = $request->input('description');
   $task->file = $input['file'];
   $task->type = $request->input('type');
   $task->save();
    return response()->json([
        'status'=>200,
        'data'=>[$task->name,
        $task->description,
        $task->type,
        ]
    ]);
      }
    }

    public function get_one_task($id)
    {
        $data =Task::find($id);
        $imgurl =url('/').'/api/'.$data->file;
        return response()->json([
         'status'=>200,
         'data'=>[
             $data->name,
             $data->description,
             $data->type,
             $imgurl,
             ]
     ]);
    }

   
    public function get_file($file)
    {
        
        $path = "private/images/{$file}";
        $data =Task::where('file', $path)->first();
       $create_at = Carbon::parse($data->created_at)->format('i');
       $final_time = round(($create_at + 10));
    $current_time = Carbon::parse(now()->toDateTimeString())->format('i');
     
        if ($current_time > $final_time) {
            return response()->json([
                'status'=>404,
                'data'=>['link expired'],
            ]);
        } else {
              $data = url('/').'/api/'.$path;
              return response()->json([
                'status'=>200,
                'data'=>[$data],
            ]);
        }


   
    }
}
