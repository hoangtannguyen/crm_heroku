@extends('backends.templates.master')
@section('title', __('Danh sách thiết bị'))
@section('content')
@php 
$statusEquipments = get_statusEquipments();
$statusCorrected = get_statusCorrected();
$statusFilter = get_statusEquipmentFilter();
$data_link = array();
if($keyword != '') $data_link['keyword'] = $keyword;
if($status != '') $data_link['status'] = $status;
if($departments_key != '') $data_link['departments_key'] = $departments_key;
if($cates_key != '') $data_link['cates_key'] = $cates_key;
if($sort != '') $data_link[$sort] = $order;
$array_value = array();
$array_file = array();
@endphp
<div id="list-events" class="content-wrapper events">
            <button class="equiment-table-show btn btn-outline-secondary  ml-2">Tùy chọn trường hiển thị <i class="far fa-arrow-alt-circle-down"></i></button>
               <div class="equiment-check-all ml-3">
                  <div class="row pt-3">
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="code" type="checkbox" id="customCheckbox6" checked>
                           <label for="customCheckbox6" class="custom-control-label">Mã thiết bị</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="title" type="checkbox" id="customCheckbox7" checked>
                           <label for="customCheckbox7" class="custom-control-label">Tên thiết bị</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="model" type="checkbox" id="customCheckbox8" checked>
                           <label for="customCheckbox8" class="custom-control-label">Model</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="serial" type="checkbox" id="customCheckbox9" checked>
                           <label for="customCheckbox9" class="custom-control-label">Số serial</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="status" type="checkbox" id="customCheckbox10" checked>
                           <label for="customCheckbox10" class="custom-control-label">Tình trạng</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="department" type="checkbox" id="customCheckbox12" checked>
                           <label for="customCheckbox12" class="custom-control-label">Khoa phòng</label>
                        </div>
                        <div class="custom-control custom-checkbox mr-3">
                           <input class="custom-control-input eq-check" name="action" type="checkbox" id="customCheckbox11" checked>
                           <label for="customCheckbox11" class="custom-control-label">Thao tác</label>
                        </div>
                  </div>
               </div>      
   <section class="content">
      <div class="head container">
         <h1 class="title">{{ __('Danh sách thiết bị') }} (<span class="text-danger">{{$equipments->total()}}</span>)</h1>
         <a href="{{ route('equipment.index')  }}" class="btnprn btn float-right"> <i class="fas fa-print"></i> Xuất bản </a>
      </div>
      <div class="main">
         <div class="row search-filter">
            <div class="col-md-2 filter">
               <ul class="nav-filter">
                  <li class="active"><a href="{{ route('equipment.index') }}">{{ __('Tất cả') }}</a></li>
                  <li class=""><a class="btn btn-success" style="color: #fff;" href="{{ route('equipment.export',['key'=> $keyword ,'departments_id'=> $departments_key ,'cate_id'=> $cates_key ,'device_id'=> $devices_key,'status_id'=> $status ]) }}"><i class="far fa-file-excel"></i> {{ _('Xuất Excel') }}</a></li>
               </ul>
            </div>
            <div class="col-md-10 search-form">        
               <form  id="equiment-form-filter" action="{{ route('equipment.index') }}" method="GET">
                  <div class="row">
                     <div class="col-md-4 s-key">
                        <input type="text" name="key" class="form-control s-key" placeholder="{{__('Nhập mã thiết bị , tên thiết bị , model , serial ...')}}" value="{{$keyword}}">
                     </div>
                     <div class="col-md-2">
                        <select class="form-control select2"  name="status">
                                 <option value="" > Tất cả tình trạng </option>                  
                                 @foreach ($statusEquipments as $key => $items)
                                    <option value="{{  $key }}"  {{ $status ==  $key ? 'selected' : '' }} >{{ $items }}</option>
                                 @endforeach 
                        </select>   
                     </div> 
                     <div class="col-md-2">
                        <select class="form-control select2"  name="department_key">
                                 <option value="" > Chọn khoa phòng </option>                  
                                 @foreach ($department_name as $department)
                                    <option value="{{ $department->id }}" {{ $departments_key ==  $department->id ? 'selected' : '' }} >{{ $department->title }}</option>
                                 @endforeach 
                        </select>   
                     </div> 
                     <div class="col-md-2">
                        <select class="form-control select2"  name="cate_key">
                                 <option value="" > Chọn nhóm thiết bị </option>                  
                                 @foreach ($cate_name as $cate)
                                    <option value="{{ $cate->id }}" {{ $cates_key ==  $cate->id ? 'selected' : '' }} >{{ $cate->title }}</option>
                                 @endforeach 
                        </select>   
                     </div>    
                     <div class="col-md-2">
                        <select  class="select2 form-control" name="device_key">
                                 <option value="" > Chọn loại thiết bị </option>
                                 @foreach ($device_name as $device)
                                    <option  value="{{$device->id}}" {{ $devices_key ==  $device->id ? 'selected' : '' }}>{{$device->title}}</option>
                                 @endforeach
                        </select>
                     </div>                 
                     <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                  </div>
               </form>
            </div>
         </div>
            <!-- <ul class="list-group list-group-horizontal pt-2">
               <div class="btn-group float-left">
                  @foreach($statusFilter as $key => $items)
                     <a type="button"  href="{{ route('equipment.index',['status'=>$key]) }}" class="btn btn-primary">{{ $items }}</a>
                  @endforeach
               </div>
            </ul> -->
         <div class="pt-3">
         <div class="card">
            <div class="card-body p-0">
               @include('notices.index')
               <form class="dev-form" action="" name="listEvent" method="POST">
                  @csrf
                  <div class="table-responsive">
                     <table class="table table-striped table-bordered table_equiments" role="table">
                        <thead class="thead">
                           <tr>
                              <th class="code">{{ __('Mã thiết bị') }}
                              @if(Request::query('sortByCode') && Request::query('sortByCode')=='asc')
                                 <a href="javascript:sortCode('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortByCode') && Request::query('sortByCode')=='desc')
                                 <a href="javascript:sortCode('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortCode('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="title">{{ __('Tên thiết bị') }}
                              @if(Request::query('sortByTitle') && Request::query('sortByTitle')=='asc')
                                 <a href="javascript:sortTitle('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortByTitle') && Request::query('sortByTitle')=='desc')
                                 <a href="javascript:sortTitle('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortTitle('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="model">{{ __('Model') }}
                              @if(Request::query('sortByModel') && Request::query('sortByModel')=='asc')
                                 <a href="javascript:sortModel('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortByModel') && Request::query('sortByModel')=='desc')
                                 <a href="javascript:sortModel('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortModel('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="serial">{{ __('Serial') }}
                              @if(Request::query('sortBySeria') && Request::query('sortBySeria')=='asc')
                                 <a href="javascript:sortSeria('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortBySeria') && Request::query('sortBySeria')=='desc')
                                 <a href="javascript:sortSeria('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortSeria('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="status">{{ __('Tình trạng') }}
                              @if(Request::query('sortByStatus') && Request::query('sortByStatus')=='asc')
                                 <a href="javascript:sortStatus('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortByStatus') && Request::query('sortByStatus')=='desc')
                                 <a href="javascript:sortStatus('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortStatus('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="department"> {{ __('Khoa / phòng') }}
                              @if(Request::query('sortByDepartment') && Request::query('sortByDepartment')=='asc')
                                 <a href="javascript:sortDepartment('desc')" class="sort-equiment" ><i class="fas fa-sort-down"></i></a>
                              @elseif(Request::query('sortByDepartment') && Request::query('sortByDepartment')=='desc')
                                 <a href="javascript:sortDepartment('asc')"  class="sort-equiment" ><i class="fas fa-sort-up"></i></a>
                              @else
                                 <a href="javascript:sortDepartment('asc')"  class="sort-equiment" ><i class="fas fa-sort"></i></a>
                              @endif
                              </th>
                              <th class="group-action action">Thao tác</th>
                           </tr>
                        </thead>
                        <tbody class="tbody">
                           @if(!$equipments->isEmpty())
                           @foreach($equipments as $key => $equipment)
                        <tr>
                           <td class="code">{{ $equipment->code}}</td>
                           <td class="title">{{ $equipment->title}}</td>
                           <td class="model">{{ $equipment->model}}</td>
                           <td class="serial">{{ $equipment->serial}}</td>
                           <td class="status">{{ isset($statusEquipments[$equipment->status]) ? $statusEquipments[$equipment->status] :'' }}</td>     
                           <td class="department">{{ isset($equipment->equipment_department->title) ? $equipment->equipment_department->title :'' }}</td>         
                           <td class="group-action action text-nowrap">
                                 @if ($equipment->status == "not_handed" )
                                       @can('equipment.hand')
                                       <a  class="text-dark hand" title="Bàn giao" data-title="{{ $equipment->title }}" data-href="{{ route('equipment.updateHandOver',$equipment->id )}}">
                                          <i class="far fa-arrow-alt-circle-up"></i>
                                       </a> 
                                       @endcan  
                                 @elseif ( $equipment->status == "inactive" )
                                       @can('equipment.liquidation')  
                                       <a class="inactive" title="Thanh lý thiết bị" data-href="{{ route('equipment.updateInactive',$equipment->id )}}">
                                          <i class="fas fa-truck-moving"></i>
                                       </a>
                                       @endcan
                                 @elseif ( $equipment->status == "corrected" )
                                       @can('equipment.update_status') 
                                       <a class="text-success corrected" title="Cập nhật tình trạng" data-href="{{ route('equipment.updateCorrected',$equipment->id )}}">
                                          <i class="fas fa-sync-alt"></i>
                                       </a>
                                       @endcan    
                                 @elseif($equipment->status != "corrected" && $equipment->status != "was_broken" && $equipment->status != "liquidated")
                                       <a class="text-danger was_broken_mediacal" title="Báo hỏng" data-title="{{  $equipment->title }}" data-href="{{ route('equipment.updateWasBroken',$equipment->id )}}">
                                          <i class="fas fa-exclamation-circle"></i>
                                       </a>
                                 @endif
                                 <a title="Nhập vật tư kèm theo" href="{{ route('equipment.createSupplie',$equipment->id )}}">
                                    <i class="fas fa-plus-square"></i>
                                 </a>
                                 <a title="Hồ sơ thiết bị" href="{{ route('equipment.show' , $equipment->id )}}">
                                    <i class="fa fa-eye"></i>
                                 </a> 
                                 <a title="Cập nhật thiết bị" href="{{ route('equipment.edit' , $equipment->id )}}">
                                    <i class="fas fa-edit"></i>
                                 </a>
                                 <a class="text-danger" title="Xóa thiết bị" href="{{ route('equipment.delete',$equipment->id ) }}" data-toggle="modal" data-target="#sideModal" data-direct="modal-top-right"><i class="fas fa-trash"></i></a>
                           </td> 
                           <!-- <td>
                              @foreach ($equipment->equipment_user_training as $number => $equipment_user_training)
                                {{ $number > 0 ? ', ' : '' }}{{$equipment_user_training->title}}
                              @endforeach
                           </td> -->
                        </tr>
                           @endforeach
                           @else
                           <tr>
                              <td colspan="15">{{ __('No items!') }}</td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
                  </div>
               </form>
               <div class="p-3">
                  {{$equipments->appends($data_link)->links()}}
               </div>
            </div>
         </div>
         </div>
      </div>
   </section>
   <!-- /.content -->
</div>
<!-- Side Modal Top Right -->


<div class="modal fade" id="modal_hand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
             <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Chọn khoa phòng bàn giao</h4>
            </div>
            <div class="modal-body">
            <form id="hand_form"  action="" name="frmProducts" data-filter="{{ route('equiment.selectHandOver') }}" class="form-horizontal" method="POST" novalidate="">
                  @csrf
                  @method('PUT')
                  <div class="form-group">
                     <label class="control-label">{{ __('Tên thiết bị') }} <small></small></label>
                     <input id="hand-title" type="text" name=""  value="" class="form-control"  disabled>
                     <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group">
                     <label class="control-label">{{ __('Khoa phòng') }} <small></small></label>
                     <select class="form-control select2" id="modal_hand_department"  name="department_id">
                        <option value="">Chọn Khoa - Phòng Ban</option>
                           @foreach ($department_name as $department)
                              <option  value="{{ $department->id }}">{{ $department->title }}</option>
                           @endforeach
                     </select>
                  </div>
                  <div class="form-group" id="modal_hand_user">
                        <select class="form-control select2"  name="">
                        <option value="">Chọn người phụ trách ở khoa </option>
                              @foreach ($user_name as $user)
                                 <option  value="{{ $user->id }}">{{ $user->name }}</option>
                              @endforeach
                        </select>
                  </div>
                  <div class="form-group" id="modal_hand_user_use">
                        <label class="control-label">{{ __('CB sử dụng') }} <small></small></label>
                        <select class="form-control select2"  name="" multiple="multiple">>
                        <option value="">Chọn cán bộ sử dụng </option>
                              @foreach ($user_name as $user)
                                 <option  value="{{ $user->id }}">{{ $user->name }}</option>
                              @endforeach
                        </select>
                  </div>
                  <div class="form-group">
                     <label class="control-label">{{ __('Ngày bàn giao') }} <small></small></label>
                     <input type="date" name="date_delivery"  value="{{ $cur_time }}" class="form-control">
                     <div class="help-block with-errors"></div>
                  </div>
                  @include('parts.attachment')
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn bàn giao thiết bị?')" value="add">Lưu</button>
                     <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Hủy</span></button>
                  </div>
            </form>
            </div>
        </div>
      </div>
  </div>
</div>


<div class="modal fade" id="modal_inactive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
             <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Thanh lý thiết bị</h4>
            </div>
            <div class="modal-body">
            <form id="inactive_form"  action="" name="frmProducts" class="form-horizontal" method="POST" novalidate="">
                  @csrf
                  @method('PUT')
                  <div class="form-group">
                     <label class="control-label">{{ __('Ngày thanh lý') }} <small></small></label>
                     <input type="date" name="liquidation_date"  value="{{ $cur_time }}" class="form-control"  required>
                     <div class="help-block with-errors"></div>
                  </div>
                  <div class="form-group">
                     <label class="control-label">{{ __('Người chịu trách nhiệm') }} <small></small></label>
                     <input type="text" name="" value="{{ Auth::user()->name }}"   class="form-control" data-error="{{ __('Vui lòng nhập người chịu trách nhiệm')}}" disabled required>
                     <div class="help-block with-errors"></div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn muốn thanh lý thiết bị này?')" value="add">Lưu</button>
                     <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Hủy</span></button>
                  </div>
            </form>
            </div>
        </div>
      </div>
  </div>
</div>



<div class="modal fade" id="modal_corrected" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
           <div class="modal-content">
             <div class="modal-header">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">Bàn giao lại thiết bị</h4>
            </div>
            <div class="modal-body">
            <form id="corrected_form"  action="" name="frmProducts" class="form-horizontal" method="POST" novalidate="">
                  @csrf
                  @method('PUT')
                  <div class="form-group">
                     <label class="control-label">{{ __('Tình trạng thiết bị') }} <small></small></label>
                     <select class="form-control select2"  name="status">
                           @foreach ($statusCorrected as $key => $items)
                              <option value="{{  $key }}">{{ $items }}</option>
                           @endforeach 
                     </select>  
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn cập nhật trạng thái thiết bị này?')" value="add">Lưu</button>
                     <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Hủy</span></button>
                  </div>
            </form>
            </div>
        </div>
      </div>
  </div>
</div>

<!-- Side Modal Top Right -->
<div class="modal fade" id="modal_was_broken_mediacal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               <h4 class="modal-title" id="myModalLabel">Báo hỏng thiết bị</h4>
         </div>
         <div class="modal-body">
            <form id="was_broken_form"  action="" name="frmProducts"  class="form-horizontal" method="POST" novalidate="">
               @csrf
               @method('PUT')
               <div class="form-group">
                  <label class="control-label">{{ __('Tên thiết bị') }} <small></small></label>
                  <input type="text" id="was_broken_form_title" value=""  name=""  class="form-control"  disabled>
                  <div class="help-block with-errors"></div>
               </div>
               <div class="form-group">
                  <label class="control-label">{{ __('Lý do hỏng') }} <small></small></label>
                  <input type="text"  name="reason"  class="form-control" value="{{ Request::old('') }}" data-error="{{ __('Vui lòng nhập lý do hỏng')}}" required>
                  <div class="help-block with-errors"></div>
               </div>
               <div class="form-group">
                  <label class="control-label">{{ __('Ngày báo hỏng') }} <small></small></label>
                  <input type="datetime" name="date_failure"  class="form-control" value="{{ date('Y-m-d h:i:s') }}" disabled>
                  <div class="help-block with-errors"></div>
               </div>
               <div class="form-group">
                  <label class="control-label">{{ __('Mã người báo hỏng') }} <small></small></label>
                  <input type="text" name=""  class="form-control" value="{{ Auth::user()->email }}" disabled required>
                  <div class="help-block with-errors"></div>
               </div>
               <div class="form-group">
                  <label class="control-label">{{ __('Người báo hỏng') }} <small></small></label>
                  <input type="text" name="{{ Auth::user()->id }}"  class="form-control" value="{{ Auth::user()->name }}" disabled required>
                  <div class="help-block with-errors"></div>
               </div>
               @include('parts.files')
               <div class="modal-footer">
                  <button type="submit" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn báo hỏng thiết bị này ?')" value="add">Lưu</button>
                  <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">Hủy</span></button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
</div>


<script>
var query=<?php echo json_encode((object)Request::only(['sortByModel','sortByTitle','sortBySeria','sortByStatus','sortByCode','sortByDepartment'])); ?>;
function sortTitle(value){
    Object.assign(query,{'sortByTitle': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
function sortModel(value){
    Object.assign(query,{'sortByModel': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
function sortSeria(value){
    Object.assign(query,{'sortBySeria': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
function sortStatus(value){
    Object.assign(query,{'sortByStatus': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
function sortCode(value){
    Object.assign(query,{'sortByCode': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
function sortDepartment(value){
    Object.assign(query,{'sortByDepartment': value});
    window.location.href="{{route('equipment.index')}}?"+$.param(query);
}
</script>
@include('backends.media.library')
@include('backends.media.multi-library')
@include('backends.media.multi-library-file')
@include('modals.modal_delete')
@include('modals.modal_deleteChoose')
@endsection