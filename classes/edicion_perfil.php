<?php

session_start();
$accion = $_POST['accion'];
$resultado = array();

switch($accion){
    case 1:
        require('edicion_perfil.model.php');
        $datos = $_POST['datos'];
        $contrasena = $_POST['pass'];
        $perfil = new Perfil();
        if($contrasena == ''){
            $resultado['resultado'] = 3;
        }else{
            if($perfil->comprobar_contrasena($_SESSION['id'], $contrasena)){
                $perfil->set_host(0);
                if($perfil->editar_empresa($_SESSION['empresa'], $datos)){
                    $perfil->set_host(1);
                    if($perfil->editar_empresa($_SESSION['empresa'], $datos)){
                        $_SESSION['nombre_empresa'] = $datos['nombre'];
                        $resultado['resultado'] = 1;
                        
                    }else{
                        $resultado['resultado'] = 0;
                    }
                }else{
                    $resultado['resultado'] = 0;
                }
            }else{
                $resultado['resultado'] = 2;
            }
        }
        echo json_encode($resultado);
        break;
    case 2:
        require('edicion_perfil.model.php');
        $datos = $_POST['datos'];
        $contrasena = $_POST['pass'];
        $perfil = new Perfil();
        $perfil->set_host(0);
        if($contrasena == ''){
            $resultado['resultado'] = 3;
        }else{
            if($perfil->comprobar_contrasena($_SESSION['id'], $contrasena)){
                $perfil->set_host(0);
                if($perfil->editar_empresa_contacto($_SESSION['empresa'], $datos)){
                    $perfil->set_host(1);
                    if($perfil->editar_empresa_contacto($_SESSION['empresa'], $datos)){
                        $resultado['resultado'] = 1;
                    }else{
                        $resultado['resultado'] = 0;
                    }
                }else{
                    $resultado['resultado'] = 0;
                }
            }else{
                $resultado['resultado'] = 2;
            }
        }
        echo json_encode($resultado);
        break;
    case 3:
        require('edicion_perfil.model.php');
        $datos = $_POST['datos'];
        $contrasena = $_POST['pass'];
        $perfil = new Perfil();
        if($contrasena == ''){
            $resultado['resultado'] = 3;
        }else{
            if($perfil->comprobar_contrasena($_SESSION['id'], $contrasena)){
                $perfil->set_host(0);
                if($perfil->editar_usuario($_SESSION['id'], $datos)){
                    $perfil->set_host(1);
                    if($perfil->editar_usuario($_SESSION['id'], $datos)){
                        $resultado['resultado'] = 1;
                        $_SESSION['nombre'] = $datos['u_nombres'] . " " . $datos['u_apellidos'];
                    }else{
                        $resultado['resultado'] = 0;
                    }
                }else{
                    $resultado['resultado'] = 0;
                }
            }else{
                $resultado['resultado'] = 2;
            }
        }
        echo json_encode($resultado);
        break;
    case 4:
        require('edicion_perfil.model.php');
        $perfil = new Perfil();
        $perfil->set_host(0);
        $datos_empresa = $perfil->obtener_datos_empresa($_SESSION['empresa']);
        echo json_encode($datos_empresa);
        break;
    case 5:
        require('edicion_perfil.model.php');
        $perfil = new Perfil();
        $perfil->set_host(0);
        $datos_usuario = $perfil->obtener_datos_usuario($_SESSION['id']);
        echo json_encode($datos_usuario);
        break;
    default:
        break;
}

?>