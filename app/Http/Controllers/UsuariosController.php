<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use Illuminate\Http\Request;

use Illuminate\support\Facades\Storage;
use Illuminate\support\Facades\DB;
use Illuminate\support\Facades\Hash;

class UsuariosController extends Controller
{
    //Funcion que valida que solamnete un usuario autenticado puede tener acceso
    public function __construct(){
        $this->middleware('auth');
    }

    //Funcion que se ejecuta cuando se manda a llamar a la ruta MiPerfil.
    public function MiPerfil(){
        return view('modulos.MiPerfil');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Indicamos que usaremos todos los registros de la tabla users
        $usuarios = Usuarios::all();
        return view('modulos.Usuarios')->with('usuarios', $usuarios);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $datos = request()->validate([
            'name' => ['string','max:255'],
            'rol' => ['required'],
            'email' => ['string','unique:users'],
            'password' => ['string','min:3']
        ]);

        //Crear el registro en la tabla users de la base de datos
        Usuarios::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
            'password' => Hash::make($datos['password']),
            'documento' => '',
            'foto' =>  ''
        ]);

        //Redireccionamos a la vista de usuario, al llamar a  la ruta de Usuario
        //enviamos una variable que indica que el usuario ha sido creado
        return redirect('Usuarios')->with('Usuario Creado','OK');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function show(Usuarios $usuarios)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function edit(Usuarios $id)
    {
        //Para editar al usuario
        if(auth()->user()->rol != 'Administrador'){
            return redirect('Inicio');
        }
        $usuarios = Usuarios::all();
        $usuario = Usuarios::find($id->id);
        return view('modulos.Usuarios', compact('usuarios','usuario'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuarios::find($id);
        if($usuario["email"]!= request('email')){
            $datos=request()->validate([
                'name' => ['required'],
                'rol' => ['required'],
                'email' => ['required', 'email', 'unique:users']
            ]);
        }else{
            $datos=request()->validate([
                'name' => ['required'],
                'rol' => ['required'],
                'email' => ['required', 'email']
            ]);
        }
        if($usuario["password"]!= request('password')){
            $clave=request("password");
        }else{
            $clave=$usuario["password"];
        }

        DB::table('users')->where('id', $usuario["id"])->update(['name'=>$datos["name"],'email'=>$datos["email"],'rol'=>$datos["rol"],
        'password'=>Hash::make($clave)]);

        return redirect('Usuarios');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //manipular el boton de eliminar
        $usuario = Usuarios::find($id);
        $exp = explode("/", $usuario->foto);

        if (Storage::delete('public/'.$usuario->foto)){
            Storage::deleteDirectory('public/'.$exp[0].'/'.$exp[1]);
        }
        Usuarios::destroy($id);
        return redirect('Usuarios');
    }
    //Función que se ejecuta cuando se da click en el botón Guardar perfil 
public function MiPerfilUpdate(Request $request){
    //Verificar si el correo actual es diferente al correo enviado por el formulario.   
    //Lo que significa que se quiere actualizar.    
    if(auth()->user()->email != request('email')){
        //Si se quiere actualizar la contraseña        
        if(request('passwordN')){        
                //Se crea un array con los datos validados,        
                //si los datos no cumplen las reglas de validación no se consideran para actualizar        
                $datos = request()->validate([
                'name'=>['required', 'string', 'max:255'], 
                'email'=>['required', 'email', 'unique users'], 
                'passwordN'=>['required', 'string', 'min:3']
                ]);
        }else{
                $datos = request()->validate([        
                'name' => ['required', 'string', 'max:255'], 
                'email' => ['required', 'email', 'unique:users']            
                ]);
            }
            //Sino se quiere actualizar el correo
        }else{
            if (request('password')){  
            $datos = request()->validate([   
            'name' => [ 'required', 'string', 'max:255'],   
            'email' => [ 'required', 'email'],   
            'passwordN' => [ 'required', 'string', 'min:3']   
            ]);   
            }else{   
            $datos = request()->validate([   
            'name' => [ 'required', 'string', 'max:255'],    
            'email' => [ 'required', 'email']    
            ]);   
            }           
        }
            //Si se quiere actualizar el documento 
            if(request('documento')){
                $documento = $request['documento '];
            }else{
                $documento = auth()->user()->documento;
            }
            //Si se quiere actualizar la foto
        if(request('fotoPerfil')){
            Storage::delete('public/'.auth()->user()->foto);
                $rutaImg = $request['fotoPerfil']->store('usuarios/'.$datos ["name"], 'public');  
            }else{
                $rutaImg = auth()->user()->foto;
            }
        //Si se quiere actualizar la contraseña y cumple con la regla 
        if(isset($datos ["passwordN"])){
            DB::table('users')->where('id', auth()->user()->id)->update(['name' => $datos ["name"], 
            'email' => $datos ["email"], 'documento' => $documento, 'foto' => $rutaImg,
            'password' =>Hash::make(request("passwordN"))]);
            }else{    
            DB::table('users')->where('id', auth()->user()->id)->update(['name' => $datos ["name"], 
            'email' => $datos ["email"], 'documento' => $documento, 'foto'=>$rutaImg]);    
            }
            
            //Después de actualizar redireccionar a la misma vista "MiPerfil" 
            return redirect('MiPerfil');

        }
}
