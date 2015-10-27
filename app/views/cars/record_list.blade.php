@extends ('base_templates.BaseLayout')

@section ('content')    
<div id="page-wrapper">
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">          
            @if(isset($version))
                @if($version == 'record')   
                    Historial
                @elseif($version == 'workshop')
                    Veh&iacute;culos en taller
                @endif                
            @endif                                   
        </h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<!-- row-content -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-info">
            <!-- /.panel-heading -->
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-4 col-sm-2">
                        <div class="row">
                            <div class="col-xs-3">
                                <span style="width:1.25em; height:1.25em; border-radius: 50%; display:inline-block" class="btn-default"></span>
                            </div>
                            <div class="col-xs-9">
                                &nbsp;Pendiente
                            </div>
                        </div>     
                    </div>
                    <div class="col-xs-4 col-sm-2">
                        <div class="row">
                            <div class="col-xs-3">
                                <span style="width:1.25em; height:1.25em; border-radius: 50%; display:inline-block" class="btn-primary"></span>
                            </div>
                            <div class="col-xs-9">
                                &nbsp;En proceso
                            </div>
                        </div>     
                    </div>
                    <div class="col-xs-4 col-sm-2">
                        <div class="row">
                            <div class="col-xs-3">
                                <span style="width:1.25em; height:1.25em; border-radius: 50%; display:inline-block" class="btn-warning"></span>
                            </div>
                            <div class="col-xs-9">
                                &nbsp;Esperando autorizacion
                            </div>
                        </div>     
                    </div>
                    <div class="col-xs-4 col-sm-2">
                        <div class="row">
                            <div class="col-xs-3">
                                <span style="width:1.25em; height:1.25em; border-radius: 50%; display:inline-block" class="btn-success"></span>
                            </div>
                            <div class="col-xs-9">
                                &nbsp;Aceptado
                            </div>
                        </div>     
                    </div>
                    <div class="col-xs-4 col-sm-2">
                        <div class="row">
                            <div class="col-xs-3">
                                <span style="width:1.25em; height:1.25em; border-radius: 50%; display:inline-block" class="btn-danger"></span>
                            </div>
                            <div class="col-xs-9">
                                &nbsp;Rechazado
                            </div>
                        </div>     
                    </div>
                </div>          
            </div>
            <!-- panel-body -->
            <div class="panel-body">                
                @if(isset($version))
                    @if($version == 'record')
                        <div class="well">
                            <label>Filtrar por fechas (inicio - fin)</label>
                            <div class="row">
                                <div class='col-md-5'>
                                    <div class='input-group date' >
                                        <input type='date' class="form-control" value="{{$ini_date}}" id='ini_date'/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class='col-md-5'>
                                    <div class='input-group date' >
                                        <input type='date' class="form-control" value="{{$fin_date}}" id='fin_date'/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class='col-md-2'>
                                    <button type="button" class="btn btn-primary btn-block"
                                    id="display_between_dates">
                                        Mostrar
                                    </button>   
                                </div>
                            </div>        
                        </div><!-- /.row -->                                          
                    @endif                
                @endif
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="dt_carsinworkshop">                     
                        <thead>
                            <!-- headers-columns -->
                            <tr role="row">
                                <th>Entrada taller</th>
                                <th>Fecha terminación</th>
                                <th>Salida taller</th>
                                <th>Veh&iacute;culo</th>
                                <th>Propietario</th>
                                <th>Estatus</th>                                         
                            </tr>
                            <!-- /.headers-columns -->
                        </thead>
                        <tbody>                                                        
                        @if(isset($services))
                        {{-- */$class = ['btn-default disabled', 'btn-primary', 'btn-warning', 'btn-success', 'btn-danger', 'btn-danger' ];/* --}}  
                        @foreach($services as $service)      
                            <tr class="gradeA odd" role="row">         
                                <td style="text-align: center; vertical-align: middle; ">
                                    {{$service['entry_date']}}
                                </td> 
                                <td style="text-align: center; vertical-align: middle; ">
                                    {{$service['completion_date']}}
                                </td>
                                <td style="text-align: center; vertical-align: middle; ">
                                    @if ($service['exit_date'])
                                        {{$service['exit_date']}}
                                    @else
                                        <span class="service_id" style="display:none">{{$service['service_order_id']}}</span>
                                        <button type="button" title="Orden de servicio" class="btn btn-primary btn-exit">
                                            Registrar
                                        </button>
                                    @endif
                                </td>                                    
                                <td style="text-align: center; vertical-align: middle; ">
                                    <span class="car_id" style="display:none">{{$service['car']['id']}}</span>
                                    <a href="#" style="cursor:pointer" class="car-model">
                                        {{$service['car']['description']}}
                                    </a>
                                </td>   
                                <td style="text-align: center; vertical-align: middle; ">
                                    <span class="owner_id" style="display:none">{{$service['owner']['id']}}</span>
                                    <a href="#" style="cursor:pointer" class="car-owner">
                                        {{$service['owner']['name']}}
                                    </a>
                                </td>   
                                <td style="text-align: center; vertical-align: middle; ">                 
                                    <span class="service_id" style="display:none">{{$service['service_order_id']}}</span>
                                    <button type="button" title="Orden de servicio"
                                    class="btn {{ $class[ $service['order_status'] ] }} btn-order">
                                        E
                                    </button>                 
                                    <button type="button" title="Diagnóstico"
                                    class="btn {{ $class[ $service['diagnostic_status'] ]}} btn-diagnostic">
                                        D
                                    </button>
                                    <button type="button" title="Cotización"
                                    class="btn {{ $class[ $service['quote_status'] ] }} btn-quote">
                                        C
                                    </button>                                            
                                </td>                                
                            </tr> 
                        @endforeach   
                        @endif                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div-->        
</div>
<!-- /.row-content -->        
</div>

<div class="modal fade" id="service_orders_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <span id="order_id" style="display:none"></span>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Entrada de vehículo</h4>
            </div>
            <div id='content_service_order' class="modal-body">
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td><label>Status</label></td>
                        <td><label id='order_status' class="text-success"></label></td>
                    </tr>
                    <tr>      
                        <td><label>Tipo de servicio</label></td>
                        <td><label id='service_name' class="text-success"></label></td>
                    </tr>
                    <tr>
                        <td><label>Dirección de recepción</label></td>
                        <td><label id='order_pickup_address' class="text-success"></label></td>
                    </tr>       
                    <tr>
                        <td><label>Dirección de entrega</label></td>
                        <td><label id='order_delivery_address' class="text-success"></label></td>
                    </tr>
                    <tr>
                        <td><label>Cliente suministra partes</label></td>
                        <td><label id='order_owner_supplied_parts' class="text-success"></label></td>
                    </tr> 
                    <tr>
                        <td><label>Cliente autoriza partes usadas o remanufacturadas</label></td>
                        <td><label id='order_owner_allow_used_parts' class="text-success"></label></td>
                    </tr> 
                </table>
                <div id="order_content">
                    <table class="table table-bordered table-hover table-striped">
                        <tr>      
                            <td><label>Usuario a cargo</label></td>
                            <td><label id='order_user_in_charge' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de creación</label></td>
                            <td><label id='order_creation_date' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de cierre</label></td>
                            <td><label id='order_closed_date' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de aceptación</label></td>
                            <td><label id='order_agree_date' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de rechazo</label></td>
                            <td><label id='order_disagree_date' class="text-success"></label></td>
                        </tr>
                        
                        <tr>
                            <td><label>Kilometraje</label></td>
                            <td><label id='order_km' class="text-success"></label></td>
                        </tr>
                    </table>       
                    <table class="table table-bordered table-hover table-striped">
                        <tr>      
                            <td><label>Nivel de gasolina</label></td>
                            <td><label id='order_fuel_level' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Líquido de frenos</label></td>
                            <td><label id='order_brake_fluid' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Anticongelante</label></td>
                            <td><label id='order_antifreeze' class="text-success"></label></td>
                        </tr>     
                        <tr>
                            <td><label>Liquido de dirección</label></td>
                            <td><label id='order_steering_fluid' class="text-success"></label></td>
                        </tr>        
                        <tr>
                            <td><label>Limpiaparabrisas</label></td>
                            <td><label id='order_wiper' class="text-success"></label></td>
                        </tr>         
                        <tr>
                            <td><label>Aceite</label></td>
                            <td><label id='order_oil' class="text-success"></label></td>
                        </tr>              
                    </table>   
                </div>
                <div class="form-group">
                    <button id='view_photos' type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Ver fotos
                    </button>
                    <button id="order_agree" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Aceptar recepcón de vehículo
                    </button>
                    <button id="order_redo" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Volver a realizar
                    </button>
                </div>              
            </div>
        </div>
    </div>
</div>    

<div class="modal fade" id="diagnostic_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <span id="diagnostic_id" style="display:none"></span>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Diagnóstico</h4>
            </div>
            <div class="modal-body">        
               
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td><label>Status</label></td>
                        <td><label id='diagnostic_status' class="text-success"></label></td>
                    </tr>                    
                </table>                                       
                <div id="diagnostic_content">
                    <table class="table table-bordered table-hover table-striped">
                        <tr>      
                            <td><label>Usuario a cargo</label></td>
                            <td><label id='diagnostic_user_in_charge' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de creación</label></td>
                            <td><label id='diagnostic_date_created' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de cierre</label></td>
                            <td><label id='diagnostic_date_closed' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de aceptación</label></td>
                            <td><label id='diagnostic_date_agree' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Fecha de rechazo</label></td>
                            <td><label id='diagnostic_date_disagree' class="text-success"></label></td>
                        </tr>
                    </table>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>      
                            <td><label>Llantas</label></td>
                            <td><label id='diagnostic_tires' class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Amortiguadores Delanteros</label></td>
                            <td><label id='diagnostic_front_dampers' accesskey="" class="text-success"></label></td>
                        </tr>
                        <tr>
                            <td><label>Frenos Delanteros</label></td>
                            <td><label id='diagnostic_front_brakes' class="text-success"></label></td>
                        </tr>     
                        <tr>
                            <td><label>Suspensión</label></td>
                            <td><label id='diagnostic_suspension' class="text-success"></label></td>
                        </tr>        
                        <tr>
                            <td><label>Bandas</label></td>
                            <td><label id='diagnostic_bands' class="text-success"></label></td>
                        </tr>         
                        <tr>
                            <td><label>Amortiguadores Traseros</label></td>
                            <td><label id='diagnostic_rear_dampers' class="text-success"></label></td>
                        </tr>              
                        <tr>
                            <td><label>Frenos Traseros</label></td>
                            <td><label id='diagnostic_rear_brakes' class="text-success"></label></td>
                        </tr>   
                    </table>
                    <table class="table table-bordered table-hover table-striped">
                        <tr>      
                            <td><label>Diagnóstico mecánico</label></td>
                            <td><label id='diagnostic_description' class="text-success">Lorem impsum et conctarum.</label></td>
                        </tr>  
                    </table>           
                </div>
                <div class="form-group">
                    <button id="diagnostic_agree" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Aceptar diagnóstico
                    </button>
                    <button id="diagnostic_redo" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Volver a realizar
                    </button>
                </div>                      
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quote_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <span id="quote_id" style="display:none"></span>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Cotización</h4>
            </div>
            <div class="modal-body">   
                <div id="quote_id" style="display:none"></div>
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td><label>Status</label></td>
                        <td><label id='quote_status' class="text-success"></label></td>
                    </tr>                    
                </table> 
                <table class="table table-bordered table-hover table-striped">
                    <tr>
                        <td><label>Fecha de creación</label></td>
                        <td><label id='quote_date_created' class="text-success"></label></td>
                    </tr>
                    <tr>
                    </tr>
                    <tr >
                        <td><label>Fecha estimada terminación servicio</label></td>
                        <td>
                            <label id='quote_date_estimated' class="text-success"></label>
                            <input type='date' id='quote_date_estimated_edit' class="edit_resource"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Fecha de cierre</label></td>
                        <td><label id='quote_date_closed' class="text-success"></label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de aceptación</label></td>
                        <td><label id='quote_date_agree' class="text-success"></label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de rechazo</label></td>
                        <td><label id='quote_date_disagree' class="text-success"></label></td>
                    </tr>                 
                </table>                               
                <div class="form-group">
                    <button id="btn-add-quote_item" type="button" class="btn btn-primary edit_resource">
                        <i class="glyphicon glyphicon-plus"></i>
                        Agregar
                    </button>
                </div>                      
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Cant.</th>
                            <th>Decripción</th>
                            <th>Subtotal</th>
                            <th></th>                            
                        </tr>
                    </thead>     
                    <tbody id="tbl-body">              
                    </tbody>                                
                </table>                                                                      

                <table class="table">
                    <tr>      
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td><label>Subtotal</label></td>
                        <td><label>$&nbsp;</label><label id='quote_subtotal'>0.00</label></td>
                    </tr>          
                    <tr>      
                        <td></td>
                        <td><label>I.V.A</label></td>
                        <td><label>$&nbsp;</label><label id='quote_tax'>0.00</label></td>
                    </tr> 
                    <tr>      
                        <td></td>
                        <td><label>Total</label></td>
                        <td><label>$&nbsp;</label><label id='quote_total'>0.00</label></td>
                    </tr>
                    <tr>      
                        <td></td>
                        <td><label>Anticipo requerido</label></td>
                        <td>
                            <label>$&nbsp;</label><label id='quote_advance_payment'></label>
                            <input type="number" id="quote_advance_payment_edit" class="edit_resource">
                        </td>
                    </tr>                     
                </table>
                <div class="form-group">
                    <button id="quote_close" type="button" class="btn btn-outline btn-primary btn-lg btn-block edit_resource">Cerrar cotización</button>
                    <button id="quote_agree" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Aceptar cotización
                    </button>
                    <button id="quote_redo" type="button" 
                    class="btn btn-outline btn-primary btn-lg btn-block">
                        Volver a realizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delivery_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <span id="delivery_id" style="display:none"></span>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Salida de vehículo</h4>
            </div>
            <div class="modal-body">   
                <div id="quote_id" style="display:none"></div>
                <table class="table table-bordered table-hover table-striped">
                    <tr>      
                        <td><label>Usuario a cargo</label></td>
                        <td><label id='delivery_user_in_charge' class="text-success">Ivan Arellano</label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de creación</label></td>
                        <td><label id='delivery_date_created' class="text-success">02-Mayo-2015</label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de aceptación</label></td>
                        <td><label id='delivery_date_agree' class="text-success">02-Mayo-2015</label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de rechazo</label></td>
                        <td><label id='delivery_date_disagree' class="text-success">02-Mayo-2015</label></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de cancelación</label></td>
                        <td><label id='delivery_date_cancel' class="text-success">02-Mayo-2015</label></td>
                    </tr>
                    <tr>
                        <td><label>Status</label></td>
                        <td><label id='delivery_status' class="text-success">Aceptado</label></td>
                    </tr>                    
                </table>          
                <div class="form-group" id="delivery_disagreee_operations">
                    <button id="delivery_redo" type="button" class="btn btn-outline btn-primary btn-lg btn-block">Volver a realizar</button>
                    <button id="delivery_cancel" type="button" class="btn btn-outline btn-primary btn-lg btn-block">Cancelar definitivamente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="car_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Vehículo</h4>
            </div>
            <div class="modal-body">                   
                <table>
                    <tr>        
                        <td>
                            <img  id="car_photo" src='#' width='320' height='240'>                                
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>                 
                        <td>
                            <table class="table">
                              <tr>      
                                <td><label>Marca</label></td>
                                <td><label class="text-success" id="car_brand">Chevrolet</label></td></tr>
                              <tr>
                                <td><label>Modelo</label></td>
                                <td><label class="text-success" id="car_model">Aveo</label></td></tr>
                              <tr>
                                <td><label>Año</label></td>
                                <td><label class="text-success" id="car_year">2010</label></td></tr>    
                              <tr>
                                <td><label>Color</label></td>
                                <td><label class="text-success" id="car_color">Plata</label></td></tr> 
                              <tr>
                                <td><label>Número de serie</label></td>
                                <td><label class="text-success" id="car_serial_number">1234567890</label></td></tr> 
                            </table>                              
                        </td>                        
                    </tr>                    
                </table>                                                                                                                                                                        
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="car_owner_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="header_modal">Propietario</h4>
            </div>
            <div class="modal-body">                                         
                <table class="table">
                    <tr>      
                        <td><label>Tipo de cliente</label></td>
                        <td><label class="text-success" id="owner_type">Ivan Arellano</label></td>
                    </tr>
                    <tr>      
                        <td><label>Nombre propietario</label></td>
                        <td><label class="text-success" id="owner_name">Ivan Arellano</label></td>
                    </tr>
                    <tr>
                        <td><label>RFC</label></td>
                        <td><label class="text-success" id="owner_rfc">RFCIVANARELLNO</label></td>
                    </tr> 
                    <tr>
                        <td><label>Dirección</label></td>
                        <td><label class="text-success" id="owner_address">Sierra de minas #132</label></td>
                    </tr>
                    <tr>
                        <td><label>Télefono</label></td>
                        <td><label class="text-success" id="owner_phone_number">4441788404</label></td>
                    </tr>
                    <tr>
                        <td><label>Télefono móvil</label></td>
                        <td><label class="text-success" id="owner_mobile_phone_number">4441788404</label></td>
                    </tr>    
                    <tr>
                        <td><label>Correo</label></td>
                        <td><label class="text-success" id="owner_email">ivan.arellano@grupohqh.com</label></td>
                    </tr> 
                </table>                                                                                                                                                                                                                      
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="carousel_modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>               
                <h4 class="modal-title" id="header_modal">Fotos orden de servicio</h4>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active">
                        <a href="#inside" id="inside_photos">Interior</a>
                    </li>
                    <li>
                        <a href="#outside" id="outside_photos">Exterior</a>
                    </li>
                    <li>
                        <a href="#motor" id="motor_photos">Motor</a>
                    </li>
                    <li>
                        <a href="#trunk" id="trunk_photos">Porta-equipaje</a>
                    </li>
                </ul>
                <br>
                <div id="carousel_servicephotos" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators" id="order_photos_indicators">
                    </ol>
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox" id="order_photos_wrapper">
                    </div>
                    <!-- Left and right controls -->
                    <a class="left carousel-control" href="#carousel_servicephotos" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel_servicephotos" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>                              
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exit-modal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Confirmar salida</h4>
                </div>
            
                <div class="modal-body">
                    <p>Está a punto de registrar la salida del vehículo, ya no se podrá editar la orden de servicio</p>
                    <p>¿Confirma salida de taller?</p>
                    <p class="debug-url"></p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <a id="confirm_exit" class="btn btn-warning">Confirmar</a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@stop

@section('scripts')
<script>

var ServiceStatus = {
    DOES_NOT_APPLY : -1,
    NO_AVAILABLE   :  0,
    IN_PROCESS     :  1,
    WAITING_AGREE  :  2,
    AGREED         :  3,
    DISAGREED      :  4
};

var StatusDescription = {
    '-1' : 'No aplica',
      0  : 'No disponible',
      1  : 'En proceso',
      2  : 'Esperando autorización',
      3  : 'Aceptado',
      4  :  'Rechazado'
};



$(document).ready(function() { 
    $.ajaxSetup({ cache: false });

    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }    

    $('#dt_carsinworkshop').dataTable( {
        paging: true,
        searching: true,    
        responsive: true,
        "order": []
    } );

    $(document).on('click','.btn-exit',function(e) {
        var id = $(this).siblings('.service_id').text().trim();
        $('#confirm_exit').data('service_order_id', id);
        $('#exit-modal').modal();
    });

    $('#confirm_exit').on('click',function(e){
        var id = $(this).data('service_order_id');
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/exitworkshop') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

/*
 |------------------------------------------------------------------------
 | Service Order
 |------------------------------------------------------------------------
*/ 
    $(document).on('click','.btn-order',function(e) {
        var id = $(this).siblings('.service_id').text().trim(); 
        fillServiceOrder(id);
    });

    function fillServiceOrder(id) {

        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/inventoryadmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success){
                    $("#order_id").text(id);
                    $("#service_name"                ).text(data.service_inventory.service_name);
                    $("#order_status"                ).text(StatusDescription[data.service_inventory.status]);
                    $("#order_user_in_charge"        ).text(data.service_inventory.user_in_charge);
                    $("#order_creation_date"         ).text(data.service_inventory.creation_date);
                    $("#order_closed_date"           ).text(data.service_inventory.closed_date);
                    $("#order_agree_date"            ).text(data.service_inventory.agree_date);
                    $("#order_disagree_date"         ).text(data.service_inventory.disagree_date);
                    $("#order_owner_supplied_parts"  ).text(data.service_inventory.owner_supplied_parts);
                    $("#order_owner_allow_used_parts").text(data.service_inventory.owner_allow_used_parts);
                    $("#order_pickup_address"        ).text(data.service_inventory.pick_up_address);
                    $("#order_delivery_address"      ).text(data.service_inventory.delivery_address);
                    $("#order_km"                    ).text(addCommas(data.service_inventory.km) + " KM");
                    $("#order_fuel_level"            ).text(data.service_inventory.fuel_level + "/4");
                    $("#order_brake_fluid"           ).text(data.service_inventory.brake_fluid);
                    $("#order_antifreeze"            ).text(data.service_inventory.antifreeze);
                    $("#order_steering_fluid"        ).text(data.service_inventory.power_steering_fluid);
                    $("#order_wiper"                 ).text(data.service_inventory.wiper_fluid);
                    $("#order_oil"                   ).text(data.service_inventory.oil);
                    $("#order_creation_date").parents('tr').css('display', 
                        data.service_inventory.creation_date ? 'table-row' : 'none');
                    $("#order_closed_date").parents('tr').css('display', 
                        data.service_inventory.closed_date ? 'table-row' : 'none');
                    $("#order_agree_date").parents('tr').css('display', 
                        data.service_inventory.agree_date ? 'table-row' : 'none');
                    $("#order_disagree_date").parents('tr').css('display', 
                        data.service_inventory.disagree_date ? 'table-row' : 'none');
                    $("#order_pickup_address").parents('tr').css('display', 
                        data.service_inventory.pick_up_address ? 'table-row' : 'none');
                    $("#order_delivery_address").parents('tr').css('display', 
                        data.service_inventory.delivery_address ? 'table-row' : 'none');
                    $("#order_content").css('display', 
                        data.service_inventory.status == ServiceStatus.IN_PROCESS    ? 'none' : 'block');
                    $("#order_agree").css('display', 
                        data.service_inventory.status == ServiceStatus.WAITING_AGREE &&
                        data.service_inventory.editable ? 'block' : 'none');
                    $("#order_redo").css('display', 
                       (data.service_inventory.status == ServiceStatus.WAITING_AGREE || 
                        data.service_inventory.status == ServiceStatus.DISAGREED)    &&
                        data.service_inventory.editable ? 'block' : 'none');
                    $('#service_orders_modal').modal();
                } else {
                    alert(data.msj);
                }
            }
        });
    }

    $('#view_photos').on('click',function(e){
        var id = $("#order_id").text().trim();
        fillPhotos(id, 'inside');
    });

    $('#inside_photos').on('click',function(e){
        var id = $("#order_id").text().trim();
        fillPhotos(id, 'inside');
    });

    $('#outside_photos').on('click',function(e){
        var id = $("#order_id").text().trim();
        fillPhotos(id, 'outside');
    });

    $('#motor_photos').on('click',function(e){
        var id = $("#order_id").text().trim();
        fillPhotos(id, 'motor');
    });

    $('#trunk_photos').on('click',function(e){
        var id = $("#order_id").text().trim();
        fillPhotos(id, 'trunk');
    });

    function fillPhotos(id, type) {
        $.ajax({
            type: 'GET', 
            url: '{{ URL::to('/listphotosadmin') }}' + '/' + id + '/' + encodeURI(type),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    translate = {'inside':'interior', 'outside':'exterior', 'motor':'motor', 'trunk':'porta equipaje'};
                    var indicators = $('#order_photos_indicators').empty();
                    var wrapper    = $('#order_photos_wrapper').empty();
                    if (!data.photos.length) {
                        item = $('<div/>', {
                            'class' : "item active"
                        });
                        img = $('<img/>', {
                            src    : "{{ URL::to('/') }}" + "/img/no-image.png",
                            alt    : type,
                            width  : "640",
                            height : "480"
                        })
                        wrapper.append(item.append(img));
                    }
                    $(".carousel-control").css('display', data.photos.length > 1 ? 'block' : 'none');
                    for (var i = 0; i < data.photos.length; i++) {
                        indicators.append($('<li/>', {
                            'data-target'   : "#carousel_servicephotos",
                            'data-slide-to' : i + "",
                            'class'         : i == 0 ? "active" : ""
                        }));
                        itemClass = "item" + (i == 0 ? " active" : "");
                        item = $('<div/>', {
                            'class' : itemClass
                        });
                        img = $('<img/>', {
                            src    : "data:image/png;base64," + data.photos[i],
                            alt    : type + i,
                            width  : "640",
                            height : "480"
                        })
                        wrapper.append(item.append(img));
                    };
                    $("#carousel_servicephotos").carousel("pause").removeData();
                    $('.nav-tabs a[href=#' + type + ']').tab('show');
                    $('#carousel_modal').modal();
                } else {
                    alert(data.msj);
                }                 
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    }

    $('#order_redo').on('click',function(e){
        var id = $("#order_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/redoinventoryadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

    $('#order_agree').on('click',function(e){
        var id = $("#order_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/agreeinventoryadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

/*
 |------------------------------------------------------------------------
 | Diagnostic
 |------------------------------------------------------------------------
*/ 
    $(document).on('click','.btn-diagnostic',function(e) {
        var id = $(this).siblings('.service_id').text().trim();  
        fillDiagnostic(id);
    });
    
    function fillDiagnostic(id) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/diagnosticadmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success){
                    $("#diagnostic_id").text(id);
                    $("#diagnostic_user_in_charge").text(data.service_diagnostic.user_in_charge);
                    $("#diagnostic_date_created").text(data.service_diagnostic.date_created);
                    $("#diagnostic_date_closed").text(data.service_diagnostic.date_closed);
                    $("#diagnostic_date_agree").text(data.service_diagnostic.date_agree);
                    $("#diagnostic_date_disagree").text(data.service_diagnostic.date_disagree);
                    $("#diagnostic_status").text(StatusDescription[ data.service_diagnostic.status]);
                    $("#diagnostic_tires").text(data.service_diagnostic.tires);
                    $("#diagnostic_front_dampers").text(data.service_diagnostic.front_shock_absorber);
                    $("#diagnostic_front_brakes").text(data.service_diagnostic.front_brakes);
                    $("#diagnostic_suspension").text(data.service_diagnostic.suspension);
                    $("#diagnostic_bands").text(data.service_diagnostic.bands);
                    $("#diagnostic_rear_dampers").text(data.service_diagnostic.rear_shock_absorber);                
                    $("#diagnostic_rear_brakes").text(data.service_diagnostic.rear_brakes);
                    $("#diagnostic_description").text(data.service_diagnostic.description);
                    $("#diagnostic_date_closed").parents('tr').css('display', 
                        data.service_diagnostic.date_closed ? 'table-row' : 'none');
                    $("#diagnostic_date_agree").parents('tr').css('display', 
                        data.service_diagnostic.date_agree ? 'table-row' : 'none');
                    $("#diagnostic_date_disagree").parents('tr').css('display', 
                        data.service_diagnostic.date_disagree ? 'table-row' : 'none');
                    $("#diagnostic_content").css('display', 
                        data.service_diagnostic.status == ServiceStatus.IN_PROCESS ? 'none' : 'block');
                    $("#diagnostic_agree").css('display', 
                        data.service_diagnostic.status == ServiceStatus.WAITING_AGREE &&
                        data.service_diagnostic.editable ? 'block' : 'none');
                    $("#diagnostic_redo").css('display', 
                       (data.service_diagnostic.status == ServiceStatus.WAITING_AGREE || 
                        data.service_diagnostic.status == ServiceStatus.DISAGREED)    &&
                        data.service_diagnostic.editable ? 'block' : 'none');
                    $('#diagnostic_modal').modal();
                } else {
                    alert(data.msj);
                }                   
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    }

    $('#diagnostic_redo').on('click',function(e){
        var id = $("#diagnostic_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/redodiagnosticadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

    $('#diagnostic_agree').on('click',function(e){
        var id = $("#diagnostic_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/agreediagnosticadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });   
    
/*
 |------------------------------------------------------------------------
 | Quote
 |------------------------------------------------------------------------
*/     
    $(document).on('click','.btn-quote',function(e) {
        var id = $(this).siblings('.service_id').text().trim();
        fillQuote(id);
    });

    function fillQuote(id) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/quoteadmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $("#quote_id").text(id);
                    $("#quote_date_created").text(data.quote.date_created);
                    $("#quote_date_estimated").text(data.quote.status == ServiceStatus.IN_PROCESS ? '' : data.quote.date_estimated);
                    $("#quote_date_closed").text(data.quote.date_closed); 
                    $("#quote_date_agree").text(data.quote.date_agree); 
                    $("#quote_date_disagree").text(data.quote.date_disagree);                     
                    $("#quote_status").text(StatusDescription[data.quote.status]);                                        
                    $("#quote_subtotal").text(addCommas(parseFloat(data.quote.subtotal).toFixed(2)));                        
                    $("#quote_tax").text(addCommas(parseFloat(data.quote.tax).toFixed(2)));                                        
                    $("#quote_total").text(addCommas(parseFloat(data.quote.total).toFixed(2)));
                    $("#quote_advance_payment").text(data.quote.status == ServiceStatus.IN_PROCESS ? '' : addCommas(parseFloat(data.quote.advance_payment).toFixed(2)));
                    $("#quote_advance_payment_edit").val(0);
                    var tblQuote = $('#tbl-body');
                    $(tblQuote).empty();
                    var items = data.quote.quote_items;
                    for (i = 0; i < items.length; i++){
                        var tr = 
                        "<tr>" +   
                        "    <td>" +
                        "        <span  class=\"item_amount text\" >" + parseFloat(items[i].amount).toFixed(2) + "</span>" +
                        "        <input class=\"item_amount edit\" style=\"display:none\" type=\"text\" value=\"" + items[i].amount + "\">" +
                        "    </td>" +
                        "    <td>" +
                        "        <span  class=\"item_description text\" >" + items[i].description + "</span>" +
                        "        <input class=\"item_description edit\" style=\"display:none\" type=\"text\" value=\"" + items[i].description + "\">" +
                        "    </td>" +
                        "    <td>" +
                        "        <span  class=\"item_subtotal text\" > $ " + addCommas(parseFloat(items[i].subtotal).toFixed(2)) + "</span>" +
                        "        <input class=\"item_subtotal edit\" style=\"display:none\" type=\"text\" value=\"" + items[i].subtotal + "\">" +
                        "    </td>" +
                        "    <td>" +
                        "        <span  class=\"item_id\" style=\"display:none\">" + items[i].id +
                        "        </span>" +
                        "        <a class =\"edit_quote_item text edit_resource\" " +
                        "        style=\"cursor:pointer\" title=\"Editar\">" +
                        "            <i class=\"glyphicon glyphicon-edit\"></i>" +
                        "        </a> " +
                        "        <a class =\"save_quote_item edit \" style=\"display:none\" " +
                        "        style=\"cursor:pointer\" title=\"Guardar\">" +
                        "            <i class=\"glyphicon glyphicon-save\"></i>" +
                        "        </a>&nbsp;" +
                        "        <a class =\"delete_quote_item edit\" style=\"display:none\" " +
                        "        style=\"cursor:pointer\" title=\"Eliminar\">" +
                        "            <i class=\"glyphicon glyphicon-trash\"></i>" +
                        "        </a>" +                       
                        "    </td>" +
                        "</tr>";
                        $(tblQuote).append(tr);
                    }
                    $("#quote_date_closed").parents('tr').css('display', 
                        data.quote.date_closed ? 'table-row' : 'none');
                    $("#quote_date_agree").parents('tr').css('display', 
                        data.quote.date_agree ? 'table-row' : 'none');
                    $("#quote_date_disagree").parents('tr').css('display', 
                        data.quote.date_disagree ? 'table-row' : 'none');
                    $(".edit_resource").css('display', 
                        data.quote.status == ServiceStatus.IN_PROCESS    ? 'inline-block' : 'none');
                    $("#quote_agree").css('display', 
                        data.quote.status == ServiceStatus.WAITING_AGREE &&
                        data.quote.editable ? 'block' : 'none');
                    $("#quote_redo").css('display', 
                       (data.quote.status == ServiceStatus.WAITING_AGREE || 
                        data.quote.status == ServiceStatus.DISAGREED)    &&
                        data.quote.editable ? 'block' : 'none');
                    $("#quote_close" ).data('quoteId', id);
                    $("#quote_redo"  ).data('quoteId', id);
                    $("#quote_cancel").data('quoteId', id);
                    $('#quote_modal').modal();
                } else {
                    alert(data.msj);
                }                 
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    } 

    $('#quote_close').on('click',function(e){
        var id = $("#quote_id").text().trim();
        var data = {
            estimated_date  : $('#quote_date_estimated_edit').val(), 
            advance_payment : $('#quote_advance_payment_edit').val()
        };
        if (!data.estimated_date)
        {
            alert('ingresar fecha terminación de servicio');
            return;
        }
        if (!data.advance_payment)
        {
            alert('ingresar anticipo válido');
            return;
        }
        if (data.advance_payment < 0 )
        {
            alert('anticipo debe ser mayor o igual a 0');
            return;
        }
        $.ajax({
            type : 'POST',
            data : data,
            url  : '{{ URL::to('/closequote') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

    $('#quote_redo').on('click',function(e){
        var id = $("#quote_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/redoquoteadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

    $('#quote_agree').on('click',function(e){
        var id = $("#quote_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/agreequoteadmin') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });
    
    $('#btn-add-quote_item').on('click',function(e) {      
        var htmlBody = $('#tbl-body');
        var tr = 
        "<tr>" +   
            "<td>" +
                "<span  class=\"item_amount text\" style=\"display:none\"></span>" +
                "<input class=\"item_amount edit\" type=\"text\" value=\"\">" +
            "</td>" +
            "<td>" +
                "<span  class=\"item_description text\" style=\"display:none\"></span>" +
                "<input class=\"item_description edit\" type=\"text\" value=\"\">" +
            "</td>" +
            "<td>" +
                "<span  class=\"item_subtotal text\" style=\"display:none\"></span>" +
                "<input class=\"item_subtotal edit\" type=\"text\" value=\"\">" +
            "</td>" +
            "<td>" +
                "<span  class=\"item_id\" style=\"display:none\">0" +
                "</span>" +
                "<a class =\"edit_quote_item text edit_resource\" style=\"display:none\" " +
                "style=\"cursor:pointer\" title=\"Editar\">" +
                "    <i class=\"glyphicon glyphicon-edit\"></i>" +
                "</a> " +
                "<a class =\"save_quote_item edit\" " +
                "style=\"cursor:pointer\" title=\"Guardar\">" +
                    "<i class=\"glyphicon glyphicon-save\"></i>" +
                "</a>&nbsp;" +
                "<a class =\"delete_quote_item edit\" " +
                "style=\"cursor:pointer\" title=\"Eliminar\">" +
                "    <i class=\"glyphicon glyphicon-trash\"></i>" +
                "</a>" +                       
            "</td>" +
        "</tr>";
        htmlBody.append(tr);
        $('#tbl-body').load(); 
    });

    $(document).on('click','.edit_quote_item',function(){
        $(this).parents('tr').find('.edit').css('display', 'inline-block');
        $(this).parents('tr').find('.text').css('display', 'none');         
    });

    $(document).on('click','.save_quote_item',function() {
        var item = {            
            service_order_id : $("#quote_id").text().trim(),
            amount           : parseFloat($(this).parents('tr:first').find('.item_amount.edit').val()),
            description      : $(this).parents('tr:first').find('.item_description.edit').val().trim(),
            subtotal         : parseFloat($(this).parents('tr:first').find('.item_subtotal.edit').val())
        };
        if (validateItem(item))
        {
            var spanId          = $(this).siblings('.item_id');
            var amountText      = $(this).parents('tr:first').find('.item_amount.text');
            var amountEdit      = $(this).parents('tr:first').find('.item_amount.edit');
            var descriptionText = $(this).parents('tr:first').find('.item_description.text');
            var descriptionEdit = $(this).parents('tr:first').find('.item_description.edit');
            var subtotalText    = $(this).parents('tr:first').find('.item_subtotal.text');
            var subtotalEdit    = $(this).parents('tr:first').find('.item_subtotal.edit');
            var edits           = $(this).parents('tr').find('.edit');
            var texts           = $(this).parents('tr').find('.text');

            $.ajax({
                type: 'POST',
                url: '{{ URL::to('/savequoteitem') }}' + '/' + spanId.text().trim() + "/",
                data : item,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        spanId.text(data.res.id);
                        $("#quote_subtotal").text(addCommas(data.res.quote_subtotal.toFixed(2)));                        
                        $("#quote_tax"     ).text(addCommas(data.res.quote_tax.toFixed(2)));                                        
                        $("#quote_total"   ).text(addCommas(data.res.quote_total.toFixed(2)));
                        amountText.text(addCommas(item.amount.toFixed(2)));
                        descriptionText.text(item.description);
                        subtotalText.text('$ ' + addCommas(item.subtotal.toFixed(2)));
                        amountEdit.val(item.amount.toFixed(2));
                        descriptionEdit.val(item.description);
                        subtotalEdit.val(item.subtotal.toFixed(2));
                        edits.css('display', 'none');
                        texts.css('display', 'inline-block');
                    } else {
                        alert(data.msj);
                    }             
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status);
                    alert(xhr.responseText);
                    alert(thrownError);
                }
            });
        }  
    });

    function validateItem(item) {
        if (isNaN(item.amount)) {
            alert('escribir cantidad valida');
            return false;
        }
        if (isNaN(item.subtotal)) {
            alert('escribir precio válido');
            return false;
        }
        if (item.amount <= 0) {
            alert('cantidad no negativa ni 0');
            return false;
        }
        if (item.subtotal < 0) {
            alert('precio no negativo');
            return false;
        }
        if (item.description == '') {
            alert('Favor de escribir una descripción');
            return false;
        }
        return true;
    }

    $(document).on('click','.delete_quote_item',function(){
        var id = $(this).siblings('.item_id').text();
        if (!confirm('¿Desea borrar de la cotización?'))
            return false;
        $.ajax({
            type: "POST",
            url: '{{ URL::to('/deletequoteitem') }}' + '/' + id,
            success: function(data) {                        
                if(data.success == true){
                    $("#quote_subtotal").text("$ " + addCommas(data.res.quote_subtotal.toFixed(2)));                        
                    $("#quote_tax"     ).text("$ " + addCommas(data.res.quote_tax.toFixed(2)));                                        
                    $("#quote_total"   ).text("$ " + addCommas(data.res.quote_total.toFixed(2)));
                } else {
                    alert(data.msj);
                }                        
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            },
            dataType: 'json'
        });          
        $(this).parents('tr:first').remove();    
        $('#tbl-body').load();    
    });    
    
/*
 |------------------------------------------------------------------------
 | Salida
 |------------------------------------------------------------------------
*/     
    $(document).on('click','.btn-delivery',function(e) {
        id = $(this).attr('id').replace('delivery_', '');
        fillDelivery(id);
    });

    function fillDelivery(id) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/servicedeliveryadmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $("#delivery_user_in_charge").text(data.service_delivery.user_in_charge);
                    $("#delivery_date_created").text(data.service_delivery.date_created);
                    $("#delivery_date_agree").text(data.service_delivery.date_agree); 
                    $("#delivery_date_disagree").text(data.service_delivery.date_disagree); 
                    $("#delivery_date_cancel").text(data.service_delivery.date_cancel);                        
                    $("#delivery_status").text(data.service_delivery.status); 
                    $("#delivery_id").text(id);
                    $("#delivery_date_agree").parents('tr').css('display', 
                        data.service_delivery.date_agree ? 'table-row' : 'none');
                    $("#delivery_date_disagree").parents('tr').css('display', 
                        data.service_delivery.date_disagree ? 'table-row' : 'none');
                    $("#delivery_date_cancel").parents('tr').css('display', 
                        data.service_delivery.date_cancel ? 'table-row' : 'none');
                    $("#delivery_disagreee_operations").css('display', 
                        data.service_delivery.status == 'Rechazado' ? 'block' : 'none');
                    $('#delivery_modal').modal();
                } else {
                    alert(data.msj);
                }                 
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    }

    $('#delivery_redo').on('click',function(e){
        var id = $("#delivery_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/redodelivery') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });

    $('#delivery_cancel').on('click',function(e){
        var id = $("#delivery_id").text().trim();
        $.ajax({
            type: 'POST',
            url: '{{ URL::to('/canceldelivery') }}' + '/' + id + "/",
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    location.reload(true);
                } else {
                    alert(data.msj);
                }             
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    });
    
/*
 |------------------------------------------------------------------------
 | Car model
 |------------------------------------------------------------------------
*/      
    
    $('.car-model').on('click',function(e){
        var id = $(this).siblings('.car_id').text().trim();
        fillCar(id);
    });

    function fillCar(id) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/caradmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $("#car_brand").text(data.car.brand);
                    $("#car_model").text(data.car.model);
                    $("#car_year").text(data.car.year); 
                    $("#car_color").text(data.car.color); 
                    $("#car_serial_number").text(data.car.serial_number);
                    $('#car_photo').attr('src', 'data:image/png;base64,' + data.car.photo);
                    $('#car_modal').modal();
                } else {
                    alert(data.msj);
                }                 
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    }


    
/*
 |------------------------------------------------------------------------
 | Car owner 
 |------------------------------------------------------------------------
*/      
    
    $('.car-owner').on('click',function(e){
        var id = $(this).siblings('.owner_id').text().trim();
        fillCarOwner(id);
    });

    function fillCarOwner(id) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('/carowneradmin') }}' + '/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    translate = {'Business':'Negocio', 'Person':'Persona'};
                    $("#owner_type").text(translate[data.car_owner.type]);
                    $("#owner_name").text(data.car_owner.name);
                    $("#owner_rfc").text(data.car_owner.rfc); 
                    $("#owner_address").text(data.car_owner.address); 
                    $("#owner_phone_number").text(data.car_owner.phone_number);                        
                    $("#owner_mobile_phone_number").text(data.car_owner.mobile_phone_number); 
                    $("#owner_email").text(data.car_owner.email);
                    $("#owner_rfc").parents('tr').css('display', 
                        data.car_owner.rfc ? 'table-row' : 'none');
                    $("#owner_phone_number").parents('tr').css('display', 
                        data.car_owner.phone_number ? 'table-row' : 'none');
                    $("#owner_mobile_phone_number").parents('tr').css('display', 
                        data.car_owner.owner_mobile_phone_number ? 'table-row' : 'none');
                    $("#owner_email").parents('tr').css('display', 
                        data.car_owner.email ? 'table-row' : 'none');
                    $('#car_owner_modal').modal();
                } else {
                    alert(data.msj);
                }                 
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(xhr.responseText);
                alert(thrownError);
            }
        });
    }       
    
 } );  


 $('#display_between_dates').on('click',function(e) {   
        location.href = location.href.replace(location.search, '') + '?ini_date='  + $('#ini_date').val() + '&fin_date=' + $('#fin_date').val();
    });
    
</script>
@stop
