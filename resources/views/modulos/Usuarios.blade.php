@extends('plantilla')
@section('contenido')

    <div class="content-wrapper">
        <section class="content-header">
            <h1>Gestor de Usuarios</h1>
        </section>

        <section class="content">
            <div class="box">
                <div class="box-body">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#CrearUsuario">Crear Nuevo</button>
                </div>
                <br>
                <div class="box-body">
                    <table class="table table-bordered table-hover table-striped dtUsers">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Foto</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>

                                    @if($user->documento == "")
                                        <td>No registrado</td>
                                    @else
                                        <td>{{ $user->documento }}</td>
                                    @endif
                                    
                                    @if($user->foto == "")
                                        <td><img src="{{ url('storage/usuario.jpeg') }}" width="50px"></td>
                                    @else
                                        <td><img src="{{ url('storage/'. $user->foto) }}" width="50px"></td>
                                    @endif

                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->rol }}</td>
                                    <td>
                                        <a href="{{ url('Editar-Usuario/'.$user->id) }}">
                                        <button class="btn btn-success"><i class="fas fa-pencil-alt" data-toggle="modal" data-target="#EditarUsuario"></i></button>
                                        </a>
                                        <button class="btn btn-danger EliminarUsuario" Uid="{{$user->id}}" Usuario="{{$user->name}}"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <!-- Formulario de creacion de usuario -->
    <div class="modal fade" id="CrearUsuario">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="form-group">
                                <h2>Tipo de Usuario</h2>
                                <select class="form-control input-lg" name="rol" required="">
                                    <option value="">Seleccionar...</option>
                                    <option value="Administrador">Administrador..</option>
                                    <option value="Vendedor">Vendedor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h2>Nombre:</h2>
                                <input type="text" class="form-control input-lg" name="name" required="">
                            </div>
                            <div class="form-group">
                                <h2>Email:</h2>
                                <input type="text" class="form-control input-lg" name="email" required="">
                            </div>
                            <div class="form-group">
                                <h2>Contraseña:</h2>
                                <input type="text" class="form-control input-lg" name="password" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Crear</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <!-- Formulario de creacion de usuario EDITAR al usuario -->

     <?php
        $exp = explode('/', $_SERVER["REQUEST_URI"]);
     ?>   

     @if($exp[3]=='Editar-Usuario')
        <div class="modal fade" id="EditarUsuario">
           <div class="modal-dialog">EditarUsuario
            <div class="modal-content">
                <form method="post" action="{{url('actualizar-Usuario/'.$exp[4])}}">
                    @csrf
                    @method('put')
                    <div class="modal-body">
                        <div class="box-body">
                            <div class="form-group">
                                <h2>Tipo de Usuario</h2>
                                <select class="form-control input-lg" name="rol" required="">
                                    <option value="{{$usuario->rol}}">{{$usuario->rol}}</option>
                                    <option value="Administrador">Administrador..</option>
                                    <option value="Vendedor">Vendedor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <h2>Nombre:</h2>
                                <input type="text" class="form-control input-lg" name="name" required="" value="{{$usuario->name}}">
                            </div>
                            <div class="form-group">
                                <h2>Email:</h2>
                                <input type="text" class="form-control input-lg" name="email" required="" value="{{$usuario->email}}">
                            </div>
                            <div class="form-group">
                                <h2>Contraseña:</h2>
                                <input type="text" class="form-control input-lg" name="password" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="submit" class="btn btn-primary">Modificar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
          </div>
       </div>
    @endif   
@endsection