<?php


$wsdl = "http://webservice.podiprint.com/soap_server.php?wsdl";
$login="test";
$password="test";

$soap_options = array( 'login' => $login,'password' => $password,'cache_wsdl' => WSDL_CACHE_NONE);
$client = new SoapClient( $wsdl , $soap_options );






$libros = array(
    array(
        "Titulo" => "Libro de Aventuras",
        "Cantidad" => "1",
        "Tamano" => "15x21",
        "Paginas" => "80",
        "PapelTripa" => "Offset",
        "GramajeTripa" => "80",
        "PapelCubierta" => "Cartulina Gráfica",
        "GramajeCubierta" => "240",
        "Encuadernado" => "ANILLADO",
        "UrlTripa" => array("url" => "http://es.tldp.org/Manuales-LuCAS/doc-guia-usuario-ruby/guia-usuario-ruby.pdf"),
        "UrlPortada" => array("url" => "https://manuals.info.apple.com/MANUALS/1000/MA1565/en_US/iphone_user_guide.pdf"),
        "Tapa" => "DURA",
        "Acabado" => "MATE",
        "Solapa" => "80",
    ),
    array(
        "Titulo" => "Libro de Aventuras 2",
        "Cantidad" => "1",
        "Tamano" => "15x21",
        "Paginas" => "80",
        "PapelTripa" => "Offset",
        "GramajeTripa" => "80",
        "PapelCubierta" => "Cartulina Gráfica",
        "GramajeCubierta" => "240",
        "Encuadernado" => "ANILLADO",
        "UrlTripa" => array("url" => "http://es.tldp.org/Manuales-LuCAS/doc-guia-usuario-ruby/guia-usuario-ruby.pdf"),
        "UrlPortada" => array("url" => "https://manuals.info.apple.com/MANUALS/1000/MA1565/en_US/iphone_user_guide.pdf"),
        "Tapa" => "DURA",
        "Acabado" => "MATE",
        "Solapa" => "80",
    ),
    array(
        "Titulo" => "Libro de Aventuras 3",
        "Cantidad" => "1",
        "Tamano" => "15x21",
        "Paginas" => "80",
        "PapelTripa" => "Offset",
        "GramajeTripa" => "80",
        "PapelCubierta" => "Cartulina Gráfica",
        "GramajeCubierta" => "240",
        "Encuadernado" => "ANILLADO",
        "UrlTripa" => array("url" => "http://es.tldp.org/Manuales-LuCAS/doc-guia-usuario-ruby/guia-usuario-ruby.pdf"),
        "UrlPortada" => array("url" => "https://manuals.info.apple.com/MANUALS/1000/MA1565/en_US/iphone_user_guide.pdf"),
        "Tapa" => "DURA",
        "Acabado" => "MATE",
        "Solapa" => "80",
    )
);

$hacerPedido = array(
    'IdentificadorPedido'=> "PED00001",
    "Peso" => "511",
    "Comprador" => array(
        "Nombre" => "Juan",
        "Apellidos" => "Prueba Pérez",
        "Telefono" => "952001122",
        "Movil" => "?",
        "Email" => "juanprueuba@email.es",
        "Direccion" => "Avenida Principal 2, 3º B",
        "Pais" => "ESP",
        "Localidad" => "Málaga",
        "CodigoPostal" => "29000",
    ),
    "Libro" => $libros
);




//=array('IdentificadorPedido'=>$_identificadorPedido,'Peso'=>$_peso,'Comprador'=>$_comprador,'Libro'=>$_libro);

try {
    $result = $client->hacerPedido($hacerPedido);
    print_r($result);
} catch (SoapFault $fault) {
    print_r($fault->faultcode." - ".$fault->faultstring);

}
