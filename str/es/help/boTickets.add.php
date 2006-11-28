<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Entrada de ticket</H3>
Utilice esta pantalla para dar entrada a un nuevo ticket.
Este fichero de ayuda explica los campos y como rellenarlos.
<BR>
<BR>
<H4>Responsable</H4>
Si no esta autorizado para otra cosa, deberia dejar 
esto siempre como 'Tickets/WO, Nuevo'

<H4>Usuario</H4>
Si hay una persona que deba ser informada de esta Ticket,
ponga sus nombre/organizacion aqui.

<H4>Telefono</H4>
Si tiene un numero de telefono de contacto de la persona, metalo aqui.

<H4>Dirección de correo</H4>
Si tiene una direccion de correo de la persona, metalo aqui.

<H4>Cuenta</H4>
Selecciona la cuenta asociada con el usuario o la Ticket.

<H4>Producto</H4>
Selecciona el producto al que se refiere la Ticket.

<H4>Versión</H4>
La version del sistema operativo afectado.

<H4>Prioridad</H4>
Selecciona la prioridad de la Ticket. 

<H4>Severidad</H4>
Selecciona la severidad de la Ticket.

<H4>Resumen</H4>
En una linea la descripcion del problema.Lo ideal seria sobre 60 caracteres,
resumir el problema tan concretamente como sea posible.
Un ejemplo está dado aqui para indicar como usaria este campo.Imagine
la siguiente descripcion de un problema

<pre>
Descripción

El enlace 'Ayuda' a la pagina 'atracciones siguientes'
(/atracciones/siguientes/index.html) que apunta a 
/help/att-upc-ayuda.html,el cual no existe.
</pre>

<UL>
  <LI>Resumen: Problema con un pagina web<BR>
	- este no es un resumen bueno. No tiene indicado cual
        es el problema,en donde esta la pagina web,o que 
        tipo de problema es.</LI>

  <LI>Resumen: Roto el enlace en una pagina web<BR>
	- mejor, en este se indica que problema es, pero todavia 
        no se indica donde o porque el enlace esta roto.</LI>

  <LI>Resumen: el enlace de ayuda a atracciones siguientes
      esta roto (/attractions/upcoming/)<BR>
	- Bien. Ahora sabemos donde esta roto el enlace y 
        que es el enlace de ayuda.</LI>
	
  <LI>Resumen: Upc. Attr. help link broken
      (no encontro/help/att-upc-help.html)<BR>
	- Mejor todavia. Asi sabemos tambien el nombre 
	  del fichero que no encuentra.</LI>

</UL>

<H4>Ticket</H4>
Aqui es donde usted pone el problema detalladamente. Incluya 
tanta información como crea relevante. Si sabe
qué necesita para resolver el problema, pongalo aquí;en 
otro caso ponga tanta información como sepa del problema.Como/donde 
ocurre.Puede ser reproducido? A cuantos clientes ha afectado,y como.
Cuantos intentos han sido hechos para resolver el problema.
Que parte del sistema ha sido afectada.

<?php
helpFooter();
?>
