<?php
namespace App\Http\Controllers\backends;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class DepartmentController extends Controller {
    public function index(Request  $request){
        $user = Auth::user();
        $keyword = isset($request->key) ? $request->key : '';
        $departments = Department::query();
        if($user->can('department.read')){
            if($keyword != ''){
                $departments = $departments->where(function ($query) use ($keyword) {
                $query->where('title','like','%'.$keyword.'%')
                    ->orWhere('code','like','%'.$keyword.'%')
                    ->orWhere('phone','like','%'.$keyword.'%')
                    ->orWhere('address','like','%'.$keyword.'%')
                    ->orWhere('email','like','%'.$keyword.'%');
                });
            }
        }else{
            $departments = $departments->where('author_id',$user->id);
            if($keyword != ''){
                $departments = $departments->where(function ($query) use ($keyword) {
                $query->where('title','like','%'.$keyword.'%')
                    ->orWhere('code','like','%'.$keyword.'%')
                    ->orWhere('phone','like','%'.$keyword.'%')
                    ->orWhere('address','like','%'.$keyword.'%')
                    ->orWhere('email','like','%'.$keyword.'%');
                });
            }
        }
        $departments = $departments->orderBy('created_at', 'asc')->paginate(10);
        return view('backends.departments.list',compact('departments','keyword'));
    }
    public function create(){
        $user = Auth::user();
        if($user->can('create', Department::class)) {
            $users = User::select('id','name')->get();
            return view('backends.departments.create',compact('users'));
        }else{
          abort(403);
        }
    }
    public function store(Request  $request){
        $rules = [
            'title'=>'required',
            'code'=>'required',
            'phone'=>'required|unique:departments,phone',
            'contact'=>'required',
            'email'=>'required|email|unique:departments,email',
            'address'=>'required',
            'user_id'=>'required',
            'nursing_id'=>'required',
        ];
        $messages = [
            'title.required'=>'Vui l??ng nh???p t??n ph??ng ban !',
            'code.required'=>'Vui l??ng nh???p m?? khoa - ph??ng ban !',
            'phone.required'=>'Vui l??ng nh???p s??? ??i???n tho???i !',
            'phone.unique'=>'S??? ??i???n tho???i ???? t???n t???i !',
            'contact.required'=>'Vui l??ng nh???p li??n h??? !',
            'email.required'=>'Vui l??ng nh???p email !',
            'email.unique' => 'Email ???? t???n t???i !',
            'address.required'=>'Vui l??ng nh???p ?????a ch??? !',
            'user_id.required'=>'Vui l??ng nh???p tr?????ng khoa !',
            'nursing_id.required'=>'Vui l??ng nh???p ??i???u d?????ng tr?????ng !',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()):
            return redirect()->route('department.create')->withErrors($validator)->withInput();
        else:
        $request['author_id'] = Auth::id(); 
        $atribute = $request->all();
        Department::create($atribute);
        return redirect()->route('department.index')->with('success','Th??m th??nh c??ng');
        endif;
    }
    public function edit($id){
        $user = Auth::user();
        $departments = Department::findOrFail($id);
        if ($user->can('update', $departments)) {
            $users = User::select('id','name')->get();
            return view('backends.departments.edit',compact('departments','users'));
        }else{
          abort(403);
        }
        
    }
    public function update(Request  $request , $id){
        $departments = Department::findOrFail($id);
        $rules = [
            'title'=>'required',
            'code'=>'required',
            'phone'=>['required',Rule::unique('departments')->ignore($departments->id)],
            'contact'=>'required',
            'email'=>['required','email',Rule::unique('departments')->ignore($departments->id)],
            'address'=>'required',
            'user_id'=>'required',
            'nursing_id'=>'required',
        ];
        $messages = [
            'title.required'=>'Vui l??ng nh???p t??n ph??ng ban !',
            'code.required'=>'Vui l??ng nh???p m?? khoa - ph??ng ban !',
            'phone.required'=>'Vui l??ng nh???p s??? ??i???n tho???i !',
            'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i !',
            'contact.required'=>'Vui l??ng nh???p li??n h??? !',
            'email'=>'Vui l??ng nh???p email',
            'email.unique' => 'Email ???? t???n t???i !',
            'address.required'=>'Vui l??ng nh???p ?????a ch??? !',
            'user_id.required'=>'Vui l??ng nh???p tr?????ng khoa !',
            'nursing_id.required'=>'Vui l??ng nh???p ??i???u d?????ng tr?????ng !',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()):
            return redirect()->route('department.edit',$id)->withErrors($validator)->withInput();
        else:
        $atribute = $request->all();
        $departments->update($atribute);
        if($departments){
            if($departments->wasChanged())
                return redirect()->route('department.edit',$id)->with('success','C???p nh???t th??nh c??ng');
            else 
                return redirect()->route('department.edit',$id);
        }else{
            return redirect()->route('department.edit',$id)->with('error','C???p nh???t kh??ng th??nh c??ng');
        }
    endif;
    }
    public function destroy($id){
        $user = Auth::user();
        $departments = Department::findOrFail($id);
        if ($user->can('delete', $departments)) {
            $departments->delete();
            return redirect()->route('department.index')->with('success','X??a th??nh c??ng');
        }else{
          abort(403);
        }
       
    }
}