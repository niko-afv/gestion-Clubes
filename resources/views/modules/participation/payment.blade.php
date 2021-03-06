@extends('layouts.app')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $breadcrumb->last() }}</h2>
            @if(isset($breadcrumb))
                <ol class="breadcrumb">
                    @foreach($breadcrumb as $url => $title)
                        @if( $url != 'active' )
                            <li class="breadcrumb-item">
                                <a href="{{ $url }}">{{ $title }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active">
                                <strong>{{ $title }}</strong>
                            </li>
                        @endif
                    @endforeach

                </ol>
            @else
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}">Principal</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('events_list') }}">Eventos</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <strong>{{ (isset($event)?$event->name:'Nuevo Evento') }}</strong>
                    </li>
                </ol>
            @endif
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-3">
                @include('partials.inscription-process-status')
            </div>

            @can('make-payment')
                <div class="col-lg-9">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Nuevo Pago <small></small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-8 b-r">
                                <p>Debe ingresar el monto y luego cargar el comprobante.</p>
                                <form role="form">
                                    <div class="form-group">
                                        <input type="text" name="amount" placeholder="Monto" class="form-control" style="height: 70px; font-size: 35px;" {{ ($payment_completed)?'disabled':'' }}>
                                    </div>
                                </form>
                                <div class="form-group  row voucher_form" id="image_form">
                                    <div class="col-sm-12">
                                        <form action="{{ route('upload_payment_file', $invoice->id) }}" class="dropzone" id="my-awesome-dropzone">
                                            <div class="fallback">
                                                <input name="file" type="file" {{ ($payment_completed)?'disabled':'' }} />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <p class="text-center payed-label text-info">
                                    <!--
                                    <a><i class="fa fa-money big-icon"></i></a>
                                    -->
                                        Pagado: $<span class="sum">{{ number_format($invoice->payments->sum('amount'),0,'.',',') }}</span>
                                </p>
                                <div class="hr-line-dashed"></div>
                                <p class="text-center total-label">
                                    <strong>
                                        TOTAL
                                    </strong>
                                </p>
                                <p class="text-center total-label">
                                    <strong>
                                        <label>${{ number_format($invoice->total,0,'.',',') }}</label>
                                    </strong>
                                </p>
                            </div>
                            <div class="col-8">
                                <button data-url="{{ route('save_payment', $invoice->id) }}" class="btn btn-block btn-primary payment-send" name="send" type="button" {{ ($payment_completed)?'disabled':'' }}><strong>Enviar</strong></button>
                                <input name="payment_id" class="d-none">
                                {{ csrf_field() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endcan

            @can('verify-payment')
                <div class="col-lg-9">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Detalle de Pago </h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive m-t">
                                <table class="table invoice-table">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($participants->has('items'))
                                        @foreach($participants->get('items') as $key => $registration)
                                            <tr class="item-{{ $key }}">
                                                <td>
                                                    <div class="registration-description">Inscripción {{ $registration['description'] }}</div>
                                                    <small class="registration-text">Valor {{ ($key == 0)?'General':'Preferencial' }}</small>
                                                </td>
                                                <td class="registration-count">{{ $registration['count'] }}</td>
                                                <td class="registration-price">$ {{ number_format($registration['price'],0,'.',',') }}</td>
                                                <td class="registration-subtotal">$ {{ number_format( ($registration['subtotal']),0,'.',',') }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                    </tbody>
                                </table>
                            </div>
                            <table class="table invoice-total">
                                <tbody>
                                <tr>
                                    <td><strong>Sub Total :</strong></td>
                                    <td class="registration-total">${{ number_format($participants->get('total'),0,'.',',') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>TOTAL :</strong></td>
                                    <td class="registration-total">${{ number_format($participants->get('total'),0,'.',',') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                </div>
            @endcan
        </div>

        <div class="form-group row">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>Pagos registrados</h3>
                    </div>

                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables-example" >
                                <thead>
                                <tr>
                                    <th>Monto</th>
                                    <th>Fecha</th>
                                    <th>Comprobante</th>
                                    @can('see-my-club')
                                    <th>Remover</th>
                                    @endcan
                                    @can('verify-payment')
                                    <th>Verificar</th>
                                    @endcan
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="for_copy d-none">
                                    <td data-name="payment_amout"></td>
                                    <td data-name="payment_date"></td>
                                    <td class="center">
                                        <a class="see_voucher" data-id="" class="btn"><i class="fa fa-eye"></i>&nbsp;</a>
                                    </td>
                                    <td class="center">
                                        <a class="remove_payment" data-url="{{ route('delete_payment') }}" class="btn"><i class="fa fa-trash"></i>&nbsp;</a>
                                    </td>
                                </tr>
                                @foreach($invoice->payments as $payment)
                                    <tr class="">
                                        <td data-name="payment_amout">$ {{ number_format($payment->amount,0,'.',',') }}</td>
                                        <td data-name="payment_date">{{ $payment->created_at->diffForHumans() }}</td>
                                        <td class="center">
                                            <a class="see_voucher" data-id="{{ $payment->id }}" href="{{ Storage::url($payment->voucher) }}" target="_blank" class="btn"><i class="fa fa-eye"></i>&nbsp;</a>
                                        </td>
                                        @can('make-payment')
                                        <td class="center">
                                            <a class="remove_payment" data-url="{{ route('delete_payment') }}" data-id="{{ $payment->id }}" class="btn"><i class="fa fa-trash"></i>&nbsp;</a>
                                        </td>
                                        @endcan
                                        @can('verify-payment')
                                        <td>
                                            <div class="switch">
                                                <div class="onoffswitch">
                                                    <input type="checkbox" {{ ($payment->verified == 1)?'checked':'' }} class="onoffswitch-checkbox" id="payment-{{ $payment->id }}">
                                                    <label class="onoffswitch-label"
                                                           data-id="{{ $payment->id }}"
                                                           for="payment-{{ $payment->id }}"
                                                           data-verified-url="{{ route('payment-verification',[$payment->invoice->participation->event->id,$payment->invoice->participation->club->id]) }}"
                                                           data-not-verified-url="{{ route('cancel-payment-verification',[$payment->invoice->participation->event->id,$payment->invoice->participation->club->id]) }}"
                                                    >
                                                        <span class="onoffswitch-inner"></span>
                                                        <span class="onoffswitch-switch"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{ csrf_field() }}

        @if (session('error_message'))
            <span id="error_message">{{ session('error_message') }}</span>
        @endif
    </div>


    <!-- The Gallery as lightbox dialog, should be a child element of the document body -->
    <div id="blueimp-gallery" class="blueimp-gallery">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <a class="play-pause"></a>
        <ol class="indicator"></ol>
    </div>

@endsection


@section('scripts')
    <script>
      function updateStatus(){
        $('.payment-status-label>i').removeClass('bg-danger');
        $('.payment-status-label>i').addClass('bg-primary');
      }

      var dropzone =Dropzone.options.myAwesomeDropzone = {
        autoProcessQueue: false,
        addRemoveLinks: true,
        paramName: "file", // The name that will be used to transfer the file
        maxFilesize: 2, // MB
        maxFiles: 1,
        dictDefaultMessage: "<strong>Arrastra el comprobante </strong></br> o búscalo haciendo click",
        sending: function (file,xhr, data) {
          var token = $("input[name='_token']").val();
          var payment_id = $("input[name='payment_id']").data('value');
          var amount = $('input[name="amount"]').val();
          data.append("_token", token);
          data.append("payment_id", payment_id);
          data.append("amount", amount);
        },
        success: function (file, response) {
          if (response.error == false){
              //$('#event_logo').attr('src',response.file_path);
              $forCopy = $('.for_copy');
              $clone = $forCopy.clone();
              $clone.find('td[data-name="payment_amout"]').html("$ "+response.data.payment.amount);
              $clone.find('td[data-name="payment_date"]').html(response.data.payment.date);
              $clone.find('.remove_payment').attr('data-id',response.data.payment.id);
              $clone.removeClass('d-none');
              $clone.appendTo($forCopy.parents('table'));
              $("input[name='amount']").val("");
              this.removeAllFiles();
              $('.sum').html(response.data.total_amount);

              if(response.data.payment_completed == true){
                updateStatus();
                $("input[name='amount']").attr('disabled', 'disabled');
                $("button[name='send']").attr('disabled', 'disabled');
              }
              if (response.error){
                toastr.warning(response.message, 'Cuidado');
              }else {
                toastr.success(response.message, 'Excelente');
              }
          }
        },
        init: function(e){
          var myDropzone = this;
          $('.payment-send').click(function(){
            myDropzone.processQueue();
          })
        }
      };

      $(document).ready(function(){

        $('.onoffswitch-label').click(function () {
          $element = $(this);

          var payment_id = $(this).data('id');
          var token = $("input[name='_token']").val();
          var checked = $element.parent().children('input').attr('checked');

          if( checked == undefined){
            var url = $element.data('verified-url');
          }else{
            var url = $element.data('not-verified-url');
          }

          $.post(url, {payment_id: payment_id, _token: token }, function (response) {
            if (response.data.payment.verified == 0){
              toastr.warning(response.message, 'Cuidado');
              $element.parent().children('input').removeAttr('checked');
            }else {
              $element.parent().children('input').attr('checked','checked');
              toastr.success(response.message, 'Excelente');
            }
          })
        });

        $('.see_voucher').click(function(event) {
            event = event || window.event
            var target = event.target || event.srcElement,
            link = target.src ? target.parentNode : target,
            options = { index: link, event: event },
            links = $(this);
            console.log(options);
            blueimp.Gallery(links, options)
        });

        $('.remove_payment').click(function () {
          var url = $(this).data('url');
          var payment_id = $(this).data('id');
          var token = $("input[name='_token']").val();
          $element = $(this);


          swal({
            title: "¿Estas seguro?",
            text: "Tu pago podria sufrir un cambio de estado si continuas.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si, quiero completar!",
            closeOnConfirm: false,
            closeOnEsc: false,
          }, function (confirmation) {
            if (confirmation){
                $.post(url, {_token: token, payment_id: payment_id}, function (response) {
                    if (response.error){
                        toastr.warning(response.message, 'Cuidado');
                    }else {
                        //toastr.success(response.message, 'Excelente');
                        $element.parent().parent().fadeOut(400);
                        swal("Hecho!", response.message, "success");
                        $("input[name='amount']").removeAttr('disabled');
                        $("button[name='send']").removeAttr('disabled');
                        $('.sum').html(response.total_amount);
                    }
                })
            }
          });
        })
      });
    </script>
@endsection

@section('style')
    <style>
        .total-label{font-size: 4vw;}
        .payed-label{font-size: 2vw;}

        .onoffswitch-inner:before {
            content: "VERIFICADO" !important;
            padding: 6px 20px;
            height: 30px;
        }
        .onoffswitch-inner:after {
            content: "NO VERIFICADO" !important;
            padding: 6px 20px;
            height: 30px;
        }
        .switch{
            width: 100%;
        }
        .onoffswitch-label{
            height: 30px;
            width: 100%;
        }
        .onoffswitch{
            width: 100%;
        }
        .onoffswitch-switch{
            right: inherit;
        }
    </style>
@endsection
