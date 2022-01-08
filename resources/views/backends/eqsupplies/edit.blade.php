@extends('backends.templates.master')
@section('title',__('Sửa Vật Tư'))
@section('content')
@php 
$statusEquipments = get_statusEquipments();
$get_statusRisk = get_statusRisk();
$compatibleEq = get_CompatibleEq();
@endphp
<div class="content-wrapper">
    <section class="content">
        <div class="container">
            <div class="head">
                <a href="{{ route('eqsupplie.index') }}" class="back-icon"><i class="fas fa-angle-left" aria-hidden="true"></i>{{ __('All') }}</a>
                <h1 class="title">{{ __('Sửa Vật Tư') }}</h1>
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#myModal">
                    Vật tư đi kèm theo thiết bị
                </button>
            </div>
            <div class="main">
                @include('notices.index')
                <form action="{{ route('eqsupplie.put' , $eqsupplies->id)}}" class="dev-form" method="POST" data-toggle="validator" role="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Tên vật tư') }} <small> * </small></label>
                                            <input type="text" name="title" placeholder="Tên thiết bị ..." value="{{ $eqsupplies->title }}" class="form-control" data-error="{{ __('Vui lòng nhập tiêu đề hiển thị')}}" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" id="ClassId">
                                            <label class="control-label">{{ __('Loại vật tư') }} <small> * </small></label>
                                            <select  class="select2 form-control" name="supplie_id" >
                                                <option value="" disabled selected> Chọn loại vật tư </option>
                                                @foreach ($supplies as $supplie)
                                                    <option  value="{{$supplie->id}}" {{ $supplie->id == $eqsupplies->supplie_id ? "selected":"" }} >{{$supplie->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                     
                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Số lượng') }} <small> * </small></label>
                                            <input type="number" min="0" name="amount" placeholder="Số lượng ..." value="{{ $eqsupplies->amount }}" class="form-control" data-error="{{ __('Vui lòng nhập số lượng')}}" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Đơn vị tính') }} <small> * </small></label>
                                        <select class="form-control select2"  name="unit_id">
                                        <option value="">Chọn đơn vị tính</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}" {{ $unit->id == $eqsupplies->unit_id ? "selected" :""  }}  >{{ $unit->title }}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Giá nhập') }} <small></small></label>
                                        <input type="text" id="currency2"  name="import_price" placeholder="VNĐ ..." value="{{ $eqsupplies->import_price }}" data-inputmask="'alias': 'decimal', 'groupSeparator': ',', 'autoGroup': true,  'digitsOptional': false, 'prefix': ' VNĐ ', 'digits': 0, 'placeholder': '0'" class="form-control" data-error="{{ __('Vui lòng nhập giá nhập')}}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Hãng sản xuất') }} <small></small></label>
                                            <input type="text" name="manufacturer" placeholder="Hãng sản xuất ..." value="{{ $eqsupplies->manufacturer }}" class="form-control" data-error="{{ __('Vui lòng nhập hãng sản xuất')}}">
                                        </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Xuất xứ') }} <small></small></label>
                                            <input type="text" name="origin" placeholder="Xuất xứ ..." value="{{ $eqsupplies->origin }}" class="form-control" data-error="{{ __('Vui lòng nhập xuất xứ')}}">
                                        </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Nhà cung cấp') }} <small></small></label>
                                        <select class="form-control select2"  name="provider_id">
                                            <option value="">Chọn nhà cung cấp</option>
                                            @foreach ($providers as $provider)
                                                <option value="{{ $provider->id }}" {{ $provider->id == $eqsupplies->provider_id ? "selected" :""  }} >{{ $provider->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Số serial') }} <small></small></label>
                                        <input name="serial"  placeholder="Số serial ..." type="text" class="form-control" value="{{ $eqsupplies->serial }}" data-error="{{ __('Vui lòng nhập số serial')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Model') }} <small></small></label>
                                        <input type="text" name="model" placeholder="Model ..." value="{{ $eqsupplies->model }}" class="form-control" data-error="{{ __('Vui lòng nhập model hiển thị')}}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                            <label class="control-label">{{ __('Năm sản xuất') }}<small> </small></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                                </div>
                                                <input type="text" placeholder="yyyy" name="year_manufacture" value="{{ $eqsupplies->year_manufacture }}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy" data-mask>
                                            </div>
                                     </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('Năm sử dụng') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="text" placeholder="yyyy" name="year_use" value="{{ $eqsupplies->year_use }}" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="yyyy" data-mask>
                                        </div>
                                    </div>
                                </div>   
                            </div>

                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Số phiếu') }} <small></small></label>
                                        <input name="votes"  placeholder="Số phiếu ..." type="text" class="form-control" value="{{ $eqsupplies->votes }}" data-error="{{ __('Vui lòng nhập số serial')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Ngày nhập kho') }}<small> </small></label>
                                            <input name="warehouse" type="date" class="form-control"  value="{{ $eqsupplies->warehouse }}" data-error="Vui lòng nhập ngày nhập kho">
                                        </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Hạn sử dụng') }} <small></small></label>
                                        <input name="expiry"  placeholder="Hạn sử dụng..." type="date" class="form-control" value="{{ $eqsupplies->expiry }}" data-error="{{ __('Vui lòng nhập hạn sử d')}}">
                                    </div>
                                </div>
                            </div>

                       
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Thông số kỹ thuật') }}<small> </small></label>
                                        <textarea name="specificat" class="form-control" rows="4" placeholder="Thông số kỹ thuật ..."  class="form-control" data-error="{{ __('Vui lòng nhập thông số kỹ thuật')}}">{{ $eqsupplies->specificat }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Cấu hình kỹ thuật') }}<small> </small></label>
                                        <textarea name="configurat" class="form-control" rows="4" placeholder="Cấu hình kỹ thuật ..." class="form-control" data-error="{{ __('Vui lòng nhập cấu hình kỹ thuật')}}">{{ $eqsupplies->configurat }}</textarea>
                                    </div>
                                </div>
                            </div>
                         
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Dự án') }} <small></small></label>
                                        <select class="form-control select2"  name="project_id">
                                            <option value="">Chọn nhà cung cấp</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}" {{ $project->id == $eqsupplies->project_id ? "selected" :""  }} >{{ $project->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">                                   
                                        <div class="form-group">
                                            <label class="control-label">{{ __('Quy trình sử dụng') }} <small></small></label>
                                            <input type="text" name="process" placeholder="Quy trình sử dụng ..." value="{{ $eqsupplies->process }}" class="form-control" data-error="{{ __('Vui lòng nhập quy trình sử dụng')}}">
                                        </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Ghi chú') }} <small></small></label>
                                        <input type="text" name="note" placeholder="Ghi chú ..." value="{{ $eqsupplies->note }}" class="form-control" data-error="{{ __('Vui lòng nhập ghi chú')}}">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                    <label class="control-label">{{ __('Người cập nhật') }} <small></small></label>
                                        <select class="form-control select2"  name="user_id">
                                            <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">{{ __('Ngày cập nhật') }}<small> </small></label>
                                        <input name="first_information"   type="date" class="form-control" value="{{ $cur_day }}" data-error="Vui lòng nhập ngày nhập thông tin">                                       
                                    </div>
                                </div>
                            </div>
                    
                        </div>
                        <div class="col-md-3">
                            <aside id="sb-image" class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __('Ảnh đại diện') }}</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse">
                                        <i class="fas fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="frm-avatar" class="img-upload">
                                        <div class="image">
                                            <a href="{{ route('popupMediaAdmin') }}" class="library"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                            {!! image($eqsupplies->image,230,230,__('Avatar')) !!}
                                            <input type="hidden" name="image" class="thumb-media" value="{{ $eqsupplies->image }}" />
                                        </div>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                    <div class="group-action">
                        <button type="submit" name="submit" class="btn btn-success">{{ __('Sửa') }}</button>
                        <a href="{{ route('eqsupplie.index') }}" class="btn btn-secondary">{{ __('Trở về') }}</a>   
                    </div>
                </form>
            </div>
        </div>
    </section>
  </div>

      <!-- The Modal -->
      <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
          
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title mx-auto">Danh sách vật tư kèm theo của thiết bị</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <!-- Modal body -->
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Mã thiết bị') }}</th>
                            <th>{{ __('Tên thiết bị') }}</th>
                            <th>{{ __('Model') }}</th>
                            <th>{{ __('Serial') }}</th>
                            <th>{{ __('Năm sản xuất') }}</th>
                            <th>{{ __('Khoa / phòng') }}</th>
                            <th>{{ __('Ghi chú') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                            @if(!$eqsupplies->supplie_devices->isEmpty())
                              @foreach($eqsupplies->supplie_devices as $key => $equipment)
                                 <tr>
                                    <td>{{ $equipment->code}}</td>
                                    <td>{{ $equipment->title}}</td>
                                    <td>{{ $equipment->model}}</td>
                                    <td>{{ $equipment->serial }}</td>
                                    <td>{{ $equipment->year_manufacture}}</td>
                                    <td>{{ isset($equipment->equipment_department->title) ? $equipment->equipment_department->title :'' }}</td>
                                    <td>{{ $compatibleEq[$equipment->pivot->note] ? $compatibleEq[$equipment->pivot->note] :'' }}</td>
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
            
            <!-- Modal footer -->
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
          </div>
        </div>
    </div>
@include('backends.media.library')
@endsection