<?php

$sidebar = new Sidebar();

class Sidebar{

    protected $tipo_cuenta, $rol, $respuesta = '';

    public function __construct(){
        session_start();
        $this->tipo_cuenta = $_SESSION['tipo_cuenta'];
        $this->rol = $_SESSION['rol'];
        $this->procesar();
    }

    protected function procesar(){
        switch($this->tipo_cuenta){
            case '1':
                ?>
                <li><a href="javascript:void(0);"><i class="fa fa-nfn fa-shopping-cart"></i><span>Ventas</span></a>
                    <ul class="sub-menu">
                        <li><a href="punto_venta.html">Punto de Venta</a>
                        </li>
                        <li><a href="cierre_caja.html">Cierre de caja</a>
                        </li>
                <?php
                if ($this->rol == 3) {
                ?>
                    <li><a href="punto_venta_v3.html">Punto de Venta V3</a>
                    </li>
                <?php
                }
                ?>
                    </ul>
                </li>

                <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-list-ol"></i><span>Productos</span></a>
                        <ul class="sub-menu">
                            <li class="inventario"><a href="inventario_categorias.html">Categorías</a>
                            </li>
                            <!--li class="inventario"><a href="inventario_marcas.html">Marcas</a>
                            </li>
                            <li class="inventario"><a href="inventario_productos.html">Productos</a>
                            </li>
                            <li class="inventario"><a href="stock.html">Stock</a>
                            </li>
                            <li class="inventario"><a href="precios.html">Precios</a>
                            </li>
                            <!--li class="inventario"><a href="inventario_materia_prima.html">Materia Prima</a>
                            </li>

                            <!--li class="inventario"><a href="inventario_tags.html">Tags</a>
                            </li>
                            <li class="inventario"><a href="inventario_promociones.html">Promociones</a>
                            </li>
                            <!--li class="inventario"><a href="inventario_bodega.html">Bodega</a>
                            </li>
                            <!--li class="inventario" id="inventario_stock"><a href="inventario_stock.html">Traspasar stock</a>
                            </li>
                        </ul>
                </li-->
                    <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-users"></i><span>Proveedores</span></a>
                        <ul class="sub-menu">
                            <li><a href="proveedores_ofertas.html">Ofertas</a>
                            </li>
                            <li><a href="proveedores_ordenes_pendientes.html">Órdenes de compra</a>
                            </li>
                        </ul>
                    </li-->
                    <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-globe"></i><span>Mi Web</span></a>
                        <ul class="sub-menu">
                            <li><a href="web.html">Selecciona tu sitio</a> </li>
                            <li><a href="configurar_sitio.html">Configurar Sitio</a> </li>
                        </ul>
                    </li-->
                    <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-android"></i><span>APP</span></a>
                        <ul class="sub-menu">
                            <li><a href="app_android.html">Descarga APP Android</a>
                            </li>
                            <li><a href="#">Descarga APP iOS</a>
                            </li>
                            <li><a href="app_conocela.html">Conoce nuestra APP!</a>
                            </li>
                        </ul>
                    </li-->
                    <li><a href="javascript:void(0);"><i class="fa fa-nfn fa-usd"></i><span>Gastos</span></a>
                        <ul class="sub-menu">
                        </ul>
                    </li>
                    <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-wrench"></i><span>Utilidades</span></a>
                        <ul class="sub-menu">
                            <li><a href="utilidades.html">General</a>
                            </li>
                            <li><a href="utilidades_agenda.html">Agenda</a>
                            </li>
                            <li><a href="generar_codigo.html">Código de barra</a>
                            </li>
                        </ul>
                    </li-->
                <?php
                if ($this->rol != 3){
                    ?>
                   <li id="administracion">
                    <a href="javascript:void(0);"><i class="fa fa-nfn fa-wrench"></i><span>Administración</span></a>
                    <ul class="sub-menu">
                    <li class="inventario"><a href="inventario_categorias.html">Categorías Productos</a>
                    </li>
                    
                    <li class="inventario"><a href="inventario_landing.html">Gestión Productos<span class="label label-warning pull-right">+</span></a>
                    </li>
                    <!--li><a href="admin_clientes.html">Clientes</a>
                    </li-->
                    <li><a href="administracion_clientes.html">Clientes y Crédito</a>
                    </li>
                    <!--li><a href="formulario_empresa.html">Usuario</a>
                    </li-->
                    <!--li><a href="admin_importacion.html">Importar productos</a>
                    </li-->
                    <li><a href="control_estadisticas.html">Reportes<span class="label label-warning pull-right">+</span></a>
                    </li>
                    <!--li><a href="control_registros.html">Registros de control</a>
                    </li-->
                </ul>
                </li>
                <?php
                }
                ?>
                <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-question"></i><span>Ayuda</span></a>
                    <ul class="sub-menu">
                        <li><a href="http://soporte.nfnlatam.com" target="_blank">Índice</a>
                        </li>
                    </ul>
                </li-->
                <?php
            break;
            case '2':
                ?>
                <li><a href="javascript:void(0);"><i class="fa fa-nfn fa-shopping-cart"></i><span>Ventas</span></a>
                    <ul class="sub-menu">
                        <li><a href="punto_venta.html">Punto de Venta</a>
                        </li>
                        <li><a href="cierre_caja.html">Cierre de caja</a></li>
                    </ul>
                </li>
                
                <li><a href="stock.html"><i class="fa fa-nfn fa-list-ol"></i><span>Stock</span></a>
                </li>
                <li><a href="registrar_gasto.html"><i class="fa fa-nfn fa-usd"></i><span>Gastos</span></a>
                </li>
                <!--li><a href="javascript:void(0);"><i class="fa fa-nfn fa-question"></i><span>Ayuda</span></a>
                    <ul class="sub-menu">
                        <li><a href="http://soporte.nfnlatam.com" target="_blank">Índice</a>
                        </li>
                    </ul>
                </li-->
            <?php
            break;
        }
        switch($this->rol){
            case '2':
                ?>
                <!--li id="distribuidores"><a href="#"><i class="fa fa-nfn fa-truck"></i><span>Distribuidores</span></a>
                    <ul class="sub-menu">
                        <li><a href="distribuidores_documentos.html">Documentos administrativos</a>
                        </li>
                        <li><a href="ficha_contrato.html">Formulario contrato cliente</a>
                        </li>
                        <li><a href="fichas_contrato.html">Lista de clientes</a>
                        </li>
                    </ul>
                </li-->
            <?php
            break;
            case '3':
                ?>
            <!--li id="distribuidores"><a href="#"><i class="fa fa-nfn fa-truck"></i><span>Distribuidores</span></a>
                <ul class="sub-menu">
                    <li><a href="distribuidores_documentos.html">Documentos administrativos</a>
                    </li>
                    <li><a href="ficha_contrato.html">Formulario contrato cliente</a>
                    </li>
                    <li><a href="fichas_contrato.html">Lista de clientes</a>
                    </li>
                </ul>
            </li-->
            <li id="administracion">
                <a href="javascript:void(0);"><i class="fa fa-nfn fa-wrench"></i><span>Administración</span></a>
                <ul class="sub-menu">
                    <li class="inventario"><a href="inventario_categorias.html">Categorías Productos</a>
                    </li>
                    
                    <li class="inventario"><a href="inventario_landing.html">Gestión Productos<span class="label label-warning pull-right">+</span></a>
                    </li>
                    <!--li><a href="admin_clientes.html">Clientes</a>
                    </li-->
                    <li><a href="administracion_clientes.html">Clientes y Crédito</a>
                    </li>
                    <!--li><a href="formulario_empresa.html">Usuario</a>
                    </li-->
                    <!--li><a href="admin_importacion.html">Importar productos</a>
                    </li-->
                    <li><a href="control_estadisticas.html">Reportes<span class="label label-warning pull-right">+</span></a>
                    </li>
                    <!--li><a href="control_registros.html">Registros de control</a>
                    </li-->
                </ul>
            </li>
            <?php
            break;
        }
    }


    public function __destruct(){
        echo $this->respuesta;
    }

}

?>
