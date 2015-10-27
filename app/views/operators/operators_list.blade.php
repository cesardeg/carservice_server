@extends ('base_templates.BaseLayout')

@section ('content') 
<div id="page-wrapper">
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Operadores</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<!-- row-content -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <!-- /.panel-heading -->
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-4">
                        <button id="add_operator"  type="button" class="btn btn-primary"
                        data-toggle="modal">
                            <i class="glyphicon glyphicon-plus"></i>
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
            <!-- panel-body -->
            <div class="panel-body">
                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <!-- headers-columns -->
                            <tr role="row">
                                <th>Nombre</th>
                                <th>Nombre de Usuario</th>
                                <th>Tel. M&oacute;vil</th>
                                <th>Tipo</th>
                                <th style="width: 70px"></th>
                            </tr>
                            <!-- /.headers-columns -->
                        </thead>
                        <tbody>
                        @if(isset($operators))
                        @foreach($operators as $operator)
                            <tr class="gradeA odd" role="row">
                                <td class="sorting_1">
                                    {{ $operator['first_name'] . ' ' . $operator['last_name'] . ' ' . $operator['mother_maiden_name'] }}
                                </td>
                                <td class="sorting_1">{{$operator['username']}}</td>
                                <td class="sorting_1">{{$operator['cell_phone']}}</td>
                                <td class="sorting_1">{{$operator['type']}}</td>
                                <td style="text-align: center; vertical-align: middle; ">
                                    <span class="operator-id" style="display:none">
                                        {{$operator['id']}}
                                    </span>
                                    <a class ="edit_operator" style="cursor:pointer" 
                                       title="Editar">
                                        <i class="glyphicon glyphicon-edit"></i>
                                    </a>
                                    <a class ="delete_operator" style="cursor:pointer; {{ $operator['type'] == 'admin' ? ' visibility:hidden;' : 'visibility:visible;'}}"
                                       title="Eliminar">
                                        <i class="glyphicon glyphicon-remove"></i>
                                    </a>
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

<div class="modal fade" id="modal-operator" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                ×
        </button>
        <h4 class="modal-title" id="head_operator_modal">
                Agregar operador
        </h4>
    </div>
    <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="tabbable" id="tabs-102605">
                        <div id="operator_id_edit" style="display:none"></div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#panel-personal-data" data-toggle="tab">
                                    Datos personales</a>
                            </li>
                            <li >
                                <a href="#panel-contact-data" data-toggle="tab">
                                    Contacto</a>
                            </li>
                            <li >
                                <a href="#panel-login-data" data-toggle="tab">
                                    Autenticación</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="panel-personal-data"><br>
                                <div class="row">
                                    <div class="col-md-6"> 
                                        <div class="form-group">
                                            <label>Nombre</label>
                                            <input id="operator_first_name" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Apellido Paterno</label>
                                            <input id="operator_last_name" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Apellido Materno</label>
                                            <input id="operator_mother_maiden_name" class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sexo</label>
                                            <select id="operator_sex" class="form-control">
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino" >Femenino</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Fecha de nacimiento</label>
                                            <input id="operator_birthdate" type="date" class="form-control" value="1980-01-01">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="panel-contact-data">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Domicilio ( calle y número )</label>
                                            <input id="operator_address" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Colonia</label>
                                            <input id="operator_neighborhood" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Estado</label>
                                            <select id="operator_state" class="form-control">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Ciudad</label>
                                            <input id="operator_city" class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Código postal</label>
                                            <input id="operator_postal_code" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Teléfono local</label>
                                            <input id="operator_home_phone" class="form-control" value="">
                                        </div>  
                                        <div class="form-group">
                                            <label>Teléfono móvil</label>
                                            <input id="operator_cell_phone" class="form-control" value="">
                                        </div>  
                                        <div class="form-group">
                                            <label>Correo electrónico</label>
                                            <input id="operator_email" class="form-control" value="">
                                        </div>
                                    </div>
                                 </div>
                            </div>
                            <div class="tab-pane" id="panel-login-data">
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre de Usuario</label>
                                            <input id="operator_username" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Contraseña</label>
                                            <input type="password" id="operator_password" class="form-control" value="">
                                        </div>
                                        <div class="form-group">
                                            <label>Confirmar contraseña</label>
                                            <input type="password" id="operator_confirm_password" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
                Cerrar
        </button> 
        <button id="save_operator" type="button" class="btn btn-primary">
                Guardar
        </button>
    </div>
</div>
</div>
</div>

</div>
@stop

@section('scripts')
<script>
$(document).ready(function() {

$.ajaxSetup({ cache: false });

$.ajax({
    type: 'GET',
    url: '{{ URL::to('/states') }}',
    dataType: 'json',
    success: function(d) {        
        var select = $('#operator_state');
        for (i = 0; i < d.length; i++)
            select.append("<option value='"+d[i].id+"'>"+d[i].state+"</option>");
    }
});

$('#dataTables-example').dataTable({
    paging: true,
    searching: true,
    responsive: true
});

/*
 |------------------------------------------------------------------------
 | Add Operators
 |------------------------------------------------------------------------
*/    
$(document).on('click','#save_operator',function(e) {

    var data = {            
        first_name         : $("#operator_first_name").val(),
        last_name          : $("#operator_last_name").val(),
        mother_maiden_name : $("#operator_mother_maiden_name").val(),
        sex                : $("#operator_sex").val(),
        birthdate          : $("#operator_birthdate").val(),
        address            : $("#operator_address").val(),
        neighborhood       : $("#operator_neighborhood").val(),
        state_id           : $("#operator_state").val(),
        city               : $("#operator_city").val(),
        postal_code        : $("#operator_postal_code").val(),
        home_phone         : $("#operator_home_phone").val(),
        cell_phone         : $("#operator_cell_phone").val(),
        email              : $("#operator_email").val(),
        username           : $("#operator_username").val(),
        password           : $("#operator_password").val(),
        confirm_password   : $("#operator_confirm_password").val(),
    };
    data.birthdate = data.birthdate != "" ? data.birthdate : null;
    if (validateOperator(data)){
        id = $('#operator_id_edit').text();
        $.ajax({
            type: "POST",
            url: '{{ URL::to('/saveoperator') }}' + (typeof id !== 'undefined'?('/' + id):''),
            data: data,
            success: function(data, textStatus, jqXHR) {
                if (data.success == true) {
                    location.reload(true);
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
    }  
});

function validateOperator(data) {
    var re_pcode = /^\d{5}$/;
    var re_phone = /^\d{10}$/;
    var re_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    var re_pword = /^(?=.*\d)(?=.*[a-zA-Z])[0-9A-Za-z@#\-_$%^&+=!\?]{8,20}$/;
    var re_uname = /^\w{5,20}$/;
    if (data.first_name == "") {
        alert('Favor de escribir el nombre');
        return false;
    }
    if (data.last_name == "") {
        alert('Favor de escribir el apellido paterno');
        return false;
    }
    if (data.mother_maiden_name == "") {
        alert('Favor de escribir el apellido materno');
        return false;
    }
    if (data.username == "") {
        alert('Favor de escribir un nombre de usuario');
        return false;
    }
    if (! re_uname.test(data.username)) {
        alert('El nombre de usario debe estar conformado por:\n' +
            '. Solo carácteres alfanumericos\n' +
            '. Una longitud entre 5 y 20 carácteres');
        return false;
    }
    if (data.password == "") {
        alert('Favor de escribir la contraseña');
        return false;
    }
    if (!re_pword.test(data.password)) {
        alert('La contraseña debe debe estar conformado por:\n' +
            '. Al menos un número\n' +
            '. Al menos una letra\n' +
            '. Solo son admitidos - _ $ % ^ & + = ! ?\n' +
            '. Una longitud entre 8 y 20 carácteres');
        return false;
    }
    if (data.password != data.confirm_password) {
        alert('Las contraseñas no coinciden');
        return false;
    }
    if (data.postal_code != "" && !re_pcode.test(data.postal_code)) {
        alert('El código postal debe estar conformado por 5 digitos');
        return false;
    }
    if (data.home_phone != "" 
        && !re_phone.test(data.home_phone.replace(/\s/g,'').replace(/-/g,'').replace(/\(/g,'').replace(/\)/,'').replace(/\+/g,'').replace(/\./g, ''))) {
        alert('El número de teléfono local debe ser de 10 digitos');
        return false;
    }
    if (data.cell_phone != "" 
        && !re_phone.test(data.cell_phone.replace(/\s/g,'').replace(/-/g,'').replace(/\(/g,'').replace(/\)/,'').replace(/\+/g,'').replace(/\./g, ''))) {
        alert('El número de teléfono celular debe ser de 10 digitos');
        return false;
    }
    if (data.email != "" && !re_email.test(data.email)) {
        alert('Favor de escribir un email válido');
        return false;
    }
    return true;
}

function clearOperatorModal(){
    $("#operator_first_name").val('');
    $("#operator_last_name").val('');
    $("#operator_mother_maiden_name").val('');
    $("#operator_sex").val('');
    $("#operator_birthdate").val('');
    $("#operator_address").val('');
    $("#operator_neighborhood").val('');
    $("#operator_state").val('');
    $("#operator_city").val('');
    $("#operator_postal_code").val('');
    $("#operator_home_phone").val('');
    $("#operator_cell_phone").val('');
    $("#operator_email").val('');
    $("#operator_username").val('');
    $("#operator_password").val('');
    $("#operator_confirm_password").val('');
}

$('#add_operator').on('click', function(e) { 
    $("#head_operator_modal").html("Agregar operador");
    $('#operator_id_edit').html("0");
    clearOperatorModal();
    $('#modal-operator').modal();
});
/*
 |------------------------------------------------------------------------
 | Edit Operators
 |------------------------------------------------------------------------
*/

function fillModalOperator(id) {
    $.ajax({
        type: 'GET',
        url: '{{ URL::to('/getoperator') }}' + '/' + id,
        dataType: 'json',
        success: function(data) {
            if (data.success){
                clearOperatorModal();
                $("#operator_first_name"        ).val(data.operator.first_name);
                $("#operator_last_name"         ).val(data.operator.last_name);
                $("#operator_mother_maiden_name").val(data.operator.mother_maiden_name);
                $("#operator_sex"               ).val(data.operator.sex);
                $("#operator_birthdate"         ).val(data.operator.birthdate ?
                    data.operator.birthdate.substring(0,10) : null);
                $("#operator_address"           ).val(data.operator.address);
                $("#operator_neighborhood"      ).val(data.operator.neighborhood);
                $("#operator_state"             ).val(data.operator.state_id);
                $("#operator_city"              ).val(data.operator.city);
                $("#operator_postal_code"       ).val(data.operator.postal_code);
                $("#operator_home_phone"        ).val(data.operator.home_phone);
                $("#operator_cell_phone"        ).val(data.operator.cell_phone);
                $("#operator_email"             ).val(data.operator.email);
                $("#operator_username"          ).val(data.operator.username);
                $("#operator_password"          ).val(data.operator.password);
                $("#operator_confirm_password"  ).val(data.operator.password);
                $('#modal-operator').modal();
                $('.nav-tabs a[href=#panel-personal-data]').tab('show');
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

$(document).on('click','.edit_operator',function(e) {
    var o = $(this),
    id = o.parents('td:first').find('span.operator-id').text(); 
    $('#operator_id_edit').html(id);
    $("#head_operator_modal").html("Editar operador");
    fillModalOperator(id);
});

/*
 |------------------------------------------------------------------------
 | Delete Operators
 |------------------------------------------------------------------------
*/
$(document).on('click','.delete_operator',function(e) {
    if (!confirm('¿Desea borrar el operador?'))
        return false;
    var o = $(this),
    id = o.parents('td:first').find('span.operator-id').text();  
    $.ajax({
        type: "DELETE",
        url: '{{ URL::to('/deleteoperator') }}' + '/' + id,
        success: function(data, textStatus, jqXHR) {
            if(data.success){
                location.reload(true);
            } else {
                alert(data.errors);
            }
        },
        dataType: 'json'
    });
});

});
</script>
@stop
